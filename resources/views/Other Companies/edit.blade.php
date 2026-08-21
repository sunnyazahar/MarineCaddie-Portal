@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

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
            line-height: 1.25 !important;
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
            /* gap: 20px; */
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
            font-size: 13px;
            color: #3c485a;
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

        @media (max-width: 1199.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px;
            }
        }

        @media (max-width: 991.98px) {
            .edit-header-summary {
                flex-wrap: wrap;
                gap: 12px 20px;
                padding: 12px 16px;
            }

            .summary-item {
                min-width: 0;
                flex: 1 1 120px;
            }

            .tabs-container {
                display: flex !important;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding: 0 8px;
                max-width: 100%;
            }

            .tab-item {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 10px 14px;
                font-size: 12px;
            }

            .form-pillar-container,
            .form-pillar-container[style*="grid-template-columns"] {
                display: grid !important;
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

            .input-row .form-group-custom {
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

            .btn-save-custom,
            .btn-saved-custom,
            .btn-cancel-custom {
                width: 100%;
                text-align: center;
            }

            .form-footer .audit-meta {
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

            .tab-content-custom,
            .card,
            .page-body {
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
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
                                                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'company-details') }}">
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
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                                                                <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person', $otherCompany->contact_person) }}" required>
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

                                                            <x-forms.country-select
                                                                name="country_id"
                                                                label="Country"
                                                                :countries="$countries"
                                                                :value="$otherCompany->country_id"
                                                                class="form-input-custom"
                                                            />

                                                            <x-forms.port-select
                                                                name="port_code"
                                                                label="Port code"
                                                                :value="$otherCompany->port_code"
                                                            />

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

                                                            <x-forms.country-select
                                                                name="office_country_id"
                                                                label="Country"
                                                                :countries="$countries"
                                                                :value="$otherCompany->office_country_id"
                                                                class="form-input-custom"
                                                            />
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
    @include('layouts.partials.pcoded-shell-end')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#edit-company-form').validate({
                rules: {
                    company_name: {
                        required: true
                    },
                    contact_person: {
                        required: true
                    }
                },
                messages: {
                    company_name: {
                        required: "Please enter the company name"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("error");
                },
                unhighlight: function (element) {
                    $(element).removeClass("error");
                }
            });

            function fixedFooterOffset() {
                var isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                if (isMobile) {
                    $('.form-footer').css('left', '0px');
                    return;
                }
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

                // Tab switching logic (keeps URL hash + hidden field so update redirects restore the active tab)
            function activateOtherCompanyTab(tabId) {
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

            $('.tab-item').on('click', function() {
                var tabId = $(this).data('tab');
                activateOtherCompanyTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateOtherCompanyTab(hashTab);
            }
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#edit-company-form', 'fallbackUrl' => route('other-companies.index'), 'saveButtonSelector' => '#btn-save'])
@endsection
