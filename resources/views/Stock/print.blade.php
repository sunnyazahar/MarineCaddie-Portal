<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock List</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 18mm 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            margin-bottom: 12px;
            padding-bottom: 6px;
        }

        .customer-info {
            font-size: 11px;
            font-weight: bold;
            width: 75%;
            vertical-align: top;
            line-height: 1.45;
        }

        .customer-info .report-title {
            font-size: 12px;
            font-weight: bold;
            color: #002d5b;
        }

        .customer-info .report-customer {
            margin-top: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        .logo-container {
            width: 25%;
            text-align: right;
            vertical-align: top;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
            table-layout: fixed;
            word-wrap: break-word;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.data-table th {
            text-align: left;
            font-weight: bold;
            color: #004080;
            padding: 3px 2px;
            overflow: hidden;
            border-bottom: 1px solid #cbd5e1;
        }

        table.data-table td {
            padding: 3px 2px;
            vertical-align: top;
            overflow: hidden;
            word-break: break-word;
            border-bottom: 0.5px solid #f1f5f9;
        }

        .vessel-header td {
            background-color: #f9f9f9;
            font-weight: bold;
            padding: 5px 3px;
            border-bottom: 1px solid #ddd;
            color: #002d5b;
            text-transform: uppercase;
        }

        .vessel-total td {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 1px solid #cbd5e1;
            border-bottom: none;
            padding: 4px 2px;
            color: #0f172a;
        }

        .vessel-total-line td {
            padding: 0;
            height: 1px;
            line-height: 1px;
            border-bottom: 1.5px solid #002d5b;
            background: transparent;
        }

        .footer-table {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            font-size: 7px;
            color: #000000;
            padding: 0;
            background-color: #fff;
            line-height: 1.35;
        }

        .footer-td {
            vertical-align: top;
            padding: 0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <table class="footer-table">
        <tr>
            <td class="footer-td" style="width: 50%;">
                {!! \App\Support\CompanyAddress::htmlBlock() !!}
            </td>
            <td class="footer-td" style="width: 20%; text-align: center;"></td>
            <td class="footer-td" style="width: 30%; text-align: right;">
                Created on {{ now()->tz('Asia/Kolkata')->format('d.m.Y H:i') }} IST
            </td>
        </tr>
    </table>

    <table class="header-table">
        <tr>
            <td class="customer-info">
                <div class="report-title">VESSEL STOCKLIST : {{ $reportVesselName }}</div>
                <div class="report-customer">CUSTOMER NAME: {{ $reportCustomerName }}</div>
            </td>
            <td class="logo-container">
                {!! \App\Support\LogoHelper::imgTag('160px') !!}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">LOCATION</th>
                <th style="width: 7%;">STOCK NO.</th>
                <th style="width: 8%;">VESSEL NAME</th>
                <th style="width: 5%;">DOE</th>
                <th style="width: 8%;">SUPPLIER NAME</th>
                <th style="width: 8%;">SUPPLIER PO NUMBER</th>
                <th style="width: 6%;">LANDED CARGO</th>
                <th style="width: 4%;">Pcs</th>
                <th style="width: 5%;">WT. (KGS)</th>
                <th style="width: 7%;">DIMS (CM)</th>
                <th style="width: 5%;">CBM</th>
                <th style="width: 5%;">VOL. WT</th>
                <th style="width: 7%;">VALUE</th>
                <th style="width: 4%;">DG</th>
                <th style="width: 7%;">REMARKS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $vesselName => $crrs)
                @php
                    $vesselPcs = 0;
                    $vesselWeight = 0.0;
                    $vesselCbm = 0.0;
                    $vesselVolWt = 0.0;
                @endphp
                <tr class="vessel-header">
                    <td colspan="15">{{ $vesselName ?: 'UNKNOWN VESSEL' }}</td>
                </tr>
                @foreach($crrs as $crr)
                    @php
                        $packages = $crr->packages;
                        $pcs = $packages->count();
                        $weight = (float) $packages->sum('weight');
                        $cbm = \App\Support\PackageVolumeMetrics::totalCbm($packages);
                        $volumeWeight = \App\Support\PackageVolumeMetrics::totalAirVolumeWeightKg($packages);
                        $vesselPcs += $pcs;
                        $vesselWeight += $weight;
                        $vesselCbm += $cbm;
                        $vesselVolWt += $volumeWeight;
                        $poNumbers = is_array($crr->po_numbers)
                            ? implode(', ', $crr->po_numbers)
                            : (string) ($crr->po_numbers ?? '');
                        $dimensions = $packages
                            ->map(function ($pkg) {
                                if ($pkg->length === null && $pkg->width === null && $pkg->height === null) {
                                    return null;
                                }

                                return implode('x', [
                                    $pkg->length !== null ? (fmod((float) $pkg->length, 1.0) === 0.0 ? (string) (int) $pkg->length : rtrim(rtrim(number_format((float) $pkg->length, 2, '.', ''), '0'), '.')) : '0',
                                    $pkg->width !== null ? (fmod((float) $pkg->width, 1.0) === 0.0 ? (string) (int) $pkg->width : rtrim(rtrim(number_format((float) $pkg->width, 2, '.', ''), '0'), '.')) : '0',
                                    $pkg->height !== null ? (fmod((float) $pkg->height, 1.0) === 0.0 ? (string) (int) $pkg->height : rtrim(rtrim(number_format((float) $pkg->height, 2, '.', ''), '0'), '.')) : '0',
                                ]);
                            })
                            ->filter()
                            ->values()
                            ->implode(', ');
                        $value = $crr->customs_value !== null
                            ? number_format((float) $crr->customs_value, 2, '.', ',')
                            : '';
                        $currency = trim((string) ($crr->currency ?? ''));
                        $valueCurrency = trim($value . ($currency !== '' ? ' ' . $currency : ''));
                        $receivingDate = $crr->created_at
                            ? \Illuminate\Support\Carbon::parse($crr->created_at)->format('d/m/y')
                            : '—';
                        $hasDgr = $packages->contains(fn ($pkg) => (bool) $pkg->is_dgr);
                    @endphp
                    <tr>
                        <td>{{ $crr->hub_agent ?: ($crr->location ?: '—') }}</td>
                        <td>{{ $crr->stock_number ?: '—' }}</td>
                        <td>{{ $crr->vessel_name ?: '—' }}</td>
                        <td>{{ $receivingDate }}</td>
                        <td>{{ $crr->supplier ?: '—' }}</td>
                        <td>{{ $poNumbers !== '' ? $poNumbers : '—' }}</td>
                        <td>{{ $crr->is_landed_goods ? 'Yes' : '—' }}</td>
                        <td>{{ $pcs }}</td>
                        <td>{{ $weight > 0 ? number_format($weight, 2, '.', '') : '—' }}</td>
                        <td>{{ $dimensions !== '' ? $dimensions : '—' }}</td>
                        <td>{{ $cbm > 0 ? \App\Support\PackageVolumeMetrics::formatCbm($cbm) : '—' }}</td>
                        <td>{{ $volumeWeight > 0 ? number_format($volumeWeight, 2, '.', '') : '—' }}</td>
                        <td>{{ $valueCurrency !== '' ? $valueCurrency : '—' }}</td>
                        <td>{{ $hasDgr ? 'Yes' : '—' }}</td>
                        <td>{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</td>
                    </tr>
                @endforeach
                <tr class="vessel-total">
                    <td colspan="6">Total — {{ $vesselName ?: 'UNKNOWN VESSEL' }}</td>
                    <td></td>
                    <td>{{ $vesselPcs }}</td>
                    <td>{{ $vesselWeight > 0 ? number_format($vesselWeight, 2, '.', '') : '—' }}</td>
                    <td></td>
                    <td>{{ $vesselCbm > 0 ? \App\Support\PackageVolumeMetrics::formatCbm($vesselCbm) : '—' }}</td>
                    <td>{{ $vesselVolWt > 0 ? number_format($vesselVolWt, 2, '.', '') : '—' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="vessel-total-line">
                    <td colspan="15"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
