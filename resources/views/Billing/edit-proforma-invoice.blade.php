@extends('layouts.app')

@section('styles')
    <style>
        body.proforma-edit-page .pcoded-inner-content,
        body.proforma-edit-page .main-body,
        body.proforma-edit-page .page-wrapper,
        body.proforma-edit-page .page-body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .proforma-edit-card {
            margin: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            background:
                radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.07), transparent 50%),
                radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.04), transparent 45%),
                #f5f7fb;
        }

        .proforma-edit-card > .card-block {
            padding: 8px 12px 20px !important;
            padding-bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .proforma-edit-body {
            padding: 0;
        }

        .proforma-form-panel {
            position: relative;
            background: #fff;
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            padding: 16px 16px 14px;
            margin-bottom: 14px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 10px 28px rgba(14, 29, 74, 0.05);
            overflow: hidden;
        }

        .proforma-form-panel::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
            pointer-events: none;
        }

        .proforma-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 991.98px) {
            .proforma-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .proforma-form-pillar {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 52%);
            border: 1px solid #e3edf3;
            border-radius: 12px;
            padding: 12px 14px 10px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .proforma-pillar__title {
            margin: 0 0 6px;
            padding: 0 0 10px 10px;
            border-bottom: 1px solid #e8eef4;
            border-left: 3px solid #008080;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .proforma-field-row {
            display: grid;
            grid-template-columns: 132px minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            min-height: 36px;
        }

        @media (max-width: 575.98px) {
            .proforma-field-row {
                grid-template-columns: 1fr;
                gap: 4px;
                min-height: auto;
            }

            .proforma-field-row label {
                text-align: left;
            }
        }

        .proforma-field-row label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-align: right;
            margin: 0;
            line-height: 1.25;
        }

        .proforma-radio-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            min-height: 34px;
        }

        .proforma-radio-option {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #0e1d4a;
            margin: 0;
            cursor: pointer;
        }

        .proforma-radio-option input[type="radio"] {
            margin: 0;
        }

        #proforma-edit-form .proforma-field-input .form-control {
            height: 34px;
            min-height: 34px;
            padding: 0 10px;
            font-size: 13px;
            color: #0e1d4a;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        #proforma-edit-form .proforma-field-input .form-control:hover:not(:disabled):not([readonly]) {
            border-color: #b7d9e8;
        }

        #proforma-edit-form .proforma-field-input .form-control:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #proforma-edit-form .proforma-field-input select.form-control {
            padding-right: 28px;
            cursor: pointer;
        }

        .proforma-field-input--with-addon {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .proforma-field-input--with-addon .form-control {
            flex: 1 1 auto;
            min-width: 0;
        }

        .proforma-field-addon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            box-shadow: 0 4px 10px rgba(0, 128, 128, 0.22);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .proforma-field-addon:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 128, 128, 0.28);
        }

        .proforma-field-addon i {
            font-size: 14px;
        }

        .proforma-split-field {
            display: flex;
            align-items: center;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .proforma-split-field:focus-within {
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        .proforma-split-field .form-control {
            flex: 1 1 0;
            min-width: 0;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .proforma-split-field .form-control:first-child {
            border-right: 1px solid #e8eef4 !important;
        }

        .proforma-weight-field {
            position: relative;
        }

        .proforma-weight-field .form-control {
            padding-right: 46px;
        }

        .proforma-weight-suffix {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 6px;
            pointer-events: none;
        }

        .proforma-readonly {
            background: #f8fafc !important;
            color: #475569 !important;
            border-color: #e2e8f0 !important;
            font-weight: 600;
        }

        .proforma-line-panel {
            background: #fff;
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 10px 28px rgba(14, 29, 74, 0.05);
        }

        .proforma-line-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 12px 16px;
            background: linear-gradient(135deg, #f0fafb 0%, #ffffff 55%, #f8fafc 100%);
            border-bottom: 1px solid #e3edf3;
        }

        .proforma-line-panel__title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #0e1d4a;
            letter-spacing: 0.02em;
        }

        .proforma-line-panel__title i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 13px;
        }

        .proforma-line-panel__hint {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .proforma-line-items-wrap {
            background: #fff;
            border: none;
            border-radius: 0;
            overflow: hidden;
        }

        .proforma-line-items-scroll {
            overflow-x: auto;
            overflow-y: visible;
            max-width: 100%;
        }

        #proforma-line-items-table {
            width: 100%;
            min-width: 2100px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        #proforma-line-items-table thead th {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            padding: 10px 6px;
            border-bottom: 2px solid #d6e3ee;
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        #proforma-line-items-table tbody td,
        #proforma-line-items-table tfoot td {
            padding: 5px 6px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background: #fff;
        }

        #proforma-line-items-table tbody tr:nth-child(even) td:not(.proforma-line-action-cell) {
            background: #fbfdff;
        }

        #proforma-line-items-table tbody tr:hover td:not(.proforma-line-action-cell) {
            background: #f0f9fa;
        }

        #proforma-line-items-table .form-control {
            font-size: 12px;
            padding: 4px 8px;
            height: 32px;
            min-height: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #0e1d4a;
            background: #fff;
        }

        #proforma-line-items-table .form-control:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 2px rgba(0, 136, 199, 0.12);
        }

        #proforma-line-items-table select.form-control {
            padding-right: 22px;
        }

        #proforma-line-items-table tfoot tr {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .proforma-line-action-cell {
            position: sticky;
            right: 0;
            z-index: 12;
            background: #fff !important;
            box-shadow: -8px 0 10px -6px rgba(15, 23, 42, 0.12);
            text-align: center;
            width: 52px;
            min-width: 52px;
        }

        #proforma-line-items-table thead th.proforma-line-action-cell {
            z-index: 22;
            background: #f1f5f9 !important;
        }

        #proforma-line-items-table tbody tr:nth-child(even) td.proforma-line-action-cell {
            background: #fbfdff !important;
        }

        #proforma-line-items-table tbody tr:hover td.proforma-line-action-cell {
            background: #f0f9fa !important;
        }

        .proforma-line-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
        }

        .proforma-line-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.16);
        }

        .proforma-line-btn--add {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .proforma-line-btn--delete {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
        }

        .proforma-line-btn i {
            font-size: 15px;
            line-height: 1;
        }

        .proforma-subtotal-label {
            font-size: 12px;
            font-weight: 800;
            color: #0e1d4a;
            text-align: right;
            padding-right: 8px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .proforma-subtotal-value {
            font-size: 13px;
            font-weight: 800;
            color: #008080;
            text-align: right;
            padding-right: 4px;
        }

        .proforma-net-payable-bar {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
            padding: 12px 14px;
            margin-top: 8px;
            border: 1px solid #c5d4e3;
            background: #f8fafc;
        }

        .proforma-net-payable-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
            width: 100%;
            max-width: 420px;
        }

        .proforma-net-payable-label {
            font-size: 12px;
            font-weight: 800;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            flex: 0 0 auto;
            min-width: 148px;
            text-align: right;
            margin: 0;
            line-height: 1.2;
        }

        .proforma-net-payable-row .form-control {
            flex: 0 0 120px;
            width: 120px;
            min-width: 120px;
            height: 34px;
            min-height: 34px;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 700;
            color: #1e3a5f;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            text-align: right;
            background: #fff;
        }

        .proforma-net-payable-row .form-control[readonly] {
            background: #f1f5f9;
            color: #0e1d4a;
        }

        .proforma-net-payable-value {
            font-size: 16px;
            font-weight: 800;
            color: #1e3a5f;
            flex: 0 0 120px;
            width: 120px;
            min-width: 120px;
            text-align: right;
        }

        .proforma-field-input--with-addon .select2-container {
            flex: 1 1 auto;
            min-width: 0;
            width: 100% !important;
        }

        #proforma-edit-form .proforma-field-input .select2-container--default .select2-selection--single {
            height: 34px;
            min-height: 34px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        #proforma-edit-form .proforma-field-input .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            padding-left: 10px;
            padding-right: 28px;
            font-size: 13px;
            color: #0e1d4a;
        }

        #proforma-edit-form .proforma-field-input .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
            top: 1px;
            right: 6px;
        }

        #proforma-edit-form .proforma-field-input .select2-container--default.select2-container--open .select2-selection--single,
        #proforma-edit-form .proforma-field-input .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #proforma-line-items-table .select2-container {
            width: 100% !important;
        }

        #proforma-line-items-table .select2-container--default .select2-selection--single {
            height: 32px;
            min-height: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
        }

        #proforma-line-items-table .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            padding-left: 8px;
            padding-right: 24px;
            font-size: 12px;
            color: #0e1d4a;
        }

        #proforma-line-items-table .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px;
            top: 1px;
            right: 4px;
        }

        #proforma-line-items-table .select2-container--default.select2-container--open .select2-selection--single,
        #proforma-line-items-table .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0088c7;
            box-shadow: 0 0 0 2px rgba(0, 136, 199, 0.12);
        }

        #proforma-line-items-table select.line-qty-type[data-qty-type-blank="1"] + .select2-container .select2-selection__placeholder {
            color: transparent;
        }

        .proforma-select2-dropdown {
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .proforma-select2-dropdown .select2-results__option {
            font-size: 13px;
            padding: 8px 10px;
        }

        .proforma-select2-dropdown .select2-results__option--highlighted[aria-selected] {
            background: #008080;
            color: #fff;
        }

        .proforma-select2-dropdown .select2-results__option[aria-selected="true"] {
            background: #008080;
            color: #fff;
        }

        .proforma-party-option__title {
            font-weight: 600;
            line-height: 1.2;
        }

        .proforma-party-option__subtitle {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.2;
        }

        .proforma-select2-dropdown .select2-results__option--highlighted[aria-selected] .proforma-party-option__title,
        .proforma-select2-dropdown .select2-results__option--highlighted[aria-selected] .proforma-party-option__subtitle,
        .proforma-select2-dropdown .select2-results__option[aria-selected="true"] .proforma-party-option__title,
        .proforma-select2-dropdown .select2-results__option[aria-selected="true"] .proforma-party-option__subtitle,
        .proforma-select2-dropdown .select2-results__option--highlighted[aria-selected] .port-option div,
        .proforma-select2-dropdown .select2-results__option[aria-selected="true"] .port-option div {
            color: #fff !important;
        }

        body.proforma-edit-page .select2-results__option--highlighted[aria-selected]:has(.port-option),
        body.proforma-edit-page .select2-results__option[aria-selected="true"]:has(.port-option) {
            background: #008080 !important;
            color: #fff !important;
        }

        body.proforma-edit-page .select2-results__option--highlighted[aria-selected]:has(.port-option) .port-option div,
        body.proforma-edit-page .select2-results__option[aria-selected="true"]:has(.port-option) .port-option div {
            color: #fff !important;
        }

        .proforma-form-actions {
            position: fixed !important;
            left: var(--spacing-sidebar, 13.25rem) !important;
            right: 0 !important;
            bottom: 0 !important;
            width: auto !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            border-top: 2px solid #008080;
            box-shadow: 0 -3px 12px rgba(15, 23, 42, 0.08);
            z-index: 1040;
        }

        .proforma-form-actions__inner {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            padding: 12px 12px max(12px, env(safe-area-inset-bottom, 0px));
        }

        @media (max-width: 991.98px) {
            body.proforma-edit-page .proforma-form-actions {
                left: 0 !important;
                right: 0 !important;
            }

            body.proforma-edit-page .proforma-form-actions__inner {
                padding-left: max(12px, env(safe-area-inset-left, 0px));
                padding-right: max(12px, env(safe-area-inset-right, 0px));
            }
        }

        .proforma-form-actions .btn {
            flex: 0 0 auto;
            min-width: 148px;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: 8px;
        }

        .proforma-form-actions .btn-generate-invoice {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.24);
        }

        .proforma-form-actions .btn-generate-invoice:hover:not(:disabled) {
            color: #fff;
            filter: brightness(1.05);
        }

        .proforma-form-actions .btn-generate-invoice:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
        }

        .proforma-form-actions .btn-update-invoice {
            background: #fff;
            border: 1px solid #008080;
            color: #008080;
        }

        .proforma-form-actions .btn-update-invoice:hover {
            background: #f0fafa;
            color: #006666;
            border-color: #006666;
        }

        .proforma-form-actions .btn-credit-note {
            background: #fff;
            border: 1px solid #d6e3ee;
            color: #0e1d4a;
        }

        .proforma-form-actions .btn-credit-note:hover {
            background: #f8fafc;
            border-color: #b7d9e8;
            color: #0e1d4a;
        }
    </style>
@endsection

@section('content')
    <div class="theme-loader">
        <div class="ball-scale">
            <div class="contain">
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card proforma-edit-card">
        <div class="card-block">
            <x-lists.page-header
                title="Edit Proforma Invoice"
                :subtitle="$invoice['job_no']"
                icon="ti-receipt"
            >
                <x-slot:actions>
                    <a href="{{ route('billing.invoicing') }}" class="btn btn-teal btn-sm">
                        <i class="ti-layout-list-thumb"></i> List Invoices
                    </a>
                </x-slot:actions>
            </x-lists.page-header>

            <div class="proforma-edit-body">
            <form id="proforma-edit-form" method="post" action="javascript:void(0)">
                @csrf
                <input type="hidden" name="shipment_id" id="shipment_id" value="{{ $shipmentId }}">
                <div class="proforma-form-panel">
                    <div class="proforma-form-grid">
                    <div class="proforma-form-pillar">
                        <h3 class="proforma-pillar__title">Parties &amp; shipment</h3>
                        <div class="proforma-field-row">
                            <label for="invoice_type">Invoice Type</label>
                            <div class="proforma-field-input">
                                <select id="invoice_type" name="invoice_type" class="form-control form-control-sm">
                                    <option value="" disabled @selected(empty($invoice['invoice_type']))>Select Invoice Type</option>
                                    @foreach ($invoiceTypeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected((string) $invoice['invoice_type'] === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="shipper-select">Select Shipper</label>
                            <div class="proforma-field-input">
                                <select id="shipper-select" name="shipper" class="form-control form-control-sm select2-departure">
                                    @if (!empty($invoice['shipper_departure']))
                                        <option value="{{ $invoice['shipper_departure'] }}" selected>{{ $invoice['shipper_departure_display'] }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="consignee-select">Select Consignee</label>
                            <div class="proforma-field-input">
                                <select id="consignee-select" name="consignee" class="form-control form-control-sm select2-consignee">
                                    @if (!empty($invoice['consignee']))
                                        <option value="{{ $invoice['consignee'] }}" selected>{{ $invoice['consignee_display'] }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="billing-party">Billing Party</label>
                            <div class="proforma-field-input">
                                <input
                                    type="text"
                                    id="billing-party"
                                    name="billing_party"
                                    class="form-control form-control-sm proforma-readonly"
                                    value="{{ $invoice['billing_party_display'] }}"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="bill_to_pos">Bill To Pos</label>
                            <div class="proforma-field-input">
                                <x-forms.country-select
                                    name="bill_to_pos"
                                    id="bill_to_pos"
                                    :value="$invoice['bill_to_pos']"
                                    :selectedText="$invoice['bill_to_pos_display']"
                                    :ajax="true"
                                    wrapperClass=""
                                    class="form-control form-control-sm"
                                    placeholder="Search country"
                                />
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="airport_of_loading">Airport Of Loading</label>
                            <div class="proforma-field-input">
                                <x-forms.port-select
                                    name="airport_of_loading"
                                    id="airport_of_loading"
                                    :value="$invoice['airport_of_loading']"
                                    wrapperClass=""
                                    class="form-control form-control-sm"
                                    placeholder="Search port code"
                                />
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="loading_date">Loading Date</label>
                            <div class="proforma-field-input">
                                <input type="text" id="loading_date" name="loading_date" class="form-control form-control-sm datepicker" value="{{ $invoice['loading_date'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label>HAWB / HBL No. &amp; Date</label>
                            <div class="proforma-field-input">
                                <div class="proforma-split-field">
                                    <input type="text" name="hawb_no" class="form-control form-control-sm" value="{{ $invoice['hawb_no'] }}">
                                    <input type="text" name="hawb_date" class="form-control form-control-sm datepicker" value="{{ $invoice['hawb_date'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label>MAWB / MBL No. &amp; Date</label>
                            <div class="proforma-field-input">
                                <div class="proforma-split-field">
                                    <input type="text" name="mawb_no" class="form-control form-control-sm" value="{{ $invoice['mawb_no'] }}">
                                    <input type="text" name="mawb_date" class="form-control form-control-sm datepicker" value="{{ $invoice['mawb_date'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="packages">Packages</label>
                            <div class="proforma-field-input">
                                <input type="text" id="packages" name="packages" class="form-control form-control-sm" value="{{ $invoice['packages'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="chargeable_wt">Chargeable Weight</label>
                            <div class="proforma-field-input">
                                <div class="proforma-weight-field">
                                    <input type="text" id="chargeable_wt" name="chargeable_wt" class="form-control form-control-sm" value="{{ $invoice['chargeable_wt'] }}">
                                    <span class="proforma-weight-suffix">KGS</span>
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="commodity">Commodity</label>
                            <div class="proforma-field-input">
                                <input type="text" id="commodity" name="commodity" class="form-control form-control-sm" value="{{ $invoice['commodity'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="type_of_supply">Type of Supply</label>
                            <div class="proforma-field-input">
                                <select id="type_of_supply" name="type_of_supply" class="form-control form-control-sm">
                                    <option value="" disabled @selected(empty($invoice['type_of_supply']))>Select service</option>
                                    @foreach ($serviceOptions as $serviceOption)
                                        <option value="{{ $serviceOption }}" @selected($invoice['type_of_supply'] === $serviceOption)>{{ $serviceOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="proforma-form-pillar">
                        <h3 class="proforma-pillar__title">Invoice reference</h3>
                        <div class="proforma-field-row">
                            <label for="proforma_no">Invoice No.</label>
                            <div class="proforma-field-input">
                                <input type="text" id="proforma_no" name="proforma_no" class="form-control form-control-sm proforma-readonly" value="{{ $invoice['proforma_no'] }}" readonly>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="proforma_date">Invoice Date</label>
                            <div class="proforma-field-input">
                                <input type="text" id="proforma_date" name="proforma_date" class="form-control form-control-sm datepicker" value="{{ $invoice['proforma_date'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="due_date">Due Date</label>
                            <div class="proforma-field-input">
                                <input type="text" id="due_date" name="due_date" class="form-control form-control-sm datepicker" value="{{ $invoice['due_date'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="client_ref_no">Client Ref No.</label>
                            <div class="proforma-field-input">
                                <input type="text" id="client_ref_no" name="client_ref_no" class="form-control form-control-sm" value="{{ $invoice['client_ref_no'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label>Job No. &amp; Date</label>
                            <div class="proforma-field-input">
                                <div class="proforma-split-field">
                                    <input type="text" name="job_no" class="form-control form-control-sm proforma-readonly" value="{{ $invoice['job_no'] }}" readonly>
                                    <input type="text" name="job_date" class="form-control form-control-sm datepicker" value="{{ $invoice['job_date'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="airport_of_destination">Airport Of Destination</label>
                            <div class="proforma-field-input">
                                <select id="airport_of_destination" name="airport_of_destination" class="form-control form-control-sm">
                                    <option value="{{ $invoice['airport_of_destination'] }}" selected>{{ $invoice['airport_of_destination'] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="destination_date">Destination Date</label>
                            <div class="proforma-field-input">
                                <input type="text" id="destination_date" name="destination_date" class="form-control form-control-sm datepicker" value="{{ $invoice['destination_date'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label>Flight No. &amp; Date</label>
                            <div class="proforma-field-input">
                                <div class="proforma-split-field">
                                    <input type="text" name="flight_no" class="form-control form-control-sm" value="{{ $invoice['flight_no'] }}">
                                    <input type="text" name="flight_date" class="form-control form-control-sm datepicker" value="{{ $invoice['flight_date'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="gross_wt">Gross Weight</label>
                            <div class="proforma-field-input">
                                <div class="proforma-weight-field">
                                    <input type="text" id="gross_wt" name="gross_wt" class="form-control form-control-sm" value="{{ $invoice['gross_wt'] }}">
                                    <span class="proforma-weight-suffix">KGS</span>
                                </div>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="vessel_name">Vessel Name</label>
                            <div class="proforma-field-input">
                                <input type="text" id="vessel_name" name="vessel_name" class="form-control form-control-sm" value="{{ $invoice['vessel_name'] }}">
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="currency">Currency</label>
                            <div class="proforma-field-input">
                                <select id="currency" name="currency" class="form-control form-control-sm select2-currency-ajax" data-placeholder="Search currency">
                                    <option value=""></option>
                                    @if (! empty($invoice['currency']))
                                        <option value="{{ $invoice['currency'] }}" selected>{{ $invoice['currency'] }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label for="einvoice_status">E-Invoice Status</label>
                            <div class="proforma-field-input">
                                <select id="einvoice_status" name="einvoice_status" class="form-control form-control-sm">
                                    @foreach (['Pending', 'Generated', 'Sent', 'Failed'] as $status)
                                        <option value="{{ $status }}" @selected($invoice['einvoice_status'] === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="proforma-field-row">
                            <label>Payment</label>
                            <div class="proforma-field-input">
                                <div class="proforma-radio-group">
                                    <label class="proforma-radio-option">
                                        <input
                                            type="radio"
                                            name="payment_type"
                                            value="partial_payment"
                                            required
                                            @checked(($invoice['payment_type'] ?? '') === 'partial_payment')
                                        >
                                        Partially payment
                                    </label>
                                    <label class="proforma-radio-option">
                                        <input
                                            type="radio"
                                            name="payment_type"
                                            value="full_payment"
                                            @checked(($invoice['payment_type'] ?? '') === 'full_payment')
                                        >
                                        Full payment
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="proforma-line-panel">
                    <div class="proforma-line-panel__header">
                        <span class="proforma-line-panel__title">
                            <i class="ti-list"></i>
                            Description of goods
                        </span>
                        <span class="proforma-line-panel__hint">Line item charges, exchange rates, and tax breakdown</span>
                    </div>
                <div class="proforma-line-items-wrap">
                    <div class="proforma-line-items-scroll" id="proforma-line-items-scroll">
                        <table id="proforma-line-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">Description of Goods</th>
                                    <th style="width: 72px;">HSN Code</th>
                                    <th style="width: 120px;">Remarks</th>
                                    <th style="width: 52px;">Qty</th>
                                    <th style="width: 62px;">Type</th>
                                    <th style="width: 72px;">Rate</th>
                                    <th style="width: 62px;">Curr</th>
                                    <th style="width: 88px;">Amount (Currency)</th>
                                    <th style="width: 72px;">Exch. Rate</th>
                                    <th style="width: 62px;">Tax Type</th>
                                    <th style="width: 88px;">Non-Taxable Value</th>
                                    <th style="width: 88px;">Taxable Value</th>
                                    <th style="width: 52px;">VAT %</th>
                                    <th style="width: 72px;">VAT Amt</th>
                                    <th class="proforma-line-action-cell">Action</th>
                                </tr>
                            </thead>
                            <tbody id="proforma-line-items-body">
                                @foreach ($invoice['line_items'] as $index => $item)
                                    <tr class="proforma-line-item-row">
                                        <td><input type="text" name="line_items[{{ $index }}][description]" class="form-control form-control-sm line-description" value="{{ $item['description'] }}"></td>
                                        <td><input type="text" name="line_items[{{ $index }}][hsn]" class="form-control form-control-sm" value="{{ $item['hsn'] }}"></td>
                                        <td><input type="text" name="line_items[{{ $index }}][remarks]" class="form-control form-control-sm" value="{{ $item['remarks'] }}"></td>
                                        <td><input type="text" name="line_items[{{ $index }}][qty]" class="form-control form-control-sm line-qty text-right" value="{{ $item['qty'] }}"></td>
                                        <td>
                                            @php
                                                $qtyTypeOptions = ['KG', 'Pcs', 'Job', 'SET', 'TRI'];
                                                $savedQtyType = empty($invoice['is_saved'])
                                                    ? ''
                                                    : (string) ($item['qty_type'] ?? '');
                                                $isBlankQtyType = $savedQtyType === '';
                                            @endphp
                                            <select
                                                name="line_items[{{ $index }}][qty_type]"
                                                class="form-control form-control-sm line-qty-type"
                                                @if ($isBlankQtyType) data-qty-type-blank="1" @endif
                                            >
                                                <option value="" @selected($isBlankQtyType)></option>
                                                @foreach ($qtyTypeOptions as $qtyType)
                                                    <option value="{{ $qtyType }}" @selected(!$isBlankQtyType && $savedQtyType === $qtyType)>{{ $qtyType }}</option>
                                                @endforeach
                                                @if ($savedQtyType !== '' && ! in_array($savedQtyType, $qtyTypeOptions, true))
                                                    <option value="{{ $savedQtyType }}" selected>{{ $savedQtyType }}</option>
                                                @endif
                                            </select>
                                        </td>
                                        <td><input type="text" name="line_items[{{ $index }}][rate]" class="form-control form-control-sm line-rate text-right" value="{{ $item['rate'] }}"></td>
                                        <td>
                                            <select name="line_items[{{ $index }}][currency]" class="form-control form-control-sm line-currency select2-currency-ajax" data-placeholder="Curr">
                                                <option value=""></option>
                                                @if (! empty($item['currency']))
                                                    <option value="{{ $item['currency'] }}" selected>{{ $item['currency'] }}</option>
                                                @endif
                                            </select>
                                        </td>
                                        <td><input type="text" name="line_items[{{ $index }}][amount]" class="form-control form-control-sm line-amount proforma-readonly text-right" value="{{ $item['amount'] }}" readonly></td>
                                        <td><input type="text" name="line_items[{{ $index }}][exchange_rate]" class="form-control form-control-sm line-exchange text-right" value="{{ $item['exchange_rate'] }}"></td>
                                        <td>
                                            <select name="line_items[{{ $index }}][tax_type]" class="form-control form-control-sm">
                                                <option value="T" @selected($item['tax_type'] === 'T')>T</option>
                                                <option value="E" @selected($item['tax_type'] === 'E')>E</option>
                                                <option value="Z" @selected($item['tax_type'] === 'Z')>Z</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="line_items[{{ $index }}][non_taxable]" class="form-control form-control-sm line-non-taxable text-right" value="{{ $item['non_taxable'] }}"></td>
                                        <td><input type="text" name="line_items[{{ $index }}][taxable]" class="form-control form-control-sm line-taxable text-right" value="{{ $item['taxable'] }}"></td>
                                        <td>
                                            <select name="line_items[{{ $index }}][igst_pct]" class="form-control form-control-sm line-igst-pct">
                                                @foreach (['0', '5', '12', '18', '28'] as $pct)
                                                    <option value="{{ $pct }}" @selected($item['igst_pct'] === $pct)>{{ $pct }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="line_items[{{ $index }}][igst_amt]" class="form-control form-control-sm line-igst-amt proforma-readonly text-right" value="{{ $item['igst_amt'] }}" readonly></td>
                                        <td class="proforma-line-action-cell">
                                            @if ($index === 0)
                                                <button type="button" class="proforma-line-btn proforma-line-btn--add proforma-line-add" title="Add row" aria-label="Add row"><i class="feather icon-plus"></i></button>
                                            @else
                                                <button type="button" class="proforma-line-btn proforma-line-btn--delete proforma-line-delete" title="Remove row" aria-label="Remove row"><i class="feather icon-x"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="proforma-subtotal-label">SubTotal</td>
                                    <td class="proforma-subtotal-value" id="proforma-subtotal-amount">0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td class="proforma-subtotal-value" id="proforma-subtotal-non-taxable">0.00</td>
                                    <td class="proforma-subtotal-value" id="proforma-subtotal-taxable">0.00</td>
                                    <td></td>
                                    <td class="proforma-subtotal-value" id="proforma-subtotal-vat">0.00</td>
                                    <td class="proforma-line-action-cell"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="proforma-net-payable-bar">
                        <div class="proforma-net-payable-row">
                            <span class="proforma-net-payable-label">Net Payable Amount</span>
                            <span class="proforma-net-payable-value" id="proforma-net-payable">0.00</span>
                        </div>
                        <div class="proforma-net-payable-row">
                            <label class="proforma-net-payable-label" for="paid_amount">Paid Amount</label>
                            <input
                                type="text"
                                id="paid_amount"
                                name="paid_amount"
                                class="form-control form-control-sm"
                                value="{{ $invoice['paid_amount'] ?? '' }}"
                                inputmode="decimal"
                            >
                        </div>
                        <div class="proforma-net-payable-row" id="proforma-due-amount-row">
                            <label class="proforma-net-payable-label" for="due_amount">Due Amount</label>
                            <input
                                type="text"
                                id="due_amount"
                                name="due_amount"
                                class="form-control form-control-sm proforma-readonly"
                                value="{{ $invoice['due_amount'] ?? '' }}"
                                readonly
                            >
                        </div>
                    </div>
                </div>
                </div>
            </form>

            <div class="proforma-form-actions" id="proforma-form-actions">
                <div class="proforma-form-actions__inner">
                    <button type="button" class="btn btn-credit-note" id="proforma-credit-note">
                        Credit Note
                    </button>
                    <button
                        type="button"
                        class="btn btn-update-invoice"
                        id="proforma-update-invoice"
                        @if (empty($invoice['is_saved'])) style="display:none;" @endif
                    >
                        Update
                    </button>
                    <button
                        type="button"
                        class="btn btn-generate-invoice"
                        id="proforma-generate-invoice"
                        @if (!empty($invoice['is_saved'])) disabled @endif
                    >
                        Generate Invoice
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('proforma-edit-page');

            var proformaIsSaved = @json(!empty($invoice['is_saved']));

            var $proformaFooter = $('#proforma-form-actions');
            if ($proformaFooter.length && !$proformaFooter.parent().is('body')) {
                $proformaFooter.appendTo('body');
            }

            function initProformaSelect2($scope) {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                $scope.find('select').not('.select2-departure, .select2-consignee, .select2-port-code, .select2-currency-ajax, .line-qty-type, [data-country-select], [data-country-select-ajax]').each(function() {
                    var $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    var isLineItem = $select.closest('#proforma-line-items-table').length > 0;
                    var options = {
                        width: '100%',
                        allowClear: false,
                        minimumResultsForSearch: 0,
                        dropdownCssClass: 'proforma-select2-dropdown'
                    };

                    if (isLineItem) {
                        options.dropdownParent = $(document.body);
                    }

                    $select.select2(options);
                });
            }

            function initLineQtyTypeSelects($scope) {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                var defaultQtyTypes = ['KG', 'Pcs', 'Job', 'SET', 'TRI'];
                var blankPlaceholder = '\u200b';

                $scope.find('.line-qty-type').each(function() {
                    var $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    var mustBeBlank = $select.is('[data-qty-type-blank="1"]') || !proformaIsSaved;

                    if (mustBeBlank) {
                        $select.find('option').prop('selected', false);
                        $select.find('option[value=""]').prop('selected', true);
                        $select.val('');
                    }

                    $select.select2({
                        width: '100%',
                        allowClear: mustBeBlank,
                        tags: true,
                        minimumResultsForSearch: 0,
                        dropdownCssClass: 'proforma-select2-dropdown',
                        dropdownParent: $(document.body),
                        placeholder: mustBeBlank ? blankPlaceholder : '',
                        createTag: function(params) {
                            var term = $.trim(params.term);

                            if (term === '') {
                                return null;
                            }

                            if (defaultQtyTypes.indexOf(term) !== -1) {
                                return null;
                            }

                            return {
                                id: term,
                                text: term,
                                newTag: true
                            };
                        }
                    });

                    if (mustBeBlank) {
                        $select.val(null).trigger('change');
                        window.setTimeout(function() {
                            if (!$select.val()) {
                                $select.next('.select2-container').find('.select2-selection__rendered').empty();
                            }
                        }, 0);
                    }

                    $select.on('select2:select select2:unselect select2:clear', function() {
                        if ($(this).val()) {
                            $(this).removeAttr('data-qty-type-blank');
                        }
                    });
                });
            }

            function destroyProformaSelect2($scope) {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                $scope.find('select.select2-hidden-accessible').each(function() {
                    $(this).select2('destroy');
                });
            }

            function resetClonedSelects($scope) {
                // Cloning a Select2 row copies the rendered .select2-container;
                // remove it so initProformaSelect2 creates a single UI again.
                $scope.find('.select2-container').remove();
                $scope.find('select').each(function() {
                    var $select = $(this);
                    $select.removeClass('select2-hidden-accessible')
                        .removeAttr('data-select2-id')
                        .removeAttr('aria-hidden')
                        .removeAttr('tabindex')
                        .css('display', '');
                    $select.find('option').removeAttr('data-select2-id');
                });
            }

            function formatParty(item) {
                if (!item.id) {
                    return item.text;
                }

                var subtitleParts = [];
                if (item.type_label) {
                    subtitleParts.push(item.type_label);
                }
                if (item.subtitle) {
                    subtitleParts.push(item.subtitle);
                }
                if (item.type && subtitleParts.length === 0) {
                    subtitleParts.push(item.type);
                }

                var subtitle = subtitleParts.join(' · ');
                return $('<div class="proforma-party-option"><div class="proforma-party-option__title">' + item.text + '</div>' + (subtitle ? '<div class="proforma-party-option__subtitle">' + subtitle + '</div>' : '') + '</div>');
            }

            function formatPartySelection(item) {
                return item.text || item.id;
            }

            function initDeparturePartySelects() {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                $('.select2-departure').each(function() {
                    var $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $select.select2({
                        placeholder: 'Type departure',
                        allowClear: false,
                        width: '100%',
                        minimumResultsForSearch: 0,
                        dropdownCssClass: 'proforma-select2-dropdown',
                        ajax: {
                            url: '{{ url('/api/parties') }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return { q: params.term };
                            },
                            processResults: function(data) {
                                return { results: data };
                            }
                        },
                        templateResult: formatParty,
                        templateSelection: formatPartySelection
                    });
                });
            }

            function initProformaCurrencyAjaxSelect($scope) {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                $scope.find('.select2-currency-ajax').each(function() {
                    var $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    var isLineItem = $select.closest('#proforma-line-items-table').length > 0;

                    $select.select2({
                        placeholder: $select.attr('data-placeholder') || 'Search currency',
                        allowClear: false,
                        width: '100%',
                        minimumInputLength: 0,
                        minimumResultsForSearch: 0,
                        dropdownCssClass: 'proforma-select2-dropdown',
                        dropdownParent: isLineItem ? $(document.body) : undefined,
                        ajax: {
                            url: @json(route('api.currencies')),
                            dataType: 'json',
                            delay: 200,
                            data: function(params) {
                                return { q: params.term || '' };
                            },
                            processResults: function(data) {
                                return { results: data.results || [] };
                            },
                            cache: true
                        }
                    });
                });
            }

            function initProformaPortSelectDropdowns() {
                setTimeout(function() {
                    $('#airport_of_loading').each(function() {
                        var s2 = $(this).data('select2');
                        if (s2) {
                            s2.options.set('dropdownCssClass', 'proforma-select2-dropdown');
                        }
                    });
                }, 0);
            }

            function initConsigneeSelect() {
                if (typeof $.fn.select2 !== 'function') {
                    return;
                }

                var $select = $('#consignee-select');
                if (!$select.length || $select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $select.select2({
                    placeholder: 'Type consignee',
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: 0,
                    dropdownCssClass: 'proforma-select2-dropdown',
                    ajax: {
                        url: '{{ url('/api/consignees') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { q: params.term };
                        },
                        processResults: function(data) {
                            return { results: data };
                        }
                    },
                    templateResult: formatParty,
                    templateSelection: formatPartySelection
                });
            }

            function initProformaDatepickers($scope) {
                if (typeof $.fn.datepicker !== 'function') {
                    return;
                }

                $scope.find('.datepicker').each(function() {
                    var $input = $(this);
                    if ($input.hasClass('hasDatepicker')) {
                        return;
                    }

                    $input.datepicker({
                        dateFormat: 'dd.mm.yy',
                        changeMonth: true,
                        changeYear: true
                    });
                });
            }

            function parseAmount(value) {
                var num = parseFloat(String(value || '').replace(/,/g, '').trim());
                return isNaN(num) ? 0 : num;
            }

            function formatAmount(num) {
                return num.toFixed(2);
            }

            function recalcLineRow($row) {
                var qty = parseAmount($row.find('.line-qty').val());
                var rate = parseAmount($row.find('.line-rate').val());
                var exchangeRate = parseAmount($row.find('.line-exchange').val());
                if (exchangeRate <= 0) {
                    exchangeRate = 1;
                }

                var amount = qty * rate;
                var taxable = amount * exchangeRate;

                $row.find('.line-amount').val(formatAmount(amount));
                $row.find('.line-taxable').val(formatAmount(taxable));
                recalcVatAmount($row);
            }

            function recalcVatAmount($row) {
                var taxable = parseAmount($row.find('.line-taxable').val());
                var vatPct = parseAmount($row.find('.line-igst-pct').val());
                var vatAmt = taxable * (vatPct / 100);
                $row.find('.line-igst-amt').val(formatAmount(vatAmt));
            }

            function getNetPayableAmount() {
                return parseAmount($('#proforma-net-payable').text());
            }

            function updateDueAmount() {
                var netPayable = getNetPayableAmount();
                var paid = parseAmount($('#paid_amount').val());
                var due = Math.max(0, netPayable - paid);

                $('#due_amount').val(formatAmount(due));
            }

            function syncPaymentTypeFromPaidAmount() {
                var netPayable = getNetPayableAmount();
                var paid = parseAmount($('#paid_amount').val());
                var paymentType = $('input[name="payment_type"]:checked').val();

                if (netPayable > 0 && paid >= netPayable) {
                    $('input[name="payment_type"][value="full_payment"]').prop('checked', true);
                } else if (paymentType === 'full_payment' && paid < netPayable) {
                    $('input[name="payment_type"][value="partial_payment"]').prop('checked', true);
                }

                applyPaymentTypePaidAmount();
            }

            function applyPaymentTypePaidAmount() {
                var paymentType = $('input[name="payment_type"]:checked').val();
                var $dueRow = $('#proforma-due-amount-row');

                if (paymentType === 'full_payment') {
                    $dueRow.hide();
                    $('#due_amount').val(formatAmount(0));
                } else {
                    $dueRow.show();
                    updateDueAmount();
                }
            }

            function updateSubtotals() {
                var totalAmount = 0;
                var totalNonTaxable = 0;
                var totalTaxable = 0;
                var totalVat = 0;

                $('#proforma-line-items-body .proforma-line-item-row').each(function() {
                    totalAmount += parseAmount($(this).find('.line-amount').val());
                    totalNonTaxable += parseAmount($(this).find('.line-non-taxable').val());
                    totalTaxable += parseAmount($(this).find('.line-taxable').val());
                    totalVat += parseAmount($(this).find('.line-igst-amt').val());
                });

                $('#proforma-subtotal-amount').text(formatAmount(totalAmount));
                $('#proforma-subtotal-non-taxable').text(formatAmount(totalNonTaxable));
                $('#proforma-subtotal-taxable').text(formatAmount(totalTaxable));
                $('#proforma-subtotal-vat').text(formatAmount(totalVat));
                $('#proforma-net-payable').text(formatAmount(totalNonTaxable + totalTaxable + totalVat));
                syncPaymentTypeFromPaidAmount();
            }

            function reindexLineItemNames() {
                $('#proforma-line-items-body .proforma-line-item-row').each(function(rowIndex) {
                    $(this).find('[name^="line_items["]').each(function() {
                        var name = $(this).attr('name');
                        if (!name) {
                            return;
                        }
                        $(this).attr('name', name.replace(/line_items\[\d+\]/, 'line_items[' + rowIndex + ']'));
                    });
                });
            }

            function refreshLineActionButtons() {
                $('#proforma-line-items-body .proforma-line-item-row').each(function(index) {
                    var $cell = $(this).find('.proforma-line-action-cell');
                    $cell.empty();

                    if (index === 0) {
                        $cell.append(
                            '<button type="button" class="proforma-line-btn proforma-line-btn--add proforma-line-add" title="Add row" aria-label="Add row"><i class="feather icon-plus"></i></button>'
                        );
                    } else {
                        $cell.append(
                            '<button type="button" class="proforma-line-btn proforma-line-btn--delete proforma-line-delete" title="Remove row" aria-label="Remove row"><i class="feather icon-x"></i></button>'
                        );
                    }
                });
            }

            function bindLineRowEvents($row) {
                $row.find('.line-qty, .line-rate, .line-exchange').on('input change', function() {
                    recalcLineRow($row);
                    updateSubtotals();
                });

                $row.find('.line-taxable').on('input change', function() {
                    recalcVatAmount($row);
                    updateSubtotals();
                });

                $row.find('.line-non-taxable').on('input change', function() {
                    updateSubtotals();
                });

                $row.find('.line-igst-pct').on('change', function() {
                    recalcVatAmount($row);
                    updateSubtotals();
                });
            }

            function addLineItemRow() {
                var $rows = $('#proforma-line-items-body .proforma-line-item-row');
                var $template = $rows.first().clone(false, false);
                var nextIndex = $rows.length;
                var defaultQtyTypes = ['KG', 'Pcs', 'Job', 'SET', 'TRI'];

                $template.find('input, select').each(function() {
                    if ($(this).hasClass('line-qty-type')) {
                        $(this).find('option').each(function() {
                            var val = $(this).val();
                            if (val && defaultQtyTypes.indexOf(val) === -1) {
                                $(this).remove();
                            }
                        });
                        $(this).find('option').prop('selected', false);
                        $(this).find('option[value=""]').prop('selected', true);
                        $(this).attr('data-qty-type-blank', '1');
                        $(this).val('');
                    } else if ($(this).is('select')) {
                        $(this).prop('selectedIndex', 0);
                    } else if ($(this).hasClass('line-amount')) {
                        $(this).val('0.00');
                    } else if ($(this).hasClass('line-qty')) {
                        $(this).val(proformaIsSaved ? '1' : '');
                    } else if ($(this).hasClass('line-rate')) {
                        $(this).val(proformaIsSaved ? '0' : '');
                    } else if ($(this).hasClass('line-exchange')) {
                        $(this).val('1');
                    } else if ($(this).hasClass('line-non-taxable') || $(this).hasClass('line-taxable') ||
                        $(this).hasClass('line-igst-amt')) {
                        $(this).val('0.00');
                    } else {
                        $(this).val('');
                    }
                });

                resetClonedSelects($template);

                $template.find('[name]').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/line_items\[\d+\]/, 'line_items[' + nextIndex + ']'));
                    }
                });

                $('#proforma-line-items-body').append($template);
                initProformaCurrencyAjaxSelect($template);
                initLineQtyTypeSelects($template);
                initProformaSelect2($template);
                bindLineRowEvents($template);
                refreshLineActionButtons();
                recalcLineRow($template);
                updateSubtotals();
            }

            $(document).on('click', '.proforma-line-add', function() {
                addLineItemRow();
            });

            $(document).on('click', '.proforma-line-delete', function() {
                var $rows = $('#proforma-line-items-body .proforma-line-item-row');
                if ($rows.length <= 1) {
                    return;
                }

                var $row = $(this).closest('.proforma-line-item-row');
                destroyProformaSelect2($row);
                $row.remove();
                reindexLineItemNames();
                refreshLineActionButtons();
                updateSubtotals();
            });

            $('#proforma-line-items-body .proforma-line-item-row').each(function() {
                bindLineRowEvents($(this));
                recalcLineRow($(this));
            });

            initProformaDatepickers($('#proforma-edit-form'));
            initDeparturePartySelects();
            if (window.MarineCaddieInitPortSelect) {
                window.MarineCaddieInitPortSelect();
            }
            initProformaPortSelectDropdowns();
            if (window.MarineCaddieInitCountrySelect) {
                window.MarineCaddieInitCountrySelect('#bill_to_pos');
            }
            initProformaCurrencyAjaxSelect($('#proforma-edit-form'));
            initLineQtyTypeSelects($('#proforma-edit-form'));
            initConsigneeSelect();
            initProformaSelect2($('#proforma-edit-form'));
            updateSubtotals();

            $('#paid_amount').on('input change', function() {
                syncPaymentTypeFromPaidAmount();
            });

            $('input[name="payment_type"]').on('change', function() {
                applyPaymentTypePaidAmount();
            });

            function setProformaSavedState(isSaved) {
                proformaIsSaved = !!isSaved;
                var $generate = $('#proforma-generate-invoice');
                var $update = $('#proforma-update-invoice');

                if (proformaIsSaved) {
                    $generate.prop('disabled', true);
                    $update.show();
                } else {
                    $generate.prop('disabled', false);
                    $update.hide();
                }
            }

            function extractAjaxErrorMessage(xhr, fallbackMessage) {
                var message = fallbackMessage;

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var firstError = Object.values(xhr.responseJSON.errors)[0];
                    if (Array.isArray(firstError) && firstError.length) {
                        message = firstError[0];
                    }
                }

                return message;
            }

            function validatePaymentTypeSelection() {
                if ($('input[name="payment_type"]:checked').length === 0) {
                    swal('Required', 'Please select Partially payment or Full payment.', 'warning');
                    return false;
                }

                return true;
            }

            function submitProformaInvoice(options) {
                if (!validatePaymentTypeSelection()) {
                    if (options.onError) {
                        options.onError();
                    }
                    return;
                }

                var formData = $('#proforma-edit-form').serializeArray();

                if (options.previewProformaNo) {
                    formData.push({ name: 'preview_proforma_no', value: options.previewProformaNo });
                }

                $.ajax({
                    url: '{{ route('billing.invoicing.store') }}',
                    type: 'POST',
                    data: $.param(formData),
                    success: function(saveResponse) {
                        if (!saveResponse.success) {
                            swal('Error', saveResponse.message || options.errorMessage, 'error');
                            if (options.onError) {
                                options.onError();
                            }
                            return;
                        }

                        $('#proforma_no').val(saveResponse.proforma_no || '');
                        setProformaSavedState(true);

                        swal({
                            title: saveResponse.is_update ? 'Updated' : 'Saved',
                            text: saveResponse.message || options.successMessage,
                            type: 'success',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        if (options.onSuccess) {
                            options.onSuccess(saveResponse);
                        }
                    },
                    error: function(xhr) {
                        swal('Error', extractAjaxErrorMessage(xhr, options.errorMessage), 'error');
                        if (options.onError) {
                            options.onError();
                        }
                    }
                });
            }

            setProformaSavedState(proformaIsSaved);

            $('#proforma-generate-invoice').on('click', function() {
                var $button = $(this);

                if ($button.prop('disabled') || proformaIsSaved) {
                    return;
                }

                if (!validatePaymentTypeSelection()) {
                    return;
                }

                $button.prop('disabled', true);

                $.get('{{ route('billing.invoicing.preview-proforma-number') }}', {
                    proforma_date: $('#proforma_date').val()
                })
                    .done(function(response) {
                        var proformaNo = response.proforma_no || '';

                        swal({
                            title: 'Generate Proforma Invoice',
                            text: 'Invoice No.: ' + proformaNo + '\n\nSave all invoice details to the database?',
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Save',
                            cancelButtonText: 'Cancel',
                            closeOnConfirm: false,
                            closeOnCancel: true,
                            showLoaderOnConfirm: true
                        }, function(isConfirm) {
                            if (!isConfirm) {
                                setProformaSavedState(proformaIsSaved);
                                return;
                            }

                            submitProformaInvoice({
                                previewProformaNo: proformaNo,
                                successMessage: 'Proforma invoice saved successfully.',
                                errorMessage: 'Could not save proforma invoice.',
                                onError: function() {
                                    setProformaSavedState(proformaIsSaved);
                                }
                            });
                        });
                    })
                    .fail(function() {
                        swal('Error', 'Could not generate proforma number.', 'error');
                        setProformaSavedState(proformaIsSaved);
                    });
            });

            $('#proforma-update-invoice').on('click', function() {
                if (!proformaIsSaved) {
                    return;
                }

                if (!validatePaymentTypeSelection()) {
                    return;
                }

                var proformaNo = $('#proforma_no').val() || '';

                swal({
                    title: 'Update Proforma Invoice?',
                    text: 'Update saved proforma' + (proformaNo ? ' ' + proformaNo : '') + ' with the current form details?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    submitProformaInvoice({
                        successMessage: 'Proforma invoice updated successfully.',
                        errorMessage: 'Could not update proforma invoice.'
                    });
                });
            });
        });
    </script>
@endpush
