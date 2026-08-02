@extends('layouts.app')

@section('styles')
    <style>
        .main-body .page-wrapper {
            padding: 0;
        }

        .md-tabs .nav-item {
            width: calc(100% / 10);
            text-align: center;
        }

        .nav-tabs .slide {
            background: #01a9ac;
            width: calc(100% / 10);
            height: 4px;
            position: absolute;
            -webkit-transition: left 0.3s ease-out;
            transition: left 0.3s ease-out;
            bottom: 0;
        }

        label {
            font-size: 13px;
            color: #262626;
            font-weight: 500;
        }

        h5 {
            font-size: 14px !important;
            font-weight: bold !important;
        }

        .card .card-header .card-title {
            font-weight: 600;
            color: #1f4356;
            border-bottom: 2px solid #c8c8c8;
            padding: 7px 0px;
        }

        /* --- New Premium Form Styling --- */
        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            gap: 40px;
            padding: 30px;
            background: #fff;
        }

        .form-pillar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-section-header {
            font-size: 14px;
            font-weight: 700;
            color: #1b5e6f;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .form-group-custom {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-label-custom {
            font-size: 12px;
            font-weight: 500;
            color: #262626;
            margin-bottom: 0;
        }

        .form-input-custom {
            height: 32px;
            padding: 6px 12px;
            font-size: 13px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            width: 100%;
            outline: none;
            color: #374151;
        }

        .form-input-custom:focus {
            border-color: #01a9ac;
            box-shadow: 0 0 0 2px rgba(1, 169, 172, 0.1);
        }

        .form-input-readonly {
            background-color: #f9fafb;
            color: #6b7280;
            border-color: #e5e7eb;
        }

        .form-textarea-custom {
            padding: 10px 12px;
            font-size: 13px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            width: 100%;
            resize: vertical;
            outline: none;
            color: #374151;
        }

        .input-row {
            display: flex;
            gap: 12px;
        }

        .input-row .form-group-custom {
            flex: 1;
        }

        .input-group-custom {
            display: flex;
            position: relative;
        }

        .input-group-custom .form-input-custom {
            padding-right: 35px;
        }

        .form-select-custom {
            height: 32px;
            padding: 0 12px;
            font-size: 13px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            width: 100%;
            outline: none;
            color: #374151;
            background-color: #fff;
        }

        .form-select-custom:focus {
            border-color: #01a9ac;
            box-shadow: 0 0 0 2px rgba(1, 169, 172, 0.1);
        }

        .btn-input-append {
            position: absolute;
            right: 1px;
            top: 1px;
            height: 30px;
            width: 30px;
            background: #f3f4f6;
            border: none;
            border-left: 1px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
        }

        .btn-input-append:hover {
            background: #e5e7eb;
        }

        /* Summary Header Bar */
        .edit-header-summary {
            background: #fff;
            padding: 15px 25px;
            display: flex;
            gap: 40px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 0;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .summary-label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 500;
        }

        .summary-value {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        /* Summary Header Bar */
        .edit-header-summary {
            background: #fff;
            padding: 10px 20px;
            display: flex;
            gap: 30px;
            border-bottom: 1px solid #eee;
            margin-bottom: 0;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
        }

        .summary-value {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        /* Footer Styling */
        .form-footer {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 240px;
            /* Adjust based on sidebar width */
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-saved-custom {
            background-color: #1b5e6f;
            color: white;
            border: none;
            padding: 8px 30px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-cancel-custom:hover {
            text-decoration: underline;
        }

        .metadata-footer {
            padding: 20px 30px 80px 30px;
            /* Increased bottom padding for fixed footer */
            background: #fff;
            text-align: right;
            font-size: 11px;
            color: #9ca3af;
        }

        @media (max-width: 1199.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px;
            }
        }

        @media (max-width: 991.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-pillar {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .input-row {
                flex-direction: column;
                gap: 12px;
            }

            .input-row .form-group-custom,
            .input-row .form-group-custom[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
            }

            .form-group-custom input[style*="width: 50%"],
            .form-input-custom[style*="width: 50%"] {
                width: 100% !important;
            }

            .form-footer {
                left: 0 !important;
                right: 0 !important;
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .btn-saved-custom {
                width: 100%;
                text-align: center;
            }

            .btn-cancel-custom {
                width: 100%;
                text-align: center;
            }

            .metadata-footer {
                padding: 16px 12px 96px;
                text-align: left;
            }

            .form-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .form-group-custom[style*="flex-direction: row"] {
                align-items: flex-start !important;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .card-block {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
        }

        /* Validation Styling */
        .error-message {
            color: #d9534f;
            font-size: 11px;
            margin-top: 5px;
            font-weight: 500;
        }

        .form-input-custom.error {
            border-color: #d9534f !important;
        }

        /* Adjustments for hub details specific layout */
        .tab-content-custom {
            display: none;
        }

        .tab-content-custom.active {
            display: block;
        }

        /* SOP Tab Specific Styling */
        .upload-area {
            border: 1px dashed #d1d5db;
            padding: 35px;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
        }

        .upload-area:hover {
            background: #f9fafb;
            border-color: #01a9ac;
        }

        .upload-icon {
            font-size: 28px;
            color: #1b5e6f;
        }

        .upload-text {
            font-size: 13px;
            color: #4b5563;
        }

        .file-list-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 0;
            margin-bottom: 5px;
        }

        .file-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .file-name {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            max-width: 90%;
            line-height: 1.4;
        }

        .file-meta {
            font-size: 11px;
            color: #9ca3af;
        }

        .btn-delete-file {
            color: #3b82f6;
            cursor: pointer;
            font-size: 18px;
        }

        /* Hub Users Tab Styling */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .custom-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
        }

        .empty-state-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 100px 0;
            color: #9ca3af;
        }

        .empty-state-icon {
            font-size: 24px;
            background: #f3f4f6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .empty-state-text {
            font-size: 13px;
        }

        /* Contacts Tab Specific */
        .custom-table td {
            padding: 12px 15px;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .table-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .table-link:hover {
            text-decoration: underline;
        }

        .btn-action-pencil {
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-action-pencil:hover {
            color: #4b5563;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        /* Select2 overrides — white background, no teal */
        .select2-container--default .select2-selection--single {
            height: 32px !important;
            padding: 2px 12px;
            font-size: 13px;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            background-color: #ffffff !important;
            background: #ffffff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #374151 !important;
            padding-left: 0 !important;
            background: transparent !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
            background: transparent !important;
        }

        /* Fix teal highlighted option in dropdown */
        .select2-container--default .select2-results__option--highlighted,
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #e9ecef !important;
            color: #374151 !important;
        }

        .flag-icon {
            margin-right: 8px;
            width: 18px;
            height: 12px;
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
                            <!-- Page-header start -->
                            <!-- Page-header end -->

                            <!-- Page-body start -->
                            <div class="page-body">
                                <!-- Base Style - Compact start -->
                                <form id="agentForm" action="{{ route('agents.store') }}" method="POST">
                                    @csrf
                                    <div class="card mt-2">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <!-- Material tab card start -->
                                                <div class="card">
                                                    <div class="card-block">
                                                        <!-- Row start -->
                                                        <div class="row">
                                                            <div class="col-lg-12 col-xl-12 col-md-12">
                                                                <!-- Tab panes -->
                                                                <!-- Tab panes -->
                                                                <div class="tab-content-container">
                                                                    <!-- Hub Details Tab -->
                                                                    <div id="hub-details" class="tab-content-custom active">
                                                                        <div class="form-pillar-container">
                                                                            <!-- Column 1: Agent information -->
                                                                            <div class="form-pillar">
                                                                                <div class="form-section-header">Agent
                                                                                    information</div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Agent
                                                                                        name</label>
                                                                                    <div class="input-group-custom">
                                                                                        <input type="text"
                                                                                            name="agent_name"
                                                                                            class="form-input-custom has-append"
                                                                                            value="" required>
                                                                                        <button class="btn-input-append"><i
                                                                                                class="ti-more-alt"></i></button>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Company
                                                                                        id</label>
                                                                                    <input type="text"
                                                                                        name="company_id"
                                                                                        class="form-input-custom form-input-readonly"
                                                                                        value="" readonly>
                                                                                </div>

                                                                                     <div class="input-row">
                                                                                     <div class="form-group-custom">
                                                                                         <label
                                                                                             class="form-label-custom">Code</label>
                                                                                         <input type="text"
                                                                                             name="code"
                                                                                             class="form-input-custom"
                                                                                             value="">
                                                                                     </div>
                                                                                     <div class="form-group-custom">
                                                                                         <label
                                                                                             class="form-label-custom">Code
                                                                                             description</label>
                                                                                         <input type="text"
                                                                                             name="code_description"
                                                                                             class="form-input-custom"
                                                                                             value="">
                                                                                     </div>
                                                                                 </div>

                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Phone
                                                                                         number (with country
                                                                                         code)</label>
                                                                                     <input type="text"
                                                                                         name="phone"
                                                                                         class="form-input-custom" value="">
                                                                                 </div>

                                                                                 <div class="form-group-custom">
                                                                                     <label
                                                                                         class="form-label-custom">Email</label>
                                                                                     <input type="text"
                                                                                         name="email"
                                                                                         class="form-input-custom" value=""
                                                                                         placeholder="email@example.com; email2@example.com">
                                                                                 </div>

                                                                                <div class="form-group-custom">
                                                                                    <label
                                                                                        class="form-label-custom">Remarks</label>
                                                                                     <textarea name="remarks" class="form-textarea-custom"
                                                                                         rows="3"></textarea>
                                                                                </div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Special
                                                                                        considerations for
                                                                                        destination</label>
                                                                                     <textarea name="special_considerations" class="form-textarea-custom"
                                                                                         rows="3"></textarea>
                                                                                </div>

                                                                                <div class="form-group-custom"
                                                                                    style="display: flex; gap: 8px; align-items: flex-start;">
                                                                                    <input type="checkbox"
                                                                                        name="show_pre_alert"
                                                                                        value="1"
                                                                                        style="margin-top: 3px;">
                                                                                    <label class="form-label-custom">Show
                                                                                        pre-alert warning when items in
                                                                                        shipment are not scanned</label>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Column 2: Agent address & Office address -->
                                                                            <div class="form-pillar">
                                                                                <div class="form-section-header">Agent
                                                                                    address</div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Agent
                                                                                        address</label>
                                                                                    <textarea name="agent_address" class="form-textarea-custom"
                                                                                        rows="3"></textarea>
                                                                                </div>

                                                                                <div class="input-row">
                                                                                    <div class="form-group-custom"
                                                                                        style="flex: 2;">
                                                                                        <label
                                                                                            class="form-label-custom">City</label>
                                                                                        <input type="text"
                                                                                            name="city"
                                                                                            class="form-input-custom"
                                                                                            value="">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label
                                                                                            class="form-label-custom">District/state</label>
                                                                                        <input type="text"
                                                                                            name="district_state"
                                                                                            class="form-input-custom">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label class="form-label-custom">Zip
                                                                                            code</label>
                                                                                        <input type="text"
                                                                                            name="zip_code"
                                                                                            class="form-input-custom"
                                                                                            value="">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group-custom">
                                                                                    <label
                                                                                        class="form-label-custom">Country</label>
                                                                                     <select name="country_id" class="select2-country">
                                                                                         <option value="">Select Country</option>
                                                                                         @foreach($countries as $country)
                                                                                             <option value="{{ $country->id }}" data-flag="{{ $country->iso_code }}">
                                                                                                 {{ $country->name }}
                                                                                             </option>
                                                                                         @endforeach
                                                                                     </select>
                                                                                </div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Port
                                                                                        code</label>
                                                                                    <select name="port_code"
                                                                                        class="select2-port-code"
                                                                                        style="width: 100%;">
                                                                                        <option value=""></option>
                                                                                    </select>
                                                                                </div>

                                                                                <div class="form-section-header"
                                                                                    style="margin-top: 15px;">Office address
                                                                                    (Optional)</div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Office
                                                                                        address</label>
                                                                                    <textarea name="office_address" class="form-textarea-custom"
                                                                                        rows="3"></textarea>
                                                                                </div>

                                                                                <div class="input-row">
                                                                                    <div class="form-group-custom"
                                                                                        style="flex: 2;">
                                                                                        <label
                                                                                            class="form-label-custom">City</label>
                                                                                        <input type="text"
                                                                                            name="office_city"
                                                                                            class="form-input-custom"
                                                                                            value="">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label
                                                                                            class="form-label-custom">District/state</label>
                                                                                        <input type="text"
                                                                                            name="office_district_state"
                                                                                            class="form-input-custom">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label class="form-label-custom">Zip
                                                                                            code</label>
                                                                                        <input type="text"
                                                                                            name="office_zip_code"
                                                                                            class="form-input-custom"
                                                                                            value="">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group-custom">
                                                                                    <label
                                                                                        class="form-label-custom">Country</label>
                                                                                     <select name="office_country_id" class="select2-country">
                                                                                         <option value="">Select Country</option>
                                                                                         @foreach($countries as $country)
                                                                                             <option value="{{ $country->id }}" data-flag="{{ $country->iso_code }}">
                                                                                                 {{ $country->name }}
                                                                                             </option>
                                                                                         @endforeach
                                                                                     </select>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Column 3: Agent details -->
                                                                            <div class="form-pillar">
                                                                                <div class="form-section-header">Agent
                                                                                    details</div>

                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">EORI
                                                                                        number</label>
                                                                                    <input type="text"
                                                                                        name="eori_number"
                                                                                        class="form-input-custom">
                                                                                </div>

                                                                                <div class="input-row">
                                                                                    <div class="form-group-custom">
                                                                                        <label
                                                                                            class="form-label-custom">UN/LOCODE</label>
                                                                                        <input type="text"
                                                                                            name="un_locode"
                                                                                            class="form-input-custom">
                                                                                    </div>
                                                                                    <div class="form-group-custom"
                                                                                        style="flex: 1.5;">
                                                                                        <label
                                                                                            class="form-label-custom">Agent
                                                                                            type</label>
                                                                                        <select name="agent_type" class="select2-agent-type">
                                                                                            <option></option>
                                                                                            <option
                                                                                                value="contracted_agent">
                                                                                                Contracted agent</option>
                                                                                            <option value="main_agent">Main
                                                                                                agent</option>
                                                                                            <option value="sub_agent">Sub
                                                                                                agent</option>
                                                                                            <option
                                                                                                value="3pl_japan_supplier">
                                                                                                3PL Japan supplier</option>
                                                                                            <option
                                                                                                value="3pl_greece_supplier">
                                                                                                3PL Greece supplier</option>
                                                                                            <option
                                                                                                value="mt_bergen_agency">MT
                                                                                                Bergen Agency supplier
                                                                                            </option>
                                                                                            <option
                                                                                                value="mt_singapore_projects">
                                                                                                MT Singapore Projects
                                                                                                supplier</option>
                                                                                            <option
                                                                                                value="mt_benelux_supplier">
                                                                                                MT Benelux supplier</option>
                                                                                            <option
                                                                                                value="door_to_deck_agent">
                                                                                                Door to Deck agent</option>
                                                                                            <option
                                                                                                value="mt_singapore_agency">
                                                                                                MT Singapore Agency supplier
                                                                                            </option>
                                                                                            <option
                                                                                                value="mt_norway_supplier">
                                                                                                MT Norway supplier</option>
                                                                                            <option
                                                                                                value="3pl_general_supplier">
                                                                                                3PL General supplier
                                                                                            </option>
                                                                                            <option value="external_entity">
                                                                                                External entity</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Footer -->
                                                                <div class="form-footer">
                                                                    <button type="submit" class="btn-saved-custom">Save
                                                                        Agent</button>
                                                                    <a href="{{ route('agents.index') }}"
                                                                        class="btn-cancel-custom">Cancel</a>
                                                                </div>

                                                                <div class="metadata-footer"></div>
                                                        </div>
                                                        <!-- Row end -->
                                                    </div>
                                                </div>
                                                <!-- Material tab card end -->
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- Base Style - Compact end -->
                            </div>
                            <!-- Page-body end -->
                        </div>
                    </div>
                    <div id="styleSelector"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <script src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Initialize Select2 with Flags
            function formatCountry(country) {
                if (!country.id) {
                    return country.text;
                }
                var flagCode = $(country.element).data('flag');
                if (!flagCode) return country.text;
                
                var flagUrl = "https://flagcdn.com/w20/" + flagCode.toLowerCase() + ".png";
                var $country = $(
                    '<span><img src="' + flagUrl + '" class="flag-icon" /> ' + country.text + '</span>'
                );
                return $country;
            };

            $('.select2-country').select2({
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
            $('.select2-agent-type').select2({
                placeholder: 'Select agent type',
                allowClear: false,
                width: '100%'
            });

            // Tab switching logic
            $('.tab-item').on('click', function () {
                var tabId = $(this).data('tab');

                // Update active tab link
                $('.tab-item').removeClass('active');
                $(this).addClass('active');

                // Show corresponding content
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
            });

            // jQuery Validation for Agent Form
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

            $('#agentForm').validate({
                rules: {
                    agent_name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        multiEmail: true
                    }
                },
                messages: {
                    agent_name: {
                        required: "Please enter the agent name",
                        minlength: "Agent name must be at least 3 characters"
                    },
                    email: {
                        multiEmail: "Please enter valid email address(es), separated by comma or semicolon"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group-custom').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("error");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass("error");
                }
            });
        });
    </script>
@endsection