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

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 5mm 8mm;
            color: #000;
        }

        .label-page {
            page-break-after: always;
            page-break-inside: avoid;
            position: relative;
            height: auto;
        }

        .label-page:last-child {
            page-break-after: avoid;
        }

        .header {
            width: 100%;
            margin-bottom: 4mm;
        }

        .header-left {
            float: left;
            width: 50%;
            font-size: 10px;
            line-height: 1.2;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
            line-height: 0.8;
        }

        .logo-text {
            font-weight: bold;
            font-size: 11pt;
            color: #002d5b;
            letter-spacing: 0.5pt;
        }

        .user-name {
            font-size: 12pt;
            margin-top: 2mm;
        }

        .clear {
            clear: both;
        }

        .transit-type {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 2mm;
        }

        .field-group {
            margin-bottom: 1.2mm;
        }

        .field-label {
            font-size: 10px;
            color: #333;
            margin-bottom: 0.5mm;
        }

        .field-value {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.1;
        }

        .small-field-group {
            margin-top: 3mm;
        }

        .small-row {
            margin-bottom: 1.5mm;
            font-size: 11px;
            vertical-align: middle;
            line-height: 1.2;
        }

        .small-label {
            display: inline-block;
            width: 32mm;
            vertical-align: middle;
        }

        .small-value {
            font-weight: bold;
            vertical-align: middle;
        }

        .footer-date {
            font-size: 11px;
            margin-bottom: 4mm;
        }

        i {
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            color: #FF6B03;
            font-weight: 650;
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
            <div class="header">
                <div class="header-left">
                    MarineCaddie India Private Limited<br>
                    +919560773375<br>
                    opsindia@marinecaddie.com
                </div>
                <div class="header-right">
                    <img src="{{ \App\Support\LogoHelper::publicUrl() }}" alt="MarineCaddie" style="width:160px; max-width:160px;">
                </div>
                <div class="clear"></div>
            </div>

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
            <div class="field-group" style="margin-top: 10px;">
                <div class="field-label">To</div>
                <div class="field-value"> MV {{ $crr->vessel_name ?: '—' }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Supplier</div>
                <div class="field-value" style="font-size: 12px;">{{ $crr->supplier ?: '—' }}</div>
            </div>

            <div class="small-field-group">
                <div class="small-row">
                    <span class="small-label">Pos</span>
                    <span class="small-value">{{ $pkg->warehouse_location ?: ($crr->location ?: '—') }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label">Pcs</span>
                    <span class="small-value"># {{ $index + 1 }} of {{ $totalPackages }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label">Weight</span>
                    <span class="small-value">{{ number_format($pkg->weight, 0) }} of {{ number_format($totalWeight, 0) }}
                        KG</span>
                </div>

                <div class="small-row">
                    <span class="small-label">L/W/H (cm)</span>
                    <span
                        class="small-value">{{ number_format($pkg->length, 0) }}/{{ number_format($pkg->width, 0) }}/{{ number_format($pkg->height, 0) }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label">Transit Id</span>
                    <span class="small-value"
                        style="font-weight:bold; font-size:12px;">{{ strtoupper($crr->transit_type ?: 'ETL') }} - {{ $crr->customs_doc_reference ?: ($crr->transit_id ?: '—') }}</span>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
