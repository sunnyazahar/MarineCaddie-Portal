@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet"
        href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <!-- Date Picker css -->
    <link rel="stylesheet" href="{{ asset('files/assets/pages/advance-elements/css/bootstrap-datetimepicker.css') }}" />
    <style>
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #349dda;
            color: white;
        }

        /* CRR Form Specific Styles */
        .crr-section-title {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }

        .crr-form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .crr-col {
            padding: 0 10px;
            flex: 0 0 25%;
            max-width: 25%;
            min-width: 0;
            overflow: visible;
            /* Changed from hidden to prevent datepicker clipping */
        }

        @media (max-width: 991.98px) {
            .crr-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 767.98px) {
            .crr-col {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .form-footer-custom {
                flex-wrap: wrap;
                gap: 8px;
                padding: 10px 12px;
            }
        }

        .crr-data-table {
            min-width: 900px;
        }

        .crr-field-group {
            margin-bottom: 15px;
        }

        .crr-label {
            display: block;
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 3px;
        }

        .crr-input {
            width: 100%;
            height: 32px;
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            outline: none;
        }

        .crr-input:focus {
            border-color: #008080;
        }

        .crr-checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0;
            font-size: 12px;
            color: #4b5563;
        }

        /* Tables Styling */
        .crr-data-table {
            width: 100%;
            margin-top: 25px;
            background: #fff;
            border: 1px solid #f3f4f6;
        }

        .crr-data-table th {
            background-color: #fdfdfd;
            padding: 10px;
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .crr-data-table td {
            padding: 8px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .crr-table-header {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin: 30px 0 10px 0;
        }

        .btn-add-item {
            float: right;
            margin-top: 10px;
            font-size: 11px;
            padding: 4px 12px;
        }

        .form-footer-custom {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 185px;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-save-custom {
            background-color: #1b5e6f;
            color: white;
            border: none;
            padding: 8px 30px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
        }

        .crr-value-display {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-top: 5px;
        }

        /* Select2 Reset */
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
            min-height: 32px !important;
            padding-bottom: 2px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #4b5563 !important;
            line-height: normal !important;
            font-size: 12px !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            margin-top: 0 !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #404e67 !important;
            border: 1px solid #d1d5db !important;
            color: #FFFFFF !important;
            padding: 1px 8px !important;
            margin-top: 4px !important;
            font-size: 11px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice span {
            color: #FFFFFF !important;
            display: none !important;
        }

        /* Remarks Select2: compact single-line in table cells */
        .select2-remarks-container .select2-selection--multiple {
            height: 30px !important;
            max-height: 30px !important;
            overflow: hidden !important;
            border: 1px solid #d1d5db !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            background: #fff !important;
            min-width: 0 !important;
        }
        .select2-remarks-container .select2-selection__rendered {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            overflow: hidden !important;
            padding: 0 4px !important;
            margin: 0 !important;
            list-style: none !important;
            white-space: nowrap !important;
        }
        .select2-remarks-container .select2-selection__choice {
            height: 22px !important;
            margin: 2px !important;
            font-size: 10px !important;
            padding: 0 16px 0 5px !important;
        }

        /* Delivery Irregularities: fixed-height multi-select with ellipsis */
        .select2-irreg-container {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            display: block !important;
        }

        .select2-irreg-container .select2-selection--multiple {
            height: 32px !important;
            max-height: 32px !important;
            overflow: hidden !important;
            border: 1px solid #d1d5db !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            background: #fff !important;
            width: 100% !important;
            min-width: 0 !important;
        }

        .select2-irreg-container .select2-selection__rendered {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            overflow: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 4px !important;
            margin: 0 !important;
            list-style: none !important;
            min-width: 0 !important;
            white-space: nowrap !important;
        }

        .select2-irreg-container .select2-selection__choice {
            display: inline-flex !important;
            align-items: center !important;
            height: 24px !important;
            margin: 2px !important;
            font-size: 10px !important;
            padding: 0 18px 0 5px !important;
            max-width: 100% !important;
            min-width: 50px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            background: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
            position: relative !important;
            color: #FFFFFF !important;
        }

        /* Ensure text inside choice also truncates if Select2 uses spans */
        .select2-irreg-container .select2-selection__choice span {
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }

        .select2-irreg-container .select2-selection__choice__remove {
            position: absolute !important;
            right: 4px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            margin: 0 !important;
        }

        .select2-irreg-container .select2-search--inline {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 1 !important;
            min-width: 30px !important;
        }

        .select2-irreg-container .select2-search__field {
            height: 22px !important;
            margin: 0 4px !important;
            width: 100% !important;
            max-width: 50px !important;
            min-width: 20px !important;
        }
        }

        /* Vessel Select Styling */
        .vessel-result {
            padding: 4px 6px;
        }

        .vessel-result__name {
            font-size: 13px;
            font-weight: 600;
            color: #000000;
            line-height: 1.2;
        }

        .vessel-result__customer {
            font-size: 10px;
            color: #000000;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Adjust Select2 highlight colors */
        .select2-container--default .select2-results__option--highlighted .vessel-result__name,
        .select2-container--default .select2-results__option--highlighted .vessel-result__customer {
            color: #FFFFFF !important;
        }

        /* Modern Datepicker Theme */
        .bootstrap-datetimepicker-widget.dropdown-menu {
            border: none !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            padding: 15px !important;
            background: #fff !important;
            width: 250px !important;
            margin-top: 5px !important;
            z-index: 9999 !important;
            /* Increased z-index */
        }

        .bootstrap-datetimepicker-widget table th {
            color: #9ca3af !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            padding-bottom: 10px !important;
            height: auto !important;
            line-height: inherit !important;
            width: auto !important;
        }

        .bootstrap-datetimepicker-widget table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: separate !important;
            border-spacing: 2px !important;
        }

        .bootstrap-datetimepicker-widget table td.day {
            height: 32px !important;
            width: 32px !important;
            line-height: 32px !important;
            border-radius: 50% !important;
            font-size: 11px !important;
            color: #4b5563 !important;
            transition: all 0.2s ease !important;
            text-align: center !important;
            vertical-align: middle !important;
            padding: 0 !important;
            text-indent: 0 !important;
        }

        .bootstrap-datetimepicker-widget table td.day:hover {
            background: #f3f4f6 !important;
            color: #008080 !important;
        }

        .bootstrap-datetimepicker-widget table td.active,
        .bootstrap-datetimepicker-widget table td.active:hover {
            background-color: #008080 !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(0, 128, 128, 0.2) !important;
            text-shadow: none !important;
        }

        .bootstrap-datetimepicker-widget table td.today:before {
            border-bottom-color: #008080 !important;
        }

        .bootstrap-datetimepicker-widget .picker-switch {
            color: #111827 !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            padding-bottom: 10px !important;
        }

        .bootstrap-datetimepicker-widget table th.prev:hover,
        .bootstrap-datetimepicker-widget table th.next:hover {
            background: #f3f4f6 !important;
            border-radius: 50% !important;
        }

        .bootstrap-datetimepicker-widget .icofont {
            color: #4b5563 !important;
            font-size: 14px !important;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.top:before,
        .bootstrap-datetimepicker-widget.dropdown-menu.top:after,
        .bootstrap-datetimepicker-widget.dropdown-menu.bottom:before,
        .bootstrap-datetimepicker-widget.dropdown-menu.bottom:after {
            display: none !important;
        }

        /* Hub Select2 Custom Styles */
        .select2-result-hub {
            padding: 5px 0;
        }

        .select2-result-hub__title {
            font-weight: 600;
            font-size: 12px;
            color: #333;
            line-height: 1.3;
        }

        .select2-result-hub__location {
            font-size: 10px;
            color: #777;
            margin-top: 1px;
        }

        .select2-container--default .select2-results__option--highlighted .select2-result-hub__title,
        .select2-container--default .select2-results__option--highlighted .select2-result-hub__location {
            color: #fff !important;
        }

        /* Supplier Select2 Custom Styles */
        .select2-result-supplier {
            padding: 5px 0;
        }

        .select2-result-supplier__title {
            font-weight: 600;
            font-size: 12px;
            color: #333;
            line-height: 1.3;
        }

        .select2-result-supplier__location {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .select2-container--default .select2-results__option--highlighted .select2-result-supplier__title,
        .select2-container--default .select2-results__option--highlighted .select2-result-supplier__location {
            color: #fff !important;
        }

        .select2-selection__clear,
        .select2-selection__choice__remove {
            display: none !important;
        }

        .supplier-add-link {
            display: block;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
        }

        .supplier-add-link:hover {
            text-decoration: underline;
        }

        .add-supplier-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }

        .add-supplier-section-title {
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 6px;
            border-bottom: 2px solid #1b5e6f;
            margin-bottom: 10px;
        }

        #add-supplier-modal .crr-input {
            width: 100%;
        }

        #add-supplier-modal .select2-container {
            width: 100% !important;
        }

        #add-supplier-modal .flag-icon {
            width: 18px;
            height: 12px;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid #eee;
        }

        @media (max-width: 992px) {
            .add-supplier-grid {
                grid-template-columns: 1fr;
            }
        }

        /* DGR Sub-row styles */
        .dgr-sub-row td,
        .irregularity-sub-row td {
            background-color: #fff !important;
            padding: 0 15px 15px 45px !important;
            border-top: none !important;
        }

        .dgr-container {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            padding: 12px 20px;
            background: #fdfdfd;
            border: 1px solid #edf2f7;
            border-radius: 4px;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
        }

        .dgr-warning-icon {
            color: #ef4444;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .dgr-field {
            flex: 1;
        }

        .dgr-field.small {
            flex: 0 0 120px;
        }
    </style>
@endsection

@section('content')
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    @include('layouts.partials.pcoded-shell-start')
                                    <div class="card">
                                    <div class="crr-form-container p-4 mt-4" style="padding-bottom: 80px;">

                                        <form action="{{ route('stocks.crr.store') }}" method="POST" id="crrForm">
                                            @csrf
                                            @if ($errors->any() || session('error'))
                                                <div id="crr-form-errors" style="margin-bottom: 16px; padding: 10px 14px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 13px;">
                                                    @if (session('error'))
                                                        <div>{{ session('error') }}</div>
                                                    @endif
                                                    @foreach ($errors->all() as $error)
                                                        <div>{{ $error }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="crr-form-row">
                                                <!-- Column 1 -->
                                                <div class="crr-col">
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Vessel</label>
                                                        <select class="form-control select2-vessel" name="vessel_name">
                                                            <option value=""></option>
                                                            @foreach($vessels as $vessel)
                                                                <option value="{{ $vessel->vessel }}"
                                                                    data-customer="{{ optional($vessel->customer)->customer_name }}"
                                                                    {{ old('vessel_name') === $vessel->vessel ? 'selected' : '' }}>
                                                                    {{ $vessel->vessel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="crr-field-group" id="vessels-customer-name-group" style="display: none;">
                                                        <label class="crr-label">Vessel customer name</label>
                                                        <input type="text" id="vessels_customer_name"
                                                            name="vessels_customer_name" readonly class="crr-input">
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">PO numbers (Separate by commas or
                                                            spaces)</label>
                                                        <input type="text" class="crr-input" name="po_numbers">
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">PO remarks</label>
                                                        <select class="form-control select2 select2-po-remarks" name="po_remarks">
                                                            <option value=""></option>
                                                            <option value="Awaiting supplier confirmation">Awaiting supplier confirmation</option>
                                                            <option value="Backordered">Backordered</option>
                                                            <option value="Cancelled by supplier">Cancelled by supplier</option>
                                                            <option value="Consolidated shipment">Consolidated shipment</option>
                                                            <option value="Delivery delayed">Delivery delayed</option>
                                                            <option value="Delivery on hold">Delivery on hold</option>
                                                            <option value="Incomplete delivery">Incomplete delivery</option>
                                                            <option value="Incorrect item received">Incorrect item received</option>
                                                            <option value="Partial delivery">Partial delivery</option>
                                                            <option value="Priority shipment">Priority shipment</option>
                                                            <option value="Short shipment">Short shipment</option>
                                                            <option value="Split delivery">Split delivery</option>
                                                            <option value="Urgent delivery required">Urgent delivery required</option>
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Content</label>
                                                        <input type="text" class="crr-input" name="content"
                                                            value="Ship spares">
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">First mile updates</label>
                                                        <select class="form-control select2" name="first_mile_updates">
                                                            <option></option>
                                                            <option value="Emailed to supplier">Emailed to supplier</option>
                                                            <option
                                                                value="Emailed to supplier for missing commercial invoice">
                                                                Emailed to supplier for missing commercial invoice</option>
                                                            <option value="Reminder 1 for missing commercial invoice">
                                                                Reminder 1
                                                                for missing commercial invoice</option>
                                                            <option value="Reminder 2 for missing commercial invoice">
                                                                Reminder 2
                                                                for missing commercial invoice</option>
                                                            <option value="Reminder 1 sent to supplier">Reminder 1 sent to
                                                                supplier</option>
                                                            <option value="Reminder 2 sent to supplier">Reminder 2 sent to
                                                                supplier</option>
                                                            <option value="Reminder 3 sent to supplier; escalate">Reminder 3
                                                                sent to supplier; escalate</option>
                                                            <option value="Marked as pick-up">Marked as pick-up</option>
                                                            <option value="No supplier email address">No supplier email
                                                                address
                                                            </option>
                                                            <option value="No reply from supplier">No reply from supplier
                                                            </option>
                                                            <option value="Not delivered on time">Not delivered on time
                                                            </option>
                                                            <option value="Unknown supplier">Unknown supplier</option>
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">First mile comment</label>
                                                        <input type="text" class="crr-input" name="first_mile_comment">
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="crr-col">
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Supplier</label>
                                                        <div id="supplier-select-wrapper">
                                                            <select class="form-control select2-supplier" name="supplier"
                                                                id="supplier-select">
                                                                <option></option>
                                                                @foreach($suppliers as $s)
                                                                    <option value="{{ $s->supplier_name }}"
                                                                        data-known="1"
                                                                        data-address="{{ $s->supplier_address }}"
                                                                        data-city="{{ $s->city }}"
                                                                        data-country="{{ optional($s->country)->name }}">
                                                                        {{ $s->supplier_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div id="supplier-input-wrapper" style="display: none;">
                                                            <input type="text" class="crr-input" name="supplier"
                                                                value="EX VESSEL" readonly id="supplier-input" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="crr-checkbox-group">
                                                        <input type="checkbox" id="landed-goods" name="is_landed_goods">
                                                        <label for="landed-goods" class="mb-0">Landed goods</label>
                                                    </div>
                                                    <div id="landed-vessel-wrapper" style="display: none;" class="mt-2">
                                                        <div class="crr-field-group">
                                                            <label class="crr-label">Landed from vessel</label>
                                                            <select class="form-control select2-vessel"
                                                                name="landed_from_vessel" id="landed-from-vessel">
                                                                <option value=""></option>
                                                                @foreach($vessels as $vessel)
                                                                    <option value="{{ $vessel->vessel }}"
                                                                        data-customer="{{ optional($vessel->customer)->customer_name }}">
                                                                        {{ $vessel->vessel }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Expected delivery date</label>
                                                                <input type="text" class="crr-input datepicker"
                                                                    name="expected_delivery_date" placeholder="YYYY-MM-DD">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label" id="actual-delivery-date-label">Actual delivery date <span id="actual-delivery-required-mark" style="color:#dc3545; display:none;">*</span></label>
                                                                <input type="text" class="crr-input datepicker" id="actual_delivery_date"
                                                                    name="actual_delivery_date" placeholder="YYYY-MM-DD">
                                                                <div id="actual-delivery-validation-error" style="display:none; margin-top: 6px; padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12px; position: relative; padding-right: 32px;">
                                                                    <span id="actual-delivery-validation-error-text"></span>
                                                                    <button type="button" id="actual-delivery-validation-error-close" title="Close" aria-label="Close" style="position: absolute; top: 6px; right: 8px; border: none; background: transparent; color: #b91c1c; font-size: 16px; line-height: 1; cursor: pointer; padding: 2px 4px;">&times;</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Supplier reference</label>
                                                        <input type="text" class="crr-input" name="supplier_reference">
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Deadline warehouse</label>
                                                        <input type="text" class="crr-input datepicker"
                                                            name="deadline_warehouse">
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Internal shipment</label>
                                                        <select class="form-control select2" name="internal_shipment">
                                                            <option></option>
                                                            <option value="ETL">ETL</option>
                                                            <option value="KTL">KTL</option>
                                                            <option value="RTL">RTL</option>
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Delivery irregularities</label>
                                                        <select class="form-control select2-irregularities"
                                                            name="delivery_irregularities[]" >
                                                            <option value="No">No</option>
                                                            <option value="Yes">Yes</option>
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Incoterm</label>
                                                        <select class="form-control select2-incoterm"
                                                            name="incoterm" >
                                                            <option value=""></option>
                                                            <option value="CFR - Cost and Freight">CFR - Cost and Freight</option>
                                                            <option value="CIF - Cost, Insurance and Freight">CIF - Cost, Insurance and Freight</option>
                                                            <option value="CIP - Carriage and Insurance Paid To">CIP - Carriage and Insurance Paid To</option>
                                                            <option value="CPT - Carriage Paid To">CPT - Carriage Paid To</option>
                                                            <option value="DAP - Delivered at Place">DAP - Delivered at Place</option>
                                                            <option value="DDP - Delivered Duty Paid">DDP - Delivered Duty Paid</option>
                                                            <option value="DDU - Delivered Duty Unpaid">DDU - Delivered Duty Unpaid</option>
                                                            <option value="DPU - Delivered at Place Unloaded">DPU - Delivered at Place Unloaded</option>
                                                            <option value="EXW - Ex Works">EXW - Ex Works</option>
                                                            <option value="FAS - Free Alongside Ship">FAS - Free Alongside Ship</option>
                                                            <option value="FCA - Free Carrier">FCA - Free Carrier</option>
                                                            <option value="FOB - Free On Board">FOB - Free On Board</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="crr-col">
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Hub/agent</label>
                                                        <select class="form-control select2-hub" name="hub_agent" required>
                                                            <option></option>
                                                            <optgroup label="Hubs">
                                                                @foreach($hubs as $hub)
                                                                    <option value="{{ $hub->code }}"
                                                                        data-city="{{ $hub->city }}"
                                                                        data-country="{{ $hub->country }}"
                                                                        {{ old('hub_agent') === $hub->code ? 'selected' : '' }}>
                                                                        {{ $hub->code }} - {{ $hub->hub_name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="Agents">
                                                                @foreach($agents as $agent)
                                                                    <option value="{{ $agent->code }}"
                                                                        data-city="{{ $agent->city }}"
                                                                        data-country="{{ optional($agent->country)->name }}"
                                                                        {{ old('hub_agent') === $agent->code ? 'selected' : '' }}>
                                                                        {{ $agent->code }} - {{ $agent->agent_name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Physical Location</label>
                                                        <input type="text" class="form-control" name="location"
                                                            value="{{ old('location') }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Transit type</label>
                                                                <select class="form-control select2" name="transit_type">
                                                                    <option value=""></option>
                                                                    <option value="AMAZON">AMAZON</option>
                                                                    <option value="AWB">AWB</option>
                                                                    <option value="B/L">B/L</option>
                                                                    <option value="CADO">CADO</option>
                                                                    <option value="CMR">CMR</option>
                                                                    <option value="DHL">DHL</option>
                                                                    <option value="DHL E+">DHL E+</option>
                                                                    <option value="DPD">DPD</option>
                                                                    <option value="DSC">DSC</option>
                                                                    <option value="DSV">DSV</option>
                                                                    <option value="FEDEX">FEDEX</option>
                                                                    <option value="GLS">GLS</option>
                                                                    <option value="MSX">MSX</option>
                                                                    <option value="MT ref">MT ref</option>
                                                                    <option value="Other">Other</option>
                                                                    <option value="SF">SF</option>
                                                                    <option value="TNT">TNT</option>
                                                                    <option value="UPS">UPS</option>
                                                                    <option value="USPS">USPS</option>
                                                                    <option value="VIVAR">VIVAR</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Transit ID</label>
                                                                <input type="text" class="crr-input" name="transit_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="crr-checkbox-group">
                                                        <input type="checkbox" id="bonded-goods" name="is_bonded_goods">
                                                        <label for="bonded-goods" class="mb-0">Bonded goods</label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Customs doc type</label>
                                                                <select class="form-control select2"
                                                                    name="customs_doc_type">
                                                                    <option></option>
                                                                    <option value="T1">T1</option>
                                                                    <option value="EX-A">EX-A</option>
                                                                    <option value="ZGST">ZGST</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Bonded date</label>
                                                                <input type="text" class="crr-input datepicker"
                                                                    name="bonded_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Customs doc reference</label>
                                                                <input type="text" class="crr-input"
                                                                    name="customs_doc_reference">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Customs lot number</label>
                                                                <input type="text" class="crr-input"
                                                                    name="customs_lot_number">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Country of origin</label>
                                                                <x-forms.country-select
                                                                    name="country_of_origin"
                                                                    :countries="$countries"
                                                                    valueKey="name"
                                                                    wrapperClass=""
                                                                    class="form-control"
                                                                    placeholder="Select country"
                                                                    :allowClear="true"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">HS code</label>
                                                                <input type="text" class="crr-input" name="hs_code">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Currency <span class="text-danger">*</span></label>
                                                                <select class="form-control select2" name="currency"
                                                                    id="currency_select" required>
                                                                    <option></option>
                                                                    @foreach($countries->whereNotNull('currency')->unique('currency')->sortBy('currency') as $country)
                                                                        <option value="{{ $country->currency }}"
                                                                            data-rate="{{ $country->currency_value }}">
                                                                            {{ $country->currency }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Customs value <span class="text-danger">*</span></label>
                                                                <input type="text" step="0.01" class="crr-input"
                                                                    name="customs_value" id="customs_value" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Priority</label>
                                                                <select class="form-control select2" name="priority">
                                                                    <option></option>
                                                                    <option value="Low">Low</option>
                                                                    <option value="Normal">Normal</option>
                                                                    <option value="Urgent">Urgent</option>
                                                                    <option value="Prevent offhire">Prevent offhire</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="crr-field-group">
                                                                <label class="crr-label">Customs value USD</label>
                                                                <div class="crr-value-display">0.00</div>
                                                                <input type="hidden" name="customs_value_usd"
                                                                    id="customs_value_usd_hidden" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 4 -->
                                                <div class="crr-col">
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Status</label>
                                                        <select class="form-control select2" name="status" id="crr_status">
                                                            @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                                                <option value="{{ $value }}" {{ $value == \App\Models\Crr::STATUS_PENDING ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="crr-field-group">
                                                        <label class="crr-label">Internal comments</label>
                                                        <textarea class="form-control" name="internal_comments" rows="8"
                                                            style="font-size: 12px; border-radius: 3px;"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Packages Table -->
                                            <div class="crr-table-header"><span>Packages &nbsp; &nbsp; <span
                                                        id="package-summary-text"
                                                        style="font-weight: normal; color: #000000; font-weight: 600;">(Total
                                                        : 0.00 kg, 0
                                                        Packages, 0.0000 CBM)</span></span></div>
                                            <div id="packages-validation-error" style="display:none; margin-bottom: 10px; padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12px; position: relative; padding-right: 32px;">
                                                <span id="packages-validation-error-text"></span>
                                                <button type="button" id="packages-validation-error-close" title="Close" aria-label="Close" style="position: absolute; top: 6px; right: 8px; border: none; background: transparent; color: #b91c1c; font-size: 16px; line-height: 1; cursor: pointer; padding: 2px 4px;">&times;</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="crr-data-table" id="packagesTable">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th style="width: 80px;">Length</th>
                                                            <th style="width: 80px;">Width</th>
                                                            <th style="width: 80px;">Height</th>
                                                            <th style="width: 80px;">Weight</th>
                                                            <th style="width: 80px;">CBM</th>
                                                            <th>Warehouse loc.</th>
                                                            <th class="text-center">Irreg.</th>
                                                             <th>DGR</th>
                                                            <th>Stack.</th>
                                                            <th>Med.</th>
                                                            <th>X-ray</th>
                                                            <th>Copy</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="empty-row">
                                                            <td colspan="13" class="text-center py-4 text-muted">No items
                                                                added yet. Click "Add item" to start.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-outline-teal btn-add-package mt-2"
                                                style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">Add
                                                item</button>
                                            <div class="clearfix"></div>

                                            <!-- Costs Table -->
                                            <div class="crr-table-header">Costs</div>
                                            <div class="table-responsive">
                                                <table class="crr-data-table" id="costsTable">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Carrier</th>
                                                            <th>Net value</th>
                                                            <th>Currency</th>
                                                            <th>Net USD</th>
                                                            <th>Invoice no</th>
                                                            <th>Remarks</th>
                                                            <th>Hub/Agent</th>
                                                            <th>Tag</th>
                                                            <th>Delete</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="empty-row">
                                                            <td colspan="11" class="text-center py-4 text-muted">No costs
                                                                added yet. Click "Add cost" to start.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-outline-teal btn-add-cost mt-2 mb-5"
                                                style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">Add
                                                cost</button>

                                            <!-- Footer Actions (Fixed) -->
                                            <div class="form-footer-custom">
                                                <button type="submit" class="btn-save-custom">Save CRR</button>
                                                <a href="{{ route('stocks') }}" class="btn-cancel-custom">Cancel</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
    @include('layouts.partials.pcoded-shell-end')

    {{-- Quick-add supplier modal --}}
    <div class="modal fade" id="add-supplier-modal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 1100px;">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="addSupplierModalLabel" style="font-size: 14px; font-weight: 600;">Add supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add-supplier-form">
                    <div class="modal-body" style="padding: 16px 20px; max-height: 70vh; overflow-y: auto;">
                        <div class="add-supplier-grid">
                            {{-- Column 1: Supplier information --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier information</div>

                                <div class="crr-field-group">
                                    <label class="crr-label">Supplier name <span class="text-danger">*</span></label>
                                    <input type="text" class="crr-input" name="supplier_name" id="modal-supplier-name" required>
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Phone number (with country code)</label>
                                    <input type="text" class="crr-input" name="phone_number" id="modal-supplier-phone">
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Email</label>
                                    <input type="text" class="crr-input" name="email" id="modal-supplier-email"
                                        placeholder="email@example.com; email2@example.com">
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Remarks</label>
                                    <textarea class="crr-input" name="remarks" id="modal-supplier-remarks" rows="3" style="height: auto; min-height: 70px;"></textarea>
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Special considerations for destination</label>
                                    <textarea class="crr-input" name="special_considerations" id="modal-supplier-special" rows="3" style="height: auto; min-height: 70px;"></textarea>
                                </div>
                            </div>

                            {{-- Column 2: Address --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier address</div>

                                <div class="crr-field-group">
                                    <label class="crr-label">Supplier address</label>
                                    <textarea class="crr-input" name="supplier_address" id="modal-supplier-address" rows="2" style="height: auto; min-height: 50px;"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">City</label>
                                            <input type="text" class="crr-input" name="city" id="modal-supplier-city">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">District/state</label>
                                            <input type="text" class="crr-input" name="district_state" id="modal-supplier-district">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">Zip code</label>
                                            <input type="text" class="crr-input" name="zip_code" id="modal-supplier-zip">
                                        </div>
                                    </div>
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Country</label>
                                    <x-forms.country-select
                                        name="country_id"
                                        id="modal-supplier-country"
                                        :countries="$countries"
                                        wrapperClass=""
                                        class="crr-input modal-supplier-country"
                                        placeholder="Select an option"
                                        :allowClear="true"
                                        dropdownParent="#add-supplier-modal"
                                    />
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Port code</label>
                                    <input type="text" class="crr-input" name="port_code" id="modal-supplier-port">
                                </div>

                                <div class="add-supplier-section-title" style="margin-top: 12px;">Office address (optional)</div>

                                <div class="crr-field-group">
                                    <label class="crr-label">Office address</label>
                                    <textarea class="crr-input" name="office_address" id="modal-office-address" rows="2" style="height: auto; min-height: 50px;"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">City</label>
                                            <input type="text" class="crr-input" name="office_city" id="modal-office-city">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">District/state</label>
                                            <input type="text" class="crr-input" name="office_district_state" id="modal-office-district">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="crr-field-group">
                                            <label class="crr-label">Zip code</label>
                                            <input type="text" class="crr-input" name="office_zip_code" id="modal-office-zip">
                                        </div>
                                    </div>
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Country</label>
                                    <x-forms.country-select
                                        name="office_country_id"
                                        id="modal-office-country"
                                        :countries="$countries"
                                        wrapperClass=""
                                        class="crr-input modal-supplier-country"
                                        placeholder="Select an option"
                                        :allowClear="true"
                                        dropdownParent="#add-supplier-modal"
                                    />
                                </div>
                            </div>

                            {{-- Column 3: Supplier details --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier details</div>

                                <div class="crr-field-group">
                                    <label class="crr-label">VAT number</label>
                                    <input type="text" class="crr-input" name="vat_number" id="modal-supplier-vat">
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">EORI number</label>
                                    <input type="text" class="crr-input" name="eori_number" id="modal-supplier-eori">
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">Currency</label>
                                    <select class="crr-input modal-supplier-currency" name="currency" id="modal-supplier-currency">
                                        <option value="">Select an option</option>
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr }}">{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="crr-field-group">
                                    <label class="crr-label">UN/LOCODE</label>
                                    <input type="text" class="crr-input" name="un_locode" id="modal-supplier-unlocode">
                                </div>
                            </div>
                        </div>
                        <div id="add-supplier-error" class="text-danger mt-2" style="font-size: 11px; display: none;"></div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-teal" id="add-supplier-save-btn" style="background:#008080;border-color:#008080;color:#fff;">Save supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script
        src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{--
    <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- Bootstrap Datepicker js -->
    <script type="text/javascript"
        src="{{ asset('files/assets/pages/advance-elements/moment-with-locales.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/assets/pages/advance-elements/bootstrap-datetimepicker.min.js') }}"></script>

    <script>
        function updatePackageSummary() {
            let totalWeight = 0;
            let totalCbm = 0;
            let count = 0;

            $('#packagesTable tbody tr:not(.empty-row):not(.dgr-sub-row):not(.irregularity-sub-row)').each(function () {
                let weight = parseFloat($(this).find('.pkg-weight').val()) || 0;
                let cbm = parseFloat($(this).find('.pkg-cbm').val()) || 0;
                totalWeight += weight;
                totalCbm += cbm;
                count++;
            });

            $('#package-summary-text').text(`(Total : ${totalWeight.toFixed(2)} kg, ${count} Packages, ${totalCbm.toFixed(4)} CBM)`);
        }
        $(document).ready(function () {
            // Refresh exchange rates in the background when Create CRR opens.
            $.ajax({
                url: @json(route('currency.update')),
                type: 'GET',
                dataType: 'json'
            });

            // Copy Row Logic
            $(document).on('click', '.btn-copy-row', function () {
                let currentTr = $(this).closest('tr');
                let table = currentTr.closest('table');
                let tableId = table.attr('id');
                let newRow = currentTr.clone();

                let targetIndex = packageIndex;

                if (tableId === 'packagesTable') {
                    // Update names for package row
                    newRow.attr('data-index', targetIndex);
                    newRow.find('td:first').text(targetIndex + 1);

                    // Copy selected remarks values before destroying Select2
                    let sourceRemarksVal = currentTr.find('.select2-pkg-remarks').val();

                    // Strip Select2 from cloned remarks select so we can re-init
                    newRow.find('.select2-container').remove();
                    newRow.find('.select2-pkg-remarks').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

                    newRow.find('input, select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']');
                            $(this).attr('name', newName);

                            // Clear ID if copying an existing record
                            if (newName.includes('[id]')) {
                                $(this).val('');
                            }
                        }
                    });

                    // Re-init Select2 for cloned remarks and restore selected values
                    newRow.find('.select2-pkg-remarks').select2({
                        placeholder: "Select or type remark",
                        tags: true,
                        tokenSeparators: [','],
                        allowClear: false,
                        width: '100%'
                    }).val(sourceRemarksVal).trigger('change');
                    newRow.find('.select2-pkg-remarks').next('.select2-container').addClass('select2-remarks-container');

                    packageIndex++;
                } else if (tableId === 'costsTable') {
                    // Update names for cost row
                    newRow.attr('data-index', costIndex);
                    newRow.find('td:first').text(costIndex + 1);

                    // Copy selected remarks values before destroying Select2
                    let sourceCostRemarksVal = currentTr.find('.select2-cost-remarks').val();

                    // Remove Select2 classes and containers to re-init
                    newRow.find('.select2-container').remove();
                    newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

                    newRow.find('input, select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/costs\[\d+\]/, 'costs[' + costIndex + ']');
                            $(this).attr('name', newName);

                            // Clear ID if copying an existing record
                            if (newName.includes('[id]')) {
                                $(this).val('');
                            }
                        }
                    });

                    // Re-init Select2 in cloned row
                    newRow.find('.select2-cost-hub').select2({
                        placeholder: "Select hub",
                        allowClear: false,
                        width: '100%',
                        templateResult: formatHub,
                        templateSelection: function (hub) {
                            return hub.text;
                        }
                    });

                    newRow.find('.select2-cost-currency').select2({
                        placeholder: "Select currency",
                        allowClear: false,
                        width: '100%'
                    });

                    // Re-init and restore remarks
                    newRow.find('.select2-cost-remarks').select2({
                        placeholder: "Select or type remark",
                        tags: true,
                        tokenSeparators: [','],
                        allowClear: false,
                        width: '100%'
                    }).val(sourceCostRemarksVal).trigger('change');
                    newRow.find('.select2-cost-remarks').next('.select2-container').addClass('select2-remarks-container');

                    costIndex++;
                }

                table.find('tbody').append(newRow);
                updatePackageSummary();

                // Trigger CBM calculation and clone sub-rows if it was a package row
                if (tableId === 'packagesTable') {
                    newRow.find('.pkg-dim').first().trigger('input');
                    
                    let currentIdx = currentTr.attr('data-index');
                    
                    // Clone irregularity sub-row
                    let irregRow = table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`).clone();
                    irregRow.find('.select2-container').remove();
                    irregRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                    irregRow.attr('data-index', targetIndex);
                    irregRow.find('select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']'));
                        }
                    });
                    
                    // Set same selected values for the cloned irregularities select
                    let sourceSelectVal = table.find(`.irregularity-sub-row[data-index="${currentIdx}"] select`).val();
                    irregRow.find('select').val(sourceSelectVal);

                    newRow.after(irregRow);

                    // Re-init select2 on cloned irregularities
                    irregRow.find('.select2-irregularities').select2({
                        placeholder: "Select irregularities",
                        allowClear: false,
                        width: '100%'
                    }).next('.select2-container').addClass('select2-irreg-container');

                    // Clone DGR sub-row
                    let dgrRow = table.find(`.dgr-sub-row[data-index="${currentIdx}"]`).clone();
                    dgrRow.attr('data-index', targetIndex);
                    dgrRow.find('input').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']'));
                        }
                    });
                    irregRow.after(dgrRow);

                    // Sync checkboxes and hide/show accordingly
                    newRow.find('.pkg-is-irregular').prop('checked', currentTr.find('.pkg-is-irregular').is(':checked'));
                    if (!currentTr.find('.pkg-is-irregular').is(':checked')) {
                        irregRow.hide();
                    } else {
                        irregRow.show();
                    }

                    newRow.find('.pkg-is-dgr').prop('checked', currentTr.find('.pkg-is-dgr').is(':checked'));
                    if (!currentTr.find('.pkg-is-dgr').is(':checked')) {
                        dgrRow.hide();
                    } else {
                        dgrRow.show();
                    }
                }
            });

            // Initialize generic Select2 (excludes PO numbers which needs tags)
            $('.select2:not([name="po_numbers[]"])').select2({
                placeholder: "Click here",
                allowClear: false,
                width: '100%'
            });

            $('.select2-incoterm').select2({
                placeholder: 'Select incoterm',
                allowClear: true,
                width: '100%'
            });

            // Initialize Delivery Irregularities select2 (multi-select with fixed height)
            $('.select2-irregularities').each(function () {
                $(this).select2({
                    placeholder: "Select irregularities",
                    allowClear: false,
                    width: '100%'
                });
                $(this).next('.select2-container').addClass('select2-irreg-container');
            });

            // PO numbers: allow free-typing with Enter key
            $('select[name="po_numbers[]"]').select2({
                placeholder: "Type PO number and press Enter",
                tags: true,
                tokenSeparators: [',', '\n'],
                allowClear: false,
                width: '100%'
            });

            // Initialize Select2 with tags for Vessel to allow manual entry
            $('.select2-vessel').select2({
                placeholder: "Select or type vessel",
                tags: true,
                allowClear: false,
                width: '100%',
                templateResult: formatVessel,
                templateSelection: function (state) {
                    return state.text;
                }
            });

            var $mainVesselSelect = $('select[name="vessel_name"]');
            var $vesselCustomerGroup = $('#vessels-customer-name-group');
            var $vesselCustomerName = $('#vessels_customer_name');

            function updateVesselCustomerName() {
                var customerName = $mainVesselSelect.find('option:selected').data('customer') || '';

                $vesselCustomerName.val(customerName);
                $vesselCustomerGroup.toggle(Boolean(customerName));
            }

            $mainVesselSelect.on('change', updateVesselCustomerName);
            updateVesselCustomerName();

            // Template for Vessel rows
            function formatVessel(state) {
                if (!state.id) return state.text;
                var $element = $(state.element);
                var customer = $element.data('customer');

                if (!customer) return state.text;

                var $result = $(
                    '<div class="vessel-result">' +
                    '<div class="vessel-result__name">' + state.text + '</div>' +
                    '<div class="vessel-result__customer">' + customer + '</div>' +
                    '</div>'
                );

                return $result;
            }

            // Hub Select2 with 2-line display
            function formatHub(hub) {
                if (!hub.id) {
                    return hub.text;
                }
                var city = $(hub.element).data('city');
                var country = $(hub.element).data('country');
                var $hub = $(
                    '<div class="select2-result-hub">' +
                    '<div class="select2-result-hub__title">' + hub.text + '</div>' +
                    '<div class="select2-result-hub__location">' + (city ? city + ', ' : '') + (country || '') + '</div>' +
                    '</div>'
                );
                return $hub;
            }
            $('.select2-hub').select2({
                placeholder: "Select hub/agent",
                allowClear: false,
                width: '100%',
                templateResult: formatHub,
                templateSelection: function (hub) {
                    return hub.text;
                }
            });

            // Supplier Select2 (with quick-add) is initialized in its own script below.

            // Initialize Bootstrap Datepicker
            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                icons: {
                    time: "icofont icofont-clock-time",
                    date: "icofont icofont-ui-calendar",
                    up: "icofont icofont-rounded-up",
                    down: "icofont icofont-rounded-down",
                    next: "icofont icofont-rounded-right",
                    previous: "icofont icofont-rounded-left"
                }
            });

            // Handle date inputs styling if needed (browser default used for now)

            // --- Dynamic Table Logic ---
            let packageIndex = 0;
            let costIndex = 0;

            // Add Package Row
            $('.btn-add-package').on('click', function () {
                $('#packagesTable tbody .empty-row').remove();

                let row = `<tr data-index="${packageIndex}"> 
                                                                                                                                                                                            <td>${packageIndex + 1}</td> 
                                                                                                                                                                                            <td><input type="text" step="0.01" class="crr-input pkg-dim pkg-l" name="packages[${packageIndex}][length]"></td> 
                                                                                                                                                                                            <td><input type="text" step="0.01" class="crr-input pkg-dim pkg-w" name="packages[${packageIndex}][width]"></td> 
                                                                                                                                                                                            <td><input type="text" step="0.01" class="crr-input pkg-dim pkg-h" name="packages[${packageIndex}][height]"></td> 
                                                                                                                                                                                            <td><input type="text" step="0.01" class="crr-input pkg-weight" name="packages[${packageIndex}][weight]"></td> 
                                                                                                                                                                                            <td><input type="text" class="crr-input pkg-cbm" name="packages[${packageIndex}][cbm]" readonly value="0"></td> 
                                                                                                                                                                                            <td><input type="text" class="crr-input" name="packages[${packageIndex}][warehouse_location]"></td> 
                                                                                                                                                                                            <td class="text-center"><input type="checkbox" class="pkg-is-irregular" name="packages[${packageIndex}][is_delivery_irregularity]"></td>
                                                                                                                                                                                            <td class="text-left"><input type="checkbox" class="pkg-is-dgr" name="packages[${packageIndex}][is_dgr]"></td> 
                                                                                                                                                                                            <td class="text-left"><input type="checkbox" name="packages[${packageIndex}][is_not_stackable]"></td> 
                                                                                                                                                                                            <td class="text-left"><input type="checkbox" name="packages[${packageIndex}][is_medicine]"></td> 
                                                                                                                                                                                            <td class="text-left"><input type="checkbox" name="packages[${packageIndex}][is_xray]"></td>
                                                                                                                                                                                            <td> <button type="button" class="btn btn-link text-primary p-0 btn-copy-row"><i class="icofont icofont-copy-alt"></i></button> </td> 
                                                                                                                                                                                            <td> <button type="button" class="btn btn-link text-danger p-0 btn-remove-row"><i class="icofont icofont-trash"></i></button> </td> 
                                                                                                                                                                                         </tr>
                                                                                                                                                                                         <tr class="irregularity-sub-row" data-index="${packageIndex}" style="display: none;">
                                                                                                                                                                                             <td colspan="2"></td>
                                                                                                                                                                                             <td colspan="13">
                                                                                                                                                                                                 <div class="dgr-container" style="background: #fff9e6; border: 1px solid #ffeeba;">
                                                                                                                                                                                                     <i class="icofont icofont-warning dgr-warning-icon" style="color: #f0ad4e;"></i>
                                                                                                                                                                                                     <div class="dgr-field" style="flex: 1;">
                                                                                                                                                                                                         <label class="crr-label">Delivery irregularities</label>
                                                                                                                                                                                                         <select class="form-control select2-irregularities" name="packages[${packageIndex}][delivery_irregularities][]" multiple="multiple">
                                                                                                                                                                                                             <option value="Damaged packaging - no repacking required">Damaged packaging - no repacking required</option>
                                                                                                                                                                                                             <option value="Damaged packaging - repacking required">Damaged packaging - repacking required</option>
                                                                                                                                                                                                             <option value="Missing DG label / marking on package">Missing DG label / marking on package</option>
                                                                                                                                                                                                             <option value="Missing documentation - Commercial invoice / Packing list">Missing documentation - Commercial invoice / Packing list</option>
                                                                                                                                                                                                             <option value="Missing documentation - DG">Missing documentation - DG</option>
                                                                                                                                                                                                             <option value="Missing documentation - Other">Missing documentation - Other</option>
                                                                                                                                                                                                             <option value="Missing label on packaging">Missing label on packaging</option>
                                                                                                                                                                                                             <option value="Packaging not fit for airfreight">Packaging not fit for airfreight</option>
                                                                                                                                                                                                             <option value="Packaging not fumigated">Packaging not fumigated</option>
                                                                                                                                                                                                             <option value="Packaging not heat treated">Packaging not heat treated</option>
                                                                                                                                                                                                             <option value="Vessel Name / PO Number not mentioned on packaging (label)">Vessel Name / PO Number not mentioned on packaging (label)</option>
                                                                                                                                                                                                             <option value="Vessel Name / PO Number not mentioned on supplier documentation">Vessel Name / PO Number not mentioned on supplier documentation</option>
                                                                                                                                                                                                         </select>
                                                                                                                                                                                                     </div>
                                                                                                                                                                                                 </div>
                                                                                                                                                                                             </td>
                                                                                                                                                                                         </tr>
                                                                                                                                                                                         <tr class="dgr-sub-row" data-index="${packageIndex}" style="display: none;">
                                                                                                                                                                                             <td colspan="2"></td>
                                                                                                                                                                                             <td colspan="13">
                                                                                                                                                                                                 <div class="dgr-container">
                                                                                                                                                                                                     <i class="icofont icofont-warning dgr-warning-icon"></i>
                                                                                                                                                                                                     <div class="dgr-field">
                                                                                                                                                                                                         <label class="crr-label">Dangerous goods description</label>
                                                                                                                                                                                                         <input type="text" class="crr-input" name="packages[${packageIndex}][dgr_description]" placeholder="">
                                                                                                                                                                                                     </div>
                                                                                                                                                                                                     <div class="dgr-field small" style="max-width: 50px;">
                                                                                                                                                                                                         <label class="crr-label">UN number</label>
                                                                                                                                                                                                         <input type="text" class="crr-input" name="packages[${packageIndex}][un_number]" placeholder="">
                                                                                                                                                                                                     </div>
                                                                                                                                                                                                     <div class="dgr-field small" style="max-width: 50px;">
                                                                                                                                                                                                         <label class="crr-label">Class</label>
                                                                                                                                                                                                         <input type="text" class="crr-input" name="packages[${packageIndex}][dgr_class]" placeholder="">
                                                                                                                                                                                                     </div>
                                                                                                                                                                                                 </div>
                                                                                                                                                                                             </td>
                                                                                                                                                                                         </tr>`;

                let $row = $(row);
                $('#packagesTable tbody').append($row);

                // Initialize Select2 on the package remarks select
                $row.filter('tr:not(.irregularity-sub-row):not(.dgr-sub-row)').find('.select2-pkg-remarks').select2({
                    placeholder: "Select or type remark",
                    tags: true,
                    tokenSeparators: [','],
                    allowClear: false,
                    width: '100%'
                }).next('.select2-container').addClass('select2-remarks-container');

                // Initialize Select2 on the new irregularity select
                $row.filter('.irregularity-sub-row').find('.select2-irregularities').select2({
                    placeholder: "Select irregularities",
                    allowClear: false,
                    width: '100%'
                }).next('.select2-container').addClass('select2-irreg-container');

                packageIndex++;
                updatePackageSummary();
            });

            // Hub & Currency data for dynamic cost rows
            var costHubs = @json($hubs);
            var costCurrencies = @json($currencies->values());

            // Add Cost Row
            $('.btn-add-cost').on('click', function () {
                $('#costsTable tbody .empty-row').remove();

                // Build hub options
                let hubOptions = '<option></option>';
                costHubs.forEach(function (hub) {
                    hubOptions += '<option value="' + hub.code + '" data-city="' + (hub.city || '') + '" data-country="' + (hub.country || '') + '">' + hub.code + ' - ' + hub.hub_name + '</option>';
                });

                // Build currency options
                let currencyOptions = '<option></option>';
                costCurrencies.forEach(function (cur) {
                    currencyOptions += '<option value="' + cur + '">' + cur + '</option>';
                });

                let row = `<tr data-index="${costIndex}"> <td>${costIndex + 1}</td> <td><input type="text" class="crr-input" name="costs[${costIndex}][type]"></td> <td><input type="text" class="crr-input" name="costs[${costIndex}][carrier]"></td> <td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value]"></td> <td><select class="form-control select2-cost-currency" name="costs[${costIndex}][currency]">${currencyOptions}</select></td> <td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value_usd]"></td> <td><input type="text" class="crr-input" name="costs[${costIndex}][invoice_no]"></td> <td><select class="form-control select2-cost-remarks" name="costs[${costIndex}][remarks][]" multiple="multiple"><option value="Approved">Approved</option><option value="Pending approval">Pending approval</option><option value="Invoice mismatch">Invoice mismatch</option><option value="Disputed">Disputed</option><option value="On hold">On hold</option><option value="Verified">Verified</option></select></td> <td><select class="form-control select2-cost-hub" name="costs[${costIndex}][hub_agent]">${hubOptions}</select></td> <td><input type="text" class="crr-input" name="costs[${costIndex}][tag]"></td> <td> <button type="button" class="btn btn-link text-danger p-0 btn-remove-row"><i class="icofont icofont-trash"></i></button> </td> </tr>`;
                let $row = $(row);
                $('#costsTable tbody').append($row);

                // Initialize Select2 on the new Hub/Agent dropdown
                $row.find('.select2-cost-hub').select2({
                    placeholder: "Select hub/agent",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#crrForm'),
                    templateResult: formatHub,
                    templateSelection: function (hub) {
                        return hub.text;
                    }
                });

                // Initialize Select2 on the new Currency dropdown
                $row.find('.select2-cost-currency').select2({
                    placeholder: "Select currency",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#crrForm')
                });

                // Initialize Select2 on the cost remarks select
                $row.find('.select2-cost-remarks').select2({
                    placeholder: "Select or type remark",
                    tags: true,
                    tokenSeparators: [','],
                    allowClear: false,
                    width: '100%'
                }).next('.select2-container').addClass('select2-remarks-container');

                costIndex++;
            });

            // Remove Row
            $(document).on('click', '.btn-remove-row', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                if (currentIdx !== undefined) {
                    table.find(`.dgr-sub-row[data-index="${currentIdx}"]`).remove();
                    table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`).remove();
                }
                row.remove();
                if ($('#packagesTable tbody tr:not(.dgr-sub-row):not(.irregularity-sub-row)').length === 0) {
                    $('#packagesTable tbody').append('<tr class="empty-row"><td colspan="13" class="text-center py-4 text-muted">No items added yet. Click "Add item" to start.</td></tr>');
                }
                if ($('#costsTable tbody tr').length === 0) {
                    $('#costsTable tbody').append('<tr class="empty-row"><td colspan="11" class="text-center py-4 text-muted">No costs added yet. Click "Add cost" to start.</td></tr>');
                }
                updatePackageSummary();
            });

            // Auto-calculate CBM
            $(document).on('input', '.pkg-dim', function () {
                let row = $(this).closest('tr');
                let l = parseFloat(row.find('.pkg-l').val()) || 0;
                let w = parseFloat(row.find('.pkg-w').val()) || 0;
                let h = parseFloat(row.find('.pkg-h').val()) || 0;
                let cbm = (l * w * h) / 1000000;
                row.find('.pkg-cbm').val(cbm.toFixed(4));
                updatePackageSummary();
            });

            $(document).on('input', '.pkg-weight', function () {
                updatePackageSummary();
            });

            // Toggle DGR sub-row
            $(document).on('change', '.pkg-is-dgr', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                let dgrRow = table.find(`.dgr-sub-row[data-index="${currentIdx}"]`);
                if ($(this).is(':checked')) {
                    dgrRow.fadeIn(200);
                } else {
                    dgrRow.fadeOut(200);
                }
            });

            // Toggle Irregularity sub-row
            $(document).on('change', '.pkg-is-irregular', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                let irregRow = table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`);
                if ($(this).is(':checked')) {
                    irregRow.fadeIn(200);
                } else {
                    irregRow.fadeOut(200);
                }
            });

            // Realtime Customs Value USD calculation
            function calculateCustomsUSD() {
                let customsValue = parseFloat($('input[name="customs_value"]').val()) || 0;
                let selectedOption = $('#currency_select').find(':selected');
                let rate = parseFloat(selectedOption.data('rate')) || 0;

                let customsUSD = 0;
                if (rate > 0) {
                    // Based on API where 1 USD = X Local Currency
                    // Formula: Local Value / Rate = USD Value
                    // However, user explicitly requested: Customs value USD = Currency value's currency_value * Customs value
                    customsUSD = customsValue / rate;
                }

                $('.crr-value-display').text(customsUSD.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#customs_value_usd_hidden').val(customsUSD.toFixed(2));
            }

            $(document).on('input', 'input[name="customs_value"]', calculateCustomsUSD);
            $(document).on('change', '#currency_select', calculateCustomsUSD);

            // Landed Goods logic
            $('#landed-goods').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#landed-vessel-wrapper').fadeIn(200);

                    $('#supplier-select-wrapper').hide();
                    $('#supplier-select').prop('disabled', true);

                    $('#supplier-input-wrapper').show();
                    $('#supplier-input').prop('disabled', false).val('EX VESSEL');

                    $('#landed-from-vessel').select2({
                        placeholder: "Select vessel",
                        allowClear: true,
                        width: '100%',
                        templateResult: formatVessel,
                        templateSelection: function (state) {
                            return state.text;
                        }
                    });
                } else {
                    $('#landed-vessel-wrapper').fadeOut(200);

                    $('#supplier-input-wrapper').hide();
                    $('#supplier-input').prop('disabled', true);

                    $('#supplier-select-wrapper').show();
                    $('#supplier-select').prop('disabled', false);
                }
            });

            // Trigger on load to handle initial state
            if ($('#landed-goods').is(':checked')) {
                $('#landed-goods').trigger('change');
            }
        });
    </script>

    <script>
        // Supplier quick-add: Add "name" option opens modal (never saves free-text into the select).
        jQuery(function ($) {
            var $supplierSelect = $('#supplier-select');
            var $modal = $('#add-supplier-modal');
            if (!$supplierSelect.length || !$modal.length) {
                return;
            }

            if (!$modal.parent().is('body')) {
                $modal.appendTo('body');
            }

            // Drop any free-text options left behind by Select2 tagging.
            $supplierSelect.find('option').each(function () {
                var $opt = $(this);
                if ($.trim($opt.val() || '') !== '' && $opt.attr('data-known') !== '1') {
                    $opt.remove();
                }
            });

            var knownNames = {};
            $supplierSelect.find('option').each(function () {
                var val = $.trim($(this).val() || '');
                if (val) {
                    knownNames[val.toLowerCase()] = true;
                }
            });

            var previousValue = $supplierSelect.val();
            if (previousValue && !knownNames[String(previousValue).toLowerCase()]) {
                previousValue = null;
                $supplierSelect.val(null);
            }

            var modalOpenScheduled = false;

            if ($supplierSelect.hasClass('select2-hidden-accessible')) {
                $supplierSelect.select2('destroy');
            }

            function isNewSupplierData(data) {
                if (!data) {
                    return false;
                }
                if (data.newTag === true) {
                    return true;
                }
                var id = String(data.id || '');
                if (id.indexOf('__new__:') === 0) {
                    return true;
                }
                if (!id) {
                    return false;
                }
                return !knownNames[id.toLowerCase()];
            }

            function getNewTerm(data) {
                if (!data) {
                    return '';
                }
                if (data.term) {
                    return String(data.term);
                }
                var id = String(data.id || '');
                if (id.indexOf('__new__:') === 0) {
                    return id.slice(8);
                }
                var text = String(data.text || '');
                var match = text.match(/^Add\s+"(.+)"$/);
                return match ? match[1] : (id || text);
            }

            function clearTempOptions() {
                $supplierSelect.find('option').each(function () {
                    var $opt = $(this);
                    var val = String($opt.val() || '');
                    if (val !== '' && (val.indexOf('__new__:') === 0 || $opt.attr('data-select2-tag') || $opt.attr('data-known') !== '1')) {
                        $opt.remove();
                    }
                });
            }

            function restorePreviousSelection() {
                clearTempOptions();
                $supplierSelect.val(previousValue || null).trigger('change.select2');
            }

            function openAddSupplierModal(name) {
                if (modalOpenScheduled) {
                    return;
                }
                modalOpenScheduled = true;

                try { $supplierSelect.select2('close'); } catch (e) {}
                restorePreviousSelection();

                var form = document.getElementById('add-supplier-form');
                if (form) {
                    form.reset();
                }
                $('.modal-supplier-country, .modal-supplier-currency').val(null).trigger('change');
                $('#add-supplier-error').hide().text('');
                $('#modal-supplier-name').val($.trim(name || ''));

                // Defer past the current click so Bootstrap does not treat it as a backdrop dismiss.
                setTimeout(function () {
                    $modal.modal('show');
                    modalOpenScheduled = false;
                    setTimeout(function () {
                        $('#modal-supplier-name').trigger('focus');
                    }, 200);
                }, 50);
            }

            function initModalSupplierSelect2() {
                $('.modal-supplier-country').each(function () {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                });

                if (window.MarineCaddieInitCountrySelect) {
                    window.MarineCaddieInitCountrySelect($modal);
                }

                $('.modal-supplier-currency').each(function () {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        placeholder: 'Select an option',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $modal
                    });
                });
            }

            initModalSupplierSelect2();
            $modal.off('shown.bs.modal.supplierSelect2').on('shown.bs.modal.supplierSelect2', function () {
                initModalSupplierSelect2();
            });

            $supplierSelect.select2({
                placeholder: 'Select supplier',
                tags: true,
                allowClear: false,
                width: '100%',
                createTag: function (params) {
                    var term = $.trim(params.term || '');
                    if (term === '' || knownNames[term.toLowerCase()]) {
                        return null;
                    }
                    return {
                        id: '__new__:' + term,
                        text: 'Add "' + term + '"',
                        newTag: true,
                        term: term
                    };
                },
                insertTag: function (data, tag) {
                    data.push(tag);
                },
                templateResult: function (s) {
                    if (s.loading) {
                        return s.text;
                    }
                    if (isNewSupplierData(s)) {
                        return $('<span class="supplier-add-link"></span>').text('Add "' + getNewTerm(s) + '"');
                    }
                    if (!s.id || !s.element) {
                        return $('<span></span>').text(s.text || '');
                    }
                    var address = $(s.element).data('address') || '';
                    var city = $(s.element).data('city') || '';
                    var country = $(s.element).data('country') || '';
                    var locationText = [address, city, country].filter(Boolean).join(', ');
                    var $res = $('<div class="select2-result-supplier"></div>');
                    $res.append($('<div class="select2-result-supplier__title"></div>').text(s.text));
                    $res.append($('<div class="select2-result-supplier__location"></div>').text(locationText));
                    return $res;
                },
                templateSelection: function (s) {
                    if (isNewSupplierData(s)) {
                        return '';
                    }
                    return s.text || '';
                }
            });

            $supplierSelect
                .off('.supplierAdd')
                .on('select2:opening.supplierAdd', function () {
                    previousValue = $supplierSelect.val();
                    clearTempOptions();
                })
                .on('select2:select.supplierAdd', function (e) {
                    var data = (e.params && e.params.data) || {};
                    if (!isNewSupplierData(data)) {
                        previousValue = $supplierSelect.val();
                        return;
                    }
                    openAddSupplierModal(getNewTerm(data));
                });

            // Capture-phase click on Add "..." while the supplier dropdown is open.
            document.addEventListener('click', function (e) {
                if (!$supplierSelect.next('.select2-container').hasClass('select2-container--open')) {
                    return;
                }

                var optionEl = e.target && e.target.closest
                    ? e.target.closest('.select2-results__option')
                    : null;
                if (!optionEl) {
                    return;
                }

                var data = $(optionEl).data('data');
                var optionText = $.trim($(optionEl).text() || '');
                var isAdd = isNewSupplierData(data) ||
                    /^Add\s+".+"$/.test(optionText) ||
                    $(optionEl).find('.supplier-add-link').length > 0;
                if (!isAdd) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') {
                    e.stopImmediatePropagation();
                }

                var term = isNewSupplierData(data)
                    ? getNewTerm(data)
                    : ((optionText.match(/^Add\s+"(.+)"$/) || [])[1] || optionText);
                openAddSupplierModal(term);
            }, true);

            $('#add-supplier-form').off('submit.supplierAdd').on('submit.supplierAdd', function (e) {
                e.preventDefault();
                var $btn = $('#add-supplier-save-btn');
                var $error = $('#add-supplier-error');
                $error.hide().text('');
                $btn.prop('disabled', true).text('Saving...');

                var payload = $(this).serializeArray();
                payload.push({ name: '_token', value: @json(csrf_token()) });

                $.ajax({
                    url: @json(route('suppliers.store')),
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: payload,
                    success: function (response) {
                        var supplier = (response && response.supplier) || {};
                        var name = $.trim(supplier.supplier_name || $('#modal-supplier-name').val());

                        if (name) {
                            clearTempOptions();
                            knownNames[name.toLowerCase()] = true;

                            if ($supplierSelect.find('option').filter(function () {
                                return $(this).val() === name;
                            }).length === 0) {
                                $supplierSelect.append(
                                    $('<option></option>')
                                        .val(name)
                                        .text(name)
                                        .attr('data-known', '1')
                                        .attr('data-address', supplier.supplier_address || '')
                                        .attr('data-city', supplier.city || '')
                                        .attr('data-country', supplier.country || '')
                                );
                            }

                            previousValue = name;
                            $supplierSelect.val(name).trigger('change');
                        }

                        $modal.modal('hide');
                        if (typeof toastr !== 'undefined') {
                            toastr.success((response && response.message) || 'Supplier created successfully');
                        }
                    },
                    error: function (xhr) {
                        var message = 'Could not create supplier.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                            message = xhr.responseJSON.errors[firstKey][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        $error.text(message).show();
                    },
                    complete: function () {
                        $btn.prop('disabled', false).text('Save supplier');
                    }
                });
            });

            // Require package details + Actual delivery date when status is Stock
            var STOCK_STATUS = {{ \App\Models\Crr::STATUS_ACTIVE }};

            function toggleActualDeliveryRequired() {
                var isStock = String($('#crr_status').val()) === String(STOCK_STATUS);
                $('#actual-delivery-required-mark').toggle(isStock);
                if (!isStock) {
                    $('#actual_delivery_date').css('border-color', '');
                    $('#actual-delivery-validation-error-text').text('');
                    $('#actual-delivery-validation-error').hide();
                }
            }

            $('#crr_status').on('change', toggleActualDeliveryRequired);
            toggleActualDeliveryRequired();

            $('#crrForm').on('submit', function (e) {
                var $errorBox = $('#packages-validation-error');
                var $errorText = $('#packages-validation-error-text');
                var $rows = $('#packagesTable tbody tr:not(.empty-row):not(.dgr-sub-row):not(.irregularity-sub-row)');
                var message = '';
                var incomplete = false;
                var $actualDelivery = $('#actual_delivery_date');
                var $actualErrorBox = $('#actual-delivery-validation-error');
                var $actualErrorText = $('#actual-delivery-validation-error-text');
                var $currency = $('#currency_select');
                var $customsValue = $('#customs_value');
                var $currencySelection = $currency.next('.select2-container').find('.select2-selection');

                $rows.find('.pkg-l, .pkg-w, .pkg-h, .pkg-weight').css('border-color', '');
                $actualDelivery.css('border-color', '');
                $actualErrorText.text('');
                $actualErrorBox.hide();
                $customsValue.css('border-color', '');
                $currencySelection.css('border-color', '');

                if (!$.trim(String($currency.val() || ''))) {
                    e.preventDefault();
                    $currencySelection.css('border-color', '#dc3545');
                    $errorText.text('Currency is required.');
                    $errorBox.show();
                    $('html, body').animate({
                        scrollTop: $currency.closest('.crr-field-group').offset().top - 100
                    }, 300);
                    return false;
                }

                var customsValue = $.trim(String($customsValue.val() || ''));
                if (customsValue === '' || isNaN(parseFloat(customsValue))) {
                    e.preventDefault();
                    $customsValue.css('border-color', '#dc3545');
                    $errorText.text('Customs value is required.');
                    $errorBox.show();
                    $('html, body').animate({
                        scrollTop: $customsValue.offset().top - 100
                    }, 300);
                    return false;
                }

                if ($rows.length === 0) {
                    message = 'Please add at least one package with Length, Width, Height and Weight before saving.';
                } else {
                    $rows.each(function () {
                        var $row = $(this);
                        ['pkg-l', 'pkg-w', 'pkg-h', 'pkg-weight'].forEach(function (cls) {
                            var $input = $row.find('.' + cls);
                            var value = $.trim(String($input.val() || ''));
                            if (value === '' || isNaN(parseFloat(value)) || parseFloat(value) <= 0) {
                                incomplete = true;
                                $input.css('border-color', '#dc3545');
                            }
                        });
                    });
                    if (incomplete) {
                        message = 'Please fill Length, Width, Height and Weight (greater than 0) for all packages before saving.';
                    }
                }

                if (message) {
                    e.preventDefault();
                    $errorText.text(message);
                    $errorBox.show();
                    $('html, body').animate({
                        scrollTop: $('.crr-table-header').first().offset().top - 80
                    }, 300);
                    return false;
                }

                $errorText.text('');
                $errorBox.hide();

                if (String($('#crr_status').val()) === String(STOCK_STATUS) && !$.trim(String($actualDelivery.val() || ''))) {
                    e.preventDefault();
                    $actualDelivery.css('border-color', '#dc3545');
                    $actualErrorText.text('Actual delivery date is required when status is Stock.');
                    $actualErrorBox.show();
                    $('html, body').animate({
                        scrollTop: $actualDelivery.offset().top - 100
                    }, 300);
                    return false;
                }
            });

            $(document).on('click', '#packages-validation-error-close', function () {
                $('#packages-validation-error-text').text('');
                $('#packages-validation-error').hide();
            });

            $(document).on('click', '#actual-delivery-validation-error-close', function () {
                $('#actual-delivery-validation-error-text').text('');
                $('#actual-delivery-validation-error').hide();
                $('#actual_delivery_date').css('border-color', '');
            });

            $modal.off('hidden.bs.modal.supplierAdd').on('hidden.bs.modal.supplierAdd', function () {
                if (String($supplierSelect.val() || '').indexOf('__new__:') === 0 ||
                    ($supplierSelect.val() && !knownNames[String($supplierSelect.val()).toLowerCase()])) {
                    restorePreviousSelection();
                }
            });
        });
    </script>
@endsection