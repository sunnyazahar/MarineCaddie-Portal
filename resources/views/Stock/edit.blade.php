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
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <style>
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #349dda;
            color: white;
        }

        .table-responsive {
            display: inline-block;
            width: 100%;
            overflow-x: auto;
            margin-top: 130px;
        }

        /* Hide Select2 clear button (X) */
        .select2-selection__clear {
            display: none !important;
        }

        /* Modern 3-Pane Layout for Edit Stock */
        .stock-edit-wrapper {
            display: flex;
            background: #f8fafc;
            min-height: calc(100vh - 120px);
            margin: -20px;
            /* Offset parent padding */
        }

        /* 1. Left Sidebar */
        .stock-sidebar {
            width: 200px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            margin-top: 75px;
            height: calc(100vh - 75px);
        }

        .sidebar-back {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-back a {
            color: #64748b;
            font-size: 11px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-list {
            flex: 1;
            overflow-y: auto;
        }

        .stock-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.2s;
            line-height: 1.2;
        }

        .stock-item:hover {
            background: #f8fafc;
        }

        .stock-item.active {
            background: #f0fdfa;
            border-left: 2px solid #0ea5e9;
        }

        .stock-item-id {
            font-weight: 600;
            font-size: 10px;
            color: #334155;
        }

        .stock-item-port {
            font-size: 10px;
            font-weight: 600;
            color: #94a3b8;
        }

        .stock-item-status {
            font-size: 10px;
            color: #64748b;
        }

        .stock-item-date {
            font-size: 10px;
            color: #94a3b8;
            text-align: right;
        }

        .stock-item-vessel {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Vessel Select Dropdown Styling */
        .vessel-result {
            padding: 4px 6px;
        }

        .vessel-result__name {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            line-height: 1.2;
        }

        .vessel-result__customer {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .select2-container--default .select2-results__option--highlighted .vessel-result__name,
        .select2-container--default .select2-results__option--highlighted .vessel-result__customer {
            color: #fff !important;
        }

        /* Delivery Irregularities: fixed-height multi-select with ellipsis */
        .select2-irreg-container {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            display: block !important;
        }

        .select2-irreg-container .select2-selection--multiple {
            height: 38px !important;
            max-height: 38px !important;
            overflow: hidden !important;
            border: 1px solid #d1d5db !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            background: #fff !important;
            width: 100% !important;
            min-width: 0 !important;
        }

        .select2-irreg-container .select2-selection__rendered {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            overflow: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 4px !important;
            margin: 0 !important;
            list-style: none !important;
            min-width: 0 !important;
            white-space: nowrap !important;
        }

        .select2-irreg-container .select2-selection__choice {
            display: inline-flex !important;
            align-items: center !important;
            height: 24px !important;
            margin: 2px !important;
            font-size: 10px !important;
            padding: 0 18px 0 5px !important;
            max-width: 100% !important;
            min-width: 50px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            background: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
            position: relative !important;
            color: #FFFFFF !important;
        }

        /* Ensure text inside choice also truncates if Select2 uses spans */
        .select2-irreg-container .select2-selection__choice span {
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }

        .select2-irreg-container .select2-selection__choice__remove {
            position: absolute !important;
            right: 4px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            margin: 0 !important;
        }

        .select2-irreg-container .select2-search--inline {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 1 !important;
            min-width: 30px !important;
        }

        .select2-irreg-container .select2-search__field {
            height: 24px !important;
            margin: 0 4px !important;
            width: 100% !important;
            max-width: 50px !important;
            min-width: 20px !important;
        }

        .select2-selection__clear,
        .select2-selection__choice__remove {
            display: none !important;
        }

        /* DGR Sub-row styles */
        .dgr-sub-row td,
        .irregularity-sub-row td {
            background-color: #fff !important;
            padding: 0 15px 15px 45px !important;
            border-top: none !important;
        }

        .dgr-container {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            background: #fdfdfd;
            border: 1px solid #edf2f7;
            border-radius: 4px;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
        }

        .dgr-warning-icon {
            color: #ef4444;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .dgr-field {
            flex: 1;
        }

        .dgr-field.small {
            flex: 0 0 120px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu {
            z-index: 9999 !important;
        }

        .stock-item-code {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* 2. Main Content Area */
        .stock-main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding-bottom: 80px;
            /* margin-top: 35px; */
            /* Space for fixed footer */
        }

        /* Summary Header - Fixed */
        .summary-header {
            background: #fff;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 60px;
            right: 0;
            left: 0;
            /* will be overridden by JS */
            z-index: 100;
            height: 75px;
        }

        @media (max-width: 991.98px) {
            .stock-edit-wrapper {
                margin: 0 !important;
                min-height: auto;
                flex-direction: column;
                width: 100%;
                max-width: 100%;
            }

            .stock-sidebar {
                display: none !important;
            }

            .stock-main-content {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
                overflow: visible !important;
                padding-bottom: 0px;
            }

            .summary-header {
                position: relative !important;
                top: auto !important;
                left: 0 !important;
                right: auto !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0;
                flex-wrap: wrap;
                gap: 10px;
                padding: 10px 12px;
            }

            .summary-info-group {
                flex-wrap: wrap;
                gap: 12px 16px !important;
                width: 100%;
            }

            .stock-tabs-container {
                position: relative !important;
                top: auto !important;
                left: 0 !important;
                right: auto !important;
                width: 100% !important;
                padding: 0 12px !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }

            .stock-tabs {
                display: inline-flex;
                flex-wrap: nowrap;
                gap: 20px;
                min-width: max-content;
            }

            .stock-form-scroll {
                padding: 12px !important;
                padding-top: 12px !important;
                margin-top: 5px !important;
                overflow: visible !important;
            }

            .edit-form-row {
                flex-direction: column;
                gap: 16px;
            }

            .edit-form-col {
                width: 100%;
                max-width: 100%;
            }

            .stock-right-panel {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 5px !important;
                padding: 12px !important;
                order: 3;
                border-left: none;
                border-top: 1px solid #e2e8f0;
            }

            .edit-footer {
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                padding: 12px !important;
                flex-wrap: wrap;
                gap: 10px;
            }

            .edit-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .edit-table {
                min-width: 900px !important;
            }

            table.edit-table[style*="min-width"] {
                min-width: 900px !important;
            }

            .page-wrapper.p-0,
            .page-body {
                overflow-x: hidden;
                max-width: 100%;
            }
        }

        @media (max-width: 575.98px) {
            .stock-tab {
                padding: 12px 4px;
                font-size: 12px;
            }

            .summary-header .header-actions,
            .summary-header .dropdown {
                width: 100%;
            }
        }

        .summary-info-group {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .summary-label {
            font-size: 10px;
            color: #64748b;
        }

        .summary-value {
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
        }

        .summary-value-bold {
            font-weight: 700;
            font-size: 13px;
        }

        .summary-link {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 500;
        }

        .summary-link:hover {
            text-decoration: underline;
        }

        /* Flags Tag */
        .summary-flag {
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: #475569;
            background: #fff;
            display: inline-block;
        }

        .summary-flag-landed {
            background: #dcf0fa;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
        }

        .status-badge {
            background: #dcfce7;
            color: #166534;
            padding: 4px 35px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .flag-icon {
            width: 18px;
            height: 12px;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid #eee;
        }

        /* Header Buttons */
        .summary-actions {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-header-outline {
            border: 1px solid #008080;
            color: #008080;
            background: transparent;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-header-outline:hover {
            background: #f0fdfa;
        }

        .btn-more-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #008080;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        /* More Dropdown */
        .more-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 280px;
            display: none;
            z-index: 1000;
            padding: 5px 0;
            border: 1px solid #e2e8f0;
        }

        .more-dropdown.show {
            display: block;
        }

        .dropdown-item-custom {
            padding: 10px 20px;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            transition: background 0.2s;
            text-align: left;
            line-height: normal;
        }

        .dropdown-item-custom:hover {
            background: #f1f5f9;
        }

        /* Tabs */
        .stock-tabs-container {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 25px;
            position: fixed;
            top: 120px;
            /* 75 navbar + ~68 header */
            right: 0;
            left: 0;
            /* will be overridden by JS */
            z-index: 99;
        }

        .stock-tabs {
            display: flex;
            gap: 30px;
        }

        .stock-tab {
            padding: 15px 5px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            position: relative;
        }

        .stock-tab.active {
            color: #008080;
        }

        .stock-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: #008080;
        }

        /* Form Scroll Area */
        .stock-form-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #fff;
            padding-top: 135px;
            margin-top: 5px;
            /* space for fixed header + tabs */
        }

        /* Form Layout (3 columns) */
        .edit-form-row {
            display: flex;
            gap: 30px;
            width: 100%;
            min-width: 0;
        }

        .edit-form-col {
            flex: 1;
            min-width: 0;
            overflow: visible;
            /* Prevent datepicker clipping */
        }

        .form-group-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #008080;
            margin-bottom: 15px;
        }

        .field-group {
            margin-bottom: 15px;
        }

        .field-label {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .field-input {
            width: 100%;
            height: 32px;
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #fff;
            position: relative;
        }

        .field-input-static {
            font-size: 13px;
            font-weight: 500;
            color: #0ea5e9;
            text-decoration: none;
        }

        /* Icon Input Wrapper */
        .icon-input-wrapper {
            position: relative;
        }

        .icon-input-wrapper .field-input {
            padding-right: 30px;
        }

        .icon-input-wrapper i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #0ea5e9;
            font-size: 14px;
            pointer-events: none;
        }

        /* Tables */
        .edit-table-container {
            margin-top: 30px;
        }

        .edit-table-title {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 12px;
        }

        .edit-table {
            width: 100%;
            border-collapse: collapse;
        }

        .edit-table th {
            text-align: left;
            padding: 10px;
            background: #f8fafc;
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
        }

        .edit-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }

        /* Fixed Footer Section */
        .edit-footer {
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
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-save-custom {
            background-color: #1b5e6f;
            color: white;
            border: none;
            padding: 8px 30px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        /* 3. Right Panel Sidebar */
        .stock-right-panel {
            width: 400px;
            background: #f8fafc;
            border-left: 1px solid #e2e8f0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
            margin-top: 110px;
        }

        .panel-card {
            background: #fff;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e2e8f0;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: #002d5b;
            margin-bottom: 10px;
        }

        #crr-doc-list {
            max-height: 220px;
            overflow-y: auto;
        }

        .crr-docs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding: 0 2px 0 0;
        }

        .crr-docs-header span {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        .crr-docs-header .crr-docs-internal-label {
            width: 56px;
            text-align: center;
            margin-right: 26px;
        }

        .doc-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #e8edf2;
        }

        .doc-item:last-child {
            border-bottom: 0;
        }

        .doc-main {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .doc-name {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            color: #008080 !important;
            line-height: 16px;
            text-decoration: none;
            word-break: break-word;
        }

        .doc-name:hover {
            text-decoration: underline;
        }

        .doc-type-select {
            display: block;
            margin-top: 4px;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            padding: 0;
            max-width: 100%;
            width: 100%;
            cursor: pointer;
            appearance: auto;
        }

        #crr-documents-panel .select2-container {
            display: block;
            margin-top: 4px;
            width: 100% !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single {
            border: 0 !important;
            background: transparent !important;
            background-color: transparent !important;
            height: 18px !important;
            min-height: 18px !important;
            display: block !important;
            align-items: unset !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #64748b !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            line-height: 18px;
            padding: 0 16px 0 0 !important;
            background: transparent !important;
            background-color: transparent !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 18px;
            width: 14px;
            right: 0;
            top: 0;
        }

        .select2-dropdown.doc-type-select2-dropdown {
            z-index: 20060;
            min-width: 220px;
            font-size: 11px;
        }

        .select2-dropdown.doc-type-select2-dropdown .select2-results__options {
            max-height: 280px;
        }

        .doc-side {
            flex: 0 0 auto;
            width: 82px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        .doc-side-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            height: 18px;
            width: 100%;
        }

        .doc-internal {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
        }

        #crr-documents-panel .doc-internal.checkbox-fade {
            display: inline-block;
            line-height: 0;
        }

        #crr-documents-panel .doc-internal.checkbox-fade label {
            margin: 0;
            padding: 0;
            display: inline-flex;
            align-items: center;
            line-height: 0;
            cursor: pointer;
        }

        #crr-documents-panel .doc-internal.checkbox-fade input[type="checkbox"] {
            display: none;
        }

        #crr-documents-panel .doc-internal.checkbox-fade .cr {
            border: 2px solid #d1d5db;
            cursor: pointer;
            display: inline-block;
            height: 18px;
            width: 18px;
            margin: 0;
            position: relative;
            background: #fff;
            border-radius: 3px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        #crr-documents-panel .doc-internal.checkbox-fade .cr .cr-icon {
            color: #fff !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            left: 50%;
            position: absolute;
            top: 50%;
            margin-left: -5.5px;
            margin-top: -5.5px;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: scale(0);
            opacity: 0;
            line-height: normal;
        }

        #crr-documents-panel .doc-internal.checkbox-fade input[type="checkbox"]:checked + .cr {
            border-color: #008080 !important;
            background: #008080 !important;
        }

        #crr-documents-panel .doc-internal.checkbox-fade input[type="checkbox"]:checked + .cr .cr-icon {
            transform: scale(1);
            opacity: 1;
        }

        .doc-trash {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
        }

        .doc-trash:hover {
            color: #ef4444;
        }

        .doc-date {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
            white-space: nowrap;
            text-align: right;
            min-height: 12px;
        }

        /* Drag & Drop Placeholder */
        .dropzone-placeholder {
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 22px 16px;
            text-align: center;
            margin-top: 14px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dropzone-placeholder:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .dropzone-text {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .dropzone-placeholder .dropzone-icon {
            font-size: 18px;
            color: #94a3b8;
            display: block;
        }

        /* Select2 Overrides matches previous ones */
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #e2e8f0 !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #334155 !important;
            font-size: 12px !important;
            padding-left: 10px !important;
            background-color: #fff !important;
        }

        /* Premium Datepicker Styling */
        .ui-datepicker {
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 0;
            font-family: inherit;
            font-size: 11px;
            z-index: 9999 !important;
            width: 250px;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 5px;
        }

        .ui-datepicker-header {
            background: #008080;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ui-datepicker-title {
            font-weight: 700;
            text-align: center;
            width: 100%;
        }

        .ui-datepicker-prev,
        .ui-datepicker-next {
            cursor: pointer;
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            text-align: center;
            position: absolute !important;
            top: 8px !important;
        }

        .ui-datepicker-prev {
            left: 10px;
        }

        .ui-datepicker-next {
            right: 10px;
        }

        .ui-datepicker-prev:hover,
        .ui-datepicker-next:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .ui-datepicker-calendar {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .ui-datepicker-calendar th {
            color: #64748b;
            font-weight: 600;
            padding: 8px 0;
            text-align: center;
        }

        .ui-datepicker-calendar td {
            padding: 2px;
            text-align: center;
        }

        .ui-datepicker-calendar td a {
            display: block;
            padding: 6px;
            text-decoration: none;
            color: #334155;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .ui-datepicker-calendar td a:hover {
            background: #f0fdfa;
            color: #008080;
        }

        .ui-datepicker-calendar .ui-state-active {
            background: #008080 !important;
            color: #fff !important;
            font-weight: 700;
        }

        .ui-datepicker-calendar .ui-datepicker-today a {
            border: 1px solid #008080;
            color: #008080;
        }

        /* CRR Form Specific Styles from Create-CRR */
        .crr-table-header {
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            margin: 30px 0 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .crr-data-table {
            width: 100%;
            margin-top: 10px;
            background: #fff;
            border: 1px solid #f3f4f6;
            border-collapse: collapse;
        }

        .crr-data-table th {
            background-color: #fdfdfd;
            padding: 10px;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .crr-data-table td {
            padding: 8px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .crr-input {
            width: 100%;
            height: 28px;
            padding: 2px 8px;
            font-size: 11px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            outline: none;
        }

        .crr-input:focus {
            border-color: #008080;
        }

        .crr-input[readonly] {
            background-color: #f9fafb;
            cursor: not-allowed;
        }

        .btn-outline-teal {
            font-size: 11px;
            padding: 5px 15px;
            border-radius: 2px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-teal:hover {
            background: #1b5e6f;
            color: #fff;
        }

        /* Selected value (input box) */
        .select2-container .select2-selection--single .select2-selection__rendered {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            display: block;
        }

        /* Dropdown options */
        .select2-results__option {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080 !important;
        }

        .select2-selection__clear {
            display: none !important;
        }

        /* Supplier Select2 Styles */
        .select2-result-supplier {
            padding: 4px;
        }

        .select2-result-supplier__title {
            font-weight: 600;
            font-size: 11px;
            color: #334155;
            line-height: normal;
        }

        .select2-result-supplier__location {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
            margin-top: 1px;
        }

        .select2-container--default .select2-results__option--highlighted .select2-result-supplier__title,
        .select2-container--default .select2-results__option--highlighted .select2-result-supplier__location {
            color: #fff !important;
        }

        .select2-result-supplier-add {
            color: #008080;
            font-weight: 600;
            font-size: 12px;
        }

        .select2-container--default .select2-results__option--highlighted .select2-result-supplier-add {
            color: #fff !important;
        }

        .supplier-add-link {
            display: block;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
        }

        .supplier-add-link:hover {
            text-decoration: underline;
        }

        .add-supplier-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }

        .add-supplier-section-title {
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            padding-bottom: 6px;
            border-bottom: 2px solid #1b5e6f;
            margin-bottom: 10px;
        }

        #add-supplier-modal .field-input {
            width: 100%;
        }

        #add-supplier-modal .select2-container {
            width: 100% !important;
        }

        @media (max-width: 992px) {
            .add-supplier-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Hub Select2 Styles */
        .select2-result-hub {
            padding: 4px;
        }

        .select2-result-hub__title {
            font-weight: 600;
            font-size: 11px;
            color: #334155;
            line-height: normal;
        }

        .select2-result-hub__location {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
            margin-top: 1px;
        }

        .select2-container--default .select2-results__option--highlighted .select2-result-hub__title,
        .select2-container--default .select2-results__option--highlighted .select2-result-hub__location {
            color: #fff !important;
        }
    </style>
@endsection

@section('content')
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <!-- ... existing loader ... -->
        <div class="ball-scale">
            <div class='contain'>
                @for($i = 0; $i < 10; $i++)
                    <div class="ring">
                        <div class="frame"></div>
                </div> @endfor
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])
                        <div class="stock-edit-wrapper">
                            <!-- 1. LEFT SIDEBAR: Stock List -->
                            <div class="stock-sidebar" style="display:none">
                                <div class="sidebar-back">
                                    <a href="{{ route('stocks') }}"><i class="fa fa-chevron-left"></i> Back to full list</a>
                                </div>
                                <div class="stock-list">
                                    @php
                                        $sidebarItems = [
                                            ['id' => 'SIN1-61699936', 'status' => 'Pending', 'vessel' => 'Stolt Virtue', 'code' => '1024605', 'date' => '08.03.2026', 'port' => 'SIN1'],
                                            ['id' => 'SIN1-61699935', 'status' => 'Pending', 'vessel' => 'Stolt Virtue', 'code' => '1024604', 'date' => '08.03.2026', 'port' => 'SIN1'],
                                            ['id' => 'SIN1-61699934', 'status' => 'Pending', 'vessel' => 'Stolt Virtue', 'code' => '1024602', 'date' => '08.03.2026', 'port' => 'SIN1'],
                                            ['id' => 'SIN1-61699933', 'status' => 'Pending', 'vessel' => 'Stolt Virtue', 'code' => '1024601', 'date' => '08.03.2026', 'port' => 'SIN1'],
                                            ['id' => 'SIN1-61699932', 'status' => 'Pending', 'vessel' => 'Stolt Virtue', 'code' => '1024596', 'date' => '08.03.2026', 'port' => 'SIN1'],
                                        ];
                                    @endphp
                                    @foreach($sidebarItems as $item)
                                        <div class="stock-item {{ $item['id'] == 'SIN1-61699936' ? 'active' : '' }}">
                                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                                <div class="stock-item-id">{{ $item['id'] }}</div>
                                                <div class="stock-item-port">{{ $item['port'] }}</div>
                                            </div>
                                            <div
                                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 1px;">
                                                <div class="stock-item-status">{{ $item['status'] }}</div>
                                                <div class="stock-item-date">{{ $item['date'] }}</div>
                                            </div>
                                            <div class="stock-item-vessel">{{ $item['vessel'] }}</div>
                                            <div class="stock-item-code">{{ $item['code'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 2. MAIN CONTENT AREA -->
                            <div class="stock-main-content">
                                <form action="{{ route('stocks.crr.update', $crr->id) }}" method="POST" id="crrEditForm">
                                    @csrf
                                    @method('PUT')
                                    @if ($errors->any() || session('error'))
                                        <div id="crr-form-errors" style="margin: 0 0 16px; padding: 10px 14px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 13px;">
                                            @if (session('error'))
                                                <div>{{ session('error') }}</div>
                                            @endif
                                            @foreach ($errors->all() as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Summary Header -->
                                    <div class="summary-header">
                                        <div class="summary-info-group">
                                            <div class="summary-item">
                                                <span class="summary-label">Stock number</span>
                                                <span
                                                    class="summary-value summary-value-bold">{{ $crr->stock_number }}</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Registration date</span>
                                                <span class="summary-value">{{ $crr->created_at->format('d.m.Y') }}</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Registered by</span>
                                                <span class="summary-value">{{ $crr->registeredBy?->name ?? '—' }}</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Account manager</span>
                                                <span class="summary-value" id="summary-account-manager">
                                                    {{ $crr->customerVessel?->customer?->responsible?->accountManager?->name ?? '—' }}
                                                </span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Flags</span>
                                                <div class="header-inline-edit" id="flags-edit-container">
                                                    <div class="header-inline-display flags-display" style="display: flex; gap: 5px; align-items: center;">
                                                        <div class="flags-pills" style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                                                            <div class="summary-flag-landed" id="header-landed-flag" {!! $crr->is_landed_goods ? '' : 'style="display: none;"' !!}>Landed</div>
                                                            @php
                                                                $stockFlags = $crr->flags ?? \App\Models\Crr::defaultFlags();
                                                            @endphp
                                                            @forelse ($stockFlags as $flag)
                                                                <span class="summary-flag">{{ $flag }}</span>
                                                            @empty
                                                                <span class="text-muted" style="font-size: 11px;">—</span>
                                                            @endforelse
                                                        </div>
                                                        <i class="ti-pencil-alt" style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                    </div>
                                                    <div class="header-inline-select flags-select-wrapper" style="display: none; min-width: 180px;">
                                                        <select class="select2-flags-inline" name="header_flags[]">
                                                            @foreach (\App\Models\Crr::availableFlags() as $flagOption)
                                                                <option value="{{ $flagOption }}" {{ in_array($flagOption, $stockFlags, true) ? 'selected' : '' }}>{{ $flagOption }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Status</span>
                                                <div style="display: flex; align-items: center; gap: 8px;"
                                                    id="status-edit-container">
                                                    <div class="status-display"
                                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                                        <span class="status-badge stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</span>
                                                        <i class="ti-pencil-alt"
                                                            style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                    </div>
                                                    <div class="status-select-wrapper"
                                                        style="display: none; min-width: 150px;">
                                                        <select class="select2-status-inline" name="status">
                                                            @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                                                <option value="{{ $value }}" {{ $crr->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="summary-actions">
                                            <button type="button"
                                                id="accept-crr-btn"
                                                class="btn btn-header-outline"
                                                data-stock-number="{{ $crr->stock_number }}"
                                                data-accept-url="{{ route('stocks.crr.update-accept', $crr->id) }}"
                                                {{ $crr->accept ? 'disabled' : '' }}>
                                                {{ $crr->accept ? 'Accepted' : 'Accept CRR' }}
                                            </button>
                                            <a href="{{ route('stocks.print-labels', $crr->id) }}" target="_blank"
                                                class="btn btn-header-outline js-print-with-location"
                                                style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Print
                                                labels</a>
                                            
                                        </div>
                                    </div>

                                    <!-- Tabs -->
                                    <div class="stock-tabs-container mt-2">
                                        <div class="stock-tabs">
                                            <div class="stock-tab active" data-tab="stock-details">Stock details</div>
                                            <div class="stock-tab" data-tab="line-items">Line items</div>
                                            <div class="stock-tab" data-tab="irregularities">Irregularities</div>
                                        </div>
                                    </div>

                                    <!-- Main Form Content -->
                                    <div id="stock-details" class="stock-tab-content active">
                                        <div class="stock-form-scroll">
                                            <div class="edit-form-row">
                                                <!-- Column 1 -->
                                                <div class="edit-form-col">
                                                    <div class="form-group-title">
                                                        <i class="fa fa-ship"></i> Vessel
                                                    </div>
                                                    <div class="field-group">
                                                        <select class="field-input select2-vessel" name="vessel_name">
                                                            <option value=""></option>
                                                            @foreach($vessels as $vessel)
                                                                <option value="{{ $vessel->vessel }}"
                                                                    data-customer="{{ optional($vessel->customer)->customer_name }}"
                                                                    data-account-manager="{{ $vessel->customer?->responsible?->accountManager?->name }}"
                                                                    {{ $crr->vessel_name == $vessel->vessel ? 'selected' : '' }}>
                                                                    {{ $vessel->vessel }}
                                                                </option>
                                                            @endforeach
                                                            @if($crr->vessel_name && !$vessels->pluck('vessel')->contains($crr->vessel_name))
                                                                <option value="{{ $crr->vessel_name }}" selected>
                                                                    {{ $crr->vessel_name }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="field-group" id="vessels-customer-name-group" style="display: none;">
                                                        <label class="field-label">Vessel customer name</label>
                                                        <input type="text" id="vessels_customer_name"
                                                            name="vessels_customer_name" readonly class="field-input">
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">PO numbers (Separate by commas or
                                                            spaces)</label>
                                                        <input type="text" class="field-input" name="po_numbers"
                                                            value="{{ is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '') }}">
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">PO remarks</label>
                                                        <select class="field-input select2" name="po_remarks">
                                                            <option value=""></option>
                                                            @php
                                                                $poRemarksOptions = [
                                                                    "Awaiting supplier confirmation",
                                                                    "Backordered",
                                                                    "Cancelled by supplier",
                                                                    "Consolidated shipment",
                                                                    "Delivery delayed",
                                                                    "Delivery on hold",
                                                                    "Incomplete delivery",
                                                                    "Incorrect item received",
                                                                    "Partial delivery",
                                                                    "Priority shipment",
                                                                    "Short shipment",
                                                                    "Split delivery",
                                                                    "Urgent delivery required",
                                                                ];
                                                            @endphp
                                                            @foreach($poRemarksOptions as $opt)
                                                                <option value="{{ $opt }}" {{ $crr->po_remarks == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Content</label>
                                                        <input type="text" class="field-input" name="content"
                                                            value="{{ $crr->content }}">
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">First mile updates</label>
                                                        <select class="field-input select2" name="first_mile_updates">
                                                            <option></option>
                                                            @php
                                                                $mileUpdates = [
                                                                    "Emailed to supplier",
                                                                    "Emailed to supplier for missing commercial invoice",
                                                                    "Reminder 1 for missing commercial invoice",
                                                                    "Reminder 2 for missing commercial invoice",
                                                                    "Reminder 1 sent to supplier",
                                                                    "Reminder 2 sent to supplier",
                                                                    "Reminder 3 sent to supplier; escalate",
                                                                    "Marked as pick-up",
                                                                    "No supplier email address",
                                                                    "No reply from supplier",
                                                                    "Not delivered on time",
                                                                    "Unknown supplier"
                                                                ];
                                                            @endphp
                                                            @foreach($mileUpdates as $update)
                                                                <option value="{{ $update }}" {{ $crr->first_mile_updates == $update ? 'selected' : '' }}>
                                                                    {{ $update }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">First mile comment</label>
                                                        <input type="text" class="field-input" name="first_mile_comment"
                                                            value="{{ $crr->first_mile_comment }}">
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="edit-form-col">
                                                    <div class="form-group-title">
                                                        <i class="fa fa-briefcase"></i> Supplier
                                                    </div>
                                                    <div class="field-group">
                                                        <div id="supplier-select-wrapper" {!! $crr->is_landed_goods ? 'style="display: none;"' : '' !!}>
                                                            <select class="field-input select2-supplier" name="supplier"
                                                                id="supplier-select" {!! $crr->is_landed_goods ? 'disabled' : '' !!}>
                                                                <option></option>
                                                                @foreach($suppliers as $s)
                                                                    <option value="{{ $s->supplier_name }}"
                                                                        data-known="1"
                                                                        data-address="{{ $s->supplier_address }}"
                                                                        data-city="{{ $s->city }}"
                                                                        data-country="{{ optional($s->country)->name }}" {{ $crr->supplier == $s->supplier_name ? 'selected' : '' }}>
                                                                        {{ $s->supplier_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div id="supplier-input-wrapper" {!! $crr->is_landed_goods ? '' : 'style="display: none;"' !!}>
                                                            <input type="text" class="field-input" name="supplier"
                                                                value="{{ $crr->is_landed_goods ? $crr->supplier : 'EX VESSEL' }}"
                                                                readonly id="supplier-input" {!! $crr->is_landed_goods ? '' : 'disabled' !!}>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Expected delivery date</label>
                                                                <div class="icon-input-wrapper">
                                                                    <input type="text" class="field-input datepicker"
                                                                        name="expected_delivery_date"
                                                                        value="{{ $crr->expected_delivery_date }}"
                                                                        placeholder="YYYY-MM-DD">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Actual delivery date</label>
                                                                <div class="icon-input-wrapper">
                                                                    <input type="text" class="field-input datepicker"
                                                                        name="actual_delivery_date"
                                                                        value="{{ $crr->actual_delivery_date }}"
                                                                        placeholder="YYYY-MM-DD">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Supplier reference</label>
                                                        <input type="text" class="field-input" name="supplier_reference"
                                                            value="{{ $crr->supplier_reference }}">
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Deadline warehouse</label>
                                                                <div class="icon-input-wrapper">
                                                                    <input type="text" class="field-input datepicker"
                                                                        name="deadline_warehouse"
                                                                        value="{{ $crr->deadline_warehouse }}"
                                                                        placeholder="YYYY-MM-DD">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group"
                                                                style="margin-top: 25px; display: none;">
                                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                                    <input type="checkbox" id="landed-goods-check"
                                                                        name="is_landed_goods" {{ $crr->is_landed_goods ? 'checked' : '' }}>
                                                                    <label for="landed-goods-check" class="field-label mb-0"
                                                                        style="color: #475569;">Landed goods</label>
                                                                </div>
                                                            </div>
                                                            <div id="landed-vessel-wrapper" class="mt-2" {!! $crr->is_landed_goods ? '' : 'style="display: none;"' !!}>
                                                                <div class="field-group">
                                                                    <label class="field-label">Landed from vessel</label>
                                                                    <select class="field-input select2-vessel"
                                                                        name="landed_from_vessel" id="landed-from-vessel">
                                                                        <option value=""></option>
                                                                        @foreach($vessels as $vessel)
                                                                            <option value="{{ $vessel->vessel }}"
                                                                                data-customer="{{ optional($vessel->customer)->customer_name }}"
                                                                                {{ $crr->landed_from_vessel == $vessel->vessel ? 'selected' : '' }}>
                                                                                {{ $vessel->vessel }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Internal shipment</label>
                                                        <select class="field-input select2" name="internal_shipment">
                                                            <option></option>
                                                            <option value="ETL" {{ $crr->internal_shipment == 'ETL' ? 'selected' : '' }}>ETL</option>
                                                            <option value="KTL" {{ $crr->internal_shipment == 'KTL' ? 'selected' : '' }}>KTL</option>
                                                            <option value="RTL" {{ $crr->internal_shipment == 'RTL' ? 'selected' : '' }}>RTL</option>
                                                        </select>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Delivery Irregularities</label>
                                                        <select class="field-input select2 select2-irregularities"
                                                            name="delivery_irregularities[]">
                                                            <option value="Yes" {{ is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities) ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ is_array($crr->delivery_irregularities) && in_array('No', $crr->delivery_irregularities) ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Incoterm</label>
                                                        <select class="field-input select2-incoterm" name="incoterm">
                                                            <option value=""></option>
                                                            @foreach([
                                                                'CFR - Cost and Freight',
                                                                'CIF - Cost, Insurance and Freight',
                                                                'CIP - Carriage and Insurance Paid To',
                                                                'CPT - Carriage Paid To',
                                                                'DAP - Delivered at Place',
                                                                'DDP - Delivered Duty Paid',
                                                                'DDU - Delivered Duty Unpaid',
                                                                'DPU - Delivered at Place Unloaded',
                                                                'EXW - Ex Works',
                                                                'FAS - Free Alongside Ship',
                                                                'FCA - Free Carrier',
                                                                'FOB - Free On Board',
                                                            ] as $incoterm)
                                                                <option value="{{ $incoterm }}" {{ $crr->incoterm === $incoterm ? 'selected' : '' }}>{{ $incoterm }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="edit-form-col">
                                                    <div class="form-group-title">
                                                        <i class="fa fa-map-marker"></i> Hub & Transit
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label">Hub / agent</label>
                                                        <select class="field-input select2-hub" name="hub_agent">
                                                            <option></option>
                                                            <optgroup label="Hubs">
                                                                @foreach($hubs as $hub)
                                                                    <option value="{{ $hub->code }}" data-code="{{ $hub->code }}"
                                                                        data-city="{{ $hub->city }}"
                                                                        data-country="{{ optional($hub->country)->name }}" {{ ($crr->hub_agent == $hub->code || $crr->hub_agent == $hub->hub_name) ? 'selected' : '' }}>
                                                                        {{ $hub->code }} - {{ $hub->hub_name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="Agents">
                                                                @foreach($agents as $agent)
                                                                    <option value="{{ $agent->code }}" data-code="{{ $agent->code }}"
                                                                        data-city="{{ $agent->city }}"
                                                                        data-country="{{ optional($agent->country)->name }}" {{ ($crr->hub_agent == $agent->code || $crr->hub_agent == $agent->agent_name) ? 'selected' : '' }}>
                                                                        {{ $agent->code }} - {{ $agent->agent_name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                    </div>

                                                    <div class="field-group">
                                                        <label class="field-label">Physical Location</label>
                                                        <input type="text" class="field-input" name="location"
                                                            value="{{ old('location', $crr->location) }}">
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Transit type</label>
                                                                <select class="field-input select2" name="transit_type">
                                                                    <option value=""></option>
                                                                    <option value="AMAZON" {{ $crr->transit_type == 'AMAZON' ? 'selected' : '' }}>AMAZON</option>
                                                                    <option value="AWB" {{ $crr->transit_type == 'AWB' ? 'selected' : '' }}>AWB</option>
                                                                    <option value="B/L" {{ $crr->transit_type == 'B/L' ? 'selected' : '' }}>B/L</option>
                                                                    <option value="CADO" {{ $crr->transit_type == 'CADO' ? 'selected' : '' }}>CADO</option>
                                                                    <option value="CMR" {{ $crr->transit_type == 'CMR' ? 'selected' : '' }}>CMR</option>
                                                                    <option value="DHL" {{ $crr->transit_type == 'DHL' ? 'selected' : '' }}>DHL</option>
                                                                    <option value="DHL E+" {{ $crr->transit_type == 'DHL E+' ? 'selected' : '' }}>DHL E+</option>
                                                                    <option value="DPD" {{ $crr->transit_type == 'DPD' ? 'selected' : '' }}>DPD</option>
                                                                    <option value="DSC" {{ $crr->transit_type == 'DSC' ? 'selected' : '' }}>DSC</option>
                                                                    <option value="DSV" {{ $crr->transit_type == 'DSV' ? 'selected' : '' }}>DSV</option>
                                                                    <option value="FEDEX" {{ $crr->transit_type == 'FEDEX' ? 'selected' : '' }}>FEDEX</option>
                                                                    <option value="GLS" {{ $crr->transit_type == 'GLS' ? 'selected' : '' }}>GLS</option>
                                                                    <option value="MSX" {{ $crr->transit_type == 'MSX' ? 'selected' : '' }}>MSX</option>
                                                                    <option value="MT ref" {{ $crr->transit_type == 'MT ref' ? 'selected' : '' }}>MT ref</option>
                                                                    <option value="Other" {{ $crr->transit_type == 'Other' ? 'selected' : '' }}>Other</option>
                                                                    <option value="SF" {{ $crr->transit_type == 'SF' ? 'selected' : '' }}>SF</option>
                                                                    <option value="TNT" {{ $crr->transit_type == 'TNT' ? 'selected' : '' }}>TNT</option>
                                                                    <option value="UPS" {{ $crr->transit_type == 'UPS' ? 'selected' : '' }}>UPS</option>
                                                                    <option value="USPS" {{ $crr->transit_type == 'USPS' ? 'selected' : '' }}>USPS</option>
                                                                    <option value="VIVAR" {{ $crr->transit_type == 'VIVAR' ? 'selected' : '' }}>VIVAR</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Transit ID</label>
                                                                <input type="text" class="field-input" name="transit_id"
                                                                    value="{{ $crr->transit_id }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group" style="margin-top: 10px;">
                                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                                    <input type="checkbox" id="bonded-goods-check"
                                                                        name="is_bonded_goods" {{ $crr->is_bonded_goods ? 'checked' : '' }}>
                                                                    <label for="bonded-goods-check" class="field-label mb-0"
                                                                        style="color: #475569;">Bonded goods</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Customs doc type</label>
                                                                <select class="field-input select2" name="customs_doc_type">
                                                                    <option value=""></option>
                                                                    <option value="T1" {{ $crr->customs_doc_type == 'T1' ? 'selected' : '' }}>T1</option>
                                                                    <option value="T2" {{ $crr->customs_doc_type == 'T2' ? 'selected' : '' }}>T2</option>
                                                                    <option value="IMA" {{ $crr->customs_doc_type == 'IMA' ? 'selected' : '' }}>IMA</option>
                                                                    <option value="EXA" {{ $crr->customs_doc_type == 'EXA' ? 'selected' : '' }}>EXA</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Bonded date</label>
                                                                <div class="icon-input-wrapper">
                                                                    <input type="text" class="field-input datepicker"
                                                                        name="bonded_date" value="{{ $crr->bonded_date }}"
                                                                        placeholder="YYYY-MM-DD">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Country of origin</label>
                                                                <select class="field-input select2-country"
                                                                    name="country_of_origin">
                                                                    <option value=""></option>
                                                                    @foreach($countries as $country)
                                                                        <option value="{{ $country->name }}"
                                                                            data-flag-url="{{ $country->flag_url }}" {{ $crr->country_of_origin == $country->name ? 'selected' : '' }}>
                                                                            {{ $country->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">HS code</label>
                                                                <input type="text" class="field-input" name="hs_code"
                                                                    value="{{ $crr->hs_code }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="field-group">
                                                                <label class="field-label">Priority</label>
                                                                <select class="field-input select2" name="priority">
                                                                    <option value="Standard" {{ $crr->priority == 'Standard' ? 'selected' : '' }}>Standard</option>
                                                                    <option value="Urgent" {{ $crr->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                                                    <option value="Critical" {{ $crr->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="field-group">
                                                                <label class="field-label">Currency</label>
                                                                <select class="field-input select2" name="currency"
                                                                    id="edit_currency_select">
                                                                    <option value=""></option>
                                                                    @foreach($countries->whereNotNull('currency')->unique('currency')->sortBy('currency') as $country)
                                                                        <option value="{{ $country->currency }}"
                                                                            data-rate="{{ $country->currency_value }}" {{ $crr->currency == $country->currency ? 'selected' : '' }}>
                                                                            {{ $country->currency }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="field-group">
                                                                <label class="field-label">Customs value</label>
                                                                <input type="text" step="0.01" class="field-input"
                                                                    name="customs_value" id="edit_customs_value"
                                                                    value="{{ $crr->customs_value }}">
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="field-group">
                                                                <label class="field-label">Customs value USD</label>
                                                                <div id="edit_customs_value_usd_display"
                                                                    style="font-size: 12px; font-weight: 600; color: #1e293b; padding: 4px 0;">
                                                                    {{ number_format($crr->customs_value_usd, 2) }}
                                                                </div>
                                                                <input type="hidden" name="customs_value_usd"
                                                                    id="edit_customs_value_usd_hidden"
                                                                    value="{{ $crr->customs_value_usd }}">
                                                            </div>
                                                        </div>

                                                    </div>



                                                    <div class="field-group">
                                                        <label class="field-label">Internal comments</label>
                                                        <textarea class="field-input" name="internal_comments"
                                                            style="height: 60px; resize: none;">{{ $crr->internal_comments }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Packages Section -->
                                            <div class="crr-table-header">
                                                <span>Packages &nbsp; &nbsp; <span id="package-summary-text"
                                                        style="font-weight: normal; color: #000000; font-weight: 600;">(Total
                                                        : 0.00 kg, 0
                                                        Packages, 0.0000 CBM)</span></span>
                                                <button type="button" class="btn btn-outline-teal btn-add-package">Add
                                                    item</button>
                                            </div>
                                            <div id="packages-validation-error" style="display:none; margin-bottom: 10px; padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12px; position: relative; padding-right: 32px;">
                                                <span id="packages-validation-error-text"></span>
                                                <button type="button" id="packages-validation-error-close" title="Close" aria-label="Close" style="position: absolute; top: 6px; right: 8px; border: none; background: transparent; color: #b91c1c; font-size: 16px; line-height: 1; cursor: pointer; padding: 2px 4px;">&times;</button>
                                            </div>
                                            <div class="table-responsive" style="margin-top: -10px !important;">
                                                <table class="crr-data-table" id="packagesTable">
                                                    <thead>
                                                         <tr>
                                                             <th>#</th>
                                                             <th style="width: 80px;">Length</th>
                                                             <th style="width: 80px;">Width</th>
                                                             <th style="width: 80px;">Height</th>
                                                             <th style="width: 80px;">Weight</th>
                                                             <th style="width: 80px;">CBM</th>
                                                             <th>Warehouse location</th>
                                                             <th class="text-center">Irreg.</th>
                                                             <th class="text-center">DGR</th>
                                                             <th class="text-center">Non Stack.</th>
                                                             <th class="text-center">Medicine</th>
                                                             <th class="text-center">X-ray</th>
                                                             <th class="text-center">Copy</th>
                                                             <th class="text-center">Delete</th>
                                                         </tr>
                                                    </thead>
                                                    <tbody>
                                                         @forelse($crr->packages as $index => $pkg)
                                                             <tr data-index="{{ $index }}">
                                                                 <td>{{ $index + 1 }}</td>
                                                                 <td>
                                                                     <input type="hidden" name="packages[{{ $index }}][id]"
                                                                         value="{{ $pkg->id }}">
                                                                     <input type="text" step="0.01"
                                                                         class="crr-input pkg-dim pkg-l"
                                                                         name="packages[{{ $index }}][length]"
                                                                         value="{{ $pkg->length }}">
                                                                 </td>
                                                                 <td><input type="text" step="0.01"
                                                                         class="crr-input pkg-dim pkg-w"
                                                                         name="packages[{{ $index }}][width]"
                                                                         value="{{ $pkg->width }}"></td>
                                                                 <td><input type="text" step="0.01"
                                                                         class="crr-input pkg-dim pkg-h"
                                                                         name="packages[{{ $index }}][height]"
                                                                         value="{{ $pkg->height }}"></td>
                                                                 <td><input type="text" step="0.01" class="crr-input pkg-weight"
                                                                         name="packages[{{ $index }}][weight]"
                                                                         value="{{ $pkg->weight }}"></td>
                                                                 <td><input type="text" class="crr-input pkg-cbm"
                                                                         name="packages[{{ $index }}][cbm]" readonly
                                                                         value="{{ $pkg->cbm }}"></td>
                                                                 <td><input type="text" class="crr-input"
                                                                         name="packages[{{ $index }}][warehouse_location]"
                                                                         value="{{ $pkg->warehouse_location }}"></td>
                                                                 <td class="text-center"><input type="checkbox"
                                                                          class="pkg-is-irregular" name="packages[{{ $index }}][is_delivery_irregularity]"
                                                                          {{ $pkg->is_delivery_irregularity ? 'checked' : '' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          class="pkg-is-dgr" name="packages[{{ $index }}][is_dgr]"
                                                                          {{ $pkg->is_dgr ? 'checked' : '' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_not_stackable]" {{ $pkg->is_not_stackable ? 'checked' : '' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_medicine]" {{ $pkg->is_medicine ? 'checked' : '' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_xray]" {{ $pkg->is_xray ? 'checked' : '' }}></td>
                                                                  <td class="text-center">
                                                                      <button type="button"
                                                                          class="btn btn-link text-primary p-0 btn-copy-row"><i
                                                                              class="icofont icofont-copy-alt"></i></button>
                                                                  </td>
                                                                  <td class="text-center">
                                                                      <button type="button"
                                                                          class="btn btn-link text-danger p-0 btn-remove-row"><i
                                                                              class="icofont icofont-trash"></i></button>
                                                                  </td>
                                                              </tr>
                                                              <tr class="irregularity-sub-row" data-index="{{ $index }}"
                                                                  style="{{ $pkg->is_delivery_irregularity ? '' : 'display: none;' }}">
                                                                  <td colspan="2"></td>
                                                                  <td colspan="12">
                                                                      <div class="dgr-container" style="background: #fff9e6; border: 1px solid #ffeeba;">
                                                                          <i class="icofont icofont-warning dgr-warning-icon" style="color: #f0ad4e;"></i>
                                                                          <div class="dgr-field" style="flex: 1;">
                                                                              <label class="field-label">Delivery irregularities</label>
                                                                              <select class="form-control select2-irregularities" name="packages[{{ $index }}][delivery_irregularities][]" multiple="multiple">
                                                                                  <option value="Damaged packaging - no repacking required" {{ in_array('Damaged packaging - no repacking required', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Damaged packaging - no repacking required</option>
                                                                                  <option value="Damaged packaging - repacking required" {{ in_array('Damaged packaging - repacking required', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Damaged packaging - repacking required</option>
                                                                                  <option value="Missing DG label / marking on package" {{ in_array('Missing DG label / marking on package', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Missing DG label / marking on package</option>
                                                                                  
                                                                                    <option value="Missing documentation - Commercial invoice / Packing list" {{ in_array('Missing documentation - Commercial invoice / Packing list', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Missing documentation - Commercial invoice / Packing list</option>
                                                                                    <option value="Missing documentation - DG" {{ in_array('Missing documentation - DG', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Missing documentation - DG</option>
                                                                                    <option value="Missing documentation - Other" {{ in_array('Missing documentation - Other', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Missing documentation - Other</option>
                                                                                    <option value="Missing label on packaging" {{ in_array('Missing label on packaging', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>Missing label on packaging</option>
                                                                                    <option value="Packaging not fit for airfreight" {{ in_array('Packaging not fit for airfreight', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>
                                                                                        Packaging not fit for airfreight</option>
                                                                                    <option value="Packaging not fumigated" {{ in_array('Packaging not fumigated', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>
                                                                                        Packaging not fumigated</option>
                                                                                    <option value="Packaging not heat treated" {{ in_array('Packaging not heat treated', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>
                                                                                        Packaging not heat treated</option>
                                                                                    <option value="Vessel Name / PO Number not mentioned on packaging (label)" {{ in_array('Vessel Name / PO Number not mentioned on packaging (label)', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>
                                                                                        Vessel Name / PO Number not mentioned on packaging
                                                                                        (label)</option>
                                                                                    <option value="Vessel Name / PO Number not mentioned on supplier documentation" {{ in_array('Vessel Name / PO Number not mentioned on supplier documentation', (array)($pkg->delivery_irregularities ?? [])) ? 'selected' : '' }}>
                                                                                        Vessel Name / PO Number not mentioned on supplier
                                                                                        documentation</option>
                                                                              </select>
                                                                          </div>
                                                                      </div>
                                                                  </td>
                                                              </tr>
                                                              <tr class="dgr-sub-row" data-index="{{ $index }}"
                                                                  style="{{ $pkg->is_dgr ? '' : 'display: none;' }}">
                                                                  <td colspan="2"></td>
                                                                  <td colspan="12">
                                                                     <div class="dgr-container">
                                                                         <i class="icofont icofont-warning dgr-warning-icon"></i>
                                                                         <div class="dgr-field">
                                                                             <label class="field-label">Dangerous goods
                                                                                 description</label>
                                                                             <input type="text" class="field-input"
                                                                                 name="packages[{{ $index }}][dgr_description]"
                                                                                 value="{{ $pkg->dgr_description }}"
                                                                                 placeholder="">
                                                                         </div>
                                                                         <div class="dgr-field small" style="max-width: 50px;">
                                                                             <label class="field-label">UN number</label>
                                                                             <input type="text" class="field-input"
                                                                                 name="packages[{{ $index }}][un_number]"
                                                                                 value="{{ $pkg->un_number }}" placeholder="">
                                                                         </div>
                                                                         <div class="dgr-field small" style="max-width: 50px;">
                                                                             <label class="field-label">Class</label>
                                                                             <input type="text" class="field-input"
                                                                                 name="packages[{{ $index }}][dgr_class]"
                                                                                 value="{{ $pkg->dgr_class }}" placeholder="">
                                                                         </div>
                                                                     </div>
                                                                 </td>
                                                             </tr>
                                                         @empty
                                                             <tr class="empty-row">
                                                                 <td colspan="14" class="text-center py-4 text-muted">No items
                                                                     added yet. Click "Add item" to start.</td>
                                                             </tr>
                                                         @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Costs Section -->
                                            <div class="crr-table-header">
                                                <span>Costs</span>
                                                <button type="button" class="btn btn-outline-teal btn-add-cost">Add
                                                    cost</button>
                                            </div>
                                            <div class="table-responsive"
                                                style="margin-top: -10px !important; margin-bottom: 30px !important;">
                                                <table class="crr-data-table" id="costsTable">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Carrier</th>
                                                            <th>Net value</th>
                                                            <th>Currency</th>
                                                            <th>Net USD</th>
                                                            <th>Invoice no</th>
                                                            <th>Remarks</th>
                                                            <th>Hub/Agent</th>
                                                            <th>Tag</th>
                                                            <th>Copy</th>
                                                            <th class="text-center">Del</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($crr->costs as $index => $cost)
                                                            <tr data-index="{{ $index }}">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <input type="hidden" name="costs[{{ $index }}][id]"
                                                                        value="{{ $cost->id }}">
                                                                    <input type="text" class="crr-input"
                                                                        name="costs[{{ $index }}][type]"
                                                                        value="{{ $cost->type }}">
                                                                </td>
                                                                <td><input type="text" class="crr-input"
                                                                        name="costs[{{ $index }}][carrier]"
                                                                        value="{{ $cost->carrier }}"></td>
                                                                <td><input type="text" step="0.01" class="crr-input"
                                                                        name="costs[{{ $index }}][net_value]"
                                                                        value="{{ $cost->net_value }}"></td>
                                                                <td>
                                                                    <select class="crr-input select2-cost-currency"
                                                                        name="costs[{{ $index }}][currency]">
                                                                        <option value=""></option>
                                                                        @foreach($currencies as $curr)
                                                                            <option value="{{ $curr }}" {{ $cost->currency == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" step="0.01" class="crr-input"
                                                                        name="costs[{{ $index }}][net_value_usd]"
                                                                        value="{{ $cost->net_value_usd }}"></td>
                                                                <td><input type="text" class="crr-input"
                                                                        name="costs[{{ $index }}][invoice_no]"
                                                                        value="{{ $cost->invoice_no }}"></td>
                                                                <td><input type="text" class="crr-input"
                                                                        name="costs[{{ $index }}][remarks]"
                                                                        value="{{ $cost->remarks }}"></td>
                                                                <td>
                                                                    <select class="crr-input select2-cost-hub"
                                                                        name="costs[{{ $index }}][hub_agent]">
                                                                        <option value=""></option>
                                                                        @foreach($hubs as $h)
                                                                            <option value="{{ $h->code }}"
                                                                                data-city="{{ $h->city }}"
                                                                                data-country="{{ $h->country }}"
                                                                                data-code="{{ $h->code }}" {{ ($cost->hub_agent == $h->code || $cost->hub_agent == $h->hub_name) ? 'selected' : '' }}>
                                                                                {{ $h->code }} - {{ $h->hub_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="crr-input"
                                                                        name="costs[{{ $index }}][tag]"
                                                                        value="{{ $cost->tag }}"></td>
                                                                <td class="text-center"><button type="button"
                                                                        class="btn btn-link text-primary p-0 btn-copy-row"><i
                                                                            class="icofont icofont-copy-alt"></i></button></td>
                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn btn-link text-danger p-0 btn-remove-row"><i
                                                                            class="icofont icofont-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr class="empty-row">
                                                                <td colspan="11" class="text-center py-4 text-muted">No costs
                                                                    added yet. Click "Add cost" to start.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> <!-- stock-form-scroll -->
                                    </div> <!-- stock-details -->

                                    <div id="line-items" class="stock-tab-content"
                                        style="display: none; padding: 25px; background: #fff; flex: 1; overflow-y: auto;">
                                        <!-- Add line item button row -->
                                        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                                            <button class="btn btn-outline-teal"
                                                style="font-size: 12px; padding: 5px 20px; border-radius: 4px; border-color: #008080; color: #008080; background: transparent;">Add
                                                line item</button>
                                        </div>

                                        <!-- Line Items Table -->
                                        <div class="table-responsive">
                                            <table class="edit-table" style="min-width: 1200px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30px;">#</th>
                                                        <th>Part number</th>
                                                        <th>HS code</th>
                                                        <th style="width: 150px;">Description</th>
                                                        <th>Manufact.</th>
                                                        <th>Origin</th>
                                                        <th class="text-right">Net wt.</th>
                                                        <th class="text-right">Gr. wt.</th>
                                                        <th class="text-right">Qty</th>
                                                        <th class="text-right">Qty rec.</th>
                                                        <th>Unit</th>
                                                        <th class="text-right">Unit price</th>
                                                        <th>Currency</th>
                                                        <th class="text-right">Sub total</th>
                                                        <th style="width: 80px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="color: #94a3b8;">1</td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option></option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option>Pcs</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option>USD</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-right" style="font-weight: 500;">0</td>
                                                        <td class="text-right">
                                                            <i class="ti-layers"
                                                                style="color: #94a3b8; cursor: pointer; margin-right: 10px;"></i>
                                                            <i class="ti-trash"
                                                                style="color: #94a3b8; cursor: pointer;"></i>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: #94a3b8;">2</td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option></option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option>Pcs</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="field-input text-right"
                                                                style="height: 28px;"></td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option>USD</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-right" style="font-weight: 500;">0</td>
                                                        <td class="text-right">
                                                            <i class="ti-layers"
                                                                style="color: #94a3b8; cursor: pointer; margin-right: 10px;"></i>
                                                            <i class="ti-trash"
                                                                style="color: #94a3b8; cursor: pointer;"></i>
                                                        </td>
                                                    </tr>
                                                    <!-- Total Row -->
                                                    <tr style="background: #fff; border-top: 2px solid #f1f5f9;">
                                                        <td colspan="6" class="text-right"
                                                            style="font-weight: 600; color: #64748b; font-size: 11px;">Total
                                                        </td>
                                                        <td class="text-right" style="font-weight: 700; color: #1e293b;">
                                                            0.000</td>
                                                        <td class="text-right" style="font-weight: 700; color: #1e293b;">
                                                            0.000</td>
                                                        <td colspan="3"></td>
                                                        <td class="text-right"
                                                            style="font-weight: 600; color: #64748b; font-size: 11px; padding-top: 15px;">
                                                            Total USD</td>
                                                        <td colspan="2" class="text-right"
                                                            style="font-weight: 700; color: #1e293b; font-size: 14px; padding-top: 15px;">
                                                            0.00</td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Manufacturer Section -->
                                        <div style="margin-top: 50px;">
                                            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                                                <button class="btn btn-outline-teal"
                                                    style="font-size: 12px; padding: 5px 20px; border-radius: 4px; border-color: #008080; color: #008080; background: transparent;">Add
                                                    manufacturer</button>
                                            </div>
                                            <table class="edit-table">
                                                <thead>
                                                    <tr>
                                                        <th>Manufacturer</th>
                                                        <th>Street</th>
                                                        <th>Zip</th>
                                                        <th>City</th>
                                                        <th>Country</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option></option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <i class="ti-trash"
                                                                style="color: #94a3b8; cursor: pointer;"></i>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td><input type="text" class="field-input" style="height: 28px;">
                                                        </td>
                                                        <td>
                                                            <select class="field-input select2" style="height: 28px;">
                                                                <option></option>
                                                            </select>
                                                        </td>

                                                        <td class="text-center">
                                                            <i class="ti-trash"
                                                                style="color: #94a3b8; cursor: pointer;"></i>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="irregularities" class="stock-tab-content"
                                        style="display: none; padding: 25px; background: #fff; flex: 1; overflow-y: auto;">
                                        <!-- Add irregularity button row -->
                                        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                                            <button class="btn btn-outline-teal"
                                                style="font-size: 12px; padding: 5px 20px; border-radius: 4px; border-color: #008080; color: #008080; background: transparent;">Add
                                                irregularity</button>
                                        </div>

                                        <!-- Irregularity Block 1 -->
                                        <div class="irregularity-item"
                                            style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                                            <!-- First row: Inputs and Status -->
                                            <div style="display: flex; gap: 15px; align-items: flex-end;">
                                                <div style="flex: 1;">
                                                    <label class="field-label">Date</label>
                                                    <div class="icon-input-wrapper">
                                                        <input type="text" class="field-input" placeholder="DD.MM.YYYY">
                                                        <i class="fa fa-calendar" style="color: #0ea5e9;"></i>
                                                    </div>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Irregularity</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Party responsible</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Hub/agent</label>
                                                    <input type="text" class="field-input">
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Consequences</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Extra costs (USD)</label>
                                                    <input type="text" class="field-input">
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Status</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="padding-bottom: 8px;">
                                                    <i class="ti-trash"
                                                        style="color: #94a3b8; cursor: pointer; font-size: 16px;"></i>
                                                </div>
                                            </div>

                                            <!-- Second row: Textareas -->
                                            <div class="row" style="margin-top: 70px;">
                                                <div class="col-sm-4">
                                                    <label class="field-label">Cause of irregularity</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="field-label">Action taken</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="field-label">Hub/agent comments</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Irregularity Block 2 -->
                                        <div class="irregularity-item" style="margin-bottom: 20px;">
                                            <!-- First row -->
                                            <div style="display: flex; gap: 15px; align-items: flex-end;">
                                                <div style="flex: 1;">
                                                    <label class="field-label">Date</label>
                                                    <div class="icon-input-wrapper">
                                                        <input type="text" class="field-input datepicker"
                                                            placeholder="YYYY-MM-DD">
                                                        <i class="fa fa-calendar" style="color: #0ea5e9;"></i>
                                                    </div>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Irregularity</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Party responsible</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Hub/agent</label>
                                                    <input type="text" class="field-input">
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Consequences</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Extra costs (USD)</label>
                                                    <input type="text" class="field-input">
                                                </div>
                                                <div style="flex: 1.5;">
                                                    <label class="field-label">Status</label>
                                                    <select class="field-input select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div style="padding-bottom: 8px;">
                                                    <i class="ti-trash"
                                                        style="color: #94a3b8; cursor: pointer; font-size: 16px;"></i>
                                                </div>
                                            </div>

                                            <!-- Second row -->
                                            <div class="row mt-3">
                                                <div class="col-sm-4">
                                                    <label class="field-label">Cause of irregularity</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="field-label">Action taken</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="field-label">Hub/agent comments</label>
                                                    <textarea class="field-input"
                                                        style="height: 100px; resize: none;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer (Fixed) -->
                                    <div class="edit-footer">
                                        <button type="submit" class="btn-save-custom">Save changes</button>
                                        <a href="{{ route('stocks') }}" class="btn-cancel-custom">Cancel</a>
                                    </div>
                                </form>
                            </div> <!-- stock-main-content -->

                            <!-- 3. RIGHT PANEL SIDEBAR -->
                            <div class="stock-right-panel">
                                <!-- Documents Panel -->
                                <div class="panel-card" id="crr-documents-panel">
                                    @php
                                        $crrDocTypeOptions = \App\Models\CrrDocument::fileTypeOptionsWithCustom();
                                    @endphp
                                    <div class="panel-title">Documents (<span class="doc-count">{{ $crr->documents->count() }}</span>)</div>
                                    <div class="crr-docs-header">
                                        <span>Filename</span>
                                        <span class="crr-docs-internal-label">Internal</span>
                                    </div>
                                    <div id="crr-doc-list">
                                        @forelse($crr->documents as $doc)
                                            @php
                                                $selectedDocType = $doc->file_type ?: 'Unspecified';
                                                if (strtolower($selectedDocType) === 'unspecified') {
                                                    $selectedDocType = 'Unspecified';
                                                }
                                            @endphp
                                            <div class="doc-item" data-id="{{ $doc->id }}">
                                                <div class="doc-main">
                                                    <a href="{{ $doc->fileUrl() }}" target="_blank" class="doc-name" title="{{ $doc->file_name }}">
                                                        {{ $doc->file_name }}
                                                    </a>
                                                    <select class="doc-type-select" data-id="{{ $doc->id }}">
                                                        @foreach ($crrDocTypeOptions as $typeOption)
                                                            <option value="{{ $typeOption }}" {{ $selectedDocType === $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                                                        @endforeach
                                                        @if (! in_array($selectedDocType, $crrDocTypeOptions, true))
                                                            <option value="{{ $selectedDocType }}" selected>{{ $selectedDocType }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="doc-side">
                                                    <div class="doc-side-row">
                                                        <div class="doc-internal checkbox-fade fade-in-primary">
                                                            <label>
                                                                <input type="checkbox" class="doc-internal-check" data-id="{{ $doc->id }}" {{ $doc->is_internal ? 'checked' : '' }}>
                                                                <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                            </label>
                                                        </div>
                                                        <i class="ti-trash doc-trash delete-doc" data-id="{{ $doc->id }}" title="Delete"></i>
                                                    </div>
                                                    <span class="doc-date">{{ $doc->created_at->format('d.m.Y') }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="no-docs-msg"
                                                style="text-align: center; padding: 20px; color: #94a3b8; font-size: 11px;">No
                                                documents uploaded yet.</div>
                                        @endforelse
                                    </div>

                                    <div class="dropzone-placeholder" id="crr-dropzone">
                                        <div class="dropzone-text">Drag files here or click to browse</div>
                                        <i class="ti-upload dropzone-icon"></i>
                                    </div>
                                    <input type="file" id="crr-file-input" style="display: none;" multiple>
                                </div>

                                <!-- Activity Panel -->
                                <div class="panel-card" style="padding: 0;">
                                    <div style="display: flex; border-bottom: 1px solid #f1f5f9;">
                                        <div class="panel-tab active" data-panel="change-log"
                                            style="flex: 1; padding: 10px; text-align: center; font-size: 10px; font-weight: 600; color: #008080; border-bottom: 2px solid #008080; cursor: pointer;">
                                            Change log</div>
                                        <div class="panel-tab" data-panel="location-history"
                                            style="flex: 1; padding: 10px; text-align: center; font-size: 10px; color: #94a3b8; cursor: pointer;">
                                            Location history</div>
                                        <div class="panel-tab" data-panel="comments"
                                            style="flex: 1; padding: 10px; text-align: center; font-size: 10px; color: #94a3b8; cursor: pointer;">
                                            Comments</div>
                                    </div>
                                    <div id="panel-contents">
                                        <div id="change-log" class="panel-tab-content active"
                                            style="padding: 15px; max-height: 400px; overflow-y: auto;">
                                            @forelse ($crr->changeLogs as $changeLog)
                                                <div style="border-bottom: 1px solid #f8fafc; padding-bottom: 5px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                                        <div style="flex: 1;">
                                                            <span style="font-size: 10px; font-weight: 600; color: #008080;">{{ $changeLog->title }}</span>
                                                            @if ($changeLog->description)
                                                                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                                                                    {{ $changeLog->description }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div style="text-align: right; min-width: 80px;">
                                                            <div style="font-size: 10px; color: #334155; font-weight: 500;">
                                                                {{ $changeLog->user?->name ?? 'System' }}
                                                            </div>
                                                            <div style="font-size: 9px; color: #94a3b8;">
                                                                {{ $changeLog->created_at->format('d.m.Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div style="font-size: 11px; color: #64748b; text-align: center; padding: 20px 0;">
                                                    No changes recorded yet.
                                                </div>
                                            @endforelse
                                        </div>
                                        <div id="location-history" class="panel-tab-content"
                                            style="display: none; padding: 15px;">
                                            <div style="font-size: 11px; color: #64748b;">No location history available
                                            </div>
                                        </div>
                                        <div id="comments" class="panel-tab-content" style="display: none; padding: 15px;">
                                            <div style="font-size: 11px; color: #64748b;">No comments available</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </div>{{-- .stock-edit-wrapper --}}
    @include('layouts.partials.pcoded-shell-end')

    {{-- Quick-add supplier modal --}}
    <div class="modal fade" id="add-supplier-modal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 1100px;">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="addSupplierModalLabel" style="font-size: 14px; font-weight: 600;">Add supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add-supplier-form">
                    <div class="modal-body" style="padding: 16px 20px; max-height: 70vh; overflow-y: auto;">
                        <div class="add-supplier-grid">
                            {{-- Column 1: Supplier information --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier information</div>

                                <div class="field-group mb-2">
                                    <label class="field-label">Supplier name <span class="text-danger">*</span></label>
                                    <input type="text" class="field-input" name="supplier_name" id="modal-supplier-name" required>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Phone number (with country code)</label>
                                    <input type="text" class="field-input" name="phone_number" id="modal-supplier-phone">
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Email</label>
                                    <input type="text" class="field-input" name="email" id="modal-supplier-email"
                                        placeholder="email@example.com; email2@example.com">
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Remarks</label>
                                    <textarea class="field-input" name="remarks" id="modal-supplier-remarks" rows="3" style="height: auto; min-height: 70px;"></textarea>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Special considerations for destination</label>
                                    <textarea class="field-input" name="special_considerations" id="modal-supplier-special" rows="3" style="height: auto; min-height: 70px;"></textarea>
                                </div>
                            </div>

                            {{-- Column 2: Address --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier address</div>

                                <div class="field-group mb-2">
                                    <label class="field-label">Supplier address</label>
                                    <textarea class="field-input" name="supplier_address" id="modal-supplier-address" rows="2" style="height: auto; min-height: 50px;"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">City</label>
                                            <input type="text" class="field-input" name="city" id="modal-supplier-city">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">District/state</label>
                                            <input type="text" class="field-input" name="district_state" id="modal-supplier-district">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">Zip code</label>
                                            <input type="text" class="field-input" name="zip_code" id="modal-supplier-zip">
                                        </div>
                                    </div>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Country</label>
                                    <select class="field-input modal-supplier-country" name="country_id" id="modal-supplier-country">
                                        <option value="">Select an option</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Port code</label>
                                    <input type="text" class="field-input" name="port_code" id="modal-supplier-port">
                                </div>

                                <div class="add-supplier-section-title" style="margin-top: 12px;">Office address (optional)</div>

                                <div class="field-group mb-2">
                                    <label class="field-label">Office address</label>
                                    <textarea class="field-input" name="office_address" id="modal-office-address" rows="2" style="height: auto; min-height: 50px;"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">City</label>
                                            <input type="text" class="field-input" name="office_city" id="modal-office-city">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">District/state</label>
                                            <input type="text" class="field-input" name="office_district_state" id="modal-office-district">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="field-group mb-2">
                                            <label class="field-label">Zip code</label>
                                            <input type="text" class="field-input" name="office_zip_code" id="modal-office-zip">
                                        </div>
                                    </div>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Country</label>
                                    <select class="field-input modal-supplier-country" name="office_country_id" id="modal-office-country">
                                        <option value="">Select an option</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" data-flag-url="{{ $country->flag_url }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Column 3: Supplier details --}}
                            <div class="add-supplier-col">
                                <div class="add-supplier-section-title">Supplier details</div>

                                <div class="field-group mb-2">
                                    <label class="field-label">VAT number</label>
                                    <input type="text" class="field-input" name="vat_number" id="modal-supplier-vat">
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">EORI number</label>
                                    <input type="text" class="field-input" name="eori_number" id="modal-supplier-eori">
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">Currency</label>
                                    <select class="field-input modal-supplier-currency" name="currency" id="modal-supplier-currency">
                                        <option value="">Select an option</option>
                                        @foreach($currencies as $curr)
                                            <option value="{{ $curr }}">{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field-group mb-2">
                                    <label class="field-label">UN/LOCODE</label>
                                    <input type="text" class="field-input" name="un_locode" id="modal-supplier-unlocode">
                                </div>
                            </div>
                        </div>
                        <div id="add-supplier-error" class="text-danger mt-2" style="font-size: 11px; display: none;"></div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-teal" id="add-supplier-save-btn" style="background:#008080;border-color:#008080;color:#fff;">Save supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function () {
            // ─── Fixed header/tabs left-offset ────────────────────────────
            function fixedHeaderOffset() {
                var isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                var $navbar = $('.pcoded-navbar');
                var navType = $('#pcoded').attr('vertical-nav-type') || '';
                var sidebarWidth = 0;

                // On phone/tablet the nav is offcanvas — never reserve its desktop width.
                if (!isMobile && $navbar.length && navType !== 'offcanvas' && $navbar.is(':visible')) {
                    sidebarWidth = $navbar.outerWidth() || 0;
                }

                $('.summary-header, .stock-tabs-container, .edit-footer').css('left', sidebarWidth + 'px');
            }
            fixedHeaderOffset();
            $(window).on('resize', fixedHeaderOffset);
            // ──────────────────────────────────────────────────────────────

            // Pass current Physical Location into print PDFs (even before save).
            $(document).on('click', '.js-print-with-location', function (e) {
                var baseHref = $(this).attr('href');
                if (!baseHref) {
                    return;
                }

                e.preventDefault();

                var locationValue = ($('input[name="location"]').val() || '').trim();
                var url = new URL(baseHref, window.location.origin);
                if (locationValue) {
                    url.searchParams.set('location', locationValue);
                } else {
                    url.searchParams.delete('location');
                }

                window.open(url.toString(), '_blank');
            });

            // Data for dynamic rows
            var hubs = @json($hubs);
            var currencies = @json($currencies);

            // Select2 Initialization
            // Nuclear option: Purge clear icon HTML via MutationObserver
            const clearIconObserver = new MutationObserver(function (mutations) {
                $('.select2-selection__clear').remove();
            });
            clearIconObserver.observe(document.body, { childList: true, subtree: true });
            $('.select2-selection__clear').remove();

            $('.select2, .select2-irregularities').select2({
                placeholder: "Select an option",
                allowClear: false,
                width: '100%'
            });

            $('.select2-incoterm').select2({
                placeholder: 'Select incoterm',
                allowClear: true,
                width: '100%'
            });

            // Special handling for Delivery Irregularities to keep fixed height
            $('.select2-irregularities').each(function () {
                $(this).next('.select2-container').addClass('select2-irreg-container');
            });

            function formatHub(hub) {
                if (!hub.id || !hub.element) return hub.text;
                var code = $(hub.element).data('code');
                var city = $(hub.element).data('city');
                var country = $(hub.element).data('country');

                var res = '<div class="select2-result-hub">' +
                    '<div class="select2-result-hub__title">' + (code ? '<strong>' + code + '</strong> - ' : '') + hub.text.replace(code + ' - ', '') + '</div>' +
                    '<div class="select2-result-hub__location">' + (city || '') + (country ? ', ' + country : '') + '</div>' +
                    '</div>';
                return $(res);
            }

            $('.select2-hub').select2({
                placeholder: "Select hub",
                allowClear: false,
                width: '100%',
                templateResult: formatHub,
                templateSelection: function (hub) {
                    return hub.text;
                }
            });

            function formatVessel(state) {
                if (!state.id) return state.text;
                var $element = $(state.element);
                var customer = $element.data('customer');

                if (!customer) return state.text;

                return $(
                    '<div class="vessel-result">' +
                    '<div class="vessel-result__name">' + state.text + '</div>' +
                    '<div class="vessel-result__customer">' + customer + '</div>' +
                    '</div>'
                );
            }

            $('.select2-vessel').select2({
                placeholder: "Select or type vessel",
                tags: true,
                width: '100%',
                templateResult: formatVessel,
                templateSelection: function (state) {
                    return state.text;
                }
            });

            var $mainVesselSelect = $('select[name="vessel_name"]');
            var $vesselCustomerGroup = $('#vessels-customer-name-group');
            var $vesselCustomerName = $('#vessels_customer_name');
            var $summaryAccountManager = $('#summary-account-manager');

            function updateVesselCustomerName() {
                var $selectedVessel = $mainVesselSelect.find('option:selected');
                var customerName = $selectedVessel.data('customer') || '';
                var accountManagerName = $selectedVessel.data('account-manager') || '—';

                $vesselCustomerName.val(customerName);
                $vesselCustomerGroup.toggle(Boolean(customerName));
                $summaryAccountManager.text(accountManagerName);
            }

            $mainVesselSelect.on('change', updateVesselCustomerName);
            updateVesselCustomerName();

            $('.select2-po').select2({
                placeholder: "Type PO and press Enter",
                tags: true,
                tokenSeparators: [',', ' '],
                width: '100%'
            });

            // Supplier Select2 is initialized in the quick-add script below.

            // --- Package Logic ---
            var packageIndex = {{ count($crr->packages) }};
            $('.btn-add-package').on('click', function () {
                $('#packagesTable tbody .empty-row').remove();
                let row = `<tr data-index="${packageIndex}"><td>${packageIndex + 1}</td><td><input name="packages[${packageIndex}][length]"class="crr-input pkg-dim pkg-l"step="0.01"></td><td><input name="packages[${packageIndex}][width]"class="crr-input pkg-dim pkg-w"step="0.01"></td><td><input name="packages[${packageIndex}][height]"class="crr-input pkg-dim pkg-h"step="0.01"></td><td><input name="packages[${packageIndex}][weight]"class="crr-input pkg-weight"step="0.01"></td><td><input name="packages[${packageIndex}][cbm]"class="crr-input pkg-cbm"readonly value="0"></td><td><input name="packages[${packageIndex}][warehouse_location]"class="crr-input"></td><td><input name="packages[${packageIndex}][remarks]" class="crr-input"></td><td class="text-center"><input type="checkbox" class="pkg-is-irregular" name="packages[${packageIndex}][is_delivery_irregularity]"></td><td class="text-center"><input name="packages[${packageIndex}][is_dgr]" class="pkg-is-dgr" type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_not_stackable]"type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_medicine]"type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_xray]"type="checkbox"></td><td class="text-center"><button class="btn btn-link p-0 btn-copy-row text-primary"type="button"><i class="icofont icofont-copy-alt"></i></button></td><td class="text-center"><button class="btn btn-link p-0 btn-remove-row text-danger"type="button"><i class="icofont icofont-trash"></i></button></td></tr><tr class="irregularity-sub-row" data-index="${packageIndex}" style="display: none;"><td colspan="2"></td><td colspan="13"><div class="dgr-container" style="background: #fff9e6; border: 1px solid #ffeeba;"><i class="icofont icofont-warning dgr-warning-icon" style="color: #f0ad4e;"></i><div class="dgr-field" style="flex: 1;"><label class="crr-label">Delivery irregularities</label><select class="form-control select2 select2-irregularities" name="packages[${packageIndex}][delivery_irregularities][]" multiple="multiple"><option value="Damaged packaging - no repacking required">Damaged packaging - no repacking required</option><option value="Damaged packaging - repacking required">Damaged packaging - repacking required</option><option value="Missing DG label / marking on package">Missing DG label / marking on package</option><option value="Missing documentation - Commercial invoice / Packing list">Missing documentation - Commercial invoice / Packing list</option><option value="Missing documentation - DG">Missing documentation - DG</option><option value="Missing documentation - Other">Missing documentation - Other</option><option value="Missing label on packaging">Missing label on packaging</option><option value="Packaging not fit for airfreight">Packaging not fit for airfreight</option><option value="Packaging not fumigated">Packaging not fumigated</option><option value="Packaging not heat treated">Packaging not heat treated</option><option value="Vessel Name / PO Number not mentioned on packaging (label)">Vessel Name / PO Number not mentioned on packaging (label)</option><option value="Vessel Name / PO Number not mentioned on supplier documentation">Vessel Name / PO Number not mentioned on supplier documentation</option></select></div></div></td></tr><tr data-index="${packageIndex}"class="dgr-sub-row"style="display:none"><td colspan="2"></td><td colspan="7"><div class="dgr-container"><i class="icofont dgr-warning-icon icofont-warning"></i><div class="dgr-field"><label class="field-label">Dangerous goods description</label> <input name="packages[${packageIndex}][dgr_description]"class="field-input"placeholder=""></div><div class="dgr-field small"style="max-width:50px"><label class="field-label">UN number</label> <input name="packages[${packageIndex}][un_number]"class="field-input"placeholder=""></div><div class="dgr-field small"style="max-width:50px"><label class="field-label">Class</label> <input name="packages[${packageIndex}][dgr_class]"class="field-input"placeholder=""></div></div></td></tr>`;
                let $row = $(row); $('#packagesTable tbody').append($row); $row.filter('.irregularity-sub-row').find('.select2-irregularities').select2({placeholder: 'Select irregularities', allowClear: false, width: '100%'}).next('.select2-container').addClass('select2-irreg-container');
                packageIndex++;
                updatePackageSummary();
            });

            // --- Cost Logic ---
            var costIndex = {{ count($crr->costs) }};
            var costHubs = @json($hubs->values());
            var costCurrencies = @json($currencies->values());

            $('.btn-add-cost').on('click', function () {
                $('#costsTable tbody .empty-row').remove();

                let hubOptions = '<option></option>';
                costHubs.forEach(function (hub) {
                    hubOptions += `<option value="${hub.code}" data-city="${hub.city || ''}" data-country="${hub.country || ''}" data-code="${hub.code || ''}">${hub.code ? hub.code + ' - ' : ''}${hub.hub_name}</option>`;
                });

                let currencyOptions = '<option></option>';
                costCurrencies.forEach(function (cur) {
                    currencyOptions += `<option value="${cur}">${cur}</option>`;
                });

                let row = ` <tr data-index="${costIndex}"><td>${costIndex + 1}</td><td><input type="text" class="crr-input" name="costs[${costIndex}][type]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][carrier]"></td><td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value]"></td><td><select class="crr-input select2-cost-currency" name="costs[${costIndex}][currency]">${currencyOptions}</select></td><td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value_usd]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][invoice_no]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][remarks]"></td><td><select class="crr-input select2-cost-hub" name="costs[${costIndex}][hub_agent]">${hubOptions}</select></td><td><input type="text" class="crr-input" name="costs[${costIndex}][tag]"></td><td class="text-center"><button type="button" class="btn btn-link text-primary p-0 btn-copy-row"><i  class="icofont icofont-copy-alt"></i></button></td><td class="text-center"><button type="button" class="btn btn-link text-danger p-0 btn-remove-row"><i class="icofont icofont-trash"></i></button></td></tr>`;
                let $row = $(row);
                $('#costsTable tbody').append($row);

                $row.find('.select2-cost-hub').select2({
                    placeholder: "Select hub",
                    allowClear: false,
                    width: '100%',
                    templateResult: formatHub,
                    templateSelection: function (hub) {
                        return hub.text;
                    }
                });

                $row.find('.select2-cost-currency').select2({
                    placeholder: "Select currency",
                    allowClear: false,
                    width: '100%'
                });

                costIndex++;
            });

            // Initialize Select2 for existing cost rows
            $('.select2-cost-hub').each(function () {
                $(this).select2({
                    placeholder: "Select hub",
                    allowClear: false,
                    width: '100%',
                    templateResult: formatHub,
                    templateSelection: function (hub) {
                        return hub.text;
                    }
                });
            });

            $('.select2-cost-currency').each(function () {
                $(this).select2({
                    placeholder: "Select currency",
                    allowClear: false,
                    width: '100%'
                });
            });

            // Copy Row Logic
            $(document).on('click', '.btn-copy-row', function () {
                let currentTr = $(this).closest('tr');
                let table = currentTr.closest('table');
                let tableId = table.attr('id');
                let newRow = currentTr.clone();
                let targetIndex = packageIndex;

                if (tableId === 'packagesTable') {
                    // Update names for package row
                    newRow.attr('data-index', targetIndex);
                    newRow.find('td:first').text(targetIndex + 1);

                    newRow.find('input').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']');
                            $(this).attr('name', newName);

                            if (newName.includes('[id]')) {
                                $(this).val('');
                            }
                        }
                    });
                    packageIndex++;
                } else if (tableId === 'costsTable') {
                    newRow.attr('data-index', costIndex);
                    newRow.find('td:first').text(costIndex + 1);

                    newRow.find('.select2-container').remove();
                    newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').val(currentTr.find('select').val());

                    newRow.find('input, select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/costs\[\d+\]/, 'costs[' + costIndex + ']');
                            $(this).attr('name', newName);

                            if (newName.includes('[id]')) {
                                $(this).val('');
                            }
                        }
                    });

                    newRow.find('.select2-cost-hub').select2({
                        placeholder: "Select hub",
                        allowClear: false,
                        width: '100%',
                        templateResult: formatHub,
                        templateSelection: function (hub) {
                            return hub.text;
                        }
                    });

                    newRow.find('.select2-cost-currency').select2({
                        placeholder: "Select currency",
                        allowClear: false,
                        width: '100%'
                    });

                    costIndex++;
                }

                table.find('tbody').append(newRow);
                updatePackageSummary();

                if (tableId === 'packagesTable') {
                    newRow.find('.pkg-dim').first().trigger('input');

                    let currentIdx = currentTr.attr('data-index');

                    let irregRow = table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`).clone();
                    irregRow.find('.select2-container').remove();
                    irregRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                    irregRow.attr('data-index', targetIndex);
                    irregRow.find('select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']'));
                        }
                    });

                    let sourceSelectVal = table.find(`.irregularity-sub-row[data-index="${currentIdx}"] select`).val();
                    irregRow.find('select').val(sourceSelectVal);

                    newRow.after(irregRow);
                    irregRow.find('.select2-irregularities').select2({
                        placeholder: "Select irregularities",
                        allowClear: false,
                        width: '100%'
                    }).next('.select2-container').addClass('select2-irreg-container');

                    let dgrRow = table.find(`.dgr-sub-row[data-index="${currentIdx}"]`).clone();
                    dgrRow.attr('data-index', targetIndex);
                    dgrRow.find('input').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/packages\[\d+\]/, 'packages[' + targetIndex + ']'));
                        }
                    });

                    irregRow.after(dgrRow);

                    newRow.find('.pkg-is-irregular').prop('checked', currentTr.find('.pkg-is-irregular').is(':checked'));
                    if (!currentTr.find('.pkg-is-irregular').is(':checked')) {
                        irregRow.hide();
                    } else {
                        irregRow.show();
                    }

                    newRow.find('.pkg-is-dgr').prop('checked', currentTr.find('.pkg-is-dgr').is(':checked'));
                    if (!currentTr.find('.pkg-is-dgr').is(':checked')) {
                        dgrRow.hide();
                    } else {
                        dgrRow.show();
                    }
                }
            });
// Remove Row Logic
            $(document).on('click', '.btn-remove-row', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                if (currentIdx !== undefined) {
                    table.find(`.dgr-sub-row[data-index="${currentIdx}"]`).remove();
                    table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`).remove();
                }
                row.remove();
                if ($('#packagesTable tbody tr:not(.dgr-sub-row):not(.irregularity-sub-row)').length === 0) {
                    $('#packagesTable tbody').append('<tr class="empty-row"><td colspan="13" class="text-center py-4 text-muted">No items added yet. Click "Add item" to start.</td></tr>');
                }
                if ($('#costsTable tbody tr').length === 0) {
                    $('#costsTable tbody').append('<tr class="empty-row"><td colspan="11" class="text-center py-4 text-muted">No costs added yet. Click "Add cost" to start.</td></tr>');
                }
                updatePackageSummary();
            });
function updatePackageSummary() {
                let totalWeight = 0;
                let totalCbm = 0;
                let count = 0;

                $('#packagesTable tbody tr:not(.empty-row):not(.dgr-sub-row):not(.irregularity-sub-row)').each(function () {
                    let weight = parseFloat($(this).find('.pkg-weight').val()) || 0;
                    let cbm = parseFloat($(this).find('.pkg-cbm').val()) || 0;
                    totalWeight += weight;
                    totalCbm += cbm;
                    count++;
                });

                $('#package-summary-text').text(`(Total : ${totalWeight.toFixed(2)} kg, ${count} Packages, ${totalCbm.toFixed(4)} CBM)`);
            }

            // Initial summary
            updatePackageSummary();

            // Automated CBM calculation
            $(document).on('input', '.pkg-dim, .pkg-weight', function () {
                let row = $(this).closest('tr');
                let l = parseFloat(row.find('.pkg-l').val()) || 0;
                let w = parseFloat(row.find('.pkg-w').val()) || 0;
                let h = parseFloat(row.find('.pkg-h').val()) || 0;
                let cbm = (l * w * h) / 1000000;
                row.find('.pkg-cbm').val(cbm.toFixed(4));
                updatePackageSummary();
            });

            // Toggle DGR sub-row
            $(document).on('change', '.pkg-is-dgr', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                let dgrRow = table.find(`.dgr-sub-row[data-index="${currentIdx}"]`);
                if ($(this).is(':checked')) {
                    dgrRow.fadeIn(200);
                } else {
                    dgrRow.fadeOut(200);
                }
            });

            // Toggle Irregularity sub-row
            $(document).on('change', '.pkg-is-irregular', function () {
                let row = $(this).closest('tr');
                let table = row.closest('table');
                let currentIdx = row.attr('data-index');
                let irregRow = table.find(`.irregularity-sub-row[data-index="${currentIdx}"]`);
                if ($(this).is(':checked')) {
                    irregRow.fadeIn(200);
                } else {
                    irregRow.fadeOut(200);
                }
            });

            // --- Realtime Customs Value USD calculation for Edit ---
            function calculateEditCustomsUSD() {
                let customsValue = parseFloat($('#edit_customs_value').val()) || 0;
                let selectedOption = $('#edit_currency_select').find(':selected');
                let rate = parseFloat(selectedOption.data('rate')) || 0;

                let customsUSD = 0;
                if (rate > 0) {
                    customsUSD = customsValue / rate;
                }

                $('#edit_customs_value_usd_display').text(customsUSD.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#edit_customs_value_usd_hidden').val(customsUSD.toFixed(2));
            }

            $(document).on('input', '#edit_customs_value', calculateEditCustomsUSD);
            $(document).on('change', '#edit_currency_select', calculateEditCustomsUSD);

            // Initial calculation on load
            calculateEditCustomsUSD();

            // Main Tab Switching
            $('.stock-tab').on('click', function () {
                var tabId = $(this).data('tab');
                $('.stock-tab').removeClass('active');
                $(this).addClass('active');
                $('.stock-tab-content').hide().removeClass('active');
                $('#' + tabId).show().addClass('active');
            });

            // Right Panel Tab Switching
            $('.panel-tab').on('click', function () {
                var panelId = $(this).data('panel');
                $('.panel-tab').removeClass('active').css({ 'color': '#94a3b8', 'border-bottom': 'none', 'font-weight': '400' });
                $(this).addClass('active').css({ 'color': '#008080', 'border-bottom': '2px solid #008080', 'font-weight': '600' });
                $('.panel-tab-content').hide().removeClass('active');
                $('#' + panelId).show().addClass('active');
            });

            // More Actions Dropdown
            $('.btn-more-circle').on('click', function (e) {
                e.stopPropagation();
                $('.more-dropdown').toggleClass('show');
            });

            $(document).on('click', function () {
                $('.more-dropdown').removeClass('show');
            });
            // --- DOCUMENT MANAGEMENT LOGIC ---
            const dropzone = $('#crr-dropzone');
            const fileInput = $('#crr-file-input');
            const docList = $('#crr-doc-list');
            const docCountBadge = $('.doc-count');
            const crrId = "{{ $crr->id }}";

            function initCrrDocTypeSelect($select) {
                if (!$select || !$select.length) {
                    return;
                }
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    width: '100%',
                    dropdownParent: $(document.body),
                    minimumResultsForSearch: 0,
                    dropdownCssClass: 'doc-type-select2-dropdown'
                });
            }

            // Click to browse
            dropzone.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.trigger('click');
            });

            fileInput.on('change', function () {
                handleFiles(this.files);
            });

            // Drag and drop events
            dropzone.on('dragover', function (e) {
                e.preventDefault();
                $(this).css('border-color', '#94a3b8').css('background', '#f8fafc');
            });

            dropzone.on('dragleave', function (e) {
                e.preventDefault();
                $(this).css('border-color', '#cbd5e1').css('background', '#fff');
            });

            dropzone.on('drop', function (e) {
                e.preventDefault();
                $(this).css('border-color', '#cbd5e1').css('background', '#fff');
                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files);
            });

            function handleFiles(files) {
                if (files.length === 0) return;

                for (let i = 0; i < files.length; i++) {
                    uploadFile(files[i]);
                }
                // Reset input
                fileInput.val('');
            }

            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function buildDocTypeOptionsHtml(selectedType, typeOptions) {
                const options = Array.isArray(typeOptions) && typeOptions.length
                    ? typeOptions
                    : @json(\App\Models\CrrDocument::fileTypeOptionsWithCustom());
                let html = '';
                let hasSelected = false;
                options.forEach(function (opt) {
                    const selected = opt === selectedType ? ' selected' : '';
                    if (selected) hasSelected = true;
                    html += `<option value="${escapeHtml(opt)}"${selected}>${escapeHtml(opt)}</option>`;
                });
                if (selectedType && !hasSelected) {
                    html += `<option value="${escapeHtml(selectedType)}" selected>${escapeHtml(selectedType)}</option>`;
                }
                return html;
            }

            function buildDocItemHtml(doc) {
                const selectedType = doc.file_type || 'Unspecified';
                return `
                    <div class="doc-item" data-id="${doc.id}">
                        <div class="doc-main">
                            <a href="${escapeHtml(doc.file_url)}" target="_blank" class="doc-name" title="${escapeHtml(doc.file_name)}">
                                ${escapeHtml(doc.file_name)}
                            </a>
                            <select class="doc-type-select" data-id="${doc.id}">
                                ${buildDocTypeOptionsHtml(selectedType, doc.type_options)}
                            </select>
                        </div>
                        <div class="doc-side">
                            <div class="doc-side-row">
                                <div class="doc-internal checkbox-fade fade-in-primary">
                                    <label>
                                        <input type="checkbox" class="doc-internal-check" data-id="${doc.id}" ${doc.is_internal ? 'checked' : ''}>
                                        <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                    </label>
                                </div>
                                <i class="ti-trash doc-trash delete-doc" data-id="${doc.id}" title="Delete"></i>
                            </div>
                            <span class="doc-date">${escapeHtml(doc.date)}</span>
                        </div>
                    </div>
                `;
            }

            function uploadFile(file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('stocks.documents.upload', $crr->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('.no-docs-msg').remove();
                        docList.append(buildDocItemHtml(response));
                        initCrrDocTypeSelect(docList.find('.doc-item[data-id="' + response.id + '"] .doc-type-select'));
                        updateDocCount(1);
                        toastr.success('File uploaded successfully');
                    },
                    error: function (xhr) {
                        const error = xhr.responseJSON ? xhr.responseJSON.error : 'Upload failed';
                        toastr.error(error);
                    }
                });
            }

            $('#crr-documents-panel .doc-type-select').each(function () {
                initCrrDocTypeSelect($(this));
            });

            $(document).on('change', '#crr-documents-panel .doc-type-select', function () {
                const $select = $(this);
                const docId = $select.data('id');
                const fileType = $.trim($select.val());
                if (!docId) return;

                $.ajax({
                    url: '{{ route('stocks.documents.update-type', ':docId') }}'.replace(':docId', docId),
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        file_type: fileType
                    },
                    success: function () {
                        toastr.success('Document type saved');
                    },
                    error: function () {
                        toastr.error('Could not update document type');
                    }
                });
            });

            $(document).on('change', '#crr-documents-panel .doc-internal-check', function () {
                const docId = $(this).data('id');
                const isInternal = $(this).is(':checked') ? 1 : 0;
                if (!docId) return;

                $.ajax({
                    url: '{{ route('stocks.documents.update-internal', ':docId') }}'.replace(':docId', docId),
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_internal: isInternal
                    },
                    error: function () {
                        toastr.error('Could not update internal flag');
                    }
                });
            });

            // Delete document
            $(document).on('click', '.delete-doc', function () {
                const btn = $(this);
                const docId = btn.data('id');
                const docItem = btn.closest('.doc-item');

                if (!confirm('Are you sure you want to delete this document?')) return;

                $.ajax({
                    url: "{{ url('/stocks/documents') }}/" + docId,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            var $select = docItem.find('.doc-type-select');
                            if ($select.hasClass('select2-hidden-accessible')) {
                                $select.select2('destroy');
                            }
                            docItem.fadeOut(300, function () {
                                $(this).remove();
                                updateDocCount(-1);
                                if (docList.children('.doc-item').length === 0) {
                                    docList.append('<div class="no-docs-msg" style="text-align: center; padding: 20px; color: #94a3b8; font-size: 11px;">No documents uploaded yet.</div>');
                                }
                            });
                            toastr.success('Document deleted');
                        } else {
                            toastr.error('Delete failed');
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred');
                    }
                });
            });

            function updateDocCount(delta) {
                let current = parseInt(docCountBadge.text()) || 0;
                docCountBadge.text(current + delta);
            }

            function formatCountry(state) {
                if (!state.id) { return state.text; }
                var flagUrl = $(state.element).data('flag-url');
                if (!flagUrl) { return state.text; }
                var $state = $(
                    '<span><img src="' + flagUrl + '" class="flag-icon" /> ' + state.text + '</span>'
                );
                return $state;
            };

            // Initialize select2-country
            $('.select2-country').select2({
                placeholder: "Select country",
                allowClear: false,
                width: '100%',
                dropdownParent: $('.stock-main-content'),
                templateResult: formatCountry,
                templateSelection: formatCountry
            });

            // Datepicker Initialization
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            // Inline Status Edit Toggle
            $('.status-display').on('click', function (e) {
                e.stopPropagation();
                $(this).hide();
                $('.status-select-wrapper').show();

                var $select = $('.select2-status-inline');
                if (!$select.hasClass("select2-hidden-accessible")) {
                    $select.select2({
                        width: '100%',
                        minimumResultsForSearch: Infinity,
                        dropdownParent: $('.stock-main-content')
                    });
                }
                $select.select2('open');
            });

            $(document).on('click', function (e) {
                if ($(e.target).closest('.sweet-alert, .sweet-overlay').length) {
                    return;
                }

                if (!$(e.target).closest('#status-edit-container, #flags-edit-container, .select2-container').length) {
                    $('.status-select-wrapper').hide();
                    $('.status-display').show();
                }
            });

            var lastStatus = '{{ $crr->status }}';
            var statusLabels = @json(\App\Models\Crr::getStatusLabels());
            var suppressStatusChange = false;
            var statusConfirmOpen = false;

            function closeStockStatusEditor() {
                $('.status-select-wrapper').hide();
                $('.status-display').show();
            }

            function revertStockStatusSelection() {
                suppressStatusChange = true;
                $('.select2-status-inline').val(lastStatus).trigger('change.select2');
                suppressStatusChange = false;
                closeStockStatusEditor();
            }

            function confirmStatusChange(newStatusLabel, onConfirm) {
                var message = 'Change status to "' + newStatusLabel + '"?';

                if (typeof swal === 'function') {
                    statusConfirmOpen = true;
                    swal({
                        title: 'Update status?',
                        text: message,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update',
                        cancelButtonText: 'Cancel',
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        showLoaderOnConfirm: true
                    }, function(isConfirm) {
                        if (!isConfirm) {
                            statusConfirmOpen = false;
                            revertStockStatusSelection();
                            return;
                        }

                        onConfirm();
                    });
                    return;
                }

                if (confirm(message)) {
                    onConfirm();
                } else {
                    revertStockStatusSelection();
                }
            }

            function saveStockStatus(newStatusValue, newStatusLabel) {
                $.ajax({
                    url: '{{ route("stocks.crr.update-status", $crr->id) }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatusValue
                    },
                    success: function(response) {
                        if (response.success) {
                            statusConfirmOpen = false;
                            if (typeof swal === 'function') {
                                swal.close();
                            }
                            window.location.reload();
                        } else {
                            statusConfirmOpen = false;
                            toastr.error('Failed to update status');
                            revertStockStatusSelection();
                        }
                    },
                    error: function() {
                        statusConfirmOpen = false;
                        toastr.error('Error updating status');
                        revertStockStatusSelection();
                    },
                    complete: function() {
                        closeStockStatusEditor();
                    }
                });
            }

            $('.select2-status-inline').on('change', function () {
                if (suppressStatusChange || statusConfirmOpen) {
                    return;
                }

                var newStatusValue = $(this).val();
                var newStatusLabel = statusLabels[newStatusValue] || 'Unknown';

                if (newStatusValue === lastStatus) {
                    closeStockStatusEditor();
                    return;
                }

                confirmStatusChange(newStatusLabel, function() {
                    saveStockStatus(newStatusValue, newStatusLabel);
                });
            });

            function acceptCurrentCrr($button) {
                $.ajax({
                    url: $button.data('accept-url'),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function(response) {
                    if (!response || !response.success) {
                        alert('Could not accept stock.');
                        return;
                    }

                    if (typeof swal === 'function') {
                        swal.close();
                    }

                    window.location.reload();
                }).fail(function(xhr) {
                    if (typeof swal === 'function') {
                        swal.close();
                    }

                    var message = 'Could not accept stock.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    alert(message);
                });
            }

            $('#accept-crr-btn').on('click', function() {
                var $button = $(this);
                var stockNumber = $button.data('stock-number');
                var message = 'Accept stock ' + stockNumber + '?';

                if (typeof swal !== 'function') {
                    if (confirm(message)) {
                        acceptCurrentCrr($button);
                    }
                    return;
                }

                swal({
                    title: 'Accept stock?',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, accept',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        acceptCurrentCrr($button);
                    }
                });
            });

            var stockUpdateFlagsUrl = '{{ route("stocks.crr.update-flags", $crr->id) }}';
            var lastHeaderFlags = @json($crr->flags ?? \App\Models\Crr::defaultFlags());
            var suppressFlagsChange = false;
            var flagsConfirmOpen = false;

            function renderStockHeaderFlags(flags) {
                var $pills = $('#flags-edit-container .flags-pills');
                $pills.find('.summary-flag').remove();

                if (!flags || !flags.length) {
                    if (!$pills.find('.text-muted').length) {
                        $pills.append('<span class="text-muted" style="font-size: 11px;">—</span>');
                    }
                    return;
                }

                $pills.find('.text-muted').remove();
                flags.forEach(function(flag) {
                    $pills.append('<span class="summary-flag">' + $('<div>').text(flag).html() + '</span>');
                });
            }

            function closeStockFlagsEditor() {
                $('#flags-edit-container .flags-select-wrapper').hide();
                $('#flags-edit-container .flags-display').show();
            }

            $('#flags-edit-container .flags-display').on('click', function(e) {
                e.stopPropagation();
                $('.status-select-wrapper').hide();
                $('.status-display').show();
                $(this).hide();
                $('#flags-edit-container .flags-select-wrapper').show();

                var $select = $('.select2-flags-inline');
                if (!$select.hasClass('select2-hidden-accessible')) {
                    $select.select2({
                        width: '100%',
                        dropdownParent: $('.stock-main-content')
                    });
                }
                $select.select2('open');
            });

            $(document).on('click', function(e) {
                if ($(e.target).closest('.sweet-alert, .sweet-overlay').length) {
                    return;
                }

                if (!$(e.target).closest('#flags-edit-container, .select2-container').length) {
                    closeStockFlagsEditor();
                }
            });

            function normalizeFlagsValue(value) {
                if (!value) {
                    return [];
                }

                return Array.isArray(value) ? value : [value];
            }

            function formatFlagsLabel(flags) {
                var normalized = normalizeFlagsValue(flags);
                return normalized.length ? normalized.join(', ') : 'None';
            }

            function revertStockFlagsSelection() {
                var currentFlags = normalizeFlagsValue(lastHeaderFlags);
                suppressFlagsChange = true;
                $('.select2-flags-inline').val(currentFlags.length === 1 ? currentFlags[0] : currentFlags).trigger('change.select2');
                suppressFlagsChange = false;
                closeStockFlagsEditor();
            }

            function confirmFlagsChange(newFlags, onConfirm) {
                var message = 'Change flags to "' + formatFlagsLabel(newFlags) + '"?';

                if (typeof swal === 'function') {
                    flagsConfirmOpen = true;
                    swal({
                        title: 'Update flags?',
                        text: message,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update',
                        cancelButtonText: 'Cancel',
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        showLoaderOnConfirm: true
                    }, function(isConfirm) {
                        if (!isConfirm) {
                            flagsConfirmOpen = false;
                            revertStockFlagsSelection();
                            return;
                        }

                        onConfirm();
                    });
                    return;
                }

                if (confirm(message)) {
                    onConfirm();
                } else {
                    revertStockFlagsSelection();
                }
            }

            function saveStockFlags(newFlags) {
                $.ajax({
                    url: stockUpdateFlagsUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        flags: normalizeFlagsValue(newFlags)
                    },
                    success: function(response) {
                        if (response.success) {
                            flagsConfirmOpen = false;
                            if (typeof swal === 'function') {
                                swal.close();
                            }
                            window.location.reload();
                        } else {
                            flagsConfirmOpen = false;
                            toastr.error('Failed to update flags');
                            revertStockFlagsSelection();
                        }
                    },
                    error: function(xhr) {
                        flagsConfirmOpen = false;
                        toastr.error((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Error updating flags');
                        revertStockFlagsSelection();
                    },
                    complete: function() {
                        closeStockFlagsEditor();
                    }
                });
            }

            $('.select2-flags-inline').on('change', function() {
                if (suppressFlagsChange || flagsConfirmOpen) {
                    return;
                }

                var newFlags = normalizeFlagsValue($(this).val());
                var previousFlags = normalizeFlagsValue(lastHeaderFlags).slice().sort().join('|');
                var nextFlags = newFlags.slice().sort().join('|');

                if (previousFlags === nextFlags) {
                    closeStockFlagsEditor();
                    return;
                }

                confirmFlagsChange(newFlags, function() {
                    saveStockFlags(newFlags);
                });
            });

            // Landed Goods logic
            $('#landed-goods-check').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#landed-vessel-wrapper').fadeIn(200);
                    $('#header-landed-flag').show();

                    $('#supplier-select-wrapper').hide();
                    $('#supplier-select').prop('disabled', true);

                    $('#supplier-input-wrapper').show();
                    $('#supplier-input').prop('disabled', false).val('EX VESSEL');

                    // Initialize vessel select2 if not already done
                    $('#landed-from-vessel').select2({
                        placeholder: "Select vessel",
                        allowClear: true,
                        width: '100%',
                        templateResult: formatVessel,
                        templateSelection: function (state) {
                            return state.text;
                        }
                    });
                } else {
                    $('#landed-vessel-wrapper').fadeOut(200);
                    $('#header-landed-flag').hide();

                    $('#supplier-input-wrapper').hide();
                    $('#supplier-input').prop('disabled', true);

                    $('#supplier-select-wrapper').show();
                    $('#supplier-select').prop('disabled', false);
                }
            });

            // Trigger on load to handle initial state
            if ($('#landed-goods-check').is(':checked')) {
                $('#landed-goods-check').trigger('change');
            }
        });
    </script>
<script>
    // Supplier quick-add: Add "name" option opens modal (never saves free-text into the select).
    jQuery(function ($) {
        var $supplierSelect = $('#supplier-select');
        var $modal = $('#add-supplier-modal');
        if (!$supplierSelect.length || !$modal.length) {
            return;
        }

        if (!$modal.parent().is('body')) {
            $modal.appendTo('body');
        }

        // Remove junk free-text options left by earlier Select2 tags (keep only server-known suppliers).
        $supplierSelect.find('option').each(function () {
            var $opt = $(this);
            var val = $.trim($opt.val() || '');
            if (val === '') {
                return;
            }
            if ($opt.attr('data-known') !== '1') {
                $opt.remove();
            }
        });

        var knownNames = {};
        $supplierSelect.find('option').each(function () {
            var val = $.trim($(this).val() || '');
            if (val) {
                knownNames[val.toLowerCase()] = true;
            }
        });

        var previousValue = $supplierSelect.val();
        if (previousValue && !knownNames[String(previousValue).toLowerCase()]) {
            previousValue = null;
            $supplierSelect.val(null);
        }

        var modalOpenScheduled = false;

        if ($supplierSelect.hasClass('select2-hidden-accessible')) {
            $supplierSelect.select2('destroy');
        }

        function isNewSupplierData(data) {
            if (!data) {
                return false;
            }
            if (data.newTag === true) {
                return true;
            }
            var id = String(data.id || '');
            if (id.indexOf('__new__:') === 0) {
                return true;
            }
            if (!id) {
                return false;
            }
            return !knownNames[id.toLowerCase()];
        }

        function getNewTerm(data) {
            if (!data) {
                return '';
            }
            if (data.term) {
                return String(data.term);
            }
            var id = String(data.id || '');
            if (id.indexOf('__new__:') === 0) {
                return id.slice(8);
            }
            var text = String(data.text || '');
            var match = text.match(/^Add\s+"(.+)"$/);
            if (match) {
                return match[1];
            }
            return id || text;
        }

        function clearTempOptions() {
            $supplierSelect.find('option').each(function () {
                var $opt = $(this);
                var val = String($opt.val() || '');
                if (val.indexOf('__new__:') === 0 || $opt.attr('data-select2-tag') || $opt.attr('data-known') !== '1') {
                    if (val !== '') {
                        $opt.remove();
                    }
                }
            });
        }

        function restorePreviousSelection() {
            clearTempOptions();
            $supplierSelect.val(previousValue || null).trigger('change.select2');
        }

        function openAddSupplierModal(name) {
            if (modalOpenScheduled) {
                return;
            }
            modalOpenScheduled = true;

            try { $supplierSelect.select2('close'); } catch (e) {}
            restorePreviousSelection();

            var form = document.getElementById('add-supplier-form');
            if (form) {
                form.reset();
            }
            $('.modal-supplier-country, .modal-supplier-currency').val(null).trigger('change');
            $('#add-supplier-error').hide().text('');
            $('#modal-supplier-name').val($.trim(name || ''));

            setTimeout(function () {
                $modal.modal('show');
                modalOpenScheduled = false;
                setTimeout(function () {
                    $('#modal-supplier-name').trigger('focus');
                }, 200);
            }, 50);
        }

        function formatModalCountry(state) {
            if (!state.id) {
                return state.text;
            }
            var flagUrl = $(state.element).data('flag-url');
            if (!flagUrl) {
                return state.text;
            }
            return $('<span><img src="' + flagUrl + '" class="flag-icon" alt="" /> ' +
                $('<div>').text(state.text).html() + '</span>');
        }

        function initModalSupplierSelect2() {
            $('.modal-supplier-country').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $modal,
                    templateResult: formatModalCountry,
                    templateSelection: formatModalCountry
                });
            });

            $('.modal-supplier-currency').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $modal
                });
            });
        }

        initModalSupplierSelect2();
        $modal.off('shown.bs.modal.supplierSelect2').on('shown.bs.modal.supplierSelect2', function () {
            initModalSupplierSelect2();
        });

        $supplierSelect.select2({
            placeholder: 'Select supplier',
            tags: true,
            allowClear: false,
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term || '');
                if (term === '') {
                    return null;
                }
                if (knownNames[term.toLowerCase()]) {
                    return null;
                }
                return {
                    id: '__new__:' + term,
                    text: 'Add "' + term + '"',
                    newTag: true,
                    term: term
                };
            },
            insertTag: function (data, tag) {
                data.push(tag);
            },
            templateResult: function (s) {
                if (s.loading) {
                    return s.text;
                }
                if (isNewSupplierData(s)) {
                    return $('<span class="supplier-add-link"></span>').text('Add "' + getNewTerm(s) + '"');
                }
                if (!s.id || !s.element) {
                    return $('<span></span>').text(s.text || '');
                }
                var address = $(s.element).data('address') || '';
                var city = $(s.element).data('city') || '';
                var country = $(s.element).data('country') || '';
                var locationText = [address, city, country].filter(Boolean).join(', ');
                var $res = $('<div class="select2-result-supplier"></div>');
                $res.append($('<div class="select2-result-supplier__title"></div>').text(s.text));
                $res.append($('<div class="select2-result-supplier__location"></div>').text(locationText));
                return $res;
            },
            templateSelection: function (s) {
                if (isNewSupplierData(s)) {
                    return '';
                }
                return s.text || '';
            }
        });

        $supplierSelect
            .off('.supplierAdd')
            .on('select2:opening.supplierAdd', function () {
                previousValue = $supplierSelect.val();
                clearTempOptions();
            })
            .on('select2:select.supplierAdd', function (e) {
                var data = (e.params && e.params.data) || {};
                if (!isNewSupplierData(data)) {
                    previousValue = $supplierSelect.val();
                    return;
                }
                openAddSupplierModal(getNewTerm(data));
            });

        // Capture-phase click on Add "..." / unknown option while supplier dropdown is open.
        document.addEventListener('click', function (e) {
            if (!$supplierSelect.next('.select2-container').hasClass('select2-container--open')) {
                return;
            }

            var optionEl = e.target && e.target.closest
                ? e.target.closest('.select2-results__option')
                : null;
            if (!optionEl) {
                return;
            }

            var data = $(optionEl).data('data');
            var optionText = $.trim($(optionEl).text() || '');
            var isAdd = isNewSupplierData(data) ||
                /^Add\s+".+"$/.test(optionText) ||
                $(optionEl).find('.supplier-add-link').length > 0;
            if (!isAdd) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }

            var term = isNewSupplierData(data)
                ? getNewTerm(data)
                : ((optionText.match(/^Add\s+"(.+)"$/) || [])[1] || optionText);
            openAddSupplierModal(term);
        }, true);

        $('#add-supplier-form').off('submit.supplierAdd').on('submit.supplierAdd', function (e) {
            e.preventDefault();
            var $btn = $('#add-supplier-save-btn');
            var $error = $('#add-supplier-error');
            $error.hide().text('');
            $btn.prop('disabled', true).text('Saving...');

            var payload = $(this).serializeArray();
            payload.push({ name: '_token', value: @json(csrf_token()) });

            $.ajax({
                url: @json(route('suppliers.store')),
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: payload,
                success: function (response) {
                    var supplier = (response && response.supplier) || {};
                    var name = $.trim(supplier.supplier_name || $('#modal-supplier-name').val());

                    if (name) {
                        clearTempOptions();
                        knownNames[name.toLowerCase()] = true;

                        if ($supplierSelect.find('option').filter(function () {
                            return $(this).val() === name;
                        }).length === 0) {
                            $supplierSelect.append(
                                $('<option></option>')
                                    .val(name)
                                    .text(name)
                                    .attr('data-known', '1')
                                    .attr('data-address', supplier.supplier_address || '')
                                    .attr('data-city', supplier.city || '')
                                    .attr('data-country', supplier.country || '')
                            );
                        }

                        previousValue = name;
                        $supplierSelect.val(name).trigger('change');
                    }

                    $modal.modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success((response && response.message) || 'Supplier created successfully');
                    }
                },
                error: function (xhr) {
                    var message = 'Could not create supplier.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        message = xhr.responseJSON.errors[firstKey][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $error.text(message).show();
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save supplier');
                }
            });
        });

        // Require package details before save
        $('#crrEditForm').on('submit', function (e) {
            var $errorBox = $('#packages-validation-error');
            var $errorText = $('#packages-validation-error-text');
            var $rows = $('#packagesTable tbody tr:not(.empty-row):not(.dgr-sub-row):not(.irregularity-sub-row)');
            var message = '';
            var incomplete = false;

            $rows.find('.pkg-l, .pkg-w, .pkg-h, .pkg-weight').css('border-color', '');

            if ($rows.length === 0) {
                message = 'Please add at least one package with Length, Width, Height and Weight before saving.';
            } else {
                $rows.each(function () {
                    var $row = $(this);
                    ['pkg-l', 'pkg-w', 'pkg-h', 'pkg-weight'].forEach(function (cls) {
                        var $input = $row.find('.' + cls);
                        var value = $.trim(String($input.val() || ''));
                        if (value === '' || isNaN(parseFloat(value)) || parseFloat(value) <= 0) {
                            incomplete = true;
                            $input.css('border-color', '#dc3545');
                        }
                    });
                });
                if (incomplete) {
                    message = 'Please fill Length, Width, Height and Weight (greater than 0) for all packages before saving.';
                }
            }

            if (message) {
                e.preventDefault();
                $errorText.text(message);
                $errorBox.show();
                $('html, body').animate({
                    scrollTop: $errorBox.offset().top - 80
                }, 300);
                return false;
            }

            $errorText.text('');
            $errorBox.hide();
        });

        $(document).on('click', '#packages-validation-error-close', function () {
            $('#packages-validation-error-text').text('');
            $('#packages-validation-error').hide();
        });

        $modal.off('hidden.bs.modal.supplierAdd').on('hidden.bs.modal.supplierAdd', function () {
            if (String($supplierSelect.val() || '').indexOf('__new__:') === 0 ||
                ($supplierSelect.val() && !knownNames[String($supplierSelect.val()).toLowerCase()])) {
                restorePreviousSelection();
            }
        });
    });
</script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#crrEditForm', 'fallbackUrl' => route('stocks'), 'includeSweetAlert' => false])
@endsection