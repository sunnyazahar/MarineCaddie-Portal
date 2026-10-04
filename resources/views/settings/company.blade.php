@extends('layouts.app')

@section('styles')
    @include('offices.partials.edit-page-styles')
    <style>
        .company-logo-preview {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            background: #fff;
        }

        .company-logo-preview img {
            max-width: 200px;
            max-height: 80px;
            object-fit: contain;
        }

        .company-logo-preview__hint,
        .company-settings-hint {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 0;
        }

        .company-otp-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 8px 0;
        }

        .company-otp-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .company-test-mail {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .company-test-mail .form-group-custom {
            flex: 1 1 220px;
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('edit-office-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-office-page">
        <div class="edit-office-hero">
            <div class="edit-office-hero-main">
                <span class="edit-office-hero-icon" aria-hidden="true">
                    <i class="ti-settings"></i>
                </span>
                <div>
                    <p class="edit-office-kicker">Settings</p>
                    <h1 class="edit-office-title">Company settings</h1>
                    <p class="edit-office-sub">Name, logo, address, bank details and login security used across screens, emails and PDFs.</p>
                </div>
            </div>
        </div>

        <div class="edit-office-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-office-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show edit-office-alert" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-office-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <form id="companySettingsForm" class="office-form-controls" action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="office-form-container">
                    <div class="office-pillars">
                        <div class="office-pillar">
                            <div class="office-pillar__title">
                                <span class="office-pillar__title-text">Company</span>
                            </div>

                            <div class="office-primary-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="company_name">Company name (shown in app, emails, PDFs)</label>
                                    <input type="text" id="company_name" name="company_name" class="form-control-custom" maxlength="150" required
                                        value="{{ old('company_name', $settings?->company_name) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="legal_name">Legal / registered name</label>
                                    <input type="text" id="legal_name" name="legal_name" class="form-control-custom" maxlength="200"
                                        value="{{ old('legal_name', $settings?->legal_name) }}">
                                    <p class="company-settings-hint">Used on invoices and PDF footers. Leave empty to use the company name.</p>
                                </div>
                            </div>

                            <div class="office-section-shell">
                                <p class="office-section-shell__title">Logo</p>
                                <div class="company-logo-preview">
                                    @if ($settings?->logo_path)
                                        <img src="{{ route('branding.logo', ['v' => $settings->updated_at?->getTimestamp()]) }}" alt="{{ $settings->company_name }}">
                                    @else
                                        <span class="company-logo-preview__hint">No logo uploaded — the company name is shown instead.</span>
                                    @endif
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="logo">Upload new logo</label>
                                    <input type="file" id="logo" name="logo" class="form-control-custom" accept="image/png,image/jpeg,image/webp">
                                    <p class="company-settings-hint">PNG, JPG or WEBP, max 2 MB. A wide logo with a transparent background works best.</p>
                                </div>
                            </div>

                            <div class="office-section-shell">
                                <p class="office-section-shell__title">Address &amp; contact</p>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="address_line_1">Address line 1</label>
                                    <input type="text" id="address_line_1" name="address_line_1" class="form-control-custom" maxlength="255"
                                        value="{{ old('address_line_1', $settings?->address_line_1) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="address_line_2">Address line 2 (city, country)</label>
                                    <input type="text" id="address_line_2" name="address_line_2" class="form-control-custom" maxlength="255"
                                        value="{{ old('address_line_2', $settings?->address_line_2) }}">
                                </div>
                                <div class="office-primary-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="phone">Phone</label>
                                        <input type="text" id="phone" name="phone" class="form-control-custom" maxlength="50"
                                            value="{{ old('phone', $settings?->phone) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="email">Email</label>
                                        <input type="email" id="email" name="email" class="form-control-custom" maxlength="150"
                                            value="{{ old('email', $settings?->email) }}">
                                    </div>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="website">Website</label>
                                    <input type="url" id="website" name="website" class="form-control-custom" maxlength="255" placeholder="https://"
                                        value="{{ old('website', $settings?->website) }}">
                                </div>
                            </div>
                        </div>

                        <div class="office-pillar">
                            <div class="office-pillar__title">
                                <span class="office-pillar__title-text">Invoice bank details</span>
                            </div>

                            <div class="office-primary-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_account_name">Account name</label>
                                    <input type="text" id="bank_account_name" name="bank_account_name" class="form-control-custom" maxlength="200"
                                        value="{{ old('bank_account_name', $settings?->bank_account_name) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_account_number">Account number</label>
                                    <input type="text" id="bank_account_number" name="bank_account_number" class="form-control-custom" maxlength="50"
                                        value="{{ old('bank_account_number', $settings?->bank_account_number) }}">
                                </div>
                            </div>
                            <div class="office-primary-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_iban">IBAN</label>
                                    <input type="text" id="bank_iban" name="bank_iban" class="form-control-custom" maxlength="50"
                                        value="{{ old('bank_iban', $settings?->bank_iban) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_swift">SWIFT code</label>
                                    <input type="text" id="bank_swift" name="bank_swift" class="form-control-custom" maxlength="20"
                                        value="{{ old('bank_swift', $settings?->bank_swift) }}">
                                </div>
                            </div>
                            <div class="office-primary-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_name">Bank name</label>
                                    <input type="text" id="bank_name" name="bank_name" class="form-control-custom" maxlength="150"
                                        value="{{ old('bank_name', $settings?->bank_name) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="bank_city_country">City / country</label>
                                    <input type="text" id="bank_city_country" name="bank_city_country" class="form-control-custom" maxlength="150"
                                        value="{{ old('bank_city_country', $settings?->bank_city_country) }}">
                                </div>
                            </div>

                            <div class="office-section-shell">
                                <p class="office-section-shell__title">Invoices</p>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="proforma_prefix">Invoice number prefix</label>
                                    <input type="text" id="proforma_prefix" name="proforma_prefix" class="form-control-custom" maxlength="20"
                                        pattern="[A-Za-z0-9\-]+" placeholder="{{ \App\Support\Branding::initials() }}-"
                                        value="{{ old('proforma_prefix', $settings?->proforma_prefix) }}">
                                    <p class="company-settings-hint">Letters, numbers and dashes. New invoices look like {{ \App\Support\Branding::proformaPrefix() }}26-27-0001.</p>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoice_notes">One note per line (printed under NOTE on invoices)</label>
                                    <textarea id="invoice_notes" name="invoice_notes" class="form-textarea-custom" rows="5" maxlength="5000">{{ old('invoice_notes', implode("\n", $settings?->invoice_notes ?? [])) }}</textarea>
                                </div>
                            </div>

                            <div class="office-section-shell">
                                <p class="office-section-shell__title">Login security</p>
                                <div class="company-otp-row">
                                    <input type="hidden" name="otp_enabled" value="0">
                                    <input type="checkbox" id="otp_enabled" name="otp_enabled" value="1"
                                        @checked(old('otp_enabled', $settings?->otp_enabled ? '1' : '0') === '1')
                                        @disabled(! $settings?->otp_enabled && ! $testMailVerified)>
                                    <label for="otp_enabled" class="mb-0">Require an emailed verification code (OTP) at login</label>
                                </div>
                                @if (! $settings?->otp_enabled && ! $testMailVerified)
                                    <p class="company-settings-hint">Send a test email below first. OTP can only be enabled once email delivery works, so nobody gets locked out.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="office-section-shell" style="margin: 0 16px 16px;">
                <p class="office-section-shell__title">Email delivery test</p>
                <form action="{{ route('settings.company.test-email') }}" method="POST" class="company-test-mail">
                    @csrf
                    <div class="form-group-custom">
                        <label class="form-label-custom" for="test_email">Send a test email to</label>
                        <input type="email" id="test_email" name="test_email" class="form-control-custom" maxlength="150" required
                            value="{{ old('test_email', auth()->user()?->email) }}">
                    </div>
                    <button type="submit" class="btn-save-custom">Send test email</button>
                </form>
                <p class="company-settings-hint">Current mailer: <strong>{{ $mailer }}</strong>. SMTP details are configured in the server .env file.</p>
            </div>
        </div>

        <div class="edit-footer">
            <button type="submit" class="btn-save-custom" form="companySettingsForm">Save settings</button>
            <a href="{{ route('dashboard') }}" class="btn-cancel-custom">Cancel</a>
            @if ($settings)
                <div class="audit-info">
                    Last updated {{ $settings->updated_at?->format('d M Y H:i') }}
                </div>
            @endif
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection
