<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre-alert {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm 22mm 10mm; }
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
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .expected-line { margin: 0 0 10px; font-size: 11px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .data-table th, .data-table td { border: 0.5px solid #ccc; padding: 5px 4px; text-align: left; vertical-align: top; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .field-block { margin: 10px 0; font-size: 11px; }
        .field-label { font-weight: bold; margin-bottom: 4px; }
        .address-block { white-space: pre-wrap; font-size: 11px; margin-top: 4px; }
        .notify-title { font-size: 12px; font-weight: normal; margin: 12px 0 6px; }
        .vessel-heading { font-size: 12px; font-weight: bold; margin: 10px 0 6px; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px; }
        .summary-table td { padding: 4px 0; vertical-align: top; }
        .summary-label { width: 38%; font-weight: bold; }
        .description-cell { white-space: pre-wrap; }
    </style>
</head>
<body>

@php
    $header = function () use ($headerSubtitle) {
        return '
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">Pre-alert</div>
                    <div class="doc-subtitle">' . e($headerSubtitle) . '</div>
                </td>
                <td class="header-right" style="width:38%;">
                    <div class="brand-logo">
                        ' . \App\Support\LogoHelper::imgTag('180px') . '
                    </div>
                </td>
            </tr>
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
    {!! $header() !!}
<br>
    <div class="notify-title" style="margin-top:0;">
        {{ $isOnBoardDelivery ? 'On-board delivery details.' : 'Incoming shipment details.' }}
    </div>
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
                <th>Owners reference</th>
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
                <td>{{ $row['owners_reference'] }}</td>
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
    <div class="field-block">
        <div class="field-label">Account handled by</div>
        <div>{{ $accountHandledBy }}</div>
    </div>

    <div class="field-block">
        <div class="field-label">Issued by and shipped through</div>
        <div>MarineCaddie Shipping LLC <br>Email: ops@marinecaddie.com </div>
    </div>

</div>

<div class="page page-break">
    @if ($isOnBoardDelivery)
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">Pre-alert</div>
                    <div class="doc-subtitle">Shippers reference {{ $shippersReference }}</div>
                    <div class="doc-subtitle" style="margin-top:16px;">{{ $headerSubtitle }}</div>
                </td>
                <td class="header-right" style="width:38%;">
                    <div class="brand-logo">
                        {!! \App\Support\LogoHelper::imgTag('180px') !!}
                    </div>
                </td>
            </tr>
        </table>
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
        {!! $header() !!}

        <div class="notify-title">This is to notify incoming shipment under</div>
        @if (!empty($showReferenceColumn) && filled($awb) && $awb !== '—')
            <table class="summary-table">
                <tr><td class="summary-label">{{ $referenceColumnLabel }} {{ $awb }}</td></tr>
            </table>
        @endif
    @endif
    <div class="vessel-heading">{{ $vesselLine }}</div>

    @unless ($isOnBoardDelivery)
        <div class="field-block">
            <div class="address-block"><strong>C/O {{ $consigneeName }}</strong> <br> {{ $consigneeAddress }}</div>
        </div>
    @endunless

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
                <th>Stock no</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($preAlertRows as $row)
            <tr>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['po_number'] }}</td>
                <td>{{ $row['items'] }}</td>
                <td>{{ $row['weight'] }}</td>
                <td>{{ number_format($row['cbm'], 2) }}</td>
                <td>{{ $row['customs_value'] }}</td>
                <td class="description-cell">{{ $row['description'] }}</td>
                <td>{{ $row['stock_number'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>{{ $totalPiecesLabel }}</strong></td>
                <td><strong>{{ $totals['weight'] ?? 0 }} kg</strong></td>
                <td><strong>{{ number_format((float) ($totals['cbm'] ?? 0), 2) }} CBM</strong></td>
                <td colspan="3"><strong>{{ $customsValueLabel }}</strong></td>
            </tr>
        </tbody>
    </table>
    <table class="summary-table">
        <tr><td class="summary-label">Total pieces in consignment</td><td>{{ $totalPiecesLabel }}</td></tr>
        <tr><td class="summary-label">Gross Weight</td><td>{{ number_format((float) ($totals['weight'] ?? 0), 2) }} kg</td></tr>
        <tr><td class="summary-label">Estimated volume weight</td><td>{{ number_format((float) ($totals['volume_weight'] ?? 0), 2) }} kg</td></tr>
        <tr><td class="summary-label">Customs value</td><td>{{ $customsValueLabel }}</td></tr>
        <tr><td class="summary-label">Repacked as</td><td>{{ $packedAsLabel }}</td></tr>
    </table>
</div>

</body>
</html>
