<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/master.php',
            __DIR__ . '/../routes/install.php',
            __DIR__ . '/../routes/web.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(\App\Http\Middleware\RedirectIfNotInstalled::class);

        $middleware->alias([
            'install.pending' => \App\Http\Middleware\EnsureNotInstalled::class,
            'master.auth' => \App\Http\Middleware\EnsureMasterAuthenticated::class,
            'otp.verified' => \App\Http\Middleware\EnsureOtpIsVerified::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'billing' => \App\Http\Middleware\EnsureUserCanAccessBilling::class,
            'ops.admin.readonly' => \App\Http\Middleware\DenyOperationsAdministrationWrite::class,
            'accounts.readonly' => \App\Http\Middleware\DenyAccountsWriteOutsideBilling::class,
            'login.throttle' => \App\Http\Middleware\ThrottleLoginAttempts::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetSecurityHeaders::class,
        ]);

        // Posted by the master setup page (different folder, no shared session); the
        // one-time handoff token is the credential, and the route 404s once installed.
        $middleware->validateCsrfTokens(except: ['install/handoff']);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
            'db_password',
            'admin_password',
            'admin_password_confirmation',
            'master_password',
            'master_password_confirmation',
        ]);

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session expired. Please refresh and try again.',
                ], 419);
            }

            if ($request->user()) {
                Auth::logout();
            }

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')
                ->with('status', 'Your session expired. Please log in again.');
        });
    })->create();
