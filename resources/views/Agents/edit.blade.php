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
    <style>
        /* Header Summary Bar */
        .edit-header-summary {
            display: flex;
            gap: 40px;
            padding: 15px 25px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .summary-label {
            font-size: 11px;
            color: #888;
        }

        .summary-value {
            font-size: 13px;
            font-weight: 700;
            color: #333;
        }

        /* Tabs Styling */
        .tabs-container {
            background: #e9ecef;
            padding: 0 15px;
            display: flex;
            border-bottom: 1px solid #ddd;
        }

        .tab-item {
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
        }

        .tab-item:hover {
            color: #1b5e6f;
        }

        .tab-item.active {
            color: #1b5e6f;
            border-bottom-color: #1b5e6f;
            background: #fff;
        }

        /* Form Layout Styling */
        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            padding: 25px;
            background: #fff;
        }

        .form-pillar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
            margin-bottom: 5px;
        }

        .form-group-custom {
            margin-bottom: 12px;
        }

        .form-label-custom {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }

        .form-input-custom {
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 2px 8px;
            font-size: 11px;
            color: #333;
            background: #fff;
        }

        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 5px 8px;
            font-size: 11px;
            color: #333;
            resize: none;
            background: #fff;
        }

        .form-input-readonly {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
            cursor: default;
        }

        .input-row {
            display: flex;
            gap: 12px;
        }

        .input-row>div {
            flex: 1;
        }

        .input-group-custom {
            display: flex;
            align-items: center;
        }

        .btn-input-append {
            height: 30px;
            background: #e9ecef;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 8px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 12px;
        }

        .form-input-custom.has-append {
            border-radius: 2px 0 0 2px;
        }

        /* Footer Styling */
        .form-footer {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 240px;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .form-footer-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-saved-custom {
            background: #e9ecef;
            color: #a0aec0;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            font-size: 12px;
            cursor: default;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            text-decoration: none;
            font-size: 12px;
        }

        /* Tab Visibility */
        .tab-content-custom {
            display: none;
        }

        .tab-content-custom.active {
            display: block;
        }

        /* SOP Tab Specifics */
        .drag-drop-area {
            border: 1px dashed #d1d5db;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 11px;
            margin-top: 10px;
            cursor: pointer;
            background: #fff;
        }

        .drag-drop-area i {
            display: block;
            font-size: 24px;
            color: #1b5e6f;
            margin-bottom: 8px;
        }

        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .doc-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .doc-name {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .doc-meta {
            font-size: 9px;
            color: #999;
        }

        .btn-delete-doc {
            background: none;
            border: none;
            color: #1b5e6f;
            font-size: 16px;
            padding: 0;
            cursor: pointer;
        }

        /* Metadata Footer */
        .metadata-footer {
            margin-left: auto;
            text-align: right;
            font-size: 10px;
            color: #999;
            line-height: 1.4;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 0 !important;
        }

        .main-body .page-wrapper {
            padding: 0 !important;
        }

        .page-body {
            padding: 0 !important;
        }

        .card {
            margin: 0;
            border: none;
            box-shadow: none;
            margin-bottom: 80px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        /* Select2 overrides — white background, no teal */
        .select2-container--default .select2-selection--single {
            height: 30px !important;
            padding: 1px 8px;
            font-size: 11px;
            border: 1px solid #d1d5db !important;
            border-radius: 2px !important;
            background-color: #ffffff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #333 !important;
            padding-left: 0 !important;
            background: transparent !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            background: transparent !important;
        }

        .select2-container--default .select2-results__option--highlighted,
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #e9ecef !important;
            color: #333 !important;
        }

        .flag-icon {
            margin-right: 6px;
            width: 16px;
            height: 11px;
            display: inline-block;
            vertical-align: middle;
            border-radius: 2px;
        }
    </style>
@endsection

@section('content')
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            @include('layouts.top-menu')
            @include('layouts.left-menu')
            <!-- Page-body start -->
            <br>
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- Main-body start -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-body">
                                <!-- Header Summary Bar -->
                                <div class="edit-header-summary mt-5">
                                    <div class="summary-item">
                                        <span class="summary-label">Company Id</span>
                                        <span class="summary-value">96696</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Used as consignees</span>
                                        <span class="summary-value">49</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Inactive</span>
                                        <span class="summary-value">No</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Blocked</span>
                                        <span class="summary-value">No</span>
                                    </div>
                                </div>

                                <!-- Tab Navigation -->
                                <div class="tabs-container">
                                    <a class="tab-item active" data-tab="agent-details">Agent details</a>
                                    <a class="tab-item" data-tab="billing-details">Billing details</a>
                                    <a class="tab-item" data-tab="sop">SOP</a>
                                    <a class="tab-item" data-tab="pricing">Pricing</a>
                                    <a class="tab-item" data-tab="agent-users">Agent users</a>
                                    <a class="tab-item" data-tab="contacts">Contacts</a>
                                    <a class="tab-item" data-tab="email-settings">Email settings</a>
                                    <a class="tab-item" data-tab="scan-gun">Scan gun</a>
                                </div>

                                <!-- Form Content -->
                                <form id="agentEditForm" action="{{ route('agents.update', $agent->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'agent-details') }}">
                                    <div class="card">
                                        <!-- Agent Details Tab -->
                                        <div id="agent-details" class="tab-content-custom active">
                                            <div class="form-pillar-container">
                                                <!-- Column 1: Agent information -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Agent information</div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Agent name</label>
                                                        <div class="input-group-custom">
                                                            <input type="text" name="agent_name"
                                                                class="form-input-custom has-append"
                                                                value="{{ old('agent_name', $agent->agent_name) }}">
                                                            <button class="btn-input-append"><i
                                                                    class="ti-more-alt"></i></button>
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Company id</label>
                                                        <input type="text" name="company_id"
                                                            class="form-input-custom form-input-readonly"
                                                            value="{{ old('company_id', $agent->company_id) }}" readonly>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Code</label>
                                                            <input type="text" name="code" class="form-input-custom"
                                                                value="{{ old('code', $agent->code) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Code description</label>
                                                            <input type="text" name="code_description"
                                                                class="form-input-custom"
                                                                value="{{ old('code_description', $agent->code_description) }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Phone number (with country
                                                            code)</label>
                                                        <input type="text" name="phone" class="form-input-custom"
                                                            value="{{ old('phone', $agent->phone) }}">
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Email</label>
                                                        <input type="text" name="email" class="form-input-custom"
                                                            value="{{ old('email', $agent->email) }}"
                                                            placeholder="email@example.com; email2@example.com">
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Remarks</label>
                                                        <textarea name="remarks" class="form-textarea-custom"
                                                            rows="3">{{ old('remarks', $agent->remarks) }}</textarea>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Special considerations for
                                                            destination</label>
                                                        <textarea name="special_considerations" class="form-textarea-custom"
                                                            rows="3">{{ old('special_considerations', $agent->special_considerations) }}</textarea>
                                                    </div>

                                                    <div class="form-group-custom"
                                                        style="display: flex; gap: 8px; align-items: flex-start;">
                                                        <input type="checkbox" name="show_pre_alert" value="1"
                                                            style="margin-top: 3px;" {{ old('show_pre_alert', $agent->show_pre_alert) ? 'checked' : '' }}>
                                                        <label class="form-label-custom">Show pre-alert warning when items
                                                            in
                                                            shipment are not scanned</label>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Agent address & Office address -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Agent address</div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Agent address</label>
                                                        <textarea name="agent_address" class="form-textarea-custom"
                                                            rows="3">{{ old('agent_address', $agent->agent_address) }}</textarea>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom" style="flex: 2;">
                                                            <label class="form-label-custom">City</label>
                                                            <input type="text" name="city" class="form-input-custom"
                                                                value="{{ old('city', $agent->city) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">District/state</label>
                                                            <input type="text" name="district_state"
                                                                class="form-input-custom"
                                                                value="{{ old('district_state', $agent->district_state) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Zip code</label>
                                                            <input type="text" name="zip_code" class="form-input-custom"
                                                                value="{{ old('zip_code', $agent->zip_code) }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Country</label>
                                                        <select name="country_id" class="select2-country-edit">
                                                            <option value="">Select Country</option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->iso_code }}" {{ old('country_id', $agent->country_id) == $country->id ? 'selected' : '' }}>
                                                                    {{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Port code</label>
                                                        <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                            <option value=""></option>
                                                            @if (old('port_code', $agent->port_code))
                                                                <option value="{{ old('port_code', $agent->port_code) }}" selected>
                                                                    {{ old('port_code', $agent->port_code) }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="form-section-header" style="margin-top: 15px;">Office
                                                        address
                                                        (Optional)</div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Office address</label>
                                                        <textarea name="office_address" class="form-textarea-custom"
                                                            rows="3">{{ old('office_address', $agent->office_address) }}</textarea>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom" style="flex: 2;">
                                                            <label class="form-label-custom">City</label>
                                                            <input type="text" name="office_city" class="form-input-custom"
                                                                value="{{ old('office_city', $agent->office_city) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">District/state</label>
                                                            <input type="text" name="office_district_state"
                                                                class="form-input-custom"
                                                                value="{{ old('office_district_state', $agent->office_district_state) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Zip code</label>
                                                            <input type="text" name="office_zip_code"
                                                                class="form-input-custom"
                                                                value="{{ old('office_zip_code', $agent->office_zip_code) }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Country</label>
                                                        <select name="office_country_id" class="select2-country-edit">
                                                            <option value="">Select Country</option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->iso_code }}" {{ old('office_country_id', $agent->office_country_id) == $country->id ? 'selected' : '' }}>
                                                                    {{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 3: Agent details -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Agent details</div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">EORI number</label>
                                                        <input type="text" name="eori_number" class="form-input-custom"
                                                            value="{{ old('eori_number', $agent->eori_number) }}">
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">UN/LOCODE</label>
                                                            <input type="text" name="un_locode" class="form-input-custom"
                                                                value="{{ old('un_locode', $agent->un_locode) }}">
                                                        </div>
                                                        <div class="form-group-custom" style="flex: 1.5;">
                                                            <label class="form-label-custom">Agent type</label>
                                                            <select name="agent_type" class="select2-agent-type-edit">
                                                                <option value=""></option>
                                                                <option value="contracted_agent" {{ old('agent_type', $agent->agent_type) == 'contracted_agent' ? 'selected' : '' }}>Contracted agent</option>
                                                                <option value="main_agent" {{ old('agent_type', $agent->agent_type) == 'main_agent' ? 'selected' : '' }}>
                                                                    Main agent</option>
                                                                <option value="sub_agent" {{ old('agent_type', $agent->agent_type) == 'sub_agent' ? 'selected' : '' }}>
                                                                    Sub agent</option>
                                                                <option value="3pl_japan_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_japan_supplier' ? 'selected' : '' }}>3PL Japan supplier</option>
                                                                <option value="3pl_greece_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_greece_supplier' ? 'selected' : '' }}>3PL Greece supplier</option>
                                                                <option value="mt_bergen_agency" {{ old('agent_type', $agent->agent_type) == 'mt_bergen_agency' ? 'selected' : '' }}>MT Bergen Agency supplier</option>
                                                                <option value="mt_singapore_projects" {{ old('agent_type', $agent->agent_type) == 'mt_singapore_projects' ? 'selected' : '' }}>MT Singapore Projects supplier
                                                                </option>
                                                                <option value="mt_benelux_supplier" {{ old('agent_type', $agent->agent_type) == 'mt_benelux_supplier' ? 'selected' : '' }}>MT Benelux supplier</option>
                                                                <option value="door_to_deck_agent" {{ old('agent_type', $agent->agent_type) == 'door_to_deck_agent' ? 'selected' : '' }}>Door to Deck agent</option>
                                                                <option value="mt_singapore_agency" {{ old('agent_type', $agent->agent_type) == 'mt_singapore_agency' ? 'selected' : '' }}>MT Singapore Agency supplier</option>
                                                                <option value="mt_norway_supplier" {{ old('agent_type', $agent->agent_type) == 'mt_norway_supplier' ? 'selected' : '' }}>MT Norway supplier</option>
                                                                <option value="3pl_general_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_general_supplier' ? 'selected' : '' }}>3PL General supplier</option>
                                                                <option value="external_entity" {{ old('agent_type', $agent->agent_type) == 'external_entity' ? 'selected' : '' }}>External entity</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div> <!-- End #agent-details -->

                                        <!-- Billing Details Tab -->
                                        <div id="billing-details" class="tab-content-custom">
                                            <div class="form-pillar-container">
                                                <!-- Column 1: Invoicing details -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Invoicing details</div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Invoicing name</label>
                                                        <input type="text" class="form-input-custom" name="invoicing_name"
                                                            value="{{ old('invoicing_name', $agent->invoicing_name) }}">
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Address</label>
                                                        <textarea class="form-textarea-custom" name="billing_address"
                                                            rows="3">{{ old('billing_address', $agent->billing_address) }}</textarea>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom" style="flex: 2;">
                                                            <label class="form-label-custom">City</label>
                                                            <div class="input-group-custom">
                                                                <input type="text" class="form-input-custom has-append"
                                                                    name="billing_city"
                                                                    value="{{ old('billing_city', $agent->billing_city) }}">
                                                                <button type="button" class="btn-input-append"><i
                                                                        class="ti-more-alt"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">District</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="billing_district_state"
                                                                value="{{ old('billing_district_state', $agent->billing_district_state) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Zip</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="billing_zip_code"
                                                                value="{{ old('billing_zip_code', $agent->billing_zip_code) }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Country</label>
                                                        <select class="form-input-custom select2-country-edit"
                                                            name="billing_country_id" style="width: 100%;">
                                                            <option value=""></option>
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    data-flag="{{ $country->iso_code }}" {{ old('billing_country_id', $agent->billing_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">E-mails for invoicing</label>
                                                        <input type="text" class="form-input-custom" name="invoicing_emails"
                                                            value="{{ old('invoicing_emails', $agent->invoicing_emails) }}">
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">E-mails for invoicing (CC)</label>
                                                        <input type="text" class="form-input-custom"
                                                            name="invoicing_emails_cc"
                                                            value="{{ old('invoicing_emails_cc', $agent->invoicing_emails_cc) }}">
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">VAT number</label>
                                                            <input type="text" class="form-input-custom" name="vat_number"
                                                                value="{{ old('vat_number', $agent->vat_number ?? '01365110996') }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Invoicing Frequency</label>
                                                            <select class="form-input-custom" name="invoicing_frequency">
                                                                <option value=""></option>
                                                                <option value="monthly" {{ old('invoicing_frequency', $agent->invoicing_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                                <option value="per shipment" {{ old('invoicing_frequency', $agent->invoicing_frequency) == 'per shipment' ? 'selected' : '' }}>Per Shipment</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="input-row" style="align-items: flex-end;">
                                                        <div class="form-group-custom"
                                                            style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                                                            <input type="checkbox" name="applies_to_rebate" value="1" {{ old('applies_to_rebate', $agent->applies_to_rebate) ? 'checked' : '' }}>
                                                            <label class="form-label-custom"
                                                                style="margin-bottom: 0;">Applies
                                                                to rebate</label>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Rebate percentage</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="rebate_percentage"
                                                                value="{{ old('rebate_percentage', $agent->rebate_percentage) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Other billing sections -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Outgoing invoices to agent</div>
                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Currency</label>
                                                            <select class="form-input-custom select2-currency-edit"
                                                                name="outgoing_currency" style="width: 100%;">
                                                                <option value=""></option>
                                                                @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                                                                    <option value="{{ $currency }}" {{ old('outgoing_currency', $agent->outgoing_currency ?? 'EUR') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Payment terms</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="outgoing_payment_terms"
                                                                value="{{ old('outgoing_payment_terms', $agent->outgoing_payment_terms) }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-section-header" style="margin-top: 15px;">Incoming
                                                        invoices
                                                        from agent</div>
                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Currency</label>
                                                            <select class="form-input-custom select2-currency-edit"
                                                                name="incoming_currency" style="width: 100%;">
                                                                <option value=""></option>
                                                                @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                                                                    <option value="{{ $currency }}" {{ old('incoming_currency', $agent->incoming_currency ?? 'EUR') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Payment terms</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="incoming_payment_terms"
                                                                value="{{ old('incoming_payment_terms', $agent->incoming_payment_terms ?? '60') }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-section-header"
                                                        style="margin-top: 15px; margin-bottom: 10px;">Billing exceptions
                                                    </div>

                                                    <div class="billing-exceptions-wrapper">
                                                        <table class="table mb-2" id="billing-exceptions-table"
                                                            style="font-size: 11px; width: 100%; border-collapse: collapse; {{ $agent->billingExceptions->count() > 0 ? '' : 'display: none;' }}">
                                                            <thead style="background: #e9ecef;">
                                                                <tr>
                                                                    <th
                                                                        style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                                                        Billing office</th>
                                                                    <th
                                                                        style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                                                        Invoice to agent</th>
                                                                    <th
                                                                        style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                                                        Currency</th>
                                                                    <th
                                                                        style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                                                        Paym. terms</th>
                                                                    <th
                                                                        style="border: 1px solid #dee2e6; background: #e9ecef; width: 30px;">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($agent->billingExceptions as $exception)
                                                                    <tr class="billing-exception-row">
                                                                        <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                            <input type="text"
                                                                                style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;"
                                                                                name="billing_exceptions[office][]"
                                                                                value="{{ $exception->office }}">
                                                                        </td>
                                                                        <td
                                                                            style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                            <select
                                                                                style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;"
                                                                                name="billing_exceptions[invoice_to_agent][]">
                                                                                <option value="incoming" {{ $exception->invoice_to_agent == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                                                                <option value="outgoing" {{ $exception->invoice_to_agent == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                                                                <option value="both" {{ $exception->invoice_to_agent == 'both' ? 'selected' : '' }}>Both</option>
                                                                            </select>
                                                                            <i class="ti-angle-down"
                                                                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #666; font-size: 9px; pointer-events: none;"></i>
                                                                        </td>
                                                                        <td
                                                                            style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                            <select
                                                                                class="form-input-custom select2-currency-edit"
                                                                                style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;"
                                                                                name="billing_exceptions[currency][]">
                                                                                <option value=""></option>
                                                                                @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                                                                                    <option value="{{ $currency }}" {{ $exception->currency == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                            <input type="text"
                                                                                style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;"
                                                                                name="billing_exceptions[payment_terms][]"
                                                                                value="{{ $exception->payment_terms }}">
                                                                        </td>
                                                                        <td
                                                                            style="padding: 6px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">
                                                                            <button type="button" class="btn-remove-exception"
                                                                                style="background: none; border: none; color: #1b5e6f; cursor: pointer; padding: 0;"><i
                                                                                    class="ti-trash"
                                                                                    style="font-size: 13px;"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        <div
                                                            style="display: flex; justify-content: flex-end; margin-top: 10px;">
                                                            <button type="button" id="btn-add-billing-exception"
                                                                style="background: #fff; border: 1px solid #d1d5db; color: #1b5e6f; font-size: 11px; padding: 4px 35px; border-radius: 4px; cursor: pointer;">Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- SOP Tab -->
                                        <div id="sop" class="tab-content-custom">
                                            <div class="form-pillar-container"
                                                style="grid-template-columns: 1fr 1fr; gap: 80px;">
                                                <!-- Column 1: SOP details -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">SOP details</div>

                                                    <div class="input-row" style="margin-top: 10px; margin-bottom: 15px;">
                                                        <div class="form-group-custom"
                                                            style="display: flex; gap: 8px; align-items: center;">
                                                            <input type="checkbox" id="coc_signed" name="coc_signed"
                                                                value="1" {{ old('coc_signed', $agent->coc_signed) ? 'checked' : '' }}>
                                                            <label for="coc_signed" class="form-label-custom"
                                                                style="margin-bottom: 0;">Code of Conduct signed</label>
                                                        </div>
                                                        <div class="form-group-custom"
                                                            style="display: flex; gap: 8px; align-items: center;">
                                                            <input type="checkbox" id="sop_implemented"
                                                                name="sop_implemented" value="1" {{ old('sop_implemented', $agent->sop_implemented) ? 'checked' : '' }}>
                                                            <label for="sop_implemented" class="form-label-custom"
                                                                style="margin-bottom: 0;">SOP implemented</label>
                                                        </div>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Code of Conduct signed
                                                                date</label>
                                                            <input type="date" class="form-input-custom"
                                                                name="coc_signed_date"
                                                                value="{{ old('coc_signed_date', $agent->coc_signed_date ? $agent->coc_signed_date->format('Y-m-d') : '') }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Responsible manager</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="responsible_manager"
                                                                value="{{ old('responsible_manager', $agent->responsible_manager) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Imported documents -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Imported documents</div>

                                                    <div class="document-list">
                                                        @foreach($agent->documents->where('section', 'sop') as $document)
                                                            <div class="document-item" style="margin-top: 10px;">
                                                                <div class="doc-info">
                                                                    <span class="doc-name"><a href="{{ $document->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $document->filename }}</a></span>
                                                                    <span class="doc-meta">Uploaded
                                                                        {{ $document->created_at->format('d.m.Y H:i') }}</span>
                                                                </div>
                                                                <button type="button" class="btn-delete-doc"
                                                                    onclick="deleteAgentDocument({{ $document->id }})"><i
                                                                        class="ti-trash"></i></button>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="drag-drop-area"
                                                        onclick="document.getElementById('sop_file_upload').click()">
                                                        Drag files here or click to browse
                                                        <i class="ti-export"></i>
                                                        <input type="file" id="sop_file_upload" name="sop_documents[]"
                                                            multiple style="display: none;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Pricing Tab -->
                                        <div id="pricing" class="tab-content-custom">
                                            <div class="form-pillar-container"
                                                style="grid-template-columns: 1fr 1fr; gap: 80px;">
                                                <!-- Column 1: Pricing details -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Pricing details</div>

                                                    <div class="input-row" style="margin-top: 10px; margin-bottom: 15px;">
                                                        <div class="form-group-custom"
                                                            style="display: flex; gap: 8px; align-items: center;">
                                                            <input type="checkbox" id="calculate_sell_rates"
                                                                name="calculate_sell_rates" value="1" {{ old('calculate_sell_rates', $agent->calculate_sell_rates) ? 'checked' : '' }}>
                                                            <label for="calculate_sell_rates" class="form-label-custom"
                                                                style="margin-bottom: 0;">Calculate sell rates by
                                                                formula</label>
                                                        </div>
                                                        <div class="form-group-custom" style="flex: 1.5;">
                                                            <label class="form-label-custom">Agreement type</label>
                                                            <select class="form-input-custom" name="agreement_type">
                                                                <option value="framework" {{ old('agreement_type', $agent->agreement_type) == 'framework' ? 'selected' : '' }}>Framework</option>
                                                                <option value="price_sheet" {{ old('agreement_type', $agent->agreement_type) == 'price_sheet' ? 'selected' : '' }}>Price Sheet</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="input-row">
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Purchase Rate</label>
                                                            <input type="text" class="form-input-custom"
                                                                name="purchase_rate"
                                                                value="{{ old('purchase_rate', $agent->purchase_rate) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Sell Rate</label>
                                                            <input type="text" class="form-input-custom" name="sell_rate"
                                                                value="{{ old('sell_rate', $agent->sell_rate) }}">
                                                        </div>
                                                        <div class="form-group-custom">
                                                            <label class="form-label-custom">Profit</label>
                                                            <input type="text" class="form-input-custom" name="profit"
                                                                value="{{ old('profit', $agent->profit) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Imported documents -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Imported documents</div>

                                                    <div class="document-list">
                                                        @foreach($agent->documents->where('section', 'pricing') as $document)
                                                            <div class="document-item" style="margin-top: 10px;">
                                                                <div class="doc-info">
                                                                    <span class="doc-name"><a href="{{ $document->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $document->filename }}</a></span>
                                                                    <span class="doc-meta">Uploaded
                                                                        {{ $document->created_at->format('d.m.Y H:i') }}</span>
                                                                </div>
                                                                <button type="button" class="btn-delete-doc"
                                                                    onclick="deleteAgentDocument({{ $document->id }})"><i
                                                                        class="ti-trash"></i></button>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="drag-drop-area"
                                                        onclick="document.getElementById('pricing_file_upload').click()">
                                                        Drag files here or click to browse
                                                        <i class="ti-export"></i>
                                                        <input type="file" id="pricing_file_upload"
                                                            name="pricing_documents[]" multiple style="display: none;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="agent-users" class="tab-content-custom">
                                            <div style="display: flex; justify-content: flex-end; margin: 10px;">
                                                <a href="{{ route('agents.users.create', $agent->id) }}"
                                                    class="btn btn-primary"
                                                    style="background-color: #1b5e6f; border-color: #1b5e6f; font-size: 11px; padding: 6px 15px;">Add
                                                    User</a>
                                            </div>
                                            <div class="table-responsive"
                                                style="background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                <table class="table table-hover mb-0" style="font-size: 11px;">
                                                    <thead style="background: #f8fafd;">
                                                        <tr>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Name</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Email</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Phone number</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Description</th>
                                                            <th style="border-bottom: 2px solid #eef2f7;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($agent->agentUsers as $user)
                                                            <tr>
                                                                <td><a
                                                                        href="{{ route('agents.users.edit', $user->id) }}">{{ $user->name }}</a>
                                                                </td>
                                                                <td>{{ $user->email }}</td>
                                                                <td>{{ $user->phone_number }}</td>
                                                                <td>{{ $user->description }}</td>
                                                                <td class="text-right">
                                                                    <a href="{{ route('agents.users.edit', $user->id) }}"
                                                                        style="color: #1b5e6f; margin-right: 10px;"><i
                                                                            class="ti-pencil"></i></a>
                                                                    <button type="button"
                                                                        style="background: none; border: none; color: #ff5252; cursor: pointer; padding: 0;"
                                                                        onclick="deleteAgentUser({{ $user->id }})">
                                                                        <i class="ti-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Contacts Tab -->
                                        <div id="contacts" class="tab-content-custom">
                                            <div style="display: flex; justify-content: flex-end; margin: 10px;">
                                                <a href="{{ route('agents.contacts.create', $agent->id) }}"
                                                    class="btn btn-primary"
                                                    style="background-color: #1b5e6f; border-color: #1b5e6f; font-size: 11px; padding: 6px 15px;">Add
                                                    Contact</a>
                                            </div>
                                            <div class="table-responsive"
                                                style="background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                <table class="table table-hover mb-0" style="font-size: 11px;">
                                                    <thead style="background: #f8fafd;">
                                                        <tr>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Name</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Email</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Phone number</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Description</th>
                                                            <th
                                                                style="color: #1b5e6f; font-weight: 600; border-bottom: 2px solid #eef2f7;">
                                                                Main contact</th>
                                                            <th style="border-bottom: 2px solid #eef2f7;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($agent->contacts as $contact)
                                                            <tr>
                                                                <td><a
                                                                        href="{{ route('agents.contacts.edit', $contact->id) }}">{{ $contact->name }}</a>
                                                                </td>
                                                                <td>{{ $contact->email }}</td>
                                                                <td>{{ $contact->phone_number }}</td>
                                                                <td>{{ $contact->description }}</td>
                                                                <td class="text-center">
                                                                    @if($contact->is_main_contact)
                                                                        <i class="ti-check" style="color: #008080;"></i>
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    <a href="{{ route('agents.contacts.edit', $contact->id) }}"
                                                                        style="color: #1b5e6f; margin-right: 10px;"><i
                                                                            class="ti-pencil"></i></a>
                                                                    <button type="button"
                                                                        style="background: none; border: none; color: #ff5252; cursor: pointer; padding: 0;"
                                                                        onclick="deleteAgentContact({{ $contact->id }})">
                                                                        <i class="ti-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Email settings Tab -->
                                        <div id="email-settings" class="tab-content-custom">
                                            <div class="form-pillar-container">
                                                <!-- Column 1: Export email settings -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Export email settings</div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Select services to add export
                                                            emails</label>
                                                        <div class="input-group-custom">
                                                            <div style="position: relative; width: 100%;">
                                                                <select class="form-input-custom"
                                                                    name="export_email_services"
                                                                    style="padding-right: 30px;">
                                                                    <option value=""></option>
                                                                    <option value="airfreight" {{ old('export_email_services', $agent->export_email_services) == 'airfreight' ? 'selected' : '' }}>Airfreight</option>
                                                                    <option value="seafreight" {{ old('export_email_services', $agent->export_email_services) == 'seafreight' ? 'selected' : '' }}>Seafreight</option>
                                                                    <option value="courier" {{ old('export_email_services', $agent->export_email_services) == 'courier' ? 'selected' : '' }}>Courier</option>
                                                                    <option value="onboarding_delivery" {{ old('export_email_services', $agent->export_email_services) == 'onboarding_delivery' ? 'selected' : '' }}>Onboarding delivery</option>
                                                                    <option value="release" {{ old('export_email_services', $agent->export_email_services) == 'release' ? 'selected' : '' }}>Release</option>
                                                                    <option value="truck" {{ old('export_email_services', $agent->export_email_services) == 'truck' ? 'selected' : '' }}>Truck</option>
                                                                    <option value="hand_carry" {{ old('export_email_services', $agent->export_email_services) == 'hand_carry' ? 'selected' : '' }}>Hand carry</option>
                                                                </select>
                                                                <div
                                                                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 5px; pointer-events: none;">
                                                                    <i class="ti-more-alt"
                                                                        style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                                                                    <i class="ti-angle-down"
                                                                        style="color: #999; font-size: 10px;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 2: Import email settings -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Import email settings</div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Select services to add import
                                                            emails</label>
                                                        <div style="position: relative; width: 100%;">
                                                            <select class="form-input-custom" name="import_email_services">
                                                                <option value=""></option>
                                                                <option value="airfreight" {{ old('export_email_services', $agent->export_email_services) == 'airfreight' ? 'selected' : '' }}>Airfreight</option>
                                                                <option value="seafreight" {{ old('export_email_services', $agent->export_email_services) == 'seafreight' ? 'selected' : '' }}>Seafreight</option>
                                                                <option value="courier" {{ old('export_email_services', $agent->export_email_services) == 'courier' ? 'selected' : '' }}>Courier</option>
                                                                <option value="onboarding_delivery" {{ old('export_email_services', $agent->export_email_services) == 'onboarding_delivery' ? 'selected' : '' }}>Onboarding delivery</option>
                                                                <option value="release" {{ old('export_email_services', $agent->export_email_services) == 'release' ? 'selected' : '' }}>Release</option>
                                                                <option value="truck" {{ old('export_email_services', $agent->export_email_services) == 'truck' ? 'selected' : '' }}>Truck</option>
                                                                <option value="hand_carry" {{ old('export_email_services', $agent->export_email_services) == 'hand_carry' ? 'selected' : '' }}>Hand carry</option>
                                                            </select>
                                                            <i class="ti-angle-down"
                                                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #999; font-size: 10px; pointer-events: none;"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Column 3: Other email settings -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Other email settings</div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Send "Status changed" emails
                                                            to</label>
                                                        <input type="text" class="form-input-custom"
                                                            name="status_changed_emails"
                                                            value="{{ old('status_changed_emails', $agent->status_changed_emails) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Send "Stock item changed" emails
                                                            to</label>
                                                        <input type="text" class="form-input-custom"
                                                            name="stock_item_changed_emails"
                                                            value="{{ old('stock_item_changed_emails', $agent->stock_item_changed_emails) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Send quote requests emails
                                                            to</label>
                                                        <input type="text" class="form-input-custom"
                                                            name="quote_requests_emails"
                                                            value="{{ old('quote_requests_emails', $agent->quote_requests_emails) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Scan gun Tab -->
                                        <div id="scan-gun" class="tab-content-custom">
                                            <div class="form-pillar-container"
                                                style="grid-template-columns: 1fr; max-width: 400px;">
                                                <!-- Credentials Section -->
                                                <div class="form-pillar">
                                                    <div class="form-section-header">Credentials</div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Login</label>
                                                        <div style="position: relative; width: 300px;">
                                                            <input type="text" class="form-input-custom"
                                                                name="scangun_login"
                                                                value="{{ old('scangun_login', $agent->scangun_login) }}"
                                                                style="padding-right: 35px;">
                                                            <div
                                                                style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                                                <i class="ti-more-alt"
                                                                    style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group-custom"
                                                        style="display: flex; align-items: center; gap: 8px; margin-top: 15px; margin-bottom: 15px;">
                                                        <input type="checkbox" id="set_new_password" checked>
                                                        <label for="set_new_password" class="form-label-custom"
                                                            style="margin-bottom: 0;">Set a new password</label>
                                                    </div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Password</label>
                                                        <div style="position: relative; width: 300px;">
                                                            <input type="password" class="form-input-custom"
                                                                name="scangun_password"
                                                                value="{{ old('scangun_password', $agent->scangun_password) }}"
                                                                style="padding-right: 60px;">
                                                            <div
                                                                style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 8px;">
                                                                <i class="ti-more-alt"
                                                                    style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px; pointer-events: none;"></i>
                                                                <i class="ti-eye"
                                                                    style="color: #333; font-size: 14px; cursor: pointer; font-weight: bold;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Features Section -->
                                                <div class="form-pillar" style="margin-top: 20px;">
                                                    <div class="form-section-header">Features</div>

                                                    <div class="form-group-custom"
                                                        style="display: flex; align-items: center; gap: 8px;">
                                                        <input type="checkbox" id="enable_picture_taking"
                                                            name="scangun_enable_picture" value="1" {{ old('scangun_enable_picture', $agent->scangun_enable_picture) ? 'checked' : '' }}>
                                                        <label for="enable_picture_taking" class="form-label-custom"
                                                            style="margin-bottom: 0;">Enable picture taking in scangun
                                                            app</label>
                                                    </div>

                                                    <div class="form-group-custom"
                                                        style="display: flex; align-items: center; gap: 8px;">
                                                        <input type="checkbox" id="enable_detailed_shipment"
                                                            name="scangun_enable_detailed_shipment" value="1" {{ old('scangun_enable_detailed_shipment', $agent->scangun_enable_detailed_shipment) ? 'checked' : '' }}>
                                                        <label for="enable_detailed_shipment" class="form-label-custom"
                                                            style="margin-bottom: 0;">Enable detailed shipment out</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="form-footer">
                                            <div class="form-footer-actions">
                                                <button type="submit" class="btn-saved-custom"
                                                    style="background:#1b5e6f; color:#fff; cursor:pointer;">Save
                                                    Agent</button>
                                                <a href="{{ route('agents.index') }}" class="btn-cancel-custom">Cancel</a>
                                            </div>
                                            <div class="metadata-footer">
                                                @include('partials.audit-info', ['record' => $agent])
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Page-body end -->
                        </div>
                    </div>
                    <div id="styleSelector">

                    </div>
                </div>
            </div>
        </div>
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

    <script>
        $(document).ready(function () {
            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: false
            });

            // Initialize Select2 with flags for country dropdowns
            function formatCountry(country) {
                if (!country.id) return country.text;
                var flagCode = $(country.element).data('flag');
                if (!flagCode) return country.text;
                var flagUrl = "https://flagcdn.com/w20/" + flagCode.toLowerCase() + ".png";
                return $('<span><img src="' + flagUrl + '" class="flag-icon" /> ' + country.text + '</span>');
            }

            $('.select2-country-edit').select2({
                templateResult: formatCountry,
                templateSelection: formatCountry,
                width: '100%'
            });

            // Port code — searched dynamically from the ports table
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
                if (!port.id) return port.text;
                return port.code ? (port.code + (port.city ? ', ' + port.city : '')) : port.text;
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

            // Initialize Select2 for Agent Type
            $('.select2-agent-type-edit').select2({
                placeholder: 'Select agent type',
                allowClear: false,
                width: '100%'
            });

            // Initialize Select2 for Currency
            $('.select2-currency-edit').select2({
                placeholder: 'Select currency',
                allowClear: false,
                width: '100%'
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonWidth: '100%',
                maxHeight: 200,
                nonSelectedText: '',
                allSelectedText: '',
                nSelectedText: '',
                numberDisplayed: 0,
                buttonClass: 'btn btn-outline-teal btn-filter-toggle',
                templates: {
                    button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><i class="ti-filter"></i></button>'
                },
                onChange: function (option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function () {
                    toggleFilterVisibility();
                },
                onDeselectAll: function () {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function () {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    { val: 'Office Name', id: 'col-Office-Name' },
                    { val: 'Short Name', id: 'col-Short-Name' },
                    { val: 'City', id: 'col-City' },
                    { val: 'Country', id: 'col-Country' },
                    { val: 'Phone', id: 'col-Phone' },
                    { val: 'Email', id: 'col-Email' }
                ];

                allFilters.forEach(function (filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }

            // Initial call to set visibility state
            toggleFilterVisibility();

            $('#offices-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": false
            });

            // Tab switching logic (keeps URL hash + hidden field so update redirects restore the active tab)
            function activateAgentTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                return true;
            }

            $('.tab-item').on('click', function () {
                var tabId = $(this).data('tab');
                activateAgentTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateAgentTab(hashTab);
            }
            // Dynamic Billing Exceptions
            var currencyOptionsHtml = '<option value=""></option>';
            @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                currencyOptionsHtml += '<option value="{{ $currency }}">{{ $currency }}</option>';
            @endforeach

            $('#btn-add-billing-exception').on('click', function () {
                var table = $('#billing-exceptions-table');
                table.show(); // Ensure table is visible when a row is added

                var rowHtml = `
                                                                                                                                        <tr class="billing-exception-row">
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                                                                                                <input type="text" style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;" name="billing_exceptions[office][]">
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                                                                                                <select style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;" name="billing_exceptions[invoice_to_agent][]">
                                                                                                                                                    <option value="incoming">Incoming</option>
                                                                                                                                                    <option value="outgoing">Outgoing</option>
                                                                                                                                                    <option value="both">Both</option>
                                                                                                                                                </select>
                                                                                                                                                <i class="ti-angle-down" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #666; font-size: 9px; pointer-events: none;"></i>
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                                                                                                <select class="form-input-custom select2-currency-dynamic" style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;" name="billing_exceptions[currency][]">
                                                                                                                                                    ${currencyOptionsHtml}
                                                                                                                                                </select>
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                                                                                                <input type="text" style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;" name="billing_exceptions[payment_terms][]">
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 6px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">
                                                                                                                                                <button type="button" class="btn-remove-exception" style="background: none; border: none; color: #1b5e6f; cursor: pointer; padding: 0;"><i class="ti-trash" style="font-size: 13px;"></i></button>
                                                                                                                                            </td>
                                                                                                                                        </tr>
                                                                                                                                    `;
                var newRow = $(rowHtml);
                table.find('tbody').append(newRow);

                // Initialize Select2 for the dynamically added dropdown
                newRow.find('.select2-currency-dynamic').select2({
                    placeholder: 'Select currency',
                    allowClear: false,
                    width: '100%'
                });
            });

            $(document).on('click', '.btn-remove-exception', function () {
                $(this).closest('tr').remove();
                if ($('#billing-exceptions-table tbody tr').length === 0) {
                    $('#billing-exceptions-table').hide();
                }
            });

        });

        function deleteAgentUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/Agents/users/destroy/' + id;

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                var methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteAgentContact(id) {
            if (confirm('Are you sure you want to delete this contact?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/Agents/contacts/destroy/' + id;

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                var methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteAgentDocument(id) {
            if (confirm('Are you sure you want to delete this document?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/Agents/documents/' + id;

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                var methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#agentEditForm', 'fallbackUrl' => route('agents.index')])
@endsection