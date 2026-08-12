<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRR - {{ $crr->stock_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 7mm 15mm;
        }

        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            display: table;
        }

        .header-col {
            display: table-cell;
            vertical-align: middle;
            font-size: 16px;
        }

        .hub-code {
            width: 15%;
            font-weight: 500;
        }

        .report-title {
            width: 70%;
            text-align: center;
            font-size: 25px;
            font-weight: 500;
        }

        .main-container {
            border: 0.5pt solid #ccc;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 0.5pt solid #ddd;
            padding: 6px 10px;
            text-align: left;
            vertical-align: top;
        }

        tr {
            height: 30px;
        }

        .stock-no-row {
            background-color: #fff;
            font-weight: 500;
        }

        .label-col {
            width: 180px;
            color: #555;
            background-color: #fafafa;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }

        .value-col {
            width: 250px;

        }

        .dim-col {
            padding: 0;
            vertical-align: top;
        }

        .dim-table {
            width: 100%;
            border: none;
        }

        .dim-table th,
        .dim-table td {
            border: none;
            /* border-bottom: 0.5pt solid #eee; */
            padding: 3px 6px;
            font-size: 9px;
        }

        .dim-table th {
            color: #000;
            font-weight: 500;
            /* border-bottom: 0.5pt solid #ddd; */
        }

        .dim-table tr:last-child td {
            border-bottom: none;
        }

        .empty-row {
            height: auto;
        }

        .landed-badge {
            background: #dcf0fa !important;
            border: 0.5pt solid #bae6fd;
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
            font-size: 7px;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: bold;
            color: #fff;
            display: inline-block;
            vertical-align: middle;
            margin-right: 2px;
            text-transform: uppercase;
            line-height: 1;
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

        .bg-medicine {
            background-color: #22c55e !important;
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
    <div class="header">
        <div class="header-col" style="text-align: right;">
            <img src="{{ \App\Support\LogoHelper::publicUrl() }}" alt="MarineCaddie" style="width:180px; max-width:180px;">
        </div>
    </div>
    <div class="header" style="margin-top: 70px;">
        <div class="header-col report-title">Cargo Receiving Report</div>
    </div>

    <div class="main-container">
        <table>
            <tr>
                <td class="label-col">Received Station / Location</td>
                <td colspan="1">
                    <b>
                        {{ collect([$hubAgentCode, $hubAgentName])->filter()->join(' - ') ?: '—' }}
                    </b>
                </td>
            </tr>
            <tr>
                <td class="label-col">Physical Location</td>
                <td colspan="1"><b>{{ $crr->location ?: '—' }}</b></td>
            </tr>
            <tr>
                <td class="label-col">Stock No: </td>
                <td colspan="1">{{ $crr->stock_number }}</td>

                <td rowspan="15" class="dim-col">
                    <table class="dim-table">
                        <thead>
                            <tr>
                                <th>L</th>
                                <th>W</th>
                                <th>H</th>
                                <th>Weight</th>
                                <th>WH Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crr->packages as $pkg)
                                <tr>
                                    <td>{{ number_format($pkg->length, 0) }}</td>
                                    <td>{{ number_format($pkg->width, 0) }}</td>
                                    <td>{{ number_format($pkg->height, 0) }}</td>
                                    <td>{{ number_format($pkg->weight, 0) }}</td>
                                    <td>
                                        <div style="font-size: 8px;">{{ $pkg->warehouse_location ?: ($crr->location ?: '—') }}</div>
                                        <div style="margin-top: 2px;">
                                            @if ($pkg->is_dgr)
                                                <span class="icon-print bg-dgr">DGR</span>
                                            @endif
                                            @if ($pkg->is_not_stackable)
                                                <span class="icon-print bg-info">Non stack</span>
                                            @endif
                                            @if ($pkg->is_medicine)
                                                <b><span class="icon-print bg-medicine" style="font-size: 10px;">+</span></b>
                                            @endif
                                            @if ($loop->first)
                                                @if ($crr->is_landed_goods)
                                                    <span class="landed-badge">Landed</span>
                                                @endif
                                                @if ($crr->documents->isNotEmpty())
                                                    <span class="icon-print bg-docs">DOC</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @for($i = $crr->packages->count(); $i < 5; $i++)
                                <tr class="empty-row">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="label-col">Vessel</td>
                <td class="value-col">{{ $crr->vessel_name ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Customer</td>
                <td class="value-col">
                    {{ $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '—' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Entered in the system</td>
                <td class="value-col">{{ $crr->created_at->tz('Asia/Kolkata')->format('d.m.Y H:i') }} IST</td>
            </tr>
            <tr>
                <td class="label-col">Received warehouse</td>
                <td class="value-col">
                    {{ $crr->actual_delivery_date ? \Carbon\Carbon::parse($crr->actual_delivery_date)->format('d.m.Y') : ($crr->expected_delivery_date ?: '—') }}
                </td>
            </tr>
            <tr>
                <td class="label-col">PO No.</td>
                <td class="value-col">
                    {{ is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?: '—') }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Supplier</td>
                <td class="value-col">{{ $crr->supplier ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Commodity</td>
                <td class="value-col">{{ $crr->content ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Pieces</td>
                <td class="value-col">{{ $crr->packages->count() }}</td>
            </tr>
            <tr>
                <td class="label-col">Weight</td>
                <td class="value-col">{{ number_format($crr->packages->sum('weight'), 0) }} kg</td>
            </tr>
            <tr>
                <td class="label-col">Customs value</td>
                <td class="value-col">{{ number_format($crr->customs_value, 2) }} {{ $crr->currency }}</td>
            </tr>
            <tr>
                <td class="label-col">Transit Type</td>
                <td class="value-col">{{ $crr->transit_type ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Transit Id</td>
                <td class="value-col">{{ $crr->hub_code }} - {{ $crr->stock_number }}</td>
            </tr>
            <tr>
                <td class="label-col">T1 reference</td>
                <td class="value-col">{{ $crr->customs_doc_reference ?: ($crr->transit_id ?: '—') }}</td>
            </tr>

        </table>
    </div>
</body>

</html>