<?php

namespace App\Support;

/**
 * Install state is a lock file (no DB lookup) so it can be checked on every
 * request, even before a database exists.
 */
class Installation
{
    public static function lockPath(): string
    {
        $path = (string) config('app.install_lock', 'storage/app/installed.lock');

        return str_starts_with($path, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1
            ? $path
            : base_path($path);
    }

    public static function isInstalled(): bool
    {
        return is_file(self::lockPath());
    }

    /** Master setup copy: serves only the folder-provisioning wizard, never the portal. */
    public static function isMaster(): bool
    {
        return config('app.mode') === 'master';
    }

    /**
     * Application URL derived from the front controller location, for use before
     * APP_URL is configured. The front controllers rewrite REQUEST_URI for subfolder
     * hosting, so Laravel's own base URL detection can lose the folder prefix.
     */
    public static function detectAppUrl(\Illuminate\Http\Request $request): string
    {
        $script = str_replace('\\', '/', (string) $request->server('SCRIPT_NAME', ''));
        $folder = rtrim(str_replace('\\', '/', dirname($script)), '/.');
        $folder = (string) preg_replace('#/public$#', '', $folder);

        // Only allow plain path characters so a crafted SCRIPT_NAME cannot inject a host.
        if ($folder !== '' && preg_match('#^(/[A-Za-z0-9._~-]+)+$#', $folder) !== 1) {
            $folder = '';
        }

        return $request->getSchemeAndHttpHost() . $folder;
    }

    /**
     * @param  array<string, scalar|null>  $meta
     */
    public static function markInstalled(array $meta = []): void
    {
        $payload = json_encode(
            ['installed_at' => now()->toIso8601String()] + $meta,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        if (@file_put_contents(self::lockPath(), (string) $payload, LOCK_EX) === false) {
            throw new \RuntimeException('Could not write ' . self::lockPath() . '. Make storage/app writable.');
        }
    }
}
