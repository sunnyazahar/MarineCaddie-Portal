<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureMasterAuthenticated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MasterAuthController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->session()->has(EnsureMasterAuthenticated::SESSION_KEY)) {
            return redirect()->route('master.show');
        }

        return view('master.login', ['passwordConfigured' => $this->passwordHash() !== '']);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate(['master_password' => ['required', 'string', 'max:255']]);
        $hash = $this->passwordHash();

        if ($hash === '' || ! $this->passwordMatches($validated['master_password'], $hash)) {
            return back()->withErrors(['master_password' => 'The master password is incorrect.']);
        }

        $request->session()->regenerate();
        $request->session()->put(EnsureMasterAuthenticated::SESSION_KEY, time());

        return redirect()->intended(route('master.show'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('master.login');
    }

    private function passwordHash(): string
    {
        return trim((string) config('master.password_hash'));
    }

    private function passwordMatches(string $password, string $hash): bool
    {
        try {
            return Hash::check($password, $hash);
        } catch (Throwable $e) {
            Log::warning('Master setup: MASTER_PASSWORD_HASH is not a valid hash. Run php artisan master:password.');

            return false;
        }
    }
}
