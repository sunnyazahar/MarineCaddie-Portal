<?php

namespace App\Http\Middleware;

use App\Services\Installer\EnvironmentWriter;
use App\Support\Installation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Global (runs before the web group). Until the install lock exists, sessions /
 * cache / queue use files so no database is needed, an APP_KEY is generated
 * when missing, and every page except the installer redirects to /install.
 * A master setup copy (APP_MODE=master) never has a database and only serves /master.
 */
class RedirectIfNotInstalled
{
    private const ALLOWED_PATHS = ['install', 'install/*', 'branding/logo', 'up'];

    private const MASTER_PATHS = ['master', 'master/*', 'up'];

    public function __construct(private EnvironmentWriter $environment)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $master = Installation::isMaster();

        if (! $master && Installation::isInstalled()) {
            return $next($request);
        }

        config([
            'session.driver' => 'file',
            'cache.default' => 'file',
            'queue.default' => 'sync',
        ]);

        if (trim((string) config('app.key')) === '') {
            try {
                config(['app.key' => $this->environment->ensureAppKey()]);
            } catch (Throwable) {
                return response(
                    'Setup cannot start: the .env file is not writable. Make the project folder writable, then reload.',
                    503,
                    ['Content-Type' => 'text/plain; charset=UTF-8']
                );
            }
        }

        if ($master) {
            return $request->is(...self::MASTER_PATHS) ? $next($request) : redirect()->to(url('/master'));
        }

        if (! $request->is(...self::ALLOWED_PATHS)) {
            return redirect()->to(url('/install'));
        }

        return $next($request);
    }
}
