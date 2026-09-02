<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $document_title }} {{ $proforma_display_no }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm 0 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; line-height: 1.4; margin: 0; }
        .pdf-header { margin-bottom: 6px; }
        .header-main { width: 100%; border-collapse: collapse; }
        .header-main td { vertical-align: top; padding: 0; }
        .header-left { width: 48%; padding-right: 20px; }
        .header-right { width: 52%; text-align: right; }
        .header-title {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a5f;
            line-height: 1.15;
            margin: 0 0 18px;
        }
        .brand-logo-wrap { text-align: right; margin-bottom: 10px; }
        .brand-logo-wrap .marinecaddie-logo {
            width: 220px !important;
            max-width: 220px !important;
            height: auto !important;
            display: inline-block;
        }
        .sender-block {
            text-align: right;
            margin-top: 0;
        }
        .invoice-to-block {
            margin-top: 0;
            max-width: 72mm;
        }
        .invoice-to-block .party-name,
        .invoice-to-block .party-line,
        .invoice-to-block .customer-no {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .invoice-to-block .party-label {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 8px;
        }
        .party-label {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            margin: 0 0 8px;
        }
        .party-name {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 4px;
        }
        .party-line {
            font-size: 10px;
            color: #111827;
            margin: 0 0 2px;
        }
        .customer-no {
            font-size: 10px;
            color: #111827;
            margin-top: 8px;
        }
        .sender-block .party-name { margin-bottom: 4px; }
        .sender-contact {
            font-size: 10px;
            color: #111827;
            margin: 0 0 2px;
        }
        .header-divider {
            height: 1px;
            background: #1e3a5f;
            margin-top: 16px;
        }
        .details-panel { margin: 12px 0 14px; }
        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta-strip td {
            padding: 3px 10px;
            vertical-align: top;
            line-height: 1.15;
        }
        .meta-strip tr:last-child td { border-bottom: none; }
        .meta-strip-label {
            width: 18%;
            font-size: 10px;
            color: #1e3a5f;
            font-weight: bold;
            line-height: 1.15;
        }
        .meta-strip-value {
            width: 32%;
            font-size: 10px;
            color: #111827;
            line-height: 1.15;
        }
        .dual-panel {
            width: 100%;
            border-collapse: collapse;
        }
        .dual-panel td { vertical-align: top; }
        .dual-col-left { width: 50%; padding-right: 14px; }
        .dual-col-right { width: 50%; padding-left: 14px; }
        .block-title {
            font-size: 10px;
            font-weight: bold;
            color: #008080;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-bottom: 4px;
            margin: 0 0 6px;
            border-bottom: 1px solid #b8d4d4;
        }
        .consolidated-po-row {
            border-bottom: 1px solid #b8d4d4;
            padding-bottom: 4px;
            margin: 0 0 10px;
        }
        .consolidated-po-label {
            font-size: 10px;
            font-weight: bold;
            color: #008080;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .consolidated-po-value {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            margin-left: 8px;
        }
        .consolidated-meta-table {
            margin-bottom: 14px;
        }
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .field-table td {
            padding: 0;
            vertical-align: top;
            line-height: 1.35;
        }
        .field-label {
            width: 38%;
            font-size: 10px;
            font-weight: bold;
            color: #1e3a5f;
            padding: 4px 6px;
        }
        .field-value {
            font-size: 10px;
            color: #111827;
            padding: 4px 6px;
        }
        .line-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 9px; page-break-inside: auto; }
        .line-table thead { display: table-header-group; }
        .line-table th, .line-table td { border: 0.5px solid #cbd5e1; padding: 5px 4px; text-align: left; }
        .line-table tbody tr { page-break-inside: avoid; }
        .line-table th { background: #f8fafc; font-weight: bold; color: #334155; }
        .text-right { text-align: right; }
        .page-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            line-height: 1.35;
        }
        .footer-summary {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #000000;
        }
        .footer-summary td {
            vertical-align: top;
            padding-top: 6px;
        }
        .footer-bank-col { width: 52%; padding-right: 12px; }
        .footer-totals-col { width: 48%; }
        .footer-totals-col .totals-table {
            width: 100%;
            margin-left: 0;
            margin-top: 0;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
        }
        .totals-label-col { width: 68%; }
        .totals-value-col { width: 32%; }
        .totals-table tr { height: 13pt; }
        .totals-table td { padding: 0; vertical-align: top; line-height: 13pt; }
        .totals-label {
            text-align: right;
            padding-right: 8px;
            font-weight: bold;
            color: #475569;
        }
        .totals-value {
            text-align: right;
            font-weight: bold;
            color: #008080;
        }
        .totals-net-label {
            text-align: right;
            padding-right: 8px;
            font-weight: bold;
            color: #1e3a5f;
            font-size: 11px;
            line-height: 14pt;
        }
        .totals-net-value {
            text-align: right;
            font-weight: bold;
            color: #1e3a5f;
            font-size: 12px;
            line-height: 14pt;
        }
        .remittance-fields-full {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 0;
            table-layout: fixed;
        }
        .remittance-key-col { width: 88px; }
        .remittance-value-col { width: auto; }
        .remittance-fields-full tr { height: 13pt; }
        .remittance-fields-full td {
            padding: 0;
            vertical-align: top;
            line-height: 13pt;
        }
        .remittance-key {
            font-weight: bold;
            color: #111827;
            white-space: nowrap;
        }
        .remittance-value {
            color: #111827;
            word-wrap: break-word;
        }
        .remittance-notes {
            font-size: 9px;
            color: #111827;
            line-height: 1.35;
            margin-top: 5px;
            margin-bottom: 6px;
        }
        .remittance-notes-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .remittance-note-line { margin-bottom: 1px; }
        .footer-page-number {
            height: 14px;
            margin: 14px 0 0 0;
            padding: 0;
        }
        .footer-clearance {
            height: 52mm;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    @include('Billing.pdf.partials.proforma-footer')

    <div class="pdf-header">
        <table class="header-main">
            <tr>
                <td class="header-left">
                    <div class="header-title">{{ $document_title }}</div>
                    <div class="invoice-to-block">
                        <div class="party-label">To</div>
                        <div class="party-name">{{ $invoice_to['name'] }}</div>
                        @foreach ($invoice_to['lines'] ?? [] as $line)
                            <div class="party-line">{{ $line }}</div>
                        @endforeach
                        @if (($invoice_to['phone'] ?? '') !== '')
                            <div class="customer-no">Phone: {{ $invoice_to['phone'] }}</div>
                        @endif
                        @if (($invoice_to['email'] ?? '') !== '')
                            <div class="customer-no">Email: {{ $invoice_to['email'] }}</div>
                        @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="brand-logo-wrap">
                        {!! \App\Support\LogoHelper::imgTag('220px', 'display:inline-block;') !!}
                    </div>
                    <div class="sender-block">
                        <div class="party-name">{{ $sender['name'] }}</div>
                        @foreach ($sender['lines'] as $line)
                            <div class="party-line">{{ $line }}</div>
                        @endforeach
                        <div class="sender-contact" style="margin-top: 6px;">Phone: {{ $sender['phone'] }}</div>
                        <div class="sender-contact">Email: {{ $sender['email'] }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="header-divider"></div>
    </div>

    <div class="details-panel">
        @if ($show_consolidation_summary_table ?? false)
            <div class="consolidated-po-row">
                <span class="consolidated-po-label">Customer PO No.</span>
                <span class="consolidated-po-value">{{ $invoice['client_ref_no'] ?? '—' }}</span>
            </div>
            <table class="field-table consolidated-meta-table">
                <tr>
                    <td class="field-label">Invoice Date</td>
                    <td class="field-value">{{ $invoice['proforma_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="field-label">Due Date</td>
                    <td class="field-value">{{ $invoice['due_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="field-label">Currency</td>
                    <td class="field-value">{{ $invoice['currency'] ?? '—' }}</td>
                </tr>
            </table>
        @else
        <table class="meta-strip">
            <tr>
                <td class="meta-strip-label">Invoice No.</td>
                <td class="meta-strip-label">{{ $proforma_display_no }}</td>
                <td class="meta-strip-label">Invoice Date</td>
                <td class="meta-strip-value">{{ $invoice['proforma_date'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="meta-strip-label">Job No.</td>
                <td class="meta-strip-value">{{ $invoice['job_no'] ?? '—' }}</td>
                <td class="meta-strip-label">Due Date</td>
                <td class="meta-strip-value">{{ $invoice['due_date'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="meta-strip-label">Currency</td>
                <td class="meta-strip-value">{{ $invoice['currency'] ?? '—' }}</td>
                <td class="meta-strip-label"> Cust Ref No.</td>
                <td class="meta-strip-label">{{ $invoice['client_ref_no'] ?? '—' }}</td>
            </tr>
        </table>
        @endif

        @if (!($hide_shipment_details ?? false))
        <div class="block-title">Shipment Details</div>
        <table class="dual-panel">
            <tr>
                <td class="dual-col-left">
                    <table class="field-table">
                        <tr>
                            <td class="field-label">Service Type</td>
                            <td class="field-value">{{ $invoice['service_type'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Shipper</td>
                            <td class="field-value">{{ $invoice['shipper_name'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Consignee</td>
                            <td class="field-value">{{ $invoice['consignee_name'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Port of Loading</td>
                            <td class="field-value">{{ $invoice['airport_of_loading'] ?? $invoice['port_of_loading'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Port of Discharge</td>
                            <td class="field-value">{{ $invoice['airport_of_destination'] ?? $invoice['port_of_discharge'] ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="dual-col-right">
                    <table class="field-table">
                        <tr>
                            <td class="field-label">MBL / AWB No.</td>
                            <td class="field-value">{{ $invoice['mawb_no'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Vessel</td>
                            <td class="field-value">{{ $invoice['vessel_name'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Packages</td>
                            <td class="field-value">{{ $invoice['packages'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Gross Weight</td>
                            <td class="field-value">{{ $invoice['gross_wt'] ?? '—' }} KGS</td>
                        </tr>
                        <tr>
                            <td class="field-label">Chargeable Weight</td>
                            <td class="field-value">{{ $invoice['chargeable_wt'] ?? '—' }} KGS</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @endif
    </div>

    <table class="line-table">
        @if ($show_consolidation_summary_table ?? false)
            <thead>
                <tr>
                    <th style="width: 14%;">Invoice No.</th>
                    <th style="width: 14%;">Customer PO No.</th>
                    <th style="width: 14%;">Shipment No</th>
                    <th style="width: 14%;">MBL / AWB No.</th>
                    <th style="width: 12%;" class="text-right">Total Packages</th>
                    <th style="width: 14%;" class="text-right">Total Gross Weight</th>
                    <th style="width: 14%;" class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($consolidation_rows ?? [] as $row)
                    <tr>
                        <td>{{ ($row['invoice_no'] ?? '') !== '' ? $row['invoice_no'] : '—' }}</td>
                        <td>{{ ($row['customer_po_no'] ?? '') !== '' ? $row['customer_po_no'] : '—' }}</td>
                        <td>{{ ($row['shipment_no'] ?? '') !== '' ? $row['shipment_no'] : '—' }}</td>
                        <td>{{ ($row['mbl_awb_no'] ?? '') !== '' ? $row['mbl_awb_no'] : '—' }}</td>
                        <td class="text-right">{{ ($row['total_packages'] ?? '') !== '' ? $row['total_packages'] : '—' }}</td>
                        <td class="text-right">
                            @if (($row['total_gross_weight'] ?? '') !== '')
                                {{ $row['total_gross_weight'] }} KGS
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">{{ ($row['total_amount'] ?? '') !== '' ? $row['total_amount'] : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No invoices.</td>
                    </tr>
                @endforelse
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="width: 24%;">Description</th>
                    <th style="width: 8%;">HSN</th>
                    <th style="width: 12%;">Remarks</th>
                    <th style="width: 6%;" class="text-right">Qty</th>
                    <th style="width: 6%;">Type</th>
                    <th style="width: 8%;" class="text-right">Rate</th>
                    <th style="width: 6%;">Curr</th>
                    <th style="width: 9%;" class="text-right">Amount</th>
                    <th style="width: 7%;" class="text-right">Taxable</th>
                    <th style="width: 6%;" class="text-right">VAT %</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($line_items as $item)
                    <tr>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td>{{ $item['hsn'] ?? '' }}</td>
                        <td>{{ $item['remarks'] ?? '' }}</td>
                        <td class="text-right">{{ $item['qty'] ?? '' }}</td>
                        <td>{{ $item['qty_type'] ?? '' }}</td>
                        <td class="text-right">{{ $item['rate'] ?? '' }}</td>
                        <td>{{ $item['currency'] ?? '' }}</td>
                        <td class="text-right">{{ $item['amount'] ?? '' }}</td>
                        <td class="text-right">{{ $item['taxable'] ?? '' }}</td>
                        <td class="text-right">{{ $item['igst_pct'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">No line items.</td>
                    </tr>
                @endforelse
            </tbody>
        @endif
    </table>

    <div class="footer-clearance"></div>

</body>
</html>
