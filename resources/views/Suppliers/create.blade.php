@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <style>
        #offices-table tbody td {
            padding: 4px 5px !important;
            vertical-align: middle !important;
            font-size: 12px;
            white-space: normal !important;
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }
        .filter-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            display: block;
        }
        .filter-input {
            height: 32px;
            font-size: 13px;
            border-radius: 2px;
        }
        .clear-filters {
            font-size: 12px;
            color: #ff5252;
            text-decoration: none;
            cursor: pointer;
            margin-top: 25px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-row {
            margin-right: -10px;
            margin-left: -10px;
        }
        .custom-col {
            padding-right: 10px;
            padding-left: 10px;
            flex: 0 0 11.5%;
            max-width: 11.5%;
        }
        @media (max-width: 992px) {
            .custom-col {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
        }
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
            background-color: transparent !important;
            background: transparent !important;
            border: 1px solid #d1d5db !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.25 !important;
            padding: 0 8px !important;
            background-color: transparent !important;
            background: transparent !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
        }
        .select2-container--open .select2-dropdown--below {
            border-top: 1px solid #aaa;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            z-index: 10001; /* Ensure it stays above fixed footer */
        }
        .flag-icon {
            width: 18px;
            height: 12px;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid #eee;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* 3-Column Form Styling */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            padding: 20px 30px 80px 30px;
            background: #fff;
        }
        .form-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 8px;
            border-bottom: 2px solid #1b5e6f;
            margin-bottom: 10px;
        }
        .form-label-custom {
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 5px;
            display: block;
        }
        .form-input-custom {
            height: 35px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 5px 10px;
            font-size: 13px;
        }
        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            min-height: 80px;
            resize: vertical;
        }
        .input-with-icon {
            position: relative;
        }
        .input-icon-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #eee;
            border: 1px solid #ccc;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 12px;
            color: #666;
            cursor: pointer;
        }
        .sub-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        .optional-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .form-footer-custom {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 185px;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        }
        .btn-save-custom {
            background-color: #1b5e6f;
            color: white;
            border: none;
            padding: 8px 30px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-cancel-custom {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
        }

        @media (max-width: 1199.98px) {
            .form-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px 20px 96px;
            }
        }

        @media (max-width: 991.98px) {
            .form-grid {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px 12px 110px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-column {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .sub-grid-3 {
                grid-template-columns: 1fr !important;
                gap: 12px;
            }

            .form-footer-custom {
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

            .form-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .card {
                margin-bottom: 0;
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
                                        <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST">
                                            @csrf
                                            <div class="card">
                                                <div class="form-grid">
                                                    <!-- Column 1: Supplier information -->
                                                    <div class="form-column">
                                                        <div class="form-section-header">Supplier information</div>
                                                        
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Supplier name</label>
                                                            <div class="input-with-icon">
                                                                <input type="text" name="supplier_name" class="form-input-custom" required>
                                                                <span class="input-icon-btn">...</span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Phone number (with country code)</label>
                                                            <input type="text" name="phone_number" class="form-input-custom">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Email</label>
                                                            <input type="text" name="email" class="form-input-custom"
                                                                placeholder="email@example.com; email2@example.com">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Remarks</label>
                                                            <textarea name="remarks" class="form-textarea-custom"></textarea>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Special considerations for destination</label>
                                                            <textarea name="special_considerations" class="form-textarea-custom"></textarea>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                                                            <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person') }}" required>
                                                        </div>
                                                    </div>

                                                    <!-- Column 2: Supplier address -->
                                                    <div class="form-column">
                                                        <div class="form-section-header">Supplier address</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Supplier address</label>
                                                            <textarea name="supplier_address" class="form-textarea-custom"></textarea>
                                                        </div>

                                                        <div class="sub-grid-3">
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">City</label>
                                                                <input type="text" name="city" class="form-input-custom">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">District/state</label>
                                                                <input type="text" name="district_state" class="form-input-custom">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Zip code</label>
                                                                <input type="text" name="zip_code" class="form-input-custom">
                                                            </div>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Country</label>
                                                            <div class="input-with-icon">
                                                                <x-forms.country-select
                                                                    name="country_id"
                                                                    :countries="$countries"
                                                                    wrapperClass=""
                                                                    class="form-input-custom"
                                                                />
                                                            </div>
                                                        </div>

                                                        <x-forms.port-select
                                                            name="port_code"
                                                            label="Port code"
                                                        />

                                                        <div class="optional-header">Office address (optional)</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Office address</label>
                                                            <textarea name="office_address" class="form-textarea-custom"></textarea>
                                                        </div>

                                                        <div class="sub-grid-3">
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">City</label>
                                                                <input type="text" name="office_city" class="form-input-custom">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">District/state</label>
                                                                <input type="text" name="office_district_state" class="form-input-custom">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Zip code</label>
                                                                <input type="text" name="office_zip_code" class="form-input-custom">
                                                            </div>
                                                        </div>

                                                        <x-forms.country-select
                                                            name="office_country_id"
                                                            label="Country"
                                                            :countries="$countries"
                                                            class="form-input-custom"
                                                        />
                                                    </div>

                                                    <!-- Column 3: Supplier details -->
                                                    <div class="form-column">
                                                        <div class="form-section-header">Supplier details</div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">VAT number</label>
                                                            <input type="text" name="vat_number" class="form-input-custom">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">EORI number</label>
                                                            <input type="text" name="eori_number" class="form-input-custom">
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Currency</label>
                                                            <select name="currency" class="form-input-custom select2">
                                                                <option value="">Select Currency</option>
                                                                @foreach($currencies as $curr)
                                                                    <option value="{{ $curr }}">{{ $curr }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">UN/LOCODE</label>
                                                            <input type="text" name="un_locode" class="form-input-custom">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-footer-custom">
                                                    <button type="submit" class="btn-save-custom">Save</button>
                                                    <a href="{{ route('suppliers.index') }}" class="btn-cancel-custom">Cancel</a>
                                                </div>
                                            </div>
                                        </form>
    @include('layouts.partials.pcoded-shell-end')


    <!-- jquery validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <style>
        .error {
            color: #ff5252;
            font-size: 11px;
            margin-top: 2px;
            width: 100%;
        }
        .form-input-custom.error, .form-textarea-custom.error {
            border-color: #ff5252 !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('.select2').not('[data-country-select]').select2({
                placeholder: "Select an option",
                allowClear: false,
                width: '100%'
            });

            // jQuery Validation
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

            $("#supplierForm").validate({
                rules: {
                    supplier_name: {
                        required: true,
                        minlength: 2
                    },
                    contact_person: {
                        required: true
                    },
                    email: {
                        multiEmail: true
                    }
                },
                messages: {
                    supplier_name: {
                        required: "Please enter the supplier name"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    },
                    email: {
                        multiEmail: "Please enter valid email address(es), separated by comma or semicolon"
                    }
                },
                errorElement: "span",
                errorPlacement: function(error, element) {
                    error.addClass("help-block");
                    if (element.parent(".input-with-icon").length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("error");
                }
            });

            $('#offices-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": false
            });
        });
    </script>
@endsection
