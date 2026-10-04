<?php

namespace App\Services\Installer;

use App\Models\User;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use App\Services\CompanySettingsService;
use App\Support\Installation;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Runs a fresh install: prepare DB → write .env → migrate → seed lookup data →
 * company settings + first admin → lock file. Shared by the web wizard and
 * `php artisan app:install`.
 */
class InstallerService
{
    public function __construct(
        private DatabaseProvisioner $database,
        private EnvironmentWriter $environment,
        private CompanySettingsService $companySettings,
        private CompanySettingsRepositoryInterface $settingsRepository,
    ) {
    }

    /**
     * @param  array{name: string, email: string, password: string}  $admin
     *
     * @throws InstallerException
     */
    public function install(
        DatabaseCredentials $credentials,
        string $companyName,
        array $admin,
        string $appUrl,
        ?UploadedFile $logo = null,
    ): void {
        if (Installation::isInstalled()) {
            throw new InstallerException('This application is already installed.');
        }

        @set_time_limit(300);

        $this->database->prepare($credentials);

        $this->writeEnvironment($credentials, $companyName, $appUrl);
        $this->useDatabase($credentials);

        $this->runArtisan('migrate', ['--force' => true]);
        $this->runArtisan('db:seed', ['--class' => MasterDataSeeder::class, '--force' => true]);

        // Stored only after the schema is ready so a failed attempt leaves no orphan upload.
        $logoPath = $logo !== null ? $this->companySettings->storeLogo($logo) : null;

        DB::transaction(function () use ($companyName, $logoPath, $admin): void {
            $this->settingsRepository->save([
                'company_name' => $companyName,
                'logo_path' => $logoPath,
                'otp_enabled' => false,
            ]);

            User::query()->create([
                'name' => $admin['name'],
                'email' => strtolower(trim($admin['email'])),
                'password' => $admin['password'],
                'role' => 'Admin',
                'is_active' => true,
            ]);
        });

        Installation::markInstalled(['company' => $companyName, 'database' => $credentials->database]);

        $this->tryArtisan('storage:link');
        $this->tryArtisan('config:clear');
    }

    private function writeEnvironment(DatabaseCredentials $credentials, string $companyName, string $appUrl): void
    {
        $this->environment->ensureAppKey();
        $this->environment->set([
            'APP_NAME' => $companyName,
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
            'APP_URL' => rtrim($appUrl, '/'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $credentials->host,
            'DB_PORT' => $credentials->port,
            'DB_DATABASE' => $credentials->database,
            'DB_USERNAME' => $credentials->username,
            'DB_PASSWORD' => $credentials->password,
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
            'QUEUE_CONNECTION' => 'database',
            'MAIL_FROM_NAME' => $companyName,
        ]);
    }

    /** Points the running request at the new database (the written .env applies from the next request). */
    private function useDatabase(DatabaseCredentials $credentials): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $credentials->host,
            'database.connections.mysql.port' => $credentials->port,
            'database.connections.mysql.database' => $credentials->database,
            'database.connections.mysql.username' => $credentials->username,
            'database.connections.mysql.password' => $credentials->password,
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');
    }

    /** @throws InstallerException */
    private function runArtisan(string $command, array $parameters): void
    {
        try {
            $exitCode = Artisan::call($command, $parameters);
        } catch (Throwable $e) {
            Log::error('Installer: ' . $command . ' failed', ['exception' => $e]);
            throw new InstallerException('Setup step "' . $command . '" failed: ' . $e->getMessage());
        }

        if ($exitCode !== 0) {
            Log::error('Installer: ' . $command . ' exit ' . $exitCode, ['output' => Artisan::output()]);
            throw new InstallerException('Setup step "' . $command . '" failed. Check storage/logs/laravel.log.');
        }
    }

    private function tryArtisan(string $command): void
    {
        try {
            Artisan::call($command);
        } catch (Throwable $e) {
            Log::warning('Installer: optional step ' . $command . ' failed: ' . $e->getMessage());
        }
    }
}
