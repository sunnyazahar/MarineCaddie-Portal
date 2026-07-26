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
        /* Select2 Custom Styling - High Specificity */
        body .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            background: #fff !important;
            border: 1px solid #d1d5db !important;
            height: 32px !important;
            border-radius: 3px !important;
            box-sizing: border-box !important;
        }
        body .select2-container--default.select2-container--focus .select2-selection--single,
        body .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1b5e6f !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            color: #333 !important;
            font-size: 12px !important;
            padding-left: 10px !important;
            background-color: transparent !important;
            background: transparent !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #999 !important;
        }

        body .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
            top: 1px !important;
            right: 6px !important;
        }
        body .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7280 transparent transparent transparent !important;
        }
        body .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #6b7280 transparent !important;
        }

        .select2-dropdown {
            background-color: #fff !important; /* Keep dropdown list white for readability */
            border: 1px solid #d1d5db !important;
        }
        .img-flag {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            vertical-align: middle;
        }
        /* Edit Header Summary Styling */
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
            margin-bottom: 10px;
        }
        .form-group-custom {
            margin-bottom: 15px;
        }
        .form-label-custom {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }
        .form-input-custom {
            height: 32px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 5px 10px;
            font-size: 12px;
            color: #333;
            background: #fff;
        }
        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 8px 10px;
            font-size: 12px;
            color: #333;
            resize: none;
            background: #fff;
        }
        .form-input-readonly {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
        }
        .input-row {
            display: flex;
            gap: 15px;
        }
        .input-row > div {
            flex: 1;
        }
        .input-group-custom {
            display: flex;
        }
        .btn-input-append {
            height: 32px;
            background: #e9ecef;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 10px;
            border-radius: 0 3px 3px 0;
            cursor: pointer;
            color: #666;
            display: flex;
            align-items: center;
        }
        .form-input-custom.has-append {
            border-radius: 3px 0 0 3px;
        }

        /* Footer Styling */
        .form-footer {
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.98);
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
            margin-top: 0;
        }
        .form-footer .audit-meta {
            margin-left: auto;
            text-align: right;
            font-size: 11px;
            color: #999;
            line-height: 1.6;
        }
        .btn-save-custom,
        .btn-saved-custom {
            background: #e9ecef;
            color: #a0aec0;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-size: 13px;
            cursor: default;
        }
        .btn-cancel-custom {
            color: #01a9ac;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-cancel-custom:hover {
            text-decoration: underline;
        }
        .page-body {
            padding-bottom: 80px;
        }

        /* Metadata Footer */
        .metadata-footer {
            padding: 10px 25px;
            text-align: right;
            font-size: 10px;
            color: #999;
            background: #fff;
        }

        /* Tab Visibility */
        .tab-content-custom {
            display: none;
        }
        .tab-content-custom.active {
            display: block;
        }

        /* Contacts Tab Specific Styling */
        .contacts-top-bar {
            padding: 15px 25px;
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
            padding: 0;
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
            background: #f8fafd;
        }
        .table-contacts td {
            padding: 12px 15px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
        }
        .main-contact-check {
            color: #28a745;
            font-weight: bold;
            font-size: 16px;
        }

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 0 !important;
        }
        .main-body .page-wrapper {
            padding: 0 !important;
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
                                    <div class="page-header">
                                        
                                    </div>
                                    <!-- Page-header end -->

                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <!-- Header Summary Bar -->
                                        <div class="edit-header-summary">
                                            <div class="summary-item">
                                                <div class="summary-label">Company Id</div>
                                                <div class="summary-value">{{ $otherCompany->id }}</div>
                                            </div>
                                            <div class="summary-item">
                                                <div class="summary-label">Used as consignees</div>
                                                <div class="summary-value" style="text-align: center;">{{ $otherCompany->created_at ? $otherCompany->created_at->format('d.m.Y') : '-' }}</div>
                                            </div>
                                            <div class="summary-item">
                                                <div class="summary-label">Inactive</div>
                                                <div class="summary-value" style="text-align: center;">No</div>
                                            </div>
                                            <div class="summary-item">
                                                <div class="summary-label">Blocked</div>
                                                <div class="summary-value" style="text-align: center;">No</div>
                                            </div>
                                        </div>

                                        <!-- Tab Navigation -->
                                        <div class="tabs-container">
                                            <a class="tab-item active" data-tab="company-details">Other company details</a>
                                            <a class="tab-item" data-tab="contacts">Contacts</a>
                                        </div>

                                        <div class="card" style="margin: 0; border: none; border-radius: 0;">
                                            <!-- Company Details Tab -->
                                            <div id="company-details" class="tab-content-custom active">
                                                @if ($errors->any())
                                                    <div style="margin: 12px 25px 0; padding: 10px 14px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 13px;">
                                                        <strong>Could not save:</strong>
                                                        <ul style="margin: 6px 0 0 18px;">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if (session('success'))
                                                    <div style="margin: 12px 25px 0; padding: 10px 14px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; border-radius: 4px; font-size: 13px;">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif
                                                <form action="{{ route('other-companies.update', $otherCompany->id) }}" method="POST" id="edit-company-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-pillar-container">
                                                        <!-- ... existing content ... -->
                                                        <!-- Column 1: Company information -->
                                                        <div class="form-pillar">
                                                            <div class="form-section-header">Company information</div>
                                                            
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Company name</label>
                                                                <input type="text" class="form-input-custom" name="company_name" value="{{ old('company_name', $otherCompany->company_name) }}">
                                                            </div>

                                                            <div class="form-group-custom d-none">
                                                                <label class="form-label-custom">Company id</label>
                                                                <input type="text" class="form-input-custom form-input-readonly" value="{{ $otherCompany->id }}" readonly>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Company type</label>
                                                                <select name="company_type" class="form-input-custom select2-company-type">
                                                                    <option value=""></option>
                                                                    @php
                                                                        $selectedCompanyType = old('company_type', $otherCompany->company_type);
                                                                    @endphp
                                                                    @if($selectedCompanyType && !in_array($selectedCompanyType, $companyTypes, true))
                                                                        <option value="{{ $selectedCompanyType }}" selected>{{ $selectedCompanyType }}</option>
                                                                    @endif
                                                                    @foreach($companyTypes as $type)
                                                                        <option value="{{ $type }}" {{ $selectedCompanyType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="input-row">
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Code</label>
                                                                    <input type="text" name="code" class="form-input-custom" value="{{ old('code', $otherCompany->code) }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Code description</label>
                                                                    <input type="text" name="code_description" class="form-input-custom" value="{{ old('code_description', $otherCompany->code_description) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                                <input type="text" class="form-input-custom" name="phone_number" value="{{ old('phone_number', $otherCompany->phone_number) }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Email</label>
                                                                <input type="email" name="email" class="form-input-custom" value="{{ old('email', $otherCompany->email) }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Remarks</label>
                                                                <textarea name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks', $otherCompany->remarks) }}</textarea>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Special considerations for destination</label>
                                                                <textarea name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations', $otherCompany->special_considerations) }}</textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Column 2: Company address -->
                                                        <div class="form-pillar">
                                                            <div class="form-section-header">Company address</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Street address</label>
                                                                <textarea name="street_address" class="form-textarea-custom" rows="4">{{ old('street_address', $otherCompany->street_address) }}</textarea>
                                                            </div>

                                                            <div class="input-row">
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">City</label>
                                                                    <input type="text" name="city" class="form-input-custom" value="{{ old('city', $otherCompany->city) }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">District/state</label>
                                                                    <input type="text" name="district_state" class="form-input-custom" value="{{ old('district_state', $otherCompany->district_state) }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Zip code</label>
                                                                    <input type="text" name="zip_code" class="form-input-custom" value="{{ old('zip_code', $otherCompany->zip_code) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Country</label>
                                                                <select name="country_id" class="form-input-custom select2-country">
                                                                    <option value="">Select Country</option>
                                                                    @foreach($countries as $c)
                                                                        <option value="{{ $c->id }}" data-flag-url="{{ $c->flag_url }}" {{ old('country_id', $otherCompany->country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Port code</label>
                                                                <select name="port_code" class="select2-port-code" style="width: 100%;">
                                                                    <option value=""></option>
                                                                    @if (old('port_code', $otherCompany->port_code))
                                                                        <option value="{{ old('port_code', $otherCompany->port_code) }}" selected>
                                                                            {{ old('port_code', $otherCompany->port_code) }}
                                                                        </option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="form-section-header" style="margin-top: 20px;">Office address (Optional)</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Street address / post box</label>
                                                                <textarea name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address', $otherCompany->office_street_address) }}</textarea>
                                                            </div>

                                                            <div class="input-row">
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">City</label>
                                                                    <input type="text" name="office_city" class="form-input-custom" value="{{ old('office_city', $otherCompany->office_city) }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">District/state</label>
                                                                    <input type="text" name="office_district_state" class="form-input-custom" value="{{ old('office_district_state', $otherCompany->office_district_state) }}">
                                                                </div>
                                                                <div class="form-group-custom">
                                                                    <label class="form-label-custom">Zip code</label>
                                                                    <input type="text" name="office_zip_code" class="form-input-custom" value="{{ old('office_zip_code', $otherCompany->office_zip_code) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Country</label>
                                                                <select name="office_country_id" class="form-input-custom select2-country">
                                                                    <option value="">Select Country</option>
                                                                    @foreach($countries as $c)
                                                                        <option value="{{ $c->id }}" data-flag-url="{{ $c->flag_url }}" {{ old('office_country_id', $otherCompany->office_country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Column 3: Company details -->
                                                        <div class="form-pillar">
                                                            <div class="form-section-header">Company details</div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">VAT number</label>
                                                                <input type="text" name="vat_number" class="form-input-custom" value="{{ old('vat_number', $otherCompany->vat_number) }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">EORI number</label>
                                                                <input type="text" name="eori_number" class="form-input-custom" value="{{ old('eori_number', $otherCompany->eori_number) }}">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Currency</label>
                                                                <select name="currency" class="form-input-custom select2-currency">
                                                                    <option value=""></option>
                                                                    @foreach($currencies as $curr)
                                                                        <option value="{{ $curr }}" {{ old('currency', $otherCompany->currency) == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">UN/LOCODE</label>
                                                                <input type="text" name="un_locode" class="form-input-custom" style="width: 50%;" value="{{ old('un_locode', $otherCompany->un_locode) }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Footer -->
                                                    <div class="form-footer">
                                                        <button type="submit" class="btn-save-custom" id="btn-save" style="background:#e9ecef;color:#a0aec0;cursor:default;" disabled>All changes saved</button>
                                                        <a href="{{ route('other-companies.index') }}" class="btn-cancel-custom">Cancel</a>
                                                        <div class="audit-meta">
                                                            @include('partials.audit-info', ['record' => $otherCompany])
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Contacts Tab -->
                                            <div id="contacts" class="tab-content-custom">
                                                <div class="contacts-top-bar">
                                                    <a href="{{ route('other-companies.contacts.create', $otherCompany->id) }}" class="btn-add-contact">Add contact</a>
                                                </div>
                                                <div class="contacts-table-container">
                                                    <table class="table-contacts">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px; text-align: center;">Main</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Phone</th>
                                                                <th>Description</th>
                                                                <th style="width: 100px; text-align: center;">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($otherCompany->contacts as $contact)
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        @if($contact->is_main_contact)
                                                                            <span class="main-contact-check">✓</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $contact->name }}</td>
                                                                    <td>{{ $contact->email }}</td>
                                                                    <td>{{ $contact->phone_number }}</td>
                                                                    <td>{{ Str::limit($contact->description, 50) }}</td>
                                                                    <td style="text-align: center;">
                                                                        <div class="action-icons" style="display: flex; gap: 10px; justify-content: center;">
                                                                            <a href="{{ route('other-companies.contacts.edit', [$otherCompany->id, $contact->id]) }}" title="Edit"><i class="ti-pencil"></i></a>
                                                                            <form action="{{ route('other-companies.contacts.destroy', [$otherCompany->id, $contact->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contact?')">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" style="background:none; border:none; padding:0; color:#01a9ac; cursor:pointer;" title="Delete"><i class="ti-trash"></i></button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" style="text-align: center; padding: 20px; color: #999;">No contacts found.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
            function fixedFooterOffset() {
                var $navbar = $('.pcoded-navbar');
                var sidebarWidth = $navbar.length ? $navbar.outerWidth() : 0;
                $('.form-footer').css('left', sidebarWidth + 'px');
            }
            fixedFooterOffset();
            $(window).on('resize', fixedFooterOffset);
            $(document).on('click', '.mobile-menu, .pcoded-navbar .pcoded-navigatio-lavel, .navbar-wrapper .menu-toggle', function () {
                setTimeout(fixedFooterOffset, 300);
            });

            $('select.select2-company-type').select2({
                placeholder: 'Select company type',
                allowClear: false,
                width: '100%'
            });

            function formatCountryFlag(state) {
                if (!state.id) {
                    return state.text;
                }
                var flagUrl = $(state.element).data('flag-url');
                if (!flagUrl) {
                    return state.text;
                }
                return $('<span><img src="' + flagUrl + '" class="img-flag" /> ' + state.text + '</span>');
            }

            $('select.select2-country').select2({
                placeholder: 'Select Country',
                allowClear: false,
                width: '100%',
                templateResult: formatCountryFlag,
                templateSelection: formatCountryFlag
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

            $('select.select2-currency').select2({
                placeholder: 'Select Currency',
                allowClear: false,
                width: '100%'
            });

             // Initialize Select2 for standard filters
            $('select.select2').select2({
                placeholder: "Click here",
                allowClear: false
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
                onChange: function(option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function() {
                    toggleFilterVisibility();
                },
                onDeselectAll: function() {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Office Name', id: 'col-Office-Name'},
                    {val: 'Short Name', id: 'col-Short-Name'},
                    {val: 'City', id: 'col-City'},
                    {val: 'Country', id: 'col-Country'},
                    {val: 'Phone', id: 'col-Phone'},
                    {val: 'Email', id: 'col-Email'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            // Initial call to set visibility state
            toggleFilterVisibility();

            $('#other-companies-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

                // Tab switching logic
            $('.tab-item').on('click', function() {
                var tabId = $(this).data('tab');
                $('.tab-item').removeClass('active');
                $(this).addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
            });
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#edit-company-form', 'fallbackUrl' => route('other-companies.index'), 'saveButtonSelector' => '#btn-save'])
@endsection
