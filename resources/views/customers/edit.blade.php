@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <!-- Select 2 css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        /* General Page Layout */
        .pcoded-inner-content { padding: 0 !important; }
        .main-body .page-wrapper { padding: 10px !important; background: #fff; }
        .card { border-radius: 0; box-shadow: none; margin-bottom: 0; border: none; background: transparent; }
        
        /* Top Summary Bar */
        .summary-bar {
            background: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }
        .summary-item { text-align: left; margin-right: 30px; }
        .summary-label { font-size: 11px; color: #888; display: block; margin-bottom: 2px; }
        .summary-value { font-size: 14px; font-weight: 700; color: #333; }
        .summary-value.active { color: #28a745; }

        /* Custom Styled Tabs */
        .custom-edit-tabs {
            background: #e9ecef;
            border: none;
            padding: 0 10px;
        }
        .custom-edit-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-size: 13px;
            color: #495057;
            font-weight: 500;
        }
        .custom-edit-tabs .nav-link.active {
            background: #fff;
            color: #333;
            border-bottom: 2px solid #01a9ac;
        }

        /* Form Grid Layout */
        .customers-edit-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            padding: 20px 30px 100px 30px;
        }

        /* Section Styling */
        .form-section-title {
            font-size: 12px;
            font-weight: 600;
            color: #4e738e;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        /* Form Group Styling */
        .form-group { margin-bottom: 12px; }
        .form-group label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #555;
            margin-bottom: 4px;
            text-transform: none;
        }
        .form-control {
            height: 28px;
            font-size: 11px;
            border-radius: 2px;
            border: 1px solid #eef2f7;
            background-color: #fff;
            padding: 2px 8px;
            color: #333;
            box-shadow: none;
        }
        .form-control:focus { border-color: #01a9ac; box-shadow: none; }
        
        /* Special Input Styles (with icons) */
        .input-with-icon { position: relative; }
        .input-with-icon .form-control { padding-right: 25px; }
        .input-icon-btn {
            position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
            color: #ccc; font-size: 12px; cursor: pointer;
            background: #eee; border-radius: 2px; padding: 1px 4px;
        }

        /* Row of fields */
        .form-inline-row { display: flex; gap: 10px; }
        .form-inline-row .form-group { flex: 1; }

        /* Checkbox Styling */
        .custom-checkbox-group { display: flex; align-items: center; gap: 8px; margin-top: 15px; }
        .custom-checkbox-group input[type="checkbox"] { width: 13px; height: 13px; cursor: pointer; margin: 0; }
        .custom-checkbox-group span { font-size: 10px; color: #555; }
        
        /* Footer Actions */
        .page-footer-actions {
            position: fixed; bottom: 0; left: 185px; right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 30px; display: flex; align-items: center; gap: 20px; z-index: 1000;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }
        .page-footer-actions .audit-meta {
            margin-left: auto;
            text-align: right;
            font-size: 11px;
            color: #999;
            line-height: 1.4;
        }
        .btn-update-customer {
            background-color: #1b5e6f; color: #fff; border: none; border-radius: 4px;
            padding: 6px 20px; font-size: 12px; font-weight: 500; cursor: pointer;
        }
        .btn-update-customer:disabled {
            background-color: #e9ecef !important;
            color: #a0aec0 !important;
            cursor: default !important;
            opacity: 1;
        }
        .link-cancel-customer { color: #01a9ac; font-size: 12px; text-decoration: none; }

        /* Select2 override */
        .select2-container .select2-selection--single {
            border: 1px solid #eef2f7 !important;
            height: 28px !important;
            border-radius: 2px !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            font-size: 11px !important;
            color: #333 !important;
            padding-left: 8px !important;
            background-color: transparent !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
        }

        /* Validation Error Styling */
        .error { color: #e74c3c !important; font-size: 10px !important; margin-top: 2px !important; }
        input.error, select.error { border-color: #e74c3c !important; }

        /* Logo Upload Placeholder */
        .logo-placeholder {
            border: 1px dashed #ccc; padding: 20px; text-align: center; color: #888; margin-top: 10px; border-radius: 4px;
        }
        .logo-placeholder i { font-size: 24px; display: block; margin-top: 5px; color: #01a9ac; }
/* SOP Tab Specifics */
        .sop-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; padding: 20px; }
        .upload-area { border: 1px dashed #ccc; background: #fcfcfc; padding: 40px 20px; text-align: center; border-radius: 4px; }
        .upload-area i { display: block; font-size: 24px; color: #01a9ac; margin-top: 10px; }

        /* Table Styling */
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th { text-align: left; padding: 12px 15px; font-size: 12px; color: #004a99; font-weight: 600; border-bottom: 1px solid #eee; background: #fcfcfc; }
        .custom-table td { padding: 12px 15px; font-size: 12px; color: #555; border-bottom: 1px solid #f9f9f9; }
        /* Vessels Tab Redesign */
        .vessels-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #fff;
            border-bottom: 1px solid #f1f4f6;
        }
        .vessels-search-container {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 4px;
            padding: 2px 15px;
            min-width: 400px;
        }
        .search-label {
            font-size: 11px;
            font-weight: 600;
            color: #4e738e;
            padding-right: 12px;
            border-right: 1px solid #eee;
            margin-right: 12px;
            white-space: nowrap;
        }
        .vessels-search-container input {
            border: none;
            outline: none;
            font-size: 11px;
            color: #333;
            width: 100%;
            height: 28px;
            background: transparent;
        }
        .btn-vessel-export {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #01a9ac;
            color: #01a9ac;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-vessel-export:hover {
            background: #01a9ac;
            color: #fff;
        }
        .btn-vessel-add {
            background: #fff;
            color: #01a9ac;
            border: 1px solid #01a9ac;
            border-radius: 4px;
            padding: 6px 15px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-vessel-add:hover {
            background: #01a9ac;
            color: #fff;
        }
        
        .vessels-table th {
            color: #4e738e !important;
            font-weight: 500 !important;
            border-top: none !important;
        }
        .vessels-table td {
            vertical-align: middle !important;
        }
        .vessel-link {
            color: #01a9ac;
            font-weight: 500;
            text-decoration: none;
        }
        .vessel-link:hover {
            text-decoration: underline;
        }

        /* File Upload Styling */
        .logo-placeholder.dragover, .upload-area.dragover {
            border-color: #01a9ac !important;
            background-color: #f0fbfc !important;
        }
        .file-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 5px;
            font-size: 11px;
            border: 1px solid #eee;
        }
        .file-item i { color: #01a9ac; }
        .file-item .remove-file {
            margin-left: auto;
            color: #e74c3c;
            cursor: pointer;
        }

        /* Fix Select2 being shown twice or layout conflicts */
        .select2-container {
            display: block !important;
            width: 100% !important;
        }
        .select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            -webkit-clip-path: inset(50%) !important;
            clip-path: inset(50%) !important;
            height: 1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            white-space: nowrap !important;
        }
    </style>
@endsection

@section('content')
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            @include('layouts.top-menu')
            @include('layouts.left-menu')
            
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            
                            <!-- Summary Bar -->
                            <div class="summary-bar">
                                <div style="display: flex;">
                                    <div class="summary-item">
                                        <span class="summary-label">Company name</span>
                                        <span class="summary-value">{{ $customer->customer_name }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">FM Number</span>
                                        <span class="summary-value">{{ $customer->customer_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Account manager</span>
                                        <span class="summary-value">{{ $customer->responsible->accountManager->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Status</span>
                                        <span class="summary-value active">Active</span>
                                    </div>
                                </div>
                            </div>

                            <form id="customerForm" action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card mt-2">
                                    <ul class="nav nav-tabs custom-edit-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#customer-details" role="tab">Customer details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#contacts" role="tab">Contacts</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#sop" role="tab">SOP</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#vessels" role="tab">Vessels</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#notification-settings" role="tab">Notification settings</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="customer-details" role="tabpanel">
                                            <div class="customers-edit-grid">
                                                <!-- Column 1: Customer information -->
                                                <div>
                                                    <div class="form-section-title">Customer information</div>
                                                    <div class="form-group">
                                                        <label>Customer name</label>
                                                        <div class="input-with-icon">
                                                            <input type="text" name="customer_name" class="form-control" value="{{ $customer->customer_name }}">
                                                            <span class="input-icon-btn"><i class="ti-more-alt"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-none">
                                                        <label>Customer number from FM</label>
                                                        <input type="text" name="customer_number_fm" class="form-control" value="{{ $customer->customer_number }}">
                                                    </div>
                                                    <div class="form-group d-none">
                                                        <label>Customer group</label>
                                                        <select name="customer_group" class="form-control select2-field">
                                                            <option value="N/A" {{ !$customer->customer_group_id ? 'selected' : '' }}>N/A</option>
                                                            @foreach($groups as $group)
                                                                <option value="{{ $group->id }}" {{ $customer->customer_group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Phone number (with country code)</label>
                                                        <input type="text" name="phone_number" class="form-control" value="{{ $customer->phone }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mail</label>
                                                        <input type="text" name="email" class="form-control"
                                                            value="{{ $customer->email }}"
                                                            placeholder="email@example.com; email2@example.com">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Internal shipment</label>
                                                        <select name="internal_shipment" class="form-control select2-field">
                                                            <option value="1" {{ $customer->internal_shipment == 1 ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ $customer->internal_shipment == 0 ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control">{{ $customer->remarks }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Special considerations for destination</label>
                                                        <textarea name="special_considerations" class="form-control">{{ $customer->special_considerations }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>UN / LOCODE</label>
                                                        <input type="text" name="un_locode" class="form-control" value="{{ $customer->un_locode }}">
                                                    </div>
                                                    
                                                    <div class="custom-checkbox-group d-none">
                                                        <input type="checkbox" name="show_transport_details" {{ $customer->show_transport_details ? 'checked' : '' }}>
                                                        <span>Show transport details on customer portal</span>
                                                    </div>
                                                    <div class="custom-checkbox-group d-none">
                                                        <input type="checkbox" name="esea_store_stock_only" {{ $customer->esea_store_stock_only ? 'checked' : '' }}>
                                                        <span>eSea store stock only</span>
                                                    </div>
                                                </div>

                                            <!-- Column 2: Customer address & Postal address -->
                                            <div>
                                                <div class="form-section-title">Customer address</div>
                                                <div class="form-group">
                                                    <label>Street address</label>
                                                    <input type="text" name="street_address" class="form-control" value="{{ $customer->primaryAddress->street ?? '' }}">
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>City</label>
                                                        <input type="text" name="city" class="form-control" value="{{ $customer->primaryAddress->city ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>District/state</label>
                                                        <input type="text" name="district_state" class="form-control" value="{{ $customer->primaryAddress->state ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 1.5;">
                                                        <label>Zip code</label>
                                                        <input type="text" name="zip_code" class="form-control" value="{{ $customer->primaryAddress->zip_code ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select name="country" class="form-control select2-field">
                                                        <option></option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" data-flag="{{ $country->flag_url }}" {{ ($customer->primaryAddress->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Port code</label>
                                                    <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                        <option value=""></option>
                                                        @if (old('port_code', $customer->primaryAddress->port_code ?? ''))
                                                            <option value="{{ old('port_code', $customer->primaryAddress->port_code ?? '') }}" selected>
                                                                {{ old('port_code', $customer->primaryAddress->port_code ?? '') }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="form-section-title" style="margin-top: 30px;">Postal address (Optional)</div>
                                                <div class="form-group">
                                                    <label>Street address/post box</label>
                                                    <input type="text" name="postal_street_address" class="form-control" value="{{ $customer->postalAddress->street ?? '' }}">
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>City</label>
                                                        <input type="text" name="postal_city" class="form-control" value="{{ $customer->postalAddress->city ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>District/state</label>
                                                        <input type="text" name="postal_district_state" class="form-control" value="{{ $customer->postalAddress->state ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 1.5;">
                                                        <label>Zip code</label>
                                                        <input type="text" name="postal_zip_code" class="form-control" value="{{ $customer->postalAddress->zip_code ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select name="postal_country" class="form-control select2-field">
                                                        <option></option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" data-flag="{{ $country->flag_url }}" {{ ($customer->postalAddress->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Column 3: Invoice details -->
                                            <div>
                                                <div class="form-section-title">Invoice details</div>
                                                <div class="form-group">
                                                    <label>Invoice recipient name</label>
                                                    <input type="text" name="invoice_recipient_name" class="form-control" value="{{ $customer->invoiceDetail->invoice_recipient_name ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Invoice recipient address</label>
                                                    <input type="text" name="invoice_recipient_address" class="form-control" value="{{ $customer->invoiceAddress->street ?? '' }}">
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>City</label>
                                                        <input type="text" name="invoice_city" class="form-control" value="{{ $customer->invoiceAddress->city ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 2;">
                                                        <label>District/state</label>
                                                        <input type="text" name="invoice_district_state" class="form-control" value="{{ $customer->invoiceAddress->state ?? '' }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 1.5;">
                                                        <label>Zip code</label>
                                                        <input type="text" name="invoice_zip_code" class="form-control" value="{{ $customer->invoiceAddress->zip_code ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select name="invoice_country" class="form-control select2-field">
                                                        <option></option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" data-flag="{{ $country->flag_url }}" {{ ($customer->invoiceAddress->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Currency</label>
                                                    <select name="currency" class="form-control select2-field">
                                                        <option value="USD" {{ ($customer->invoiceDetail->currency_code ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                        <option value="EUR" {{ ($customer->invoiceDetail->currency_code ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                        <option value="GBP" {{ ($customer->invoiceDetail->currency_code ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                        <option value="INR" {{ ($customer->invoiceDetail->currency_code ?? '') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>E-mails for invoicing</label>
                                                    <input type="text" name="invoicing_email" class="form-control" value="{{ $customer->invoiceDetail->invoice_email ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>E-mails for invoicing (CC)</label>
                                                    <input type="text" name="invoicing_email_cc" class="form-control" value="{{ $customer->invoiceDetail->invoice_email_cc ?? '' }}">
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group" style="flex: 1;">
                                                        <label>Payment terms (days)</label>
                                                        <input type="text" name="payment_terms" class="form-control" value="{{ $customer->invoiceDetail->payment_terms_days ?? 30 }}">
                                                    </div>
                                                    <div class="form-group" style="flex: 1;">
                                                        <label>Invoice frequency</label>
                                                        <select name="invoice_frequency" class="form-control">
                                                            <option value="Daily" {{ ($customer->invoiceDetail->invoice_frequency ?? '') == 'Daily' ? 'selected' : '' }}>Daily</option>
                                                            <option value="Weekly" {{ ($customer->invoiceDetail->invoice_frequency ?? '') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                                            <option value="Monthly" {{ ($customer->invoiceDetail->invoice_frequency ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Remarks regarding invoicing</label>
                                                    <textarea name="invoicing_remarks" class="form-control" style="min-height: 40px;">{{ $customer->invoiceDetail->invoice_remarks ?? '' }}</textarea>
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group">
                                                        <label>VAT number</label>
                                                        <input type="text" name="vat_number" class="form-control" value="{{ $customer->invoiceDetail->vat_number ?? '' }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>EORI number</label>
                                                        <input type="text" name="eori_number" class="form-control" value="{{ $customer->invoiceDetail->eori_number ?? '' }}">
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
                                                            <option value="{{ $manager->id }}" {{ ($customer->responsible->sales_manager_id ?? '') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Main account manager</label>
                                                    <select id="main-account-manager-select" name="main_account_manager" class="form-control select2-office-user">
                                                        <option value=""></option>
                                                        @php
                                                            $selectedMainAccountManagerId = old('main_account_manager', $customer->responsible->account_manager_id ?? null);
                                                            $selectedMainAccountManager = $selectedMainAccountManagerId
                                                                ? \App\Models\Contact::find($selectedMainAccountManagerId)
                                                                : null;
                                                        @endphp
                                                        @if($selectedMainAccountManager)
                                                            <option value="{{ $selectedMainAccountManager->id }}" selected>{{ $selectedMainAccountManager->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Responsible accounting users</label>
                                                    <select id="responsible-accounting-users-select" name="responsible_accounting_users" class="form-control select2-office-user">
                                                        <option value=""></option>
                                                        @php
                                                            $selectedAccountingUserId = old('responsible_accounting_users', $customer->responsible->accounting_user_id ?? null);
                                                            $selectedAccountingUser = $selectedAccountingUserId
                                                                ? (
                                                                    ($customer->responsible?->accountingUser?->id ?? null) == $selectedAccountingUserId
                                                                        ? $customer->responsible->accountingUser
                                                                        : \App\Models\Contact::with('office')->find($selectedAccountingUserId)
                                                                )
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

                                                 <div class="form-section-title">Company's logo</div>
                                                 <div class="logo-placeholder" id="logo_drop_zone" style="cursor: pointer; position: relative;">
                                                     @if($customer->logo)
                                                         <img src="{{ route('customers.logo.show', $customer->id) }}" id="logo_preview" style="max-width: 100%; max-height: 80px; margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;">
                                                     @else
                                                         <img src="" id="logo_preview" style="max-width: 100%; max-height: 80px; margin-bottom: 8px; display: none; margin-left: auto; margin-right: auto;">
                                                     @endif
                                                     <p id="logo_text" style="font-size: 11px; margin: 0;">Drag image file here or click to browse</p>
                                                     <i class="ti-camera" id="logo_icon"></i>
                                                 </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="contacts" role="tabpanel">
                                        <div class="vessels-header">
                                            <div class="vessels-search-container">
                                                <span class="search-label">Search</span>
                                                <input type="text" id="contactSearchInput" placeholder="type here">
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="{{ route('contacts.create', $customer->id) }}" class="btn-vessel-add">Add contact</a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table" id="contactsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone number</th>
                                                        <th>Description</th>
                                                        <th style="text-align: center;">Main contact</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customer->contacts as $contact)
                                                        <tr>
                                                            <td><a href="{{ route('contacts.edit', $contact->id) }}" style="color: #01a9ac; font-weight: 500;">{{ $contact->name }}</a></td>
                                                            <td>{{ $contact->email }}</td>
                                                            <td>{{ $contact->phone_number }}</td>
                                                            <td>{{ $contact->description }}</td>
                                                            <td style="text-align: center;">
                                                                @if($contact->is_main_contact)
                                                                    <i class="ti-check" style="font-weight: bold; color: #333;"></i>
                                                                @endif
                                                            </td>
                                                            <td style="text-align: right;">
                                                                <a href="{{ route('contacts.edit', $contact->id) }}">
                                                                    <i class="ti-pencil" style="color: #ccc; cursor: pointer; margin-right: 10px;"></i>
                                                                </a>
                                                                <i class="ti-trash delete-contact" data-id="{{ $contact->id }}" style="color: #ccc; cursor: pointer;"></i>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="tab-pane" id="sop" role="tabpanel">
                                        <div class="sop-grid">
                                            <!-- Column 1: General procedures -->
                                            <div>
                                                <div class="form-section-title" style="margin-top: 0;">General procedures</div>
                                                <div class="form-inline-row">
                                                    <div class="form-group">
                                                        <label>Send stocklist?</label>
                                                        <select name="send_stocklist" class="form-control">
                                                            <option></option>
                                                            <option value="Yes" {{ ($customer->sop->send_stocklist ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ ($customer->sop->send_stocklist ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Onboard delivery?</label>
                                                        <select name="onboard_delivery" class="form-control">
                                                            <option></option>
                                                            <option value="Yes" {{ ($customer->sop->onboard_delivery ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ ($customer->sop->onboard_delivery ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-inline-row">
                                                    <div class="form-group">
                                                        <label>Quotes prior to instructions?</label>
                                                        <select name="quotes_prior_to_instructions" class="form-control">
                                                            <option></option>
                                                            <option value="Yes" {{ ($customer->sop->quotes_prior_to_instructions ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ ($customer->sop->quotes_prior_to_instructions ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Agreed rate?</label>
                                                        <select name="agreed_rate" class="form-control">
                                                            <option></option>
                                                            <option value="Yes" {{ ($customer->sop->agreed_rate ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ ($customer->sop->agreed_rate ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Invoicing</label>
                                                    <input type="text" name="invoicing_procedure" class="form-control" value="{{ $customer->sop->invoicing_procedure ?? '' }}">
                                                </div>
                                                <div class="form-group" style="width: 50%;">
                                                    <label>Pending entry?</label>
                                                    <select name="pending_entry" class="form-control">
                                                        <option></option>
                                                        <option value="Yes" {{ ($customer->sop->pending_entry ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ ($customer->sop->pending_entry ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Special pending routines</label>
                                                    <input type="text" name="special_pending_routines" class="form-control" value="{{ $customer->sop->special_pending_routines ?? '' }}">
                                                </div>
                                            </div>
                                            <!-- Column 2: Other procedures -->
                                            <div>
                                                <div class="form-section-title" style="margin-top: 0;">Other procedures</div>
                                                <div class="form-group">
                                                    <label>Other procedures/comments</label>
                                                    <textarea name="other_procedures_comments" class="form-control" style="height: 200px;">{{ $customer->sop->other_procedures_comments ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <!-- Column 3: Imported documents -->
                                            <div>
                                                <div class="form-section-title" style="margin-top: 0;">Imported documents</div>
                                                 <div class="upload-area" id="sop_drop_zone" style="cursor: pointer; position: relative;">
                                                     <p id="sop_text" style="font-size: 11px;">Drag files here or click to browse</p>
                                                     <i class="ti-upload" id="sop_icon"></i>
                                                 </div>
                                                 <div id="sop_file_list" style="margin-top: 10px;">
                                                     @foreach($customer->documents as $doc)
                                                         <div class="file-item">
                                                             <i class="ti-file"></i>
                                                             <a href="{{ $doc->fileUrl() }}" target="_blank" style="color: #01a9ac; text-decoration: none;">{{ $doc->file_name }}</a>
                                                             <span class="remove-file" data-id="{{ $doc->id }}"><i class="ti-trash"></i></span>
                                                         </div>
                                                     @endforeach
                                                 </div>
                                                 <input type="hidden" name="removed_documents" id="removed_documents" value="">
                                        </div>
                                    </div>
                                    </div>

                                     <div class="tab-pane" id="vessels" role="tabpanel">
                                        <div class="vessels-header">
                                            <div class="vessels-search-container">
                                                <span class="search-label">Search</span>
                                                <input type="text" id="vesselSearchInput" placeholder="type here">
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <button type="button" class="btn-vessel-export"><i class="ti-download"></i></button>
                                                <a href="{{ route('customers.vessels.create', $customer->id) }}" class="btn-vessel-add">Add vessel</a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table vessels-table" id="vesselsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Vessel name</th>
                                                        <th>Customer vessel code</th>
                                                        <th>IMO</th>
                                                        <th>Manager</th>
                                                        <th>Account manager</th>
                                                        <th>Status</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($customer->vessels as $vessel)
                                                    <tr>
                                                        <td><a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-link">{{ $vessel->vessel }}</a></td>
                                                        <td>{{ $vessel->customer_vessel_code }}</td>
                                                        <td>{{ $vessel->vessel_imo }}</td>
                                                        <td>{{ $vessel->manager }}</td>
                                                        <td>{{ $vessel->account_manager }}</td>
                                                        <td>
                                                            @if($vessel->inactive_vessel)
                                                                <span class="label label-danger">Inactive</span>
                                                            @elseif($vessel->sanction_blocked || $vessel->financially_blocked)
                                                                <span class="label label-warning">Blocked</span>
                                                            @else
                                                                <span class="label label-success">Active</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right;"><i class="ti-pencil cursor-pointer" style="color: #ccc;"></i></td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No vessels found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="notification-settings" role="tabpanel">
                                        <div class="sop-grid">
                                            <div>
                                                <div class="form-section-title">Stock items to stock</div>
                                                <div class="form-group">
                                                    <label>Notify when stock items come to stock</label>
                                                    <div class="input-with-icon">
                                                        <input type="text" name="notify_stock_items" class="form-control" style="background: #fcfcfc;" value="{{ $customer->notificationSetting->notify_stock_items ?? '' }}">
                                                        <span class="input-icon-btn"><i class="ti-more-alt"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-section-title">First mile management</div>
                                                <div class="custom-checkbox-group">
                                                    <input type="checkbox" name="send_automatic_first_mile_email" {{ ($customer->notificationSetting->send_automatic_first_mile_email ?? false) ? 'checked' : '' }}>
                                                    <span>Send automatic first-mile email to supplier</span>
                                                </div>
                                                <div class="form-group">
                                                    <label>Notify when first mile email is sent to supplier:</label>
                                                    <input type="text" name="notify_first_mile_email_sent" class="form-control" value="{{ $customer->notificationSetting->notify_first_mile_email_sent ?? '' }}">
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-section-title">Free storage period</div>
                                                <div class="form-inline-row">
                                                    <div class="form-group"><label>Days limit</label><input type="text" name="shipping_free_storage_days" class="form-control" value="{{ $customer->notificationSetting->shipping_free_storage_days ?? '' }}"></div>
                                                    <div class="form-group"><label>Weight limit (kg)</label><input type="text" name="shipping_free_storage_weight" class="form-control" value="{{ $customer->notificationSetting->shipping_free_storage_weight ?? '' }}"></div>
                                                    <div class="form-group"><label>Volume limit (CBM)</label><input type="text" name="shipping_free_storage_volume" class="form-control" value="{{ $customer->notificationSetting->shipping_free_storage_volume ?? '' }}"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Notify when free storage period exceeds</label>
                                                    <input type="text" name="notify_free_storage_exceeded" class="form-control" value="{{ $customer->notificationSetting->notify_free_storage_exceeded ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end tab-content -->
                            </div> <!-- end card -->

                            <!-- Footer Actions -->
                            <div class="page-footer-actions">
                                <button type="submit" id="customer-update-btn" class="btn-update-customer"
                                    data-saved-label="Update" data-dirty-label="Update" disabled>Update</button>
                                <a href="{{ route('customers.index') }}" class="link-cancel-customer">Cancel</a>
                                <div class="audit-meta">
                                    @include('partials.audit-info', ['record' => $customer])
                                </div>
                            </div>

                            <input type="file" name="logo" id="logo_input" accept="image/*" style="display:none;" form="customerForm">
                            <input type="file" name="sop_documents[]" id="sop_documents_input" multiple style="display:none;" form="customerForm">
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end pcoded-container -->
</div> <!-- end pcoded -->

     <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

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
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- jQuery Validation js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
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
            $('.select2-field, .select2-office-user').on('change', function() {
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
                    sales_manager: "required",
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
                    sales_manager: "Please select sales manager",
                    main_account_manager: "Please select account manager"
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-field') || element.hasClass('select2-office-user')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-with-icon').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function(element) {
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
            $("#vesselSearchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#vesselsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Real-time search for Contacts table
            $("#contactSearchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#contactsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Delete Contact via AJAX
            $(document).on('click', '.delete-contact', function() {
                var contactId = $(this).data('id');
                var row = $(this).closest('tr');

                if (confirm('Are you sure you want to delete this contact?')) {
                    $.ajax({
                        url: '/contacts/' + contactId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(function() {
                                    $(this).remove();
                                });
                            }
                        },
                        error: function() {
                            alert('An error occurred while deleting the contact.');
                        }
                    });
                }
            });

            // =============================================
            // Company Logo Upload (click + drag & drop)
            // =============================================
            var $logoZone   = $('#logo_drop_zone');
            var $logoInput  = $('#logo_input');
            var $logoPreview = $('#logo_preview');
            var $logoText   = $('#logo_text');
            var $logoIcon   = $('#logo_icon');

            $logoZone.on('click', function() { $logoInput.trigger('click'); });
            $logoInput.on('click', function(e) { e.stopPropagation(); });

            $logoInput.on('change', function() {
                var file = this.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    $logoPreview.attr('src', e.target.result).show();
                    $logoIcon.hide();
                    $logoText.text('Click to change logo');
                };
                reader.readAsDataURL(file);
            });

            $logoZone.on('dragover dragenter', function(e) {
                e.preventDefault(); e.stopPropagation();
                $(this).addClass('dragover');
            }).on('dragleave drop', function(e) {
                e.preventDefault(); e.stopPropagation();
                $(this).removeClass('dragover');
            }).on('drop', function(e) {
                var file = e.originalEvent.dataTransfer.files[0];
                if (!file || !file.type.startsWith('image/')) return;
                var dt = new DataTransfer();
                dt.items.add(file);
                $logoInput[0].files = dt.files;
                $logoInput.trigger('change');
            });

            // =============================================
            // SOP Documents Upload — AJAX (reliable cross-browser)
            // =============================================
            var $sopZone  = $('#sop_drop_zone');
            var $sopInput = $('#sop_documents_input');
            var $sopList  = $('#sop_file_list');
            var customerId = {{ $customer->id }};
            var uploadUrl  = '/customers/' + customerId + '/documents';
            var csrfToken  = $('meta[name="csrf-token"]').attr('content') ||
                             $('input[name="_token"]').first().val();

            function uploadFile(file) {
                var fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('file', file);

                var $item = $('<div class="file-item uploading">' +
                    '<i class="ti-file"></i>' +
                    '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#999;">Uploading ' + file.name + '...</span>' +
                    '</div>');
                $sopList.append($item);

                $.ajax({
                    url: uploadUrl,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $item.replaceWith(
                            '<div class="file-item" data-doc-id="' + res.id + '">' +
                            '<i class="ti-file"></i>' +
                            '<a href="' + res.file_url + '" target="_blank" style="color:#01a9ac;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + res.file_name + '</a>' +
                            '<span class="remove-file" data-id="' + res.id + '" style="margin-left:auto;color:#e74c3c;cursor:pointer;"><i class="ti-trash"></i></span>' +
                            '</div>'
                        );
                    },
                    error: function() {
                        $item.replaceWith(
                            '<div class="file-item" style="color:#e74c3c;">' +
                            '<i class="ti-alert"></i><span>Failed to upload ' + file.name + '</span>' +
                            '</div>'
                        );
                    }
                });
            }

            $sopZone.on('click', function() { $sopInput.trigger('click'); });
            $sopInput.on('click', function(e) { e.stopPropagation(); });

            $sopInput.on('change', function() {
                $.each(this.files, function(i, file) { uploadFile(file); });
                this.value = '';
            });

            $sopZone.on('dragover dragenter', function(e) {
                e.preventDefault(); e.stopPropagation();
                $(this).addClass('dragover');
            }).on('dragleave drop', function(e) {
                e.preventDefault(); e.stopPropagation();
                $(this).removeClass('dragover');
            }).on('drop', function(e) {
                var files = e.originalEvent.dataTransfer.files;
                $.each(files, function(i, file) { uploadFile(file); });
            });

            // Delete saved document via AJAX
            $sopList.on('click', '.remove-file', function() {
                var $btn  = $(this);
                var docId = $btn.data('id');
                if (!confirm('Delete this document?')) return;
                $.ajax({
                    url: '/customers/documents/' + docId,
                    type: 'POST',
                    data: { _token: csrfToken, _method: 'DELETE' },
                    success: function() { $btn.closest('.file-item').remove(); },
                    error:   function() { alert('Failed to delete document.'); }
                });
            });

        });
    </script>
@include('partials.unsaved-changes-guard', [
    'formSelector' => '#customerForm',
    'fallbackUrl' => route('customers.index'),
    'saveButtonSelector' => '#customer-update-btn',
])
@endsection
