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
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding: 2px 8px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080;
            border: 1px solid #006666;
            color: #fff;
            font-size: 10px;
            margin-top: 2px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 30px;
            border: 1px solid #ced4da;
            border-radius: 2px;
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

        .btn-filter-toggle:hover,
        .btn-filter-toggle:focus,
        .btn-filter-toggle:active {
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

        /* Contact Form Styling */
        .contact-form-container {
            padding: 30px;
            max-width: 600px;
        }

        .form-group-custom {
            margin-bottom: 20px;
        }

        .label-red {
            color: #d9534f;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }

        .label-normal {
            color: #555;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }

        .error-message {
            color: #d9534f;
            font-size: 11px;
            margin-top: 5px;
        }

        .input-custom {
            height: 35px;
            border: 1px solid #eee;
            border-radius: 2px;
            width: 100%;
            padding: 5px 10px;
            font-size: 13px;
        }

        .textarea-custom {
            border: 1px solid #eee;
            border-radius: 2px;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            min-height: 100px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon .input-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            background: #eee;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .checkbox-container input {
            width: 16px;
            height: 16px;
        }

        .checkbox-container label {
            font-size: 13px;
            color: #555;
            margin: 0;
        }

        .form-footer {
            margin-top: 100px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .btn-save-contact {
            background-color: #1b5e6f;
            color: white;
            border: none;
            padding: 8px 30px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-cancel-contact {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
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
                                <div class="card">
                                    <form id="contactForm" method="POST"
                                        action="{{ route('agents.contacts.store', $agent_id) }}">
                                        @csrf
                                        <div class="contact-form-container">
                                            <div class="form-group-custom">
                                                <label class="label-normal">Name</label>
                                                <input type="text" name="name" class="input-custom" required>
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="label-normal">Email</label>
                                                <input type="email" name="email" class="input-custom" required>
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="label-normal">Phone number (with country code)</label>
                                                <div class="input-with-icon">
                                                    <input type="text" name="phone_number" class="input-custom">
                                                    <span class="input-icon"><i class="ti-more-alt"></i></span>
                                                </div>
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="label-normal">Description</label>
                                                <textarea name="description" class="textarea-custom"></textarea>
                                            </div>

                                            <div class="checkbox-container">
                                                <input type="checkbox" name="is_main_contact" id="is_main_contact"
                                                    value="1">
                                                <label for="is_main_contact">Is main contact</label>
                                            </div>

                                            <div class="form-footer">
                                                <button type="submit" class="btn-save-contact">Save</button>
                                                <a href="{{ route('agents.edit', $agent_id) }}#contacts"
                                                    class="btn-cancel-contact">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- Base Style - Compact end -->
                            </div>
                            <!-- Page-body end -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
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
            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true
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
                onChange: function (option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function () {
                    toggleFilterVisibility();
                },
                onDeselectAll: function () {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function () {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    { val: 'Office Name', id: 'col-Office-Name' },
                    { val: 'Short Name', id: 'col-Short-Name' },
                    { val: 'City', id: 'col-City' },
                    { val: 'Country', id: 'col-Country' },
                    { val: 'Phone', id: 'col-Phone' },
                    { val: 'Email', id: 'col-Email' }
                ];

                allFilters.forEach(function (filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }

            // Initial call to set visibility state
            toggleFilterVisibility();

            $('#offices-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": false
            });

            // jQuery Validation for Contact Form
            $('#contactForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter contact name",
                        minlength: "Name must be at least 2 characters"
                    },
                    email: {
                        required: "Please enter contact email",
                        email: "Please enter a valid email address"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group-custom").addClass("has-error");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group-custom").removeClass("has-error");
                }
            });
        });
    </script>
@endsection