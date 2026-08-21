@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <style>
        /* Table Styling */
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
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
        .hub-status-toggle {
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
        .hub-status-toggle.is-active {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }
        .hub-status-toggle.is-inactive {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }
        .hub-status-toggle:hover {
            filter: brightness(0.97);
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
            font-size: 10px;
            
            margin-bottom: 2px;
            display: block;
            font-weight: 600;
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
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #eef2f7 !important;
            padding: 10px 15px !important;
        }
        .table tbody td {
            padding: 10px 15px !important;
            border-top: 1px solid #f8fafd !important;
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
            font-size: 11px !important;
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.25 !important;
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
        .country-flag {
            width: 18px;
            margin-right: 6px;
            vertical-align: middle;
        }

        .hub-filters-toolbar {
            display: none;
        }
        .hub-filters-bar {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: 2px solid #eef2f7;
            margin-bottom: 10px;
            padding: 8px 10px 12px;
        }
        .hub-filters-bar .hub-filter-field {
            width: 150px;
            min-width: 0;
            flex: 0 0 auto;
            margin-bottom: 0;
        }
        .hub-filters-bar .hub-filter-field-country {
            width: 200px;
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
        }
        .filter-group .multiselect-native-select .multiselect-container input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #176b87;
        }
        .filter-group .multiselect-native-select .multiselect-container .multiselect-reset a {
            color: #176b87;
            font-weight: 600;
        }
        .hub-filters-bar .hub-filter-field-sm {
            width: 120px;
        }
        .hub-filters-bar .hub-filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            padding-bottom: 1px;
        }
        .hub-filters-bar .hub-filter-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 1px;
            flex-wrap: wrap;
        }
        .hub-table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-other-companies {
            min-width: 900px;
        }

        @media (max-width: 991.98px) {
            .hub-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 4px 0 8px;
            }
            .hub-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .hub-filters-bar {
                display: none !important;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                max-height: 42vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding: 8px 0 12px;
                margin-bottom: 8px;
            }
            body.hub-filters-open .hub-filters-bar {
                display: flex !important;
            }
            #btn-hub-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }
            .hub-filters-bar .hub-filter-field,
            .hub-filters-bar .hub-filter-field-sm,
            .hub-filters-bar .hub-filter-field-country {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 auto !important;
            }
            .hub-filters-bar .filter-label {
                white-space: normal;
                line-height: 1.3;
            }
            .hub-filters-bar .hub-filter-meta,
            .hub-filters-bar .hub-filter-actions {
                margin-left: 0;
                width: 100%;
                justify-content: flex-start;
            }
            .hub-filters-bar .hub-add-desktop {
                display: none !important;
            }
            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                float: none;
                margin-bottom: 8px;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: calc(100% - 70px);
                max-width: 100%;
                margin-left: 8px !important;
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
        }

        @media (min-width: 992px) {
            .hub-filters-toolbar {
                display: none !important;
            }
            .hub-filters-bar {
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
                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <!-- Hub Index Design start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="hub-filters-toolbar">
                                                    <button type="button" id="btn-hub-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="hub-filters-toggle-label">Show filters</span>
                                                    </button>
                                                    @if($canWriteAdministration)
                                                    <div class="hub-filters-toolbar-actions">
                                                        <a class="btn btn-sm" href="{{ route('hub.create') }}"
                                                           style="font-size: 11px; padding: 6px 12px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                           Add hub
                                                        </a>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="hub-filters-bar">
                                                    <div class="form-group mb-0 hub-filter-field">
                                                        <span class="filter-label">Name</span>
                                                        <input type="text" id="filter-hub-name" class="form-control filter-input" placeholder="type here">
                                                    </div>

                                                    <div class="form-group mb-0 hub-filter-field hub-filter-field-sm">
                                                        <span class="filter-label">Code</span>
                                                        <input type="text" id="filter-hub-code" class="form-control filter-input" placeholder="type here">
                                                    </div>

                                                    <div class="form-group mb-0 hub-filter-field">
                                                        <span class="filter-label">Address</span>
                                                        <div style="position: relative;">
                                                            <input type="text" id="filter-hub-address" class="form-control filter-input" placeholder="type here" style="padding-right: 30px;">
                                                            <div style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                                                <i class="ti-more-alt" style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-0 hub-filter-field">
                                                        <span class="filter-label">City</span>
                                                        <input type="text" id="filter-hub-city" class="form-control filter-input" placeholder="type here">
                                                    </div>

                                                    <div class="form-group mb-0 hub-filter-field hub-filter-field-country">
                                                        <div class="filter-group">
                                                            <span class="filter-label">Country</span>
                                                            <select id="filter-hub-country" class="form-control filter-input hub-filter-multiselect" multiple="multiple">
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country }}">{{ $country }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="hub-filter-meta">
                                                        <div class="d-flex align-items-center" style="border: 1px solid #ced4da; padding: 4px 8px; border-radius: 2px; height: 30px;">
                                                            <span style="font-size: 11px; margin-right: 8px;">Hide inactive</span>
                                                            <input type="checkbox" id="filter-hide-inactive" checked style="width: 14px; height: 14px;">
                                                        </div>
                                                        <a href="#" id="clear-hub-filters" class="clear-filters">Clear filters</a>
                                                    </div>

                                                    <div class="hub-filter-actions">
                                                        <a href="#" style="border: 1px solid #ced4da; padding: 4px 10px; border-radius: 2px; color: #666; font-size: 14px;">
                                                            <i class="ti-download"></i>
                                                        </a>
                                                        @if($canWriteAdministration)
                                                        <a class="btn btn-primary hub-add-desktop" href="{{ route('hub.create') }}"
                                                           style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                           Add hub
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="hub-table-wrap dt-responsive">
                                                    <table id="offices-table" class="table-other-companies">
                                                        <thead style="background: #fdfdfd;">
                                                            <tr>
                                                                <th style=" font-weight: 600;">Hub name</th>
                                                                <th style=" font-weight: 600;">Code</th>
                                                                <th style=" font-weight: 600;">City</th>
                                                                <th style=" font-weight: 600;">Country</th>
                                                                <th style=" font-weight: 600;">Phone number</th>
                                                                <th style=" font-weight: 600;">E-mail</th>
                                                                <th style=" font-weight: 600;">Status</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @include('hub.partials.rows')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="hubs-pagination" class="mt-3 px-3 pb-2">
                                                    {{ $hubs->links() }}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Hub Index Design end -->
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
            var hubsIndexUrl = @json(route('hub.index'));
            var table = null;
            var searchTimer = null;
            var filtersReady = false;
            var requestToken = 0;
            var suppressFilterLoad = false;
            var currentPage = 1;

            function shouldLoadFilters() {
                return filtersReady && !suppressFilterLoad;
            }

            $('.hub-filter-multiselect').multiselect({
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
                        loadHubs(1);
                    }
                },
                onSelectAll: function () {
                    if (shouldLoadFilters()) {
                        loadHubs(1);
                    }
                },
                onDeselectAll: function () {
                    if (shouldLoadFilters()) {
                        loadHubs(1);
                    }
                }
            });

            function initHubsTable() {
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
                        { "orderable": false, "targets": [7] }
                    ],
                    "language": {
                        "emptyTable": "No hubs found."
                    }
                });
            }

            table = initHubsTable();

            function currentFilterParams(page) {
                return {
                    name: $.trim($('#filter-hub-name').val() || ''),
                    code: $.trim($('#filter-hub-code').val() || ''),
                    address: $.trim($('#filter-hub-address').val() || ''),
                    city: $.trim($('#filter-hub-city').val() || ''),
                    country: $('#filter-hub-country').val() || [],
                    hide_inactive: $('#filter-hide-inactive').is(':checked') ? 1 : 0,
                    page: page || 1
                };
            }

            function replaceHubRows(html, paginationHtml) {
                table = initHubsTable();
                table.clear();

                var $rows = $('<table><tbody>' + html + '</tbody></table>').find('tr').filter(function () {
                    return $(this).find('td[colspan]').length === 0;
                });

                if ($rows.length) {
                    table.rows.add($rows);
                }

                table.draw(false);
                table.columns.adjust();
                $('#hubs-pagination').html(paginationHtml || '');
            }

            function loadHubs(page) {
                var params = currentFilterParams(page);
                var token = ++requestToken;
                currentPage = page || 1;

                $.ajax({
                    url: hubsIndexUrl,
                    method: 'GET',
                    data: params,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).done(function (response) {
                    if (token !== requestToken) {
                        return;
                    }

                    replaceHubRows(response.html, response.pagination);
                });
            }

            function resetHubFilterFields() {
                $('#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city').val('');
                $('.hub-filter-multiselect').each(function () {
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

            $('#btn-hub-filters-toggle').on('click', function () {
                $('body').toggleClass('hub-filters-open');
                var isOpen = $('body').hasClass('hub-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.hub-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
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

            $('#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city').on('input keyup', function (e) {
                if (e.type === 'keyup' && e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    loadHubs(1);
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    loadHubs(1);
                }, 200);
            });

            $('#filter-hide-inactive').on('change', function () {
                loadHubs(1);
            });

            $('#hubs-pagination').on('click', 'a', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    return;
                }

                e.preventDefault();
                var page = new URL(href, window.location.origin).searchParams.get('page') || 1;
                loadHubs(page);
            });

            $(document).on('click', '#clear-hub-filters', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimer);
                suppressFilterLoad = true;
                resetHubFilterFields();
                suppressFilterLoad = false;
                loadHubs(1);
                return false;
            });

            $(document).on('click', '.hub-filter-field .multiselect-reset a', function () {
                if (!shouldLoadFilters()) {
                    return;
                }

                setTimeout(function () {
                    loadHubs(1);
                }, 0);
            });

            setTimeout(function () {
                filtersReady = true;
            }, 200);

            $(document).on('click', '.hub-status-toggle', function() {
                var $button = $(this);
                var $row = $button.closest('tr');
                var currentStatus = String($button.data('status') || 'active').toLowerCase();
                var nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
                var nextStatusLabel = nextStatus === 'active' ? 'Active' : 'Inactive';
                var hubName = $button.data('name') || 'this hub';

                swal({
                    title: nextStatus === 'active' ? 'Activate hub?' : 'Deactivate hub?',
                    text: 'Are you sure you want to mark "' + hubName + '" as ' + nextStatusLabel.toLowerCase() + '?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: nextStatus === 'active' ? 'Yes, activate' : 'Yes, deactivate',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
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
                        success: function(response) {
                            if (!response.success) {
                                $button.prop('disabled', false);
                                swal('Error', response.message || 'Unable to update hub status.', 'error');
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

                            if (response.is_inactive && $('#filter-hide-inactive').is(':checked')) {
                                loadHubs(currentPage);
                            } else if (table && table.row($row).node()) {
                                table.row($row).invalidate('dom').draw(false);
                            }

                            swal({
                                title: 'Status updated',
                                text: response.message,
                                type: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            $button.prop('disabled', false);
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while updating the hub status.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.delete-hub', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this hub';

                swal({
                    title: 'Delete hub?',
                    text: 'Are you sure you want to delete "' + name + '"? This can be restored later.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/hubs') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Hub deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadHubs(currentPage);
                            } else {
                                swal('Error', response.message || 'Error deleting hub.', 'error');
                            }
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the hub.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
