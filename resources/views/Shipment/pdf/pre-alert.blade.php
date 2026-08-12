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
        .brand-marine { font-size: 20px; font-weight: bold; color: #002D5B; }
        .brand-caddie { font-size: 20px; font-weight: bold; color: #349DDA; }
        .brand-tagline { display: block; font-size: 9px; color: #FF6B03; font-weight: bold; margin-top: 2px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .expected-line { margin: 0 0 10px; font-size: 11px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .data-table th, .data-table td { border: 0.5px solid #ccc; padding: 5px 4px; text-align: left; vertical-align: top; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .field-block { margin: 10px 0; font-size: 11px; }
        .field-label { font-weight: bold; margin-bottom: 4px; }
        .address-block { white-space: pre-wrap; font-size: 11px; margin-top: 4px; }
        .notify-title { font-size: 12px; font-weight: bold; margin: 12px 0 6px; }
        .vessel-heading { font-size: 12px; font-weight: bold; margin: 10px 0 6px; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px; }
        .summary-table td { padding: 4px 0; vertical-align: top; }
        .summary-label { width: 38%; font-weight: bold; }
        .description-cell { white-space: pre-wrap; }
    </style>
</head>
<body>

@php
    $header = function () use ($headerSubtitle, $companyName, $companyAddress) {
        return '
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">Pre-alert</div>
                    <div class="doc-subtitle">' . e($headerSubtitle) . '</div>
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

    $flightColumnLabel = match ($shipment->service) {
        'Sea freight' => 'Vessel',
        'Truck' => 'Freight company',
        'Courier' => 'Carrier',
        'Release' => 'Freight company',
        'Hand Carry' => 'Contact',
        default => 'Flight',
    };
@endphp

<div class="page">
    {!! $header() !!}

    <div class="section-title">Freight details</div>
    <div class="expected-line">{{ $expectedLine }}</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Port of departure</th>
                <th>Port of destination</th>
                <th>Shippers reference</th>
                <th>AWB</th>
                <th>Owners reference</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $departurePortSimple }}</td>
                <td>{{ $destinationPortSimple }}</td>
                <td>{{ $shippersReference }}</td>
                <td>{{ $awb }}</td>
                <td>{{ $ownersReference }}</td>
            </tr>
        </tbody>
    </table>

    <table class="data-table" style="margin-top:10px;">
        <thead>
            <tr>
                <th>Service</th>
                <th>Additional service</th>
                <th>Departure port</th>
                <th>{{ $flightColumnLabel }}</th>
                <th>Arrival date</th>
                <th>Arrival time</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $serviceLabel }}</td>
                <td>{{ $additionalServiceLabel }}</td>
                <td>{{ $serviceDeparturePort }}</td>
                <td>{{ $flightNumber }}</td>
                <td>{{ $arrivalDate }}</td>
                <td>{{ $arrivalTime }}</td>
            </tr>
        </tbody>
    </table>

    <div class="field-block">
        <div class="field-label">Account handled by</div>
        <div>{{ $accountHandledBy }}</div>
    </div>

    <div class="field-block">
        <div class="field-label">Issued by and shipped through</div>
        <div style="font-weight:bold;">{{ $issuedByName }}</div>
        <div class="address-block">{{ $issuedByAddress }}</div>
    </div>

    <div class="notify-title">This is to notify incoming shipment to</div>
    <div class="vessel-heading">{{ $vesselLine }}</div>

    <div class="field-block">
        <div class="field-label">C/O</div>
        <div style="font-weight:bold;">{{ $consigneeName }}</div>
        <div class="address-block">{{ $consigneeAddressBlock }}</div>
    </div>
</div>

<div class="page page-break">
    {!! $header() !!}

    <div class="notify-title">This is to notify incoming shipment to</div>
    <div class="vessel-heading">{{ $vesselLine }}</div>

    <div class="field-block">
        <div class="field-label">C/O</div>
        <div style="font-weight:bold;">{{ $consigneeName }}</div>
    </div>

    <table class="summary-table">
        <tr><td class="summary-label">AWB</td><td>{{ $awb }}</td></tr>
        <tr><td class="summary-label">Total pieces in consignment</td><td>{{ $totalPiecesLabel }}</td></tr>
        <tr><td class="summary-label">Packed as</td><td>{{ $packedAsLabel }}</td></tr>
        <tr><td class="summary-label">Customs value</td><td>{{ $customsValueLabel }}</td></tr>
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
                <th>Stock no</th>
                <th>Location</th>
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
                <td>{{ $row['location'] ?? '—' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total {{ $primaryVessel }}</strong></td>
                <td><strong>{{ $totalPiecesLabel }}</strong></td>
                <td><strong>{{ $totals['weight'] ?? 0 }} kg</strong></td>
                <td><strong>{{ number_format((float) ($totals['cbm'] ?? 0), 2) }} CBM</strong></td>
                <td colspan="4"><strong>{{ $customsValueLabel }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
