@extends('layouts.app')

@section('styles')

    <!-- Date-range picker css  -->

    <x-lists.base-styles bodyClass="cost-filters-open" toolbarClass="cost-filters-toolbar" />
    <x-lists.multiselect-assets />
    <style>
        #offices-table {
            width: 100% !important;
            min-width: 1500px;
            table-layout: fixed;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            background: #fff;
        }
        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
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
            box-sizing: border-box !important;
        }
        #offices-table th:nth-child(1),
        #offices-table td:nth-child(1) { width: 120px; min-width: 120px; max-width: 120px; }
        #offices-table th:nth-child(2),
        #offices-table td:nth-child(2) { width: 160px; min-width: 160px; max-width: 160px; }
        #offices-table th:nth-child(3),
        #offices-table td:nth-child(3) { width: 130px; min-width: 130px; max-width: 130px; }
        #offices-table th:nth-child(4),
        #offices-table td:nth-child(4) { width: 100px; min-width: 100px; max-width: 100px; }
        #offices-table th:nth-child(5),
        #offices-table td:nth-child(5) { width: 130px; min-width: 130px; max-width: 130px; }
        #offices-table th:nth-child(6),
        #offices-table td:nth-child(6) { width: 200px; min-width: 200px; max-width: 200px; }
        #offices-table th:nth-child(7),
        #offices-table td:nth-child(7) { width: 70px; min-width: 70px; max-width: 70px; }
        #offices-table th:nth-child(8),
        #offices-table td:nth-child(8) { width: 70px; min-width: 70px; max-width: 70px; }
        #offices-table th:nth-child(9),
        #offices-table td:nth-child(9) { width: 90px; min-width: 90px; max-width: 90px; }
        #offices-table th:nth-child(10),
        #offices-table td:nth-child(10) { width: 90px; min-width: 90px; max-width: 90px; }
        #offices-table th:nth-child(11),
        #offices-table td:nth-child(11) { width: 90px; min-width: 90px; max-width: 90px; }
        #offices-table th:nth-child(12),
        #offices-table td:nth-child(12) { width: 100px; min-width: 100px; max-width: 100px; }
        #offices-table th:nth-child(13),
        #offices-table td:nth-child(13) { width: 90px; min-width: 90px; max-width: 90px; }
        #offices-table th:nth-child(14),
        #offices-table td:nth-child(14) { width: 120px; min-width: 120px; max-width: 120px; }
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
        .btn-outline-teal:hover,
        .btn-outline-teal:focus,
        .btn-outline-teal:active,
        .send-reminder-btn:hover,
        .send-reminder-btn:focus,
        .send-reminder-btn:active {
            background-color: #008080 !important;
            border-color: #008080 !important;
            color: #ffffff !important;
        }
        .send-reminder-btn {
            color: #008080 !important;
            border-color: #008080 !important;
            background-color: #ffffff !important;
        }
        .custom-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }
        .filter-row {
            margin-bottom: 8px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
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
        .select2-selection__clear,
        .select2-selection__choice__remove {
            display: none !important;
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

        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        body.cost-follow-up-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.cost-follow-up-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.cost-follow-up-list-page .pcoded-inner-content,
        body.cost-follow-up-list-page .main-body,
        body.cost-follow-up-list-page .page-wrapper,
        body.cost-follow-up-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .cost-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 104px);
            margin-bottom: 0 !important;
            overflow: hidden;
        }
        .cost-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding-bottom: 8px !important;
        }
        .cost-filters-fixed {
            flex-shrink: 0;
            background: #fff;
            position: relative;
            z-index: 40;
            padding-bottom: 6px;
        }
        .cost-filters-fields {
            width: 100%;
        }
        .cost-table-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cost-table-area .dataTables_wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100%;
            padding-bottom: 0 !important;
            width: 100%;
        }
        .cost-table-area .table-scroll-wrapper {
            flex: 1;
            min-height: 0;
            overflow: auto !important;
            width: 100%;
            position: relative;
            -webkit-overflow-scrolling: touch;
        }
        .cost-table-area .office-table,
        .cost-table-area #offices-table {
            min-width: 1100px;
        }

        @media (max-width: 991.98px) {
            .cost-list-card {
                height: calc(100vh - 64px) !important;
                margin-top: 8px !important;
            }
            .cost-filters-fields {
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
            body.cost-filters-open .cost-filters-fields {
                display: flex !important;
            }
            .cost-filters-fields .custom-col[style*="flex: 0 0 50px"],
            .cost-filters-fields .btn-filter-toggle {
                display: none !important;
            }
            .cost-filters-fields .filter-row,
            .cost-filters-fields .row.custom-row {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100%;
            }
            .cost-filters-fields .custom-col,
            .cost-filters-fields .custom-col[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
            }
            .cost-filters-fields .filter-group {
                width: 100%;
                max-width: 100%;
            }
            .cost-table-area {
                flex: 1 1 auto;
                min-height: 45vh;
            }
            .cost-table-area .table-scroll-wrapper {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .pagination-sticky-footer {
                justify-content: center !important;
                padding: 8px 12px !important;
                height: auto !important;
                min-height: 48px;
            }
        }

        @media (min-width: 992px) {
            .cost-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.cost-filters-open .cost-filters-fields {
                display: flex !important;
            }
        }

        #offices-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 5 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 1px 0 #dee2e6;
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
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
        .dataTables_wrapper {
            padding-bottom: 0 !important;
        }
        td a {
            color: rgb(24, 100, 131) !important;
        }
        @include('Shipment.partials.reminder-compose-styles')
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
                                        <div class="card cost-list-card mt-4">
                                            <div class="card-block">
                                                <div class="cost-filters-fixed">
                                                <x-lists.filter-toolbar
                                                    toggle-id="btn-cost-filters-toggle"
                                                    body-class="cost-filters-open"
                                                    toolbar-class="cost-filters-toolbar"
                                                />
                                                <div class="d-flex justify-content-between align-items-start pt-2 cost-filters-fields">
                                                    <div style="width: 100%;">
                                                        <div class="row custom-row filter-row">
                                                            <div class="custom-col" style="flex: 0 0 50px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Shipment no" selected>Shipment no</option>
                                                                    <option value="Customer" selected>Customer</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Port of destination" selected>Port of destination</option>
                                                                    <option value="Status" selected>Status</option>
                                                                    <option value="Created by" selected>Created by</option>
                                                                </select>
                                                            </div>

                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select id="filter-account-manager" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager->name }}">{{ $manager->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" id="filter-shipment-no" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>

                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select id="filter-customer" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select id="filter-vessel" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" id="filter-port-destination" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>

                                                            <div id="col-Status" class="custom-col" style="flex: 0 0 180px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Status</span>
                                                                    <select id="filter-status" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($statuses as $status)
                                                                            <option value="{{ $status }}">{{ $status }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Created-by" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Created by</span>
                                                                    <select id="filter-created-by" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->name }}">{{ $creator->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="custom-col">
                                                                <x-lists.clear-filters id="clear-cost-filters" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="cost-table-area">
                                                    <table id="offices-table"
                                                        class="table office-table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment no.</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Service reference</th>
                                                                <th>Consignee</th>
                                                                <th>Dep.</th>
                                                                <th>Dest.</th>
                                                                <th>Dep. date</th>
                                                                <th>Arrival date</th>
                                                                <th>Del. date</th>
                                                                <th>Status</th>
                                                                <th>Rem. sent</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
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
    @include('Shipment.partials.reminder-compose-modal')


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('cost-follow-up-list-page');

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
                onChange: function() {
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

            var costFilterIds = [
                'col-Account-manager',
                'col-Shipment-no',
                'col-Customer',
                'col-Vessel',
                'col-Port-of-destination',
                'col-Status',
                'col-Created-by'
            ];

            function isCostMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureCostMobileFiltersVisible() {
                if (!isCostMobile()) {
                    return;
                }
                costFilterIds.forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.cost-filters-fields .custom-col[style*="flex: 0 0 50px"]').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                if (isCostMobile()) {
                    ensureCostMobileFiltersVisible();
                    if (typeof table !== 'undefined' && table.columns) {
                        setTimeout(adjustCostTableLayout, 50);
                    }
                    return;
                }

                var selectedValues = [];
                $('#filter-multiselect option:selected').each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Port of destination', id: 'col-Port-of-destination'},
                    {val: 'Status', id: 'col-Status'},
                    {val: 'Created by', id: 'col-Created-by'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });

                if (typeof table !== 'undefined' && table.columns) {
                    setTimeout(adjustCostTableLayout, 50);
                }
            }

            toggleFilterVisibility();
            ensureCostMobileFiltersVisible();

            var table;

            function adjustCostTableLayout() {
                if (!table || !table.columns) {
                    return;
                }

                table.columns.adjust();
            }

            table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "targets": 0, "width": "120px" },
                    { "targets": 1, "width": "160px" },
                    { "targets": 2, "width": "130px" },
                    { "targets": 3, "width": "100px" },
                    { "targets": 4, "width": "130px" },
                    { "targets": 5, "width": "200px" },
                    { "targets": 6, "width": "70px" },
                    { "targets": 7, "width": "70px" },
                    { "targets": 8, "width": "90px" },
                    { "targets": 9, "width": "90px" },
                    { "targets": 10, "width": "90px" },
                    { "targets": 11, "width": "100px" },
                    { "targets": 12, "width": "90px" },
                    { "targets": 13, "width": "120px", "orderable": false }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    },
                    "emptyTable": "Use filters to search shipments"
                },
                "drawCallback": function() {
                    this.api().columns.adjust();
                }
            });

            $(window).on('resize', function() {
                toggleFilterVisibility();
                ensureCostMobileFiltersVisible();
                adjustCostTableLayout();
            });

            setTimeout(adjustCostTableLayout, 100);
            setTimeout(adjustCostTableLayout, 400);

            var searchRequest = null;
            var searchTimer = null;
            var searchUrl = @json(route('cost-follow-up.search'));

            function escapeHtml(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function getActiveFilters() {
                return {
                    account_manager: $('#filter-account-manager').val() || [],
                    customer: $('#filter-customer').val() || [],
                    vessel: $('#filter-vessel').val() || [],
                    status: $('#filter-status').val() || [],
                    created_by: $('#filter-created-by').val() || [],
                    shipment_no: String($('#filter-shipment-no').val() || '').trim(),
                    port_destination: String($('#filter-port-destination').val() || '').trim()
                };
            }

            function hasActiveFilters(filters) {
                return (filters.account_manager && filters.account_manager.length)
                    || (filters.customer && filters.customer.length)
                    || (filters.vessel && filters.vessel.length)
                    || (filters.status && filters.status.length)
                    || (filters.created_by && filters.created_by.length)
                    || filters.shipment_no !== ''
                    || filters.port_destination !== '';
            }

            function consigneeCellHtml(row) {
                var name = escapeHtml(row.consignee);
                var type = String(row.consignee_type || '');
                if (type === 'hub') {
                    return '<span class="consignee-row consignee-hub-agent"><i class="ti-home consignee-hub-icon" title="Hub"></i><span class="consignee-hub-agent-text">' + name + '</span></span>';
                }
                if (type === 'agent') {
                    return '<span class="consignee-row consignee-hub-agent"><i class="ti-user consignee-hub-icon" title="Agent"></i><span class="consignee-hub-agent-text">' + name + '</span></span>';
                }
                return '<span class="consignee-row"><span class="consignee-hub-icon consignee-hub-icon-spacer"></span><span class="consignee-hub-agent-text">' + name + '</span></span>';
            }

            function buildRowHtml(row) {
                var shipmentCell = '<div class="d-flex align-items-center">'
                    + '<a href="' + escapeHtml(row.edit_url) + '">' + escapeHtml(row.shipment_number) + '</a>'
                    + (row.has_open_irregularities ? '<i class="ti-alert text-danger ml-2" title="Open irregularities"></i>' : '')
                    + '</div>';

                var delDateStyle = row.del_overdue ? ' style="color: #ff5252; font-weight: 500;"' : '';

                return [
                    shipmentCell,
                    escapeHtml(row.customer),
                    escapeHtml(row.vessel),
                    escapeHtml(row.service),
                    escapeHtml(row.service_reference),
                    consigneeCellHtml(row),
                    escapeHtml(row.departure),
                    escapeHtml(row.destination),
                    escapeHtml(row.etd),
                    escapeHtml(row.eta),
                    '<span' + delDateStyle + '>' + escapeHtml(row.del_date) + '</span>',
                    '<span class="' + escapeHtml(row.status_badge_class) + '" style="padding: 4px 8px; font-weight: 500;">' + escapeHtml(row.status) + '</span>',
                    '<span class="reminder-sent-count" data-shipment-id="' + escapeHtml(row.id) + '">' + escapeHtml(row.reminder_sent_count || 0) + '</span>',
                    '<button type="button" class="btn btn-outline-teal py-1 px-2 send-reminder-btn" style="font-size: 11px; height: 26px;"'
                        + ' data-shipment-id="' + escapeHtml(row.id) + '"'
                        + ' data-preview-url="' + escapeHtml(row.preview_url) + '"'
                        + ' data-send-url="' + escapeHtml(row.send_url) + '"'
                        + ' data-eml-url="' + escapeHtml(row.eml_url) + '"'
                        + ' data-eml-filename="' + escapeHtml(row.eml_filename) + '">Send reminder</button>'
                ];
            }

            function clearTableRows() {
                table.clear().draw(false);
                setTimeout(adjustCostTableLayout, 50);
            }

            function fetchFilteredShipments() {
                if (!table) {
                    return;
                }

                var filters = getActiveFilters();

                if (!hasActiveFilters(filters)) {
                    if (searchRequest && searchRequest.readyState !== 4) {
                        searchRequest.abort();
                    }
                    clearTableRows();
                    return;
                }

                if (searchRequest && searchRequest.readyState !== 4) {
                    searchRequest.abort();
                }

                searchRequest = $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    data: filters,
                    traditional: false,
                    dataType: 'json'
                }).done(function(response) {
                    var rows = (response && response.data) ? response.data : [];
                    table.clear();

                    rows.forEach(function(row) {
                        table.row.add(buildRowHtml(row));
                    });

                    table.draw(false);
                    setTimeout(adjustCostTableLayout, 50);
                    setTimeout(adjustCostTableLayout, 200);
                }).fail(function(xhr) {
                    if (xhr.statusText === 'abort') {
                        return;
                    }
                    clearTableRows();
                });
            }

            function scheduleFetch() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchFilteredShipments, 300);
            }

            initializeSearchableFilterMultiselect(
                '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by',
                {
                    onChange: function () { scheduleFetch(); },
                    onSelectAll: function () { scheduleFetch(); },
                    onDeselectAll: function () { scheduleFetch(); }
                }
            );

            $('#filter-shipment-no, #filter-port-destination').on('keyup input', scheduleFetch);
            $('#filter-customer, #filter-vessel, #filter-account-manager, #filter-status, #filter-created-by').on('change', scheduleFetch);

            $(document).on('click', '.multiselect-reset a', function () {
                setTimeout(scheduleFetch, 0);
            });

            $('#clear-cost-filters').on('click', function(e) {
                e.preventDefault();
                clearTimeout(searchTimer);
                clearSearchableFilterMultiselect(
                    '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by',
                    false
                );
                $('.filter-input:not(select)').val('');
                clearTableRows();
            });

            @include('Shipment.partials.reminder-compose-script')
        });
    </script>
@endpush
