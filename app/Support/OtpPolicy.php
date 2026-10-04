<?php

namespace App\Support;

class OtpPolicy
{
    /**
     * OTP is skipped when an admin has not enabled it in Company Settings
     * (requires a verified test email), or for the explicit local dev bypass.
     */
    public static function shouldBypass(): bool
    {
        if (! Branding::otpEnabled()) {
            return true;
        }

        if (app()->environment('production')) {
            return false;
        }

        return app()->environment(['local', 'localhost', 'development', 'testing'])
            && (bool) config('app.local_otp_bypass', false);
    }
}
