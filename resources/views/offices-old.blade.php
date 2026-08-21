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
                                            <div class="card-block">
                                                <div class="d-flex justify-content-between align-items-start pt-2">
                                                    <div style="width: 80%;">
                                                        <div class="row custom-row">
                                                            <div class="custom-col mb-1" style="flex: 0 0 5%; max-width: 5%;">
                                                                <div style="margin-top: 18px;">
                                                                    <select id="filter-multiselect" multiple="multiple">
                                                                        <option value="Office Name">Office Name</option>
                                                                        <option value="Short Name">Short Name</option>
                                                                        <option value="City">City</option>
                                                                        <option value="Country">Country</option>
                                                                        <option value="Phone">Phone</option>
                                                                        <option value="Email">Email</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div id="col-Office-Name" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">Office Name</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        <option>Click here</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Short-Name" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">Short Name</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-City" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">City</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        <option>Click here</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Country" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">Country</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        <option>Click here</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Phone" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">Phone</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-Email" class="custom-col mb-2">
                                                                <div class="form-group mb-0">
                                                                    <span class="filter-label">Email</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div class="custom-col mb-2">
                                                                <a class="clear-filters"><i class="ti-close"></i> Clear filters</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="text-right" style="width: 20%; padding-top: 18px;">
                                                     <button class="btn btn-outline-teal"><i class="ti-download"></i> Export</button>
                                                     <a class="btn btn-teal ml-2" href="{{ route('offices.create') }}">Create Office</a>
                                                </div>
                                                </div>
                                                <div class="dt-responsive">
                                                    <table id="offices-table"
                                                        class="table compact table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Office Name</th>
                                                                <th>Short Name</th>
                                                                <th>City</th>
                                                                <th>Country</th>
                                                                <th>Phone</th>
                                                                <th>Email</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Best Global Logistic Norway AS</td>
                                                                <td>MSS</td>
                                                                <td>Moss</td>
                                                                <td>Norway</td>
                                                                <td>+47 69 24 11 30</td>
                                                                <td>info@bestgloballogistics.no</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Global Transport Solutions</td>
                                                                <td>GTS</td>
                                                                <td>Rozenberg</td>
                                                                <td>Belgium</td>
                                                                <td>+32 9 216 70 70</td>
                                                                <td>contact@gts-belgium.be</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Marintrans AS</td>
                                                                <td>OSL</td>
                                                                <td>Oslo</td>
                                                                <td>Norway</td>
                                                                <td>+47 22 41 80 50</td>
                                                                <td>info@marintrans.no</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>  
                                                            </tr>
                                                            <tr>
                                                                <td>Nordic Freight & Logistics</td>
                                                                <td>NFL</td>
                                                                <td>Copenhagen</td>
                                                                <td>Denmark</td>
                                                                <td>+45 32 46 00 00</td>
                                                                <td>support@nordicfreight.dk</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Schenker AB</td>
                                                                <td>SCH</td>
                                                                <td>Gothenburg</td>
                                                                <td>Sweden</td>
                                                                <td>+46 31 703 80 00</td>
                                                                <td>info.se@dbschenker.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Kuehne + Nagel Ltd</td>
                                                                <td>KN</td>
                                                                <td>Helsinki</td>
                                                                <td>Finland</td>
                                                                <td>+358 20 1611</td>
                                                                <td>info.helsinki@kuehne-nagel.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>DSV Air & Sea</td>
                                                                <td>DSV</td>
                                                                <td>Hedehusene</td>
                                                                <td>Denmark</td>
                                                                <td>+45 43 20 30 40</td>
                                                                <td>info@dsv.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>PostNord Logistics</td>
                                                                <td>PN</td>
                                                                <td>Solna</td>
                                                                <td>Sweden</td>
                                                                <td>+46 771 33 33 10</td>
                                                                <td>kundservice.se@postnord.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Blue Water Shipping</td>
                                                                <td>BWS</td>
                                                                <td>Esbjerg</td>
                                                                <td>Denmark</td>
                                                                <td>+45 79 13 41 44</td>
                                                                <td>bws@bws.dk</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bring Cargo AS</td>
                                                                <td>BRG</td>
                                                                <td>Oslo</td>
                                                                <td>Norway</td>
                                                                <td>+47 04045</td>
                                                                <td>kundeservice@bring.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>ColliCare Logistics</td>
                                                                <td>CLC</td>
                                                                <td>Moss</td>
                                                                <td>Norway</td>
                                                                <td>+47 69 20 95 00</td>
                                                                <td>sales@collicare.no</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Greencarrier Freight Services</td>
                                                                <td>GFS</td>
                                                                <td>Gothenburg</td>
                                                                <td>Sweden</td>
                                                                <td>+46 31 85 79 00</td>
                                                                <td>info@greencarrier.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>DHL Express Norway</td>
                                                                <td>DHL</td>
                                                                <td>Oslo</td>
                                                                <td>Norway</td>
                                                                <td>+47 21 00 22 00</td>
                                                                <td>no.kundeservice@dhl.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Leman International</td>
                                                                <td>LEM</td>
                                                                <td>Greve</td>
                                                                <td>Denmark</td>
                                                                <td>+45 33 43 42 00</td>
                                                                <td>kontakt.dk@leman.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Freja Transport & Logistics</td>
                                                                <td>FTL</td>
                                                                <td>Skive</td>
                                                                <td>Denmark</td>
                                                                <td>+45 96 70 50 00</td>
                                                                <td>info@freja.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Geodis Wilson Sweden AB</td>
                                                                <td>GEO</td>
                                                                <td>Stockholm</td>
                                                                <td>Sweden</td>
                                                                <td>+46 10 456 00 00</td>
                                                                <td>info.se@geodis.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>NTEX AB</td>
                                                                <td>NTX</td>
                                                                <td>Gothenburg</td>
                                                                <td>Sweden</td>
                                                                <td>+46 31 727 85 00</td>
                                                                <td>info@ntex.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Scan Global Logistics</td>
                                                                <td>SGL</td>
                                                                <td>Kastrup</td>
                                                                <td>Denmark</td>
                                                                <td>+45 32 48 00 00</td>
                                                                <td>headoffice@scangl.com</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                                <td>Marintrans AS</td>
                                                                <td>OSL</td>
                                                                <td>Oslo</td>
                                                                <td>Norway</td>
                                                                <td>+47 22 22 22 22</td>
                                                                <td>info@marintrans.no</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>  
                                                            </tr>
                                                            <tr>
                                                                <td>Marintrans AS</td>
                                                                <td>OSL</td>
                                                                <td>Oslo</td>
                                                                <td>Norway</td>
                                                                <td>+47 22 22 22 22</td>
                                                                <td>info@marintrans.no</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary p-1"><i class="ti-pencil-alt"></i></button>
                                                                    <button class="btn btn-danger p-1"><i class="ti-trash"></i></button>
                                                                </td>  
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Office Name</th>
                                                                <th>Short Name</th>
                                                                <th>City</th>
                                                                <th>Country</th>
                                                                <th>Phone</th>
                                                                <th>Email</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
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
