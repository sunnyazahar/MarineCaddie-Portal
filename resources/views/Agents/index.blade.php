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
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <style>
        /* Filter Bar Styling */
        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .agents-filters-toolbar {
            display: none;
        }

        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover,
        .btn-outline-teal.is-open {
            background-color: #008080;
            color: #fff;
            border-color: #008080;
        }

        .filter-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .filter-label-custom {
            font-size: 11px;
            color: #666;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .filter-input-custom {
            height: 28px;
            border: 1px solid #e0e0e0;
            border-radius: 2px;
            width: 100%;
            padding: 2px 8px;
            font-size: 11px;
            color: #333;
        }

        .filter-input-custom::placeholder {
            color: #ccc;
            font-style: italic;
        }

        .input-group-custom {
            display: flex;
            align-items: center;
        }

        .btn-input-append {
            height: 28px;
            background: #f3f4f6;
            border: 1px solid #e0e0e0;
            border-left: none;
            padding: 0 8px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 11px;
        }

        .filter-input-custom.has-append {
            border-radius: 2px 0 0 2px;
        }

        /* Select2 filter dropdowns - match text inputs */
        .filter-row .select2-container--default .select2-selection--single {
            height: 28px !important;
            min-height: 28px !important;
            font-size: 11px !important;
            background-color: #fff !important;
            background: #fff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            color: #333 !important;
            background-color: transparent !important;
            background: transparent !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #ccc !important;
            font-style: italic;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            top: 1px !important;
            background: transparent !important;
        }

        .filter-row .select2-container--default .select2-results__option--highlighted,
        .filter-row .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #f3f4f6 !important;
            color: #333 !important;
        }

        .filter-checkbox-group {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: 15px;
            /* Aligned with other items */
            white-space: nowrap;
        }

        .filter-checkbox-group label {
            font-size: 11px;
            color: #666;
            margin-bottom: 0;
            cursor: pointer;
            order: 2;
            /* Label after checkbox */
        }

        .filter-checkbox-group input[type="checkbox"] {
            width: 14px;
            height: 14px;
            cursor: pointer;
            accent-color: #1b5e6f;
            order: 1;
            /* Checkbox first */
        }

        .btn-clear-filters {
            font-size: 11px;
            color: #01a9ac;
            text-decoration: none;
            white-space: nowrap;
            margin-left: 15px;
            /* Aligned with other items */
        }

        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: visible;
            width: 100%;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding: 0 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            align-items: center;
            background: #f8fafc;
            min-width: fit-content;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 11px;
            padding: 0 10px !important;
            background: transparent !important;
            width: 100%;
            color: #1e293b;
        }
        .filter-group .multiselect-native-select {
            flex: 1;
            min-width: 0;
        }
        .filter-group .multiselect-native-select .btn-group {
            width: 100%;
        }
        .filter-group .multiselect-native-select .multiselect {
            height: 30px;
            padding: 4px 26px 4px 10px;
            overflow: hidden;
            border: 0;
            border-radius: 0;
            background: #fff;
            color: #1e293b;
            font-size: 11px;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .filter-group .multiselect-native-select .multiselect-container {
            width: max(100%, 280px);
            max-height: 420px;
            overflow-y: auto;
            padding: 6px 0;
            z-index: 1050;
        }
        .filter-group .multiselect-native-select .multiselect-container .input-group {
            width: calc(100% - 12px);
            margin: 0 6px 6px;
        }
        .filter-group .multiselect-native-select .multiselect-container label {
            padding-top: 7px;
            padding-bottom: 7px;
            color: #263238;
            font-size: 12px;
            white-space: normal;
        }
        .filter-group .multiselect-native-select .multiselect-container input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #176b87;
        }
        .filter-group .multiselect-native-select .multiselect-container .multiselect-reset a {
            color: #176b87;
            font-weight: 600;
        }

        .btn-add-agent {
            height: 28px;
            padding: 0 12px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            border-radius: 3px;
            font-size: 11px;
            display: none;
            /* Hidden as per screenshot preference for clean filter bar */
        }

        /* Table Styling */
        .table-agents {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .table-agents th {
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            background: #f8fafd;
            /* Consistent light blue-grey header */
        }

        .table-agents td {
            padding: 6px 10px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            vertical-align: middle;
            background: #fff;
        }

        .table-agents tr:hover td {
            background-color: #f5f7f9 !important;
        }

        .table-agents tr.selected td {
            background-color: #e2e8f0 !important;
        }

        .agent-link {
            color: #016699;
            text-decoration: none;
        }

        .agent-link:hover {
            text-decoration: underline;
        }

        .agent-status-toggle {
            border: 1px solid transparent;
            padding: 3px 10px;
            border-radius: 12px;
            min-width: 66px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            text-align: center;
            cursor: pointer;
        }

        .agent-status-toggle.is-active {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }

        .agent-status-toggle.is-inactive {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .agent-status-toggle:hover {
            filter: brightness(0.97);
        }

        .action-icons {
            display: flex;
            gap: 8px;
            color: #ccc;
            justify-content: flex-end;
        }

        .action-icons i {
            cursor: pointer;
            font-size: 14px;
        }

        .action-icons i:hover {
            color: #666;
        }

        .company-link {
            color: #016699;
            text-decoration: none;
        }

        .company-link:hover {
            text-decoration: underline;
        }

        .country-flag {
            width: 18px;
            margin-right: 6px;
            vertical-align: text-top;
        }

        .action-icons {
            display: flex;
            gap: 10px;
            color: #ccc;
            justify-content: center;
        }

        .action-icons i {
            cursor: pointer;
            font-size: 14px;
        }

        .action-icons i:hover {
            color: #666;
        }

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 5px !important;
            background: #f6f7fb;
            /* Page background color */
        }

        .main-body .page-wrapper {
            padding: 5px !important;
            background: #f6f7fb;
        }

        .table-responsive {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-agents {
            min-width: 900px;
        }

        .agents-add-desktop {
            font-size: 11px;
            padding: 6px 15px;
            border-radius: 2px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            font-weight: 600;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .agents-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 8px 12px;
                background: #fff;
                border-bottom: 1px solid #eee;
            }

            .agents-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .filter-row {
                display: none !important;
                flex-direction: column;
                align-items: stretch;
                flex-wrap: nowrap;
                gap: 10px;
                max-height: 42vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding: 10px 12px 12px;
            }

            body.agents-filters-open .filter-row {
                display: flex !important;
            }

            #btn-agents-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }

            .filter-item,
            .filter-item[style*="margin-left"] {
                flex-direction: column !important;
                align-items: stretch !important;
                margin-left: 0 !important;
                width: 100%;
                gap: 4px;
            }

            .filter-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .filter-input-custom,
            .filter-input-custom[style*="width"],
            .filter-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .input-group-custom {
                width: 100%;
            }

            .input-group-custom .filter-input-custom {
                flex: 1;
            }

            .filter-checkbox-group,
            .btn-clear-filters {
                margin-left: 0 !important;
            }

            .agents-add-desktop {
                display: none !important;
            }

            .dataTables_wrapper .dataTables_filter {
                display: none !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                padding-top: 8px;
            }

            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
            }

            .table-agents th:first-child,
            .table-agents td:first-child {
                padding-left: 12px;
            }
        }

        @media (min-width: 992px) {
            .agents-filters-toolbar {
                display: none !important;
            }
            .filter-row {
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
                                <div class="agents-filters-toolbar">
                                    <button type="button" id="btn-agents-filters-toggle" class="btn btn-outline-teal btn-sm">
                                        <i class="ti-filter"></i> <span class="agents-filters-toggle-label">Show filters</span>
                                    </button>
                                    <div class="agents-filters-toolbar-actions">
                                        @if($canWriteAdministration)
                                        <a class="btn btn-sm agents-add-mobile" href="{{ route('agents.create') }}"
                                            style="font-size: 11px; padding: 6px 12px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                            Add Agent
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <!-- Filter Bar -->
                                <div class="filter-row">
                                    <div class="filter-item" style="width: 180px;">
                                        <div class="filter-group">
                                            <span class="filter-label">Name</span>
                                            <input type="text" id="filter-agent-name" class="form-control filter-input" placeholder="type here">
                                        </div>
                                    </div>
                                    <div class="filter-item" style="width: 150px;">
                                        <div class="filter-group">
                                            <span class="filter-label">Code</span>
                                            <input type="text" id="filter-agent-code" class="form-control filter-input" placeholder="type here">
                                        </div>
                                    </div>
                                    <div class="filter-item" style="width: 200px;">
                                        <div class="filter-group">
                                            <span class="filter-label">Address</span>
                                            <input type="text" id="filter-agent-address" class="form-control filter-input" placeholder="type here">
                                        </div>
                                    </div>
                                    <div class="filter-item" style="width: 180px;">
                                        <div class="filter-group">
                                            <span class="filter-label">City</span>
                                            <input type="text" id="filter-agent-city" class="form-control filter-input" placeholder="type here">
                                        </div>
                                    </div>
                                    <div class="filter-item" style="width: 200px;">
                                        <div class="filter-group">
                                            <span class="filter-label">Country</span>
                                            <select id="filter-agent-country" class="form-control filter-input agent-filter-multiselect" multiple="multiple">
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="filter-item" style="width: 180px;">
                                        <div class="filter-group">
                                            <span class="filter-label">Type</span>
                                            <select id="filter-agent-type" class="form-control filter-input agent-filter-multiselect" multiple="multiple">
                                                @foreach ($agentTypes as $type)
                                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="filter-checkbox-group">
                                        <input type="checkbox" id="hide-inactive-check" checked>
                                        <label for="hide-inactive-check">Hide inactive</label>
                                    </div>
                                    <a href="#" id="clear-agent-filters" class="btn-clear-filters">Clear filters</a>
                                    @if($canWriteAdministration)
                                    <a class="agents-add-desktop" href="{{ route('agents.create') }}">
                                        Add Agent
                                    </a>
                                    @endif
                                </div>

                                {{-- thead template used by JS to rebuild table after destroy(true) --}}
                                <template id="agents-table-thead-template">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Agent name</th>
                                            <th style="width: 6%;">Code</th>
                                            <th style="width: 9%;">City</th>
                                            <th style="width: 10%;">Country</th>
                                            <th style="width: 10%;">Phone number</th>
                                            <th style="width: 15%;">Email</th>
                                            <th style="width: 12%;">Type</th>
                                            <th style="width: 5%;">Status</th>
                                            <th style="width: 3%;"></th>
                                        </tr>
                                    </thead>
                                </template>

                                <!-- Data Table -->
                                <div id="agents-table-wrapper" class="table-responsive" style="padding: 0;">
                                    <table id="agents-table" class="table-agents">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Agent name</th>
                                                <th style="width: 6%;">Code</th>
                                                <th style="width: 9%;">City</th>
                                                <th style="width: 10%;">Country</th>
                                                <th style="width: 10%;">Phone number</th>
                                                <th style="width: 15%;">Email</th>
                                                <th style="width: 12%;">Type</th>
                                                <th style="width: 5%;">Status</th>
                                                <th style="width: 3%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('Agents.partials.rows')
                                        </tbody>
                                    </table>
                                </div>
                                <div id="agents-pagination" class="mt-3 px-3 pb-2">
                                    {{ $agents->links() }}
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
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function () {
            var agentsIndexUrl = @json(route('agents.index'));
            var table = null;
            var searchTimer = null;
            var filtersReady = false;
            var requestToken = 0;
            var suppressFilterLoad = false;

            function shouldLoadFilters() {
                return filtersReady && !suppressFilterLoad;
            }

            $('.agent-filter-multiselect').multiselect({
                enableCaseInsensitiveFiltering: true,
                includeResetOption: true,
                resetText: 'Clear',
                filterPlaceholder: 'Type here',
                maxHeight: 420,
                buttonWidth: '100%',
                nonSelectedText: 'Click here',
                numberDisplayed: 1,
                nSelectedText: 'selected',
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Click here';
                    }

                    var firstSelection = $(options[0]).text();
                    return options.length === 1 ? firstSelection : firstSelection + ', ...';
                },
                buttonTitle: function (options) {
                    var labels = [];
                    options.each(function () {
                        labels.push($(this).text());
                    });

                    return labels.join(', ');
                },
                onChange: function () {
                    if (shouldLoadFilters()) {
                        loadAgents(1);
                    }
                },
                onSelectAll: function () {
                    if (shouldLoadFilters()) {
                        loadAgents(1);
                    }
                },
                onDeselectAll: function () {
                    if (shouldLoadFilters()) {
                        loadAgents(1);
                    }
                }
            });

            var dtConfig = {
                "dom": 'rt',
                "lengthChange": false,
                "paging": false,
                "info": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "orderable": false, "targets": [8] }
                ],
                "language": {
                    "emptyTable": "No agents found."
                }
            };

            table = $('#agents-table').DataTable(dtConfig);

            function currentFilterParams(page) {
                return {
                    name: $.trim($('#filter-agent-name').val() || ''),
                    code: $.trim($('#filter-agent-code').val() || ''),
                    address: $.trim($('#filter-agent-address').val() || ''),
                    city: $.trim($('#filter-agent-city').val() || ''),
                    country: $('#filter-agent-country').val() || [],
                    type: $('#filter-agent-type').val() || [],
                    hide_inactive: $('#hide-inactive-check').is(':checked') ? 1 : 0,
                    page: page || 1
                };
            }

            function replaceAgentRows(html, paginationHtml) {
                table.destroy(true);
                $('#agents-table-wrapper').html(
                    '<table id="agents-table" class="table-agents"></table>'
                );
                $('#agents-table').html(
                    $('#agents-table-thead-template').html() +
                    '<tbody>' + html + '</tbody>'
                );
                table = $('#agents-table').DataTable(dtConfig);
                table.columns.adjust();
                $('#agents-pagination').html(paginationHtml || '');
            }

            function loadAgents(page) {
                var params = currentFilterParams(page);
                var token = ++requestToken;

                $.ajax({
                    url: agentsIndexUrl,
                    method: 'GET',
                    data: params,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).done(function (response) {
                    if (token !== requestToken) {
                        return;
                    }

                    replaceAgentRows(response.html, response.pagination);
                });
            }

            $('#btn-agents-filters-toggle').on('click', function () {
                $('body').toggleClass('agents-filters-open');
                var isOpen = $('body').hasClass('agents-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.agents-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                setTimeout(function () {
                    if (table) {
                        table.columns.adjust();
                    }
                }, 50);
            });

            $(window).on('resize', function () {
                if (table) {
                    table.columns.adjust();
                }
            });

            setTimeout(function () {
                if (table) {
                    table.columns.adjust();
                }
            }, 100);

            $('#filter-agent-name, #filter-agent-code, #filter-agent-address, #filter-agent-city').on('input keyup', function (e) {
                if (e.type === 'keyup' && e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    loadAgents(1);
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    loadAgents(1);
                }, 200);
            });

            $('#hide-inactive-check').on('change', function () {
                loadAgents(1);
            });

            $('#agents-pagination').on('click', 'a', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    return;
                }

                e.preventDefault();
                var page = new URL(href, window.location.origin).searchParams.get('page') || 1;
                loadAgents(page);
            });

            function resetAgentFilterFields() {
                $('#filter-agent-name, #filter-agent-code, #filter-agent-address, #filter-agent-city').val('');
                $('.agent-filter-multiselect').each(function () {
                    var $select = $(this);
                    $select.find('option').prop('selected', false);
                    $select.val([]);
                    $select.multiselect('clearSelection');
                    $select.closest('.multiselect-native-select').find('.multiselect-search').val('');
                    $select.closest('.multiselect-native-select').find('li.multiselect-filter-hidden')
                        .removeClass('multiselect-filter-hidden')
                        .show();
                });
                $('#hide-inactive-check').prop('checked', true);
            }

            $(document).on('click', '#clear-agent-filters', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimer);
                suppressFilterLoad = true;
                resetAgentFilterFields();
                suppressFilterLoad = false;
                loadAgents(1);
                return false;
            });

            $(document).on('click', '.filter-item .multiselect-reset a', function () {
                if (!shouldLoadFilters()) {
                    return;
                }

                setTimeout(function () {
                    loadAgents(1);
                }, 0);
            });

            setTimeout(function () {
                filtersReady = true;
            }, 200);

            $(document).on('click', '.agent-status-toggle', function () {
                var $button = $(this);
                var $row = $button.closest('tr');
                var currentStatus = String($button.data('status') || 'active').toLowerCase();
                var nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
                var nextStatusLabel = nextStatus === 'active' ? 'Active' : 'Inactive';
                var agentName = $button.data('name') || 'this agent';

                swal({
                    title: nextStatus === 'active' ? 'Activate agent?' : 'Deactivate agent?',
                    text: 'Are you sure you want to mark "' + agentName + '" as ' + nextStatusLabel.toLowerCase() + '?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: nextStatus === 'active' ? 'Yes, activate' : 'Yes, deactivate',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $button.prop('disabled', true);

                    $.ajax({
                        url: $button.data('url'),
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: nextStatus
                        },
                        success: function (response) {
                            if (!response.success) {
                                $button.prop('disabled', false);
                                swal('Error', response.message || 'Unable to update agent status.', 'error');
                                return;
                            }

                            $button
                                .data('status', nextStatus)
                                .attr('data-status', nextStatus)
                                .toggleClass('is-active', !response.is_inactive)
                                .toggleClass('is-inactive', response.is_inactive)
                                .text(response.status)
                                .prop('disabled', false);

                            $row.attr('data-is-inactive', response.is_inactive ? '1' : '0');
                            table.row($row).invalidate('dom').draw(false);

                            swal({
                                title: 'Status updated',
                                text: response.message,
                                type: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function (xhr) {
                            $button.prop('disabled', false);
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while updating the agent status.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.delete-agent', function () {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this agent';
                var $row = $(this).closest('tr');

                swal({
                    title: 'Delete agent?',
                    text: 'Are you sure you want to delete "' + name + '"? This can be restored later.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/Agents') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Agent deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $row.fadeOut(400, function () {
                                    table.row($row).remove().draw(false);
                                });
                            } else {
                                swal('Error', response.message || 'Error deleting agent.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the agent.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection