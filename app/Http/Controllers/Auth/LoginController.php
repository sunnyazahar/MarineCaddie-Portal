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

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
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
            'browser_latitude' => ['required', 'numeric', 'between:-90,90'],
            'browser_longitude' => ['required', 'numeric', 'between:-180,180'],
            'browser_location_accuracy' => ['nullable', 'numeric', 'min:0'],
        ], [
            'browser_latitude.required' => 'Location permission is required to log in.',
            'browser_longitude.required' => 'Location permission is required to log in.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $request->session()->put('login_client_context', [
            'latitude' => $request->input('browser_latitude'),
            'longitude' => $request->input('browser_longitude'),
            'accuracy' => $request->input('browser_location_accuracy'),
            'screen_resolution' => $request->input('screen_resolution'),
            'language' => $request->input('browser_language'),
            'timezone' => $request->input('browser_timezone'),
        ]);

        app(OtpController::class)->issueOtp($request);

        return redirect()->route('otp.show');
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
            'login_client_context',
        ]);

        return redirect()->route('login');
    }
}
