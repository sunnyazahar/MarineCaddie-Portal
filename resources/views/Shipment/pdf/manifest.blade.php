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
        .doc-title { display: block; font-size: 17px; font-weight: bold; margin: 0 0 10px; }
        .doc-subtitle { display: block; font-size: 13px; font-weight: bold; margin: 8px 0 8px; }
        .revision-highlight { color: #FD6C0A; font-weight: bold; font-size: 18px; margin: 0 0 4px; }
        .company { font-size: 12px; font-weight: bold; }
        .muted { color: #555; font-size: 11px; }
        .header-right { text-align: right; font-size: 10px; }
        .brand-logo { line-height: 1.05; margin-bottom: 4px; }
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
        .party-heading { font-weight: bold; display: block; margin-bottom: 2px; }
        .party-block {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            width: 250px;
            line-height: 1.15;
        }
        .party-block br { line-height: 1.15; margin: 0; padding: 0; }
        .party-cell { padding-top: 10px; text-align: left; }
        .party-inner { width: 250px; border-collapse: collapse; }
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
        .meta-wrap { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .meta-wrap td { vertical-align: top; }
        .meta-fields { width: 58%; }
        .meta-barcode { width: 42%; text-align: right; padding-left: 12px; }
        .meta-row { width: 100%; border-collapse: collapse; margin: 0 0 3px; }
        .meta-row td { padding: 1px 0; vertical-align: top; font-size: 11px; }
        .meta-label { width: 42%; font-weight: bold; }
        .meta-value { width: 58%; }
        .po-link { color: #1d4ed8; text-decoration: underline; font-size: 11px; margin: 6px 0 12px; }
        .info-block { margin: 0 0 10px; font-size: 11px; line-height: 1.45; }
        .info-label { font-weight: bold; display: inline; }
        .prepare-title { font-weight: bold; margin: 12px 0 4px; font-size: 11px; }
        .prepare-vessel { font-weight: bold; margin: 0 0 10px; font-size: 12px; }
        .agent-note { font-size: 11px; margin: 0 0 6px; }
        .agent-box {
            border: none;
            background: transparent;
            padding: 0;
            margin: 0 0 12px;
        }
        .agent-box-table { width: 100%; border-collapse: collapse; }
        .agent-box-table td { padding: 2px 0; vertical-align: top; font-size: 11px; }
        .agent-box-label { width: 18%; font-weight: normal; }
        .comments-title { font-size: 12px; font-weight: bold; color: #000000; margin: 10px 0 4px; }
        .comments-body { white-space: pre-wrap; font-size: 11px; line-height: 1.45; color: #dc2626; }
    </style>
</head>
<body>

@php
    $header = function ($docTitle, bool $showCompany = true, bool $showDocumentHandledBy = false) use ($titleLine, $companyName, $companyAddress, $documentHandledBy, $manifestRevisionLabel) {
        $companyHtml = '';
        if ($showCompany) {
            $companyHtml = '
                    <div class="company">' . e($companyName) . '</div>
                    <div class="muted">' . e($companyAddress) . '</div>';
        }

        $documentHandledHtml = '';
        if ($showDocumentHandledBy) {
            $documentHandledHtml = '
                    <div style="margin-top:4px; font-size:11px;">
                        <strong>Document handled by</strong>:  ' . e($documentHandledBy ?? '—') . '
                    </div>';
        }

        $revisionHtml = '';
        if (! empty($manifestRevisionLabel)) {
            $revisionHtml = '<div class="revision-highlight">(' . e($manifestRevisionLabel) . ')</div>';
        }

        return '
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    ' . $revisionHtml . '
                    <div class="doc-title">' . e($docTitle) . '</div>
                    <br>
                    <div class="doc-subtitle">' . e($titleLine) . '</div>
                    ' . $documentHandledHtml . '
                    ' . $companyHtml . '
                </td>
                <td class="header-right" style="width:38%;">
                    <div class="brand-logo">
                        ' . \App\Support\LogoHelper::imgTag('180px') . '
                    </div>
                </td>
            </tr>
        </table>';
    };

    $partyHeader = function (string $docTitle) use ($titleLine, $shipperLine, $consigneeLine) {
        return '
        <table class="header-table" style="table-layout:fixed;">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">' . e($docTitle) . '</div>
                </td>
                <td class="header-right" style="width:38%;">
                    <table align="right" style="width:180px; border-collapse:collapse;">
                        <tr>
                            <td style="text-align:left; vertical-align:top;">
                                <div class="brand-logo">
                                    ' . \App\Support\LogoHelper::imgTag('180px') . '
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <div class="doc-subtitle">' . e($titleLine) . '</div>
                </td>
            </tr>
            <tr>
                <td style="width:62%;" class="party-cell">
                    <table class="party-inner">
                        <tr>
                            <td style="text-align:left; vertical-align:top; width:180px;">
                                <span class="party-heading">Shipper</span>
                                <div class="party-block">' . nl2br(e($shipperLine), false) . '</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="header-right party-cell" style="width:38%;">
                    <table align="right" class="party-inner">
                        <tr>
                            <td style="text-align:left; vertical-align:top; width:180px;">
                                <span class="party-heading">Consignee</span>
                                <div class="party-block">' . nl2br(e($consigneeLine), false) . '</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
    };

    $marineCaddieBillingAddress = 'MarineCaddie Shipping LLC, Unit No. 204 – 224, Al Safi Building, Tower 1, Deira, Dubai, United Arab Emirates, Phone +971 50 5643375, Email ops@marinecaddie.com';
    $serviceDisplay = trim(e($serviceLabel ?? '—') . (!empty($additionalServiceLabel) && $additionalServiceLabel !== '—' ? '<br>' . e($additionalServiceLabel) : ''));
@endphp

{{-- Shipping Instructions (matches reference layout) --}}
<div class="page">
    {!! $header('Shipping Instructions (' . ($serviceLabel ?? '—') . ')', false, true) !!}

    <table class="meta-wrap">
        <tr>
            <td class="meta-fields">
                <table class="meta-row"><tr><td class="meta-label">Attention</td><td class="meta-value">{{ $agentName }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Port of departure</td><td class="meta-value">{{ $departurePort }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Port of destination</td><td class="meta-value">{{ $destinationPort }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Cargo Location</td><td class="meta-value">{{ $shipmentLocation }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Service</td><td class="meta-value">{!! $serviceDisplay !!}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">PCS / Repacked as / Weight</td><td class="meta-value">{{ $pcsSummary }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Preferred shipment date</td><td class="meta-value">{{ $preferredShipmentDate }}</td></tr></table>
                <table class="meta-row"><tr><td class="meta-label">Deadline arrival</td><td class="meta-value">{{ $deadlineArrival }}</td></tr></table>
            </td>
        </tr>
    </table>

    <div class="info-block">
        <span class="info-label">Shipped through:</span> {{ $marineCaddieBillingAddress }}
    </div>
    <div class="info-block">
        <span class="info-label">Invoice to:</span> {{ $marineCaddieBillingAddress }}
    </div>

    <div class="prepare-title">Please prepare shipment to:</div>
    <div class="prepare-vessel">{{ $vesselLine }}</div>

    <div class="agent-box">
        <table class="agent-box-table">
            <tr>
                <td class="agent-box-label">
                    <strong>C/O {{ $consigneeName }}</strong>
                    @if (empty($isOnBoardDelivery))
                        <br> {{ $consigneeAddress }}
                    @endif
                    <br> Email: {{ $consigneeEmail }}
                    <br> Phone: {{ $consigneePhone }}
                    <br> Contact person: {{ $consigneeContact }}
                </td>
            </tr>
        </table>
    </div>

    <div class="comments-title">Special considerations for destination port</div>
    <div class="comments-body">{{ $special_considerations_destination ?: '—' }}</div>
    <br>
    <div class="comments-title">Comments to hub</div>
    <div class="comments-body">{{ $commentsHub ?: '—' }}</div>
</div>

{{-- Manifest / Invoice --}}
<div class="page page-break{{ !empty($isOnBoardDelivery) ? ' page-manifest-invoice' : '' }}">
    {!! $partyHeader('Shipping / Invoice') !!}
    <br>
    <table class="totals-table" style="margin-top:0; margin-bottom:10px;">
        <tr><td class="totals-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Port of destination</td><td>{{ $destinationPort }}</td></tr>
    </table>
    <div class="vessel-heading">{{ $vesselLine }}</div>
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
                <td colspan="2"><strong>Total </strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td><strong>{{ number_format($totals['cbm'], 2) }} CBM</strong></td>
                <td><strong>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table">
        <tr><td class="totals-label">Total pieces in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Repacked as </td><td>{{ $totals['repacked_items'] }} item(s)  / {{ $totals['repacked_weight'] }} kg</td></tr>
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
    {!! $partyHeader('Packing List') !!}
    <br>
    <table class="totals-table" style="margin-top:0; margin-bottom:10px;">
        <tr><td class="totals-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Port of destination</td><td>{{ $destinationPort }}</td></tr>
    </table>
    <div class="vessel-heading">{{ $vesselLine }}</div>
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
