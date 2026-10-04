<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use App\Services\CompanySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CompanySettingsController extends Controller
{
    /** OTP can only be switched on within this window after a successful test mail. */
    private const TEST_MAIL_VALID_MINUTES = 30;

    private const TEST_MAIL_SESSION_KEY = 'company_settings.test_mail_ok_until';

    public function __construct(
        private CompanySettingsRepositoryInterface $settings,
        private CompanySettingsService $service,
    ) {
    }

    public function edit(Request $request): View
    {
        return view('settings.company', [
            'settings' => $this->settings->current(),
            'testMailVerified' => $this->testMailVerified($request),
            'mailer' => (string) config('mail.default'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'bank_account_name' => ['nullable', 'string', 'max:200'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_iban' => ['nullable', 'string', 'max:50'],
            'bank_swift' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_city_country' => ['nullable', 'string', 'max:150'],
            'invoice_notes' => ['nullable', 'string', 'max:5000'],
            'proforma_prefix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/'],
            'otp_enabled' => ['nullable', 'boolean'],
            'logo' => CompanySettingsService::LOGO_RULES,
        ]);

        $wantsOtp = $request->boolean('otp_enabled');
        $otpAlreadyOn = (bool) $this->settings->current()?->otp_enabled;

        if ($wantsOtp && ! $otpAlreadyOn && ! $this->testMailVerified($request)) {
            return back()
                ->withInput()
                ->withErrors(['otp_enabled' => 'Send a successful test email first, then enable login verification codes (OTP).']);
        }

        $attributes = collect($validated)->except(['logo', 'otp_enabled'])->all();
        $attributes['otp_enabled'] = $wantsOtp;

        try {
            $this->service->update($attributes, $request->file('logo'), $request->user()?->id);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['logo' => $e->getMessage()]);
        }

        return redirect()->route('settings.company.edit')->with('success', 'Company settings saved.');
    }

    public function testEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:150'],
        ]);

        try {
            $this->service->sendTestEmail($validated['test_email']);
        } catch (RuntimeException $e) {
            $request->session()->forget(self::TEST_MAIL_SESSION_KEY);

            return back()->with('error', $e->getMessage());
        }

        $request->session()->put(self::TEST_MAIL_SESSION_KEY, now()->addMinutes(self::TEST_MAIL_VALID_MINUTES)->getTimestamp());

        return back()->with('success', 'Test email sent to ' . $validated['test_email'] . '. If it arrived, you can now enable OTP.');
    }

    private function testMailVerified(Request $request): bool
    {
        return (int) $request->session()->get(self::TEST_MAIL_SESSION_KEY, 0) > now()->getTimestamp();
    }
}
