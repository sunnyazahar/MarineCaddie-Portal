@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

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
            font-size: 13px;
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
                                                @if($canWriteAdministration)
                                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary customers-add-mobile">Add customer</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="customers-filters-panel">
                                            <div class="customers-filters-fields">
                                                <div style="width: 180px;">
                                                    <div class="filter-group">
                                                        <span class="filter-label">Search</span>
                                                        <input type="text" id="filter-customer-search" class="form-control filter-input"
                                                            placeholder="type here">
                                                    </div>
                                                </div>
                                                <div style="width: 220px;">
                                                    <div class="filter-group">
                                                        <span class="filter-label">Office</span>
                                                        <select id="filter-responsible-office" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                                                            @foreach ($responsibleOffices as $office)
                                                                <option value="{{ $office }}">{{ $office }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center"
                                                    style="border: 1px solid #ced4da; padding: 0 8px; border-radius: 4px; height: 32px;">
                                                    <span style="font-size: 11px; margin-right: 8px;">Hide inactive</span>
                                                    <input type="checkbox" id="filter-hide-inactive" checked style="width: 14px; height: 14px;">
                                                </div>
                                                <div style="width: 220px;">
                                                    <div class="filter-group">
                                                        <span class="filter-label">Account manager</span>
                                                        <select id="filter-account-manager" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                                                            @foreach ($accountManagers as $manager)
                                                                <option value="{{ $manager }}">{{ $manager }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="width: 220px;">
                                                    <div class="filter-group">
                                                        <span class="filter-label">Sales manager</span>
                                                        <select id="filter-sales-manager" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                                                            @foreach ($salesManagers as $manager)
                                                                <option value="{{ $manager }}">{{ $manager }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="width: 180px;">
                                                    <div class="filter-group">
                                                        <span class="filter-label">Country</span>
                                                        <select id="filter-customer-country" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country }}">{{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="padding-bottom: 0;">
                                                    <a href="#" id="clear-customer-filters" class="clear-filters">Clear filters</a>
                                                </div>
                                            </div>
                                            <div class="customers-filters-actions">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    style="height: 28px; padding: 0 10px; border-radius: 2px;"><i
                                                        class="ti-download"></i></button>
                                                @if($canWriteAdministration)
                                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary"
                                                    style="height: 28px; font-size: 11px; padding: 5px 12px; border-radius: 2px;">Add
                                                    customer</a>
                                                @endif
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
                                                    @include('customers.partials.rows')
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="customers-pagination" class="mt-3">
                                            {{ $customers->links() }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')

<script>
        $(document).ready(function () {
            var customersIndexUrl = @json(route('customers.index'));
            var table = null;
            var searchTimer = null;
            var filtersReady = false;
            var requestToken = 0;
            var suppressFilterLoad = false;

            function shouldLoadFilters() {
                return filtersReady && !suppressFilterLoad;
            }

            $('.customer-filter-multiselect').multiselect({
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
                        loadCustomers(1);
                    }
                },
                onSelectAll: function () {
                    if (shouldLoadFilters()) {
                        loadCustomers(1);
                    }
                },
                onDeselectAll: function () {
                    if (shouldLoadFilters()) {
                        loadCustomers(1);
                    }
                }
            });

            function initCustomersTable() {
                if ($.fn.DataTable.isDataTable('#offices-table')) {
                    return $('#offices-table').DataTable();
                }

                return $('#offices-table').DataTable({
                    "dom": 'rt',
                    "lengthChange": false,
                    "paging": false,
                    "info": false,
                    "responsive": false,
                    "searching": false,
                    "ordering": true,
                    "order": [],
                    "autoWidth": false,
                    "scrollX": true,
                    "columnDefs": [
                        { "orderable": false, "targets": [5] }
                    ],
                    "language": {
                        "emptyTable": "No customers found."
                    }
                });
            }

            table = initCustomersTable();

            function currentFilterParams(page) {
                return {
                    search: $.trim($('#filter-customer-search').val() || ''),
                    responsible_office: $('#filter-responsible-office').val() || [],
                    account_manager: $('#filter-account-manager').val() || [],
                    sales_manager: $('#filter-sales-manager').val() || [],
                    country: $('#filter-customer-country').val() || [],
                    page: page || 1
                };
            }

            function replaceCustomerRows(html, paginationHtml) {
                table = initCustomersTable();
                table.clear();

                var $rows = $('<table><tbody>' + html + '</tbody></table>').find('tr').filter(function () {
                    return $(this).find('td[colspan]').length === 0;
                });

                if ($rows.length) {
                    table.rows.add($rows);
                }

                table.draw(false);
                table.columns.adjust();
                $('#customers-pagination').html(paginationHtml || '');
            }

            function loadCustomers(page) {
                var params = currentFilterParams(page);
                var token = ++requestToken;

                $.ajax({
                    url: customersIndexUrl,
                    method: 'GET',
                    data: params,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).done(function (response) {
                    if (token !== requestToken) {
                        return;
                    }

                    replaceCustomerRows(response.html, response.pagination);
                });
            }

            $('#btn-customers-filters-toggle').on('click', function () {
                $('body').toggleClass('customers-filters-open');
                var isOpen = $('body').hasClass('customers-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.customers-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
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

            $('#filter-customer-search').on('input keyup', function (e) {
                if (e.type === 'keyup' && e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    loadCustomers(1);
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    loadCustomers(1);
                }, 200);
            });

            $('#customers-pagination').on('click', 'a', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    return;
                }

                e.preventDefault();
                var page = new URL(href, window.location.origin).searchParams.get('page') || 1;
                loadCustomers(page);
            });

            function resetCustomerFilterFields() {
                $('#filter-customer-search').val('');
                $('.customer-filter-multiselect').each(function () {
                    var $select = $(this);
                    $select.find('option').prop('selected', false);
                    $select.val([]);
                    $select.multiselect('clearSelection');
                    $select.closest('.multiselect-native-select').find('.multiselect-search').val('');
                    $select.closest('.multiselect-native-select').find('li.multiselect-filter-hidden')
                        .removeClass('multiselect-filter-hidden')
                        .show();
                });
                $('#filter-hide-inactive').prop('checked', true);
            }

            $(document).on('click', '#clear-customer-filters', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimer);
                suppressFilterLoad = true;
                resetCustomerFilterFields();
                suppressFilterLoad = false;
                loadCustomers(1);
                return false;
            });

            $(document).on('click', '.filter-group .multiselect-reset a', function () {
                if (!shouldLoadFilters()) {
                    return;
                }

                setTimeout(function () {
                    loadCustomers(1);
                }, 0);
            });

            setTimeout(function () {
                filtersReady = true;
            }, 200);
        });
    </script>
@endsection