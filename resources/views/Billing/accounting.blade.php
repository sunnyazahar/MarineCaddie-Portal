@extends('layouts.app')

@section('styles')

    <!-- Date-range picker css  -->

    <style>
        /* High Density Table Styles */
        #offices-table, #exports-table, #exrates-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #offices-table thead th, #exports-table thead th, #exrates-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); 
        }
        #offices-table tbody td, #exports-table tbody td, #exrates-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
        }
        #offices-table th, #offices-table td,
        #exports-table th, #exports-table td,
        #exrates-table th, #exrates-table td {
            white-space: nowrap !important; 
        }
        /* ===== Premium Tab Styles ===== */
        .accounting-tabs {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 18px;
            gap: 4px;
            padding: 0;
            list-style: none;
        }
        .accounting-tabs .nav-item {
            position: relative;
        }
        .accounting-tabs .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            padding: 9px 18px;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            transition: color 0.18s, border-color 0.18s, background 0.18s;
            text-transform: none;
            letter-spacing: 0.01em;
            margin-bottom: -2px;
        }
        .accounting-tabs .nav-link i {
            font-size: 13px;
            opacity: 0.7;
            transition: opacity 0.18s;
        }
        .accounting-tabs .nav-link:hover {
            color: #008080;
            background: #f0fafa;
            text-decoration: none;
            border-bottom-color: #99d6d6;
        }
        .accounting-tabs .nav-link:hover i {
            opacity: 1;
        }
        .accounting-tabs .nav-link.active {
            color: #008080;
            border-bottom-color: #008080;
            background: #f0fafa;
            font-weight: 700;
        }
        .accounting-tabs .nav-link.active i {
            opacity: 1;
        }
        .slide { display: none; }

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
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
            font-size: 11px !important;
            color: #1e293b !important;
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
        .filter-group i {
            color: #64748b;
            font-size: 14px;
        }
        .custom-col {
            padding: 2px;
            margin-bottom: 0;
        }
        .btn-filter-icon {
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #008080;
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            padding: 0;
            margin-right: 8px;
        }
        .btn-export-download {
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            height: 32px;
            padding: 0 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            font-size: 16px;
        }
        .btn-reports {
            border: 1px solid #008080;
            color: #008080;
            background: #fff;
            height: 32px;
            padding: 0 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            font-size: 11px;
            font-weight: 500;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            height: 32px;
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            padding: 0 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #fff;
        }
        .checkbox-container input[type="checkbox"] {
            margin-left: 10px;
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #008080;
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
        /* Bootstrap Multiselect adjustments */
        .multiselect-native-select .btn-group {
            width: 32px;
        }
        .multiselect-container {
            font-size: 11px;
            min-width: 200px;
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        .label {
            border-radius: 4px;
            font-size: 100%;
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
            color: #4b5563 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            width: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #4b5563 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
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
        
        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 150px);
            width: 100%;
            position: relative;
        }
        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
        td a {
            color: #008080 !important;
            font-weight: 500;
        }
        .badge-ready-billing {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            display: inline-block;
            border: none;
        }
        .table-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
          .status-paid { background-color: #dbeafe; color: #1e40af; }
        
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
                                        <!-- Base Style - Compact start -->
                                        <div class="card">
                                            <div class="card-block p-1 mt-2">
                                                <div class="container-fluid p-0">
                                                  <div class="aaa">
                                                     <ul class="accounting-tabs nav nav-tabs" id="accountingTabs" role="tablist">
                                                         <li class="nav-item">
                                                             <a class="nav-link active" id="tab-ledger" data-toggle="tab" href="#ledger" role="tab">
                                                                 <i class="ti-bar-chart"></i> Ledger
                                                             </a>
                                                         </li>
                                                         <li class="nav-item">
                                                             <a class="nav-link" id="tab-exports" data-toggle="tab" href="#exports" role="tab">
                                                                 <i class="ti-export"></i> Exports
                                                             </a>
                                                         </li>
                                                         <li class="nav-item">
                                                             <a class="nav-link" id="tab-exrates" data-toggle="tab" href="#exchange-rates" role="tab">
                                                                 <i class="ti-exchange-vertical"></i> Exchange rates
                                                             </a>
                                                         </li>
                                                     </ul>

                                                    <div class="tab-content" style="padding: 0;">
                                                        <div class="tab-pane active" id="ledger" role="tabpanel">
                                                            <div class="row no-gutters filter-row align-items-center mb-0 mt-2">
                                                                
                                                                <div class="custom-col" style="flex: 0 0 250px;">
                                                                    <div class="filter-group" style="height: 30px;">
                                                                        <span class="filter-label" style="background:#fff; border-right:none; color:#008080;">Office</span>
                                                                        <select class="form-control filter-input select2">
                                                                            <option selected>SIN - Marinetrans Singap...</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto ml-2">
                                                                    <a class="clear-filters" style="font-size: 11px; color:#008080;">Clear filters</a>
                                                                </div>
                                                            </div>

                                                            <div style="margin-top: 15px;">
                                                                <table id="offices-table" class="table table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Office</th>
                                                                            <th>Timestamp</th>
                                                                            <th>Journal</th>
                                                                            <th>Transaction type</th>
                                                                            <th>Reference</th>
                                                                            <th class="text-right">Value</th>
                                                                            <th>Currency</th>
                                                                            <th>Booking period</th>
                                                                            <th>Exported</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach([
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:37', 'journal' => 'Sales', 'type' => 'Outgoing aggregated invoice', 'ref' => 'SIN-110459', 'val' => '1 990.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:37', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117877-S', 'val' => '1 187.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:37', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117876-S', 'val' => '803.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:26', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110458', 'val' => '475.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:17', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110457', 'val' => '3 810.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:15', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110456', 'val' => '2 485.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:14', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110455', 'val' => '175.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:14', 'journal' => 'General', 'type' => 'Cost accrual', 'ref' => 'TRU6373316-226', 'val' => '18.35', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:13', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110454', 'val' => '545.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:12', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110453', 'val' => '515.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:12', 'journal' => 'General', 'type' => 'Cost accrual', 'ref' => 'TRU6365477-126', 'val' => '55.04', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:11', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110452', 'val' => '735.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:11', 'journal' => 'General', 'type' => 'Cost accrual', 'ref' => 'TRU6363230-126', 'val' => '26.37', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:09', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110451', 'val' => '2 855.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:08', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110450', 'val' => '1 895.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 17:06', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110449', 'val' => '610.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:50', 'journal' => 'Sales', 'type' => 'Outgoing aggregated invoice', 'ref' => 'SIN-110448', 'val' => '10 883.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:50', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117875-S', 'val' => '4 695.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:50', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-115888-S', 'val' => '2 603.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:50', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-115887-S', 'val' => '2 830.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:50', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-115686-S', 'val' => '655.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:41', 'journal' => 'Sales', 'type' => 'Outgoing aggregated invoice', 'ref' => 'SIN-110447', 'val' => '4 345.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:41', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117874-S', 'val' => '2 055.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:41', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117873-S', 'val' => '1 700.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:41', 'journal' => 'General', 'type' => 'Cost accrual', 'ref' => 'HAR6367662-226', 'val' => '11.70', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:41', 'journal' => 'General', 'type' => 'Outgoing sub invoice', 'ref' => 'SIN-117872-S', 'val' => '590.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:35', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110446', 'val' => '750.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:29', 'journal' => 'Sales', 'type' => 'Outgoing invoice', 'ref' => 'SIN-110445', 'val' => '3 400.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                            ['office' => 'SIN', 'ts' => '14.03.2026 16:27', 'journal' => 'Sales', 'type' => 'Outgoing aggregated invoice', 'ref' => 'SIN-110444', 'val' => '6 000.00', 'cur' => 'USD', 'period' => '2026-03'],
                                                                        ] as $row)
                                                                        <tr>
                                                                            <td>{{ $row['office'] }}</td>
                                                                            <td>{{ $row['ts'] }}</td>
                                                                            <td>{{ $row['journal'] }}</td>
                                                                            <td>{{ $row['type'] }}</td>
                                                                            <td><a href="#" style="color: #008080;">{{ $row['ref'] }}</a></td>
                                                                            <td class="text-right">{{ $row['val'] }}</td>
                                                                            <td>{{ $row['cur'] }}</td>
                                                                            <td>{{ $row['period'] }}</td>
                                                                            <td></td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="exports" role="tabpanel">
                                                            <!-- Exports Filter Row -->
                                                            <div class="row no-gutters filter-row align-items-center mb-0 mt-2">
                                                                <div class="custom-col" style="flex: 0 0 250px;">
                                                                    <div class="filter-group" style="height: 30px;">
                                                                        <span class="filter-label" style="background:#fff; border-right:none; color:#008080;">Office</span>
                                                                        <select class="form-control filter-input select2-exports">
                                                                            <option selected>SIN - Marinetrans Singap...</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto ml-2">
                                                                    <a class="clear-filters" style="font-size: 11px; color:#008080;">Clear filters</a>
                                                                </div>
                                                                <div class="col-auto ml-auto">
                                                                    <button class="btn btn-outline-teal" style="font-size: 11px; height: 30px; padding: 0 14px; border-radius: 4px;">Transactions report</button>
                                                                </div>
                                                            </div>

                                                            <!-- Exports Table -->
                                                            <div style="margin-top: 15px;">
                                                                <table id="exports-table" class="table table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Office</th>
                                                                            <th>Date</th>
                                                                            <th>Accounting system</th>
                                                                            <th>Transactions</th>
                                                                            <th>Files</th>
                                                                            <th>Operations</th>
                                                                            <th>Status</th>
                                                                            <th>User name</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach([
                                                                            ['office' => 'SIN', 'date' => '13.03.2026 09:41', 'system' => 'Exact', 'transactions' => '852', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '11.03.2026 09:32', 'system' => 'Exact', 'transactions' => '1863', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '10.03.2026 14:46', 'system' => 'Exact', 'transactions' => '113',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '04.03.2026 22:59', 'system' => 'Exact', 'transactions' => '21',   'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '04.03.2026 20:33', 'system' => 'Exact', 'transactions' => '126',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '03.03.2026 18:20', 'system' => 'Exact', 'transactions' => '1180', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '03.03.2026 09:57', 'system' => 'Exact', 'transactions' => '1952', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '27.02.2026 10:24', 'system' => 'Exact', 'transactions' => '601',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '26.02.2026 09:11', 'system' => 'Exact', 'transactions' => '1639', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '20.02.2026 15:58', 'system' => 'Exact', 'transactions' => '1724', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '12.02.2026 13:44', 'system' => 'Exact', 'transactions' => '1533', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '10.02.2026 10:09', 'system' => 'Exact', 'transactions' => '875',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '05.02.2026 22:24', 'system' => 'Exact', 'transactions' => '29',   'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '05.02.2026 16:55', 'system' => 'Exact', 'transactions' => '24',   'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '05.02.2026 15:31', 'system' => 'Exact', 'transactions' => '151',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '04.02.2026 19:30', 'system' => 'Exact', 'transactions' => '225',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '02.02.2026 19:05', 'system' => 'Exact', 'transactions' => '2006', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '31.01.2026 19:11', 'system' => 'Exact', 'transactions' => '805',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '30.01.2026 10:25', 'system' => 'Exact', 'transactions' => '452',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '29.01.2026 13:40', 'system' => 'Exact', 'transactions' => '337',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '28.01.2026 19:50', 'system' => 'Exact', 'transactions' => '1232', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '26.01.2026 09:59', 'system' => 'Exact', 'transactions' => '951',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '22.01.2026 13:33', 'system' => 'Exact', 'transactions' => '1167', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '20.01.2026 09:50', 'system' => 'Exact', 'transactions' => '858',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '16.01.2026 15:50', 'system' => 'Exact', 'transactions' => '1691', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '13.01.2026 11:21', 'system' => 'Exact', 'transactions' => '1368', 'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '08.01.2026 11:20', 'system' => 'Exact', 'transactions' => '112',  'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                            ['office' => 'SIN', 'date' => '07.01.2026 18:38', 'system' => 'Exact', 'transactions' => '44',   'files' => '2', 'status' => 'ok', 'user' => 'Venus Celemin'],
                                                                        ] as $row)
                                                                        <tr>
                                                                            <td>{{ $row['office'] }}</td>
                                                                            <td>{{ $row['date'] }}</td>
                                                                            <td>{{ $row['system'] }}</td>
                                                                            <td>{{ $row['transactions'] }}</td>
                                                                            <td>{{ $row['files'] }}</td>
                                                                            <td>
                                                                                <a href="#" style="color:#008080; margin-right:8px;" title="Download"><i class="ti-download" style="font-size:13px;"></i></a>
                                                                                <a href="#" style="color:#008080;" title="View lines"><i class="ti-menu" style="font-size:13px;"></i></a>
                                                                            </td>
                                                                            <td>
                                                                                <span style="color:#16a34a; font-size:16px;" title="OK"><i class="ti-check-box"></i></span>
                                                                            </td>
                                                                            <td>{{ $row['user'] }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="exchange-rates" role="tabpanel">
                                                            <!-- Exchange Rates Filter Row -->
                                                            <div class="row no-gutters filter-row align-items-center mb-0 mt-2">
                                                                <div class="custom-col" style="flex: 0 0 220px;">
                                                                    <div class="filter-group" style="height: 30px;">
                                                                        <span class="filter-label" style="background:#fff; border-right:none; color:#008080;">Currency</span>
                                                                        <select class="form-control filter-input select2-exrates">
                                                                            <option selected>USD - US Dollar</option>
                                                                            <option>EUR - Euro</option>
                                                                            <option>SGD - Singapore Dollar</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="custom-col ml-2" style="flex: 0 0 190px;">
                                                                    <div class="filter-group" style="height: 30px;">
                                                                        <span class="filter-label" style="background:#fff; border-right:none; color:#008080;">Date</span>
                                                                        <div class="input-group p-0 m-0" style="border: none; height: 30px;">
                                                                            <input type="text" id="exrates-date" class="form-control filter-input datepicker datepicker-exrates" value="14.03.2026" style="height:30px;" autocomplete="off">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text bg-transparent border-0" style="height:30px;"><i class="ti-calendar" style="font-size:13px;"></i></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto ml-2">
                                                                    <a href="#" style="font-size: 11px; color:#008080;">Clear filters</a>
                                                                </div>
                                                            </div>

                                                            <!-- Exchange Rates Table -->
                                                            <div style="margin-top: 15px;">
                                                                <table id="exrates-table" class="table table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Currency code</th>
                                                                            <th>Name</th>
                                                                            <th class="text-right">From USD</th>
                                                                            <th class="text-right">To USD</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach([
                                                                            ['code' => 'USD', 'name' => 'US Dollar',                     'from' => '1',           'to' => '1'],
                                                                            ['code' => 'EUR', 'name' => 'Euro',                           'from' => '0.87204',     'to' => '1.14674'],
                                                                            ['code' => 'SGD', 'name' => 'Singapore Dollar',               'from' => '1.2815',      'to' => '0.78033'],
                                                                            ['code' => 'JPY', 'name' => 'Yen',                            'from' => '159.74441',   'to' => '0.00626'],
                                                                            ['code' => 'NOK', 'name' => 'Norwegian Krone',                'from' => '9.74621',     'to' => '0.1026'],
                                                                            ['code' => 'GBP', 'name' => 'Pound Sterling',                 'from' => '0.75546',     'to' => '1.3237'],
                                                                            ['code' => 'DKK', 'name' => 'Danish Krone',                   'from' => '6.5451',      'to' => '0.15279'],
                                                                            ['code' => 'SEK', 'name' => 'Swedish Krona',                  'from' => '9.48074',     'to' => '0.10548'],
                                                                            ['code' => 'KRW', 'name' => 'Won',                            'from' => '1503.7594',   'to' => '0.00067'],
                                                                            ['code' => 'CNY', 'name' => 'Yuan Renminbi',                  'from' => '6.8956',      'to' => '0.145'],
                                                                            ['code' => 'CHF', 'name' => 'Swiss Franc',                    'from' => '0.79595',     'to' => '1.25636'],
                                                                            ['code' => 'AED', 'name' => 'UAE Dirham',                     'from' => '3.6725',      'to' => '0.27229'],
                                                                            ['code' => 'INR', 'name' => 'Indian Rupee',                   'from' => '92.58402',    'to' => '0.0108'],
                                                                            ['code' => 'HKD', 'name' => 'Hong Kong Dollar',               'from' => '7.83042',     'to' => '0.12771'],
                                                                            ['code' => 'VND', 'name' => 'Dong',                           'from' => '2631578947',  'to' => '0.00004'],
                                                                            ['code' => 'CAD', 'name' => 'Canadian Dollar',                'from' => '1.38085',     'to' => '0.72419'],
                                                                            ['code' => 'MYR', 'name' => 'Malaysian Ringgit',              'from' => '3.9385',      'to' => '0.2539'],
                                                                            ['code' => 'AUD', 'name' => 'Australian Dollar',              'from' => '1.43123',     'to' => '0.6987'],
                                                                            ['code' => 'PLN', 'name' => 'Zloty',                          'from' => '3.74845',     'to' => '0.26678'],
                                                                            ['code' => 'ZAR', 'name' => 'Rand',                           'from' => '16.88305',    'to' => '0.05923'],
                                                                            ['code' => 'NZD', 'name' => 'New Zealand Dollar',             'from' => '1.731',       'to' => '0.5777'],
                                                                            ['code' => 'MAD', 'name' => 'Moroccan Dirham',                'from' => '9.41779',     'to' => '0.1068'],
                                                                            ['code' => 'AFN', 'name' => 'Afghani',                        'from' => '63.00008',    'to' => '0.01587'],
                                                                            ['code' => 'ALL', 'name' => 'Lek',                            'from' => '83.70302',    'to' => '0.01195'],
                                                                            ['code' => 'AMD', 'name' => 'Armenian Dram',                  'from' => '378.93178',   'to' => '0.00265'],
                                                                            ['code' => 'ANG', 'name' => 'Netherlands Antillean Guilder',  'from' => '1.79008',     'to' => '0.55863'],
                                                                            ['code' => 'AOA', 'name' => 'Kwanza',                         'from' => '916.59028',   'to' => '0.00109'],
                                                                            ['code' => 'ARS', 'name' => 'Argentine Peso',                 'from' => '1398.6014',   'to' => '0.00072'],
                                                                            ['code' => 'AWG', 'name' => 'Aruban Florin',                  'from' => '1.8',         'to' => '0.55556'],
                                                                            ['code' => 'AZN', 'name' => 'Azerbaijanian Manat',            'from' => '1.70397',     'to' => '0.58687'],
                                                                        ] as $row)
                                                                        <tr>
                                                                            <td>{{ $row['code'] }}</td>
                                                                            <td>{{ $row['name'] }}</td>
                                                                            <td class="text-right">{{ $row['from'] }}</td>
                                                                            <td class="text-right">{{ $row['to'] }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                                </div>
                                            </div>
                                            
                                                    
                                            </div>
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
        // Prevent UMD/CommonJS detection by clearing dangling globals
        if (typeof exports === 'object' && typeof module === 'undefined') {
            window.exports = undefined;
        }
    </script>

    <!-- date-range-picker js -->

    

    <script>
        $(document).ready(function() {
            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: '100%'
            });

            // Initialize Datepickers
            $('.datepicker').datepicker({
                dateFormat: 'dd.mm.yy',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true
            });

            // Trigger datepicker on icon click
            $('.input-group-text i.ti-calendar').on('click', function() {
                $(this).closest('.input-group').find('input.datepicker').focus();
            });

            // Custom placeholder text styling can be placed here if needed

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            // Initialize exports-table DataTable
            var exportsTable = $('#exports-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            // Initialize exrates-table DataTable
            var exratesTable = $('#exrates-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            // Initialize Select2 for exports tab Office filter
            $('.select2-exports').select2({
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for exchange rates tab Currency filter
            $('.select2-exrates').select2({
                allowClear: true,
                width: '100%'
            });

            // Calendar icon click opens datepicker — delegated so it works in hidden tabs too
            $(document).on('click', '.input-group-text i.ti-calendar, .input-group-text .ti-calendar', function(e) {
                e.preventDefault();
                var $input = $(this).closest('.input-group').find('input.datepicker');
                if ($input.length) {
                    $input.datepicker('option', 'dateFormat', 'dd.mm.yy').datepicker('show');
                }
            });

            $('.clear-filters').on('click', function() {
                $('.select2').val(null).trigger('change');
                $('.filter-input:not(select)').val('').trigger('change');
                $('input[type="checkbox"]').prop('checked', false);
                table.columns().search('').draw();
            });
        });

    </script>
@endsection
