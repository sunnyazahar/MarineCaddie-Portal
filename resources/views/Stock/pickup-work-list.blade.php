@extends('layouts.app')

@section('styles')
    <script>
        /* Apply ASAP in <head> so first paint is not sand/grey */
        document.documentElement.classList.add('pickup-list-page');
    </script>
    @include('partials.list-pagination-footer-styles')

    <!-- Date-range picker css  -->

    <style>
        /* Full-viewport list shell — use vh on the card (not % chain) like stocks list */
        html.pickup-list-page,
        html.pickup-list-page body {
            height: 100% !important;
            overflow: hidden !important;
        }
        html.pickup-list-page body,
        body.pickup-list-page {
            overflow: hidden !important;
            height: 100vh !important;
            max-height: 100vh !important;
            background: #ffffff !important;
            background-image: none !important;
        }
        html.pickup-list-page #app,
        html.pickup-list-page main,
        html.pickup-list-page .pcoded,
        html.pickup-list-page .pcoded-container.navbar-wrapper,
        body.pickup-list-page #app,
        body.pickup-list-page main,
        body.pickup-list-page .pcoded,
        body.pickup-list-page .pcoded-container.navbar-wrapper {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }
        html.pickup-list-page .pcoded-main-container,
        body.pickup-list-page .pcoded-main-container {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
        }
        html.pickup-list-page .pcoded-wrapper,
        body.pickup-list-page .pcoded-wrapper {
            height: calc(100vh - 4rem) !important;
            min-height: calc(100vh - 4rem) !important;
            max-height: calc(100vh - 4rem) !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }
        html.pickup-list-page .pcoded-navbar,
        body.pickup-list-page .pcoded-navbar {
            top: 4rem !important;
            bottom: 0 !important;
            height: calc(100vh - 4rem) !important;
            min-height: calc(100vh - 4rem) !important;
        }
        html.pickup-list-page .pcoded-content,
        body.pickup-list-page .pcoded-content {
            overflow: hidden !important;
            height: calc(100vh - 4rem) !important;
            min-height: calc(100vh - 4rem) !important;
            max-height: calc(100vh - 4rem) !important;
            background: #ffffff !important;
            background-image: none !important;
        }
        html.pickup-list-page .pcoded-inner-content,
        html.pickup-list-page .main-body,
        html.pickup-list-page .page-wrapper,
        html.pickup-list-page .page-body,
        body.pickup-list-page .pcoded-inner-content,
        body.pickup-list-page .main-body,
        body.pickup-list-page .page-wrapper,
        body.pickup-list-page .page-body {
            height: 100% !important;
            min-height: 100% !important;
            max-height: 100% !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            position: relative !important;
        }
        .pickup-list-card {
            display: flex !important;
            flex-direction: column !important;
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            margin: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            overflow: hidden !important;
        }
        .pickup-list-card > .card-block {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 auto !important;
            min-height: 0 !important;
            height: 100% !important;
            overflow: hidden !important;
            padding: 8px 12px 0 !important;
            background: #ffffff !important;
        }
        .pickup-filters-fixed {
            flex-shrink: 0;
            padding: 8px 0 0;
            margin: 0;
            background: #fff;
            position: relative;
            z-index: 120;
        }
        .pickup-table-area {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            background: #ffffff !important;
        }
        #pickup-pagination.pagination-sticky-footer {
            flex-shrink: 0 !important;
            margin-top: auto !important;
        }
        .pickup-table-area .dataTables_wrapper,
        .pickup-table-area .dt-responsive,
        .pickup-table-area .list-ajax-table-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
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
            align-items: stretch;
            border: 1px solid #ced4da;
            padding: 0 !important;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: visible;
            width: 100%;
            box-sizing: border-box;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin: 0 !important;
            padding: 0 10px !important;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #e2e8f0;
            height: auto !important;
            display: inline-flex;
            align-items: center;
            background: #f8fafc;
            flex: 0 0 auto;
            min-width: fit-content;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 8px !important;
            background: transparent !important;
            width: 100%;
            min-width: 0;
            flex: 1 1 auto;
        }
        .filter-group .searchable-filter-wrapper,
        .filter-group .select2-container {
            flex: 1 1 auto;
            min-width: 0;
            height: 100%;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
            min-height: 30px !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 8px !important;
        }
        .filter-group .filter-date-icon,
        .filter-group > i.ti-calendar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 28px;
            width: 28px;
            color: #0088c7 !important;
            font-size: 13px !important;
            opacity: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .pickup-filters-fields {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .pickup-filters-fields .custom-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }

        .pickup-filters-fields .custom-col {
            padding-right: 5px;
            padding-left: 5px;
            margin-bottom: 0 !important;
            flex-shrink: 0;
        }

        .pickup-filters-fields .filter-row {
            margin-bottom: 0;
        }

        .pickup-filters-fields .list-dense-filter-row {
            gap: 8px;
            border-bottom: none !important;
            padding: 0 !important;
            background: transparent !important;
            align-items: center;
            flex-wrap: wrap !important;
            align-content: flex-start;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .pickup-filters-fields .list-dense-filter-row .btn-clear-filters {
            display: inline-flex;
            align-items: center;
            height: 32px;
            white-space: nowrap;
            margin-left: 0;
            padding: 0 4px;
        }

        .pickup-table-area #offices-table thead th {
            z-index: 10 !important;
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
        
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
        body.pickup-list-page .main-body .page-wrapper,
        body.pickup-list-page .pcoded-inner-content {
            padding: 0 !important;
        }
        /* Table visibility fixes */
        .dt-responsive {
            width: 100%;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .table-scroll-wrapper {
            width: 100%;
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto !important;
            max-height: none !important;
            -webkit-overflow-scrolling: touch;
            border-bottom: none;
            position: relative;
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

            body.pickup-filters-collapsed .pickup-filters-fields,
            .pickup-filters-fields.is-collapsed {
                display: none !important;
            }

            #btn-pickup-filters-toggle.is-collapsed {
                background: transparent !important;
                color: #008080 !important;
            }

            /* Hide column picker on mobile — toolbar toggle controls filter visibility */
            .pickup-filters-fields .list-dense-filter-controls,
            .pickup-filters-fields .pickup-filter-controls {
                display: none !important;
            }

            .pickup-filters-fields > div {
                width: 100% !important;
                max-width: 100% !important;
            }

            .pickup-filters-fields .list-dense-filter-row {
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
                max-height: none !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .dataTables_wrapper .dataTables_paginate {
                justify-content: center;
            }
        }

        @media (min-width: 992px) {
            .pickup-filters-toolbar {
                display: none !important;
            }
            .pickup-filters-fixed {
                overflow: visible;
                z-index: 120;
            }
            .pickup-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.pickup-filters-collapsed .pickup-filters-fields {
                display: flex !important;
            }
            .pickup-filters-fields .list-dense-filter-shell {
                align-items: flex-start;
            }
            .pickup-filters-fields .list-dense-filter-controls {
                align-self: flex-start;
                position: relative;
                z-index: 51;
            }
            .pickup-filters-fields .list-dense-filter-fields {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                overflow: visible !important;
            }
            .pickup-filters-fields .list-dense-filter-controls .mc-column-picker {
                position: relative;
                z-index: 52;
            }
            .pickup-filters-fields .list-dense-filter-controls .mc-column-picker__panel {
                z-index: 1300;
                isolation: isolate;
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

        /* Pagination look: partials/list-pagination-footer-styles */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            float: none !important;
            width: 100%;
        }
        .dataTables_wrapper {
            padding-bottom: 0 !important;
        }
    </style>
    <x-lists.multiselect-assets />
@endsection

@section('content')
<script>
    document.documentElement.classList.add('pickup-list-page');
    document.body.classList.add('pickup-list-page');
</script>
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
    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])
                                        <div class="card pickup-list-card">
                                            <div class="card-block">
                                                <x-lists.page-header
                                                    title="Pick up work list"
                                                    subtitle="Plan and track stock pickups"
                                                    icon="ti-truck"
                                                    :count="$crrs->total()"
                                                    countLabel="pickups"
                                                />
                                                <div class="pickup-filters-fixed">
                                                <div class="pickup-filters-toolbar">
                                                    <button type="button" id="btn-pickup-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="pickup-filters-toggle-label">Hide filters</span>
                                                    </button>
                                                </div>
                                                <div class="d-flex pt-2 pickup-filters-fields list-dense-filter-bar">
                                                    <div class="list-dense-filter-shell" style="width: 100%;">
                                                        <div class="list-dense-filter-controls pickup-filter-controls">
                                                            <select id="filter-multiselect" multiple="multiple" data-storage-key="pickup-list-filters-v2">
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
                                                        <div class="list-dense-filter-fields">
                                                        <div class="row custom-row filter-row list-dense-filter-row">
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
                                                                    <i class="ti-calendar filter-date-icon" aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Pick-up-date" class="custom-col" style="flex: 0 0 225px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Pick up date</span>
                                                                    <input type="text" id="filter-pickup-date" class="form-control filter-input date-range-filter" placeholder="Select range">
                                                                    <i class="ti-calendar filter-date-icon" aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Deadline-warehouse" class="custom-col" style="flex: 0 0 320px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Deadline warehouse</span>
                                                                    <input type="text" id="filter-deadline-warehouse" class="form-control filter-input date-range-filter" placeholder="Select range">
                                                                    <i class="ti-calendar filter-date-icon" aria-hidden="true"></i>
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
                                                            <div class="custom-col" style="flex: 0 0 auto;">
                                                                <x-lists.clear-filters id="clear-pickup-filters" />
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>{{-- .pickup-filters-fixed --}}

                                                <div class="pickup-table-area">
                                                <div class="dt-responsive table-responsive">
                                                    <x-lists.ajax-table
                                                        table-id="offices-table"
                                                        table-class="office-table"
                                                        :paginator="null"
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
                                                </div>{{-- .pickup-table-area --}}
                                                <div id="pickup-pagination" class="pagination-sticky-footer">
                                                    @include('partials.list-pagination-footer-inner', ['paginator' => $crrs])
                                                </div>
                                            </div>
                                        </div>
    @include('layouts.partials.pcoded-shell-end')


    <!-- date-range-picker js -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('pickup-list-page');
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

            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                includeResetOption: true,
                resetText: 'Clear all',
                storageKey: 'pickup-list-filters-v2',
                onChange: function () {
                    toggleFilterVisibility();
                },
                onSelectAll: function () {
                    toggleFilterVisibility();
                },
                onDeselectAll: function () {
                    toggleFilterVisibility();
                }
            });

            $('#filter-multiselect').multiselect('selectAll', false);
            if ($('#filter-multiselect option:selected').length === 0) {
                $('#filter-multiselect option').prop('selected', true);
                $('#filter-multiselect').multiselect('updateButtonText');
            }

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
                $('.pickup-filter-controls').hide();
                var $panel = $('#filter-multiselect').data('mcColumnPickerPanel');
                if ($panel && $panel.length) {
                    $panel.removeClass('is-open');
                }
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

                if (selectedValues.length === 0) {
                    allFilters.forEach(function(filter) {
                        $('#' + filter.id).hide();
                    });
                    return;
                }

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).css('display', '');
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

            var pickupTable = null;

            // Bind before DataTable so Hide/Show filters works even if DT init fails
            $('#btn-pickup-filters-toggle').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $fields = $('.pickup-filters-fields');
                var collapsed = !$('body').hasClass('pickup-filters-collapsed');
                $('body').toggleClass('pickup-filters-collapsed', collapsed);
                $fields.toggleClass('is-collapsed', collapsed);
                $(this).toggleClass('is-collapsed', collapsed);
                $(this).find('.pickup-filters-toggle-label').text(collapsed ? 'Show filters' : 'Hide filters');
                if (!collapsed) {
                    ensureMobileFiltersVisible();
                }
                setTimeout(adjustPickupTableLayout, 50);
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

            pickupTable = $('#offices-table').DataTable({
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

            function getPickupTableScrollHeight() {
                var $tableArea = $('.pickup-table-area');
                var $scroll = $('.pickup-table-area .table-scroll-wrapper');
                if (!$tableArea.length) {
                    return 180;
                }

                // Prefer flex-allocated area height; keep pagination in view.
                var areaHeight = $tableArea.innerHeight() || 0;
                var paginationHeight = $('#pickup-pagination').outerHeight() || 52;

                if (areaHeight >= 120) {
                    return Math.max(120, areaHeight - 2);
                }

                // Fallback when flex height is not ready yet
                var topOffset = $tableArea.offset() ? $tableArea.offset().top : 220;
                var available = window.innerHeight - topOffset - paginationHeight - 8;
                return Math.max(120, available);
            }

            function adjustPickupTableLayout() {
                var height = getPickupTableScrollHeight();
                var $scroll = $('.pickup-table-area .table-scroll-wrapper');
                $scroll.css({
                    height: height + 'px',
                    maxHeight: height + 'px',
                    flex: '1 1 auto',
                    minHeight: '0'
                });

                // Keep card pinned to page-body (absolute inset)
                $('.pickup-list-card').css({
                    position: 'absolute',
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                    height: 'auto',
                    maxHeight: 'none'
                });
                $('.page-body').css({ position: 'relative' });

                if (pickupTable) {
                    pickupTable.columns.adjust();
                }
            }

            $(window).on('resize', adjustPickupTableLayout);
            setTimeout(adjustPickupTableLayout, 50);
            setTimeout(adjustPickupTableLayout, 250);
            setTimeout(adjustPickupTableLayout, 600);

            window.pickupListFilters = bindAjaxListFilters({
                tableSelector: '#offices-table',
                paginationSelector: '#pickup-pagination',
                indexUrl: @json(route('pickup-work-list')),
                existingTable: pickupTable,
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
                    adjustPickupTableLayout();
                }
            });
        });
    </script>
@endpush
