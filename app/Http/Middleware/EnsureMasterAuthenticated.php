<?php

namespace App\Http\Middleware;

use App\Support\Installation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Master setup pages require the master password; the session expires after idle minutes. */
class EnsureMasterAuthenticated
{
    public const SESSION_KEY = 'master.authenticated_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (! Installation::isMaster()) {
            abort(404);
        }

        $authenticatedAt = (int) $request->session()->get(self::SESSION_KEY, 0);
        $idleSeconds = max(5, (int) config('master.session_idle_minutes', 60)) * 60;

        if ($authenticatedAt === 0 || time() - $authenticatedAt > $idleSeconds) {
            $request->session()->forget(self::SESSION_KEY);

            return $request->expectsJson()
                ? response()->json(['message' => 'Your session expired. Log in again.'], 401)
                : redirect()->guest(route('master.login'));
        }

        $request->session()->put(self::SESSION_KEY, time());

        return $next($request);
    }
}
