@extends('layouts.app')

@section('styles')

    <!-- Date-range picker css  -->

    <style>
        /* High Density Table Styles */
        #offices-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #offices-table thead th {
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
        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
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
                                                    <!-- Row 1 -->
                                                    <div class="row no-gutters filter-row">
                                                        <div class="col-auto mr-2">
                                                            <select id="filter-multiselect" multiple="multiple">
                                                                <option value="Office" selected>Office</option>
                                                                <option value="Customer" selected>Customer</option>
                                                                <option value="Vessel" selected>Vessel</option>
                                                                <option value="Status" selected>Status</option>
                                                                <option value="Shipment no" selected>Shipment no</option>
                                                                <option value="PO no" selected>PO no</option>
                                                                <option value="Service reference" selected>Service reference</option>
                                                                <option value="Departure" selected>Departure</option>
                                                                <option value="Consignee" selected>Consignee</option>
                                                                <option value="Destination" selected>Destination</option>
                                                                <option value="Account manager" selected>Account manager</option>
                                                                <option value="Remarks" selected>Remarks</option>
                                                                <option value="Closed date" selected>Closed date</option>
                                                                <option value="Service" selected>Service</option>
                                                                <option value="Cost product" selected>Cost product</option>
                                                                <option value="Customer reference" selected>Customer reference</option>
                                                                <option value="Ready for billing date" selected>Ready for billing date</option>
                                                                <option value="Chargeable status" selected>Chargeable status</option>
                                                            </select>
                                                        </div>
                                                        <div id="col-Office" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Office</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option selected>SIN - Marinetrans Singap...</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Customer</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Vessel" class="custom-col" style="flex: 0 0 230px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Vessel</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Status" class="custom-col" style="flex: 0 0 180px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Status</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 230px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Shipment no</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-PO-no" class="custom-col" style="flex: 0 0 160px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">PO no</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div class="col-auto ml-auto d-flex">
                                                            <button class="btn btn-export-download mr-1"><i class="ti-download"></i></button>
                                                            <button class="btn btn-reports">Add incoming invoice</button>
                                                        </div>
                                                    </div>

                                                    <!-- Row 2 -->
                                                    <div class="row no-gutters filter-row">
                                                        <div id="col-Service-reference" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Service reference</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-Departure" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Departure</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Consignee" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Consignee</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-Destination" class="custom-col" style="flex: 0 0 220px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Destination</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-Account-manager" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Account manager</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Remarks" class="custom-col" style="flex: 0 0 220px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Remarks</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Row 3 -->
                                                    <div class="row no-gutters filter-row">
                                                        <div id="col-Closed-date" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Closed date</span>
                                                                <div class="input-group p-0 m-0" style="border: none;">
                                                                    <input type="text" class="form-control filter-input datepicker" placeholder="">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text bg-transparent border-0"><i class="ti-calendar"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="col-Service" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Service</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Cost-product" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Cost product</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Customer-reference" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Customer reference</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-Ready-for-billing-date" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Ready for billing date</span>
                                                                <div class="input-group p-0 m-0" style="border: none;">
                                                                    <input type="text" class="form-control filter-input datepicker" placeholder="">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text bg-transparent border-0"><i class="ti-calendar"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Row 4 -->
                                                    <div class="row no-gutters filter-row">
                                                        <div id="col-Chargeable-status" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Chargeable status</span>
                                                                <select class="form-control filter-input select2">
                                                                    <option>Click here</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="col-Has-unbilled-charges" class="custom-col" style="flex: 0 0 160px;">
                                                            <div class="checkbox-container">
                                                                <span>Has unbilled charges</span>
                                                                <input type="checkbox">
                                                            </div>
                                                        </div>
                                                        <div id="col-Economic-date" class="custom-col" style="flex: 0 0 220px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Economic date</span>
                                                                <div class="input-group p-0 m-0" style="border: none;">
                                                                    <input type="text" class="form-control filter-input datepicker" placeholder="">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text bg-transparent border-0"><i class="ti-calendar"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="col-Inc-invoice-no" class="custom-col" style="flex: 0 0 200px;">
                                                            <div class="filter-group">
                                                                <span class="filter-label">Inc. invoice no</span>
                                                                <input type="text" class="form-control filter-input" placeholder="type here">
                                                            </div>
                                                        </div>
                                                        <div id="col-Port-agency" class="custom-col" style="flex: 0 0 130px;">
                                                            <div class="checkbox-container">
                                                                <span>Port agency</span>
                                                                <input type="checkbox">
                                                            </div>
                                                        </div>
                                                        <div id="col-Project-logistics" class="custom-col" style="flex: 0 0 250px;">
                                                            <div class="checkbox-container">
                                                                <span>Project logistics</span>
                                                                <input type="checkbox">
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a class="clear-filters">Clear filters</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                                    <table id="offices-table" class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Office</th>
                                                                <th>Inc. invoice id</th>
                                                                <th>Inc. invoice no</th>
                                                                <th>Invoice date</th>
                                                                <th>Due date</th>
                                                                <th>Status</th>
                                                                <th class="text-right">Gross amount</th>
                                                                <th>Currency</th>
                                                                <th>Hub/Agent/Office</th>
                                                                <th>Notes</th>
                                                                <th>Reference no</th>
                                                                <th style="width: 30px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach([
                                                                ['office' => 'SIN', 'id' => '232923', 'no' => 'A42500030562', 'info' => true, 'idate' => '18.11.2025', 'ddate' => '17.01.2026', 'status' => 'Draft', 'gross' => '550.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING CREDIT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '241590', 'no' => 'A42500031293', 'info' => false, 'idate' => '18.11.2025', 'ddate' => '17.01.2026', 'status' => 'Draft', 'gross' => '550.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING CREDIT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '245526', 'no' => 'A42500032715', 'info' => false, 'idate' => '26.11.2025', 'ddate' => '25.01.2026', 'status' => 'Draft', 'gross' => '1 845.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '257683', 'no' => '35457333', 'info' => false, 'idate' => '27.11.2025', 'ddate' => '27.12.2025', 'status' => 'Draft', 'gross' => '825.00', 'cur' => 'EUR', 'hub' => 'ANR - Ziegler N.V.', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT emailed Bertson fo', 'ref' => 'SEA-35457333'],
                                                                ['office' => 'SIN', 'id' => '246107', 'no' => 'A42500032949', 'info' => false, 'idate' => '27.11.2025', 'ddate' => '26.01.2026', 'status' => 'Draft', 'gross' => '95.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '246394', 'no' => 'A42500033002', 'info' => false, 'idate' => '28.11.2025', 'ddate' => '27.01.2026', 'status' => 'Draft', 'gross' => '71.59', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '249454', 'no' => 'A42500034017', 'info' => false, 'idate' => '04.12.2025', 'ddate' => '02.02.2026', 'status' => 'Draft', 'gross' => '52.57', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '249451', 'no' => 'A42500034002', 'info' => false, 'idate' => '04.12.2025', 'ddate' => '02.02.2026', 'status' => 'Draft', 'gross' => '41.25', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '253821', 'no' => 'A42500035246', 'info' => true, 'idate' => '15.12.2025', 'ddate' => '13.02.2026', 'status' => 'Draft', 'gross' => '2 260.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES COU', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '255480', 'no' => 'A42500035826', 'info' => false, 'idate' => '18.12.2025', 'ddate' => '16.02.2026', 'status' => 'Draft', 'gross' => '526.98', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '256958', 'no' => 'A42500036181', 'info' => false, 'idate' => '22.12.2025', 'ddate' => '20.02.2026', 'status' => 'Draft', 'gross' => '90.21', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '257744', 'no' => 'A42500036657', 'info' => true, 'idate' => '23.12.2025', 'ddate' => '21.02.2026', 'status' => 'Draft', 'gross' => '1 740.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES COU', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '257727', 'no' => 'YEO8354545-1225-1', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '22.01.2026', 'status' => 'Draft', 'gross' => '170.00', 'cur' => 'USD', 'hub' => 'PUS1 - Marinetrans Korea Co., Ltd.', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'YEO6354545-1225'],
                                                                ['office' => 'SIN', 'id' => '257714', 'no' => 'A42500036564', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '21.02.2026', 'status' => 'Draft', 'gross' => '1 008.50', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'POO6351730-1225'],
                                                                ['office' => 'SIN', 'id' => '257695', 'no' => 'JA16346144C-1', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '22.01.2026', 'status' => 'Draft', 'gross' => '420.00', 'cur' => 'USD', 'hub' => 'PUS1 - Marinetrans Korea Co., Ltd.', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'JAI6346144-1125'],
                                                                ['office' => 'SIN', 'id' => '259521', 'no' => 'A42500037122', 'info' => true, 'idate' => '29.12.2025', 'ddate' => '27.02.2026', 'status' => 'Draft', 'gross' => '1 898.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES COU', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '260199', 'no' => 'A42500037513', 'info' => false, 'idate' => '30.12.2025', 'ddate' => '28.02.2026', 'status' => 'Draft', 'gross' => '790.64', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING CREDIT', 'ref' => 'VIN6329590-1025'],
                                                                ['office' => 'SIN', 'id' => '256958', 'no' => 'A42500036181', 'info' => false, 'idate' => '22.12.2025', 'ddate' => '20.02.2026', 'status' => 'Draft', 'gross' => '90.21', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING AGENT', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '257744', 'no' => 'A42500036657', 'info' => true, 'idate' => '23.12.2025', 'ddate' => '21.02.2026', 'status' => 'Draft', 'gross' => '1 740.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES COU', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '257727', 'no' => 'YEO8354545-1225-1', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '22.01.2026', 'status' => 'Draft', 'gross' => '170.00', 'cur' => 'USD', 'hub' => 'PUS1 - Marinetrans Korea Co., Ltd.', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'YEO6354545-1225'],
                                                                ['office' => 'SIN', 'id' => '257714', 'no' => 'A42500036564', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '21.02.2026', 'status' => 'Draft', 'gross' => '1 008.50', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'POO6351730-1225'],
                                                                ['office' => 'SIN', 'id' => '257695', 'no' => 'JA16346144C-1', 'info' => false, 'idate' => '23.12.2025', 'ddate' => '22.01.2026', 'status' => 'Draft', 'gross' => '420.00', 'cur' => 'USD', 'hub' => 'PUS1 - Marinetrans Korea Co., Ltd.', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING OPS RE', 'ref' => 'JAI6346144-1125'],
                                                                ['office' => 'SIN', 'id' => '259521', 'no' => 'A42500037122', 'info' => true, 'idate' => '29.12.2025', 'ddate' => '27.02.2026', 'status' => 'Draft', 'gross' => '1 898.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES COU', 'ref' => ''],
                                                                ['office' => 'SIN', 'id' => '260199', 'no' => 'A42500037513', 'info' => false, 'idate' => '30.12.2025', 'ddate' => '28.02.2026', 'status' => 'Draft', 'gross' => '790.64', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT AWAITING CREDIT', 'ref' => 'VIN6329590-1025'],
                                                                ['office' => 'SIN', 'id' => '262668', 'no' => 'WV00002244', 'info' => true, 'idate' => '08.01.2026', 'ddate' => '09.03.2026', 'status' => 'Draft', 'gross' => '2 053.00', 'cur' => 'EUR', 'hub' => 'AMS3 - AIT Worldwide Logistics B.V', 'notes' => 'DO NOT DELETE ALREADY REGISTERED IN EXACT T1 CHARGES T1 W', 'ref' => ''],
                                                            ] as $row)
                                                            <tr>
                                                                <td>{{ $row['office'] }}</td>
                                                                <td>{{ $row['id'] }}</td>
                                                                <td>
                                                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                                                        <a href="#">{{ $row['no'] }}</a>
                                                                        @if($row['info'])
                                                                        <i class="ti-info-alt text-muted" style="font-size: 13px;"></i>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>{{ $row['idate'] }}</td>
                                                                <td>{{ $row['ddate'] }}</td>
                                                                <td>{{ $row['status'] }}</td>
                                                                <td class="text-right">{{ $row['gross'] }}</td>
                                                                <td>{{ $row['cur'] }}</td>
                                                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="{{ $row['hub'] }}">{{ $row['hub'] }}</td>
                                                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;" title="{{ $row['notes'] }}">{{ $row['notes'] }}</td>
                                                                <td>{{ $row['ref'] }}</td>
                                                                <td class="text-center">
                                                                    <a href="#" style="color: #94a3b8 !important;" title="Delete">
                                                                        <i class="ti-trash" style="font-size: 14px;"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
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

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonWidth: '100%',
                maxHeight: 200,
                nonSelectedText: '',
                allSelectedText: '',
                nSelectedText: '',
                numberDisplayed: 0,
                buttonClass: 'btn btn-filter-toggle',
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

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Office', id: 'col-Office'},
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Status', id: 'col-Status'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'PO no', id: 'col-PO-no'},
                    {val: 'Service reference', id: 'col-Service-reference'},
                    {val: 'Departure', id: 'col-Departure'},
                    {val: 'Consignee', id: 'col-Consignee'},
                    {val: 'Destination', id: 'col-Destination'},
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Remarks', id: 'col-Remarks'},
                    {val: 'Closed date', id: 'col-Closed-date'},
                    {val: 'Service', id: 'col-Service'},
                    {val: 'Cost product', id: 'col-Cost-product'},
                    {val: 'Customer reference', id: 'col-Customer-reference'},
                    {val: 'Ready for billing date', id: 'col-Ready-for-billing-date'},
                    {val: 'Chargeable status', id: 'col-Chargeable-status'},
                    {val: 'Has unbilled charges', id: 'col-Has-unbilled-charges'},
                    {val: 'Economic date', id: 'col-Economic-date'},
                    {val: 'Inc. invoice no', id: 'col-Inc-invoice-no'},
                    {val: 'Port agency', id: 'col-Port-agency'},
                    {val: 'Project logistics', id: 'col-Project-logistics'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            toggleFilterVisibility();

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

            $('.clear-filters').on('click', function() {
                $('.select2').val(null).trigger('change');
                $('.filter-input:not(select)').val('').trigger('change');
                $('input[type="checkbox"]').prop('checked', false);
                table.columns().search('').draw();
            });
        });
    </script>
@endsection
