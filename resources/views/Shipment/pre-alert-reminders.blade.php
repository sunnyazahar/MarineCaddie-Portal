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
    <!-- Date-range picker css  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
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
            font-weight: 600;
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
            -webkit-overflow-scrolling: touch;
        }
        .table-scroll-wrapper .office-table,
        .dataTables_wrapper .office-table,
        #offices-table {
            min-width: 1100px;
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
        .prealert-filters-toolbar {
            display: none;
        }
        .prealert-filters-fields {
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .prealert-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 4px 0 8px;
            }
            .prealert-filters-fields {
                display: none !important;
                flex-direction: column;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                margin-bottom: 8px;
                border-bottom: 1px solid #eef2f7;
            }
            body.prealert-filters-open .prealert-filters-fields {
                display: flex !important;
            }
            #btn-prealert-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }
            .prealert-filters-fields .mr-2,
            .prealert-filters-fields .btn-filter-toggle {
                display: none !important;
            }
            .prealert-filters-fields .row.no-gutters {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100%;
            }
            .prealert-filters-fields .custom-col,
            .prealert-filters-fields .custom-col[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
            }
            .prealert-filters-fields .filter-group {
                width: 100%;
                max-width: 100%;
            }
            .prealert-filters-fields .clear-filters {
                margin: 4px 0 8px;
            }
            .table-scroll-wrapper,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                width: 100% !important;
                max-width: 100%;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .pagination-sticky-footer {
                justify-content: center !important;
                padding: 8px 12px !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                justify-content: center;
            }
        }

        @media (min-width: 992px) {
            .prealert-filters-toolbar {
                display: none !important;
            }
            .prealert-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.prealert-filters-open .prealert-filters-fields {
                display: flex !important;
            }
        }
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
        td a {
            color: rgb(24, 100, 131) !important;
        }
        @include('Shipment.partials.reminder-compose-styles')
    </style>
    @include('partials.searchable-filter-multiselect-styles')
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
                                            <div class="card-block">
                                                <div class="prealert-filters-toolbar">
                                                    <button type="button" id="btn-prealert-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="prealert-filters-toggle-label">Show filters</span>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 prealert-filters-fields">
                                                    <div style="width: 100%;">
                                                        <div class="row no-gutters">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Show ETL shipments" selected>Show ETL shipments</option>
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

                                                            <div id="col-Show-ETL-shipments" class="custom-col" style="flex: 0 0 160px;">
                                                                <div class="filter-group" style="border: none; background: transparent;">
                                                                    <div class="d-flex align-items-center" style="font-size: 11px; color: #64748b; font-weight: 500;">
                                                                        <span>Show ETL shipments</span>
                                                                        <input type="checkbox" id="filter-show-etl" class="ml-2" style="width: 14px; height: 14px; margin-top: 0;">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" id="filter-shipment-no" class="form-control filter-input" placeholder="type here">
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
                                                                    <input type="text" id="filter-port-destination" class="form-control filter-input" placeholder="type here">
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

                                                            <a class="clear-filters">Clear filters</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="dt-responsive table-responsive">
                                                    <table id="offices-table"
                                                        class="office-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment no</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>Weight</th>
                                                                <th>Deadline arrival</th>
                                                                <th>Status</th>
                                                                <th>PA reminder</th>
                                                                <th>Handled by</th>
                                                                <th>Rem. sent</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($shipments as $shipment)
                                                            @php
                                                                $departureDisplay = $shipment->partyDisplay($shipment->departure, $partyNames);
                                                                $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
                                                                $paReminder = $shipment->pre_alert_reminder;
                                                                $paReminderOverdue = $paReminder && $paReminder->startOfDay()->lt(now()->startOfDay());
                                                            @endphp
                                                            <tr
                                                                data-customers="{{ $shipment->customer_names->implode(',') }}"
                                                                data-vessels="{{ $shipment->vessel_names->implode(',') }}"
                                                                data-shipment-number="{{ $shipment->shipment_number }}"
                                                                data-destination="{{ $shipment->destination_display }}"
                                                                data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
                                                                data-created-by="{{ $shipment->creator?->name ?? '' }}"
                                                                data-status="{{ $shipment->status ?? '' }}"
                                                                data-has-etl="{{ $shipment->hasEtlStock() ? '1' : '0' }}"
                                                            >
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <a href="{{ route('shipments.edit', $shipment->id) }}">{{ $shipment->shipment_number }}</a>
                                                                        @if ($shipment->hasOpenIrregularities())
                                                                            <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>{{ $shipment->customer_display }}</td>
                                                                <td>{{ $shipment->vessel_display }}</td>
                                                                <td>{{ $shipment->service ?? '—' }}</td>
                                                                <td>{{ $consigneeDisplay }}</td>
                                                                <td>{{ $departureDisplay }}</td>
                                                                <td>{{ $shipment->destination_display }}</td>
                                                                <td>{{ $shipment->total_weight_display }}</td>
                                                                <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>
                                                                    <span class="{{ $shipment->statusBadgeClass() }}" style="padding: 4px 8px; font-weight: 500;">
                                                                        {{ $shipment->status ?? '—' }}
                                                                    </span>
                                                                </td>
                                                                <td @if($paReminderOverdue) style="color: #ff5252;" @endif>{{ $paReminder?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>{{ $shipment->accountManager?->name ?? '—' }}</td>
                                                                <td class="reminder-sent-count" data-shipment-id="{{ $shipment->id }}">{{ $shipment->reminder_sent_count }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-teal py-1 pl-2 pr-2 send-reminder-btn"
                                                                        style="font-size: 10px; height: 24px; border-color: #ddd; color: #666;"
                                                                        data-shipment-id="{{ $shipment->id }}"
                                                                        data-preview-url="{{ route('shipments.pre-alert-reminder-mail.preview', $shipment->id) }}"
                                                                        data-send-url="{{ route('shipments.pre-alert-reminder-mail.dispatch', $shipment->id) }}"
                                                                        data-eml-url="{{ route('shipments.pre-alert-reminder-mail', $shipment->id) }}"
                                                                        data-eml-filename="pre-alert-reminder-{{ $shipment->shipment_number }}.eml">Send reminder</button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="14" class="text-center py-4 text-muted">No shipments found.</td>
                                                            </tr>
                                                            @endforelse
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

    <div class="modal fade" id="compose-reminder-modal" tabindex="-1" role="dialog"
        aria-labelledby="composeReminderLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="compose-send-loader" aria-live="polite" aria-busy="true">
                    <div class="compose-send-spinner" role="status"></div>
                    <p class="compose-send-loader-text">Sending email...</p>
                </div>
                <div class="compose-header">
                    <h5 class="compose-title" id="composeReminderLabel">Compose New Message</h5>
                </div>
                <div class="compose-body">
                    <div class="compose-field">
                        <input type="text" id="reminder-mail-to" class="compose-input" placeholder="To:">
                    </div>
                    <div class="compose-field compose-field-contact">
                        <div class="compose-field-with-icon">
                            <input type="text" id="reminder-mail-cc" class="compose-input" placeholder="Cc:">
                            <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-cc" title="Contact directory">
                                <i class="icofont icofont-contacts"></i>
                            </button>
                        </div>
                        <div class="compose-contact-picker" data-for="reminder-mail-cc">
                            <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                            <ul class="compose-contact-list"></ul>
                        </div>
                    </div>
                    <div class="compose-field compose-field-contact">
                        <div class="compose-field-with-icon">
                            <input type="text" id="reminder-mail-bcc" class="compose-input" placeholder="Bcc:">
                            <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-bcc" title="Contact directory">
                                <i class="icofont icofont-contacts"></i>
                            </button>
                        </div>
                        <div class="compose-contact-picker" data-for="reminder-mail-bcc">
                            <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                            <ul class="compose-contact-list"></ul>
                        </div>
                    </div>
                    <div class="compose-field">
                        <input type="text" id="reminder-mail-subject" class="compose-input" placeholder="Subject:">
                    </div>
                    <div class="compose-editor-wrap">
                        <div class="compose-toolbar">
                            <select id="reminder-font-size" title="Text style">
                                <option value="3">A Normal text</option>
                                <option value="2">Small</option>
                                <option value="4">Large</option>
                                <option value="5">Heading</option>
                            </select>
                            <button type="button" class="compose-tool-btn" data-cmd="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" class="compose-tool-btn" data-cmd="italic" title="Italic"><em>I</em></button>
                            <button type="button" class="compose-tool-btn" data-cmd="underline" title="Underline"><u>U</u></button>
                            <button type="button" class="compose-tool-btn" data-cmd="fontSize" data-value="2" title="Small">Small</button>
                            <button type="button" class="compose-tool-btn" data-cmd="formatBlock" data-value="blockquote" title="Quote"><i class="ti-quote-left"></i></button>
                            <button type="button" class="compose-tool-btn" data-cmd="insertUnorderedList" title="Bulleted list"><i class="ti-list"></i></button>
                            <button type="button" class="compose-tool-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="ti-list-ol"></i></button>
                            <button type="button" class="compose-tool-btn" data-cmd="outdent" title="Outdent"><i class="ti-shift-left-alt"></i></button>
                            <button type="button" class="compose-tool-btn" data-cmd="indent" title="Indent"><i class="ti-shift-right-alt"></i></button>
                        </div>
                        <div id="reminder-mail-body" class="compose-editor" contenteditable="true" data-placeholder="Your Message Here...."></div>
                    </div>
                    <div class="compose-attach-row">
                        <button type="button" class="btn-compose-attach" id="reminder-attachment-btn">
                            <i class="ti-clip"></i> Attachment
                        </button>
                        <p class="compose-attach-hint">Maximum 20MB per file</p>
                        <div class="compose-attach-previews" id="reminder-attach-previews"></div>
                        <input type="file" id="reminder-attachment-input" multiple style="display:none;"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip">
                    </div>
                </div>
                <div class="compose-footer">
                    <button type="button" class="btn-compose-discard" id="reminder-mail-discard">
                        <i class="ti-close"></i> Discard
                    </button>
                    <div class="compose-footer-right">
                        <button type="button" class="btn-compose-send" id="reminder-mail-send">
                            <i class="ti-email"></i> Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <!-- date-range-picker js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function() {
            initializeSearchableFilterMultiselect(
                '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by'
            );

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

            var prealertFilterIds = [
                'col-Account-manager',
                'col-Show-ETL-shipments',
                'col-Shipment-no',
                'col-Customer',
                'col-Vessel',
                'col-Port-of-destination',
                'col-Status',
                'col-Created-by'
            ];

            function isPrealertMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensurePrealertMobileFiltersVisible() {
                if (!isPrealertMobile()) {
                    return;
                }
                prealertFilterIds.forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.prealert-filters-fields .mr-2').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                if (isPrealertMobile()) {
                    ensurePrealertMobileFiltersVisible();
                    return;
                }

                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Show ETL shipments', id: 'col-Show-ETL-shipments'},
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
            }

            toggleFilterVisibility();
            ensurePrealertMobileFiltersVisible();

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            $('#btn-prealert-filters-toggle').on('click', function () {
                $('body').toggleClass('prealert-filters-open');
                var isOpen = $('body').hasClass('prealert-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.prealert-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                if (isOpen) {
                    ensurePrealertMobileFiltersVisible();
                }
                setTimeout(function () {
                    table.columns.adjust();
                }, 50);
            });

            $(window).on('resize', function () {
                toggleFilterVisibility();
                ensurePrealertMobileFiltersVisible();
                table.columns.adjust();
            });

            setTimeout(function () {
                table.columns.adjust();
            }, 100);

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

            $('#filter-shipment-no, #filter-port-destination, #filter-customer, #filter-vessel, #filter-account-manager, #filter-status, #filter-created-by, #filter-show-etl').on('change keyup', function() {
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

                if ($('#filter-show-etl').is(':checked') && rowData($row, 'has-etl') !== '1') {
                    return false;
                }

                if (!matchesAnySelectedValues($('#filter-customer').val() || [], rowData($row, 'customers'))) {
                    return false;
                }

                if (!matchesAnySelectedValues($('#filter-vessel').val() || [], rowData($row, 'vessels'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#filter-account-manager').val() || [], rowData($row, 'account-manager'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#filter-created-by').val() || [], rowData($row, 'created-by'))) {
                    return false;
                }

                var selectedStatuses = $('#filter-status').val() || [];
                if (!matchesSelectedValues(selectedStatuses, rowData($row, 'status'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-shipment-no'), rowData($row, 'shipment-number'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-port-destination'), rowData($row, 'destination'))) {
                    return false;
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                clearSearchableFilterMultiselect(
                    '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by'
                );
                $('.filter-input:not(select)').val('').trigger('keyup');
                $('#filter-show-etl').prop('checked', false);
                table.columns().search('').draw();
            });

            @include('Shipment.partials.reminder-compose-script')
        });
    </script>
@endsection
