<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginActivityService;
use App\Mail\LoginOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    private const OTP_LENGTH = 6;
    private const OTP_TTL_MINUTES = 10;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const MAX_OTP_ATTEMPTS = 5;
    private const BLOCK_MINUTES = 30;
    private const VERIFY_RATE_LIMIT_ATTEMPTS = 10;
    private const VERIFY_RATE_LIMIT_DECAY_SECONDS = 300;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        if ($request->session()->get('otp_verified') === true) {
            return redirect()->intended('/dashboard');
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $blockedSecondsLeft = $this->blockedSecondsLeft($user);

        $needsNewOtp = blank($user->login_otp_hash)
            || blank($user->login_otp_expires_at)
            || now()->greaterThan($user->login_otp_expires_at);

        if ($needsNewOtp && ! $blockedSecondsLeft) {
            $this->issueOtp($request);
            $user->refresh();
        }

        return response()
            ->view('auth.otp', [
                'maskedEmail'       => $this->maskEmail((string) $user->email),
                'resendAvailableIn' => $this->resendAvailableIn($user),
                'localOtp'          => $this->localDebugOtp($request),
                'otpMailFailed'     => (bool) $request->session()->get('login_otp_mail_failed'),
                'otpMailError'      => $request->session()->get('login_otp_mail_error'),
                'blockedSecondsLeft'=> $blockedSecondsLeft,
                'attemptsLeft'      => max(0, self::MAX_OTP_ATTEMPTS - (int) $user->otp_failed_attempts),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function verify(Request $request, LoginActivityService $loginActivityService)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:' . self::OTP_LENGTH, 'regex:/^\d+$/'],
        ], [
            'otp.required' => 'Please enter the verification code.',
            'otp.size' => 'The verification code must be ' . self::OTP_LENGTH . ' digits.',
            'otp.regex' => 'The verification code must contain only numbers.',
        ]);

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $verifyRateKey = $this->verifyRateLimitKey($request);
        if (RateLimiter::tooManyAttempts($verifyRateKey, self::VERIFY_RATE_LIMIT_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($verifyRateKey);

            throw ValidationException::withMessages([
                'otp' => "Too many verification attempts. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        // Check if user is currently blocked.
        $blockedSecondsLeft = $this->blockedSecondsLeft($user);
        if ($blockedSecondsLeft > 0) {
            RateLimiter::hit($verifyRateKey, self::VERIFY_RATE_LIMIT_DECAY_SECONDS);
            $minutes = ceil($blockedSecondsLeft / 60);
            throw ValidationException::withMessages([
                'otp' => "Your account is temporarily locked due to too many failed attempts. Please try again in {$minutes} minute(s).",
            ]);
        }

        $storedOtpHash = $user->login_otp_hash;
        $expiresAt = $user->login_otp_expires_at;

        if (! $storedOtpHash || ! $expiresAt || now()->greaterThan($expiresAt)) {
            RateLimiter::hit($verifyRateKey, self::VERIFY_RATE_LIMIT_DECAY_SECONDS);
            throw ValidationException::withMessages([
                'otp' => 'This code has expired. Please request a new one.',
            ]);
        }

        // Wrong OTP entered.
        if (! hash_equals((string) $storedOtpHash, hash('sha256', (string) $request->input('otp')))) {
            RateLimiter::hit($verifyRateKey, self::VERIFY_RATE_LIMIT_DECAY_SECONDS);
            $attempts = (int) $user->otp_failed_attempts + 1;

            if ($attempts >= self::MAX_OTP_ATTEMPTS) {
                // Block the user.
                $blockedUntil = now()->addMinutes(self::BLOCK_MINUTES);
                $user->forceFill([
                    'otp_failed_attempts' => $attempts,
                    'otp_blocked_until'   => $blockedUntil,
                    'login_otp_hash'      => null,
                    'login_otp_expires_at'=> null,
                    'login_otp_sent_at'   => null,
                ])->saveQuietly();

                Log::warning('OTP user blocked after too many failed attempts', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'blocked_until' => $blockedUntil,
                ]);

                throw ValidationException::withMessages([
                    'otp' => 'Too many incorrect attempts. Your account has been temporarily locked for ' . self::BLOCK_MINUTES . ' minutes.',
                ]);
            }

            $remaining = self::MAX_OTP_ATTEMPTS - $attempts;
            $user->forceFill(['otp_failed_attempts' => $attempts])->saveQuietly();

            throw ValidationException::withMessages([
                'otp' => "Invalid verification code. You have {$remaining} attempt(s) remaining.",
            ]);
        }

        // Correct OTP — clear all OTP state and reset attempt counter.
        $user->forceFill([
            'login_otp_hash'       => null,
            'login_otp_expires_at' => null,
            'login_otp_sent_at'    => null,
            'otp_failed_attempts'  => 0,
            'otp_blocked_until'    => null,
        ])->saveQuietly();

        $request->session()->forget([
            'login_otp_local',
            'login_otp_mail_failed',
            'login_otp_mail_error',
            'login_otp_hash',
            'login_otp_expires_at',
            'login_otp_last_sent_at',
        ]);
        RateLimiter::clear($verifyRateKey);
        $request->session()->put('otp_verified', true);
        $this->terminateOtherSessions($request);
        $loginActivityService->record($request, $user);

        return redirect()->intended('/dashboard');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $blockedSecondsLeft = $this->blockedSecondsLeft($user);
            if ($blockedSecondsLeft > 0) {
                $minutes = ceil($blockedSecondsLeft / 60);
                throw ValidationException::withMessages([
                    'otp' => "Your account is temporarily locked. Please try again in {$minutes} minute(s).",
                ]);
            }
        }

        $key = $this->resendRateLimitKey($request);

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'otp' => "Please wait {$seconds} seconds before requesting another code.",
            ]);
        }

        $this->issueOtp($request);
        RateLimiter::hit($key, self::RESEND_COOLDOWN_SECONDS);

        return back()->with('status', 'A new verification code has been sent.');
    }

    public function issueOtp(Request $request): string
    {
        $otp = str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
        $user = $request->user();

        if (! $user) {
            return $otp;
        }

        $sentAt = now();

        // Save a one-way hash of the OTP against the logged-in user record.
        $user->forceFill([
            'login_otp_hash' => hash('sha256', $otp),
            'login_otp_expires_at' => $sentAt->copy()->addMinutes(self::OTP_TTL_MINUTES),
            'login_otp_sent_at' => $sentAt,
        ])->saveQuietly();

        $request->session()->forget('otp_verified');

        $this->deliverOtp($user->email, $otp);

        return $otp;
    }

    private function deliverOtp(?string $email, string $otp): void
    {
        $delivered = false;
        $mailer = (string) config('mail.default');
        $failureReason = null;
        $nonDeliveringMailers = ['log', 'array'];

        if ($email) {
            try {
                // log/array never reach an inbox — treat as failure before sending.
                if (in_array($mailer, $nonDeliveringMailers, true)) {
                    $failureReason = "MAIL_MAILER={$mailer} only writes to the log and does not send email. Set MAIL_MAILER=sendmail on the server, then run: php artisan config:clear";
                    Log::error('OTP email not sent: MAIL_MAILER is set to a non-delivering driver.', [
                        'email' => $email,
                        'mailer' => $mailer,
                        'env' => app()->environment(),
                    ]);
                } else {
                    Mail::to($email)->send(new LoginOtpMail(
                        $otp,
                        self::OTP_TTL_MINUTES,
                    ));

                    // Local macOS/XAMPP sendmail accepts mail but cannot relay externally.
                    $delivered = ! (
                        app()->environment(['local', 'localhost', 'development', 'testing'])
                        && $mailer === 'sendmail'
                    );

                    if (! $delivered) {
                        $failureReason = 'Local system mailer cannot deliver external email. Use the on-screen code.';
                    }
                }
            } catch (\Throwable $e) {
                $failureReason = 'Mail send failed ('.$mailer.'): '.$e->getMessage();
                Log::warning('OTP email failed to send: ' . $e->getMessage(), [
                    'email' => $email,
                    'mailer' => $mailer,
                ]);
            }
        } else {
            $failureReason = 'Your account has no email address configured.';
            Log::warning('OTP email skipped: user has no email address.');
        }

        // Always record delivery result so production can show a useful error.
        session([
            'login_otp_mail_failed' => ! $delivered,
            'login_otp_mail_error' => $delivered ? null : $failureReason,
        ]);

        // Local/dev only: surface the code when real inbox delivery is unavailable.
        if ($this->shouldExposeLocalOtp()) {
            session([
                'login_otp_local' => $otp,
            ]);
            Log::info('Local OTP code issued', [
                'email' => $email,
                'otp' => $otp,
                'mailer' => $mailer,
                'mail_delivered' => $delivered,
            ]);
        }
    }

    private function localDebugOtp(Request $request): ?string
    {
        if (! $this->shouldExposeLocalOtp()) {
            return null;
        }

        $otp = $request->session()->get('login_otp_local');

        return is_string($otp) && $otp !== '' ? $otp : null;
    }

    /**
     * Show the OTP on-screen only outside production (local XAMPP, etc.).
     */
    private function shouldExposeLocalOtp(): bool
    {
        if (app()->environment('production')) {
            return false;
        }

        return app()->environment(['local', 'localhost', 'development', 'testing']);
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(2, strlen($local)));
        $maskedLocal = $visible . str_repeat('*', max(strlen($local) - strlen($visible), 3));

        return $maskedLocal . '@' . $domain;
    }

    private function resendAvailableIn($user): int
    {
        if (! $user || ! $user->login_otp_sent_at) {
            return 0;
        }

        $elapsed = max(0, now()->getTimestamp() - $user->login_otp_sent_at->getTimestamp());

        return max(0, self::RESEND_COOLDOWN_SECONDS - $elapsed);
    }

    private function blockedSecondsLeft($user): int
    {
        if (! $user || ! $user->otp_blocked_until) {
            return 0;
        }

        $secondsLeft = now()->diffInSeconds($user->otp_blocked_until, false);

        return max(0, (int) $secondsLeft);
    }

    private function resendRateLimitKey(Request $request): string
    {
        return 'otp-resend:' . ($request->user()?->id ?? $request->ip());
    }

    private function verifyRateLimitKey(Request $request): string
    {
        return 'otp-verify:' . ($request->user()?->id ?? 'guest') . ':' . $request->ip();
    }

    private function terminateOtherSessions(Request $request): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        $user->setRememberToken(Str::random(60));
        $user->saveQuietly();
    }
}
