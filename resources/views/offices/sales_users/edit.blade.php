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
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding: 2px 8px;
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

        /* Essential for Select2 positioning fix */
        .select2-dropdown {
            z-index: 10001 !important;
            position: absolute !important;
        }

        .form-group-custom,
        .input-with-icon {
            position: relative !important;
            overflow: visible !important;
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

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }

        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Premium Index Styles */
        .page-title-link {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            background: #eef2ff;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .header-search-bar {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-inner {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-text {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .search-input-custom {
            border: 1px solid #f3f4f6;
            padding: 6px 12px;
            font-size: 13px;
            width: 200px;
            border-radius: 4px;
            color: #9ca3af;
        }

        .btn-add-office {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 6px 15px;
            font-size: 13px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-add-office:hover {
            background: #3b82f6;
            color: #fff;
        }

        .office-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .office-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .office-table td {
            padding: 12px 15px;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .office-name-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .office-name-link:hover {
            text-decoration: underline;
        }

        .badge-inactive {
            background: #e5e7eb;
            color: #6b7280;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .flag-icon {
            margin-right: 8px;
            vertical-align: middle;
            width: 18px;
        }

        .action-icon {
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
        }

        .action-icon:hover {
            color: #4b5563;
        }

        /* Premium Form & Tab Styles */
        .custom-tabs {
            display: flex;
            background: #e9ecef;
            border-bottom: 1px solid #ced4da;
            margin-bottom: 30px;
            margin: -3rem -3rem 30px -3rem;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .custom-tab {
            padding: 15px 30px;
            font-size: 13px;
            font-weight: 500;
            color: #1b5e6f;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border-top: 3px solid transparent;
            text-align: center;
        }

        .custom-tab.active {
            background: #fff;
            color: #1b5e6f;
            border-top-color: #00b5ad;
        }

        .custom-tab:hover:not(.active) {
            background: #dee2e6;
        }

        .user-form-container {
            max-width: 800px;
        }

        .form-group-custom {
            margin-bottom: 20px;
        }

        .form-label-custom {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3c485a;
            margin-bottom: 6px;
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid #f3f4f6;
            padding: 8px 12px;
            font-size: 13px;
            color: #4b5563;
            background: #fff;
            border-radius: 4px;
        }

        .form-control-custom.readonly {
            background: #fdfdfd;
            color: #9ca3af;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-btn {
            position: absolute;
            right: 8px;
            background: #e5e7eb;
            border-radius: 3px;
            padding: 2px 6px;
            color: #9ca3af;
            font-size: 10px;
            cursor: pointer;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
            cursor: pointer;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            border: 1px solid #1b5e6f;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1b5e6f;
        }

        .checkbox-custom i {
            color: #fff;
            font-size: 10px;
        }

        .checkbox-label {
            font-size: 12px;
            color: #1b5e6f;
            font-weight: 500;
        }

        .edit-footer {
            position: fixed;
            bottom: 0;
            left: 240px;
            right: 0;
            background: #fff;
            padding: 20px 30px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1000;
        }

        .btn-status-saved {
            background: #e5e7eb;
            color: #9ca3af;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .btn-cancel-custom {
            color: #3b82f6;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }

        .audit-info {
            position: absolute;
            right: 30px;
            bottom: 20px;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
            line-height: 1.4;
        }

        .tab-content-pane {
            display: none;
        }

        .tab-content-pane.active {
            display: block;
        }

        /* Vessels Tab Specific Styles */
        .vessels-search-container {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .vessels-search-label {
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }

        .vessels-search-input {
            border: 1px solid #e5e7eb;
            padding: 6px 12px;
            font-size: 12px;
            width: 150px;
            border-radius: 4px;
            color: #4b5563;
        }

        .vessels-table {
            width: 100%;
            border-collapse: collapse;
        }

        .vessels-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #e5e7eb;
            background: #fdfdfd;
        }

        .vessels-table td {
            padding: 8px 12px;
            font-size: 11px;
            color: #4b5563;
            border-bottom: 1px solid #f9fafb;
            vertical-align: middle;
        }

        .vessels-table tr:hover {
            background: #f9fafb;
        }

        .vessel-checkbox {
            width: 14px;
            height: 14px;
            border: 1px solid #ced4da;
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
                            <div class="card p-5 mt-5">
                                <!-- Tab Navigation -->
                                <div class="custom-tabs">
                                    <div class="custom-tab active" data-tab="user-details">User details</div>
                                    <div class="custom-tab" data-tab="vessels">Vessels</div>
                                    <div class="custom-tab" data-tab="user-access">User access</div>
                                </div>

                                <form
                                    action="{{ route('offices.sales_users.update', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                    method="POST" id="salesUserEditForm">
                                    @csrf
                                    @method('PUT')
                                    <!-- Tab Panes -->
                                    <div id="user-details" class="tab-content-pane active">
                                        <div class="user-form-container">
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Name</label>
                                                <div class="input-with-icon">
                                                    <input type="text" name="name" class="form-control-custom"
                                                        value="{{ old('name', $contact->name) }}" required>
                                                    <div class="input-icon-btn"><i class="fa fa-ellipsis-h"></i></div>
                                                </div>
                                            </div>
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Email</label>
                                                <input type="email" name="email" class="form-control-custom"
                                                    value="{{ old('email', $contact->email) }}" required>
                                            </div>
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                <input type="text" name="phone_number" class="form-control-custom"
                                                    value="{{ old('phone_number', $contact->phone_number) }}">
                                            </div>
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Reply to on emails</label>
                                                <input type="email" name="reply_to_email" class="form-control-custom"
                                                    value="{{ old('reply_to_email', $contact->reply_to_email) }}">
                                            </div>
                                            <div class="checkbox-container"
                                                style="display: flex; align-items: center; gap: 10px; margin-top: 25px;">
                                                <input type="checkbox" name="is_cc_enabled" id="is_cc_enabled" value="1" {{ old('is_cc_enabled', $contact->is_cc_enabled) ? 'checked' : '' }}
                                                    style="width: 18px; height: 18px; cursor: pointer;">
                                                <label for="is_cc_enabled" class="checkbox-label"
                                                    style="margin-bottom: 0; cursor: pointer; font-size: 12px; color: #1b5e6f; font-weight: 500;">Add
                                                    as CC on emails</label>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div id="vessels" class="tab-content-pane">
                                    <div class="vessels-search-container">
                                        <span class="vessels-search-label">Search</span>
                                        <input type="text" class="vessels-search-input" placeholder="type here">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="vessels-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;"><input type="checkbox" class="vessel-checkbox">
                                                    </th>
                                                    <th>Vessel</th>
                                                    <th>Customer</th>
                                                    <th>IMO</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $vessels = [
                                                        ['ALICE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9942720'],
                                                        ['BRITTA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9853046'],
                                                        ['CARL OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9484704'],
                                                        ['CEDRIC OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9591571'],
                                                        ['CLIVIA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9599195'],
                                                        ['CORA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9622916'],
                                                        ['CHIARA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9599171'],
                                                        ['CHRISTINE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9537898'],
                                                        ['EDGAR OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9794484'],
                                                        ['EDWARD OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702613'],
                                                        ['EIKE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9794472'],
                                                        ['ELSA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702625'],
                                                        ['ERNST OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702637'],
                                                        ['ERNA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9717870'],
                                                        ['EVA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9707754'],
                                                        ['GEBE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9727596'],
                                                        ['General Stock Oldendorff (for BGL SIN)', 'Oldendorff (BGL Singapore)', ''],
                                                        ['GINA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9942732'],
                                                        ['HAUKE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871115'],
                                                        ['HILLE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9731573'],
                                                        ['HEDWIG OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9742728'],
                                                        ['HEIDE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871103'],
                                                        ['HELGA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9713040'],
                                                        ['HENRY OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871086'],
                                                        ['HERMINE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9718375'],
                                                        ['JENS OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9852028'],
                                                        ['KENDRA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9849813'],
                                                        ['KIM OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9848998'],
                                                        ['KIRA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9867566'],
                                                        ['KLARA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9849007'],
                                                    ];
                                                @endphp
                                                @foreach($vessels as $vessel)
                                                    <tr>
                                                        <td><input type="checkbox" class="vessel-checkbox"></td>
                                                        <td>{{ $vessel[0] }}</td>
                                                        <td>{{ $vessel[1] }}</td>
                                                        <td>{{ $vessel[2] }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="user-access" class="tab-content-pane">
                                    <p style="font-size: 13px; color: #6b7280; padding: 20px 0;">User access content coming
                                        soon...</p>
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

    <div class="edit-footer">
        <button type="submit" form="salesUserEditForm" class="btn-save-custom"
            style="background: #1b5e6f; color: white; border: none; padding: 8px 30px; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer;">Save
            user</button>
        <a href="{{ route('offices.edit', $office) }}#sales-users" class="btn-cancel-custom">Cancel</a>
        <div class="audit-info">
            @include('partials.audit-info', ['record' => $contact, 'bold' => true])
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
            // Initialize Select2 with local anchoring
            $('.select2').each(function () {
                $(this).select2({
                    placeholder: "Click here",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this).parent()
                });
            });

            // Tab Switching Logic
            $('.custom-tab').on('click', function () {
                var tabId = $(this).data('tab');

                // Update tabs
                $('.custom-tab').removeClass('active');
                $(this).addClass('active');

                // Update panes
                $('.tab-content-pane').removeClass('active');
                $('#' + tabId).addClass('active');

                // Toggle Footer Visibility
                if (tabId === 'user-details') {
                    $('.edit-footer').show();
                } else {
                    $('.edit-footer').hide();
                }
            });

            // Vessels Search Logic
            $('.vessels-search-input').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $(".vessels-table tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Vessels Check-all Logic
            theadCheckbox = $('.vessels-table thead .vessel-checkbox');
            theadCheckbox.on('change', function () {
                var isChecked = $(this).prop('checked');
                $('.vessels-table tbody .vessel-checkbox').prop('checked', isChecked);
            });
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#salesUserEditForm', 'fallbackUrl' => route('offices.edit', $office->id) . '#sales-users'])
@endsection