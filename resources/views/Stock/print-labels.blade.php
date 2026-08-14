<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Label - {{ $crr->stock_number }}</title>
    <style>
        @page {
            size: 150mm 100mm;
            margin: 0;
        }

        html, body {
            font-family: Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .label-page {
            width: 150mm;
            padding: 4mm 6mm 3mm 6mm;
            box-sizing: border-box;
            page-break-after: always;
        }

        .label-page:last-child {
            page-break-after: avoid;
        }

        .layout {
            width: 100%;
            border-collapse: collapse;
        }

        .layout td {
            border: 0;
            padding: 0;
        }

        .col-left {
            width: 50%;
            padding-right: 3mm;
            vertical-align: top;
        }

        .col-right {
            width: 50%;
            text-align: left;
            vertical-align: middle;
            padding-left: 2mm;
        }

        .brand img {
            width: 52mm;
            max-width: 52mm;
            height: auto;
        }

        .label-footer {
            font-size: 6.5px;
            line-height: 1.3;
            color: #333;
            text-align: left;
            padding-top: 3mm;
        }

        .field-group {
            margin-bottom: 1.2mm;
        }

        .field-label {
            font-size: 10px;
            color: #333;
            margin-bottom: 0.4mm;
        }

        .field-value {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.15;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3mm;
        }

        .meta td {
            border: 0;
            padding: 0 0 1.4mm 0;
            font-size: 11px;
            line-height: 1.2;
            vertical-align: top;
        }

        .meta-label {
            width: 28mm;
            color: #000;
        }

        .meta-value {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $totalWeight = $crr->packages->sum('weight');
        $totalPackages = $crr->packages->count();
    @endphp

    @foreach($crr->packages as $index => $pkg)
        <div class="label-page">
            <table class="layout">
                <tr>
                    <td class="col-left">
                        <div class="field-group">
                            <div class="field-label">Stock number</div>
                            <div class="field-value">{{ $crr->stock_number }}</div>
                        </div>
                        <div class="field-group">
                            <div class="field-label">Hub / Agent</div>
                            <div class="field-value" style="font-size: 12px;">{{ $consignee }}</div>
                        </div>
                        <div class="field-group">
                            <div class="field-label">Physical Location</div>
                            <div class="field-value" style="font-size: 12px;">{{ $crr->location ?: '—' }}</div>
                        </div>
                        <div class="field-group" style="margin-top: 3mm;">
                            <div class="field-label">To</div>
                            <div class="field-value"> MV {{ $crr->vessel_name ?: '—' }}</div>
                        </div>
                        <div class="field-group">
                            <div class="field-label">Supplier</div>
                            <div class="field-value" style="font-size: 12px;">{{ $crr->supplier ?: '—' }}</div>
                        </div>

                        <table class="meta">
                            <tr>
                                <td class="meta-label">Pos</td>
                                <td class="meta-value">{{ $pkg->warehouse_location ?: ($crr->location ?: '—') }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Pcs</td>
                                <td class="meta-value"># {{ $index + 1 }} of {{ $totalPackages }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Weight</td>
                                <td class="meta-value">{{ number_format($pkg->weight, 0) }} of {{ number_format($totalWeight, 0) }} KG</td>
                            </tr>
                            <tr>
                                <td class="meta-label">L/W/H (cm)</td>
                                <td class="meta-value">{{ number_format($pkg->length, 0) }}/{{ number_format($pkg->width, 0) }}/{{ number_format($pkg->height, 0) }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Transit Id</td>
                                <td class="meta-value" style="font-size: 12px;">{{ strtoupper($crr->transit_type ?: 'ETL') }} - {{ $crr->customs_doc_reference ?: ($crr->transit_id ?: '—') }}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="col-right">
                        <div class="brand">
                            <br><br>
                            {!! \App\Support\LogoHelper::imgTag('52mm') !!}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="label-footer">
                        <br><br><br><br><br>
                        {!! \App\Support\CompanyAddress::htmlBlock() !!}
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>

</html>
