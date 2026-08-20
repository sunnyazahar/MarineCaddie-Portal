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
        /* Premium Form Styles */
        .main-body .page-wrapper {
            padding: 0;
        }

        .form-pillar-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 25px;
            padding: 20px;
            background: #fff;
            min-height: 600px;
        }

        .form-pillar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-section-header {
            font-size: 14px;
            font-weight: 600;
            color: #1f4356;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-group-custom {
            margin-bottom: 0px;
            position: relative;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #3c485a;
            margin-bottom: 4px;
            display: block;
        }

        .form-control-custom {
            height: 32px;
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 0 10px;
            width: 100%;
            background: #fff;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            outline: none;
        }

        .form-textarea-custom {
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 10px;
            width: 100%;
            resize: none;
        }

        .input-with-append {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-append-btn {
            position: absolute;
            right: 1px;
            background: #e5e7eb;
            border: none;
            padding: 0 8px;
            height: 30px;
            border-radius: 0 4px 4px 0;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select-custom {
            height: 32px;
            border: 1px solid #f3f4f6;
            border-radius: 4px;
            font-size: 12px;
            padding: 0 5px;
            width: 100%;
            background: #fff;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
        }

        .checkbox-label {
            font-size: 12px;
            color: #4b5563;
        }

        .address-sub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .btn-add-account {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 4px 12px;
            font-size: 12px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
        }

        .remove-account-btn {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .remove-account-btn:hover {
            color: #ef4444;
        }

        .remove-account-btn i {
            font-size: 14px;
        }

        .edit-footer {
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
        }

        .btn-save-custom {
            background: #1b5e6f;
            color: #fff;
            padding: 8px 25px;
            border-radius: 4px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel-custom {
            color: #3b82f6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-cancel-custom:hover {
            text-decoration: underline;
        }

        #office-details.tab-pane.active {
            padding-bottom: 72px;
        }

        /* Summary Header Styles */
        .summary-header {
            display: flex;
            gap: 40px;
            padding: 15px 25px;
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .summary-label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 500;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        /* Tab Styles */
        .custom-tabs {
            display: flex;
            background: #e5e7eb;
            padding: 0 10px;
            gap: 5px;
        }

        .custom-tab {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 500;
            color: #4b5563;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
        }

        .custom-tab:hover {
            color: #111827;
            text-decoration: none;
        }

        .custom-tab.active {
            color: #111827;
            border-bottom-color: #1b5e6f;
            background: #fff;
        }

        .edit-footer .audit-info {
            margin-left: auto;
            position: static;
            text-align: right;
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.4;
        }

        .edit-footer .audit-info b {
            color: #6b7280;
        }

        /* Remove Select2 background and match height */
        .select2-container--default .select2-selection--single {
            background-color: transparent !important;
            border: 1px solid #f3f4f6 !important;
            /* Match other inputs */
            height: 32px !important;
            border-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            line-height: 32px !important;
            padding-left: 10px !important;
            font-size: 12px !important;
            color: #4b5563 !important;
        }

        /* Remove Select2 background and match height */
        .select2-container--default .select2-selection--single {
            background-color: transparent !important;
            border: 1px solid #f3f4f6 !important;
            /* Match other inputs */
            height: 32px !important;
            border-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            line-height: 32px !important;
            padding-left: 10px !important;
            font-size: 12px !important;
            color: #4b5563 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        /* Style the dropdown list itself */
        .select2-dropdown {
            border: 1px solid #f3f4f6 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            z-index: 10001 !important;
            position: absolute !important;
        }

        .select2-results__option {
            font-size: 12px !important;
            padding: 8px 10px !important;
        }

        /* Essential for dropdownParent: $(this).parent() */
        .form-group-custom,
        .input-with-append {
            position: relative !important;
            overflow: visible !important;
        }

        .btn-status-saved {
            background: #e5e7eb;
            color: #9ca3af;
            padding: 8px 20px;
            border-radius: 4px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: default;
        }

        /* Tab Content Styles */
        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .coming-soon-pane {
            padding: 50px;
            text-align: center;
            background: #fff;
            min-height: 400px;
            color: #6b7280;
        }

        /* Operations Users Table Styles */
        .ops-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .ops-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 11px;
            font-weight: 600;
            color: #1f4356;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
            text-transform: capitalize;
        }

        .ops-table td {
            padding: 12px 15px;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .ops-name-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .ops-email-link {
            color: #4b5563;
            text-decoration: none;
        }

        .ops-action-icon {
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
        }

        .ops-action-icon:hover {
            color: #4b5563;
        }

        .activated-icon {
            color: #111827;
            font-weight: bold;
            font-size: 16px;
        }

        /* Empty State Styles */
        .no-data-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 0;
            color: #9ca3af;
        }

        .no-data-icon {
            font-size: 24px;
            margin-bottom: 15px;
            color: #d1d5db;
        }

        .no-data-text {
            font-size: 12px;
            font-weight: 500;
        }

        /* Pane Header Actions */
        .pane-header-actions {
            display: flex;
            justify-content: flex-end;
            padding: 10px 15px;
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
        }

        .btn-pane-action {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 6px 15px;
            font-size: 12px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-pane-action:hover {
            background: #3b82f6;
            color: #fff;
        }

        /* Modal Styles */
        .custom-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .custom-modal {
            background: #fff;
            width: 450px;
            border-radius: 8px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-body {
            padding: 30px;
        }

        .modal-field {
            margin-bottom: 25px;
        }

        .modal-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #1b5e6f;
            margin-bottom: 8px;
        }

        .modal-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 13px;
            color: #374151;
            transition: border-color 0.2s;
        }

        .modal-input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-top: 40px;
        }

        .btn-modal-save {
            background: #1b5e6f;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-modal-save:hover {
            background: #144d5a;
        }

        .btn-modal-cancel {
            color: #3b82f6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-modal-cancel:hover {
            text-decoration: underline;
        }

        /* Template Modal Specifics */
        .modal-error-msg {
            color: #ef4444;
            font-size: 11px;
            margin-top: 4px;
            font-weight: 400;
        }

        .modal-input.error {
            border-color: #fca5a5;
        }

        .modal-section-title {
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            margin-bottom: 12px;
            margin-top: 5px;
        }

        .dynamic-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .btn-remove-row {
            color: #1b5e6f;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s;
        }

        .btn-remove-row:hover {
            color: #ef4444;
        }

        .btn-add-row {
            border: 1px solid #1b5e6f;
            color: #1b5e6f;
            background: #fff;
            padding: 6px 15px;
            font-size: 12px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 5px;
            margin-bottom: 20px;
            width: fit-content;
        }

        .btn-add-row:hover {
            background: #f8fafc;
        }

        .ops-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: #fff;
        }

        .ops-table {
            min-width: 640px;
        }

        @media (max-width: 1199.98px) {
            .form-pillar-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 20px;
                padding: 16px !important;
            }
        }

        @media (max-width: 991.98px) {
            .main-body .page-wrapper {
                padding: 0 !important;
            }

            .summary-header {
                flex-wrap: wrap;
                gap: 16px 24px;
                padding: 12px 16px;
            }

            .custom-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
                padding: 0 8px;
                gap: 0;
            }

            .custom-tab {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 10px 14px;
                font-size: 12px;
            }

            .form-pillar-container {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px !important;
                min-height: 0 !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .form-pillar {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .address-sub-grid,
            .account-row-grid {
                grid-template-columns: 1fr !important;
            }

            .tab-pane.active {
                padding: 12px;
                max-width: 100%;
                overflow-x: hidden;
            }

            #office-details.tab-pane.active {
                padding: 0;
                padding-bottom: 72px;
            }

            .pane-header-actions {
                padding: 10px 0;
                justify-content: stretch !important;
            }

            .pane-header-actions .btn-pane-action,
            .pane-header-actions a.btn-pane-action {
                width: 100%;
                text-align: center;
                display: block;
            }

            .ops-table-wrap {
                margin: 0 -4px;
                padding: 0 4px;
            }

            .ops-table th,
            .ops-table td {
                padding: 10px 12px;
                white-space: nowrap;
            }

            .ops-table th:first-child,
            .ops-table td:first-child {
                padding-left: 12px;
            }

            .edit-footer {
                flex-wrap: wrap;
                padding: 16px;
                gap: 12px;
                position: static !important;
                left: auto !important;
            }

            .edit-footer .audit-info {
                width: 100%;
                text-align: left;
                margin-left: 0;
            }

            .btn-save-custom {
                width: 100%;
                text-align: center;
            }

            .custom-modal {
                width: calc(100vw - 24px) !important;
                max-width: 450px;
                margin: 12px;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .coming-soon-pane {
                padding: 32px 16px;
                min-height: 200px;
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
                                @if(session('success'))
                                    <div class="alert alert-success border-none"
                                        style="background-color: #ecfdf5; color: #065f46; border: 1px solid #10b981;">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger border-none"
                                        style="background-color: #fef2f2; color: #991b1b; border: 1px solid #f87171;">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if($errors->any())
                                    <div class="alert alert-danger border-none"
                                        style="background-color: #fef2f2; color: #991b1b; border: 1px solid #f87171;">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Summary Header -->
                                <div class="summary-header">
                                    <div class="summary-item">
                                        <div class="summary-label">Office Id</div>
                                        <div class="summary-value">{{ $office->id }}</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Inactive</div>
                                        <div class="summary-value">{{ (int) $office->status === 1 ? 'No' : 'Yes' }}</div>
                                    </div>
                                </div>

                                <!-- Tab Navigation -->
                                <div class="custom-tabs">
                                    <a href="javascript:void(0)" class="custom-tab active" data-tab="office-details">Office
                                        details</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="operations-users">Operations
                                        users</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="accounting-users">Accounting
                                        users</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="sales-users">Sales users</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="manager-users">Manager
                                        users</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="invoice-templates">Invoice
                                        templates</a>
                                    <a href="javascript:void(0)" class="custom-tab" data-tab="accounting-systems">Accounting
                                        systems</a>
                                </div>

                                <!-- Tab Content -->
                                <div id="office-details" class="tab-pane active">
                                    <form id="officeEditForm" action="{{ route('offices.update', $office->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <!-- 4-Pillar Form Content -->
                                        <div class="form-pillar-container"
                                            style="position: relative; padding-bottom: 80px;">
                                            <!-- Pillar 1: Office information -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Office information</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office name</label>
                                                    <div class="input-with-append">
                                                        <input type="text" name="office_name" class="form-control-custom"
                                                            value="{{ old('office_name', $office->office_name) }}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group-custom d-none">
                                                    <label class="form-label-custom">Company id</label>
                                                    <input type="text" class="form-control-custom" value="{{ $office->id }}"
                                                        readonly style="background: #f9fafb;">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office short name</label>
                                                    <input type="text" name="office_short_name" class="form-control-custom"
                                                        value="{{ old('office_short_name', $office->office_short_name) }}">
                                                </div>

                                                <div class="form-group-custom d-none">
                                                    <label class="form-label-custom">Customer number from FM</label>
                                                    <input type="text" name="customer_fm_number" class="form-control-custom"
                                                        value="{{ old('customer_fm_number') }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Phone number (with country
                                                        code)</label>
                                                    <input type="text" name="phone_number" class="form-control-custom"
                                                        value="{{ old('phone_number', $office->phone_number) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Email</label>
                                                    <input type="email" name="email" class="form-control-custom"
                                                        value="{{ old('email', $office->email) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">EORI number</label>
                                                    <input type="text" name="eori_number" class="form-control-custom"
                                                        value="{{ old('eori_number', $office->eori_number) }}">
                                                </div>
                                            </div>

                                            <!-- Pillar 2: Main address & Office address (Optional) -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Main address</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Office address</label>
                                                    <textarea name="address" class="form-textarea-custom"
                                                        rows="3">{{ old('address', $office->address) }}</textarea>
                                                </div>

                                                <div class="address-sub-grid">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">City</label>
                                                        <input type="text" name="city" class="form-control-custom"
                                                            value="{{ old('city', $office->city) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">District/state</label>
                                                        <input type="text" name="district_state" class="form-control-custom"
                                                            value="{{ old('district_state', $office->district_state) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Zip code</label>
                                                        <input type="text" name="zip_code" class="form-control-custom"
                                                            value="{{ old('zip_code', $office->zip_code) }}">
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Country</label>
                                                    <div class="input-with-append">
                                                        <x-forms.country-select
                                                            name="country_id"
                                                            :countries="$countries"
                                                            :value="$office->country_id"
                                                            wrapperClass=""
                                                            class="select2-flag"
                                                            dropdownParent=".pcoded-inner-content"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="form-section-header" style="margin-top: 20px;">Office address
                                                    (Optional)</div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Postal address (optional)</label>
                                                    <textarea name="postal_address" class="form-textarea-custom"
                                                        rows="3">{{ old('postal_address', $office->postal_address) }}</textarea>
                                                </div>

                                                <div class="address-sub-grid">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">City</label>
                                                        <input type="text" name="postal_city" class="form-control-custom"
                                                            value="{{ old('postal_city', $office->postal_city) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">District/state</label>
                                                        <input type="text" name="postal_district_state"
                                                            class="form-control-custom"
                                                            value="{{ old('postal_district_state', $office->postal_district_state) }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Zip code</label>
                                                        <input type="text" name="postal_zip_code"
                                                            class="form-control-custom"
                                                            value="{{ old('postal_zip_code', $office->postal_zip_code) }}">
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Country</label>
                                                    <div class="input-with-append">
                                                        <x-forms.country-select
                                                            name="office_country_id"
                                                            :countries="$countries"
                                                            :value="$office->office_country_id"
                                                            wrapperClass=""
                                                            class="select2-flag"
                                                            dropdownParent=".pcoded-inner-content"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pillar 3: Billing details -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Billing details</div>

                                                <div class="address-sub-grid" style="grid-template-columns: 1fr 1fr;">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Invoicing currency</label>
                                                        <select name="invoicing_currency" class="select2-simple"
                                                            style="width: 100%;">
                                                            <option value="">Select currency</option>
                                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                                <option value="{{ $curr }}" {{ old('invoicing_currency', $office->invoicing_currency) == $curr ? 'selected' : '' }}>
                                                                    {{ $curr }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Reporting currency</label>
                                                        <select name="reporting_currency" class="select2-simple"
                                                            style="width: 100%;">
                                                            <option value="">Select currency</option>
                                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                                <option value="{{ $curr }}" {{ old('reporting_currency', $office->reporting_currency) == $curr ? 'selected' : '' }}>
                                                                    {{ $curr }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">VAT rates</label>
                                                    <select name="vat_rates" class="form-control-custom">
                                                        <option value="Out of scope" {{ old('vat_rates', $office->vat_rates) == 'Out of scope' ? 'selected' : '' }}>Out of
                                                            scope</option>
                                                    </select>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">VAT country specific name</label>
                                                    <input type="text" name="vat_country_specific_name"
                                                        class="form-control-custom"
                                                        value="{{ old('vat_country_specific_name', $office->vat_country_specific_name) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">VAT number</label>
                                                    <input type="text" name="vat_number" class="form-control-custom"
                                                        value="{{ old('vat_number', $office->vat_number) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Invoicing e-mails</label>
                                                    <input type="email" name="invoicing_emails" class="form-control-custom"
                                                        value="{{ old('invoicing_emails', $office->invoicing_emails) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Heading of invoice</label>
                                                    <input type="text" name="heading_invoice" class="form-control-custom"
                                                        value="{{ old('heading_invoice', $office->heading_invoice) }}">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Information on invoice</label>
                                                    <textarea name="information_invoice" class="form-textarea-custom"
                                                        rows="5">{{ old('information_invoice', $office->information_invoice) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Pillar 4: Accounts -->
                                            <!-- Pillar 4: Accounts -->
                                            <div class="form-pillar">
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: start; padding: 5px; margin-top:-11px ;">
                                                    <div class="form-section-header"
                                                        style="border: none; margin-bottom: 0;">Accounts</div>
                                                    <button type="button" class="btn-add-account">Add account</button>
                                                </div>
                                                <div style="border-bottom: 1px solid #e5e7eb; margin-top: -11px;"></div>
                                                <div id="accounts-container">
                                                    @foreach($office->bankAccounts as $index => $account)
                                                        <div class="account-block">
                                                            <div
                                                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                                                <div class="form-section-header"
                                                                    style="border: none; margin-bottom: 0; font-size: 13px;">
                                                                    Account Detail #{{ $index + 1 }}</div>
                                                                <button type="button" class="remove-account-btn" title="Delete account">
                                                                    <i class="feather icon-trash-2"></i> Delete
                                                                </button>
                                                            </div>
                                                            <div
                                                                style="border-bottom: 1px solid #f3f4f6; margin-top: 8px; margin-bottom: 15px;">
                                                            </div>

                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Bank</label>
                                                                <input type="text" name="bank[]" class="form-control-custom"
                                                                    value="{{ $account->bank }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Currency</label>
                                                                <select name="currency[]" class="select-custom">
                                                                    <option value="">Select currency</option>
                                                                    @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                                        <option value="{{ $curr }}" {{ $account->currency == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">Account number</label>
                                                                <input type="text" name="account_number[]"
                                                                    class="form-control-custom"
                                                                    value="{{ $account->account_number }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">IBAN</label>
                                                                <input type="text" name="iban[]" class="form-control-custom"
                                                                    value="{{ $account->iban }}">
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label class="form-label-custom">SWIFT</label>
                                                                <input type="text" name="swift[]" class="form-control-custom"
                                                                    value="{{ $account->swift }}">
                                                            </div>
                                                            <div class="checkbox-group">
                                                                <input type="checkbox" class="main-account-checkbox" {{ $account->is_main_account ? 'checked' : '' }}>
                                                                <input type="hidden" name="is_main_account_status[]"
                                                                    class="main-account-hidden"
                                                                    value="{{ $account->is_main_account ? '1' : '0' }}">
                                                                <label class="checkbox-label">Set as main account</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Footer Actions -->
                                        <div class="edit-footer">
                                            <button type="submit" class="btn-save-custom">Save office</button>
                                            <a href="{{ route('offices.index') }}" class="btn-cancel-custom">Cancel</a>
                                            <div class="audit-info">
                                                @include('partials.audit-info', ['record' => $office, 'bold' => true])
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Placeholder Panes -->
                                <div id="operations-users" class="tab-pane">
                                    <div class="pane-header-actions"
                                        style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                                        <a href="{{ route('offices.operations_users.create', $office->id) }}"
                                            class="btn-pane-action"
                                            style="background: #1b5e6f; color: white; padding: 6px 15px; border-radius: 4px; font-size: 12px; text-decoration: none;">
                                            Add operation user
                                        </a>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'operations') as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.operations_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.operations_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                                <div id="sales-users" class="tab-pane">
                                    <div class="pane-header-actions"
                                        style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                                        <a href="{{ route('offices.sales_users.create', $office->id) }}"
                                            class="btn-pane-action"
                                            style="background: #1b5e6f; color: white; padding: 6px 15px; border-radius: 4px; font-size: 12px; text-decoration: none;">
                                            Add sales user
                                        </a>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'sales') as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.sales_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.sales_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'sales')->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
                                <div id="manager-users" class="tab-pane">
                                    <div class="pane-header-actions"
                                        style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                                        <a href="{{ route('offices.manager_users.create', $office->id) }}"
                                            class="btn-pane-action"
                                            style="background: #1b5e6f; color: white; padding: 6px 15px; border-radius: 4px; font-size: 12px; text-decoration: none;">
                                            Add manager user
                                        </a>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'manager') as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.manager_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.manager_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'manager')->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
                                <div id="accounting-users" class="tab-pane">
                                    <div class="pane-header-actions"
                                        style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                                        <a href="{{ route('offices.account_users.create', $office->id) }}"
                                            class="btn-pane-action"
                                            style="background: #1b5e6f; color: white; padding: 6px 15px; border-radius: 4px; font-size: 12px; text-decoration: none;">
                                            Add account user
                                        </a>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'account') as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.account_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.account_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'account')->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
                                <div id="invoice-templates" class="tab-pane">
                                    <div class="pane-header-actions">
                                        <button type="button" class="btn-pane-action" id="btn-add-template">Add
                                            template</button>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">Template name</th>
                                                <th style="width: 25%;">Last updated</th>
                                                <th style="width: 20%;">Updated by</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </div>
                                    <div class="no-data-wrapper">
                                        <i class="fa fa-info-circle no-data-icon"></i>
                                        <div class="no-data-text">No data to show</div>
                                    </div>
                                </div>
                                <div id="accounting-systems" class="tab-pane">
                                    <div class="pane-header-actions">
                                        <button type="button" class="btn-pane-action" id="btn-add-accounting">Add accounting
                                            system configuration</button>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 45%;">Name</th>
                                                <th style="width: 35%;">Accounting system</th>
                                                <th style="width: 15%;">Is current</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </div>
                                    <div class="no-data-wrapper">
                                        <i class="fa fa-info-circle no-data-icon"></i>
                                        <div class="no-data-text">No data to show</div>
                                    </div>
                                </div>

    @include('layouts.partials.pcoded-shell-end')

    <!-- Accounting System Modal -->
    <div class="custom-modal-overlay" id="modal-overlay">
        <div class="custom-modal">
            <div class="modal-body">
                <div class="modal-field">
                    <label class="modal-label">Name</label>
                    <input type="text" class="modal-input" placeholder="">
                </div>
                <div class="modal-field">
                    <label class="modal-label">Accounting system</label>
                    <select class="modal-input">
                        <option value="" selected disabled></option>
                        <option value="visma">Visma</option>
                        <option value="exact">Exact</option>
                        <option value="softone">Softone</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-save">Save</button>
                    <a href="javascript:void(0)" class="btn-modal-cancel" id="btn-close-modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Invoice Template Modal -->
    <div class="custom-modal-overlay" id="template-modal-overlay">
        <div class="custom-modal" style="width: 500px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-body">
                <div class="modal-field">
                    <label class="modal-label" style="color: #ef4444;">Template name</label>
                    <input type="text" class="modal-input error" value="">
                    <div class="modal-error-msg">Provide value</div>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Price charge</div>
                    <div id="price-charge-container">
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-add-row" id="btn-add-price">Add price charge</button>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Cost charge</div>
                    <div id="cost-charge-container">
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-add-row" id="btn-add-cost">Add cost charge</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-save">Save</button>
                    <a href="javascript:void(0)" class="btn-modal-cancel" id="btn-close-template-modal">Cancel</a>
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
            function fixedFooterOffset() {
                var $navbar = $('.pcoded-navbar');
                var sidebarWidth = $navbar.length ? $navbar.outerWidth() : 0;
                $('.edit-footer').css('left', sidebarWidth + 'px');
            }
            fixedFooterOffset();
            $(window).on('resize', fixedFooterOffset);
            $(document).on('click', '.mobile-menu, .pcoded-navbar .pcoded-navigatio-lavel, .navbar-wrapper .menu-toggle', function () {
                setTimeout(fixedFooterOffset, 300);
            });

            // Initialize Select2
            function initSelect2() {
                $('.select2-simple, .select-custom').each(function () {
                    if (!$(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $(this).parent()
                        });
                    }
                });
            }

            initSelect2();

            // Tab switching logic (keeps URL hash so redirects can restore the active tab)
            function activateOfficeTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.custom-tab[data-tab="' + tabId + '"]').length) {
                    return;
                }
                $('.custom-tab').removeClass('active');
                $('.custom-tab[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-pane').removeClass('active');
                $('#' + tabId).addClass('active');
            }

            $('.custom-tab').on('click', function () {
                var tabId = $(this).data('tab');
                activateOfficeTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateOfficeTab(hashTab);
            }

            // Dynamic Bank Accounts
            var accountCount = {{ $office->bankAccounts->count() }};

            $('.btn-add-account').click(function () {
                accountCount++;
                var currencyOptions = `
                                                <option value="">Select currency</option>
                                                @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                    <option value="{{ $curr }}">{{ $curr }}</option>
                                                @endforeach
                                            `;

                var $newAccount = $(`
                                                <div class="account-block">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                                        <div class="form-section-header" style="border: none; margin-bottom: 0; font-size: 13px;">Account Detail #${accountCount}</div>
                                                        <button type="button" class="remove-account-btn" title="Delete account">
                                                            <i class="feather icon-trash-2"></i> Delete
                                                        </button>
                                                    </div>
                                                    <div style="border-bottom: 1px solid #f3f4f6; margin-top: 8px; margin-bottom: 15px;"></div>

                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Bank</label>
                                                        <input type="text" name="bank[]" class="form-control-custom">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Currency</label>
                                                        <select name="currency[]" class="select-custom">
                                                            ${currencyOptions}
                                                        </select>
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Account number</label>
                                                        <input type="text" name="account_number[]" class="form-control-custom">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">IBAN</label>
                                                        <input type="text" name="iban[]" class="form-control-custom">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">SWIFT</label>
                                                        <input type="text" name="swift[]" class="form-control-custom">
                                                    </div>
                                                    <div class="checkbox-group">
                                                        <input type="checkbox" class="main-account-checkbox">
                                                        <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="0">
                                                        <label class="checkbox-label">Set as main account</label>
                                                    </div>
                                                </div>
                                            `);

                $('#accounts-container').append($newAccount);

                $newAccount.find('.select-custom').select2({
                    width: '100%',
                    dropdownParent: $newAccount.find('.select-custom').parent()
                });
            });

            $(document).on('click', '.remove-account-btn', function () {
                $(this).closest('.account-block').remove();
            });

            $(document).on('change', '.main-account-checkbox', function () {
                $('.main-account-checkbox').not(this).prop('checked', false);
                $('.main-account-hidden').val('0');
                if ($(this).is(':checked')) {
                    $(this).siblings('.main-account-hidden').val('1');
                }
            });

            // Modal logic (Cleanup)
            $('#btn-add-accounting').on('click', function () {
                $('#modal-overlay').css('display', 'flex');
            });

            $('#btn-close-modal, #modal-overlay').on('click', function (e) {
                if (e.target === this) $('#modal-overlay').hide();
            });
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#officeEditForm', 'fallbackUrl' => route('offices.index')])
@endsection