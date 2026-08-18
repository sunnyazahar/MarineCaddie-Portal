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
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
        }
        .table-other-companies th {
            text-align: left;
            padding: 8px 5px;
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            background: #f8fafd;
        }
        .table-other-companies td {
            padding: 4px 8px;
            font-size: 13px !important;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .table-other-companies tr:hover td {
            background-color: #f9fafb;
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
            height: 28px !important;
            font-size: 11px;
            background-color: transparent !important;
            background: transparent !important;
            border: 1px solid #ced4da !important;
            border-radius: 2px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding: 0 8px !important;
            background-color: transparent !important;
            background: transparent !important;
            color: #333 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
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
        .select2-container--default .select2-selection--multiple {
            padding: 0px !important;
        }

        .vessels-filters-toolbar {
            display: none;
        }

        .vessels-filters-panel {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .vessels-filters-fields {
            display: flex;
            align-items: flex-end;
            gap: 15px;
            flex-grow: 1;
            flex-wrap: wrap;
        }

        #vessels-table {
            min-width: 720px !important;
            width: 100% !important;
        }

        #vessels-table th,
        #vessels-table td {
            font-size: 13px !important;
            line-height: 1.35;
            white-space: nowrap;
        }

        /* Hide DataTables scrollX cloned header */
        #vessels-table_wrapper .dataTables_scrollBody > table > thead,
        #vessels-table_wrapper .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        #vessels-table_wrapper .dataTables_scrollBody thead tr,
        #vessels-table_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
            background: transparent !important;
        }
        #vessels-table_wrapper .dataTables_scrollBody thead th:before,
        #vessels-table_wrapper .dataTables_scrollBody thead th:after {
            display: none !important;
            content: none !important;
        }

        @media (max-width: 991.98px) {
            .vessels-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 0 0 10px;
            }

            .vessels-filters-panel {
                display: none !important;
                flex-direction: column;
                justify-content: flex-start !important;
                align-items: stretch !important;
                gap: 8px;
                max-height: 42vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 12px;
                padding: 0 2px 8px;
                -webkit-text-size-adjust: 100%;
                text-size-adjust: 100%;
            }

            body.vessels-filters-open .vessels-filters-panel {
                display: flex !important;
            }

            #btn-vessels-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }

            .vessels-filters-fields {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
                width: 100%;
                flex-grow: 0 !important;
            }

            .vessels-filters-fields > div {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                flex: 0 0 auto !important;
            }

            .vessels-filters-fields input.filter-input {
                width: 100% !important;
                margin: 0 !important;
            }

            .vessels-filters-fields select.filter-input,
            .vessels-filters-fields select.select2,
            .vessels-filters-panel .select2-hidden-accessible {
                position: absolute !important;
                width: 1px !important;
                height: 1px !important;
                padding: 0 !important;
                margin: -1px !important;
                overflow: hidden !important;
                clip: rect(0, 0, 0, 0) !important;
                border: 0 !important;
            }

            .vessels-filters-panel .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }

            .vessels-filters-panel .select2-container .select2-selection--single {
                height: 28px !important;
                min-height: 28px !important;
            }

            .clear-filters {
                margin-top: 0;
            }

            .card-block {
                padding: 12px !important;
            }

            #vessels-table th,
            #vessels-table td {
                font-size: 11px !important;
                padding: 8px 10px !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                padding-top: 8px;
                font-size: 11px !important;
            }

            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
            }

            .dt-responsive,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (min-width: 992px) {
            .vessels-filters-toolbar {
                display: none !important;
            }
            .vessels-filters-panel {
                display: flex !important;
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
                                        <div class="card" style="border-radius: 0; box-shadow: none; border: 1px solid #eef2f7;">
                                            <div class="card-block" style="padding: 15px;">
                                                <div class="vessels-filters-toolbar">
                                                    <button type="button" id="btn-vessels-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="vessels-filters-toggle-label">Show filters</span>
                                                    </button>
                                                </div>
                                                <div class="vessels-filters-panel">
                                                    <div class="vessels-filters-fields">
                                                        <div style="width: 200px;">
                                                            <span class="filter-label" style="font-size: 10px; font-weight: 600;">Vessel name</span>
                                                            <input type="text" id="vesselNameFilter" class="form-control filter-input" placeholder="type here" style="height: 28px; font-size: 11px;">
                                                        </div>
                                                        <div style="width: 120px;">
                                                            <span class="filter-label" style="font-size: 10px; font-weight: 600;">IMO</span>
                                                            <input type="text" id="imoFilter" class="form-control filter-input" placeholder="type here" style="height: 28px; font-size: 11px;">
                                                        </div>
                                                        <div style="width: 150px;">
                                                            <span class="filter-label" style="font-size: 10px; font-weight: 600;">Type</span>
                                                            <select id="typeFilter" class="form-control filter-input select2" style="height: 28px; font-size: 11px;">
                                                                <option value="">Click here</option>
                                                                @foreach($vesselTypes as $type)
                                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div style="padding-bottom: 5px;">
                                                            <span class="clear-filters" style="font-size: 11px; color: #3b82f6; cursor: pointer;">Clear filters</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="dt-responsive">
                                                    <table id="vessels-table" class="table-other-companies">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 25%;">Vessel name</th>
                                                                <th style="width: 15%;">IMO</th>
                                                                <th style="width: 15%;">Type</th>
                                                                <th style="width: 45%;">Connected customers</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @include('Vessels.partials.rows')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="vessels-pagination" class="mt-3 px-3 pb-2">
                                                    {{ $vessels->links() }}
                                                </div>
                                            </div>
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
    @include('partials.searchable-filter-multiselect-script')

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: '100%'
            });

            function fixVesselsFilterSelect2Width() {
                $('.vessels-filters-panel .select2-container').css('width', '100%');
            }

            var table = $('#vessels-table').DataTable({
                "dom": 'rt',
                "paging": false,
                "info": false,
                "lengthChange": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true
            });

            $('#btn-vessels-filters-toggle').on('click', function () {
                $('body').toggleClass('vessels-filters-open');
                var isOpen = $('body').hasClass('vessels-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.vessels-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                setTimeout(function () {
                    fixVesselsFilterSelect2Width();
                    table.columns.adjust();
                }, 50);
            });

            $(window).on('resize', function () {
                fixVesselsFilterSelect2Width();
                table.columns.adjust();
            });

            setTimeout(function () {
                fixVesselsFilterSelect2Width();
                table.columns.adjust();
            }, 100);

            window.vesselsListFilters = bindAjaxListFilters({
                tableSelector: '#vessels-table',
                paginationSelector: '#vessels-pagination',
                indexUrl: @json(route('vessels.index')),
                existingTable: table,
                getParams: function (page) {
                    return {
                        name: $.trim($('#vesselNameFilter').val() || ''),
                        imo: $.trim($('#imoFilter').val() || ''),
                        type: $.trim($('#typeFilter').val() || ''),
                        page: page || 1
                    };
                },
                textSelectors: '#vesselNameFilter, #imoFilter',
                changeSelectors: '#typeFilter',
                resetFields: function () {
                    $('#vesselNameFilter, #imoFilter').val('');
                    $('#typeFilter').val(null).trigger('change');
                },
                afterDraw: function () {
                    table.columns.adjust();
                }
            });
        });
    </script>
@endsection
