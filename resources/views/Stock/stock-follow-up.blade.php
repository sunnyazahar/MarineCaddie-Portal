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
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <style>
        .office-table {
            width: 1900px !important;
            min-width: 1900px !important;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }
        .office-table th {
            text-align: left;
            padding: 10px 8px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .office-table td {
            padding: 6px 8px;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            margin-top: 25px;
            display: inline-block;
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
        @media (max-width: 576px) {
            .custom-col {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
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
        .shipment-badge {
            background-color: #fde68a;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
        }
        .po-badge {
            background-color: #fecaca;
            color: #b91c1c;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-stock {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            border: none !important;
        }
        .badge-pending {
            background-color: #ffedd5 !important;
            color: #9a3412 !important;
            border: none !important;
        }
        .badge-transit {
            background-color: #e0f2fe !important;
            color: #075985 !important;
            border: none !important;
        }
        .badge-on-call {
            background-color: #f3f4f6 !important;
            color: #374151 !important;
            border: none !important;
        }
        .icon-doc-blue {
            color: #0ea5e9;
            margin-left: 5px;
        }
        .icon-warning-red {
            color: #ef4444;
            margin-right: 5px;
        }
        .icon-info-yellow {
            color: #f59e0b;
            margin-left: 5px;
        }
        .text-pending {
            color: #f59e0b !important;
        }
        .badge-landed {
            background-color: #f0f9ff;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            border: 1px solid #bae6fd;
            margin-right: 5px;
        }
        .btn-accept {
            color: #008080;
            background: transparent;
            border: 1px solid #e5e7eb;
            padding: 2px 10px;
            font-size: 11px;
            border-radius: 4px;
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
        .stock-followup-table-area {
            min-height: 0;
        }
        .table-scroll-wrapper {
            width: 100%;
            overflow: auto !important;
            max-height: calc(100vh - 280px);
            -webkit-overflow-scrolling: touch;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        .office-table {
            width: 1900px !important;
            min-width: 1900px !important;
            max-width: none !important;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }
        .office-table th:nth-child(1),
        .office-table td:nth-child(1) { width: 90px; min-width: 90px; }
        .office-table th:nth-child(2),
        .office-table td:nth-child(2) { width: 180px; min-width: 180px; }
        .office-table th:nth-child(3),
        .office-table td:nth-child(3) { width: 240px; min-width: 240px; }
        .office-table th:nth-child(4),
        .office-table td:nth-child(4) { width: 180px; min-width: 180px; }
        .office-table th:nth-child(5),
        .office-table td:nth-child(5) { width: 200px; min-width: 200px; }
        .office-table th:nth-child(6),
        .office-table td:nth-child(6) { width: 180px; min-width: 180px; }
        .office-table th:nth-child(7),
        .office-table td:nth-child(7) { width: 60px; min-width: 60px; }
        .office-table th:nth-child(8),
        .office-table td:nth-child(8) { width: 80px; min-width: 80px; }
        .office-table th:nth-child(9),
        .office-table td:nth-child(9) { width: 110px; min-width: 110px; }
        .office-table th:nth-child(10),
        .office-table td:nth-child(10) { width: 140px; min-width: 140px; }
        .office-table th:nth-child(11),
        .office-table td:nth-child(11) { width: 140px; min-width: 140px; }
        .office-table th:nth-child(12),
        .office-table td:nth-child(12) { width: 90px; min-width: 90px; }
        .office-table th:nth-child(13),
        .office-table td:nth-child(13) { width: 110px; min-width: 110px; }
        .office-table th:nth-child(14),
        .office-table td:nth-child(14) { width: 90px; min-width: 90px; }
        .office-table .cell-ellipsis {
            display: block;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
        .office-table td.stock-no-cell {
            overflow: hidden;
        }
        .office-table .stock-no-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-width: 0;
            max-width: 100%;
        }
        .office-table .stock-no-row a {
            min-width: 0;
            flex: 1 1 0%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .office-table .stock-no-flags {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        .stock-followup-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 12px 16px;
            background: #fff;
            padding: 12px 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            max-width: 100%;
        }
        .stock-followup-filter-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            flex: 1 1 200px;
            max-width: 280px;
        }
        .stock-followup-filter-item .filter-control {
            width: 100%;
            min-width: 0;
        }
        .stock-followup-filters .clear-filters {
            margin-top: 0;
            align-self: center;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .stock-followup-filter-item {
                flex: 1 1 100%;
                max-width: 100%;
            }
            .stock-followup-filters .clear-filters {
                width: 100%;
            }
            .table-scroll-wrapper {
                max-height: none;
                max-height: calc(100vh - 220px);
            }
            .pagination-sticky-footer {
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                padding: 8px 12px !important;
                height: auto !important;
                min-height: 48px;
                justify-content: center !important;
                overflow-x: auto;
            }
            .card-block {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
        }
        .office-table thead {
            position: sticky !important;
            top: 0 !important;
            z-index: 20 !important;
            background-color: #fdfdfd !important;
        }
        .office-table thead th {
            position: sticky !important;
            top: 0 !important;
            background-color: #fdfdfd !important;
            z-index: 21 !important;
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
    @include('layouts.partials.pcoded-shell-start')
                                        <!-- Base Style - Compact start -->
                                        <div class="card" style="border: none; box-shadow: none; background: transparent;">
                                            <div class="card-block" style="padding: 10px 0;">
                                                <!-- Filter Row -->
                                                <div class="stock-followup-filters">
                                                    <div class="stock-followup-filter-item">
                                                        <span class="filter-label" style="margin-bottom: 0;">Account manager</span>
                                                        <div class="filter-control">
                                                            <select id="filter-account-manager" class="form-control searchable-filter-multiselect" multiple="multiple">
                                                                @foreach ($accountManagers as $manager)
                                                                    <option value="{{ $manager }}">{{ $manager }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="stock-followup-filter-item">
                                                        <span class="filter-label" style="margin-bottom: 0;">Customer</span>
                                                        <div class="filter-control">
                                                            <select id="filter-customer" class="form-control searchable-filter-multiselect" multiple="multiple">
                                                                @foreach ($customers as $customer)
                                                                    <option value="{{ $customer }}">{{ $customer }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="clear-filters">Clear filters</a>
                                                </div>

                                                <!-- Data Table -->
                                                <div class="stock-followup-table-area" style="background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                    <table id="offices-table" class="office-table">
                                                        <colgroup>
                                                            <col style="width: 90px">
                                                            <col style="width: 180px">
                                                            <col style="width: 240px">
                                                            <col style="width: 180px">
                                                            <col style="width: 200px">
                                                            <col style="width: 180px">
                                                            <col style="width: 60px">
                                                            <col style="width: 80px">
                                                            <col style="width: 110px">
                                                            <col style="width: 140px">
                                                            <col style="width: 140px">
                                                            <col style="width: 90px">
                                                            <col style="width: 110px">
                                                            <col style="width: 90px">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>Hub</th>
                                                                <th>Stock number</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>PO numbers</th>
                                                                <th>Supplier</th>
                                                                <th class="text-center">Items</th>
                                                                <th class="text-center">Weight</th>
                                                                <th class="text-right">Value</th>
                                                                <th>Shipment</th>
                                                                <th>Reg.by</th>
                                                                <th>ETL</th>
                                                                <th>Status</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($crrs as $crr)
                                                            @php
                                                                $customerName = $crr->customerVessel?->customer?->customer_name ?? '';
                                                                $accountManager = $crr->customerVessel?->account_manager
                                                                    ?: $crr->customerVessel?->customer?->responsible?->accountManager?->name
                                                                    ?: '';
                                                                $poNumbers = is_array($crr->po_numbers) ? $crr->po_numbers : [];
                                                                $totalItems = $crr->packages->count();
                                                                $totalWeight = $crr->packages->sum('weight');
                                                                $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                                                $hasDocs = $crr->documents->isNotEmpty();
                                                                $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                                                $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                                                $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities, true);
                                                                $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                                                $valueDisplay = $crr->customs_value
                                                                    ? number_format((float) $crr->customs_value, 2) . ' ' . ($crr->currency ?: 'USD')
                                                                    : '—';
                                                                $isEtl = strtoupper((string) $crr->internal_shipment) === 'ETL';
                                                            @endphp
                                                            <tr
                                                                data-account-manager="{{ $accountManager }}"
                                                                data-customer="{{ $customerName }}"
                                                                data-has-etl="{{ $isEtl ? '1' : '0' }}"
                                                            >
                                                                <td title="{{ $crr->hub_code ?? ($crr->hub_agent ?? '—') }}"><span class="cell-ellipsis">{{ $crr->hub_code ?? ($crr->hub_agent ?? '—') }}</span></td>
                                                                <td class="stock-no-cell">
                                                                    <div class="stock-no-row">
                                                                        <a href="{{ route('stocks.edit', $crr->id) }}" style="color: #0ea5e9;" title="{{ $crr->stock_number }}">{{ $crr->stock_number }}</a>
                                                                        <div class="stock-no-flags">
                                                                            @if($crr->is_landed_goods)
                                                                                <span class="badge-landed" title="Landed Goods">Landed</span>
                                                                            @endif
                                                                            @if($hasDgr)
                                                                                <i class="icofont icofont-warning text-danger" title="Dangerous Goods" style="font-size: 15px;"></i>
                                                                            @endif
                                                                            @if($hasDocs)
                                                                                <i class="icofont icofont-file-alt text-muted" title="Documents Attached" style="font-size: 15px; color: #64748b !important;"></i>
                                                                            @endif
                                                                            @if($hasMedicine)
                                                                                <i class="icofont icofont-first-aid text-success" title="Medicine" style="font-size: 15px;"></i>
                                                                            @endif
                                                                            @if($hasDeliveryIrreg)
                                                                                <i class="icofont icofont-info-circle text-pending" title="Delivery irregularities - missing info" style="font-size: 15px;"></i>
                                                                            @endif
                                                                            @if($isNotStackable)
                                                                                <i class="icofont icofont-info-square text-warning" title="Non-Stackable Content" style="font-size: 15px;"></i>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td title="{{ $customerName ?: '—' }}"><span class="cell-ellipsis">{{ $customerName ?: '—' }}</span></td>
                                                                <td title="{{ $crr->vessel_name ?? '—' }}"><span class="cell-ellipsis">{{ $crr->vessel_name ?? '—' }}</span></td>
                                                                <td title="{{ $poNumbers ? implode(', ', $poNumbers) : '—' }}">
                                                                    <span class="cell-ellipsis">
                                                                    @forelse ($poNumbers as $poNumber)
                                                                        <span class="po-badge">{{ $poNumber }}</span>
                                                                    @empty
                                                                        —
                                                                    @endforelse
                                                                    </span>
                                                                </td>
                                                                <td title="{{ $crr->supplier ?? '—' }}"><span class="cell-ellipsis">{{ $crr->supplier ?? '—' }}</span></td>
                                                                <td class="text-center">{{ $totalItems ?: '—' }}</td>
                                                                <td class="text-center">{{ $totalWeight > 0 ? number_format((float) $totalWeight, 1) : '—' }}</td>
                                                                <td class="text-right" title="{{ $valueDisplay }}"><span class="cell-ellipsis">{{ $valueDisplay }}</span></td>
                                                                <td title="{{ $crr->shipments->pluck('shipment_number')->filter()->implode(', ') ?: '—' }}">
                                                                    <span class="cell-ellipsis">
                                                                    @forelse ($crr->shipments as $shipment)
                                                                        <span class="shipment-badge">{{ $shipment->shipment_number }}</span>
                                                                    @empty
                                                                        —
                                                                    @endforelse
                                                                    </span>
                                                                </td>
                                                                <td title="{{ $crr->registeredBy?->name ?? '—' }}"><span class="cell-ellipsis">{{ $crr->registeredBy?->name ?? '—' }}</span></td>
                                                                <td title="{{ $isEtl ? 'ETL' : ($crr->internal_shipment ?: '—') }}"><span class="cell-ellipsis">{{ $isEtl ? 'ETL' : ($crr->internal_shipment ?: '—') }}</span></td>
                                                                <td><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span></td>
                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn-accept accept-stock-btn"
                                                                        data-crr-id="{{ $crr->id }}"
                                                                        data-stock-number="{{ $crr->stock_number }}"
                                                                        data-accept-url="{{ route('stocks.crr.update-accept', $crr->id) }}">Accept</button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="14" class="text-center py-4 text-muted">No stocks found.</td>
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
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            initializeSearchableFilterMultiselect('#filter-account-manager, #filter-customer');

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 200,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [13] },
                    { "targets": 2, "width": "240px" },
                    { "targets": 4, "width": "200px" },
                    { "targets": 5, "width": "180px" }
                ]
            });

            function getFollowupTableScrollHeight() {
                var paginationHeight = $('.pagination-sticky-footer').outerHeight() || 48;
                var $wrapper = $('.table-scroll-wrapper');
                var topOffset = $wrapper.length && $wrapper.offset()
                    ? $wrapper.offset().top
                    : 220;
                return Math.max(180, window.innerHeight - topOffset - paginationHeight - 8);
            }

            function adjustFollowupTableLayout() {
                var height = getFollowupTableScrollHeight();
                $('.stock-followup-table-area .table-scroll-wrapper').css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });
            }

            $(window).on('resize', adjustFollowupTableLayout);
            table.on('draw', adjustFollowupTableLayout);
            setTimeout(adjustFollowupTableLayout, 100);
            setTimeout(adjustFollowupTableLayout, 400);

            $('#filter-account-manager, #filter-customer').on('change', function() {
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
                var selectedManagers = $('#filter-account-manager').val() || [];
                if (selectedManagers.length > 0) {
                    var rowManager = String($row.data('account-manager') || '');
                    if (!selectedManagers.includes(rowManager)) {
                        return false;
                    }
                }

                var selectedCustomers = $('#filter-customer').val() || [];
                if (selectedCustomers.length > 0) {
                    var rowCustomer = String($row.data('customer') || '');
                    if (!selectedCustomers.includes(rowCustomer)) {
                        return false;
                    }
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                clearSearchableFilterMultiselect('#filter-account-manager, #filter-customer');
                table.search('').columns().search('').draw();
            });

            function acceptStock($button) {
                var acceptUrl = $button.data('accept-url');
                var stockNumber = $button.data('stock-number');
                var $row = $button.closest('tr');

                $.ajax({
                    url: acceptUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken
                    }
                }).done(function(response) {
                    if (!response || !response.success) {
                        alert('Could not accept stock.');
                        return;
                    }

                    if (typeof swal === 'function') {
                        swal.close();
                    }

                    table.row($row).remove().draw(false);
                }).fail(function(xhr) {
                    if (typeof swal === 'function') {
                        swal.close();
                    }

                    var message = 'Could not accept stock.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    alert(message);
                });
            }

            $(document).on('click', '.accept-stock-btn', function() {
                var $button = $(this);
                var stockNumber = $button.data('stock-number');
                var message = 'Accept stock ' + stockNumber + '?';

                if (typeof swal !== 'function') {
                    if (confirm(message)) {
                        acceptStock($button);
                    }
                    return;
                }

                swal({
                    title: 'Accept stock?',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, accept',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    acceptStock($button);
                });
            });
        });
    </script>
@endsection
