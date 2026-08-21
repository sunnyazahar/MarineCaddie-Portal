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
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.25 !important;
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

        /* Custom Form Styling (Same as Agents/edit) */
        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 25px;
            background: #fff;
        }
        .form-group-custom {
            margin-bottom: 15px;
            max-width: 400px;
        }
        .form-label-custom {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }
        .form-input-custom {
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 2px 8px;
            font-size: 11px;
            color: #333;
            background: #fff;
        }
        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 5px 8px;
            font-size: 11px;
            color: #333;
            resize: none;
            background: #fff;
            min-height: 80px;
        }
        .input-group-custom {
            display: flex;
            align-items: center;
        }
        .btn-input-append {
            height: 30px;
            background: #e9ecef;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 8px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 12px;
        }
        .form-input-custom.has-append {
            border-radius: 2px 0 0 2px;
        }

        /* Footer Styling */
        .form-footer {
            padding: 15px 25px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 10px;
        }
        .btn-saved-custom {
            background: #e9ecef;
            color: #a0aec0;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            font-size: 11px;
            cursor: default;
        }
        .btn-cancel-custom {
            color: #01a9ac;
            text-decoration: none;
            font-size: 11px;
        }

        /* Metadata Footer */
        .metadata-footer {
            padding: 5px 25px 15px;
            background: #fdfdfd;
            text-align: right;
            font-size: 10px;
            color: #aaa;
            line-height: 1.4;
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
                                        <div class="card" style="margin-bottom: 0;">
                                            <div class="form-pillar-container">
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Name</label>
                                                    <div class="input-group-custom">
                                                        <input type="text" class="form-input-custom has-append" value="Operations">
                                                        <button class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Email</label>
                                                    <input type="email" class="form-input-custom" value="alessio@gedtransport.com">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Phone number (with country code)</label>
                                                    <input type="text" class="form-input-custom" value="+39 348 222 9099">
                                                </div>

                                                <div class="form-group-custom" style="max-width: 440px;">
                                                    <label class="form-label-custom">Description</label>
                                                    <textarea class="form-textarea-custom">MV SHANDONG FU DE
c/o GED Transport srl
c/o SERVICE GROUP SRL C/O BOSSI SPED
VIA DELL'AZOTO 6</textarea>
                                                </div>

                                                <div class="form-group-custom" style="display: flex; align-items: center; gap: 8px;">
                                                    <input type="checkbox" id="is_main_contact">
                                                    <label for="is_main_contact" class="form-label-custom" style="margin-bottom: 0;">Is main contact</label>
                                                </div>
                                            </div>

                                            <div class="form-footer">
                                                <button class="btn-saved-custom">All changes saved</button>
                                                <a href="{{ route('agents.edit', ['id' => 1]) }}" class="btn-cancel-custom">Cancel</a>
                                            </div>

                                            <div class="metadata-footer"></div>
                                        </div>
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


    <script>
        $(document).ready(function() {
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
