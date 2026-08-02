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
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
            min-width: 0;
        }

        .table-scroll-wrapper .table-other-companies,
        .dataTables_wrapper .table-other-companies,
        .table-responsive .table-other-companies {
            min-width: 900px;
        }

        .table-other-companies th {
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            background: #f8fafd;
        }

        .table-other-companies td {
            padding: 8px 10px;
            font-size: 11px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .table-other-companies tr:hover td {
            background-color: #f9fafb;
        }

        .filter-label {
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 2px;
            display: block;
        }

        .filter-input {
            height: 28px;
            font-size: 11px;
            border-radius: 2px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }

        .clear-filters {
            font-size: 11px;
            color: #3b82f6;
            text-decoration: none;
            cursor: pointer;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            font-size: 11px !important;
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            color: #495057 !important;
            background-color: transparent !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            top: 1px !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }

        .main-body .page-wrapper {
            padding: 5px !important;
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

        .customers-filters-toolbar {
            display: none;
        }

        .customers-add-mobile {
            display: none;
        }

        .customers-filters-panel {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .customers-filters-fields {
            display: flex;
            align-items: flex-end;
            gap: 15px;
            flex-grow: 1;
            flex-wrap: wrap;
        }

        .customers-filters-actions {
            display: flex;
            gap: 5px;
            flex-shrink: 0;
        }

        #offices-table {
            min-width: 900px !important;
            width: 100% !important;
        }

        #offices-table th,
        #offices-table td {
            white-space: nowrap;
        }

        /* Hide DataTables scrollX cloned header inside scroll body */
        #offices-table_wrapper .dataTables_scrollBody > table > thead,
        #offices-table_wrapper .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        #offices-table_wrapper .dataTables_scrollBody thead tr,
        #offices-table_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
            background: transparent !important;
        }
        #offices-table_wrapper .dataTables_scrollBody thead th:before,
        #offices-table_wrapper .dataTables_scrollBody thead th:after {
            display: none !important;
            content: none !important;
        }

        @media (max-width: 991.98px) {
            .customers-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 0 0 10px;
                margin-bottom: 0;
            }

            .customers-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .customers-add-mobile {
                display: inline-flex !important;
                align-items: center;
                height: 28px;
                font-size: 11px;
                padding: 5px 12px;
                border-radius: 2px;
                text-decoration: none;
            }

            .customers-filters-panel {
                display: none !important;
                flex-direction: column;
                justify-content: flex-start !important;
                align-items: stretch !important;
                align-content: flex-start !important;
                gap: 8px;
                flex-grow: 0 !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: 48vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 12px;
                padding: 0 2px 8px;
            }

            body.customers-filters-open .customers-filters-panel {
                display: flex !important;
            }

            #btn-customers-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }

            .customers-filters-fields {
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                align-items: stretch !important;
                align-content: flex-start !important;
                gap: 8px !important;
                width: 100%;
                flex-grow: 0 !important;
                flex-wrap: nowrap !important;
                height: auto !important;
                min-height: 0 !important;
            }

            .customers-filters-fields > div {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                flex: 0 0 auto !important;
            }

            .customers-filters-fields > div.d-flex {
                height: 28px !important;
            }

            /* Only stretch real inputs — never force native <select> visible (breaks Select2) */
            .customers-filters-fields input.filter-input {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }

            .customers-filters-fields select.filter-input,
            .customers-filters-fields select.select2,
            .customers-filters-panel .select2-hidden-accessible {
                position: absolute !important;
                width: 1px !important;
                height: 1px !important;
                padding: 0 !important;
                margin: -1px !important;
                overflow: hidden !important;
                clip: rect(0, 0, 0, 0) !important;
                border: 0 !important;
                display: block !important;
            }

            .customers-filters-fields .select2-container,
            .customers-filters-panel .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
                margin: 0 !important;
                height: auto !important;
                min-height: 0 !important;
            }

            .customers-filters-panel .select2-container .select2-selection--single {
                width: 100% !important;
                height: 28px !important;
                min-height: 28px !important;
            }

            .customers-filters-actions {
                display: none !important;
            }

            .card-block {
                padding: 12px !important;
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

            .dt-responsive,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (min-width: 992px) {
            .customers-filters-toolbar {
                display: none !important;
            }
            .customers-filters-panel {
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
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    @include('layouts.partials.pcoded-shell-start')
                                <!-- Base Style - Compact start -->
                                <div class="card" style="border-radius: 0; box-shadow: none; border: 1px solid #eef2f7;">
                                    <div class="card-block" style="padding: 15px;">
                                        <div class="customers-filters-toolbar">
                                            <button type="button" id="btn-customers-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                <i class="ti-filter"></i> <span class="customers-filters-toggle-label">Show filters</span>
                                            </button>
                                            <div class="customers-filters-toolbar-actions">
                                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary customers-add-mobile">Add customer</a>
                                            </div>
                                        </div>
                                        <div class="customers-filters-panel">
                                            <div class="customers-filters-fields">
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Search</span>
                                                    <input type="text" id="filter-customer-search" class="form-control filter-input"
                                                        placeholder="type here">
                                                </div>
                                                <div style="width: 180px;">
                                                    <span class="filter-label">Responsible offices</span>
                                                    <select id="filter-responsible-office" class="form-control filter-input select2">
                                                        <option value=""></option>
                                                        @foreach ($responsibleOffices as $office)
                                                            <option value="{{ $office }}">{{ $office }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="d-flex align-items-center"
                                                    style="border: 1px solid #ced4da; padding: 0 8px; border-radius: 2px; height: 28px;">
                                                    <span style="font-size: 11px; margin-right: 8px;">Hide inactive</span>
                                                    <input type="checkbox" id="filter-hide-inactive" checked style="width: 14px; height: 14px;">
                                                </div>
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Account managers</span>
                                                    <select id="filter-account-manager" class="form-control filter-input select2">
                                                        <option value=""></option>
                                                        @foreach ($accountManagers as $manager)
                                                            <option value="{{ $manager }}">{{ $manager }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Sales managers</span>
                                                    <select id="filter-sales-manager" class="form-control filter-input select2">
                                                        <option value=""></option>
                                                        @foreach ($salesManagers as $manager)
                                                            <option value="{{ $manager }}">{{ $manager }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="width: 120px;">
                                                    <span class="filter-label">Country</span>
                                                    <select id="filter-customer-country" class="form-control filter-input select2">
                                                        <option value=""></option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}">{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="padding-bottom: 5px;">
                                                    <a href="#" id="clear-customer-filters" class="clear-filters">Clear filters</a>
                                                </div>
                                            </div>
                                            <div class="customers-filters-actions">
                                                <button class="btn btn-outline-secondary"
                                                    style="height: 28px; padding: 0 10px; border-radius: 2px;"><i
                                                        class="ti-download"></i></button>
                                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary"
                                                    style="height: 28px; font-size: 11px; padding: 5px 12px; border-radius: 2px;">Add
                                                    customer</a>
                                            </div>
                                        </div>

                                        <div class="dt-responsive">
                                            <table id="offices-table" class="table-other-companies">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 25%;">Customer name</th>
                                                        <th style="width: 15%;">Main contact</th>
                                                        <th style="width: 15%;">Responsible office</th>
                                                        <th style="width: 20%;">Account manager</th>
                                                        <th style="width: 10%;">Status</th>
                                                        <th style="width: 5%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customers as $customer)
                                                        @php
                                                            $mainContact = $customer->contacts->first();
                                                            $mainContactName = $mainContact?->name ?? '';
                                                            $responsibleOffice = $customer->responsible?->accountManager?->office?->office_short_name ?? '';
                                                            $accountManager = $customer->responsible?->accountManager?->name ?? '';
                                                            $salesManager = $customer->responsible?->salesManager?->name ?? '';
                                                            $countryName = $customer->primaryAddress?->country?->name ?? '';
                                                            $searchText = trim(implode(' ', array_filter([
                                                                $customer->customer_name,
                                                                $customer->customer_number,
                                                                $customer->email,
                                                                $customer->phone,
                                                                $mainContactName,
                                                                $responsibleOffice,
                                                                $accountManager,
                                                                $salesManager,
                                                                $countryName,
                                                            ])));
                                                        @endphp
                                                        <tr
                                                            data-search-text="{{ $searchText }}"
                                                            data-responsible-office="{{ $responsibleOffice }}"
                                                            data-account-manager="{{ $accountManager }}"
                                                            data-sales-manager="{{ $salesManager }}"
                                                            data-country="{{ $countryName }}"
                                                            data-is-inactive="0"
                                                        >
                                                            <td>
                                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                                    style="color: #3b82f6; font-weight: 500;">
                                                                    {{ $customer->customer_name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $customer->responsible->accountManager->office->phone_number ?? '—' }}</td>
                                                            <td>{{ $responsibleOffice ?: '—' }}</td>
                                                            <td>{{ $accountManager ?: '—' }}</td>
                                                            <td>
                                                                <span class="label label-success">Active</span>
                                                            </td>
                                                            <td class="text-right">
                                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                                    style="color: #ccc;"><i class="ti-pencil"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')
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
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: '100%'
            });

            function fixCustomerFilterSelect2Width() {
                $('.customers-filters-panel .select2-container').css('width', '100%');
            }

            var table = $('#offices-table').DataTable({
                "dom": 'rt<"d-flex flex-wrap justify-content-between align-items-center"ip>',
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "orderable": false, "targets": [5] }
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            $('#btn-customers-filters-toggle').on('click', function () {
                $('body').toggleClass('customers-filters-open');
                var isOpen = $('body').hasClass('customers-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.customers-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                setTimeout(function () {
                    fixCustomerFilterSelect2Width();
                    table.columns.adjust();
                }, 50);
            });

            $(window).on('resize', function () {
                fixCustomerFilterSelect2Width();
                table.columns.adjust();
            });

            setTimeout(function () {
                fixCustomerFilterSelect2Width();
                table.columns.adjust();
            }, 100);

            function rowData($row, key) {
                return String($row.attr('data-' + key) || '');
            }

            function getFilterText(selector) {
                return String($(selector).val() || '').toLowerCase().trim();
            }

            function matchesContains(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '').toLowerCase().indexOf(filterValue) !== -1;
            }

            function matchesExact(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '') === filterValue;
            }

            $('#filter-customer-search, #filter-responsible-office, #filter-account-manager, #filter-sales-manager, #filter-customer-country, #filter-hide-inactive').on('change keyup', function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);

                if ($('#filter-hide-inactive').is(':checked') && rowData($row, 'is-inactive') === '1') {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-customer-search'), rowData($row, 'search-text'))) {
                    return false;
                }

                if (!matchesExact($('#filter-responsible-office').val(), rowData($row, 'responsible-office'))) {
                    return false;
                }

                if (!matchesExact($('#filter-account-manager').val(), rowData($row, 'account-manager'))) {
                    return false;
                }

                if (!matchesExact($('#filter-sales-manager').val(), rowData($row, 'sales-manager'))) {
                    return false;
                }

                if (!matchesExact($('#filter-customer-country').val(), rowData($row, 'country'))) {
                    return false;
                }

                return true;
            });

            $('#clear-customer-filters').on('click', function (e) {
                e.preventDefault();
                $('#filter-customer-search').val('');
                $('#filter-responsible-office, #filter-account-manager, #filter-sales-manager, #filter-customer-country').val(null).trigger('change');
                $('#filter-hide-inactive').prop('checked', true);
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endsection