@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet"
        href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        /* Premium Form Styles */
        .main-body .page-wrapper {
            padding: 0;
        }

        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 25px;
            padding: 20px;
            background: #fff;
            min-height: 600px;
        }

        .form-pillar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1f4356;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-group-custom {
            margin-bottom: 0px;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 500;
            color: #3c485a;
            margin-bottom: 4px;
            display: block;
        }

        .form-control-custom {
            height: 32px;
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 0 10px;
            width: 100%;
            background: #fff;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            outline: none;
        }

        .form-textarea-custom {
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 10px;
            width: 100%;
            resize: none;
        }

        .input-with-append {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-append-btn {
            position: absolute;
            right: 1px;
            background: #e5e7eb;
            border: none;
            padding: 0 8px;
            height: 30px;
            border-radius: 0 4px 4px 0;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select-custom {
            height: 32px;
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 0 5px;
            width: 100%;
            background: #fff;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
        }

        .checkbox-label {
            font-size: 12px;
            color: #4b5563;
        }

        .address-sub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .btn-add-account {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 4px 12px;
            font-size: 12px;
            border-radius: 4px;
            font-weight: 500;
        }

        .edit-footer {
            background: #fff;
            padding: 20px 30px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: -5px;
        }

        .btn-save-custom {
            background: #1b5e6f;
            color: #fff;
            padding: 8px 25px;
            border-radius: 4px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Remove Select2 background and match height */
        .select2-container--default .select2-selection--single {
            background-color: transparent !important;
            border: 1px solid #f3f4f6 !important;
            /* Match other inputs */
            height: 32px !important;
            border-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            line-height: 32px !important;
            padding-left: 10px !important;
            font-size: 12px !important;
            color: #4b5563 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        /* Style the dropdown list itself */
        .select2-dropdown {
            border: 1px solid #f3f4f6 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        .select2-results__option {
            font-size: 12px !important;
            padding: 8px 10px !important;
        }

        .btn-cancel-custom {
            color: #3b82f6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        /* Dynamic Account Styles */
        .account-block {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .account-block:last-child {
            border-bottom: none;
        }

        .account-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .remove-account-btn {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .remove-account-btn:hover {
            color: #ef4444;
        }

        .page-body {
            background-color: #FFFFFF;
        }

        @media (max-width: 1199.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 20px;
                padding: 16px;
            }
        }

        @media (max-width: 991.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr !important;
                gap: 24px;
                padding: 12px;
                min-height: 0;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-pillar {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .address-sub-grid {
                grid-template-columns: 1fr !important;
                gap: 12px;
            }

            .account-row-grid {
                grid-template-columns: 1fr !important;
            }

            .form-section-header {
                margin-bottom: 12px;
            }

            .card-block[style*="padding: 25px"] {
                padding: 12px !important;
            }

            .card-header[style*="padding: 15px 25px"] {
                padding: 12px 16px !important;
                height: auto !important;
            }

            .edit-footer {
                flex-wrap: wrap;
                padding: 16px;
                gap: 12px;
            }

            .btn-save-custom {
                width: 100%;
                text-align: center;
            }

            .form-label-custom,
            .checkbox-label {
                overflow: visible;
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .checkbox-group {
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            @include('layouts.top-menu')
            @include('layouts.left-menu')
            <!-- Page-body start -->
            <br>
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- Main-body start -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- Page-header start -->
                            <div class="page-header">

                            </div>
                            <!-- Page-header end -->

                            <!-- Page-body start -->
                            <div class="page-body">
                                <!-- Base Style - Compact start -->
                                <div class="card-header"
                                    style="height: 60px; padding: 15px 25px; display: flex; align-items: center; justify-content: space-between;">
                                    <h5 style="font-size: 16px; font-weight: 600; color: #1f2937;">Create Office</h5>
                                </div>
                                <div class="card-block" style="padding: 25px;">

                                    @if(session('success'))
                                        <div class="alert alert-success"
                                            style="padding: 10px 15px; background-color: #ecfdf5; border-color: #10b981; color: #065f46; font-size: 13px; border-radius: 6px; margin-bottom: 20px;">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger"
                                            style="padding: 10px 15px; background-color: #fef2f2; border-color: #ef4444; color: #991b1b; font-size: 13px; border-radius: 6px; margin-bottom: 20px;">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('offices.store') }}" method="POST">
                                        @csrf
                                        <div class="form-pillar-container">
                                            <!-- Pillar 1: Office information -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Office information</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office name</label>
                                                    <div class="input-with-append">
                                                        <input type="text" class="form-control-custom" name="office_name"
                                                            value="{{ old('office_name') }}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office short name</label>
                                                    <input type="text" class="form-control-custom" name="office_short_name"
                                                        value="{{ old('office_short_name') }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Phone number (with country
                                                        code)</label>
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

                                            <!-- Pillar 2: Main address & Office address (Optional) -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Main address</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office address</label>
                                                    <textarea class="form-textarea-custom" name="address"
                                                        rows="3">{{ old('address') }}</textarea>
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

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Country</label>
                                                    <div class="input-with-append">
                                                        <div style="flex: 1; position: relative;">
                                                            <select class="form-control-custom select2-flag"
                                                                name="country_id">
                                                                <option value="">Select country</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->id }}"
                                                                        data-flag="{{ $country->flag_url }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                                        {{ $country->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-section-header" style="margin-top: 20px;">Office address
                                                    (Optional)</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Postal address (optional)</label>
                                                    <textarea class="form-textarea-custom" name="postal_address"
                                                        rows="3">{{ old('postal_address') }}</textarea>
                                                </div>

                                                <div class="address-sub-grid">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">City</label>
                                                        <input type="text" class="form-control-custom" name="postal_city"
                                                            value="{{ old('postal_city') }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">District/state</label>
                                                        <input type="text" class="form-control-custom"
                                                            name="postal_district_state"
                                                            value="{{ old('postal_district_state') }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Zip code</label>
                                                        <input type="text" class="form-control-custom"
                                                            name="postal_zip_code" value="{{ old('postal_zip_code') }}">
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Country</label>
                                                    <select class="form-control-custom select2-flag"
                                                        name="office_country_id">
                                                        <option value="">Select country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                data-flag="{{ $country->flag_url }}" {{ old('office_country_id') == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Pillar 3: Billing details -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Billing details</div>

                                                <div class="address-sub-grid" style="grid-template-columns: 1fr 1fr;">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Invoicing currency</label>
                                                        <select class="form-control-custom select2-simple"
                                                            name="invoicing_currency">
                                                            <option value="">Select currency</option>
                                                            @foreach($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                                <option value="{{ $curr }}" {{ old('invoicing_currency') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Reporting currency</label>
                                                        <select class="form-control-custom select2-simple"
                                                            name="reporting_currency">
                                                            <option value="">Select currency</option>
                                                            @foreach($countries->pluck('currency')->unique()->filter()->sort() as $curr)
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
                                                    <div class="input-with-append">
                                                        <input type="text" class="form-control-custom"
                                                            name="vat_country_specific_name"
                                                            value="{{ old('vat_country_specific_name') }}">
                                                    </div>
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
                                                    <textarea class="form-textarea-custom" name="information_invoice"
                                                        rows="5">{{ old('information_invoice') }}</textarea>
                                                </div>

                                                <div class="checkbox-group">
                                                    <input type="checkbox" class="checkbox-custom" name="use_vat_check" {{ old('use_vat_check') ? 'checked' : '' }}>
                                                    <label class="checkbox-label">Use VAT check when creating
                                                        invoice</label>
                                                </div>
                                                <div class="checkbox-group">
                                                    <input type="checkbox" class="checkbox-custom" name="show_imo" {{ old('show_imo') ? 'checked' : '' }}>
                                                    <label class="checkbox-label">Show IMO numbers on invoices</label>
                                                </div>
                                                <div class="checkbox-group">
                                                    <input type="checkbox" class="checkbox-custom" name="enable_reader" {{ old('enable_reader') ? 'checked' : '' }}>
                                                    <label class="checkbox-label">Enable Incoming invoice reader</label>
                                                </div>
                                            </div>

                                            <!-- Pillar 4: Accounts -->
                                            <div class="form-pillar">
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: start; margin-top: -10px; padding:5px">
                                                    <div class="form-section-header"
                                                        style="border: none; margin-bottom: 0;">Accounts</div>
                                                    <button type="button" class="btn-add-account">Add account</button>
                                                </div>
                                                <div style="border-bottom: 1px solid #e5e7eb;margin-top: -14px;"></div>
                                                <div id="accounts-container"></div>
                                            </div>
                                        </div>
                                        <div class="edit-footer">
                                            <button type="submit" class="btn-save-custom">Save</button>
                                            <a href="{{ route('offices.index') }}" class="btn-cancel-custom">Cancel</a>
                                        </div>
                                    </form>
                                    <!-- Base Style - Compact end -->
                                </div>
                            </div>
                        </div>
                        <div id="styleSelector">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script
        src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{--
    <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Select2 Flag Formatter
            function formatFlag(state) {
                if (!state.id) return state.text;
                var flagUrl = $(state.element).data('flag');
                if (!flagUrl) return state.text;
                return $(`<span><img src="${flagUrl}" class="img-flag" style="width: 20px; height: 15px; margin-right: 8px; vertical-align: middle;" /> ${state.text}</span>`);
            }

            // Initialize Static Select2
            $('.select2-flag').select2({
                templateResult: formatFlag,
                templateSelection: formatFlag,
                width: '100%'
            });

            $('.select2-simple').select2({
                width: '100%'
            });

            $('.select2-company').select2({
                width: '100%'
            });

            // Pre-build currency options for dynamic accounts
            const currencies = {!! json_encode($countries->pluck('currency')->unique()->filter()->sort()->values()) !!};
            const currencyOptions = `
                                                                                <option value="">Select currency</option>
                                                                                ${currencies.map(curr => `<option value="${curr}">${curr}</option>`).join('')}
                                                                            `;

            $('.btn-add-account').on('click', function () {
                const accountHtml = `
                                                                                    <div class="account-block">
                                                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                                                                            <label class="form-label-custom" style="margin-bottom: 0;">Bank</label>
                                                                                            <button type="button" class="remove-account-btn">
                                                                                                <i class="feather icon-trash-2" style="font-size: 16px;"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                        <textarea class="form-textarea-custom" name="bank[]" rows="3" style="border: 1px solid #e5e7eb; margin-top: 8px;"></textarea>

                                                                                        <div class="account-row-grid">
                                                                                            <div class="form-group-custom">
                                                                                                <label class="form-label-custom">Currency</label>
                                                                                                <select class="form-control-custom select2-simple-dynamic" name="currency[]">
                                                                                                    ${currencyOptions}
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="form-group-custom">
                                                                                                <label class="form-label-custom">Account number</label>
                                                                                                <input type="text" class="form-control-custom" name="account_number[]">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="account-row-grid" style="margin-top: 10px;">
                                                                                            <div class="form-group-custom">
                                                                                                <label class="form-label-custom">IBAN</label>
                                                                                                <input type="text" class="form-control-custom" name="iban[]">
                                                                                            </div>
                                                                                            <div class="form-group-custom">
                                                                                                <label class="form-label-custom">Swift</label>
                                                                                                <input type="text" class="form-control-custom" name="swift[]">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="checkbox-group" style="margin-top: 15px;">
                                                                                            <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="0">
                                                                                            <input type="checkbox" class="checkbox-custom main-account-checkbox">
                                                                                            <label class="checkbox-label" style="margin-bottom: 0;">Set as main account</label>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                const $newAccount = $(accountHtml);
                $('#accounts-container').append($newAccount);

                // Initialize Select2 on the new dynamic dropdown
                $newAccount.find('.select2-simple-dynamic').select2({
                    width: '100%'
                });
            });

            // Handle "Set as main account" logic - only one can be checked
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
        });
    </script>
@endsection