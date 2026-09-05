<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock List</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/icofont/css/icofont.css') }}">
    <style>
        @page {
            size: A4;
            margin: 10mm 10mm 28mm 10mm;
        }

        .landed-badge {
            background: #dcf0fa !important;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
            vertical-align: middle;
            margin-right: 4px;
            text-transform: uppercase;
        }

        .icon-print {
            font-size: 10px;
            vertical-align: middle;
            margin-left: 2px;
        }

        .text-danger {
            color: #ff5252 !important;
        }

        .text-muted {
            color: #64748b !important;
        }

        .text-warning {
            color: #ffb64d !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            /* border-bottom: 0.5px solid #eee; */
            padding-bottom: 10px;
        }

        .customer-info {
            font-size: 11px;
            font-weight: bold;
            width: 75%;
            vertical-align: top;
        }

        .icon-print {
            font-size: 7px;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: bold;
            color: #fff;
            display: inline-block;
            vertical-align: middle;
            margin-right: 2px;
            text-transform: uppercase;
        }

        .bg-dgr {
            background-color: #ff5252 !important;
        }

        .bg-docs {
            background-color: #64748b !important;
        }

        .bg-info {
            background-color: #ffb64d !important;
        }

        .logo-container {
            width: 25%;
            text-align: right;
            vertical-align: top;
        }

        .logo-text {
            font-size: 16px;
            font-weight: bold;
            color: #002d5b;
            margin: 0;
            font-family: 'Inter', sans-serif;
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            text-align: center;
            line-height: 16px;
        }

        .logo-subtext {
            font-size: 8px;
            color: #666;
            margin-top: 1px;
        }

        .vessel-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .vessel-name {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #000;
        }

        /* .total-row {
            font-weight: bold;
            background-color: #fafafa;
        } */

        .total-row td {
            /* border-top: 1px solid #ccc; */
            padding: 2px 2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
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
            /* border-bottom: 1px solid #ccc; */
            padding: 4px 2px;
            overflow: hidden;
        }

        table.data-table td {
            padding: 4px 2px;
            /* border-bottom: 0.1px solid #f0f0f0; */
            vertical-align: top;
            overflow: hidden;
            word-break: break-all;
        }

        .text-right {
            text-align: left;
        }

        .text-center {
            text-align: left;
        }

        /* .total-row {
            font-weight: bold;
            background-color: #fafafa;
        } */

        .total-row td {
            /* border-top: 1px solid #ccc; */
            padding: 6px 2px;
        }

        .footer-table {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            font-size: 8px;
            color: #000000;
            padding: 0;
            background-color: #fff;
            line-height: 1.35;
        }

        .footer-td {
            vertical-align: top;
            padding: 0;
        }

        .totals-block {
            margin-top: 15px;
            border-top: 1px solid #002d5b;
            padding-top: 5px;
            page-break-inside: avoid;
        }

        i {
            font-family: 'Inter', sans-serif;
            font-size: 8px;
            color: #FF6B03;
            font-weight: 650;
        }
    </style>
</head>

<body>
    <table class="footer-table">
        <tr>
            <td class="footer-td" style="width: 50%; font-size: 8px;">
                {!! \App\Support\CompanyAddress::htmlBlock() !!}
            </td>
            <td class="footer-td text-center" style="width: 20%;">

            </td>
            <td class="footer-td text-right" style="width: 30%; font-size: 8px;">
                Created on {{ now()->tz('Asia/Kolkata')->format('d.m.Y H:i') }} IST
            </td>
        </tr>
    </table>
    <table class="header-table">
        <tr>
            <td class="customer-info">
                Stock list Report for {{ $reportCustomerName }}
            </td>
            <td class="logo-container" style="text-align:right;">
                {!! \App\Support\LogoHelper::imgTag('180px') !!}
            </td>
        </tr>
    </table>

    @php
        $grandTotalItems = 0;
        $grandTotalWeight = 0;
        $grandTotalCbm = 0;
        $grandTotalValue = 0;
        $grandTotalAir = 0;
        $grandTotalCourier = 0;
        foreach ($grouped as $vcrrs) {
            foreach ($vcrrs as $c) {
                $grandTotalItems += $c->packages->count();
                $grandTotalWeight += $c->packages->sum('weight');
                $grandTotalCbm += \App\Support\PackageVolumeMetrics::totalCbm($c->packages);
                $grandTotalValue += $c->customs_value_usd;
                $grandTotalAir += \App\Support\PackageVolumeMetrics::totalAirVolumeWeightKg($c->packages);
                $grandTotalCourier += \App\Support\PackageVolumeMetrics::totalCourierVolumeWeightKg($c->packages);
            }
        }
        $grandCurrency = $grouped->first() && $grouped->first()->first() ? $grouped->first()->first()->currency : 'USD';
    @endphp
    <table class="data-table" style="margin-top: 50px;">
        <thead>
            <tr>
                <th style="width: 4%;">Hub</th>
                <th style="width: 8%;">Entry Date</th>
                <th style="width: 15%;">PO numbers</th>
                <th style="width: 18%;">Supplier</th>
                <th class="text-center" style="width: 5%;">Pkgs</th>
                <th class="text-right" style="width: 7%;">Weight</th>
                <th class="text-right" style="width: 6%;">CBM</th>
                <th class="text-right" style="width: 10%">Value</th>
                <th style="width: 9%;">Status</th>
                <th style="width: 18%;">Stock No.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $vesselName => $crrs)
                @php
                    $vesselItems = 0;
                    $vesselWeight = 0;
                    $vesselCbm = 0;
                @endphp
                <tr>
                    <td colspan="10"
                        style="background-color: #f9f9f9; font-weight: bold; padding: 6px 4px; border-bottom: 1px solid #ddd; color: #002d5b; text-transform: uppercase;">
                        {{ $vesselName ?: 'UNKNOWN VESSEL' }}
                    </td>
                </tr>
                @foreach($crrs as $crr)
                    @php
                        $itemsCount = $crr->packages->count();
                        $weight = $crr->packages->sum('weight');
                        $cbm = \App\Support\PackageVolumeMetrics::totalCbm($crr->packages);

                        $vesselItems += $itemsCount;
                        $vesselWeight += $weight;
                        $vesselCbm += $cbm;

                        $poNumbers = is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : $crr->po_numbers;
                    @endphp
                    <tr>
                        <td style="width: 10px;">{{ $crr->hub_code }}</td>
                        <td style="width: 50px;">{{ $crr->expected_delivery_date ?: '—' }}</td>
                        <td style="width: 350px;">{{ $poNumbers ?: '—' }}</td>
                        <td style="width: 250px;">{{ $crr->supplier ?: '—' }}</td>
                        <td class="text-center" style="width: 20px;">{{ $itemsCount }}</td>
                        <td class="text-right" style="width: 20px;">{{ number_format($weight, 2, '.', ' ') }}</td>
                        <td class="text-right">{{ number_format($cbm, 2, '.', ' ') }}</td>
                        <td class="text-right">{{ number_format($crr->customs_value, 2, '.', ' ') }} <span
                                style="font-size:8px; font-weight:bold">{{ $crr->currency }}</span></td>
                        <td>{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</td>
                        <td>
                            <div style="font-weight: bold; font-size: 9px; margin-bottom: 2px;">{{ $crr->stock_number }}</div>
                            <div style="white-space: nowrap;">
                                @if($crr->is_landed_goods)
                                    <span class="landed-badge">Landed</span>
                                @endif
                                @php
                                    $hasDgrP = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                    $hasDocsP = $crr->documents->isNotEmpty();
                                    $isNotStackableP = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                @endphp
                                @if($hasDgrP)
                                    <span class="icon-print bg-dgr" title="Dangerous Goods">DGR</span>
                                @endif
                                @if($hasDocsP)
                                    <!-- <span class="icon-print bg-docs" title="Documents Attached">DOC</span> -->
                                @endif
                                @if($isNotStackableP)
                                    <span class="icon-print bg-info" title="Non-Stackable Content">Non stack</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                @php
                    $vesselValue = $crrs->sum('customs_value');
                    $currency = $crrs->first()->currency;
                @endphp

            @endforeach
        </tbody>
    </table>

    @if($grouped->count() > 0)
        <div class="totals-block">
            <table class="data-table" border="0" style="width: 50%; font-size: 9px;">
                <tr>
                    <td style="width: 250px; padding: 4px; ">Total No. of Packages</td>
                    <td style="padding: 4px; font-weight: bold;">{{ $grandTotalItems }}
                        pcs</td>
                </tr>
                <tr>
                    <td style="padding: 4px; ">Total Actual Weight (Kgs)</td>
                    <td style="padding: 4px; font-weight: bold;">
                        {{ number_format($grandTotalWeight, 2, '.', ' ') }} kg
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; ">Total Volume Weight for Air (Kgs)</td>
                    <td style="padding: 4px; font-weight: bold;">
                        {{ number_format($grandTotalAir, 2, '.', ' ') }} kg
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; ">Total Volume Weight for Courier (Kgs)</td>
                    <td style="padding: 4px; font-weight: bold;">
                        {{ number_format($grandTotalCourier, 2, '.', ' ') }} kg
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; ">Total Seafright (CBM)</td>
                    <td style="padding: 4px; font-weight: bold;">
                        {{ number_format($grandTotalCbm, 2, '.', ' ') }} CBM
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; ">Cargo Value</td>
                    <td style="padding: 4px; font-weight: bold;">
                        {{ number_format($grandTotalValue, 2, '.', ' ') }} USD
                    </td>
                </tr>

            </table>
        </div>
    @endif


</body>

</html>