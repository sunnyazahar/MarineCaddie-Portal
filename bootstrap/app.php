<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'otp.verified' => \App\Http\Middleware\EnsureOtpIsVerified::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'ops.admin.readonly' => \App\Http\Middleware\DenyOperationsAdministrationWrite::class,
            'login.throttle' => \App\Http\Middleware\ThrottleLoginAttempts::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetSecurityHeaders::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
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
