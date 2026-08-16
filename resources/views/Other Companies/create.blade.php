@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        /* Form Layout Styling */
        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            padding: 20px;
            background: #fff;
        }
        .form-pillar {
            display: flex;
            flex-direction: column;
            /* gap: 20px; */
        }
        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }
        .form-group-custom {
            margin-bottom: 15px;
        }
        .form-label-custom {
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 4px;
            display: block;
        }
        .form-input-custom {
            height: 32px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 5px 10px;
            font-size: 12px;
            color: #333;
        }
        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 8px 10px;
            font-size: 12px;
            color: #333;
            resize: none;
        }
        .input-row {
            display: flex;
            gap: 15px;
        }
        .input-row > div {
            flex: 1;
        }
        .input-group-custom {
            display: flex;
        }
        .btn-input-append {
            height: 32px;
            background: #e9ecef;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 10px;
            border-radius: 0 3px 3px 0;
            cursor: pointer;
            color: #666;
            display: flex;
            align-items: center;
        }
        .form-input-custom.has-append {
            border-radius: 3px 0 0 3px;
        }

        /* Footer Styling */
        .form-footer {
            padding: 20px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }
        .btn-save-custom {
            background: #1b5e6f;
            color: #fff;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-cancel-custom {
            color: #01a9ac;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-save-custom:hover {
            background: #144653;
        }

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 10px !important;
        }
        .main-body .page-wrapper {
            padding: 10px !important;
        }

        /* Select2 Background Removal - High Specificity */
        body .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            background: #fff !important;
            border: 1px solid #d1d5db !important;
            height: 32px !important;
            border-radius: 3px !important;
            box-sizing: border-box !important;
        }
        body .select2-container--default.select2-container--focus .select2-selection--single,
        body .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1b5e6f !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            color: #333 !important;
            font-size: 12px !important;
            padding-left: 10px !important;
            background-color: transparent !important;
            background: transparent !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #999 !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
            top: 1px !important;
            right: 6px !important;
        }
        body .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7280 transparent transparent transparent !important;
        }
        body .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #6b7280 transparent !important;
        }
        .select2-dropdown {
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
        }
        .img-flag {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            vertical-align: middle;
        }

        @media (max-width: 1199.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px;
            }
        }

        @media (max-width: 991.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-pillar {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .input-row {
                flex-direction: column;
                gap: 12px;
            }

            .input-row .form-group-custom {
                flex: 0 0 auto !important;
                width: 100% !important;
            }

            .form-group-custom input[style*="width: 50%"],
            .form-input-custom[style*="width: 50%"] {
                width: 100% !important;
            }

            .form-footer {
                left: 0 !important;
                right: 0 !important;
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .btn-save-custom,
            .btn-cancel-custom {
                width: 100%;
                text-align: center;
            }

            .footer-metadata {
                margin-left: 0 !important;
                width: 100%;
                text-align: left !important;
            }

            .form-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .card {
                margin-bottom: 24px;
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
    @include('layouts.partials.pcoded-shell-start')
                                        <div class="card">
                                            <form action="{{ route('other-companies.store') }}" method="POST" id="companyCreateForm">
                                                @csrf
                                                <div class="form-pillar-container">
                                                    <!-- Column 1: Company information -->
                                                    <div class="form-pillar">
                                                        <div class="form-section-header">Company information</div>
                                                        
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Company name</label>
                                                            <input type="text" name="company_name" class="form-input-custom" value="{{ old('company_name') }}" required>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Company type</label>
                                                            <select name="company_type" class="form-input-custom select2-company-type">
                                                                <option value=""></option>
                                                                @foreach($companyTypes as $type)
                                                                    <option value="{{ $type }}" {{ old('company_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="input-row">
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Code</label>
                                                                <input type="text" name="code" class="form-input-custom" value="{{ old('code') }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Code description</label>
                                                                <input type="text" name="code_description" class="form-input-custom" value="{{ old('code_description') }}">
                                                            </div>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Phone number (with country code)</label>
                                                            <input type="text" name="phone_number" class="form-input-custom" value="{{ old('phone_number') }}">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Email</label>
                                                            <input type="email" name="email" class="form-input-custom" value="{{ old('email') }}">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Remarks</label>
                                                            <textarea name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Special considerations for destination</label>
                                                            <textarea name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                                                            <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person') }}" required>
                                                        </div>
                                                    </div>

                                                    <!-- Column 2: Company address -->
                                                    <div class="form-pillar">
                                                        <div class="form-section-header">Company address</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Street address</label>
                                                            <textarea name="street_address" class="form-textarea-custom" rows="3">{{ old('street_address') }}</textarea>
                                                        </div>

                                                        <div class="input-row">
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">City</label>
                                                                <input type="text" name="city" class="form-input-custom" value="{{ old('city') }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">District/state</label>
                                                                <input type="text" name="district_state" class="form-input-custom" value="{{ old('district_state') }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Zip code</label>
                                                                <input type="text" name="zip_code" class="form-input-custom" value="{{ old('zip_code') }}">
                                                            </div>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Country</label>
                                                            <select name="country_id" class="form-input-custom select2-country">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Port code</label>
                                                            <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                                <option value=""></option>
                                                                @if (old('port_code'))
                                                                    <option value="{{ old('port_code') }}" selected>{{ old('port_code') }}</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="form-section-header" style="margin-top: 20px;">Office address (Optional)</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Street address / post box</label>
                                                            <textarea name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address') }}</textarea>
                                                        </div>

                                                        <div class="input-row">
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">City</label>
                                                                <input type="text" name="office_city" class="form-input-custom" value="{{ old('office_city') }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">District/state</label>
                                                                <input type="text" name="office_district_state" class="form-input-custom" value="{{ old('office_district_state') }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Zip code</label>
                                                                <input type="text" name="office_zip_code" class="form-input-custom" value="{{ old('office_zip_code') }}">
                                                            </div>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Country</label>
                                                            <select name="office_country_id" class="form-input-custom select2-country">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}" {{ old('office_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Column 3: Company details -->
                                                    <div class="form-pillar">
                                                        <div class="form-section-header">Company details</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">VAT number</label>
                                                            <input type="text" name="vat_number" class="form-input-custom" value="{{ old('vat_number') }}">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">EORI number</label>
                                                            <input type="text" name="eori_number" class="form-input-custom" value="{{ old('eori_number') }}">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Currency</label>
                                                            <select name="currency" class="form-input-custom select2-currency">
                                                                <option value=""></option>
                                                                @foreach($currencies as $curr)
                                                                    <option value="{{ $curr }}" {{ old('currency') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">UN/LOCODE</label>
                                                            <input type="text" name="un_locode" class="form-input-custom" style="width: 50%;" value="{{ old('un_locode') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Footer Buttons -->
                                                <div class="form-footer">
                                                    <button type="submit" class="btn-save-custom">Save</button>
                                                    <a href="{{ route('other-companies.index') }}" class="btn-cancel-custom">Cancel</a>
                                                    <div class="footer-metadata" style="margin-left: auto; text-align: right; font-size: 11px; color: #999; line-height: 1.4;"></div>
                                                </div>
                                            </form>
                                        </div>
    @include('layouts.partials.pcoded-shell-end')
     <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

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
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#companyCreateForm').validate({
                rules: {
                    company_name: {
                        required: true
                    },
                    contact_person: {
                        required: true
                    }
                },
                messages: {
                    company_name: {
                        required: "Please enter the company name"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("error");
                },
                unhighlight: function (element) {
                    $(element).removeClass("error");
                }
            });

            $('select.select2-company-type').select2({
                placeholder: 'Select company type',
                allowClear: false,
                width: '100%'
            });

            function formatCountryFlag(state) {
                if (!state.id) {
                    return state.text;
                }
                var flagUrl = $(state.element).data('flag-url');
                if (!flagUrl) {
                    return state.text;
                }
                return $('<span><img src="' + flagUrl + '" class="img-flag" /> ' + state.text + '</span>');
            }

            $('select.select2-country').select2({
                placeholder: 'Select Country',
                allowClear: false,
                width: '100%',
                templateResult: formatCountryFlag,
                templateSelection: formatCountryFlag
            });

            // Port code — searched dynamically from the ports table
            function formatPortResult(port) {
                if (port.loading || !port.id) {
                    return port.text;
                }
                var title = port.code + (port.city ? ', ' + port.city : '');
                var $option = $(
                    '<div class="port-option">' +
                        '<div style="font-weight: 600; font-size: 13px; color: #111827;"></div>' +
                        '<div style="font-size: 12px; color: #6b7280;"></div>' +
                    '</div>'
                );
                $option.find('div').eq(0).text(title);
                $option.find('div').eq(1).text(port.country || '');
                return $option;
            }

            function formatPortSelection(port) {
                if (!port.id) return port.text;
                return port.code ? (port.code + (port.city ? ', ' + port.city : '')) : port.text;
            }

            $('.select2-port-code').select2({
                placeholder: 'Search port code',
                allowClear: false,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('api.ports') }}',
                    dataType: 'json',
                    delay: 200,
                    data: function (params) {
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        return { results: data.results || [] };
                    },
                    cache: true
                },
                templateResult: formatPortResult,
                templateSelection: formatPortSelection
            });

            $('select.select2-currency').select2({
                placeholder: 'Select Currency',
                allowClear: false,
                width: '100%'
            });

             // Initialize Select2 for standard filters
            $('select.select2').select2({
                placeholder: "Click here",
                allowClear: false
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonWidth: '100%',
                maxHeight: 200,
                nonSelectedText: '',
                allSelectedText: '',
                nSelectedText: '',
                numberDisplayed: 0,
                buttonClass: 'btn btn-outline-teal btn-filter-toggle',
                templates: {
                    button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><i class="ti-filter"></i></button>'
                },
                onChange: function(option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function() {
                    toggleFilterVisibility();
                },
                onDeselectAll: function() {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Office Name', id: 'col-Office-Name'},
                    {val: 'Short Name', id: 'col-Short-Name'},
                    {val: 'City', id: 'col-City'},
                    {val: 'Country', id: 'col-Country'},
                    {val: 'Phone', id: 'col-Phone'},
                    {val: 'Email', id: 'col-Email'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            // Initial call to set visibility state
            toggleFilterVisibility();

            $('#other-companies-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });
        });
    </script>
@endsection
