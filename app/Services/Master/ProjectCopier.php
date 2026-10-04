<?php

namespace App\Services\Master;

use App\Services\Installer\InstallerException;
use DirectoryIterator;

/**
 * Copies only what a working install needs. Allow-listed so secrets (.env),
 * git history, tests, node_modules, logs and uploaded files never travel.
 */
final class ProjectCopier
{
    public const ENTRIES = [
        'app', 'bootstrap', 'config', 'database', 'lang', 'public', 'resources', 'routes', 'vendor',
        'artisan', 'composer.json', 'composer.lock', 'index.php', '.htaccess', '.env.example',
    ];

    public const REQUIRED = [
        'app', 'bootstrap/app.php', 'config', 'database', 'public/index.php', 'resources', 'routes',
        'vendor/autoload.php', 'artisan', 'index.php', '.htaccess', '.env.example',
    ];

    /** Empty runtime folders the new copy needs (its storage starts clean). */
    public const RUNTIME_DIRECTORIES = [
        'storage/app/private', 'storage/app/public', 'storage/framework/cache/data',
        'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache',
    ];

    /** Relative paths skipped inside copied folders: build caches and runtime links. */
    private const SKIP_PATHS = ['bootstrap/cache', 'public/storage', 'public/hot'];

    private const SKIP_NAMES = ['.DS_Store'];

    /** @return list<string> */
    public function missingEntries(string $source): array
    {
        return array_values(array_filter(
            self::REQUIRED,
            fn (string $entry): bool => ! file_exists($source . DIRECTORY_SEPARATOR . $entry)
        ));
    }

    /**
     * @return int number of files copied
     *
     * @throws InstallerException
     */
    public function copy(string $source, string $target): int
    {
        if ($missing = $this->missingEntries($source)) {
            throw new InstallerException('The application source is incomplete (missing: ' . implode(', ', $missing) . ').');
        }

        $this->makeDirectory($target, 0755);
        $count = 0;

        foreach (self::ENTRIES as $entry) {
            $from = $source . DIRECTORY_SEPARATOR . $entry;
            if (! file_exists($from) || is_link($from)) {
                continue;
            }

            $to = $target . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($from)) {
                $count += $this->copyDirectory($from, $to, $entry);
            } else {
                $this->copyFile($from, $to);
                $count++;
            }
        }

        foreach (self::RUNTIME_DIRECTORIES as $directory) {
            $this->makeDirectory($target . DIRECTORY_SEPARATOR . $directory, 0775);
        }

        return $count;
    }

    /** @throws InstallerException */
    private function copyDirectory(string $from, string $to, string $relative): int
    {
        $this->makeDirectory($to, 0755);
        $count = 0;

        foreach (new DirectoryIterator($from) as $item) {
            if ($item->isDot() || in_array($item->getFilename(), self::SKIP_NAMES, true)) {
                continue;
            }

            $itemRelative = $relative . '/' . $item->getFilename();
            // Links are skipped so a copy can never reach outside the source tree.
            if ($item->isLink() || in_array($itemRelative, self::SKIP_PATHS, true)) {
                continue;
            }

            $destination = $to . DIRECTORY_SEPARATOR . $item->getFilename();
            if ($item->isDir()) {
                $count += $this->copyDirectory($item->getPathname(), $destination, $itemRelative);
            } else {
                $this->copyFile($item->getPathname(), $destination);
                $count++;
            }
        }

        return $count;
    }

    /** @throws InstallerException */
    private function copyFile(string $from, string $to): void
    {
        if (! @copy($from, $to)) {
            throw new InstallerException('Could not copy ' . basename($from) . ' into the new folder.');
        }
    }

    /** @throws InstallerException */
    private function makeDirectory(string $path, int $mode): void
    {
        if (! is_dir($path) && ! @mkdir($path, $mode, true) && ! is_dir($path)) {
            throw new InstallerException('Could not create folder ' . basename($path) . '.');
        }
        @chmod($path, $mode);
    }
}
