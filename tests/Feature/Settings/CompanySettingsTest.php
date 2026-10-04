<?php

namespace Tests\Feature\Settings;

use App\Models\CompanySetting;
use App\Models\ProformaInvoice;
use App\Models\Shipment;
use App\Models\User;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use App\Services\ProformaNumberGenerator;
use App\Support\Branding;
use App\Support\CompanyAddress;
use App\Support\LiveHosts;
use App\Support\ProformaInvoiceBankDetails;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\RegressionTestCase;

class CompanySettingsTest extends RegressionTestCase
{
    private function saveSettings(array $attributes): void
    {
        app(CompanySettingsRepositoryInterface::class)->save(array_merge(['company_name' => 'Acme Shipping'], $attributes));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'company_name' => 'Acme Shipping',
            'legal_name' => 'Acme Shipping LLC',
            'address_line_1' => '1 Harbour Road',
            'phone' => '+1 555 0100',
            'email' => 'ops@acme.test',
            'bank_name' => 'Acme Bank',
            'invoice_notes' => "First note\n\nSecond note",
        ], $overrides);
    }

    public function test_admin_can_save_company_settings_and_branding_reflects_them(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAsVerified($admin)
            ->post(route('settings.company.update'), $this->validPayload())
            ->assertRedirect(route('settings.company.edit'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Acme Shipping', Branding::name());
        $this->assertSame('Acme Shipping LLC', CompanyAddress::name());
        $this->assertSame(['1 Harbour Road'], Branding::addressLines());
        $this->assertSame(['First note', 'Second note'], ProformaInvoiceBankDetails::toArray()['notes']);
        $this->assertSame('Acme Bank', ProformaInvoiceBankDetails::toArray()['bank_name']);
        $this->assertSame($admin->id, CompanySetting::query()->value('updated_by'));
    }

    public function test_non_admin_cannot_open_or_update_company_settings(): void
    {
        $user = User::factory()->create(['role' => 'Operations', 'is_active' => true]);

        $this->actingAsVerified($user)->get(route('settings.company.edit'))->assertForbidden();
        $this->actingAsVerified($user)->post(route('settings.company.update'), $this->validPayload())->assertForbidden();
    }

    public function test_otp_cannot_be_enabled_without_a_successful_test_email(): void
    {
        $this->saveSettings(['otp_enabled' => false]);

        $this->actingAsVerified($this->createAdminUser())
            ->from(route('settings.company.edit'))
            ->post(route('settings.company.update'), $this->validPayload(['otp_enabled' => '1']))
            ->assertSessionHasErrors('otp_enabled');

        $this->assertFalse(Branding::otpEnabled());
    }

    public function test_otp_can_be_enabled_after_a_successful_test_email(): void
    {
        $this->saveSettings(['otp_enabled' => false]);

        $this->actingAsVerified($this->createAdminUser())
            ->withSession(['company_settings.test_mail_ok_until' => now()->addMinutes(10)->getTimestamp()])
            ->post(route('settings.company.update'), $this->validPayload(['otp_enabled' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertTrue(Branding::otpEnabled());
    }

    public function test_test_email_is_refused_while_mailer_only_logs(): void
    {
        config(['mail.default' => 'log']);

        $this->actingAsVerified($this->createAdminUser())
            ->from(route('settings.company.edit'))
            ->post(route('settings.company.test-email'), ['test_email' => 'admin@acme.test'])
            ->assertSessionHas('error')
            ->assertSessionMissing('company_settings.test_mail_ok_until');
    }

    public function test_svg_logo_upload_is_rejected(): void
    {
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $this->actingAsVerified($this->createAdminUser())
            ->from(route('settings.company.edit'))
            ->post(route('settings.company.update'), $this->validPayload(['logo' => $svg]))
            ->assertSessionHasErrors('logo');
    }

    public function test_login_skips_otp_and_records_activity_when_otp_is_disabled(): void
    {
        config(['app.local_otp_bypass' => false]);
        $this->saveSettings(['otp_enabled' => false]);
        $user = $this->createAdminUser(['email' => 'admin@acme.test']);

        $this->post('/login', $this->loginPayload('admin@acme.test'))->assertRedirect('/dashboard');

        $this->assertTrue(session('otp_verified'));
        $this->assertDatabaseHas('user_login_activities', ['user_id' => $user->id]);
    }

    public function test_login_requires_otp_when_enabled(): void
    {
        config(['app.local_otp_bypass' => false]);
        $this->saveSettings(['otp_enabled' => true]);
        $this->createAdminUser(['email' => 'admin@acme.test']);

        $this->post('/login', $this->loginPayload('admin@acme.test'))->assertRedirect(route('otp.show'));

        $this->assertNull(session('otp_verified'));
    }

    public function test_branding_logo_falls_back_to_initials_icon(): void
    {
        $this->saveSettings(['company_name' => 'Acme Shipping', 'logo_path' => null]);

        $this->get(route('branding.logo'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('AS', false);
    }

    public function test_branding_ignores_logo_paths_outside_branding_directory(): void
    {
        $this->saveSettings(['logo_path' => '../../.env']);

        $this->assertNull(Branding::logoPath());
        $this->assertSame('', Branding::logoDataUri());
    }

    public function test_live_hosts_support_exact_and_wildcard_entries(): void
    {
        config(['app.live_hosts' => ['portal.acme.test', '*.example.com']]);

        $this->assertTrue(LiveHosts::matches('portal.acme.test'));
        $this->assertTrue(LiveHosts::matches('APP.EXAMPLE.COM'));
        $this->assertTrue(LiveHosts::matches('example.com'));
        $this->assertFalse(LiveHosts::matches('evil-example.com'));
        $this->assertFalse(LiveHosts::matches('localhost'));
        $this->assertFalse(LiveHosts::matches(''));
    }

    public function test_initials_split_camel_case_single_word_names(): void
    {
        $this->saveSettings(['company_name' => 'BlueOcean']);
        $this->assertSame('BO', Branding::initials());

        $this->saveSettings(['company_name' => 'ACME']);
        $this->assertSame('A', Branding::initials());

        $this->saveSettings(['company_name' => 'Acme Shipping Lines']);
        $this->assertSame('AS', Branding::initials());
    }

    public function test_proforma_numbers_use_configured_prefix_or_company_initials(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');
        $generator = app(ProformaNumberGenerator::class);

        $this->saveSettings(['proforma_prefix' => null]);
        $this->assertSame('AS-26-27-0001', $generator->previewNext());

        $this->saveSettings(['proforma_prefix' => 'ACM-INV']);
        $this->assertSame('ACM-INV26-27-0001', $generator->previewNext());
    }

    public function test_invoice_prefix_is_validated_on_settings_save(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAsVerified($admin)
            ->post(route('settings.company.update'), $this->validPayload(['proforma_prefix' => 'AC/../<b>']))
            ->assertSessionHasErrors('proforma_prefix');

        $this->actingAsVerified($admin)
            ->post(route('settings.company.update'), $this->validPayload(['proforma_prefix' => 'AC-UK']))
            ->assertSessionHasNoErrors();

        $this->assertSame('AC-UK', Branding::proformaPrefix());
    }

    public function test_mark_installed_keeps_existing_proforma_prefix(): void
    {
        $shipment = Shipment::create(['shipment_number' => 'PREFIX-1', 'status' => 'In process', 'service' => 'Airfreight']);
        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0042',
            'financial_year_label' => '26-27',
            'sequence_no' => 42,
        ]);
        $this->saveSettings(['proforma_prefix' => null]);

        $this->artisan('app:install', ['--mark-installed' => true])->assertSuccessful();

        $this->assertSame('MC-AE', Branding::proformaPrefix());
    }
}
