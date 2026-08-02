@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <!-- ... (other CSS links) ... -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        /* General Page Layout */
        .pcoded-inner-content {
            padding: 0 !important;
        }

        .main-body .page-wrapper {
            padding: 10px !important;
            background: #fff;
        }

        .card {
            border-radius: 0;
            box-shadow: none;
            margin-bottom: 0;
            border: none;
            background: transparent;
        }

        /* Form Grid Layout */
        .customers-create-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            padding: 20px 30px 100px 30px;
            /* Extra bottom padding for footer actions */
        }

        /* Section Styling */
        .form-section-title {
            font-size: 12px;
            font-weight: 600;
            color: #4e738e;
            /* Darker blue-gray */
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        /* Form Group Styling */
        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #555;
            margin-bottom: 4px;
            text-transform: none;
        }

        .form-control {
            height: 30px;
            font-size: 12px;
            border-radius: 2px;
            border: 1px solid #eef2f7;
            background-color: #fff;
            padding: 2px 8px;
            color: #333;
            box-shadow: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #01a9ac;
            box-shadow: none;
        }

        select.form-control {
            padding-top: 0;
            padding-bottom: 0;
        }

        textarea.form-control {
            height: auto;
            min-height: 50px;
        }

        /* Special Input Styles (with icons) */
        .input-with-icon {
            position: relative;
        }

        .input-with-icon .form-control {
            padding-right: 25px;
        }

        .input-icon-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            font-size: 12px;
            cursor: pointer;
            background: #eee;
            border-radius: 2px;
            padding: 1px 4px;
        }

        /* Row of fields (e.g., City, State, Zip) */
        .form-inline-row {
            display: flex;
            gap: 10px;
        }

        .form-inline-row .form-group {
            flex: 1;
        }

        /* Checkbox Styling */
        .custom-checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }

        .custom-checkbox-group input[type="checkbox"] {
            width: 13px;
            height: 13px;
            cursor: pointer;
            margin: 0;
        }

        .custom-checkbox-group span {
            font-size: 10px;
            color: #555;
        }

        /* Footer Actions (Bottom Left) */
        .page-footer-actions {
            position: fixed;
            bottom: 0;
            left: 185px;
            /* Sidebar width */
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1000;
        }

        .btn-save-customer {
            background-color: #1b5e6f;
            /* Teal/Dark Blue */
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-save-customer:hover {
            background-color: #144a57;
        }

        .link-cancel-customer {
            color: #01a9ac;
            font-size: 12px;
            text-decoration: none;
        }

        .link-cancel-customer:hover {
            text-decoration: underline;
        }

        /* Select2 Custom Styling to match other inputs */
        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single,
        .select2-selection,
        .select2-selection--single {
            background-color: #fff !important;
            background: #fff !important;
            border: 1px solid #eef2f7 !important;
            height: 28px !important;
            border-radius: 2px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: #fff !important;
            background: #fff !important;
            color: #333 !important;
            line-height: 26px !important;
            font-size: 11px !important;
            padding-left: 8px !important;
        }

        .select2-container .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            right: 5px !important;
        }

        .select2-container .select2-selection--single .select2-selection__placeholder,
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #999 !important;
        }

        /* Validation Error Styling */
        .error {
            color: #e74c3c !important;
            font-size: 10px !important;
            margin-top: 2px !important;
            font-weight: 500 !important;
            display: block !important;
        }

        input.error,
        select.error,
        textarea.error {
            border-color: #e74c3c !important;
        }

        .select2-container--default.error .select2-selection--single {
            border-color: #e74c3c !important;
        }

        /* SOP Tab Specifics */
        .sop-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            padding: 20px;
        }

        .upload-area {
            border: 1px dashed #ccc;
            background: #fcfcfc;
            padding: 40px 20px;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
        }

        .upload-area i {
            display: block;
            font-size: 24px;
            color: #01a9ac;
            margin-top: 10px;
        }

        .upload-area p {
            font-size: 11px;
            color: #666;
            margin: 0;
        }

        /* Vessels Tab Specifics */
        .vessels-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .vessels-search-container {
            display: flex;
            align-items: center;
            background: #fcfcfc;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 5px 15px;
            width: 60%;
        }

        .vessels-search-container label {
            margin: 0 10px 0 0;
            font-size: 11px;
            color: #888;
            font-weight: 500;
        }

        .vessels-search-container input {
            border: none;
            background: transparent;
            font-size: 12px;
            width: 100%;
            outline: none;
        }

        .vessels-actions {
            display: flex;
            gap: 10px;
        }

        .btn-add-vessel {
            background: #fff;
            color: #01a9ac;
            border: 1px solid #01a9ac;
            padding: 5px 15px;
            font-size: 12px;
            border-radius: 4px;
        }

        .btn-download-vessels {
            background: #fff;
            color: #555;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .vessels-table {
            width: 100%;
            border-collapse: collapse;
        }

        .vessels-table th {
            text-align: left;
            padding: 12px 20px;
            font-size: 11px;
            color: #555;
            font-weight: 500;
            border-bottom: 1px solid #eee;
        }

        .vessels-table td {
            padding: 12px 20px;
            font-size: 12px;
            color: #333;
            border-bottom: 1px dotted #eee;
        }

        .vessels-table a {
            color: #01a9ac;
            text-decoration: none;
        }

        .vessels-table .edit-icon {
            color: #ccc;
            cursor: pointer;
        }

        /* Contacts Tab Specifics */
        .contacts-header {
            display: flex;
            justify-content: flex-end;
            padding: 10px 20px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .btn-add-contact {
            background: transparent;
            border: 1px solid #01a9ac;
            color: #01a9ac;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
        }

        .contacts-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            color: #004a99;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            background: #fcfcfc;
        }

        .contacts-table td {
            padding: 12px 15px;
            font-size: 12px;
            color: #555;
            border-bottom: 1px solid #f9f9f9;
            vertical-align: middle;
        }

        .contacts-table .name-link {
            color: #01a9ac;
            text-decoration: none;
            font-weight: 500;
        }

        .contacts-table .action-icon {
            font-size: 16px;
            color: #ccc;
            cursor: pointer;
            margin-left: 10px;
        }

        .contacts-table .action-icon:hover {
            color: #888;
        }

        .main-contact-check {
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        @media (max-width: 1199.98px) {
            .customers-create-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px 20px 100px;
            }
        }

        @media (max-width: 991.98px) {
            .main-body .page-wrapper {
                padding: 0 !important;
            }

            .customers-create-grid {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px 12px 110px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .customers-create-grid > div {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .form-inline-row {
                flex-direction: column !important;
                gap: 12px;
            }

            .form-inline-row .form-group,
            .form-inline-row .form-group[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
            }

            .page-footer-actions {
                left: 0 !important;
                right: 0 !important;
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .btn-save-customer,
            .link-cancel-customer {
                width: 100%;
                text-align: center;
            }

            .form-group label {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .sop-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.pcoded-shell-start')

                            @if(session('success'))
                                <div class="alert alert-success mt-2">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger mt-2">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger mt-2">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Summary Bar -->
                            <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
                                @csrf
                                <div class="card mt-2">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="customer-details" role="tabpanel">
                                            <div class="customers-create-grid">
                                                <!-- Column 1: Customer information -->
                                                <div>
                                                    <div class="form-section-title">Customer information</div>
                                                    <div class="form-group">
                                                        <label>Customer name</label>
                                                        <div class="input-with-icon">
                                                            <input type="text" name="customer_name" class="form-control"
                                                                value="{{ old('customer_name') }}" placeholder="">
                                                            <span class="input-icon-btn"><i class="ti-more-alt"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-none">
                                                        <label>Customer number from FM</label>
                                                        <input type="text" name="customer_number_fm" class="form-control"
                                                            value="{{ old('customer_number_fm') }}">
                                                    </div>
                                                    <div class="form-group d-none">
                                                        <label>Customer group</label>
                                                        <select name="customer_group" class="form-control select2-field">
                                                            <option></option>
                                                            <option value="N/A" {{ old('customer_group') === 'N/A' ? 'selected' : '' }}>N/A</option>
                                                            @foreach($groups as $group)
                                                                <option value="{{ $group->id }}" {{ (string) old('customer_group') === (string) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Phone number (with country code)</label>
                                                        <input type="text" name="phone_number" class="form-control"
                                                            value="{{ old('phone_number') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mail</label>
                                                        <input type="text" name="email" class="form-control"
                                                            value="{{ old('email') }}"
                                                            placeholder="email@example.com; email2@example.com">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Internal shipment</label>
                                                        <select name="internal_shipment" class="form-control select2-field">
                                                            <option></option>
                                                            <option value="1" {{ old('internal_shipment') === '1' ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ old('internal_shipment') === '0' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control">{{ old('remarks') }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Special considerations for destination</label>
                                                        <textarea name="special_considerations"
                                                            class="form-control">{{ old('special_considerations') }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>UN / LOCODE</label>
                                                        <select name="un_locode" class="form-control select2-field">
                                                            <option></option>
                                                            @if(old('un_locode'))
                                                                <option value="{{ old('un_locode') }}" selected>{{ old('un_locode') }}</option>
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="custom-checkbox-group d-none">
                                                        <input type="checkbox" name="show_transport_details" {{ old('show_transport_details') ? 'checked' : '' }}>
                                                        <span>Show transport details on customer portal</span>
                                                    </div>
                                                    <div class="custom-checkbox-group d-none">
                                                        <input type="checkbox" name="esea_store_stock_only" {{ old('esea_store_stock_only') ? 'checked' : '' }}>
                                                        <span>eSea store stock only</span>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Customer address & Postal address -->
                                                <div>
                                                    <div class="form-section-title">Customer address</div>
                                                    <div class="form-group">
                                                        <label>Street address</label>
                                                        <input type="text" name="street_address" class="form-control"
                                                            value="{{ old('street_address') }}">
                                                    </div>
                                                    <div class="form-inline-row">
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>City</label>
                                                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>District/state</label>
                                                            <input type="text" name="district_state" class="form-control"
                                                                value="{{ old('district_state') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 1.5;">
                                                            <label>Zip code</label>
                                                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <select name="country" class="form-control select2-field">
                                                            <option></option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->flag_url }}"
                                                                    {{ (string) old('country') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Port code</label>
                                                        <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                            <option value=""></option>
                                                            @if (old('port_code'))
                                                                <option value="{{ old('port_code') }}" selected>{{ old('port_code') }}</option>
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="form-section-title" style="margin-top: 30px;">Postal address
                                                        (Optional)</div>
                                                    <div class="form-group">
                                                        <label>Street address/post box</label>
                                                        <input type="text" name="postal_street_address"
                                                            class="form-control" value="{{ old('postal_street_address') }}">
                                                    </div>
                                                    <div class="form-inline-row">
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>City</label>
                                                            <input type="text" name="postal_city" class="form-control"
                                                                value="{{ old('postal_city') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>District/state</label>
                                                            <input type="text" name="postal_district_state"
                                                                class="form-control" value="{{ old('postal_district_state') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 1.5;">
                                                            <label>Zip code</label>
                                                            <input type="text" name="postal_zip_code" class="form-control"
                                                                value="{{ old('postal_zip_code') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <select name="postal_country" class="form-control select2-field">
                                                            <option></option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->flag_url }}"
                                                                    {{ (string) old('postal_country') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 3: Invoice details -->
                                                <div>
                                                    <div class="form-section-title">Invoice details</div>
                                                    <div class="form-group">
                                                        <label>Invoice recipient name</label>
                                                        <input type="text" name="invoice_recipient_name"
                                                            class="form-control" value="{{ old('invoice_recipient_name') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Invoice recipient address</label>
                                                        <input type="text" name="invoice_recipient_address"
                                                            class="form-control" value="{{ old('invoice_recipient_address') }}">
                                                    </div>
                                                    <div class="form-inline-row">
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>City</label>
                                                            <input type="text" name="invoice_city" class="form-control"
                                                                value="{{ old('invoice_city') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 2;">
                                                            <label>District/state</label>
                                                            <input type="text" name="invoice_district_state"
                                                                class="form-control" value="{{ old('invoice_district_state') }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 1.5;">
                                                            <label>Zip code</label>
                                                            <input type="text" name="invoice_zip_code" class="form-control"
                                                                value="{{ old('invoice_zip_code') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <select name="invoice_country" class="form-control select2-field">
                                                            <option></option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->flag_url }}"
                                                                    {{ (string) old('invoice_country') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Currency</label>
                                                        <select name="currency" class="form-control select2-field">
                                                            <option></option>
                                                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                            <option value="INR" {{ old('currency') === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mails for invoicing</label>
                                                        <input type="text" name="invoicing_email" class="form-control"
                                                            value="{{ old('invoicing_email') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mails for invoicing (CC)</label>
                                                        <input type="text" name="invoicing_email_cc" class="form-control"
                                                            value="{{ old('invoicing_email_cc') }}">
                                                    </div>
                                                    <div class="form-inline-row">
                                                        <div class="form-group" style="flex: 1;">
                                                            <label>Payment terms (days)</label>
                                                            <input type="text" name="payment_terms" class="form-control"
                                                                value="{{ old('payment_terms', 30) }}">
                                                        </div>
                                                        <div class="form-group" style="flex: 1;">
                                                            <label>Invoice frequency</label>
                                                            <select name="invoice_frequency" class="form-control">
                                                                <option></option>
                                                                <option value="Daily" {{ old('invoice_frequency') === 'Daily' ? 'selected' : '' }}>Daily</option>
                                                                <option value="Weekly" {{ old('invoice_frequency') === 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                                                <option value="Monthly" {{ old('invoice_frequency') === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Remarks regarding invoicing</label>
                                                        <textarea name="invoicing_remarks" class="form-control"
                                                            style="min-height: 40px;">{{ old('invoicing_remarks') }}</textarea>
                                                    </div>
                                                    <div class="form-inline-row">
                                                        <div class="form-group">
                                                            <label>VAT number</label>
                                                            <input type="text" name="vat_number" class="form-control"
                                                                value="{{ old('vat_number') }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>EORI number</label>
                                                            <input type="text" name="eori_number" class="form-control"
                                                                value="{{ old('eori_number') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 4: Responsible office/person -->
                                                <div>
                                                    <div class="form-section-title">Responsible office/person</div>
                                                    <div class="form-group">
                                                        <label>Sales manager</label>
                                                        <select name="sales_manager" class="form-control select2-field">
                                                            <option></option>
                                                            @foreach($salesManagers as $manager)
                                                                <option value="{{ $manager->id }}" {{ (string) old('sales_manager') === (string) $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Main account manager</label>
                                                        <select id="main-account-manager-select" name="main_account_manager"
                                                            class="form-control select2-office-user">
                                                            <option value=""></option>
                                                            @php
                                                                $selectedMainAccountManager = old('main_account_manager')
                                                                    ? \App\Models\Contact::find(old('main_account_manager'))
                                                                    : null;
                                                            @endphp
                                                            @if($selectedMainAccountManager)
                                                                <option value="{{ $selectedMainAccountManager->id }}" selected>{{ $selectedMainAccountManager->name }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Responsible accounting users</label>
                                                        <select id="responsible-accounting-users-select" name="responsible_accounting_users"
                                                            class="form-control select2-office-user">
                                                            <option value=""></option>
                                                            @php
                                                                $selectedAccountingUser = old('responsible_accounting_users')
                                                                    ? \App\Models\Contact::with('office')->find(old('responsible_accounting_users'))
                                                                    : null;
                                                                $responsibleOfficeShortName = old(
                                                                    'responsible_office',
                                                                    $selectedAccountingUser?->office?->office_short_name ?? ''
                                                                );
                                                            @endphp
                                                            @if($selectedAccountingUser)
                                                                <option value="{{ $selectedAccountingUser->id }}" selected
                                                                    data-office-short-name="{{ $selectedAccountingUser->office?->office_short_name ?? '' }}">{{ $selectedAccountingUser->name }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Responsible Office</label>
                                                        <input type="text" id="responsible-office-input" name="responsible_office"
                                                            class="form-control" value="{{ $responsibleOfficeShortName }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end tab-content -->
                                </div> <!-- end card -->

                                <!-- Footer Actions -->
                                <div class="page-footer-actions">
                                    <button type="submit" class="btn-save-customer">Save</button>
                                    <a href="{{ route('customers.index') }}" class="link-cancel-customer">Cancel</a>
                                </div>

                            </form>
    @include('layouts.partials.pcoded-shell-end')

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
    <!-- jQuery Validation js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Select2 on specified fields
            function formatCountry(state) {
                if (!state.id) {
                    return state.text;
                }
                var flagUrl = $(state.element).data('flag');
                if (!flagUrl) {
                    return state.text;
                }
                var $state = $(
                    '<span><img src="' + flagUrl + '" class="img-flag" style="width: 20px; height: 15px; margin-right: 8px; vertical-align: middle;" /> ' + state.text + '</span>'
                );
                return $state;
            };

            function formatOfficeUser(item) {
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

                var subtitle = subtitleParts.join(' · ');
                return $(
                    '<div style="line-height:1.1;"><div style="font-weight:600;">' + item.text + '</div>' +
                    (subtitle ? '<div style="font-size:11px;color:#6b7280;">' + subtitle + '</div>' : '') +
                    '</div>'
                );
            }

            function formatOfficeUserSelection(item) {
                return item.text || item.id;
            }

            function initOfficeUserSelect(selector, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: false,
                    width: '100%',
                    minimumInputLength: 0,
                    ajax: {
                        url: @json(url('/api/account-managers')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                categories: 'operations,account,manager'
                            };
                        },
                        processResults: function (data) {
                            return { results: data };
                        }
                    },
                    templateResult: formatOfficeUser,
                    templateSelection: formatOfficeUserSelection
                });
            }

            initOfficeUserSelect('#main-account-manager-select', 'Select account manager');
            initOfficeUserSelect('#responsible-accounting-users-select', 'Select accounting user');

            function setResponsibleOfficeFromAccountingUser(officeShortName) {
                $('#responsible-office-input').val(officeShortName || '');
            }

            $('#responsible-accounting-users-select').on('select2:select', function (e) {
                setResponsibleOfficeFromAccountingUser(e.params.data.office_short_name);
            });

            $('#responsible-accounting-users-select').on('select2:clear', function () {
                setResponsibleOfficeFromAccountingUser('');
            });

            var $preselectedAccountingUser = $('#responsible-accounting-users-select option:selected');
            if ($preselectedAccountingUser.length && $preselectedAccountingUser.val()) {
                setResponsibleOfficeFromAccountingUser(
                    $preselectedAccountingUser.attr('data-office-short-name') || $('#responsible-office-input').val()
                );
            }

            $('.select2-field').select2({
                placeholder: 'Select an option',
                allowClear: false,
                width: '100%',
                templateResult: formatCountry,
                templateSelection: formatCountry
            });

            function formatPortResult(port) {
                if (port.loading || !port.id) {
                    return port.text;
                }

                var title = port.code + (port.city ? ', ' + port.city : '');
                var $option = $(
                    '<div class="port-option">' +
                        '<div style="font-weight: 600; font-size: 13px; color: #111827;"></div>' +
                        '<div style="font-size: 12px; color: #6b7280;"></div>' +
                    '</div>'
                );
                $option.find('div').eq(0).text(title);
                $option.find('div').eq(1).text(port.country || '');

                return $option;
            }

            function formatPortSelection(port) {
                if (!port.id) {
                    return port.text;
                }

                return port.code
                    ? port.code + (port.city ? ', ' + port.city : '')
                    : port.text;
            }

            $('.select2-port-code').select2({
                placeholder: 'Search port code',
                allowClear: false,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('api.ports') }}',
                    dataType: 'json',
                    delay: 200,
                    data: function (params) {
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        return { results: data.results || [] };
                    },
                    cache: true
                },
                templateResult: formatPortResult,
                templateSelection: formatPortSelection
            });

            // Trigger validation on Select2 change
            $('.select2-field, .select2-office-user').on('change', function () {
                $(this).valid();
                // Handle the red border for Select2 container
                if ($(this).hasClass('error')) {
                    $(this).next('.select2-container').addClass('error');
                } else {
                    $(this).next('.select2-container').removeClass('error');
                }
            });

            // Initialize form validation
            $.validator.addMethod('multiEmail', function (value, element) {
                if (this.optional(element)) {
                    return true;
                }

                var emails = value.split(/[;,]+/).map(function (part) {
                    return $.trim(part);
                }).filter(Boolean);

                if (!emails.length) {
                    return false;
                }

                return emails.every(function (email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                });
            }, 'Please enter valid email address(es), separated by comma or semicolon');

            $("#customerForm").validate({
                rules: {
                    customer_name: "required",
                    email: {
                        required: true,
                        multiEmail: true
                    },
                    street_address: "required",
                    city: "required",
                    country: "required",
                    invoice_recipient_name: "required",
                    invoice_recipient_address: "required",
                    invoice_city: "required",
                    invoice_country: "required",
                    currency: "required",
                    invoicing_email: {
                        required: true,
                        email: true
                    },
                    invoicing_email_cc: {
                        email: true
                    },
                    // sales_manager: "required",
                    main_account_manager: "required"
                },
                messages: {
                    customer_name: "Please enter customer name",
                    email: "Please enter valid email address(es), separated by comma or semicolon",
                    street_address: "Please enter street address",
                    city: "Please enter city",
                    country: "Please select country",
                    invoice_recipient_name: "Please enter recipient name",
                    invoice_recipient_address: "Please enter recipient address",
                    invoice_city: "Please enter city",
                    invoice_country: "Please select country",
                    currency: "Please select currency",
                    invoicing_email: "Please enter a valid invoicing email",
                    // sales_manager: "Please select sales manager",
                    main_account_manager: "Please select account manager"
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-field') || element.hasClass('select2-office-user')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-with-icon').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });

            // Also initialize the existing responsible accounting users select
            if ($('select.form-control').not('.select2-field, .select2-office-user').length) {
                $('select.form-control').not('.select2-field, .select2-office-user').select2({
                    placeholder: 'Select user',
                    width: '100%'
                });
            }

            // Real-time search for Vessels table
            $("#vesselSearchInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#vesselsTable tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Real-time search for Contacts table
            $("#contactSearchInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#contactsTable tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection