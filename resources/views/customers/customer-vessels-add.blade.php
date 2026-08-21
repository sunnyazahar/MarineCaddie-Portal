@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

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

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            height: 30px !important;
            font-size: 11px;
            background-color: transparent !important;
            background: none !important;
            background-image: none !important;
            border: none !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da !important;
            height: 30px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding: 2px 8px;
            background-color: transparent !important;
            background: transparent !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
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

        .btn-filter-toggle:hover,
        .btn-filter-toggle:focus,
        .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        /* Internal Tab Navigation */
        .vessel-tabs {
            display: flex;
            background: #f4f7fa;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }

        .vessel-tabs .tab-item {
            padding: 12px 25px;
            font-size: 13px;
            color: #555;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            font-weight: 500;
        }

        .vessel-tabs .tab-item.active {
            color: #333;
            border-bottom-color: #01a9ac;
            background: #fff;
        }

        /* 4-Column Grid Layout */
        .vessel-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 40px;
            padding: 20px 30px;
        }

        .vessel-section-title {
            font-size: 14px;
            font-weight: 600;
            color: #004a99;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        /* Form Styling Overrides */
        .form-group label {
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            height: 32px;
            font-size: 13px;
            border: 1px solid #ced4da;
            border-radius: 2px;
            padding: 5px 10px;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 30px;
            margin-bottom: 20px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            font-size: 11px;
            color: #555;
        }

        .checkbox-item input {
            margin-right: 8px;
        }

        /* Metadata footer */
        .metadata-footer {
            margin-left: auto;
            text-align: right;
            font-size: 11px;
            color: #999;
            line-height: 1.4;
            margin-top: 0;
            padding-bottom: 0;
        }

        /* Action Buttons footer */
        .vessel-footer {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 185px;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .page-body {
            padding-bottom: 80px;
        }

        .btn-save-vessel {
            background: #ddd;
            border: none;
            color: #888;
            padding: 8px 25px;
            border-radius: 4px;
            font-size: 12px;
            cursor: not-allowed;
        }

        .btn-cancel-vessel {
            color: #01a9ac;
            font-size: 13px;
            text-decoration: none;
        }

        /* Links and custom icons */
        .add-contact-link {
            color: #01a9ac;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
            float: right;
        }

        .add-contact-link:hover {
            text-decoration: underline;
        }

        /* Dynamic Contact Card */
        .contact-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 12px;
            position: relative;
        }

        .contact-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .contact-card-header label {
            font-size: 12px;
            color: #01a9ac;
            font-weight: 600;
            margin-bottom: 0;
            margin-right: 10px;
            white-space: nowrap;
        }

        .contact-card-header .contact-select-wrap {
            flex: 1;
            position: relative;
        }

        .contact-card-header .contact-select-wrap select {
            width: 100%;
            height: 32px;
            font-size: 12px;
            border: 1px solid #ced4da;
            border-radius: 2px;
            padding: 4px 30px 4px 8px;
            appearance: auto;
        }

        .btn-remove-contact {
            background: none;
            border: none;
            font-size: 20px;
            color: #888;
            cursor: pointer;
            padding: 0 0 0 10px;
            line-height: 1;
        }

        .btn-remove-contact:hover {
            color: #333;
        }

        .contact-card .select2-container .select2-selection--single {
            background-color: transparent !important;
            border: 1px solid #ced4da !important;
        }

        .contact-card .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
        }

        .contact-checkboxes {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 5px;
        }

        .contact-checkboxes label {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: #333;
            font-weight: 400;
            cursor: pointer;
            margin-bottom: 0;
        }

        .contact-checkboxes label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            accent-color: #4e738e;
            cursor: pointer;
        }

        /* Validation Error Styling */
        label.error {
            color: #e74c3c !important;
            font-size: 10px !important;
            margin-top: 2px !important;
            font-weight: 400 !important;
        }

        input.error,
        select.error,
        textarea.error {
            border-color: #e74c3c !important;
        }

        .btn-save-vessel {
            background: #1b5e6f !important;
            color: #fff !important;
            cursor: pointer !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }

        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Tab Pane visibility */
        .vessel-tab-pane {
            display: none;
        }

        .vessel-tab-pane.active {
            display: block;
        }

        @media (max-width: 1199.98px) {
            .vessel-details-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 24px;
                padding: 20px 20px 100px;
            }
        }

        @media (max-width: 991.98px) {
            .vessel-tabs {
                display: flex !important;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                max-width: 100%;
            }

            .vessel-tabs .tab-item {
                flex: 0 0 auto;
                white-space: nowrap;
            }

            .vessel-details-grid {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
                padding: 12px 12px 110px !important;
                max-width: 100%;
                overflow-x: hidden;
            }

            .vessel-details-grid > div {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .vessel-details-grid > div > div[style*="display: flex"] {
                flex-direction: column !important;
                gap: 12px !important;
            }

            .vessel-details-grid > div > div[style*="display: flex"] > div,
            .vessel-details-grid .form-group[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
            }

            .checkbox-group {
                flex-direction: column;
                gap: 10px;
            }

            .vessel-footer {
                left: 0 !important;
                right: 0 !important;
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .btn-save-vessel,
            .btn-cancel-vessel {
                width: 100%;
                text-align: center;
            }

            .metadata-footer {
                margin-left: 0;
                width: 100%;
                text-align: left;
            }

            .add-contact-link {
                float: none;
                display: block;
                margin-top: 8px;
            }

            .contact-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .form-group label {
                white-space: normal;
                line-height: 1.3;
            }

            .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

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
                                <form id="vesselForm" method="POST" action="{{ route('customers.vessels.store', $id) }}">
                                    @csrf
                                    <div class="card">

                                        <div class="vessel-tab-content">
                                            <!-- Vessel Details Tab -->
                                            <div class="vessel-tab-pane active" id="vessel-details">
                                                <div class="vessel-details-grid">
                                                    <!-- Column 1: Vessel information -->
                                                    <div>
                                                        <div class="vessel-section-title">Vessel information</div>

                                                        <div class="form-group">
                                                            <label>Vessel</label>
                                                            <input type="text" name="vessel" class="form-control" value="">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Vessel name alias</label>
                                                            <input type="text" name="vessel_name_alias" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Vessel IMO</label>
                                                            <input type="text" name="vessel_imo" class="form-control">
                                                        </div>

                                                        <div style="display: flex; gap: 40px; margin-bottom: 20px;">
                                                            <div>
                                                                <div class="filter-label">Shipyard</div>
                                                                <input type="text" name="shipyard" class="form-control">
                                                            </div>
                                                            <div>
                                                                <div class="filter-label">Shipyard location</div>
                                                                <input type="text" name="shipyard_location"
                                                                    class="form-control">
                                                            </div>
                                                        </div>

                                                        <div class="checkbox-group">
                                                            <label class="checkbox-item"><input type="checkbox"
                                                                    name="not_in_transit" value="1"> Vessel is not in
                                                                transit</label>
                                                            <label class="checkbox-item"><input type="checkbox"
                                                                    name="inactive_vessel" value="1"> Inactive
                                                                vessel</label>
                                                            <label class="checkbox-item"><input type="checkbox"
                                                                    name="sanction_blocked" value="1"> Sanction
                                                                blocked</label>
                                                            <label class="checkbox-item"><input type="checkbox"
                                                                    name="financially_blocked" value="1"> Financially
                                                                blocked</label>
                                                            <label class="checkbox-item"><input type="checkbox"
                                                                    name="pre_payment_only" value="1"> Pre-payment
                                                                only</label>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Customer vessel code</label>
                                                            <input type="text" name="customer_vessel_code"
                                                                class="form-control" value="">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Vessel type alias</label>
                                                            <select name="vessel_type_alias" class="form-control select2">
                                                                <option></option>
                                                                <option value="MV">MV</option>
                                                                <option value="MT">MT</option>
                                                                <option value="LPG">LPG</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>PO example</label>
                                                            <input type="text" name="po_example" class="form-control">
                                                        </div>

                                                        <div style="display: flex; gap: 15px;">
                                                            <div class="form-group" style="flex: 1;">
                                                                <label>Internal shipment</label>
                                                                <select name="internal_shipment" class="form-control">
                                                                    <option></option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group" style="flex: 1;">
                                                                <label>Except from Hubs</label>
                                                                <div style="position: relative;">
                                                                    <select name="except_from_hubs" class="form-control">
                                                                        <option></option>
                                                                    </select>
                                                                    <span
                                                                        style="position: absolute; right: 2px; top: 50%; transform: translateY(-50%); background: #eee; padding: 2px 4px; border-radius: 2px; font-size: 9px;">
                                                                        <i class="ti-more-alt"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Remarks</label>
                                                            <textarea name="remarks" class="form-control"
                                                                style="height: 80px;"></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Column 2: Responsible managers -->
                                                    <div>
                                                        <div class="vessel-section-title">Responsible managers</div>

                                                        <div class="form-group">
                                                            <label>Manager (from customer)</label>
                                                            <select name="manager" class="form-control select2">
                                                                <option value=""></option>
                                                                @foreach($customerContacts as $contact)
                                                                    <option value="{{ $contact->name }}">{{ $contact->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Account manager</label>
                                                            <select id="account-manager-select" name="account_manager" class="form-control select2-account-manager">
                                                                <option value=""></option>
                                                                @if(old('account_manager'))
                                                                    <option value="{{ old('account_manager') }}" selected>{{ old('account_manager') }}</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="vessel-section-title" style="margin-top: 40px;">
                                                            Receivers of stocklists, pre-alert and notifications</div>

                                                        <div class="form-group">
                                                            <label>Receivers of stocklists and pre-alert</label>
                                                            <input type="text" name="receivers_stocklists"
                                                                class="form-control">
                                                        </div>

                                                        <div id="contact-cards-container"></div>

                                                        <a class="add-contact-link" id="btn-add-contact">Add contact</a>
                                                    </div>

                                                    <!-- Column 3: Invoice details -->
                                                    <div>
                                                        <div class="vessel-section-title">Invoice details</div>

                                                        <label class="checkbox-item" style="margin-bottom: 20px;">
                                                            <input type="checkbox" name="invoice_vessel_separately"
                                                                value="1"> Invoice vessel separately
                                                        </label>

                                                        <div class="form-group">
                                                            <label>Title for invoice recipient</label>
                                                            <input type="text" name="title_invoice_recipient"
                                                                class="form-control">
                                                        </div>

                                                        <div class="form-group" style="margin-top: 40px;">
                                                            <label>Yearly customer reference</label>
                                                            <input type="text" name="yearly_customer_reference"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                    <!-- Column 4: Home ports -->
                                                    <div>
                                                        <div class="vessel-section-title">Home ports</div>

                                                        <div class="form-group">
                                                            <label>Home consolidation port</label>
                                                            <input type="text" name="home_consolidation_port"
                                                                class="form-control">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Home delivery port</label>
                                                            <input type="text" name="home_delivery_port"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="vessel-footer">
                                            <button type="submit" class="btn-save-vessel">Save vessel</button>
                                            <a href="{{ route('customers.edit', $id) }}#vessels"
                                                class="btn-cancel-vessel">Cancel</a>
                                            <div class="metadata-footer"></div>
                                        </div>
                                    </div>
                                </form>
    @include('layouts.partials.pcoded-shell-end')

<!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {
            function fixedFooterOffset() {
                var isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                if (isMobile) {
                    $('.vessel-footer').css('left', '0px');
                    return;
                }
                var $navbar = $('.pcoded-navbar');
                var sidebarWidth = $navbar.length ? $navbar.outerWidth() : 0;
                $('.vessel-footer').css('left', sidebarWidth + 'px');
            }
            fixedFooterOffset();
            $(window).on('resize', fixedFooterOffset);
            $(document).on('click', '.mobile-menu, .pcoded-navbar .pcoded-navigatio-lavel, .navbar-wrapper .menu-toggle', function () {
                setTimeout(fixedFooterOffset, 300);
            });

            function formatAccountManager(item) {
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

            function formatAccountManagerSelection(item) {
                return item.text || item.id;
            }

            $('#account-manager-select').select2({
                placeholder: 'Select account manager',
                allowClear: true,
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
                        data.forEach(function (group) {
                            if (!group.children) {
                                return;
                            }

                            group.children.forEach(function (item) {
                                item.id = item.text;
                            });
                        });

                        return { results: data };
                    }
                },
                templateResult: formatAccountManager,
                templateSelection: formatAccountManagerSelection
            });

            // Initialize Select2 for standard filters
            $('select.select2').select2({
                placeholder: "Click here",
                allowClear: true
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

            // Vessel Tab Switching Logic
            $('.tab-item').on('click', function () {
                var target = $(this).data('target');

                // Update active tab item
                $('.tab-item').removeClass('active');
                $(this).addClass('active');

                // Update active tab pane
                $('.vessel-tab-pane').removeClass('active');
                $(target).addClass('active');
            });

            // =============================================
            // Dynamic Add Contact
            // =============================================
            var contactIndex = 0;

            $('#btn-add-contact').on('click', function (e) {
                e.preventDefault();
                // Allow only one contact card at a time
                if ($('#contact-cards-container .contact-card').length > 0) return;
                contactIndex++;
                var cardHtml =
                    '<div class="contact-card" id="contact-card-' + contactIndex + '">' +
                    '<div class="contact-card-header">' +
                    '<label>Contact</label>' +
                    '<div class="contact-select-wrap">' +
                    '<select name="contacts[' + contactIndex + '][contact_id]" class="form-control select2">' +
                    '<option value="">Select contact...</option>' +
                    @foreach($customerContacts as $contact)
                        '<option value="{{ $contact->id }}">{{ $contact->name }}</option>' +
                    @endforeach
                    '</select>' +
                    '</div>' +
                    '<span class="remove-contact" data-id="' + contactIndex + '">&times;</span>' +
                    '</div>' +
                    '<div class="contact-checkboxes">' +
                    '<label><input type="checkbox" name="contacts[' + contactIndex + '][stocklists]" value="1" checked> Stocklists</label>' +
                    '<label><input type="checkbox" name="contacts[' + contactIndex + '][pre_alerts]" value="1" checked> Pre-alerts</label>' +
                    '<label><input type="checkbox" name="contacts[' + contactIndex + '][stock_notifications]" value="1"> Stock notifications</label>' +
                    '<label><input type="checkbox" name="contacts[' + contactIndex + '][free_storage_notifications]" value="1"> Receive free storage notifications</label>' +
                    '<label><input type="checkbox" name="contacts[' + contactIndex + '][offers]" value="1"> Offers</label>' +
                    '</div>' +
                    '</div>';

                $('#contact-cards-container').append(cardHtml);

                // Initialize Select2 on the new contact dropdown for filtering
                $('#contact-card-' + contactIndex + ' select').select2({
                    placeholder: 'Select contact...',
                    allowClear: true,
                    width: '100%'
                });
            });

            // Remove contact card
            $(document).on('click', '.remove-contact', function () {
                var cardId = $(this).data('id');
                $('#contact-card-' + cardId).remove();
                $('#btn-add-contact').show();
            });

            // =============================================
            // Form Validation
            // =============================================
            $('#vesselForm').validate({
                rules: {
                    vessel: "required",
                    account_manager: "required"
                },
                messages: {
                    vessel: "Please enter vessel name",
                    account_manager: "Please enter account manager"
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-account-manager')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.hasClass('select2')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2') || $(element).hasClass('select2-account-manager')) {
                        $(element).next('.select2-container').find('.select2-selection').css('border-color', '#e74c3c');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2') || $(element).hasClass('select2-account-manager')) {
                        $(element).next('.select2-container').find('.select2-selection').css('border-color', '#ced4da');
                    }
                }
            });

            // Trigger validation on Select2 change
            $('select.select2, select.select2-account-manager').on('change', function () {
                $(this).valid();
            });
        });
    </script>
@endsection