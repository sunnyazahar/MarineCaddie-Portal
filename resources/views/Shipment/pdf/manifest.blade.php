<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manifest {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm 22mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.4; margin: 0; }
        .page-break { page-break-before: always; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; }
        .doc-title { font-size: 17px; font-weight: bold; margin: 0 0 4px; }
        .doc-subtitle { font-size: 13px; font-weight: bold; margin: 0 0 8px; }
        .company { font-size: 12px; font-weight: bold; }
        .muted { color: #555; font-size: 11px; }
        .header-right { text-align: right; font-size: 10px; }
        .brand-logo { line-height: 1.05; margin-bottom: 4px; }
        .brand-marine { font-size: 20px; font-weight: bold; color: #002D5B; }
        .brand-caddie { font-size: 20px; font-weight: bold; color: #349DDA; }
        .brand-tagline { display: block; font-size: 9px; color: #FF6B03; font-weight: bold; margin-top: 2px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .field-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11px; }
        .field-table td { padding: 3px 0; vertical-align: top; }
        .field-label { width: 30%; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .data-table th, .data-table td { border: 0.5px solid #ccc; padding: 5px 4px; text-align: left; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        .totals-table td { padding: 3px 0; }
        .totals-label { width: 38%; font-weight: bold; }
        .comments { white-space: pre-wrap; font-size: 10px; margin-top: 8px; }
        .vessel-heading { font-size: 12px; font-weight: bold; margin: 10px 0 6px; }
        .pending-eta { font-size: 10px; color: #666; margin: 6px 0 2px; }
        .onboard-receipt {
            margin-top: 0;
            width: 100%;
        }
        .onboard-receipt-labels {
            width: 100%;
            border-collapse: collapse;
        }
        .onboard-receipt-labels td {
            padding: 0;
            font-size: 14px;
            font-weight: bold;
            color: #222;
            vertical-align: top;
            width: 33.33%;
        }
        .onboard-receipt-space {
            height: 55px;
        }
        .onboard-receipt-line {
            width: 50%;
            border-top: 1px dashed #9ca3af;
            margin: 0 0 10px;
        }
        .onboard-receipt-signatory {
            font-size: 14px;
            color: #222;
            font-weight: bold;
        }
        .page-manifest-invoice {
            position: relative;
            min-height: 245mm;
            padding-bottom: 110px;
            box-sizing: border-box;
        }
        .page-manifest-invoice .onboard-receipt-wrap {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .page {
            position: relative;
        }
        .revision-watermark {
            position: absolute;
            right: -55mm;
            top: 50%;
            margin-top: -20px;
            width: 180mm;
            text-align: center;
            transform: rotate(-90deg);
            transform-origin: center center;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
        .revision-watermark h1 {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 72px;
            font-weight: bold;
            color: #90c0c0;
            opacity: 0.55;
            letter-spacing: 2px;
            line-height: 1;
        }
    </style>
</head>
<body>

@php
    $header = function ($docTitle) use ($titleLine, $companyName, $companyAddress) {
        return '
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">' . e($docTitle) . '</div>
                    <div class="doc-subtitle">' . e($titleLine) . '</div> <br>
                    <div class="company">' . e($companyName) . '</div>
                    <div class="muted">' . e($companyAddress) . '</div>
                </td>
                <td class="header-right" style="width:38%;">
                    <div class="brand-logo">
                        {!! \App\Support\LogoHelper::pdfImgTag('180px') !!}
                    </div>
                </td>
            </tr>
        </table>';
    };
@endphp

{{-- Shipment Instructions (single page) --}}
<div class="page">
    @if (!empty($revisionWatermark))
        <div class="revision-watermark"><h1>{{ $revisionWatermark }}</h1></div>
    @endif
    {!! $header('Shipment Instructions') !!}
    <table class="field-table">
        <tr><td class="field-label">Shipped through</td><td>{{ $shippedThrough }}</td></tr>
        <tr><td class="field-label">Invoice to</td><td>{{ $invoiceTo }}</td></tr>
    </table>
    <div class="section-title" style="margin-top:10px;">Please prepare shipment to</div>
    <div style="font-weight:bold;">{{ $vesselLine }}</div>
    <table class="field-table" style="margin-top:6px;">
        <tr><td class="field-label">C/O</td><td>{{ $consigneeName }}</td></tr>
        <tr><td class="field-label"></td><td>{{ $consigneeAddress }}</td></tr>
        <tr><td class="field-label">E-mail</td><td>{{ $consigneeEmail }}</td></tr>
        <tr><td class="field-label">Phone</td><td>{{ $consigneePhone }}</td></tr>
        <tr><td class="field-label">Contact Person</td><td>{{ $consigneeContact ?: '—' }}</td></tr>
        <tr><td class="field-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Port of destination</td><td>{{ $destinationPort }}</td></tr>
        <tr><td class="field-label">Location</td><td>{{ $shipmentLocation }}</td></tr>
        <tr><td class="field-label">Service</td><td>{{ $serviceLabel }}</td></tr>
        <tr><td class="field-label">Additional service</td><td>{{ $additionalServiceLabel }}</td></tr>
        <tr><td class="field-label">PCS / Repacked as / Weight</td><td>{{ $pcsSummary }}</td></tr>
        <tr><td class="field-label">Deadline arrival</td><td>{{ $deadlineArrival }}</td></tr>
        <tr><td class="field-label">Document handled by</td><td>{{ $documentHandledBy }}</td></tr>
    </table>
    <div class="section-title" style="margin-top:10px;">Comments to hub</div>
    <div class="comments">{{ $commentsHub ?: '—' }}</div>
</div>

{{-- Manifest / Invoice --}}
<div class="page page-break{{ !empty($isOnBoardDelivery) ? ' page-manifest-invoice' : '' }}">
    {!! $header('Manifest / Invoice') !!}
    <div class="vessel-heading">{{ $vesselLine }}</div>
    <table class="totals-table" style="margin-top:0; margin-bottom:10px;">
        <tr><td class="totals-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Shipper</td><td>{{ $shipperLine }}</td></tr>
        <tr><td class="totals-label">Consignee</td><td>{{ $consigneeLine }}</td></tr>
        <tr><td class="totals-label">Contact</td><td>{{ $consigneeContact }}, {{ $consigneeContactEmail }}, {{ $consigneeContactPhone }}</td></tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>PO number</th>
                <th>Items</th>
                <th>Weight</th>
                <th>CBM</th>
                <th>Cust. value</th>
                <th>Description</th>
                <th>Stock no / Transit id</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($manifestRows as $row)
            <tr>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['po_number'] }}</td>
                <td>{{ $row['items'] }}</td>
                <td>{{ $row['weight'] }}</td>
                <td>{{ number_format($row['cbm'], 2) }}</td>
                <td>{{ $row['customs_value'] }} {{ $row['currency'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['stock_number'] }}@if($row['transit_id']) / {{ $row['transit_id'] }}@endif</td>
                <td>{{ $row['location'] ?? '—' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total {{ $manifestRows->first()['vessel'] ?? $vesselLine }}</strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td><strong>{{ number_format($totals['cbm'], 2) }} CBM</strong></td>
                <td><strong>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table">
        <tr><td class="totals-label">Total in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Total CBM</td><td>{{ number_format($totals['cbm'], 2) }} m³</td></tr>
        <tr><td class="totals-label">Total CBFT</td><td>{{ number_format($totals['cbft'], 2) }} ft³</td></tr>
    </table>
    @if (!empty($isOnBoardDelivery))
        <div class="onboard-receipt-wrap">
            <div class="onboard-receipt">
                <table class="onboard-receipt-labels">
                    <tr>
                        <td style="text-align:left;">Date received</td>
                        <td style="text-align:center;">Stamp</td>
                        <td></td>
                    </tr>
                </table>
                <div class="onboard-receipt-space"></div>
                <div class="onboard-receipt-line"></div>
                <div class="onboard-receipt-signatory">{{ $onBoardSignatory }}</div>
            </div>
        </div>
    @endif
</div>

{{-- Packing List (single page) --}}
<div class="page page-break">
    {!! $header('Packing List') !!}
    <div class="vessel-heading">{{ $manifestRows->first()['vessel'] ?? $vesselLine }}</div>
    <table class="field-table" style="margin-top:0; margin-bottom:8px;">
        <tr><td class="field-label">Shipper</td><td>{{ $shipperLine }}</td></tr>
        <tr><td class="field-label">Consignee</td><td>{{ $consigneeLine }}</td></tr>
        <tr><td class="field-label">Departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Destination</td><td>{{ $destinationPort }}</td></tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th>Stock no</th>
                <th>Location Position</th>
                <th>Supplier</th>
                <th>PO number</th>
                <th>Items</th>
                <th>Weight</th>
                <th>Dimensions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packingRows as $index => $row)
                @if ($row['pending_eta'] && ($index === 0 || ($packingRows[$index - 1]['stock_number'] ?? null) !== $row['stock_number']))
                <tr>
                    <td colspan="7" class="pending-eta" style="font-weight: bold; color: #000000; font-size: 11px;">In Transit &nbsp;&nbsp;  Transit ID: &nbsp;&nbsp; {{ $row['transit_id'] ?? '' }} &nbsp;&nbsp; ETA: &nbsp;&nbsp; {{ $row['pending_eta'] }}</td>
                </tr>
                @endif
                <tr>
                    <td>{{ $row['stock_number'] }}<br>{{ $row['label_code'] }}</td>
                    <td>{{ $row['position'] }}</td>
                    <td>{{ $row['supplier'] }}</td>
                    <td>{{ $row['po_number'] }}</td>
                    <td>{{ $row['item_label'] }}</td>
                    <td>{{ $row['weight_label'] }}</td>
                    <td>{{ $row['dimensions'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table" style="margin-top:8px;">
        <tr><td class="totals-label">Total in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Total CBM</td><td>{{ number_format($totals['cbm'], 2) }} m³</td></tr>
        <tr><td class="totals-label">Total CBFT</td><td>{{ number_format($totals['cbft'], 2) }} ft³</td></tr>
    </table>
</div>

</body>
</html>
