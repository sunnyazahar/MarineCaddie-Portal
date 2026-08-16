@extends('layouts.app')

@section('styles')
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
        /* High Density Table Styles */
        #offices-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            min-width: 0;
        }
        @media (min-width: 1200px) {
            #offices-table {
                min-width: 1400px;
            }
        }
        .table-scroll-wrapper #offices-table,
        .dataTables_wrapper #offices-table {
            min-width: 1100px;
        }
        #offices-table thead th {
            position: relative !important;
            top: auto !important;
            z-index: auto !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: none;
        }
        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
        }
        #offices-table .consignee-row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            width: max-content;
            min-width: max-content;
            white-space: nowrap !important;
        }
        #offices-table .consignee-hub-agent {
            font-weight: 600;
            font-size: 12px;
            color: #05354B;
        }
        #offices-table .consignee-hub-icon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 14px !important;
            width: 14px !important;
            min-width: 14px !important;
            margin: 0 6px 0 0 !important;
            font-size: 12px;
            color: #05354B;
            line-height: 1;
        }
        #offices-table .consignee-hub-icon-spacer {
            visibility: hidden;
        }
        #offices-table .consignee-hub-agent-text {
            display: inline-block !important;
            white-space: nowrap !important;
            line-height: 1.2;
        }
        #offices-table th, #offices-table td {
            white-space: nowrap !important; 
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
            line-height: 30px !important;
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
            line-height: 28px !important;
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
            height: calc(100vh - 104px);
            margin-bottom: 0 !important;
            overflow: hidden;
        }
        .shipments-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding-bottom: 8px !important;
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
                margin-top: 8px !important;
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
            .pagination-sticky-footer {
                padding: 8px 12px !important;
                height: auto !important;
                min-height: 48px;
                justify-content: center !important;
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
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
            position: relative;
        }
        .shipments-table-area .dataTables_scroll {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100% !important;
            width: 100%;
        }
        .shipments-table-area .dataTables_scrollHead {
            flex-shrink: 0 !important;
            position: relative !important;
            overflow: hidden !important;
            background: #fdfdfd;
            border-bottom: 2px solid #dee2e6;
            z-index: 5;
        }
        .shipments-table-area .dataTables_scrollHeadInner,
        .shipments-table-area .dataTables_scrollHead table {
            width: 100% !important;
        }
        .shipments-table-area .dataTables_scrollBody {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-x: auto !important;
            overflow-y: auto !important;
            margin-top: -10px !important;
        }

        .pagination-sticky-footer {
            position: fixed !important;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 1040;
            margin: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            float: none !important;
            width: 100%;
        }
        body.shipments-list-page {
            padding-bottom: 48px;
        }
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Custom Datepicker Styles */
        .ui-datepicker {
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 0;
            font-family: inherit;
            font-size: 11px;
            z-index: 9999 !important;
            width: 250px;
            border-radius: 6px;
            overflow: hidden;
        }
        .ui-datepicker-header {
            background: #008080;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ui-datepicker-title {
            font-weight: 700;
            text-align: center;
            width: 100%;
        }
        .ui-datepicker-prev, .ui-datepicker-next {
            cursor: pointer;
            color: #fff;
            background: rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background: rgba(255,255,255,0.2);
        }
        .ui-datepicker-title select {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 3px;
            padding: 2px 4px;
            margin: 0 2px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }
        .ui-datepicker-title select option {
            background: #fff;
            color: #374151;
            font-weight: normal;
        }
        .ui-datepicker-calendar {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        .ui-datepicker-calendar th {
            color: #6b7280;
            font-weight: 600;
            padding: 8px 0;
            text-align: center;
        }
        .ui-datepicker-calendar td {
            padding: 2px;
            text-align: center;
        }
        .ui-datepicker-calendar td a {
            display: block;
            padding: 6px;
            text-decoration: none;
            color: #374151;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .ui-datepicker-calendar td a:hover {
            background: #f0fdfa;
            color: #008080;
        }
        .ui-datepicker-calendar .ui-state-active {
            background: #008080 !important;
            color: #fff !important;
            font-weight: 700;
        }
        .ui-datepicker-calendar .ui-datepicker-today a {
            border: 1px solid #008080;
            color: #008080;
        }
    </style>
    @include('partials.searchable-filter-multiselect-styles')
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
                                        <div class="card shipments-list-card mt-4">
                                            <div class="card-block">
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
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-Service-reference-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service reference</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-PO-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">PO number</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
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
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
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
                                                                    <div class="input-group p-0 m-0" style="border: none;">
                                                                        <input type="date" class="form-control filter-input datepicker" placeholder="dd/mm/yyyy" style="border: none; background: transparent;">
                                                                    </div>
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
                                                            <a class="clear-filters">Clear filters</a>
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
                                                            @forelse ($shipments as $shipment)
                                                            @php
                                                                $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
                                                                $consigneeType = explode(':', (string) $shipment->consignee, 2)[0];
                                                                $customerNames = $shipment->customerNamesFromVessels($vesselCustomerMap ?? []);
                                                                $customerDisplay = $shipment->formatNamesDisplay($customerNames);
                                                                $customerDisplayShort = $shipment->formatNamesDisplayShort($customerNames);
                                                                $serviceReferenceDisplay = $shipment->service_reference_display;
                                                            @endphp
                                                            <tr
                                                                data-customers="{{ $customerNames->implode(',') }}"
                                                                data-vessels="{{ $shipment->vessel_names->implode(',') }}"
                                                                data-shipment-number="{{ $shipment->shipment_number }}"
                                                                data-service-reference="{{ $serviceReferenceDisplay }}"
                                                                data-consignee="{{ $consigneeDisplay }}"
                                                                data-departure-port-code="{{ $shipment->departure_port_code ?? '' }}"
                                                                data-destination="{{ $shipment->destination_display }}"
                                                                data-service="{{ $shipment->service ?? '' }}"
                                                                data-po-numbers="{{ $shipment->po_numbers_display }}"
                                                                data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
                                                                data-created-by="{{ $shipment->creator?->name ?? '' }}"
                                                                data-office="{{ $shipment->accountManager?->office?->office_name ?? '' }}"
                                                                data-creation-date="{{ $shipment->created_at?->format('Y-m-d') ?? '' }}"
                                                                data-status="{{ $shipment->status ?? '' }}"
                                                            >
                                                                <td>
                                                                    <a href="{{ route('shipments.edit', $shipment->id) }}" class="text-primary">{{ $shipment->shipment_number }}</a>
                                                                    @if ($shipment->hasOpenIrregularities())
                                                                        <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($customerNames->count() > 2)
                                                                        <span title="{{ $customerDisplay }}" style="cursor: help;">{{ $customerDisplayShort }}</span>
                                                                    @else
                                                                        {{ $customerDisplay }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($shipment->vessel_names->count() > 2)
                                                                        <span title="{{ $shipment->vessel_display }}" style="cursor: help;">{{ $shipment->vessel_display_short }}</span>
                                                                    @else
                                                                        {{ $shipment->vessel_display }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $shipment->service ?? '—' }}</td>
                                                                <td>
                                                                    @if ($shipment->service_reference_values->count() > 2)
                                                                        <span title="{{ $shipment->service_reference_display }}" style="cursor: help;">{{ $shipment->service_reference_display_short }}</span>
                                                                    @else
                                                                        {{ $shipment->service_reference_display }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($consigneeType === 'hub')
                                                                        <span class="consignee-row consignee-hub-agent"><i class="ti-home consignee-hub-icon" title="Hub"></i><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
                                                                    @elseif ($consigneeType === 'agent')
                                                                        <span class="consignee-row consignee-hub-agent"><i class="ti-user consignee-hub-icon" title="Agent"></i><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
                                                                    @else
                                                                        <span class="consignee-row"><span class="consignee-hub-icon consignee-hub-icon-spacer"></span><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $shipment->departure_port_code ?: '—' }}</td>
                                                                <td>{{ $shipment->destination_display }}</td>
                                                                <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>{{ $shipment->pre_alert_reminder?->format('d.m.Y') ?? '—' }}</td>
                                                                <td><label class="{{ $shipment->statusBadgeClass() }}">{{ $shipment->status }}</label></td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="11" class="text-center py-4 text-muted">No shipments found.</td>
                                                            </tr>
                                                            @endforelse
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
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    @include('partials.searchable-filter-multiselect-script')

    <script>
        $(document).ready(function() {
            $('body').addClass('shipments-list-page');

            initializeSearchableFilterMultiselect('.searchable-filter-multiselect');

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
                    if (typeof table !== 'undefined' && table.columns) {
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

                if (typeof table !== 'undefined' && table.columns) {
                    setTimeout(adjustShipmentsTableLayout, 50);
                }
            }

            toggleFilterVisibility();
            ensureShipmentsMobileFiltersVisible();

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "scrollY": '50vh',
                "scrollX": true,
                "scrollCollapse": true,
                "columnDefs": [
                    { "targets": 5, "width": "220px" }
                ]
            });

            function getShipmentsTableScrollHeight() {
                var isMobile = isShipmentsMobile();
                var $tableArea = $('.shipments-table-area');
                var $scrollHead = $('.dataTables_scrollHead');
                var areaHeight = $tableArea.length ? $tableArea.innerHeight() : 0;
                var headHeight = $scrollHead.length ? $scrollHead.outerHeight() : 40;
                var available = areaHeight - headHeight - 2;

                if (isMobile) {
                    var paginationHeight = $('.pagination-sticky-footer').outerHeight() || 48;
                    var topOffset = $tableArea.length && $tableArea.offset()
                        ? $tableArea.offset().top
                        : 160;
                    available = window.innerHeight - topOffset - paginationHeight - 8;
                    return Math.max(260, available);
                }

                if (available < 180) {
                    var topOffset = $scrollHead.length ? $scrollHead.offset().top : 220;
                    var paginationHeight = $('.pagination-sticky-footer').outerHeight() || 48;
                    available = window.innerHeight - topOffset - paginationHeight - 4;
                }

                return Math.max(180, available);
            }

            function adjustShipmentsTableLayout() {
                var height = getShipmentsTableScrollHeight();
                var $scrollBody = $('.dataTables_scrollBody');

                $scrollBody.css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });

                table.columns.adjust();
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

            function rowData($row, key) {
                return String($row.attr('data-' + key) || '');
            }

            function getFilterText(selector) {
                return String($(selector).val() || '').toLowerCase().trim();
            }

            function matchesSelectedValues(selectedValues, rowValue) {
                if (!selectedValues || selectedValues.length === 0) {
                    return true;
                }

                return selectedValues.indexOf(String(rowValue || '')) !== -1;
            }

            function matchesAnySelectedValues(selectedValues, rowValuesString) {
                if (!selectedValues || selectedValues.length === 0) {
                    return true;
                }

                var rowValues = rowValuesString.split(',').map(function(value) {
                    return value.trim();
                }).filter(Boolean);

                return selectedValues.some(function(selectedValue) {
                    return rowValues.indexOf(selectedValue) !== -1;
                });
            }

            function matchesContains(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '').toLowerCase().indexOf(filterValue) !== -1;
            }

            $('#col-Customer select, #col-Vessel select, #col-Shipment-no input, #col-Service-reference-number input, #col-PO-number input, #col-Departure-hub select, #col-Consignee input, #col-Port-of-destination input, #col-Account-manager select, #col-Created-by select, #col-Office select, #col-Creation-date input, #col-Service select, #col-Status select').on('change keyup', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);

                if (!matchesAnySelectedValues($('#col-Customer select').val() || [], rowData($row, 'customers'))) {
                    return false;
                }

                if (!matchesAnySelectedValues($('#col-Vessel select').val() || [], rowData($row, 'vessels'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#col-Shipment-no input'), rowData($row, 'shipment-number'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#col-Service-reference-number input'), rowData($row, 'service-reference'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#col-PO-number input'), rowData($row, 'po-numbers'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#col-Departure-hub select').val() || [], rowData($row, 'departure-port-code'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#col-Consignee input'), rowData($row, 'consignee'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#col-Port-of-destination input'), rowData($row, 'destination'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#col-Account-manager select').val() || [], rowData($row, 'account-manager'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#col-Created-by select').val() || [], rowData($row, 'created-by'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#col-Office select').val() || [], rowData($row, 'office'))) {
                    return false;
                }

                var creationDate = $('#col-Creation-date input').val();
                if (creationDate && rowData($row, 'creation-date') !== creationDate) {
                    return false;
                }

                if (!matchesSelectedValues($('#col-Service select').val() || [], rowData($row, 'service'))) {
                    return false;
                }

                var selectedStatuses = $('#col-Status select').val() || [];
                if (!matchesSelectedValues(selectedStatuses, rowData($row, 'status'))) {
                    return false;
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                clearSearchableFilterMultiselect('.searchable-filter-multiselect');
                $('.filter-input:not(select)').val('').trigger('keyup');
                table.columns().search('').draw();
            });
        });
    </script>
@endsection
