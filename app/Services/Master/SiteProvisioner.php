<?php

namespace App\Services\Master;

use App\Services\Installer\DatabaseCredentials;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallHandoff;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Master setup: check the database → copy the application into a staging folder →
 * write its .env and patch its front controllers for the new URL → leave an
 * encrypted install handoff → rename to the final folder. The new copy's own
 * installer then runs the migrations when the browser posts the handoff token.
 */
final class SiteProvisioner
{
    public function __construct(
        private MasterSettings $settings,
        private ProjectCopier $copier,
        private DatabaseProvisioner $database,
        private InstallHandoff $handoff,
        private SiteRegistry $registry,
        private Filesystem $files,
    ) {
    }

    /**
     * @param  array<string, mixed>  $input  validated wizard input (company, folder, db_*, admin_*)
     * @return array{folder: string, path: string, app_url: string, token: string}
     *
     * @throws InstallerException
     */
    public function provision(array $input, string $appUrl, string $urlPrefix, ?UploadedFile $logo = null): array
    {
        @set_time_limit(0);
        ignore_user_abort(true);

        $folder = (string) $input['folder'];
        $root = $this->settings->targetRoot();
        $final = $this->settings->folderPath($folder);

        if (! is_dir($root) || ! is_writable($root)) {
            throw new InstallerException("The web server cannot create folders in {$root}.");
        }
        if (file_exists($final)) {
            throw new InstallerException("A folder named \"{$folder}\" already exists. Choose another folder name.");
        }

        $credentials = DatabaseCredentials::fromInput($input);
        $this->database->prepare($credentials);

        $staging = $root . DIRECTORY_SEPARATOR . '.' . $folder . '.partial-' . bin2hex(random_bytes(4));

        try {
            $this->copier->copy($this->settings->sourcePath(), $staging);
            $this->configure($staging, (string) $input['company_name'], $appUrl, $urlPrefix);

            $token = $this->handoff->create($staging, [
                'company_name' => trim((string) $input['company_name']),
                'app_url' => $appUrl,
                'db' => [
                    'db_host' => $credentials->host,
                    'db_port' => $credentials->port,
                    'db_database' => $credentials->database,
                    'db_username' => $credentials->username,
                    'db_password' => $credentials->password,
                ],
                'admin' => [
                    'name' => trim((string) $input['admin_name']),
                    'email' => (string) $input['admin_email'],
                    'password' => (string) $input['admin_password'],
                ],
            ], $logo);

            if (file_exists($final) || ! @rename($staging, $final)) {
                throw new InstallerException("Could not create the folder \"{$folder}\".");
            }
        } catch (Throwable $e) {
            $this->discardStaging($root, $staging);

            if ($e instanceof InstallerException) {
                throw $e;
            }

            Log::error('Master setup: copy failed', ['folder' => $folder, 'exception' => $e]);
            throw new InstallerException('Copying the application failed. Check storage/logs/laravel.log for details.');
        }

        $this->registry->add([
            'company' => trim((string) $input['company_name']),
            'folder' => $folder,
            'url' => $appUrl,
            'database' => $credentials->database,
        ]);

        return ['folder' => $folder, 'path' => $final, 'app_url' => $appUrl, 'token' => $token];
    }

    /**
     * Points the copied subfolder front controllers at the new URL prefix
     * (they strip it from REQUEST_URI and serve /public files through it).
     *
     * @throws InstallerException
     */
    public function patchFrontControllers(string $path, string $urlPrefix): void
    {
        foreach (['index.php', 'public/index.php'] as $file) {
            $this->replaceInFile($path, $file, [
                "/preg_replace\\('#\\^[^#']*#'/" => "preg_replace('#^" . $urlPrefix . "#'",
            ], 1);
        }

        $this->replaceInFile($path, '.htaccess', [
            '#%\{DOCUMENT_ROOT\}\S*?/public/#' => '%{DOCUMENT_ROOT}' . $urlPrefix . '/public/',
            '#!\^\S*?/public/#' => '!^' . $urlPrefix . '/public/',
        ], 3);
    }

    /** @throws InstallerException */
    private function configure(string $path, string $companyName, string $appUrl, string $urlPrefix): void
    {
        $environment = new EnvironmentWriter($path . DIRECTORY_SEPARATOR . '.env');
        $environment->ensureAppKey();
        $environment->set([
            'APP_NAME' => trim($companyName),
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
            'APP_URL' => $appUrl,
            'APP_MODE' => 'app',
        ]);

        $this->patchFrontControllers($path, $urlPrefix);
    }

    /**
     * @param  array<string, string>  $replacements  regex => replacement (replacement holds only validated path characters)
     *
     * @throws InstallerException
     */
    private function replaceInFile(string $root, string $file, array $replacements, int $expected): void
    {
        $path = $root . DIRECTORY_SEPARATOR . $file;
        $contents = is_file($path) ? (string) file_get_contents($path) : '';
        $total = 0;

        foreach ($replacements as $pattern => $replacement) {
            $contents = (string) preg_replace($pattern, $replacement, $contents, -1, $count);
            $total += $count;
        }

        if ($total !== $expected || @file_put_contents($path, $contents, LOCK_EX) === false) {
            throw new InstallerException("Could not adjust {$file} for the new folder address.");
        }
    }

    /** Removes only the staging folder this request created (never the final or any other folder). */
    private function discardStaging(string $root, string $staging): void
    {
        if (dirname($staging) !== $root
            || preg_match('/^\.[a-z0-9-]+\.partial-[a-f0-9]{8}$/', basename($staging)) !== 1
            || ! is_dir($staging)) {
            return;
        }

        $this->files->deleteDirectory($staging);
    }
}
