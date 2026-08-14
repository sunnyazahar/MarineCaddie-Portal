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

        $needsNewOtp = blank($user->login_otp_hash)
            || blank($user->login_otp_expires_at)
            || now()->greaterThan($user->login_otp_expires_at);

        if ($needsNewOtp) {
            $this->issueOtp($request);
            $user->refresh();
        } elseif (
            $this->shouldExposeLocalOtp()
            && ! $request->session()->has('login_otp_local')
        ) {
            // Local/dev: re-issue so the on-screen debug code is available.
            $this->issueOtp($request);
            $user->refresh();
        }

        return response()
            ->view('auth.otp', [
                'maskedEmail' => $this->maskEmail((string) $user->email),
                'resendAvailableIn' => $this->resendAvailableIn($user),
                'localOtp' => $this->localDebugOtp($request),
                'otpMailFailed' => (bool) $request->session()->get('login_otp_mail_failed'),
                'otpMailError' => $request->session()->get('login_otp_mail_error'),
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

        $hash = $user->login_otp_hash;
        $expiresAt = $user->login_otp_expires_at;

        if (! $hash || ! $expiresAt || now()->greaterThan($expiresAt)) {
            throw ValidationException::withMessages([
                'otp' => 'This code has expired. Please request a new one.',
            ]);
        }

        if (! hash_equals((string) $hash, hash('sha256', $request->input('otp')))) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid verification code. Please try again.',
            ]);
        }

        $user->forceFill([
            'login_otp_hash' => null,
            'login_otp_expires_at' => null,
            'login_otp_sent_at' => null,
        ])->saveQuietly();

        $request->session()->forget([
            'login_otp_local',
            'login_otp_mail_failed',
            'login_otp_mail_error',
            'login_otp_hash',
            'login_otp_expires_at',
            'login_otp_last_sent_at',
        ]);
        $request->session()->put('otp_verified', true);
        $this->terminateOtherSessions($request);
        $loginActivityService->record($request, $user);

        return redirect()->intended('/dashboard');
    }

    public function resend(Request $request)
    {
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

        // Save / update OTP against the logged-in user record.
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

        return app()->environment(['local', 'localhost', 'development', 'testing'])
            || (bool) config('app.debug');
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

    private function resendRateLimitKey(Request $request): string
    {
        return 'otp-resend:' . ($request->user()?->id ?? $request->ip());
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
