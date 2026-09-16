<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Instruction {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm 28mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; line-height: 1.4; margin: 0; }
        .page-break { page-break-before: always; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; }
        .si-title-row { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        .si-title-row td { vertical-align: middle; }
        .si-title-spacer { width: 30%; }
        .si-banner { width: 72%; border-collapse: collapse; margin: 0 0 12px; }
        .si-banner td { vertical-align: top; }
        .si-details-cell { width: 100%; }
        .si-logo-cell { width: 30%; text-align: right; }
        .si-page-title {
            width: 40%;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.03em;
            margin: 0;
            text-align: center;
            color: #111;
        }
        .si-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .si-details td {
            border: 1px solid #222;
            padding: 2px 6px;
            text-align: left;
            vertical-align: middle;
        }
        .si-details .si-revision {
            font-weight: bold;
            letter-spacing: 0.02em;
        }
        .si-handled {
            margin-top: 6px;
            font-size: 12px;
        }
        .si-fields {
            width: 72%;
            border-collapse: collapse;
            margin: 0 0 14px;
            font-size: 12px;
        }
        .si-fields td {
            border: 1px solid #222;
            padding: 2px 6px;
            vertical-align: middle;
        }
        .si-fields .si-field-label {
            width: 34%;
            font-weight: bold;
        }
        .doc-title { display: block; font-size: 17px; font-weight: bold; margin: 0; text-align: center; }
        .doc-subtitle { display: block; font-size: 13px; font-weight: bold; margin: 8px 0 8px; }
        .revision-highlight { color: #FD6C0A; font-weight: bold; font-size: 18px; margin: 0 0 4px; }
        .company { font-size: 12px; font-weight: bold; }
        .muted { color: #555; font-size: 11px; }
        .header-right { text-align: right; font-size: 10px; }
        .brand-logo { line-height: 1.05; margin-bottom: 4px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .field-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 12px; }
        .field-table td { padding: 3px 0; vertical-align: top; }
        .field-label { width: 30%; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        .data-table th, .data-table td { border: 1px solid #222; padding: 5px 4px; text-align: left; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .po-cell { width: 240px; word-wrap: break-word; overflow-wrap: break-word; }
        .nowrap-cell { white-space: nowrap; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .totals-table td { padding: 3px 0; }
        .summary-totals { width: 340px; }
        .summary-totals td { padding: 1px 0; }
        .summary-totals .totals-label { width: 155px; padding-right: 10px; white-space: nowrap; }
        .port-totals .totals-label { width: 120px; padding-right: 10px; white-space: nowrap; }
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
        .party-cell { padding-top: 10px; text-align: left; font-size: 12px; }
        .party-inner { width: 250px; border-collapse: collapse; }
        .comments { white-space: pre-wrap; font-size: 10px; margin-top: 8px; }
        .vessel-heading { font-size: 13px; font-weight: bold; margin: 10px 0 6px; }
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
        .prepare-title { font-weight: bold; margin: 12px 0 4px; font-size: 12px; }
        .prepare-vessel { font-weight: bold; margin: 0 0 10px; font-size: 13px; }
        .agent-note { font-size: 12px; margin: 0 0 6px; }
        .agent-box {
            border: none;
            background: transparent;
            padding: 0;
            margin: 0 0 12px;
        }
        .agent-box-table { width: 100%; border-collapse: collapse; }
        .agent-box-table td { padding: 2px 0; vertical-align: top; font-size: 12px; }
        .agent-box-label { width: 18%; font-weight: normal; }
        .comments-title { font-size: 13px; font-weight: bold; color: #000000; margin: 10px 0 4px; }
        .comments-body { white-space: pre-wrap; font-size: 12px; line-height: 1.45; color: #dc2626; }
    </style>
</head>
<body>

@php
    $partyHeader = function (string $docTitle) use ($titleLine, $shipperLine, $consigneeLine, $isOnBoardDelivery) {
        $shipperHeading = ! empty($isOnBoardDelivery) ? 'MarineCaddie Agent:' : 'Shipper';
        $consigneeHeading = ! empty($isOnBoardDelivery) ? 'Vessel Agent:' : 'Consignee';

        return '
        <table class="header-table" style="table-layout:fixed;">
            <tr>
                <td style="width:30%;"></td>
                <td style="width:40%; vertical-align:middle;">
                    <div class="doc-title">' . e($docTitle) . '</div>
                </td>
                <td class="header-right" style="width:30%; vertical-align:middle;">
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
        </table>
        <table class="header-table">
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
                                <span class="party-heading">' . e($shipperHeading) . '</span>
                                <div class="party-block">' . nl2br(e($shipperLine), false) . '</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="header-right party-cell" style="width:38%;">
                    <table align="right" class="party-inner">
                        <tr>
                            <td style="text-align:left; vertical-align:top; width:180px;">
                                <span class="party-heading">' . e($consigneeHeading) . '</span>
                                <div class="party-block">' . nl2br(e($consigneeLine), false) . '</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
    };

    $serviceDisplay = trim(e($serviceLabel ?? '—') . (!empty($additionalServiceLabel) && $additionalServiceLabel !== '—' ? '<br>' . e($additionalServiceLabel) : ''));
@endphp

{{-- Shipping Instruction — title centered, logo on its right, details box aligned with the field grid --}}
<div class="page">
    <table class="si-title-row">
        <tr>
            <td class="si-title-spacer"></td>
            <td class="si-page-title">[SHIPPING INSTRUCTION]</td>
            <td class="si-logo-cell">
                <div class="brand-logo">
                    {!! \App\Support\LogoHelper::imgTag('190px') !!}
                </div>
            </td>
        </tr>
    </table>
    <table class="si-banner">
        <tr>
            <td class="si-details-cell">
                @if (! empty($manifestRevisionLabel))
                    <div class="revision-highlight">({{ $manifestRevisionLabel }})</div>
                @endif
                <table class="si-details">
                    <tr>
                        <td><strong>Service:</strong> {!! $serviceDisplay !!}</td>
                    </tr>
                    <tr>
                        <td><strong>Ref No.</strong> {{ $shipment->shipment_number ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Shipment handled by:</strong> {{ $documentHandledBy ?: '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="si-fields">
        <tr>
            <td class="si-field-label">Attn.</td>
            <td>{{ $agentName }}</td>
        </tr>
        <tr>
            <td class="si-field-label">Departure Port</td>
            <td>{{ $destinationPort }}</td>
        </tr>
        <tr>
            <td class="si-field-label">Arrival Port</td>
            <td>{{ $departurePort }}</td>
        </tr>
        <tr>
            <td class="si-field-label">Shipment Mode</td>
            <td>{!! $serviceDisplay !!}</td>
        </tr>
        <tr>
            <td class="si-field-label">Pcs / Wt. / Dims</td>
            <td>{{ $pcsSummary }}</td>
        </tr>
        <tr>
            <td class="si-field-label">Deadline Date</td>
            <td>{{ $deadlineArrival }}</td>
        </tr>
    </table>

    <div class="prepare-title">Consignee:</div>
    <div class="prepare-vessel">{{ $vesselLine }}</div>

    <div class="agent-box">
        <table class="agent-box-table">
            <tr>
                <td class="agent-box-label">
                    <strong>
                        @if (!empty($isOnBoardDelivery))
                            Vessel Agent: {{ $consigneeName }}
                        @else
                            C/O {{ $consigneeName }}
                        @endif
                    </strong>
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

    <div class="comments-title">Remarks to Agent</div>
    <div class="comments-body">{{ $commentsHub ?: '—' }}</div>
</div>

{{-- Manifest / Invoice --}}
<div class="page page-break{{ !empty($isOnBoardDelivery) ? ' page-manifest-invoice' : '' }}">
    {!! $partyHeader('Shipping Invoice') !!}
    <br>
    <table class="totals-table port-totals" style="margin-top:0; margin-bottom:10px;">
        <tr><td class="totals-label">Departure Port</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Arrival Port</td><td>{{ $destinationPort }}</td></tr>
    </table>
    <div class="vessel-heading">{{ $vesselLine }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Stock number</th>
                <th>PO Number</th>
                <th>Supplier name</th>
                <th>Pieces</th>
                <th>Wt (KG)</th>
                <th>Value (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($manifestRows as $row)
            <tr>
                <td>{{ $row['stock_number'] }}</td>
                <td><div class="po-cell">{{ $row['po_number'] }}</div></td>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['items'] }}</td>
                <td>{{ $row['weight'] }}</td>
                <td>{{ $row['customs_value'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align:center;"><strong>Total</strong></td>
                <td><strong>{{ $totals['packages'] }}</strong></td>
                <td><strong>{{ $totals['weight'] }}</strong></td>
                <td><strong>{{ $totals['customs_value'] }}</strong></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table summary-totals">
        <tr><td class="totals-label">Repacked</td><td><strong>{{ $totals['repacked_items'] }} item(s)  / {{ $totals['repacked_weight'] }} kg</strong></td></tr>
        <tr><td class="totals-label">Total pkgs</td><td><strong>{{ $totals['packages'] }} pcs</strong></td></tr>
        <tr><td class="totals-label">Gross weight</td><td><strong>{{ $totals['weight'] }} kg</strong></td></tr>
        <tr><td class="totals-label">Volumetric weight</td><td><strong>{{ number_format($totals['volume_weight'], 2) }} kg</strong></td></tr>
        <tr><td class="totals-label">Custom value</td><td><strong>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</strong></td></tr>
    </table>
    @if (!empty($isOnBoardDelivery))
        <div class="onboard-receipt-wrap">
            <div class="onboard-receipt">
                <table class="onboard-receipt-labels">
                    <tr>
                        <td style="text-align:left;">Receiving Date</td>
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
    <table class="totals-table port-totals" style="margin-top:0; margin-bottom:10px;">
        <tr><td class="totals-label">Departure Port</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Arrival Port</td><td>{{ $destinationPort }}</td></tr>
    </table>
    <div class="vessel-heading">{{ $vesselLine }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" class="nowrap-cell">Stock number</th>
                <th rowspan="2" class="nowrap-cell">Ref. number</th>
                <th rowspan="2">Supplier</th>
                <th rowspan="2">PO number</th>
                <th rowspan="2">Pieces</th>
                <th rowspan="2">Weight (KG)</th>
                <th colspan="3" style="text-align:center;">Dims (cms)</th>
            </tr>
            <tr>
                <th style="text-align:center;">L</th>
                <th style="text-align:center;">W</th>
                <th style="text-align:center;">H</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packingRows as $row)
                <tr>
                    <td class="nowrap-cell">{!! str_replace('-', '&#8209;', e($row['stock_number'])) !!}<br>{!! str_replace('-', '&#8209;', e($row['label_code'])) !!}</td>
                    <td class="nowrap-cell">{!! str_replace('-', '&#8209;', e($shipment->shipment_number ?: '—')) !!}</td>
                    <td>{{ $row['supplier'] }}</td>
                    <td>{{ $row['po_number'] }}</td>
                    <td>{{ $row['item_label'] }}</td>
                    <td>{{ $row['weight_label'] }}</td>
                    <td style="text-align:center;">{{ $row['length'] }}</td>
                    <td style="text-align:center;">{{ $row['width'] }}</td>
                    <td style="text-align:center;">{{ $row['height'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:center;"><strong>Total</strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table summary-totals">
        <tr><td class="totals-label">Repacked</td><td><strong>{{ $totals['repacked_items'] }} item(s)  / {{ $totals['repacked_weight'] }} kg</strong></td></tr>
        <tr><td class="totals-label">Total pkgs</td><td><strong>{{ $totals['packages'] }} pcs</strong></td></tr>
        <tr><td class="totals-label">Gross weight</td><td><strong>{{ $totals['weight'] }} kg</strong></td></tr>
        <tr><td class="totals-label">Volumetric weight</td><td><strong>{{ number_format($totals['volume_weight'], 2) }} kg</strong></td></tr>
    </table>
</div>

</body>
</html>
