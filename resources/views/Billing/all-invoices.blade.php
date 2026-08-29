@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <style>
        /* High Density Table Styles */
        #invoicing-table {
            width: 100% !important;
            min-width: 2660px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            table-layout: fixed !important;
        }
        #invoicing-table tbody td {
            padding: 6px 8px !important;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 0;
        }
        #invoicing-table tbody td.invoicing-action-cell,
        #invoicing-table tbody td.invoicing-checkbox-cell,
        #invoicing-table tbody td.dataTables_empty {
            overflow: visible !important;
            text-overflow: clip !important;
            max-width: none;
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
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none !important;
            letter-spacing: normal !important;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            vertical-align: middle;
            overflow: visible !important;
            text-overflow: clip !important;
        }
        #invoicing-table thead th:first-child:after,
        #invoicing-table thead th:first-child:before {
            display: none !important;
            content: none !important;
        }
        #invoicing-table th:nth-child(1),
        #invoicing-table td:nth-child(1) { width: 40px; min-width: 40px; }
        #invoicing-table th.invoicing-checkbox-cell,
        #invoicing-table td.invoicing-checkbox-cell {
            overflow: visible !important;
            text-overflow: clip !important;
            max-width: none;
            text-align: center;
            vertical-align: middle;
        }
        #invoicing-table thead th.invoicing-checkbox-cell {
            position: sticky !important;
            left: 0 !important;
            top: 0 !important;
            z-index: 31 !important;
            background-color: #fdfdfd !important;
            box-shadow: 8px 0 10px -6px rgba(15, 23, 42, 0.14), 0 2px 2px -1px rgba(0, 0, 0, 0.1) !important;
        }
        #invoicing-table tbody td.invoicing-checkbox-cell {
            position: sticky !important;
            left: 0 !important;
            z-index: 13 !important;
            background-color: #fff !important;
            box-shadow: 8px 0 10px -6px rgba(15, 23, 42, 0.1);
            padding-left: 10px !important;
        }
        #invoicing-table tbody tr:hover td.invoicing-checkbox-cell {
            background-color: #fff !important;
        }
        #invoicing-table th:nth-child(2),
        #invoicing-table td:nth-child(2) { width: 105px; min-width: 105px; }
        #invoicing-table th:nth-child(3),
        #invoicing-table td:nth-child(3) { width: 115px; min-width: 115px; }
        #invoicing-table th:nth-child(4),
        #invoicing-table td:nth-child(4) { width: 100px; min-width: 100px; }
        #invoicing-table th:nth-child(5),
        #invoicing-table td:nth-child(5) { width: 105px; min-width: 105px; }
        #invoicing-table th:nth-child(6),
        #invoicing-table td:nth-child(6) { width: 85px; min-width: 85px; }
        #invoicing-table th:nth-child(7),
        #invoicing-table td:nth-child(7) { width: 85px; min-width: 85px; }
        #invoicing-table th:nth-child(8),
        #invoicing-table td:nth-child(8) { width: 160px; min-width: 160px; }
        #invoicing-table th:nth-child(9),
        #invoicing-table td:nth-child(9) { width: 180px; min-width: 180px; }
        #invoicing-table th:nth-child(10),
        #invoicing-table td:nth-child(10) { width: 260px; min-width: 260px; }
        #invoicing-table th:nth-child(11),
        #invoicing-table td:nth-child(11) { width: 115px; min-width: 115px; }
        #invoicing-table th:nth-child(12),
        #invoicing-table td:nth-child(12) { width: 125px; min-width: 125px; }
        #invoicing-table th:nth-child(13),
        #invoicing-table td:nth-child(13) { width: 200px; min-width: 200px; }
        #invoicing-table th:nth-child(14),
        #invoicing-table td:nth-child(14) { width: 80px; min-width: 80px; }
        #invoicing-table th:nth-child(15),
        #invoicing-table td:nth-child(15) { width: 95px; min-width: 95px; }
        #invoicing-table th:nth-child(16),
        #invoicing-table td:nth-child(16) { width: 75px; min-width: 75px; }
        #invoicing-table th:nth-child(17),
        #invoicing-table td:nth-child(17) { width: 100px; min-width: 100px; }
        #invoicing-table th:nth-child(18),
        #invoicing-table td:nth-child(18) { width: 70px; min-width: 70px; }
        #invoicing-table th:nth-child(19),
        #invoicing-table td:nth-child(19) { width: 95px; min-width: 95px; }
        #invoicing-table th:nth-child(20),
        #invoicing-table td:nth-child(20) { width: 130px; min-width: 130px; }
        #invoicing-table th:nth-child(21),
        #invoicing-table td:nth-child(21) { width: 152px; min-width: 152px; }
        #invoicing-table thead th.invoicing-action-cell {
            position: sticky !important;
            right: 0 !important;
            top: 0 !important;
            z-index: 30 !important;
            background-color: #fdfdfd !important;
            box-shadow: -8px 0 10px -6px rgba(15, 23, 42, 0.14), 0 2px 2px -1px rgba(0, 0, 0, 0.1) !important;
        }
        #invoicing-table tbody td.invoicing-action-cell {
            position: sticky !important;
            right: 0 !important;
            z-index: 12 !important;
            background-color: #fff !important;
            box-shadow: -8px 0 10px -6px rgba(15, 23, 42, 0.1);
            padding-right: 10px !important;
        }
        #invoicing-table tbody tr:hover td.invoicing-action-cell {
            background-color: #fff !important;
        }
        .invoicing-row-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            vertical-align: middle;
        }
        .invoicing-row-action {
            width: 32px;
            height: 32px;
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
        .invoicing-row-action--copy {
            background-color: #eff6ff;
            border-color: #93c5fd;
            color: #1d4ed8;
        }
        .invoicing-row-action--copy:hover,
        .invoicing-row-action--copy:focus {
            background-color: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
        }
        .invoicing-row-action--delete {
            background-color: #fef2f2;
            border-color: #fca5a5;
            color: #b91c1c;
        }
        .invoicing-row-action--delete:hover,
        .invoicing-row-action--delete:focus {
            background-color: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }
        #invoicing-table th, #invoicing-table td {
            white-space: nowrap !important;
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
          .status-paid { background-color: #dbeafe; color: #1e40af; }
        
</style>
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
            <div class="billing-table-area">
                <div id="invoicing-table-scroll" class="table-scroll-wrapper">
                <table id="invoicing-table" class="office-table">
                    <colgroup>
                        <col style="width: 40px">
                        <col style="width: 105px">
                        <col style="width: 115px">
                        <col style="width: 100px">
                        <col style="width: 105px">
                        <col style="width: 85px">
                        <col style="width: 85px">
                        <col style="width: 160px">
                        <col style="width: 180px">
                        <col style="width: 260px">
                        <col style="width: 115px">
                        <col style="width: 125px">
                        <col style="width: 200px">
                        <col style="width: 80px">
                        <col style="width: 95px">
                        <col style="width: 75px">
                        <col style="width: 100px">
                        <col style="width: 70px">
                        <col style="width: 95px">
                        <col style="width: 130px">
                        <col style="width: 152px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="invoicing-checkbox-cell">
                                <input type="checkbox" id="invoicing-check-all" class="table-checkbox" aria-label="Select all on this page">
                            </th>
                            <th>Service Type</th>
                            <th>EInvoice Status</th>
                            <th>Proforma No</th>
                            <th>Proforma Date</th>
                            <th>Job No</th>
                            <th>Job Date</th>
                            <th>Shipper Name</th>
                            <th>Consignee Name</th>
                            <th>Party Name</th>
                            <th>Port of Loading</th>
                            <th>Port of Discharge</th>
                            <th>Client Ref No</th>
                            <th>SB / BE No</th>
                            <th>MBL No</th>
                            <th>Gross Wt.</th>
                            <th>Chargeable Wt.</th>
                            <th>Currency</th>
                            <th>GST Amount</th>
                            <th>Net Invoice Amount</th>
                            <th class="invoicing-action-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $row)
                        <tr>
                            <td class="invoicing-checkbox-cell">
                                <input type="checkbox" class="table-checkbox invoicing-row-checkbox" value="{{ $row['proforma_no'] }}" aria-label="Select row">
                            </td>
                            <td title="{{ $row['service_type'] }}">{{ $row['service_type'] }}</td>
                            <td title="{{ $row['einvoice_status'] }}">{{ $row['einvoice_status'] }}</td>
                            <td title="{{ $row['proforma_no'] }}">{{ $row['proforma_no'] }}</td>
                            <td title="{{ $row['proforma_date'] }}">{{ $row['proforma_date'] }}</td>
                            <td title="{{ $row['job_no'] }}">{{ $row['job_no'] }}</td>
                            <td title="{{ $row['job_date'] }}">{{ $row['job_date'] }}</td>
                            <td title="{{ $row['shipper_name'] }}">{{ $row['shipper_name'] }}</td>
                            <td title="{{ $row['consignee_name'] }}">{{ $row['consignee_name'] }}</td>
                            <td title="{{ $row['party_name'] }}">{{ $row['party_name'] }}</td>
                            <td title="{{ $row['port_of_loading'] }}">{{ $row['port_of_loading'] }}</td>
                            <td title="{{ $row['port_of_discharge'] }}">{{ $row['port_of_discharge'] }}</td>
                            <td title="{{ $row['client_ref_no'] }}">{{ $row['client_ref_no'] }}</td>
                            <td title="{{ $row['sb_be_no'] }}">{{ $row['sb_be_no'] }}</td>
                            <td title="{{ $row['mbl_no'] }}">{{ $row['mbl_no'] }}</td>
                            <td class="text-right" title="{{ $row['gross_wt'] }}">{{ $row['gross_wt'] }}</td>
                            <td class="text-right" title="{{ $row['chargeable_wt'] }}">{{ $row['chargeable_wt'] }}</td>
                            <td title="{{ $row['currency'] }}">{{ $row['currency'] }}</td>
                            <td class="text-right" title="{{ $row['gst_amount'] }}">{{ $row['gst_amount'] }}</td>
                            <td class="text-right" title="{{ $row['net_invoice_amount'] }}">{{ $row['net_invoice_amount'] }}</td>
                            <td class="invoicing-action-cell">
                                <div class="invoicing-row-actions" role="group" aria-label="Invoice actions">
                                    <a href="{{ route('billing.invoicing.edit', ['proformaNo' => $row['proforma_no']]) }}" class="invoicing-row-action invoicing-row-action--edit" title="Edit invoice" aria-label="Edit invoice">
                                        <i class="feather icon-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="invoicing-row-action invoicing-row-action--print" title="Print invoice" aria-label="Print invoice">
                                        <i class="feather icon-printer"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="invoicing-row-action invoicing-row-action--copy" title="Print copy" aria-label="Print copy">
                                        <i class="feather icon-copy"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="invoicing-row-action invoicing-row-action--delete" title="Delete invoice" aria-label="Delete invoice">
                                        <i class="feather icon-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div id="invoicing-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $invoices])
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

            $('#invoicing-check-all').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('#invoicing-table tbody .invoicing-row-checkbox').prop('checked', isChecked);
                $(this).prop('indeterminate', false);
            });

            $(document).on('change', '#invoicing-table tbody .invoicing-row-checkbox', function() {
                syncInvoicingCheckAll();
            });

            syncInvoicingCheckAll();

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

            $(window).on('resize', adjustInvoicingTableLayout);
            setTimeout(adjustInvoicingTableLayout, 50);
        });
    </script>
@endpush
