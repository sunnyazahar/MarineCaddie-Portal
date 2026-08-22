@extends('layouts.app')

@section('styles')
    <style>
        body.create-office-page {
            padding-bottom: 84px;
        }

        .page-body:has(.create-office-page) {
            padding: 0 !important;
            margin: 0 !important;
        }

        .create-office-page {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 12px 0 28px;
            background:
                radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
                radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
                #f5f7fb;
            box-sizing: border-box;
        }

        .create-office-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 0 0 14px;
            padding: 4px 16px 0;
        }

        .create-office-hero-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .create-office-hero-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0, 128, 128, 0.28);
        }

        .create-office-kicker {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0088c7;
        }

        .create-office-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .create-office-sub {
            margin: 6px 0 0;
            font-size: 13px;
            color: #64748b;
            max-width: 36rem;
        }

        .create-office-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #d6e3ee;
            background: #fff;
            color: #0088c7;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(14, 29, 74, 0.04);
            transition: border-color 0.15s ease, background 0.15s ease, transform 0.15s ease;
        }

        .create-office-back:hover {
            border-color: #00aeef;
            background: #e8f6fc;
            color: #0088c7;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .create-office-card {
            position: relative;
            width: 100%;
            max-width: none;
            margin: 0;
            background: #fff;
            border: none;
            border-top: 1px solid rgba(214, 227, 238, 0.95);
            border-bottom: 1px solid rgba(214, 227, 238, 0.95);
            border-radius: 0;
            box-shadow: 0 8px 24px rgba(14, 29, 74, 0.04);
            overflow: visible;
        }

        .create-office-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
            pointer-events: none;
        }

        .office-form-container {
            width: 100%;
            box-sizing: border-box;
            padding: 20px 16px 24px !important;
            background: transparent;
        }

        .office-pillars {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            align-items: stretch;
        }

        .office-pillar-col {
            display: flex;
            min-width: 0;
        }

        .office-pillar {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            padding: 14px 14px 12px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .office-pillar__title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin: 0 0 4px;
            padding: 0 0 10px 10px;
            border-bottom: 1px solid #e8eef4;
            border-left: 3px solid #00aeef;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .office-pillar__title-text {
            min-width: 0;
        }

        .office-section-shell {
            margin-top: 4px;
            padding: 12px 12px 10px;
            background: linear-gradient(180deg, #f8fcfd 0%, #ffffff 100%);
            border: 1px dashed #c5dde8;
            border-radius: 10px;
        }

        .office-section-shell__title {
            margin: 0 0 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }

        .form-group-custom {
            margin-bottom: 0;
        }

        .address-sub-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .address-sub-grid.is-two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #office-form .office-pillar .form-control-custom,
        #office-form .office-pillar .form-textarea-custom,
        #office-form .office-pillar .select-custom {
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            color: #0e1d4a;
        }

        #office-form .office-pillar .form-control-custom,
        #office-form .office-pillar .select-custom {
            height: var(--mc-control-height, 34px);
            padding: 0 10px;
        }

        #office-form .office-pillar .form-control-custom:focus,
        #office-form .office-pillar .form-textarea-custom:focus,
        #office-form .office-pillar .select-custom:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #office-form .office-pillar .form-textarea-custom {
            padding: 8px 10px;
            min-height: 72px;
            resize: vertical;
            line-height: 1.4;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 4px;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #008080;
        }

        .checkbox-label {
            font-size: 13px;
            color: #475569;
            line-height: 1.35;
        }

        .btn-add-account {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #008080;
            background: #fff;
            border: 1px solid #94c9e3;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .btn-add-account:hover {
            background: #e8f6fc;
            border-color: #00aeef;
            transform: translateY(-1px);
        }

        .account-block {
            padding: 12px 0 14px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e8eef4;
        }

        .account-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .account-row-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 10px;
        }

        .remove-account-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s ease, background 0.15s ease;
        }

        .remove-account-btn:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        .office-form-alert {
            margin-bottom: 16px;
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 10px;
        }

        body.create-office-page .create-office-footer {
            position: fixed !important;
            left: var(--spacing-sidebar, 13.25rem) !important;
            right: 0 !important;
            bottom: 0 !important;
            margin: 0 !important;
            width: calc(100vw - var(--spacing-sidebar, 13.25rem)) !important;
            max-width: none !important;
            padding: 12px 28px !important;
            box-sizing: border-box !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(8px);
            display: flex !important;
            align-items: center !important;
            gap: 16px;
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            z-index: 1040 !important;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06);
        }

        body.create-office-page .create-office-footer .btn-save-custom {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            color: #fff !important;
            border: none !important;
            padding: 10px 28px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        body.create-office-page .create-office-footer .btn-save-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.32);
        }

        body.create-office-page .create-office-footer .btn-cancel-custom {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        body.create-office-page .create-office-footer .btn-cancel-custom:hover {
            color: #008080 !important;
            text-decoration: none !important;
        }

        body.create-office-page .office-pillar-col,
        body.create-office-page .office-form-container,
        body.create-office-page .create-office-card,
        body.create-office-page .office-pillars {
            overflow: visible !important;
        }

        @media (max-width: 1399.98px) {
            .office-pillars {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .office-pillars {
                grid-template-columns: 1fr;
            }

            .address-sub-grid,
            .account-row-grid {
                grid-template-columns: 1fr !important;
            }

            body.create-office-page .create-office-footer {
                left: 0 !important;
                width: 100vw !important;
                padding: 12px 16px !important;
                flex-wrap: wrap;
            }

            body.create-office-page .create-office-footer .btn-save-custom {
                flex: 1 1 auto;
            }

            .create-office-hero {
                padding: 4px 12px 0;
            }

            .office-form-container {
                padding: 16px 12px 20px !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('create-office-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-office-page">
        <div class="create-office-hero">
            <div class="create-office-hero-main">
                <span class="create-office-hero-icon" aria-hidden="true">
                    <i class="ti-home"></i>
                </span>
                <div>
                    <p class="create-office-kicker">Administration</p>
                    <h1 class="create-office-title">Create office</h1>
                    <p class="create-office-sub">Add a regional office with address, billing settings, and bank accounts.</p>
                </div>
            </div>
            <a href="{{ route('offices.index') }}" class="create-office-back">
                <i class="ti-arrow-left"></i> Back to offices
            </a>
        </div>

        <div class="create-office-card">
            <div class="office-form-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show office-form-alert" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show office-form-alert" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form action="{{ route('offices.store') }}" method="POST" id="office-form">
                    @csrf

                    <div class="office-pillars">
                        {{-- Pillar 1: Office information --}}
                        <div class="office-pillar-col">
                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Office information</span>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Office name</label>
                                    <input type="text" class="form-control-custom" name="office_name"
                                        value="{{ old('office_name') }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Office short name</label>
                                    <input type="text" class="form-control-custom" name="office_short_name"
                                        value="{{ old('office_short_name') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Phone number (with country code)</label>
                                    <input type="text" class="form-control-custom" name="phone_number"
                                        value="{{ old('phone_number') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Email</label>
                                    <input type="email" class="form-control-custom" name="email"
                                        value="{{ old('email') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">EORI number</label>
                                    <input type="text" class="form-control-custom" name="eori_number"
                                        value="{{ old('eori_number') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Pillar 2: Addresses --}}
                        <div class="office-pillar-col">
                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Main address</span>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Office address</label>
                                    <textarea class="form-textarea-custom" name="address" rows="3">{{ old('address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">City</label>
                                        <input type="text" class="form-control-custom" name="city"
                                            value="{{ old('city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">District/state</label>
                                        <input type="text" class="form-control-custom" name="district_state"
                                            value="{{ old('district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Zip code</label>
                                        <input type="text" class="form-control-custom" name="zip_code"
                                            value="{{ old('zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="country_id"
                                    label="Country"
                                    :countries="$countries"
                                    wrapperClass="form-group-custom"
                                />

                                <div class="office-section-shell">
                                    <p class="office-section-shell__title">Office address (optional)</p>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Postal address (optional)</label>
                                        <textarea class="form-textarea-custom" name="postal_address" rows="3">{{ old('postal_address') }}</textarea>
                                    </div>

                                    <div class="address-sub-grid">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">City</label>
                                            <input type="text" class="form-control-custom" name="postal_city"
                                                value="{{ old('postal_city') }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">District/state</label>
                                            <input type="text" class="form-control-custom" name="postal_district_state"
                                                value="{{ old('postal_district_state') }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Zip code</label>
                                            <input type="text" class="form-control-custom" name="postal_zip_code"
                                                value="{{ old('postal_zip_code') }}">
                                        </div>
                                    </div>

                                    <x-forms.country-select
                                        name="office_country_id"
                                        label="Country"
                                        :countries="$countries"
                                        wrapperClass="form-group-custom"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Pillar 3: Billing --}}
                        <div class="office-pillar-col">
                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Billing details</span>
                                </div>

                                <div class="address-sub-grid is-two-col">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Invoicing currency</label>
                                        <select class="form-control-custom select2-simple" name="invoicing_currency">
                                            <option value="">Select currency</option>
                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                <option value="{{ $curr }}" {{ old('invoicing_currency') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Reporting currency</label>
                                        <select class="form-control-custom select2-simple" name="reporting_currency">
                                            <option value="">Select currency</option>
                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                <option value="{{ $curr }}" {{ old('reporting_currency') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">VAT rates</label>
                                    <select class="select-custom" name="vat_rates">
                                        <option value="standard">Standard</option>
                                        <option value="zero">Zero</option>
                                        <option value="exempt">Exempt</option>
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">VAT country specific name</label>
                                    <input type="text" class="form-control-custom" name="vat_country_specific_name"
                                        value="{{ old('vat_country_specific_name') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">VAT number</label>
                                    <input type="text" class="form-control-custom" name="vat_number"
                                        value="{{ old('vat_number') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Invoicing e-mails</label>
                                    <input type="email" class="form-control-custom" name="invoicing_emails"
                                        value="{{ old('invoicing_emails') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Heading of invoice</label>
                                    <input type="text" class="form-control-custom" name="heading_invoice"
                                        value="{{ old('heading_invoice') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Information on invoice</label>
                                    <textarea class="form-textarea-custom" name="information_invoice" rows="4">{{ old('information_invoice') }}</textarea>
                                </div>

                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="use_vat_check" id="use_vat_check" {{ old('use_vat_check') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="use_vat_check">Use VAT check when creating invoice</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="show_imo" id="show_imo" {{ old('show_imo') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="show_imo">Show IMO numbers on invoices</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="enable_reader" id="enable_reader" {{ old('enable_reader') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="enable_reader">Enable incoming invoice reader</label>
                                </div>
                            </div>
                        </div>

                        {{-- Pillar 4: Accounts --}}
                        <div class="office-pillar-col">
                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Accounts</span>
                                    <button type="button" class="btn-add-account">
                                        <i class="ti-plus"></i> Add account
                                    </button>
                                </div>
                                <div id="accounts-container"></div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="create-office-footer">
            <button type="submit" class="btn-save-custom" form="office-form">Save office</button>
            <a href="{{ route('offices.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script>
        $(document).ready(function () {
            $('body').addClass('create-office-page');

            $('.select2-simple').select2({
                width: '100%'
            });

            $('.select2-company').select2({
                width: '100%'
            });

            var currencies = {!! json_encode($countries->pluck('currency')->unique()->filter()->sort()->values()) !!};
            var currencyOptions = '<option value="">Select currency</option>' +
                currencies.map(function (curr) {
                    return '<option value="' + curr + '">' + curr + '</option>';
                }).join('');

            $('.btn-add-account').on('click', function () {
                var accountHtml = ''
                    + '<div class="account-block">'
                    + '  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">'
                    + '    <label class="form-label-custom" style="margin-bottom:0;">Bank</label>'
                    + '    <button type="button" class="remove-account-btn" title="Remove account">'
                    + '      <i class="feather icon-trash-2" style="font-size:16px;"></i>'
                    + '    </button>'
                    + '  </div>'
                    + '  <textarea class="form-textarea-custom" name="bank[]" rows="3" style="margin-top:8px;"></textarea>'
                    + '  <div class="account-row-grid">'
                    + '    <div class="form-group-custom">'
                    + '      <label class="form-label-custom">Currency</label>'
                    + '      <select class="form-control-custom select2-simple-dynamic" name="currency[]">' + currencyOptions + '</select>'
                    + '    </div>'
                    + '    <div class="form-group-custom">'
                    + '      <label class="form-label-custom">Account number</label>'
                    + '      <input type="text" class="form-control-custom" name="account_number[]">'
                    + '    </div>'
                    + '  </div>'
                    + '  <div class="account-row-grid">'
                    + '    <div class="form-group-custom">'
                    + '      <label class="form-label-custom">IBAN</label>'
                    + '      <input type="text" class="form-control-custom" name="iban[]">'
                    + '    </div>'
                    + '    <div class="form-group-custom">'
                    + '      <label class="form-label-custom">Swift</label>'
                    + '      <input type="text" class="form-control-custom" name="swift[]">'
                    + '    </div>'
                    + '  </div>'
                    + '  <div class="checkbox-group" style="margin-top:12px;">'
                    + '    <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="0">'
                    + '    <input type="checkbox" class="checkbox-custom main-account-checkbox" id="main-account-' + Date.now() + '">'
                    + '    <label class="checkbox-label">Set as main account</label>'
                    + '  </div>'
                    + '</div>';

                var $newAccount = $(accountHtml);
                $('#accounts-container').append($newAccount);
                $newAccount.find('.select2-simple-dynamic').select2({ width: '100%' });
            });

            $(document).on('change', '.main-account-checkbox', function () {
                if ($(this).is(':checked')) {
                    $('.main-account-checkbox').not(this).prop('checked', false);
                    $('.main-account-hidden').val('0');
                    $(this).closest('.checkbox-group').find('.main-account-hidden').val('1');
                } else {
                    $(this).closest('.checkbox-group').find('.main-account-hidden').val('0');
                }
            });

            $(document).on('click', '.remove-account-btn', function () {
                $(this).closest('.account-block').remove();
            });

            $(document).on('select2:open', '.office-pillar select', function () {
                $('.office-pillar').css('z-index', '');
                $(this).closest('.office-pillar').css('z-index', 40);
            });

            $(document).on('select2:close', '.office-pillar select', function () {
                $(this).closest('.office-pillar').css('z-index', '');
            });
        });
    </script>
@endsection
