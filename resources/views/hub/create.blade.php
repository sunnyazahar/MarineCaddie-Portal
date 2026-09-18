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
            padding: 0;
            background: transparent;
        }

        .form-pillar-container.hub-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            padding: 22px 18px 28px;
            align-items: stretch;
        }

        .form-pillar.hub-pillar-card {
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #cfe0ec;
            border-radius: 14px;
            padding: 18px 18px 16px;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 12px 28px rgba(14, 29, 74, 0.06);
            overflow: visible;
            position: relative;
            z-index: 1;
            min-width: 0;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .form-pillar.hub-pillar-card:hover {
            border-color: #94c9e3;
            box-shadow:
                0 2px 4px rgba(14, 29, 74, 0.05),
                0 16px 32px rgba(0, 136, 199, 0.08);
        }

        .hub-pillar-head {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 4px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e8eef4;
        }

        .hub-pillar-head-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(0, 128, 128, 0.22);
        }

        .hub-pillar-head-icon.is-location {
            background: linear-gradient(135deg, #0e1d4a 0%, #0088c7 100%);
            box-shadow: 0 6px 14px rgba(14, 29, 74, 0.2);
        }

        .hub-pillar-head-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .hub-pillar-head-sub {
            margin: 4px 0 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.35;
        }

        .hub-soft-panel {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 4px;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(180deg, #f8fbfd 0%, #f1f7fb 100%);
            border: 1px solid #dce8f1;
        }

        .hub-soft-panel-title {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #0e1d4a;
        }

        .hub-soft-panel-title span {
            margin-left: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0;
            text-transform: none;
            color: #94a3b8;
        }

        .form-group-custom {
            margin-bottom: 0;
            position: relative;
            overflow: visible;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .input-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .input-row > .form-group-custom {
            flex: 1;
            min-width: 0;
        }

        #hubForm .form-input-custom,
        #hubForm .form-select-custom,
        #hubForm .form-textarea-custom {
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            color: #0e1d4a;
            font-size: 13px;
        }

        #hubForm .form-input-custom,
        #hubForm .form-select-custom {
            height: var(--mc-control-height, 34px);
            padding: 0 10px;
        }

        #hubForm .form-textarea-custom {
            padding: 8px 10px;
            min-height: 72px;
            height: auto;
            overflow-y: hidden;
            resize: none;
            line-height: 1.4;
            box-sizing: border-box;
        }

        #hubForm .form-input-custom:focus,
        #hubForm .form-select-custom:focus,
        #hubForm .form-textarea-custom:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #hubForm .form-input-custom.error,
        #hubForm .form-textarea-custom.error {
            border-color: #dc2626 !important;
        }

        .hub-form-alert {
            margin: 16px 18px 0;
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

        body.create-hub-page .hub-form-container,
        body.create-hub-page .create-hub-card,
        body.create-hub-page .form-pillar-container {
            overflow: visible !important;
        }

        @media (max-width: 991.98px) {
            .form-pillar-container.hub-details-grid {
                grid-template-columns: 1fr !important;
                padding: 16px 12px 20px !important;
            }

            .input-row {
                flex-direction: column;
            }

            .form-pillar.hub-pillar-card:hover {
                box-shadow:
                    0 1px 2px rgba(14, 29, 74, 0.04),
                    0 12px 28px rgba(14, 29, 74, 0.06);
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
                    <p class="create-hub-sub">Add a warehouse hub with identity, contact, and location details.</p>
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

                    <input type="hidden" name="company_id" value="{{ old('company_id') }}">
                    <input type="hidden" name="customer_number_fm" value="{{ old('customer_number_fm') }}">
                    <input type="hidden" name="is_gts_company" value="0">

                    <div class="form-pillar-container hub-details-grid hub-pillars">
                        <div class="form-pillar hub-pillar-card">
                            <div class="hub-pillar-head">
                                <span class="hub-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                <div>
                                    <div class="hub-pillar-head-title">Identity &amp; contact</div>
                                    <p class="hub-pillar-head-sub">Core hub profile and how to reach the desk.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_name">Hub name <span class="text-danger">*</span></label>
                                <input type="text" id="hub_name" name="hub_name" class="form-input-custom"
                                    value="{{ old('hub_name') }}" required autocomplete="organization">
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" id="code" name="code" class="form-input-custom" value="{{ old('code') }}" required>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="code_description">Code description <span class="text-danger">*</span></label>
                                    <input type="text" id="code_description" name="code_description" class="form-input-custom"
                                        value="{{ old('code_description') }}" required>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="phone_number">Phone number (with country code) <span class="text-danger">*</span></label>
                                <input type="text" id="phone_number" name="phone_number" class="form-input-custom"
                                    value="{{ old('phone_number') }}" autocomplete="tel" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" id="email" name="email" class="form-input-custom"
                                    value="{{ old('email') }}" placeholder="email@example.com; email2@example.com" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="contact_person">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" id="contact_person" name="contact_person" class="form-input-custom"
                                    value="{{ old('contact_person') }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="eori_number">EORI number</label>
                                <input type="text" id="eori_number" name="eori_number" class="form-input-custom"
                                    value="{{ old('eori_number') }}">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="remarks">Remarks</label>
                                <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="special_considerations">Notes for consignee</label>
                                <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                            </div>
                        </div>

                        <div class="form-pillar hub-pillar-card">
                            <div class="hub-pillar-head">
                                <span class="hub-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                <div>
                                    <div class="hub-pillar-head-title">Location</div>
                                    <p class="hub-pillar-head-sub">Physical hub address and port identifiers.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_address">Hub address <span class="text-danger">*</span></label>
                                <textarea id="hub_address" name="hub_address" class="form-textarea-custom" rows="3" required>{{ old('hub_address') }}</textarea>
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom" style="flex: 2;">
                                    <label class="form-label-custom" for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" id="city" name="city" class="form-input-custom" value="{{ old('city') }}" required>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="district_state">District/state</label>
                                    <input type="text" id="district_state" name="district_state" class="form-input-custom"
                                        value="{{ old('district_state') }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="zip_code">Zip code</label>
                                    <input type="text" id="zip_code" name="zip_code" class="form-input-custom" value="{{ old('zip_code') }}">
                                </div>
                            </div>

                            <x-forms.country-select
                                name="country"
                                label="Country"
                                :countries="$countries"
                                valueKey="name"
                                :value="old('country')"
                                class="form-select-custom select2-flag"
                                :required="true"
                                :allowClear="false"
                            />

                            <x-forms.port-select
                                name="port_code"
                                label="Port code"
                                :value="old('port_code')"
                                :required="true"
                            />

                            <div class="hub-soft-panel">
                                <div class="hub-soft-panel-title">Office address <span>optional</span></div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_address">Office address</label>
                                    <textarea id="office_address" name="office_address" class="form-textarea-custom" rows="3">{{ old('office_address') }}</textarea>
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom" style="flex: 2;">
                                        <label class="form-label-custom" for="office_city">City</label>
                                        <input type="text" id="office_city" name="office_city" class="form-input-custom" value="{{ old('office_city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_district_state">District/state</label>
                                        <input type="text" id="office_district_state" name="office_district_state" class="form-input-custom"
                                            value="{{ old('office_district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_zip_code">Zip code</label>
                                        <input type="text" id="office_zip_code" name="office_zip_code" class="form-input-custom"
                                            value="{{ old('office_zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="office_country"
                                    label="Country"
                                    :countries="$countries"
                                    valueKey="name"
                                    :value="old('office_country')"
                                    class="form-select-custom select2-flag"
                                    :allowClear="true"
                                />
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

            function autoResizeHubTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeHubTextareas() {
                $('#hubForm textarea.form-textarea-custom').each(function () {
                    autoResizeHubTextarea(this);
                });
            }

            $(document).on('input.autoResizeHubTextarea change.autoResizeHubTextarea', '#hubForm textarea.form-textarea-custom', function () {
                autoResizeHubTextarea(this);
            });

            refreshAutoResizeHubTextareas();

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
                    code: {
                        required: true
                    },
                    code_description: {
                        required: true
                    },
                    phone_number: {
                        required: true
                    },
                    email: {
                        required: true,
                        multiEmail: true
                    },
                    contact_person: {
                        required: true
                    },
                    hub_address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    country: {
                        required: true
                    },
                    port_code: {
                        required: true
                    }
                },
                messages: {
                    hub_name: {
                        required: 'Please enter the hub name',
                        minlength: 'Hub name must be at least 3 characters'
                    },
                    code: {
                        required: 'Please enter the code'
                    },
                    code_description: {
                        required: 'Please enter the code description'
                    },
                    phone_number: {
                        required: 'Please enter the phone number'
                    },
                    email: {
                        required: 'Please enter the email',
                        multiEmail: 'Please enter valid email address(es), separated by comma or semicolon'
                    },
                    contact_person: {
                        required: 'Please enter the contact person'
                    },
                    hub_address: {
                        required: 'Please enter the hub address'
                    },
                    city: {
                        required: 'Please enter the city'
                    },
                    country: {
                        required: 'Please select the country'
                    },
                    port_code: {
                        required: 'Please select the port code'
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

            $(document).on('select2:open', '.hub-pillar-card select', function () {
                $('.hub-pillar-card').css('z-index', '');
                $(this).closest('.hub-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.hub-pillar-card select', function () {
                $(this).closest('.hub-pillar-card').css('z-index', '');
            });
        });
    </script>
@endsection
