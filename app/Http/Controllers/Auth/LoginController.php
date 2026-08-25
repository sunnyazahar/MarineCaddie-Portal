<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/otp';

    /**
     * Failed credential attempts before lockout (ThrottlesLogins trait).
     */
    protected int $maxAttempts = 5;

    /**
     * Lockout window in minutes (ThrottlesLogins trait).
     */
    protected int $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'csrfToken']);
        $this->middleware('auth')->only('logout');
        $this->middleware('login.throttle')->only('login');
    }

    protected function credentials(Request $request): array
    {
        return [
            $this->username() => $request->input($this->username()),
            'password' => $request->input('password'),
            'is_active' => true,
        ];
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $request->session()->put('login_client_context', [
            'latitude' => null,
            'longitude' => null,
            'accuracy' => null,
            'screen_resolution' => $request->input('screen_resolution'),
            'language' => $request->input('browser_language'),
            'timezone' => $request->input('browser_timezone'),
        ]);

        if ($this->shouldBypassOtp()) {
            $request->session()->put('otp_verified', true);

            return redirect()->intended('/dashboard');
        }

        $request->session()->forget('otp_verified');

        return redirect()->route('otp.show');
    }

    private function shouldBypassOtp(): bool
    {
        if (app()->environment('production')) {
            return false;
        }

        return app()->environment(['local', 'localhost', 'development', 'testing'])
            && (bool) config('app.local_otp_bypass', false);
    }

    public function csrfToken()
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }

    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    protected function loggedOut(Request $request)
    {
        $request->session()->forget([
            'otp_verified',
            'login_otp_hash',
            'login_otp_expires_at',
            'login_otp_last_sent_at',
            'login_otp_local',
            'login_otp_mail_failed',
            'login_otp_mail_error',
            'login_client_context',
        ]);

        return redirect()->route('login');
    }
}
