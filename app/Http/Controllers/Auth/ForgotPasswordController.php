<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    private const RESET_LINK_ATTEMPTS = 3;
    private const RESET_LINK_DECAY_SECONDS = 900;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $key = $this->resetLinkRateLimitKey($request);
        if (RateLimiter::tooManyAttempts($key, self::RESET_LINK_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many reset requests. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        RateLimiter::hit($key, self::RESET_LINK_DECAY_SECONDS);

        Password::broker()->sendResetLink(
            $this->credentials($request)
        );

        return back()->with('status', 'If an account exists for that email address, a password reset link has been sent.');
    }

    private function resetLinkRateLimitKey(Request $request): string
    {
        return 'password-reset-link:' . sha1(strtolower((string) $request->input('email'))) . ':' . $request->ip();
    }
}
