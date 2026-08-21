@extends('layouts.app')

@section('styles')

    <!-- Date-range picker css  -->

    <style>
        .office-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .office-table th {
            text-align: left;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .office-table td {
            padding: 6px 12px;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
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
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            padding: 0 10px;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding-right: 10px;
            margin-right: 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #ced4da;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 !important;
            background: transparent !important;
            width: 100%;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
        }
        .filter-group i {
            color: #008080;
            font-size: 14px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
            margin-bottom: 10px;
        }
        .clear-filters {
            font-size: 12px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            margin-left: 10px;
            align-self: center;
            display: flex;
            align-items: center;
        }
        .label {
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        .label-stock {
            background-color: #d4edda !important;
            color: #155724 !important;
            border: 1px solid #c3e6cb;
        }
        .label-pending {
            background-color: #ffeeba !important;
            color: #856404 !important;
            border: 1px solid #ffeeba;
        }
        .label-danger {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border: 1px solid #f5c6cb;
        }
        .label-inverse {
            background-color: #e2e3e5 !important;
            color: #383d41 !important;
            border: 1px solid #d6d8db;
        }
        .landed-badge {
            background: #dcf0fa;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        .text-pending {
            color: #f59e0b !important;
        }
        .table-link {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 500;
        }
        .table-link:hover {
            text-decoration: underline;
        }
        .icon-density {
            font-size: 14px;
            margin: 0 2px;
            cursor: pointer;
        }
        .icon-pdf { color: #64748b; }
        .icon-bell { color: #64748b; }
        .icon-warning { color: #f59e0b; }
        .icon-dollar { color: #10b981; }
        
        .shipment-badge {
            background-color: #fef08a;
            color: #1e293b;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
        }
        .icon-doc-blue {
            color: #4682b4;
            margin-left: 5px;
        }
        .icon-warning-red {
            color: #ff5252;
            margin-right: 5px;
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
            padding: 6px 10px 6px 6px;
            display: block;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
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
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            min-height: 30px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #495057 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            margin-top: 0 !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice span {
            color: #495057 !important;
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
        /* Table visibility fixes */
        .dt-responsive {
            width: 100%;
        }
        .table-scroll-wrapper {
            width: 100%;
            overflow-x: auto;
            max-height: 750px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-bottom: 1px solid #e5e7eb;
        }
        .office-table {
            min-width: 0;
            border-collapse: separate; /* Required for sticky borders */
            border-spacing: 0;
        }
        @media (min-width: 1200px) {
            .office-table {
                min-width: 1500px;
            }
        }
        .table-scroll-wrapper .office-table,
        .dataTables_wrapper .office-table {
            min-width: 1100px;
        }

        .pickup-filters-toolbar {
            display: none;
        }
        .pickup-filters-fields {
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .pickup-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 4px 0 8px;
            }

            /* Always show filter fields on mobile; toggle only collapses */
            .pickup-filters-fields {
                display: flex !important;
                flex-direction: column;
                width: 100%;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 8px;
                margin-bottom: 8px;
                border-bottom: 1px solid #eef2f7;
            }

            body.pickup-filters-collapsed .pickup-filters-fields {
                display: none !important;
            }

            #btn-pickup-filters-toggle.is-collapsed {
                background: transparent !important;
                color: #008080 !important;
            }

            /* Hide column-picker — its Search box was the only thing showing */
            .pickup-filters-fields .mr-2,
            .pickup-filters-fields .btn-filter-toggle,
            .pickup-filters-fields #filter-multiselect + .btn-group,
            .pickup-filters-fields .multiselect-native-select .btn-group:has(.btn-filter-toggle) {
                display: none !important;
            }

            .pickup-filters-fields > div {
                width: 100% !important;
                max-width: 100% !important;
            }

            .pickup-filters-fields .row.no-gutters {
                margin-left: 0 !important;
                margin-right: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                width: 100%;
            }

            .pickup-filters-fields .custom-col,
            .pickup-filters-fields .custom-col[style*="flex"],
            .pickup-filters-fields #col-Account-manager,
            .pickup-filters-fields #col-Stock-number,
            .pickup-filters-fields #col-Expected-del-date,
            .pickup-filters-fields #col-Pick-up-date,
            .pickup-filters-fields #col-Deadline-warehouse,
            .pickup-filters-fields #col-Handled-by,
            .pickup-filters-fields #col-Vessel,
            .pickup-filters-fields #col-Supplier-ref,
            .pickup-filters-fields #col-Hub-Agent {
                flex: 0 0 auto !important;
                max-width: 100% !important;
                width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .pickup-filters-fields .filter-group {
                width: 100%;
                max-width: 100%;
            }

            .table-scroll-wrapper,
            .dt-responsive.table-responsive,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                width: 100% !important;
                max-width: 100%;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .dataTables_scrollHead {
                overflow: hidden !important;
            }

            #offices-table,
            .office-table {
                min-width: 1100px !important;
            }

            .pickup-filters-fields .filter-group {
                width: 100%;
            }

            .pickup-filters-fields .clear-filters {
                display: inline-block;
                margin: 4px 0 8px;
            }

            .table-scroll-wrapper,
            .dt-responsive.table-responsive {
                max-height: calc(100vh - 260px);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .pagination-sticky-footer {
                justify-content: center !important;
                overflow-x: auto;
            }

            .dataTables_wrapper .dataTables_paginate {
                justify-content: center;
            }
        }

        @media (min-width: 992px) {
            .pickup-filters-toolbar {
                display: none !important;
            }
            .pickup-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.pickup-filters-collapsed .pickup-filters-fields {
                display: flex !important;
            }
        }
        .office-table thead th {
            position: sticky !important;
            top: 0 !important;
            background-color: #fdfdfd !important;
            z-index: 100 !important;
            border-top: 1px solid #e5e7eb !important;
            border-bottom: 2px solid #dee2e6 !important;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); 
        }
        .office-table th, .office-table td {
            white-space: nowrap; 
        }
        /* Hide sorting icons for checkbox column */
        .office-table thead th:first-child:after,
        .office-table thead th:first-child:before {
            display: none !important;
        }
        .office-table thead th:first-child {
            padding-right: 10px !important; /* Reset padding usually reserved for arrows */
        }

        /* Pagination Styling */
        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            background-color: #ffffff;
            padding: 10px 0;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        .pagination .page-item.active .page-link {
            background-color: #008080 !important;
            border-color: #008080 !important;
            color: #fff !important;
        }
        .pagination .page-link {
            color: #008080 !important;
            font-size: 12px;
            padding: 5px 10px;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d !important;
        }
    </style>
    <x-lists.multiselect-assets />
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
                                        <!-- Base Style - Compact start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="pickup-filters-toolbar">
                                                    <button type="button" id="btn-pickup-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="pickup-filters-toggle-label">Hide filters</span>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 pickup-filters-fields">
                                                    <div style="width: 100%;">
                                                        <div class="row no-gutters">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Stock number" selected>Stock number</option>
                                                                    <option value="Expected del. date" selected>Expected del. date</option>
                                                                    <option value="Pick up date" selected>Pick up date</option>
                                                                    <option value="Deadline warehouse" selected>Deadline warehouse</option>
                                                                    <option value="Handled by" selected>Handled by</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Supplier ref" selected>Supplier ref</option>
                                                                    <option value="Hub/Agent" selected>Hub/Agent</option>
                                                                </select>
                                                            </div>
                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select id="filter-account-manager" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager }}">{{ $manager }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Stock-number" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Stock number</span>
                                                                    <input type="text" id="filter-stock-number" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Expected-del-date" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Expected del. date</span>
                                                                    <input type="text" id="filter-expected-delivery" class="form-control filter-input date-range-filter" placeholder="Select range">
                                                                    <i class="ti-calendar ml-1" style="font-size: 12px; opacity: 0.7;"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Pick-up-date" class="custom-col" style="flex: 0 0 225px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Pick up date</span>
                                                                    <input type="text" id="filter-pickup-date" class="form-control filter-input date-range-filter" placeholder="Select range">
                                                                    <i class="ti-calendar ml-1" style="font-size: 12px; opacity: 0.7;"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Deadline-warehouse" class="custom-col" style="flex: 0 0 320px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Deadline warehouse</span>
                                                                    <input type="text" id="filter-deadline-warehouse" class="form-control filter-input date-range-filter" placeholder="Select range">
                                                                    <i class="ti-calendar ml-1" style="font-size: 12px; opacity: 0.7;"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Handled-by" class="custom-col" style="flex: 0 0 227px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Handled by</span>
                                                                    <select id="filter-handled-by" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($handledByOptions as $handledBy)
                                                                            <option value="{{ $handledBy }}">{{ $handledBy }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select id="filter-vessel" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Supplier-ref" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Supplier ref</span>
                                                                    <input type="text" id="filter-supplier-ref" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Hub-Agent" class="custom-col" style="flex: 0 0 280px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Hub/Agent</span>
                                                                    <select id="filter-hub-agent" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($hubAgents as $hubAgent)
                                                                            <option value="{{ $hubAgent }}">{{ $hubAgent }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <x-lists.clear-filters id="clear-pickup-filters" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="dt-responsive table-responsive">
                                                    <x-lists.ajax-table
                                                        table-id="offices-table"
                                                        table-class="office-table"
                                                        pagination-id="pickup-pagination"
                                                        :paginator="$crrs->links()"
                                                    >
                                                        <x-slot:head>
                                                            <tr>
                                                                <th>Stock number</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>PO number</th>
                                                                <th>Supplier</th>
                                                                <th>Supplier ref</th>
                                                                <th>Expected del. date</th>
                                                                <th>Deadline warehouse</th>
                                                                <th>Comment</th>
                                                                <th>Status</th>
                                                                <th>Handled by</th>
                                                                <th>Pick up date</th>
                                                            </tr>
                                                        </x-slot:head>
                                                        @include('Stock.partials.pickup-rows')
                                                    </x-lists.ajax-table>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')


    <!-- date-range-picker js -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            initializeSearchableFilterMultiselect(
                '#filter-account-manager, #filter-handled-by, #filter-vessel, #filter-hub-agent',
                {
                    onChange: function () {
                        if (window.pickupListFilters) {
                            window.pickupListFilters.load(1);
                        }
                    },
                    onSelectAll: function () {
                        if (window.pickupListFilters) {
                            window.pickupListFilters.load(1);
                        }
                    },
                    onDeselectAll: function () {
                        if (window.pickupListFilters) {
                            window.pickupListFilters.load(1);
                        }
                    }
                }
            );

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: false,
                buttonWidth: '100%',
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

            $('#filter-multiselect').multiselect('selectAll', false);
            $('#filter-multiselect').multiselect('updateButtonText');

            var pickupFilterIds = [
                'col-Account-manager',
                'col-Stock-number',
                'col-Expected-del-date',
                'col-Pick-up-date',
                'col-Deadline-warehouse',
                'col-Handled-by',
                'col-Vessel',
                'col-Supplier-ref',
                'col-Hub-Agent'
            ];

            function isPickupMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureMobileFiltersVisible() {
                if (!isPickupMobile()) {
                    return;
                }
                pickupFilterIds.forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                // Column picker + its "Search" filter must stay hidden on mobile
                $('.pickup-filters-fields .mr-2').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                // On mobile always show every filter field (column picker is hidden)
                if (isPickupMobile()) {
                    ensureMobileFiltersVisible();
                    return;
                }

                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Stock number', id: 'col-Stock-number'},
                    {val: 'Expected del. date', id: 'col-Expected-del-date'},
                    {val: 'Pick up date', id: 'col-Pick-up-date'},
                    {val: 'Deadline warehouse', id: 'col-Deadline-warehouse'},
                    {val: 'Handled by', id: 'col-Handled-by'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Supplier ref', id: 'col-Supplier-ref'},
                    {val: 'Hub/Agent', id: 'col-Hub-Agent'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }

            toggleFilterVisibility();
            ensureMobileFiltersVisible();
            $(window).on('resize', function () {
                toggleFilterVisibility();
                ensureMobileFiltersVisible();
            });

            // Initialize Date Range Picker
            $('.date-range-filter').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD.MM.YYYY'
                }
            });

            $('.date-range-filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY')).trigger('change');
            });

            $('.date-range-filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('').trigger('change');
            });

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt>',
                "lengthChange": false,
                "paging": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "scrollX": true
            });

            $('#btn-pickup-filters-toggle').on('click', function () {
                $('body').toggleClass('pickup-filters-collapsed');
                var collapsed = $('body').hasClass('pickup-filters-collapsed');
                $(this).toggleClass('is-collapsed', collapsed);
                $(this).find('.pickup-filters-toggle-label').text(collapsed ? 'Show filters' : 'Hide filters');
                if (!collapsed) {
                    ensureMobileFiltersVisible();
                }
                setTimeout(function () {
                    table.columns.adjust();
                }, 50);
            });

            window.pickupListFilters = bindAjaxListFilters({
                tableSelector: '#offices-table',
                paginationSelector: '#pickup-pagination',
                indexUrl: @json(route('pickup-work-list')),
                existingTable: table,
                clearSelector: '#clear-pickup-filters',
                getParams: function (page) {
                    return {
                        account_manager: $('#filter-account-manager').val() || [],
                        handled_by: $('#filter-handled-by').val() || [],
                        vessel: $('#filter-vessel').val() || [],
                        hub_agent: $('#filter-hub-agent').val() || [],
                        stock_number: $.trim($('#filter-stock-number').val() || ''),
                        supplier_reference: $.trim($('#filter-supplier-ref').val() || ''),
                        expected_delivery: $.trim($('#filter-expected-delivery').val() || ''),
                        deadline_warehouse: $.trim($('#filter-deadline-warehouse').val() || ''),
                        pickup_date: $.trim($('#filter-pickup-date').val() || ''),
                        page: page || 1
                    };
                },
                textSelectors: '#filter-stock-number, #filter-supplier-ref',
                changeSelectors: '#filter-expected-delivery, #filter-deadline-warehouse, #filter-pickup-date',
                resetFields: function () {
                    clearSearchableFilterMultiselect(
                        '#filter-account-manager, #filter-handled-by, #filter-vessel, #filter-hub-agent',
                        false
                    );
                    $('#filter-stock-number, #filter-supplier-ref, #filter-expected-delivery, #filter-deadline-warehouse, #filter-pickup-date').val('');
                },
                afterDraw: function () {
                    table.columns.adjust();
                }
            });
        });
    </script>
@endpush
