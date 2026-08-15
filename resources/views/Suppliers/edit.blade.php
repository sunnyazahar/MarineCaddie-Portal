@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        #offices-table tbody td {
            padding: 4px 5px !important;
            vertical-align: middle !important;
            font-size: 12px;
            white-space: normal !important;
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }
        .filter-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            display: block;
        }
        .filter-input {
            height: 32px;
            font-size: 13px;
            border-radius: 2px;
        }
        .clear-filters {
            font-size: 12px;
            color: #ff5252;
            text-decoration: none;
            cursor: pointer;
            margin-top: 25px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-row {
            margin-right: -10px;
            margin-left: -10px;
        }
        .custom-col {
            padding-right: 10px;
            padding-left: 10px;
            flex: 0 0 11.5%;
            max-width: 11.5%;
        }
        @media (max-width: 992px) {
            .custom-col {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
        }
        .select2-container--default .select2-selection--single {
            height: 35px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 3px !important;
            background-color: transparent !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding: 0 10px !important;
            font-size: 13px !important;
            color: #333 !important;
            background-color: transparent !important;
            background: transparent !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080;
            border: 1px solid #006666;
            color: #fff;
            font-size: 10px;
            margin-top: 2px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 30px;
            border: 1px solid #ced4da;
            border-radius: 2px;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
        }

        /* 3-Column Form Styling */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            padding: 20px 30px 100px 30px;
            background: #fff;
        }
        .form-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 8px;
            border-bottom: 2px solid #1b5e6f;
            margin-bottom: 10px;
        }
        .form-label-custom {
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 5px;
            display: block;
        }
        .form-input-custom {
            height: 35px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 5px 10px;
            font-size: 13px;
        }
        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            min-height: 80px;
            resize: vertical;
        }
        .input-with-icon {
            position: relative;
        }
        .input-icon-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #eee;
            border: 1px solid #ccc;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 12px;
            color: #666;
            cursor: pointer;
        }
        .sub-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        .optional-header {
            font-size: 14px;
            font-weight: 600;
            color: #1b5e6f;
            margin-top: 20px;
            margin-bottom: 10px;
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
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        }

        /* Tab Styling */
        .nav-tabs-custom {
            border-bottom: 1px solid #ddd;
            background: #e9ecef;
            display: flex;
            padding: 0 20px;
        }
        .nav-tabs-custom .nav-link {
            padding: 10px 25px;
            color: #666;
            font-size: 14px;
            border: none;
            border-bottom: 3px solid transparent;
            text-decoration: none;
            cursor: pointer;
        }
        .nav-tabs-custom .nav-link.active {
            color: #1b5e6f;
            border-bottom-color: #1b5e6f;
            font-weight: 600;
        }
        .tab-content-custom {
            display: none;
        }
        .tab-content-custom.active {
            display: block;
        }

        .company-id-label {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            margin-bottom: 2px;
        }
        .company-id-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .footer-metadata {
            margin-left: auto;
            text-align: right;
            font-size: 11px;
            color: #999;
            line-height: 1.4;
        }
        .btn-saved-status {
            background-color: #e0e0e0;
            color: #888;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-size: 13px;
            cursor: default;
        }

        /* Contacts Tab Specific Styling */
        .contacts-top-bar {
            padding: 15px 30px;
            background: #fff;
            display: flex;
            justify-content: flex-end;
            border-bottom: 1px solid #eee;
        }
        .btn-add-contact {
            background-color: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            padding: 6px 20px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-add-contact:hover {
            background-color: #f0f8f9;
            color: #12404c;
            border-color: #12404c;
        }
        .contacts-table-container {
            padding-bottom: 100px;
            background: #fff;
        }
        .table-contacts {
            width: 100%;
            border-collapse: collapse;
        }
        .table-contacts th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #f5f5f5;
        }
        .table-contacts td {
            padding: 12px 15px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            border-right: 1px solid #f9f9f9;
        }
        .table-contacts tr:last-child td {
            border-bottom: none;
        }
        .table-contacts th:last-child, .table-contacts td:last-child {
            border-right: none;
        }
        .main-contact-check {
            color: #28a745;
            font-weight: bold;
            font-size: 16px;
        }

        @media (max-width: 1199.98px) {
            .form-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px 20px 110px;
            }
        }

        @media (max-width: 991.98px) {
            .nav-tabs-custom {
                overflow-x: auto;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
                padding: 0 8px;
                max-width: 100%;
            }

            .nav-tabs-custom .nav-link {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 10px 14px;
                font-size: 13px;
            }

            .form-grid {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px 12px 120px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-column {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .sub-grid-3 {
                grid-template-columns: 1fr !important;
                gap: 12px;
            }

            .form-footer-custom {
                left: 0 !important;
                right: 0 !important;
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .form-footer-custom .btn,
            .btn-cancel-custom {
                width: 100%;
                text-align: center;
            }

            .footer-metadata {
                margin-left: 0;
                width: 100%;
                text-align: left;
            }

            .contacts-top-bar {
                padding: 12px 16px;
                justify-content: stretch;
            }

            .btn-add-contact {
                width: 100%;
                text-align: center;
            }

            .contacts-table-container {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-contacts {
                min-width: 640px;
            }

            .form-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .tab-content-custom,
            .card,
            .page-body {
                max-width: 100%;
                overflow-x: hidden;
            }
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
    @include('layouts.partials.pcoded-shell-start')
                                        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" id="edit-supplier-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'supplier-details') }}">

                                            <div class="nav-tabs-custom">
                                                <a href="javascript:void(0)" class="nav-link active" data-tab="supplier-details">Supplier details</a>
                                                <a href="javascript:void(0)" class="nav-link" data-tab="contacts">Contacts</a>
                                            </div>

                                            <div class="card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                                                <!-- Supplier details Tab -->
                                                <div id="supplier-details" class="tab-content-custom active">
                                                    <div class="form-grid">
                                                        <!-- Column 1: Supplier information -->
                                                        <div class="form-column">
                                                            <div class="form-section-header">Supplier information</div>
                                                            
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Supplier name</label>
                                                                <div class="input-with-icon">
                                                                    <input type="text" name="supplier_name" class="form-input-custom" value="{{ $supplier->supplier_name }}" required>
                                                                    <span class="input-icon-btn">...</span>
                                                                </div>
                                                                <div class="company-id-label">Company id</div>
                                                                <div class="company-id-value">{{ $supplier->id }}</div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                                <input type="text" name="phone_number" class="form-input-custom" value="{{ $supplier->phone_number }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Email</label>
                                                                <input type="text" name="email" class="form-input-custom" value="{{ $supplier->email }}"
                                                                    placeholder="email@example.com; email2@example.com">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Remarks</label>
                                                                <textarea name="remarks" class="form-textarea-custom">{{ $supplier->remarks }}</textarea>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Special considerations for destination</label>
                                                                <textarea name="special_considerations" class="form-textarea-custom">{{ $supplier->special_considerations }}</textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Column 2: Supplier address -->
                                                        <div class="form-column">
                                                            <div class="form-section-header">Supplier address</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Supplier address</label>
                                                                <textarea name="supplier_address" class="form-textarea-custom">{{ $supplier->supplier_address }}</textarea>
                                                            </div>

                                                            <div class="sub-grid-3">
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">City</label>
                                                                    <input type="text" name="city" class="form-input-custom" value="{{ $supplier->city }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">District/state</label>
                                                                    <input type="text" name="district_state" class="form-input-custom" value="{{ $supplier->district_state }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Zip code</label>
                                                                    <input type="text" name="zip_code" class="form-input-custom" value="{{ $supplier->zip_code }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Country</label>
                                                                <select name="country_id" class="form-input-custom select2">
                                                                    <option value="">Select Country</option>
                                                                    @foreach($countries as $country)
                                                                        <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}" {{ $supplier->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Port code</label>
                                                                <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                                    <option value=""></option>
                                                                    @if (old('port_code', $supplier->port_code))
                                                                        <option value="{{ old('port_code', $supplier->port_code) }}" selected>
                                                                            {{ old('port_code', $supplier->port_code) }}
                                                                        </option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="optional-header">Office address (optional)</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Office address</label>
                                                                <textarea name="office_address" class="form-textarea-custom">{{ $supplier->office_address }}</textarea>
                                                            </div>

                                                            <div class="sub-grid-3">
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">City</label>
                                                                    <input type="text" name="office_city" class="form-input-custom" value="{{ $supplier->office_city }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">District/state</label>
                                                                    <input type="text" name="office_district_state" class="form-input-custom" value="{{ $supplier->office_district_state }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Zip code</label>
                                                                    <input type="text" name="office_zip_code" class="form-input-custom" value="{{ $supplier->office_zip_code }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Country</label>
                                                                <select name="office_country_id" class="form-input-custom select2">
                                                                    <option value="">Select Country</option>
                                                                    @foreach($countries as $country)
                                                                        <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}" {{ $supplier->office_country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Column 3: Supplier details -->
                                                        <div class="form-column">
                                                            <div class="form-section-header">Supplier details</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">VAT number</label>
                                                                <input type="text" name="vat_number" class="form-input-custom" value="{{ $supplier->vat_number }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">EORI number</label>
                                                                <input type="text" name="eori_number" class="form-input-custom" value="{{ $supplier->eori_number }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Currency</label>
                                                                <select name="currency" class="form-input-custom">
                                                                    <option value="">Select Currency</option>
                                                                    @foreach($currencies as $currency)
                                                                        <option value="{{ $currency }}" {{ $supplier->currency == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">UN/LOCODE</label>
                                                                <input type="text" name="un_locode" class="form-input-custom" value="{{ $supplier->un_locode }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contacts Tab -->
                                                <div id="contacts" class="tab-content-custom">
                                                    <div class="contacts-top-bar">
                                                        <a href="{{ route('suppliers.contacts.create', $supplier->id) }}" class="btn-add-contact">Add contact</a>
                                                    </div>
                                                    <div class="contacts-table-container">
                                                        <table class="table-contacts">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 25%;">Name</th>
                                                                    <th style="width: 20%;">Email</th>
                                                                    <th style="width: 15%;">Phone number</th>
                                                                    <th style="width: 25%;">Description</th>
                                                                    <th style="width: 10%; text-align: center;">Main contact</th>
                                                                    <th style="width: 5%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($supplier->contacts as $contact)
                                                                    <tr>
                                                                        <td>{{ $contact->name }}</td>
                                                                        <td>{{ $contact->email }}</td>
                                                                        <td>{{ $contact->phone_number }}</td>
                                                                        <td>{{ $contact->description }}</td>
                                                                        <td style="text-align: center;">
                                                                            @if($contact->is_main_contact)
                                                                                <span class="main-contact-check">✓</span>
                                                                            @endif
                                                                        </td>
                                                                        <td style="text-align: right;">
                                                                            <a href="{{ route('suppliers.contacts.edit', [$supplier->id, $contact->id]) }}">
                                                                                <i class="ti-pencil btn-action-pencil" style="color: #9ca3af;"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">No contacts available yet.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="form-footer-custom">
                                                    <button type="submit" class="btn btn-primary" style="background: #1b5e6f; border-color: #1b5e6f; padding: 8px 25px;">Update supplier</button>
                                                    <a href="{{ route('suppliers.index') }}" class="btn-cancel-custom">Cancel</a>
                                                    <div class="footer-metadata">
                                                        @include('partials.audit-info', ['record' => $supplier])
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
    @include('layouts.partials.pcoded-shell-end')
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

    <script>
        $(document).ready(function() {
            // Initialize Select2 with flags
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var flagUrl = $(state.element).data('flag-url');
                var $state = $(
                    '<span><img src="' + flagUrl + '" class="img-flag" style="width: 20px; height: 15px; margin-right: 8px; vertical-align: middle; border: 1px solid #eee;" /> ' + state.text + '</span>'
                );
                return $state;
            };

            $('.select2').select2({
                templateResult: formatState,
                templateSelection: formatState,
                width: '100%',
                placeholder: "Select Country",
                allowClear: false
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

            // Tab switching logic (keeps URL hash + hidden field so update redirects restore the active tab)
            function activateSupplierTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.nav-tabs-custom .nav-link[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.nav-tabs-custom .nav-link').removeClass('active');
                $('.nav-tabs-custom .nav-link[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                return true;
            }

            $('.nav-tabs-custom .nav-link').on('click', function() {
                var tabId = $(this).data('tab');
                activateSupplierTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateSupplierTab(hashTab);
            }
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#edit-supplier-form', 'fallbackUrl' => route('suppliers.index')])
@endsection
