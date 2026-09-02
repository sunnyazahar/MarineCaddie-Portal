@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <style>
        /* Column widths: colgroup only (19 cols = 2491px total) */
        #invoicing-table-scroll #invoicing-table.office-table {
            width: 2491px !important;
            min-width: 2491px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            table-layout: fixed !important;
        }
        #invoicing-table th,
        #invoicing-table td {
            box-sizing: border-box !important;
            padding: 8px !important;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 0;
            text-align: left !important;
        }
        #invoicing-table thead th {
            color: #374151;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            background-color: #fdfdfd !important;
        }
        #invoicing-table tbody td {
            background-color: #fff;
        }
        #invoicing-table .invoicing-checkbox-cell,
        #invoicing-table .invoicing-status-cell,
        #invoicing-table .invoicing-action-cell,
        #invoicing-table td.dataTables_empty {
            max-width: none;
            overflow: visible !important;
            text-overflow: clip !important;
        }
        #invoicing-table .invoicing-checkbox-cell {
            text-align: center;
            padding-left: 10px !important;
            padding-right: 8px !important;
        }
        #invoicing-table .invoicing-status-cell {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        #invoicing-table .invoicing-action-cell {
            padding-left: 8px !important;
            padding-right: 10px !important;
        }
        .office-table thead {
            position: sticky !important;
            top: 0 !important;
            z-index: 20 !important;
            background-color: #fdfdfd !important;
        }
        .office-table thead th,
        #invoicing-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 21 !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }
        #invoicing-table thead th:first-child:after,
        #invoicing-table thead th:first-child:before {
            display: none !important;
            content: none !important;
        }
        #invoicing-table thead th.invoicing-checkbox-cell {
            position: sticky !important;
            left: 0 !important;
            top: 0 !important;
            z-index: 31 !important;
            box-shadow: 8px 0 10px -6px rgba(15, 23, 42, 0.14), 0 2px 2px -1px rgba(0, 0, 0, 0.1) !important;
        }
        #invoicing-table tbody td.invoicing-checkbox-cell {
            position: sticky !important;
            left: 0 !important;
            z-index: 13 !important;
            box-shadow: 8px 0 10px -6px rgba(15, 23, 42, 0.1);
        }
        #invoicing-table tbody tr:hover td.invoicing-checkbox-cell {
            background-color: #fff !important;
        }
        #invoicing-table thead th.invoicing-status-cell {
            position: sticky !important;
            right: 76px !important;
            top: 0 !important;
            z-index: 30 !important;
            box-shadow: -8px 0 10px -6px rgba(15, 23, 42, 0.14), 0 2px 2px -1px rgba(0, 0, 0, 0.1) !important;
        }
        #invoicing-table tbody td.invoicing-status-cell {
            position: sticky !important;
            right: 76px !important;
            z-index: 12 !important;
            box-shadow: -8px 0 10px -6px rgba(15, 23, 42, 0.1);
        }
        #invoicing-table tbody tr:hover td.invoicing-status-cell {
            background-color: #fff !important;
        }
        #invoicing-table thead th.invoicing-action-cell {
            position: sticky !important;
            right: 0 !important;
            top: 0 !important;
            z-index: 31 !important;
            box-shadow: none !important;
        }
        #invoicing-table tbody td.invoicing-action-cell {
            position: sticky !important;
            right: 0 !important;
            z-index: 13 !important;
            box-shadow: none;
        }
        #invoicing-table tbody tr:hover td.invoicing-action-cell {
            background-color: #fff !important;
        }
        .invoicing-status-badge {
            display: inline-block;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
            white-space: nowrap;
        }
        .invoicing-status-badge--ready {
            background-color: #fffbeb;
            border: 1px solid #fcd34d;
            color: #b45309;
        }
        .invoicing-status-badge--billed {
            background-color: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #047857;
        }
        .invoicing-status-badge--partial {
            background-color: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1d4ed8;
        }
        .invoicing-row-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 6px;
            vertical-align: middle;
        }
        .invoicing-row-action {
            width: 25px;
            height: 25px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            flex-shrink: 0;
            transition: transform 0.14s ease, box-shadow 0.14s ease, background-color 0.14s ease, border-color 0.14s ease, color 0.14s ease;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }
        .invoicing-row-action i {
            font-size: 15px;
            line-height: 1;
            color: inherit;
        }
        .invoicing-row-action:hover,
        .invoicing-row-action:focus {
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
        }
        .invoicing-row-action--edit {
            background-color: #fffbeb;
            border-color: #fcd34d;
            color: #b45309;
        }
        .invoicing-row-action--edit:hover,
        .invoicing-row-action--edit:focus {
            background-color: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }
        .invoicing-row-action--print {
            background-color: #ecfdf5;
            border-color: #6ee7b7;
            color: #047857;
        }
        .invoicing-row-action--print:hover,
        .invoicing-row-action--print:focus {
            background-color: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }
        #invoicing-table th, #invoicing-table td {
            white-space: nowrap !important;
        }
        #invoicing-table-scroll .office-table {
            min-width: 2491px !important;
        }

        body.billing-invoicing-list-page {
            overflow: hidden !important;
            height: var(--mc-app-vh, 100vh);
        }
        body.billing-invoicing-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.billing-invoicing-list-page .pcoded-inner-content,
        body.billing-invoicing-list-page .main-body,
        body.billing-invoicing-list-page .page-wrapper,
        body.billing-invoicing-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .billing-invoicing-list-card {
            display: flex;
            flex-direction: column;
            height: calc(var(--mc-app-vh, 100vh) - var(--mc-header-h, 64px));
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
        }
        .billing-invoicing-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 0 !important;
        }
        .billing-invoicing-list-card > .card-block > .list-page-header {
            flex-shrink: 0;
        }
        .billing-table-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .billing-table-area .table-scroll-wrapper {
            flex: 1;
            min-height: 0;
            overflow: auto !important;
            display: block;
            width: 100%;
            position: relative;
            padding-top: 2px;
        }
        #invoicing-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }
        .invoicing-consolidate-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 0;
        }
        .invoicing-consolidate-wrap[hidden] {
            display: none !important;
        }
        #btn-invoicing-consolidated-pdf {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        @media (max-width: 991.98px) {
            body.billing-invoicing-list-page {
                height: var(--mc-app-vh, 100svh) !important;
                max-height: var(--mc-app-vh, 100svh) !important;
            }
            .billing-invoicing-list-card {
                height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
                max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
            }
        }

        /* Beat page-local gutters on invoicing list shell */
        body.billing-invoicing-list-page .pcoded-inner-content,
        body.billing-invoicing-list-page .main-body .page-wrapper {
            padding: 0 !important;
        }
        .table-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #008080;
            vertical-align: middle;
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
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
            min-height: 30px !important;
            max-height: 30px !important;
            overflow: hidden !important;
        }
        .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            max-height: 28px !important;
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
        .filter-group .filter-date-icon {
            font-size: 14px;
            color: #64748b;
            padding: 0 8px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .filter-group .filter-date-icon:hover {
            color: #008080;
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
        .invoicing-filters-fixed {
            flex-shrink: 0;
            background: #fff;
            position: relative;
            z-index: 40;
            padding-bottom: 6px;
        }
        .invoicing-filters-toolbar {
            display: none;
        }
        @media (max-width: 991.98px) {
            .invoicing-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 4px 0 8px;
            }
            .invoicing-filters-fields .custom-col[style*="margin-left: auto"],
            .invoicing-filters-fields .custom-col.d-flex.justify-content-end {
                display: none !important;
            }
            .invoicing-filters-fields .list-dense-filter-controls {
                display: none !important;
            }
            .invoicing-filters-fields .filter-row {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .invoicing-filters-fields .custom-col,
            .invoicing-filters-fields .custom-col[style*="flex"] {
                flex: 1 1 100% !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            #btn-invoicing-filters-toggle.is-open {
                background-color: #008080;
                color: #fff;
                border-color: #008080;
            }
        }
        @media (min-width: 992px) {
            .invoicing-filters-toolbar {
                display: none !important;
            }
            .invoicing-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
        }
        .invoicing-filters-fields .list-dense-filter-row {
            margin-bottom: 0;
        }
        .invoicing-filters-fields .list-dense-filter-row:first-child {
            margin-bottom: 8px;
        }
    </style>
    <x-lists.multiselect-assets />
@endsection

@section('content')
    <div class="theme-loader">
        <div class="ball-scale">
            <div class="contain">
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card billing-invoicing-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Invoicing"
                subtitle="Search and manage proforma invoices"
                icon="ti-receipt"
                :count="$invoices->total()"
                countLabel="invoices"
            />
            <div class="invoicing-filters-fixed">
                <div class="invoicing-filters-toolbar">
                    <button type="button" id="btn-invoicing-filters-toggle" class="btn btn-outline-teal btn-sm">
                        <i class="ti-filter"></i> <span class="invoicing-filters-toggle-label">Show filters</span>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-start pt-2 invoicing-filters-fields list-dense-filter-bar">
                    <div class="list-dense-filter-shell" style="width: 100%;">
                        <div class="list-dense-filter-controls invoicing-filter-controls">
                            <select id="filter-multiselect" multiple="multiple" data-storage-key="invoicing-list-filters">
                                <option value="Invoice No" selected>Invoice No</option>
                                <option value="Customer" selected>Customer</option>
                                <option value="Vessel" selected>Vessel</option>
                                <option value="Acc Manager" selected>Acc Manager</option>
                                <option value="PO Number" selected>PO Number</option>
                                <option value="Job No" selected>Job No</option>
                                <option value="MAWB / MBL No" selected>MAWB / MBL No</option>
                                <option value="Status" selected>Status</option>
                                <option value="Date Range" selected>Date Range</option>
                            </select>
                        </div>
                        <div class="list-dense-filter-fields">
                            <div class="row custom-row filter-row list-dense-filter-row">
                                <div id="col-Invoice-no" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Invoice No</span>
                                        <input type="text" class="form-control filter-input" placeholder="search" value="{{ request('invoice_no', '') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Customer</span>
                                        <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer }}">{{ $customer }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Vessel</span>
                                        <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                            @foreach($vessels as $vessel)
                                                <option value="{{ $vessel }}">{{ $vessel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="col-Account-manager" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Acc Manager</span>
                                        <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                            @foreach($accountManagers as $accountManager)
                                                <option value="{{ $accountManager }}">{{ $accountManager }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="col-PO-number" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">PO Number</span>
                                        <input type="text" class="form-control filter-input" placeholder="search" autocomplete="off">
                                    </div>
                                </div>
                                <div id="col-Job-no" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Job No</span>
                                        <input type="text" class="form-control filter-input" placeholder="starts with">
                                    </div>
                                </div>
                            </div>
                            <div class="row custom-row filter-row list-dense-filter-row">
                                <div id="col-Mawb-mbl" class="custom-col" style="flex: 0 0 220px;">
                                    <div class="filter-group">
                                        <span class="filter-label">MAWB / MBL No</span>
                                        <input type="text" class="form-control filter-input" placeholder="starts with">
                                    </div>
                                </div>
                                <div id="col-Status" class="custom-col" style="flex: 0 0 200px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Status</span>
                                        <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                            <option value="Ready for billing">Ready for billing</option>
                                            <option value="Partially paid">Partially paid</option>
                                            <option value="Billed">Billed</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="col-Date-range" class="custom-col" style="flex: 0 0 220px;">
                                    <div class="filter-group">
                                        <span class="filter-label">Date Range</span>
                                        <input type="text" id="filter-invoicing-date-range" class="form-control filter-input" placeholder="Select range" autocomplete="off" readonly>
                                        <i class="ti-calendar filter-date-icon" title="Pick date range" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="custom-col">
                                    <x-lists.clear-filters id="clear-invoicing-filters" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="billing-table-area">
                <div id="invoicing-table-scroll" class="table-scroll-wrapper">
                <table id="invoicing-table" class="office-table">
                    <colgroup>
                        <col style="width: 40px">
                        <col style="width: 140px">
                        <col style="width: 105px">
                        <col style="width: 130px">
                        <col style="width: 130px">
                        <col style="width: 85px">
                        <col style="width: 160px">
                        <col style="width: 180px">
                        <col style="width: 260px">
                        <col style="width: 115px">
                        <col style="width: 125px">
                        <col style="width: 160px">
                        <col style="width: 95px">
                        <col style="width: 75px">
                        <col style="width: 100px">
                        <col style="width: 70px">
                        <col style="width: 130px">
                        <col style="width: 140px">
                        <col style="width: 76px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="invoicing-checkbox-cell">
                                <input type="checkbox" id="invoicing-check-all" class="table-checkbox" aria-label="Select all on this page">
                            </th>
                            <th>Service Type</th>
                            <th>Invoice Date</th>
                            <th>Invoice No.</th>
                            <th>Job No.</th>
                            <th>Job Date</th>
                            <th>Shipper Name</th>
                            <th>Consignee Name</th>
                            <th>Party Name</th>
                            <th>Port of Loading</th>
                            <th>Port of Discharge</th>
                            <th>PO No.</th>
                            <th>MBL No</th>
                            <th>Gross Wt.</th>
                            <th>Chargeable Wt.</th>
                            <th>Currency</th>
                            <th>Net Invoice Amount</th>
                            <th class="invoicing-status-cell">Status</th>
                            <th class="invoicing-action-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('Billing.partials.invoicing-rows')
                    </tbody>
                </table>
                </div>
            </div>
            <div id="invoicing-pagination" class="pagination-sticky-footer">
                <div id="invoicing-pagination-meta" class="list-pagination-meta">
                    @include('Billing.partials.invoicing-pagination-meta', ['paginator' => $invoices])
                </div>
                <div id="invoicing-consolidate-wrap" class="invoicing-consolidate-wrap" hidden>
                    <button type="button" id="btn-invoicing-consolidated-pdf" class="btn btn-sm btn-outline-teal">
                        Generate Consolidate Invoice
                    </button>
                </div>
                <div id="invoicing-pagination-links" class="list-pagination-links">
                    @include('Billing.partials.invoicing-pagination-links', ['paginator' => $invoices])
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('billing-invoicing-list-page');

            function syncInvoicingCheckAll() {
                var $rows = $('#invoicing-table tbody .invoicing-row-checkbox');
                var $checkAll = $('#invoicing-check-all');
                var checked = $rows.filter(':checked').length;
                var total = $rows.length;

                if (total === 0) {
                    $checkAll.prop('checked', false).prop('indeterminate', false);
                    return;
                }

                $checkAll.prop('checked', checked === total);
                $checkAll.prop('indeterminate', checked > 0 && checked < total);
            }

            $(document).on('change', '#invoicing-table tbody .invoicing-row-checkbox', function() {
                syncInvoicingCheckAll();
                syncInvoicingConsolidateAction();
            });

            function syncInvoicingConsolidateAction() {
                var $checked = $('#invoicing-table tbody .invoicing-row-checkbox:checked');
                var $wrap = $('#invoicing-consolidate-wrap');

                if ($checked.length < 2) {
                    $wrap.prop('hidden', true);
                    return;
                }

                var poNo = null;
                var partyName = null;
                var canConsolidate = true;

                $checked.each(function() {
                    var rowPo = $.trim($(this).data('po-no') || '');
                    var rowParty = $.trim($(this).data('party-name') || '');

                    if (rowPo === '' || rowParty === '') {
                        canConsolidate = false;
                        return false;
                    }

                    if (poNo === null) {
                        poNo = rowPo;
                        partyName = rowParty;
                        return;
                    }

                    if (rowPo !== poNo || rowParty !== partyName) {
                        canConsolidate = false;
                        return false;
                    }
                });

                $wrap.prop('hidden', !canConsolidate);
            }

            $('#invoicing-check-all').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('#invoicing-table tbody .invoicing-row-checkbox').prop('checked', isChecked);
                $(this).prop('indeterminate', false);
                syncInvoicingConsolidateAction();
            });

            function loadInvoicingOnFilterChange() {
                if (window.invoicingListFilters) {
                    window.invoicingListFilters.load(1);
                }
            }

            function queueInvoicingFilterLoad() {
                if (window.invoicingListFilters) {
                    window.invoicingListFilters.queueLoad(1);
                }
            }

            var invoicingDateFrom = '';
            var invoicingDateTo = '';
            var $invoicingDateRange = $('#filter-invoicing-date-range');

            function clearInvoicingDateRange() {
                invoicingDateFrom = '';
                invoicingDateTo = '';
                $invoicingDateRange.val('');

                var picker = $invoicingDateRange.data('daterangepicker');
                if (picker) {
                    picker.setStartDate(moment());
                    picker.setEndDate(moment());
                }
            }

            $invoicingDateRange.daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    format: 'DD.MM.YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $invoicingDateRange.on('apply.daterangepicker', function (ev, picker) {
                invoicingDateFrom = picker.startDate.format('YYYY-MM-DD');
                invoicingDateTo = picker.endDate.format('YYYY-MM-DD');
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
                loadInvoicingOnFilterChange();
            });

            $invoicingDateRange.on('cancel.daterangepicker', function () {
                clearInvoicingDateRange();
                loadInvoicingOnFilterChange();
            });

            $('#col-Date-range .filter-date-icon').on('click', function () {
                $invoicingDateRange.trigger('focus');
                $invoicingDateRange.data('daterangepicker').show();
            });

            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                includeResetOption: true,
                resetText: 'Clear all',
                storageKey: 'invoicing-list-filters',
                onChange: function () {
                    toggleInvoicingFilterVisibility();
                },
                onSelectAll: function () {
                    toggleInvoicingFilterVisibility();
                },
                onDeselectAll: function () {
                    toggleInvoicingFilterVisibility();
                }
            });

            $('#filter-multiselect').multiselect('selectAll', false);
            $('#filter-multiselect').multiselect('updateButtonText');
            toggleInvoicingFilterVisibility();

            function isInvoicingMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureInvoicingMobileFiltersVisible() {
                if (!isInvoicingMobile()) {
                    return;
                }
                [
                    'col-Invoice-no',
                    'col-Customer',
                    'col-Vessel',
                    'col-Account-manager',
                    'col-PO-number',
                    'col-Job-no',
                    'col-Mawb-mbl',
                    'col-Status',
                    'col-Date-range'
                ].forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.invoicing-filter-controls').hide();
                var $panel = $('#filter-multiselect').data('mcColumnPickerPanel');
                if ($panel && $panel.length) {
                    $panel.removeClass('is-open');
                }
            }

            function toggleInvoicingFilterVisibility() {
                if (isInvoicingMobile()) {
                    ensureInvoicingMobileFiltersVisible();
                    setTimeout(adjustInvoicingTableLayout, 50);
                    return;
                }

                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Invoice No', id: 'col-Invoice-no'},
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Acc Manager', id: 'col-Account-manager'},
                    {val: 'PO Number', id: 'col-PO-number'},
                    {val: 'Job No', id: 'col-Job-no'},
                    {val: 'MAWB / MBL No', id: 'col-Mawb-mbl'},
                    {val: 'Status', id: 'col-Status'},
                    {val: 'Date Range', id: 'col-Date-range'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });

                setTimeout(adjustInvoicingTableLayout, 50);
            }

            ensureInvoicingMobileFiltersVisible();
            $(window).on('resize.invoicingFilters', ensureInvoicingMobileFiltersVisible);

            var table = $('#invoicing-table').DataTable({
                dom: 'rt',
                lengthChange: false,
                paging: false,
                info: false,
                responsive: false,
                searching: false,
                ordering: true,
                order: [],
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: 0 },
                    { searchable: false, targets: 0 },
                    { orderable: false, targets: 17 },
                    { orderable: false, targets: 18 },
                    { targets: 0, width: '40px' }
                ]
            });

            function getInvoicingTableScrollHeight() {
                var isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                var $tableArea = $('.billing-table-area');
                var areaHeight = $tableArea.length ? $tableArea.innerHeight() : 0;
                var available = areaHeight - 2;

                if (isMobile) {
                    var paginationHeight = $('#invoicing-pagination').outerHeight() || 52;
                    var topOffset = $tableArea.length && $tableArea.offset()
                        ? $tableArea.offset().top
                        : 160;
                    available = window.innerHeight - topOffset - paginationHeight;
                    return Math.max(260, available);
                }

                if (available < 180) {
                    var topOffsetFallback = $tableArea.length ? $tableArea.offset().top : 220;
                    var paginationHeightFallback = $('#invoicing-pagination').outerHeight() || 52;
                    available = window.innerHeight - topOffsetFallback - paginationHeightFallback;
                }

                return Math.max(180, available);
            }

            function adjustInvoicingTableLayout() {
                var height = getInvoicingTableScrollHeight();
                $('#invoicing-table-scroll').css({
                    height: height + 'px',
                    maxHeight: height + 'px',
                });
            }

            $('#btn-invoicing-filters-toggle').on('click', function () {
                $('body').toggleClass('invoicing-filters-open');
                var isOpen = $('body').hasClass('invoicing-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.invoicing-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                if (isOpen) {
                    ensureInvoicingMobileFiltersVisible();
                }
                setTimeout(adjustInvoicingTableLayout, 50);
                setTimeout(adjustInvoicingTableLayout, 200);
            });

            window.invoicingListFilters = bindAjaxListFilters({
                tableSelector: '#invoicing-table',
                paginationSelector: '#invoicing-pagination-links',
                paginationMetaSelector: '#invoicing-pagination-meta',
                indexUrl: @json(route('billing.invoicing')),
                existingTable: table,
                clearSelector: '#clear-invoicing-filters',
                debounceMs: 200,
                getParams: function (page) {
                    return {
                        invoice_no: $.trim($('#col-Invoice-no input').val() || ''),
                        customer: $('#col-Customer select').val() || [],
                        vessel: $('#col-Vessel select').val() || [],
                        account_manager: $('#col-Account-manager select').val() || [],
                        po_number: $.trim($('#col-PO-number input').val() || ''),
                        job_no: $.trim($('#col-Job-no input').val() || ''),
                        mawb_mbl: $.trim($('#col-Mawb-mbl input').val() || ''),
                        status: $('#col-Status select').val() || [],
                        date_from: invoicingDateFrom,
                        date_to: invoicingDateTo,
                        page: page || 1
                    };
                },
                textSelectors: '#col-Invoice-no input, #col-PO-number input, #col-Job-no input, #col-Mawb-mbl input',
                resetFields: function () {
                    clearSearchableFilterMultiselect('.searchable-filter-multiselect', false);
                    $('#col-Invoice-no input, #col-PO-number input, #col-Job-no input, #col-Mawb-mbl input').val('');
                    clearInvoicingDateRange();
                },
                afterDraw: function () {
                    $('#invoicing-check-all').prop('checked', false).prop('indeterminate', false);
                    syncInvoicingCheckAll();
                    syncInvoicingConsolidateAction();
                    adjustInvoicingTableLayout();
                }
            });

            $('#btn-invoicing-consolidated-pdf').on('click', function () {
                var jobNumbers = $('#invoicing-table tbody .invoicing-row-checkbox:checked')
                    .map(function () {
                        return $.trim($(this).val() || '');
                    })
                    .get()
                    .filter(function (value) {
                        return value !== '';
                    });

                if (jobNumbers.length < 2) {
                    return;
                }

                var url = @json(route('billing.invoicing.consolidated-print')) + '?' + $.param({ job_no: jobNumbers });
                window.open(url, '_blank', 'noopener,noreferrer');
            });

            initializeSearchableFilterMultiselect('.searchable-filter-multiselect', {
                onChange: queueInvoicingFilterLoad,
                onSelectAll: queueInvoicingFilterLoad,
                onDeselectAll: queueInvoicingFilterLoad
            });

            table.on('draw', function() {
                adjustInvoicingTableLayout();
            });

            $(window).on('resize', adjustInvoicingTableLayout);
            setTimeout(adjustInvoicingTableLayout, 100);
            setTimeout(adjustInvoicingTableLayout, 400);
            syncInvoicingCheckAll();
        });
    </script>
@endpush
