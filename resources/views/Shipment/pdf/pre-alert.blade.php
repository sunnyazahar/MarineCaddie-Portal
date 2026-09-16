<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre Arrival Notification {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm 28mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.4; margin: 0; }
        .page-break { page-break-before: always; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; }
        .doc-title { font-size: 17px; font-weight: bold; margin: 0 0 4px; }
        .doc-subtitle { font-size: 12px; font-weight: bold; margin: 0 0 8px; }
        .company { font-size: 12px; font-weight: bold; margin-top: 20px; }
        .muted { color: #555; font-size: 11px; }
        .header-right { text-align: right; font-size: 10px; }
        .brand-logo { line-height: 1.05; margin-bottom: 4px; }
        .si-title-row { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        .si-title-row td { vertical-align: middle; }
        .si-title-spacer { width: 30%; }
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
        .si-banner { width: 72%; border-collapse: collapse; margin: 0 0 12px; }
        .si-banner td { vertical-align: top; }
        .si-details-cell { width: 100%; }
        .si-details { width: 100%; border-collapse: collapse; font-size: 12px; }
        .si-details td { border: 1px solid #222; padding: 2px 6px; text-align: left; vertical-align: middle; }
        .revision-highlight { color: #FD6C0A; font-weight: bold; font-size: 18px; margin: 0 0 4px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .expected-line { margin: 0 0 10px; font-size: 11px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .data-table th, .data-table td { border: 1px solid #222; padding: 5px 4px; text-align: left; vertical-align: top; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .invoice-table { font-size: 11px; }
        .po-cell { width: 240px; word-wrap: break-word; overflow-wrap: break-word; }
        .field-block { margin: 10px 0; font-size: 11px; }
        .field-label { font-weight: bold; margin-bottom: 4px; }
        .address-block { white-space: pre-wrap; font-size: 11px; margin-top: 4px; }
        .notify-title { font-size: 12px; font-weight: normal; margin: 12px 0 6px; }
        .vessel-heading { font-size: 12px; font-weight: bold; margin: 10px 0 6px; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 12px; }
        .summary-table td { padding: 1px 0; vertical-align: top; }
        .summary-table.summary-totals { width: 340px; }
        .summary-totals .summary-label { width: 155px; padding-right: 10px; white-space: nowrap; font-weight: bold; }
        .description-cell { white-space: pre-wrap; }
    </style>
</head>
<body>

@php
    $serviceParts = array_filter([
        filled($shipment->service) ? e($shipment->service) : null,
        ! empty($additionalServiceLabel) && $additionalServiceLabel !== '—' ? e($additionalServiceLabel) : null,
    ]);
    $serviceDisplay = $serviceParts !== [] ? implode(' / ', $serviceParts) : '—';

    $preAlertTitle = function () {
        return '
        <table class="si-title-row">
            <tr>
                <td class="si-title-spacer"></td>
                <td class="si-page-title">Pre Arrival Notification</td>
                <td class="si-logo-cell">
                    <div class="brand-logo">
                        ' . \App\Support\LogoHelper::imgTag('190px') . '
                    </div>
                </td>
            </tr>
        </table>';
    };

    $preAlertDetailsTable = function (bool $includeReference = false) use ($serviceDisplay, $shipment, $ownersReference, $referenceColumnLabel, $awb, $showReferenceColumn) {
        $referenceRow = '';
        if ($includeReference && ! empty($showReferenceColumn)) {
            $referenceValue = filled($awb) && $awb !== '—' ? e($awb) : '—';
            $referenceRow = '<tr><td><strong>' . e($referenceColumnLabel ?? 'AWB') . '</strong> ' . $referenceValue . '</td></tr>';
        }

        $customerPo = filled($ownersReference) && $ownersReference !== '—' ? e($ownersReference) : '—';

        return '
                    <table class="si-details">
                        <tr><td><strong>Service:</strong> ' . $serviceDisplay . '</td></tr>
                        ' . $referenceRow . '
                        <tr><td><strong>MarineCaddie Ref. No.</strong> ' . e($shipment->shipment_number ?: '—') . '</td></tr>
                        <tr><td><strong>Customer\'s PO No.</strong> ' . $customerPo . '</td></tr>
                        <tr><td><strong>Shipment arranged by:</strong> ' . e(\App\Support\CompanyAddress::NAME) . '</td></tr>
                        <tr><td><strong>MarineCaddie account handler:</strong> ' . e($shipment->accountManager?->name ?: '—') . '</td></tr>
                    </table>';
    };

    $flightColumnLabel = match ($shipment->service) {
        'Sea freight' => 'Vessel',
        'Truck' => 'Freight company',
        'Courier' => 'Carrier',
        'Release' => 'Freight company',
        'Hand Carry' => 'Contact',
        'On-board delivery' => 'Delivery',
        default => 'Flight',
    };
    $isOnBoardDelivery = ($shipment->service ?? '') === 'On-board delivery';
@endphp

<div class="page">
    {!! $preAlertTitle() !!}
    <table class="si-banner">
        <tr>
            <td class="si-details-cell">
                @if (! empty($preAlertRevisionLabel))
                    <div class="revision-highlight">({{ $preAlertRevisionLabel }})</div>
                @endif
                {!! $preAlertDetailsTable() !!}
            </td>
        </tr>
    </table>
<br>
    @unless ($isOnBoardDelivery)
        <div class="notify-title" style="margin-top:0;">Incoming shipment details.</div>
    @endunless
    <div class="vessel-heading">{{ $vesselLine }}</div>
    @unless ($isOnBoardDelivery)
        <div class="address-block"><strong>C/O {{ $consigneeName }}</strong> <br> {{ $consigneeAddress }}</div>

        <div class="section-title">Freight details</div>
    @endunless
    <div class="expected-line">
        {{ $isOnBoardDelivery ? 'Delivery is expected on' : 'Shipment is expected on' }}
        <strong>{{ $arrivalDate }} in {{ $destinationPortSimple }}</strong> with the below details
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Port of departure</th>
                <th>Port of destination</th>
                <th>Shippers reference</th>
                @if (!empty($showReferenceColumn))
                    <th>{{ $referenceColumnLabel }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($freightDetailRows as $row)
            <tr>
                <td>{{ $row['departure_port'] }}</td>
                <td>{{ $row['destination_port'] }}</td>
                <td>{{ $row['shippers_reference'] }}</td>
                @if (!empty($showReferenceColumn))
                    <td>{{ $row['reference'] }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top:10px;">
        <thead>
            <tr>
                <th>Service</th>
                <th>Departure port</th>
                <th>Departure date</th>
                <th>{{ $flightColumnLabel }}</th>
                <th>Arrival date</th>
                <th>Arrival time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($serviceDetailRows as $row)
            <tr>
                <td>{{ $row['service'] }}</td>
                <td>{{ $row['departure_port'] }}</td>
                <td>{{ $row['departure_date'] ?? '—' }}</td>
                <td>{{ $row['flight'] }}</td>
                <td>{{ $row['arrival_date'] }}</td>
                <td>{{ $row['arrival_time'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>

</div>

<div class="page page-break">
    <table class="si-title-row">
        <tr>
            <td class="si-title-spacer"></td>
            <td class="si-page-title"></td>
            <td class="si-logo-cell">
                <div class="brand-logo">
                    {!! \App\Support\LogoHelper::imgTag('190px') !!}
                </div>
            </td>
        </tr>
    </table>
    @if ($isOnBoardDelivery)
        <div class="doc-subtitle">Shippers reference {{ $shippersReference }}</div>
        <div class="doc-subtitle" style="margin-top:16px;">{{ $headerSubtitle }}</div>
        <table class="summary-table" style="margin-top:10px;">
            <tr>
                <td style="width:50%; padding-right:12px; vertical-align:top; font-size:11px;">
                    <div class="field-label">Shipped through:</div>
                    <div>MarineCaddie Shipping LLC,</div>
                    <div>Email ops@marinecaddie.com</div>
                </td>
                <td style="width:50%; padding-left:12px; vertical-align:top; font-size:11px;">
                    <div class="field-label">Vessel Agent:</div>
                    <div>{{ $consigneeName }}</div>
                    <div>Phone: {{ $consigneePhone ?: '—' }}</div>
                    <div>Email: {{ $consigneeEmail ?: '—' }}</div>
                </td>
            </tr>
        </table>
    @else
        <div class="section-title" style="margin-top:0;">Shipment Details</div>
        <table class="si-banner">
            <tr>
                <td class="si-details-cell">
                    {!! $preAlertDetailsTable(true) !!}
                </td>
            </tr>
        </table>
    @endif
    <div class="vessel-heading">{{ $vesselLine }}</div>

    @unless ($isOnBoardDelivery)
        <div class="field-block">
            <div class="address-block"><strong>C/O {{ $consigneeName }}</strong> <br> {{ $consigneeAddress }}</div>
        </div>
    @endunless

    <table class="data-table invoice-table">
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
            @foreach ($preAlertRows as $row)
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
    <table class="summary-table summary-totals">
        <tr><td class="summary-label">Repacked</td><td><strong>{{ filled($shipment->repacked_items) ? (int) $shipment->repacked_items : '—' }} item(s)  / {{ filled($shipment->repacked_weight) ? number_format((float) $shipment->repacked_weight, 2) : '—' }} kg</strong></td></tr>
        <tr><td class="summary-label">Total pkgs</td><td><strong>{{ $totals['packages'] }} pcs</strong></td></tr>
        <tr><td class="summary-label">Gross weight</td><td><strong>{{ $totals['weight'] }} kg</strong></td></tr>
        <tr><td class="summary-label">Volumetric weight</td><td><strong>{{ number_format($totals['volume_weight'], 2) }} kg</strong></td></tr>
        <tr><td class="summary-label">Custom value</td><td><strong>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</strong></td></tr>
    </table>
</div>

</body>
</html>
