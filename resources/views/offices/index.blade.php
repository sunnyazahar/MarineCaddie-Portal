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

        /* Premium Index Styles */
        .page-title-link {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            background: #eef2ff;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .header-search-bar {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-inner {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1 1 auto;
            min-width: 0;
        }
        .search-text {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            flex-shrink: 0;
        }
        .search-input-custom {
            border: 1px solid #e5e7eb;
            padding: 6px 12px;
            font-size: 13px;
            width: 200px;
            max-width: 100%;
            border-radius: 4px;
            color: #374151;
            min-width: 0;
            flex: 1 1 160px;
        }
        .btn-add-office {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 6px 15px;
            font-size: 13px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-add-office:hover {
            background: #3b82f6;
            color: #fff;
            text-decoration: none;
        }

        .offices-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: #fff;
        }
        .office-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            min-width: 900px;
        }
        .office-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .office-table td {
            padding: 12px 15px;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .office-name-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .office-name-link:hover {
            text-decoration: underline;
        }
        .badge-inactive {
            background: #e5e7eb;
            color: #6b7280;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-active {
            background: #ecfdf5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            border: 1px solid #10b981;
        }
        .flag-icon {
            margin-right: 8px;
            vertical-align: middle;
            width: 18px;
        }
        .action-icon {
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
        }
        .action-icon:hover {
            color: #4b5563;
        }

        /* Kill DataTables responsive "+" control if theme CSS reintroduces it */
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before,
        table.dataTable.dtr-column.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-column.collapsed > tbody > tr > th.dtr-control:before {
            display: none !important;
        }

        @media (max-width: 991.98px) {
            .page-title-link {
                margin-left: 4px;
            }
            .header-search-bar {
                padding: 10px 12px;
                margin-bottom: 12px;
            }
            .search-inner {
                width: 100%;
                gap: 8px;
            }
            .search-input-custom {
                width: auto;
                flex: 1 1 auto;
            }
            .btn-add-office {
                width: 100%;
                text-align: center;
            }
            .offices-table-wrap,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .office-table th,
            .office-table td {
                padding: 10px 12px;
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
                                   <a href="#" class="page-title-link">Offices</a>
                                   
                                   <div class="header-search-bar">
                                       <div class="search-inner">
                                           <span class="search-text">Search</span>
                                           <input type="text" class="search-input-custom" placeholder="Type here">
                                       </div>
                                       <a href="{{ route('offices.create') }}" class="btn-add-office">Add office</a>
                                   </div>

                                   <div class="offices-table-wrap">
                                   <table id="offices-table" class="office-table">
                                       <thead>
                                           <tr>
                                               <th>Office name</th>
                                               <th>Short name</th>
                                               <th>City</th>
                                               <th>Country</th>
                                               <th>Phone number</th>
                                               <th>Email</th>
                                               <th>Status</th>
                                               <th></th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                           @foreach($offices as $office)
                                               <tr>
                                                   <td>
                                                       <a href="{{ route('offices.edit', $office->id) }}" class="office-name-link">
                                                           {{ $office->office_name }}
                                                       </a>
                                                   </td>
                                                   <td>{{ $office->office_short_name }}</td>
                                                   <td>{{ $office->city }}</td>
                                                   <td>
                                                       @if($office->country)
                                                           <img src="{{ $office->country->flag_url }}" class="flag-icon" alt="">
                                                           {{ $office->country->name }}
                                                       @endif
                                                   </td>
                                                   <td>{{ $office->phone_number }}</td>
                                                   <td>
                                                       @if($office->email)
                                                           <a href="mailto:{{ $office->email }}" style="color: #3b82f6; text-decoration: none;">
                                                               {{ $office->email }}
                                                           </a>
                                                       @endif
                                                   </td>
                                                   <td>
                                                       @if($office->status == 1)
                                                           <span class="badge-active">Active</span>
                                                       @else
                                                           <span class="badge-inactive">Inactive</span>
                                                       @endif
                                                   </td>
                                                   <td style="text-align: right;">
                                                       <a href="{{ route('offices.edit', $office->id) }}">
                                                           <i class="ti-pencil action-icon"></i>
                                                       </a>
                                                   </td>
                                               </tr>
                                           @endforeach
                                       </tbody>
                                   </table>
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

    <script>
        $(document).ready(function() {
            var table = $('#offices-table').DataTable({
                "dom": 't',
                "pageLength": 100,
                "ordering": true,
                "autoWidth": false,
                "responsive": false,
                "scrollX": true,
                "searching": true
            });

            // Link custom search input to DataTable
            $('.search-input-custom').on('keyup', function() {
                table.search(this.value).draw();
            });

            $(window).on('resize', function () {
                table.columns.adjust();
            });

            setTimeout(function () {
                table.columns.adjust();
            }, 100);
        });
    </script>
@endsection
