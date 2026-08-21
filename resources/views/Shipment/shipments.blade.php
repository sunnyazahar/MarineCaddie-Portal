@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <style>
        /* High Density Table Styles */
        .card .card-block table tr {
            padding-bottom: 5px;
        }
        #offices-table,
        #offices-table.dataTable {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            border-collapse: unset !important;
            border-spacing: 0 !important;
        }
        #offices-table > thead {
            display: grid !important;
            grid-template-columns:
                minmax(110px, 1.1fr)
                minmax(130px, 1.4fr)
                minmax(70px, 0.9fr)
                minmax(100px, 1.1fr)
                minmax(80px, 1fr)
                minmax(140px, 1.5fr)
                minmax(70px, 0.7fr)
                minmax(70px, 0.8fr)
                minmax(80px, 0.8fr)
                minmax(90px, 0.9fr)
                minmax(80px, 0.9fr);
            position: sticky !important;
            top: 0 !important;
            z-index: 6 !important;
            background: #f4f7fb !important;
            margin: 0 !important;
            padding: 0 !important;
            border-bottom: 1px dotted #e0e0e0;
        }
        #offices-table > thead tr {
            display: contents !important;
        }
        #offices-table > tbody {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        #offices-table > tbody tr {
            display: grid !important;
            grid-template-columns:
                minmax(110px, 1.1fr)
                minmax(130px, 1.4fr)
                minmax(70px, 0.9fr)
                minmax(100px, 1.1fr)
                minmax(80px, 1fr)
                minmax(140px, 1.5fr)
                minmax(70px, 0.7fr)
                minmax(70px, 0.8fr)
                minmax(80px, 0.8fr)
                minmax(90px, 0.9fr)
                minmax(80px, 0.9fr);
            margin: 0 !important;
            align-items: center;
            border-bottom: 1px dotted #e0e0e0;
        }
        #offices-table thead th {
            padding: 10px 5px !important;
            display: flex !important;
            align-items: center;
            box-sizing: border-box !important;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0 !important;
            border: none !important;
            line-height: 1.2 !important;
            font-size: 13px !important;
            min-height: 0 !important;
        }
         #offices-table tbody td {
            display: flex !important;
            align-items: center;
            box-sizing: border-box !important;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0 !important;
            border: none !important;
            padding: 4px 8px !important;
            line-height: 1.2 !important;
            font-size: 13px !important;
            min-height: 0 !important;
        }
        #offices-table thead th {
            position: static !important;
            background: #f4f7fb !important;
            color: #374151;
            font-weight: 600;
            text-transform: none;
            box-shadow: none;
        }
        #offices-table tbody td {
            color: #1f2937;
            background: #fff;
            min-width: 0;
        }
        #offices-table .cell-ellipsis {
            display: block;
            min-width: 0;
            flex: 1 1 0%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
        #offices-table tbody td .label {
            margin: 0 !important;
            padding: 2px 8px !important;
            font-size: 11px !important;
            line-height: 1.2 !important;
        }
        #offices-table td[colspan],
        #offices-table td.dataTables_empty {
            grid-column: 1 / -1;
        }
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc:after {
            display: none !important;
        }
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc,
        table.dataTable thead .sorting_asc_disabled,
        table.dataTable thead .sorting_desc_disabled {
            background-image: none !important;
            padding-right: 8px !important;
        }
        #offices-table td.consignee-cell {
            overflow: hidden;
        }
        #offices-table .consignee-row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            max-width: 220px;
            min-width: 0;
            overflow: hidden;
            white-space: nowrap !important;
        }
        #offices-table .consignee-hub-agent {
            font-weight: 600;
            font-size: 12px;
            color: #05354B;
        }
        #offices-table .consignee-icon-slot {
            display: flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 16px !important;
            width: 16px !important;
            min-width: 16px !important;
            height: 12px;
            margin-right: 6px;
        }
        #offices-table .consignee-icon-slot i {
            font-size: 12px;
            color: #05354B;
            line-height: 1;
        }
        #offices-table .consignee-hub-agent-text {
            display: block !important;
            flex: 1 1 auto;
            min-width: 0 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            line-height: 1.2;
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
            margin-top: 3px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-col {
            padding: 2px;
            margin-bottom: 0;
        }
        .filter-row {
            margin-bottom: 4px !important;
        }
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            padding: 0;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
            width: 100%;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #ffffff;
            margin-bottom: 0;
            padding: 0 10px;
            white-space: nowrap;
            font-weight: 700;
            border-right: 1px solid #5a7fa0;
            height: 100%;
            display: flex;
            align-items: center;
            background: #6992b5;
            background-color: #6992b5;
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
        .filter-group .filter-date-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 28px;
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            opacity: 0.85;
        }
        .filter-group .filter-date-icon:hover {
            color: #008080;
            opacity: 1;
        }
        #col-Creation-date .filter-group {
            padding-right: 2px;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
        }
        #col-Status .select2-container--default .select2-selection--single,
        #col-Status .select2-container--default.select2-container--focus .select2-selection--single,
        #col-Status .select2-container--default.select2-container--open .select2-selection--single {
            background-color: transparent !important;
        }
        #col-Status .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #1e293b !important;
        }
        #col-Status .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent !important;
        }
        #col-Status .select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #64748b transparent !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
            font-size: 11px !important;
            color: #1e293b !important;
            line-height: 1.25 !important;
        }
        .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered,
        .filter-group .select2-container--default .select2-search--inline .select2-search__field {
            font-size: 11px !important;
            padding-left: 5px !important;
        }
        .filter-group .select2-container--default .select2-search--inline .select2-search__field::placeholder {
            font-size: 11px !important;
            color: #94a3b8 !important;
        }
        .clear-filters {
            font-size: 11px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            height: 32px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-weight: 500;
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
            height: 32px;
            width: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #008080;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            border-radius: 4px;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus {
            background-color: #f8fafc;
            color: #006666;
            border-color: #cbd5e1;
        }
        
        /* Shipments list: lock page scroll; only table body scrolls */
        body.shipments-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.shipments-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.shipments-list-page .pcoded-inner-content,
        body.shipments-list-page .main-body,
        body.shipments-list-page .page-wrapper,
        body.shipments-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .shipments-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 64px);
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
        }
        .shipments-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .shipments-filters-fixed {
            flex-shrink: 0;
            background: #fff;
            position: relative;
            z-index: 40;
            padding-bottom: 6px;
        }
        .shipments-filters-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: absolute;
            top: 6px;
            right: 0;
            z-index: 50;
        }
        .shipments-filters-toolbar #btn-shipments-filters-toggle {
            display: none;
        }
        .shipments-filters-fields {
            width: 100%;
            padding-right: 170px;
            box-sizing: border-box;
        }
        .shipments-filters-fields-main {
            width: 100%;
        }
        .shipments-create-desktop {
            display: none;
        }

        @media (max-width: 991.98px) {
            body.shipments-list-page {
                overflow: hidden !important;
                height: 100vh;
            }
            body.shipments-list-page .pcoded-inner-content,
            body.shipments-list-page .main-body,
            body.shipments-list-page .page-wrapper,
            body.shipments-list-page .page-body {
                height: 100%;
                overflow: hidden !important;
            }
            .shipments-list-card {
                height: calc(100vh - 64px) !important;
                margin: 0 !important;
            }
            .shipments-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: static;
                gap: 8px;
                flex-wrap: wrap;
                padding: 4px 0 8px;
            }
            .shipments-filters-fields {
                padding-right: 0;
            }
            .shipments-filters-toolbar #btn-shipments-filters-toggle {
                display: inline-flex;
            }
            .shipments-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            /* Start collapsed on mobile so the table is visible */
            .shipments-filters-fields {
                display: none !important;
                flex-direction: column;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                margin-bottom: 4px;
                border-bottom: 1px solid #eef2f7;
            }
            body.shipments-filters-open .shipments-filters-fields {
                display: flex !important;
            }
            #btn-shipments-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }
            .shipments-filters-fields .mr-2,
            .shipments-filters-fields .btn-filter-toggle {
                display: none !important;
            }
            .shipments-filters-fields .filter-row {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100%;
            }
            .shipments-filters-fields .custom-col,
            .shipments-filters-fields .custom-col[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
            }
            .shipments-create-desktop {
                display: none !important;
            }
            .shipments-table-area {
                flex: 1 1 auto;
                min-height: 45vh;
            }
            .shipments-table-area .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (min-width: 992px) {
            .shipments-filters-toolbar {
                display: flex !important;
            }
            .shipments-filters-toolbar #btn-shipments-filters-toggle {
                display: none !important;
            }
            .shipments-filters-fields {
                display: block !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.shipments-filters-open .shipments-filters-fields {
                display: block !important;
            }
        }

        .shipments-table-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .shipments-table-area .dataTables_wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100%;
            padding-bottom: 0 !important;
        }
        .shipments-table-area .table-scroll-wrapper {
            flex: 1;
            min-height: 0;
            overflow: auto !important;
            display: block;
            width: 100%;
            position: relative;
        }

        /* Pagination look: partials/list-pagination-footer-styles (in-flow under table) */
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
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 0 !important;
        }
        .main-body .page-wrapper {
            padding: 0 !important;
        }

</style>
    @include('partials.searchable-filter-multiselect-styles')
    <x-lists.multiselect-assets />
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
    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])
                                        <!-- Base Style - Compact start -->
                                        <div class="card shipments-list-card">
                                            <div class="card-block">
                                                <x-lists.page-header
                                                    title="Shipments"
                                                    subtitle="Search, filter, and manage active shipments"
                                                    icon="icofont icofont-ship"
                                                    :count="$shipments->total()"
                                                    countLabel="shipments"
                                                >
                                                    <x-slot:actions>
                                                        <a href="{{ route('create-shipment') }}" class="btn btn-teal btn-sm d-none d-lg-inline-flex">Create shipment</a>
                                                    </x-slot:actions>
                                                </x-lists.page-header>
                                                <div class="shipments-filters-fixed">
                                                <div class="shipments-filters-toolbar">
                                                    <button type="button" id="btn-shipments-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="shipments-filters-toggle-label">Show filters</span>
                                                    </button>
                                                    <div class="shipments-filters-toolbar-actions">
                                                        <a href="{{ route('create-shipment') }}" class="btn btn-teal btn-sm">Create shipment</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 shipments-filters-fields">
                                                    <div class="shipments-filters-fields-main">
                                                        <div class="row no-gutters filter-row">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Customer" selected>Customer</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Shipment no" selected>Shipment no</option>
                                                                    <option value="Service reference number" selected>Service reference number</option>
                                                                    <option value="PO number" selected>PO number</option>
                                                                    <option value="Departure hub" selected>Departure hub</option>
                                                                    <option value="Consignee" selected>Consignee</option>
                                                                    <option value="Port of destination" selected>Port of destination</option>
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Created by" selected>Created by</option>
                                                                    <option value="Office" selected>Office</option>
                                                                    <option value="Creation date" selected>Creation date</option>
                                                                    <option value="Service" selected>Service</option>
                                                                    <option value="Status" selected>Status</option>
                                                                </select>
                                                            </div>
                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Service-reference-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service reference</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-PO-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">PO number</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="full PO no.">
                                                                </div>
                                                            </div>
                                                            <div id="col-Departure-hub" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Departure port code</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($departureOptions as $departure)
                                                                            <option value="{{ $departure }}">{{ $departure }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Row 2 -->
                                                        <div class="row no-gutters filter-row">
                                                            
                                                            <div id="col-Consignee" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Consignee</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager->name }}">{{ $manager->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Created-by" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Created by</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->name }}">{{ $creator->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Office" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Office</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($offices as $office)
                                                                            <option value="{{ $office->office_name }}">{{ $office->office_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Creation-date" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Creation date</span>
                                                                    <input type="text" id="filter-creation-date" class="form-control filter-input datepicker" placeholder="dd.mm.yyyy" autocomplete="off">
                                                                    <i class="ti-calendar filter-date-icon" title="Pick date" aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                            <div id="col-Service" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($services as $service)
                                                                            <option value="{{ $service }}">{{ $service }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Status" class="custom-col" style="flex: 0 0 180px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Status</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($statuses as $status)
                                                                            <option value="{{ $status }}">{{ $status }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <a href="#" id="clear-shipments-filters" class="clear-filters">Clear filters</a>
                                                        </div>
                                                </div>
                                                <div class="text-right shipments-create-desktop">
                                                     <!-- <button class="btn btn-outline-teal"><i class="ti-download"></i> Export</button> -->
                                                     <a href="{{ route('create-shipment') }}" class="btn btn-teal ml-2">Create shipment</a>
                                                </div>
                                            </div>
                                                </div>

                                                <div class="shipments-table-area">
                                                    <table id="offices-table"
                                                        class="office-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment No</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Service Reference</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>Deadline</th>
                                                                <th>PA Reminder</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @include('Shipment.partials.rows')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="shipments-pagination" class="pagination-sticky-footer">
                                                    @include('partials.list-pagination-footer-inner', ['paginator' => $shipments])
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('shipments-list-page');

            var shipmentsIndexUrl = @json(route('shipments'));
            var table = null;
            var searchTimer = null;
            var filtersReady = false;
            var requestToken = 0;
            var suppressFilterLoad = false;

            function shouldLoadFilters() {
                return filtersReady && !suppressFilterLoad;
            }

            function loadShipmentsOnFilterChange() {
                if (shouldLoadFilters()) {
                    loadShipments(1);
                }
            }

            initializeSearchableFilterMultiselect('.searchable-filter-multiselect', {
                onChange: loadShipmentsOnFilterChange,
                onSelectAll: loadShipmentsOnFilterChange,
                onDeselectAll: loadShipmentsOnFilterChange
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: false,
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

            $('#filter-multiselect').multiselect('selectAll', false);
            $('#filter-multiselect').multiselect('updateButtonText');

            var shipmentFilterIds = [
                'col-Customer',
                'col-Vessel',
                'col-Shipment-no',
                'col-Service-reference-number',
                'col-PO-number',
                'col-Departure-hub',
                'col-Consignee',
                'col-Port-of-destination',
                'col-Account-manager',
                'col-Created-by',
                'col-Office',
                'col-Creation-date',
                'col-Service',
                'col-Status'
            ];

            function isShipmentsMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureShipmentsMobileFiltersVisible() {
                if (!isShipmentsMobile()) {
                    return;
                }
                shipmentFilterIds.forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.shipments-filters-fields .mr-2').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                if (isShipmentsMobile()) {
                    ensureShipmentsMobileFiltersVisible();
                    if (table && table.columns) {
                        setTimeout(adjustShipmentsTableLayout, 50);
                    }
                    return;
                }

                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Service reference number', id: 'col-Service-reference-number'},
                    {val: 'PO number', id: 'col-PO-number'},
                    {val: 'Departure hub', id: 'col-Departure-hub'},
                    {val: 'Consignee', id: 'col-Consignee'},
                    {val: 'Port of destination', id: 'col-Port-of-destination'},
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Created by', id: 'col-Created-by'},
                    {val: 'Office', id: 'col-Office'},
                    {val: 'Creation date', id: 'col-Creation-date'},
                    {val: 'Service', id: 'col-Service'},
                    {val: 'Status', id: 'col-Status'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });

                if (table && table.columns) {
                    setTimeout(adjustShipmentsTableLayout, 50);
                }
            }

            toggleFilterVisibility();
            ensureShipmentsMobileFiltersVisible();

            function initShipmentsTable() {
                if ($.fn.DataTable.isDataTable('#offices-table')) {
                    return $('#offices-table').DataTable();
                }

                return $('#offices-table').DataTable({
                    "dom": '<"table-scroll-wrapper"rt>',
                    "lengthChange": false,
                    "paging": false,
                    "info": false,
                    "responsive": false,
                    "searching": false,
                    "ordering": true,
                    "order": [],
                    "autoWidth": false,
                    "language": {
                        "emptyTable": "No shipments found."
                    }
                });
            }

            table = initShipmentsTable();

            function getShipmentsTableScrollHeight() {
                var isMobile = isShipmentsMobile();
                var $tableArea = $('.shipments-table-area');
                var areaHeight = $tableArea.length ? $tableArea.innerHeight() : 0;
                // Pagination is in-flow sibling under table-area — use full area height.
                var available = areaHeight - 2;

                if (isMobile) {
                    var paginationHeight = $('#shipments-pagination').outerHeight() || 52;
                    var topOffset = $tableArea.length && $tableArea.offset()
                        ? $tableArea.offset().top
                        : 160;
                    available = window.innerHeight - topOffset - paginationHeight - 8;
                    return Math.max(260, available);
                }

                if (available < 180) {
                    var topOffset = $tableArea.length ? $tableArea.offset().top : 220;
                    var paginationHeight = $('#shipments-pagination').outerHeight() || 52;
                    available = window.innerHeight - topOffset - paginationHeight - 4;
                }

                return Math.max(180, available);
            }

            function adjustShipmentsTableLayout() {
                var height = getShipmentsTableScrollHeight();
                $('.shipments-table-area .table-scroll-wrapper').css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });
            }

            $('#btn-shipments-filters-toggle').on('click', function () {
                $('body').toggleClass('shipments-filters-open');
                var isOpen = $('body').hasClass('shipments-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.shipments-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                if (isOpen) {
                    ensureShipmentsMobileFiltersVisible();
                }
                setTimeout(adjustShipmentsTableLayout, 50);
                setTimeout(adjustShipmentsTableLayout, 200);
            });

            $(window).on('resize', function() {
                toggleFilterVisibility();
                ensureShipmentsMobileFiltersVisible();
                adjustShipmentsTableLayout();
            });

            setTimeout(adjustShipmentsTableLayout, 100);
            setTimeout(adjustShipmentsTableLayout, 400);

            table.on('draw', function() {
                adjustShipmentsTableLayout();
            });

            function creationDateForQuery() {
                var value = $.trim($('#filter-creation-date').val() || '');
                if (!value) {
                    return '';
                }
                var match = value.match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
                if (match) {
                    return match[3] + '-' + match[2] + '-' + match[1];
                }
                return value;
            }

            function currentFilterParams(page) {
                return {
                    customer: $('#col-Customer select').val() || [],
                    vessel: $('#col-Vessel select').val() || [],
                    shipment_number: $.trim($('#col-Shipment-no input').val() || ''),
                    service_reference: $.trim($('#col-Service-reference-number input').val() || ''),
                    po_number: $.trim($('#col-PO-number input').val() || ''),
                    departure_port_code: $('#col-Departure-hub select').val() || [],
                    consignee: $.trim($('#col-Consignee input').val() || ''),
                    destination: $.trim($('#col-Port-of-destination input').val() || ''),
                    account_manager: $('#col-Account-manager select').val() || [],
                    created_by: $('#col-Created-by select').val() || [],
                    office: $('#col-Office select').val() || [],
                    creation_date: creationDateForQuery(),
                    service: $('#col-Service select').val() || [],
                    status: $('#col-Status select').val() || [],
                    page: page || 1
                };
            }

            function replaceShipmentRows(html, paginationHtml, total) {
                table = initShipmentsTable();
                table.clear();

                var $rows = $('<table><tbody>' + html + '</tbody></table>').find('tr').filter(function () {
                    return $(this).find('td[colspan]').length === 0;
                });

                if ($rows.length) {
                    table.rows.add($rows);
                }

                table.draw(false);
                $('#shipments-pagination').html(paginationHtml || '');
                if (typeof total === 'number') {
                    var $count = $('.list-page-header-count strong');
                    if ($count.length) {
                        $count.text(total.toLocaleString());
                    }
                }
                adjustShipmentsTableLayout();
            }

            function loadShipments(page) {
                var params = currentFilterParams(page);
                var token = ++requestToken;

                $.ajax({
                    url: shipmentsIndexUrl,
                    method: 'GET',
                    data: params,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).done(function (response) {
                    if (token !== requestToken) {
                        return;
                    }

                    replaceShipmentRows(response.html, response.pagination, response.total);
                });
            }

            function resetShipmentFilterFields() {
                $('#col-Shipment-no input, #col-Service-reference-number input, #col-PO-number input, #col-Consignee input, #col-Port-of-destination input, #filter-creation-date').val('');
                if ($('#filter-creation-date').hasClass('hasDatepicker')) {
                    $('#filter-creation-date').datepicker('setDate', null);
                }
                $('.searchable-filter-multiselect').each(function () {
                    var $select = $(this);
                    $select.find('option').prop('selected', false);
                    $select.val([]);
                    $select.multiselect('clearSelection');
                    $select.closest('.multiselect-native-select').find('.multiselect-search').val('');
                    $select.closest('.multiselect-native-select').find('li.multiselect-filter-hidden')
                        .removeClass('multiselect-filter-hidden')
                        .show();
                });
            }

            $('#filter-creation-date').datepicker({
                dateFormat: 'dd.mm.yy',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-10:c+2',
                onSelect: function () {
                    loadShipments(1);
                }
            });

            $(document).on('click', '#col-Creation-date .filter-date-icon', function () {
                $('#filter-creation-date').datepicker('show');
            });

            $('#col-Shipment-no input, #col-Service-reference-number input, #col-PO-number input, #col-Consignee input, #col-Port-of-destination input').on('input keyup', function (e) {
                if (e.type === 'keyup' && e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    loadShipments(1);
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    loadShipments(1);
                }, 200);
            });

            $('#filter-creation-date').on('change', function () {
                loadShipments(1);
            });

            $('#shipments-pagination').on('click', 'a', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    return;
                }

                e.preventDefault();
                var page = new URL(href, window.location.origin).searchParams.get('page') || 1;
                loadShipments(page);
            });

            $(document).on('click', '#clear-shipments-filters', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimer);
                suppressFilterLoad = true;
                resetShipmentFilterFields();
                suppressFilterLoad = false;
                loadShipments(1);
                return false;
            });

            $(document).on('click', '.shipments-filters-fields .multiselect-reset a', function () {
                if (!shouldLoadFilters()) {
                    return;
                }

                setTimeout(function () {
                    loadShipments(1);
                }, 0);
            });

            setTimeout(function () {
                filtersReady = true;
            }, 200);
        });
    </script>
@endpush
