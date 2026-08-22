@extends('layouts.app')

@section('styles')
    <style>
        body.create-hub-page {
            padding-bottom: 84px;
        }

        .page-body:has(.create-hub-page) {
            padding: 0 !important;
            margin: 0 !important;
        }

        .create-hub-page {
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

        .create-hub-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 0 0 14px;
            padding: 4px 16px 0;
        }

        .create-hub-hero-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .create-hub-hero-icon {
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

        .create-hub-kicker {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0088c7;
        }

        .create-hub-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .create-hub-sub {
            margin: 6px 0 0;
            font-size: 13px;
            color: #64748b;
            max-width: 36rem;
        }

        .create-hub-back {
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

        .create-hub-back:hover {
            border-color: #00aeef;
            background: #e8f6fc;
            color: #0088c7;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .create-hub-card {
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

        .create-hub-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
            pointer-events: none;
        }

        .hub-form-container {
            width: 100%;
            box-sizing: border-box;
            padding: 20px 16px 24px !important;
            background: transparent;
        }

        .hub-pillars {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            align-items: stretch;
        }

        .hub-pillar-col {
            display: flex;
            min-width: 0;
        }

        .hub-pillar {
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

        .hub-pillar__title {
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

        .form-group-custom {
            margin-bottom: 0;
        }

        .address-sub-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 10px;
        }

        #hubForm .hub-pillar .form-control-custom,
        #hubForm .hub-pillar .form-textarea-custom,
        #hubForm .hub-pillar .select-custom {
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            color: #0e1d4a;
        }

        #hubForm .hub-pillar .form-control-custom,
        #hubForm .hub-pillar .select-custom {
            height: var(--mc-control-height, 34px);
            padding: 0 10px;
        }

        #hubForm .hub-pillar .form-control-custom:focus,
        #hubForm .hub-pillar .form-textarea-custom:focus,
        #hubForm .hub-pillar .select-custom:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #hubForm .hub-pillar .form-textarea-custom {
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

        .hub-form-alert {
            margin-bottom: 16px;
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 10px;
        }

        .error-message {
            color: #dc2626;
            font-size: 11px;
            margin-top: 4px;
            font-weight: 500;
        }

        #hubForm .form-control-custom.error,
        #hubForm .form-textarea-custom.error {
            border-color: #dc2626 !important;
        }

        .select2-container--default.error .select2-selection--single {
            border-color: #dc2626 !important;
        }

        body.create-hub-page .create-hub-footer {
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

        body.create-hub-page .create-hub-footer .btn-save-custom {
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

        body.create-hub-page .create-hub-footer .btn-save-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.32);
        }

        body.create-hub-page .create-hub-footer .btn-cancel-custom {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        body.create-hub-page .create-hub-footer .btn-cancel-custom:hover {
            color: #008080 !important;
            text-decoration: none !important;
        }

        body.create-hub-page .hub-pillar-col,
        body.create-hub-page .hub-form-container,
        body.create-hub-page .create-hub-card,
        body.create-hub-page .hub-pillars {
            overflow: visible !important;
        }

        @media (max-width: 1199.98px) {
            .hub-pillars {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .hub-pillars {
                grid-template-columns: 1fr;
            }

            .address-sub-grid {
                grid-template-columns: 1fr !important;
            }

            body.create-hub-page .create-hub-footer {
                left: 0 !important;
                width: 100vw !important;
                padding: 12px 16px !important;
                flex-wrap: wrap;
            }

            body.create-hub-page .create-hub-footer .btn-save-custom {
                flex: 1 1 auto;
            }

            .create-hub-hero {
                padding: 4px 12px 0;
            }

            .hub-form-container {
                padding: 16px 12px 20px !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('create-hub-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-hub-page">
        <div class="create-hub-hero">
            <div class="create-hub-hero-main">
                <span class="create-hub-hero-icon" aria-hidden="true">
                    <i class="ti-location-pin"></i>
                </span>
                <div>
                    <p class="create-hub-kicker">Administration</p>
                    <h1 class="create-hub-title">Create hub</h1>
                    <p class="create-hub-sub">Add a warehouse hub with address, port, and customer portal settings.</p>
                </div>
            </div>
            <a href="{{ route('hub.index') }}" class="create-hub-back">
                <i class="ti-arrow-left"></i> Back to hubs
            </a>
        </div>

        <div class="create-hub-card">
            <div class="hub-form-container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show hub-form-alert" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form id="hubForm" action="{{ route('hub.store') }}" method="POST">
                    @csrf

                    <div class="hub-pillars">
                        <div class="hub-pillar-col">
                            <div class="hub-pillar">
                                <div class="hub-pillar__title">Hub information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="hub_name">Hub name</label>
                                    <input type="text" id="hub_name" name="hub_name" class="form-control-custom"
                                        value="{{ old('hub_name') }}" required autocomplete="organization">
                                </div>

                                <input type="hidden" name="company_id" value="{{ old('company_id') }}">
                                <input type="hidden" name="customer_number_fm" value="{{ old('customer_number_fm') }}">
                                <input type="hidden" name="is_gts_company" value="0">

                                <div class="address-sub-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="code">Code</label>
                                        <input type="text" id="code" name="code" class="form-control-custom" value="{{ old('code') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="code_description">Code description</label>
                                        <input type="text" id="code_description" name="code_description" class="form-control-custom"
                                            value="{{ old('code_description') }}">
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="phone_number">Phone number (with country code)</label>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number') }}" autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="email">Email</label>
                                    <input type="text" id="email" name="email" class="form-control-custom"
                                        value="{{ old('email') }}" placeholder="email@example.com; email2@example.com">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person') }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="remarks">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="special_considerations">Special considerations for destination</label>
                                    <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                                </div>

                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="show_pre_alert" id="show_pre_alert" value="1"
                                        {{ old('show_pre_alert') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="show_pre_alert">Show pre-alert warning when items in shipment are not scanned</label>
                                </div>
                            </div>
                        </div>

                        <div class="hub-pillar-col">
                            <div class="hub-pillar">
                                <div class="hub-pillar__title">Hub address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="hub_address">Hub address</label>
                                    <textarea id="hub_address" name="hub_address" class="form-textarea-custom" rows="3">{{ old('hub_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="city">City</label>
                                        <input type="text" id="city" name="city" class="form-control-custom" value="{{ old('city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="district_state">District/state</label>
                                        <input type="text" id="district_state" name="district_state" class="form-control-custom"
                                            value="{{ old('district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="zip_code">Zip code</label>
                                        <input type="text" id="zip_code" name="zip_code" class="form-control-custom" value="{{ old('zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="country"
                                    label="Country"
                                    :countries="$countries"
                                    valueKey="name"
                                    :value="old('country')"
                                    wrapperClass="form-group-custom"
                                    :allowClear="true"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="old('port_code')"
                                    wrapperClass="form-group-custom"
                                />
                            </div>
                        </div>

                        <div class="hub-pillar-col">
                            <div class="hub-pillar">
                                <div class="hub-pillar__title">Hub details &amp; portal</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="eori_number">EORI number</label>
                                    <input type="text" id="eori_number" name="eori_number" class="form-control-custom"
                                        value="{{ old('eori_number') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="un_locode">UN/LOCODE</label>
                                    <input type="text" id="un_locode" name="un_locode" class="form-control-custom"
                                        value="{{ old('un_locode') }}">
                                </div>

                                <x-forms.country-select
                                    name="office_country"
                                    label="Office country"
                                    :countries="$countries"
                                    valueKey="name"
                                    :value="old('office_country')"
                                    wrapperClass="form-group-custom"
                                    :allowClear="true"
                                />

                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="hide_in_portal" id="hide_in_portal" value="1"
                                        {{ old('hide_in_portal') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="hide_in_portal">Do not show this hub in Customer portal</label>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="portal_remarks">Remarks for the customer portal</label>
                                    <textarea id="portal_remarks" name="portal_remarks" class="form-textarea-custom" rows="3">{{ old('portal_remarks') }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="portal_email">Email for Customer Portal</label>
                                    <input type="text" id="portal_email" name="portal_email" class="form-control-custom"
                                        value="{{ old('portal_email') }}" placeholder="email@example.com; email2@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="create-hub-footer">
            <button type="submit" class="btn-save-custom" form="hubForm">Save hub</button>
            <a href="{{ route('hub.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('create-hub-page');

            $.validator.addMethod('multiEmail', function (value, element) {
                if (this.optional(element)) {
                    return true;
                }

                var emails = value.split(/[;,]+/).map(function (part) {
                    return $.trim(part);
                }).filter(Boolean);

                if (!emails.length) {
                    return false;
                }

                return emails.every(function (email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                });
            }, 'Please enter valid email address(es), separated by comma or semicolon');

            $('#hubForm').validate({
                rules: {
                    hub_name: {
                        required: true,
                        minlength: 3
                    },
                    contact_person: {
                        required: true
                    },
                    email: {
                        multiEmail: true
                    },
                    portal_email: {
                        multiEmail: true
                    }
                },
                messages: {
                    hub_name: {
                        required: 'Please enter the hub name',
                        minlength: 'Hub name must be at least 3 characters'
                    },
                    contact_person: {
                        required: 'Please enter the contact person'
                    },
                    email: {
                        multiEmail: 'Please enter valid email address(es), separated by comma or semicolon'
                    },
                    portal_email: {
                        multiEmail: 'Please enter valid email address(es), separated by comma or semicolon'
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });

            $(document).on('select2:open', '.hub-pillar select', function () {
                $('.hub-pillar').css('z-index', '');
                $(this).closest('.hub-pillar').css('z-index', 40);
            });

            $(document).on('select2:close', '.hub-pillar select', function () {
                $(this).closest('.hub-pillar').css('z-index', '');
            });
        });
    </script>
@endsection
