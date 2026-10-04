<?php

namespace App\Services\Master;

use App\Services\Installer\InstallerException;
use App\Services\Installer\RequirementsChecker;
use App\Support\Installation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Where the master setup copies from / to and how new folders map to web addresses. */
final class MasterSettings
{
    /** Lower-case letters, numbers and inner dashes (2–40 chars): safe as a folder name and URL segment. */
    public const FOLDER_PATTERN = '/^[a-z0-9][a-z0-9-]{0,38}[a-z0-9]$/';

    private const URL_PATH_PATTERN = '#^(/[A-Za-z0-9._~-]+)*$#';

    public function __construct(private ProjectCopier $copier, private RequirementsChecker $requirements)
    {
    }

    public function sourcePath(): string
    {
        return rtrim((string) (config('master.source_path') ?: base_path()), '/\\');
    }

    public function targetRoot(): string
    {
        return rtrim((string) (config('master.target_root') ?: dirname(base_path())), '/\\');
    }

    public function folderPath(string $folder): string
    {
        return $this->targetRoot() . DIRECTORY_SEPARATOR . $folder;
    }

    /** Web address of the target root, e.g. "https://example.com" (no trailing slash). */
    public function targetBaseUrl(Request $request): string
    {
        $configured = rtrim(trim((string) config('master.target_url')), '/');
        if ($configured !== '') {
            return $configured;
        }

        $selfPath = (string) parse_url(Installation::detectAppUrl($request), PHP_URL_PATH);
        $parent = rtrim(str_replace('\\', '/', dirname($selfPath)), '/.');

        return $request->getSchemeAndHttpHost() . $parent;
    }

    public function appUrl(Request $request, string $folder): string
    {
        return $this->targetBaseUrl($request) . '/' . $folder;
    }

    /**
     * URL path of a new folder ("/acme" or "/clients/acme"), used to patch its front controllers.
     *
     * @throws InstallerException
     */
    public function urlPrefix(Request $request, string $folder): string
    {
        $basePath = rtrim((string) parse_url($this->targetBaseUrl($request), PHP_URL_PATH), '/');
        if (preg_match(self::URL_PATH_PATTERN, $basePath) !== 1) {
            throw new InstallerException('MASTER_TARGET_URL contains unsupported characters in its path.');
        }

        return $basePath . '/' . $folder;
    }

    public function isReserved(string $folder): bool
    {
        $reserved = array_map('strtolower', (array) config('master.reserved_folders', []));
        $reserved[] = strtolower(basename(base_path()));
        $reserved[] = strtolower(basename($this->sourcePath()));

        return in_array(strtolower($folder), $reserved, true);
    }

    public function suggestFolder(string $companyName): string
    {
        $slug = trim(Str::limit(Str::slug($companyName), 40, ''), '-');

        return strlen($slug) >= 2 ? $slug : 'portal';
    }

    /**
     * @return list<array{label: string, ok: bool, detail: string}>
     */
    public function checks(Request $request): array
    {
        $checks = array_values(array_filter(
            $this->requirements->check(),
            fn (array $check): bool => str_starts_with($check['label'], 'PHP')
        ));

        $passwordSet = trim((string) config('master.password_hash')) !== '';
        $checks[] = [
            'label' => 'Master password',
            'ok' => $passwordSet,
            'detail' => $passwordSet ? 'Set' : 'Run: php artisan master:password',
        ];

        $missing = $this->copier->missingEntries($this->sourcePath());
        $checks[] = [
            'label' => 'Application source',
            'ok' => $missing === [],
            'detail' => $missing === [] ? $this->sourcePath() : 'Missing: ' . implode(', ', $missing),
        ];

        $root = $this->targetRoot();
        $writable = is_dir($root) && is_writable($root);
        $checks[] = [
            'label' => 'New folders are created in',
            'ok' => $writable,
            'detail' => $root . ($writable ? '' : ' — not writable by the web server'),
        ];

        $documentRoot = (string) $request->server('DOCUMENT_ROOT', '');
        $target = $this->targetBaseUrl($request);
        if ($documentRoot !== '' && parse_url($target, PHP_URL_HOST) === $request->getHost()) {
            $expected = realpath($documentRoot . (string) parse_url($target, PHP_URL_PATH));
            $matches = $expected !== false && $expected === realpath($root);
            $checks[] = [
                'label' => 'Folder matches web address',
                'ok' => $matches,
                'detail' => $matches ? $target . '/<folder>' : 'Set MASTER_TARGET_URL so it points at ' . $root,
            ];
        }

        return $checks;
    }
}
