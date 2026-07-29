@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <!-- Date-range picker css  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <style>
        /* High Density Table Styles */
        #offices-table {
            width: 100% !important;
            min-width: 1410px;
            table-layout: fixed;
            border-collapse: collapse !important;
        }
        #offices-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            vertical-align: middle;
        }
        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #offices-table th,
        #offices-table td {
            white-space: nowrap !important;
        }
        #offices-table thead th:first-child:after,
        #offices-table thead th:first-child:before {
            display: none !important;
        }
        #offices-table th:nth-child(1),
        #offices-table td:nth-child(1) { width: 120px; min-width: 120px; }
        #offices-table th:nth-child(2),
        #offices-table td:nth-child(2) { width: 160px; min-width: 160px; }
        #offices-table th:nth-child(3),
        #offices-table td:nth-child(3) { width: 130px; min-width: 130px; }
        #offices-table th:nth-child(4),
        #offices-table td:nth-child(4) { width: 90px; min-width: 90px; }
        #offices-table th:nth-child(5),
        #offices-table td:nth-child(5) { width: 140px; min-width: 140px; }
        #offices-table th:nth-child(6),
        #offices-table td:nth-child(6) { width: 90px; min-width: 90px; }
        #offices-table th:nth-child(7),
        #offices-table td:nth-child(7) { width: 90px; min-width: 90px; }
        #offices-table th:nth-child(8),
        #offices-table td:nth-child(8) { width: 80px; min-width: 80px; }
        #offices-table th:nth-child(9),
        #offices-table td:nth-child(9) { width: 80px; min-width: 80px; }
        #offices-table th:nth-child(10),
        #offices-table td:nth-child(10) { width: 100px; min-width: 100px; }
        #offices-table th:nth-child(11),
        #offices-table td:nth-child(11) { width: 130px; min-width: 130px; }
        #offices-table th:nth-child(12),
        #offices-table td:nth-child(12) { width: 90px; min-width: 90px; }
        #offices-table th:nth-child(13),
        #offices-table td:nth-child(13) { width: 110px; min-width: 110px; }
        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
            width: 100%;
            position: relative;
        }
        .dataTables_wrapper {
            width: 100%;
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
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            padding: 0 10px;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding-right: 10px;
            margin-right: 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #ced4da;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 !important;
            background: transparent !important;
            width: 100%;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
        }
        #col-Status .select2-container--default .select2-selection--single,
        #col-Status .select2-container--default.select2-container--focus .select2-selection--single,
        #col-Status .select2-container--default.select2-container--open .select2-selection--single {
            background-color: transparent !important;
        }
        #col-Status .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #1e293b !important;
        }
        #col-Status .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent !important;
        }
        #col-Status .select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #64748b transparent !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
        }
        .filter-group i {
            color: #008080;
            font-size: 14px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
            margin-bottom: 10px;
        }
        .clear-filters {
            font-size: 12px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            margin-left: 10px;
            align-self: center;
            display: flex;
            align-items: center;
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        .label {
            border-radius: 4px;
            font-size: 100%;
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
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            min-height: 30px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #4b5563 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            width: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #4b5563 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
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
        
        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
        td a {
            color: rgb(24, 100, 131) !important;
        }

        /* ── Compose reminder modal (same design as manifest email modal) ── */
        #compose-reminder-modal .modal-dialog {
            max-width: 860px;
            margin: 1.75rem auto;
        }
        #compose-reminder-modal .modal-content {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
            position: relative;
        }
        #compose-reminder-modal .compose-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        #compose-reminder-modal .compose-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }
        #compose-reminder-modal .compose-body {
            padding: 16px 20px 8px;
        }
        #compose-reminder-modal .compose-field {
            margin-bottom: 10px;
        }
        #compose-reminder-modal .compose-field-contact {
            position: relative;
        }
        #compose-reminder-modal .compose-field-with-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #compose-reminder-modal .compose-field-with-icon .compose-input {
            flex: 1;
            min-width: 0;
        }
        #compose-reminder-modal .compose-contact-btn {
            width: 34px;
            height: 30px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #f8fafc;
            color: #475569;
            cursor: pointer;
            padding: 0;
        }
        #compose-reminder-modal .compose-contact-btn:hover,
        #compose-reminder-modal .compose-contact-btn.active {
            background: #e0f2fe;
            border-color: #93c5fd;
            color: #0369a1;
        }
        #compose-reminder-modal .compose-contact-btn i {
            font-size: 15px;
            line-height: 1;
        }
        #compose-reminder-modal .compose-contact-picker {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            z-index: 20;
            width: min(360px, calc(100vw - 80px));
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.14);
            overflow: hidden;
        }
        #compose-reminder-modal .compose-contact-picker.open {
            display: block;
        }
        #compose-reminder-modal .compose-contact-search {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 12px;
            font-size: 12px;
            outline: none;
        }
        #compose-reminder-modal .compose-contact-list {
            max-height: 220px;
            overflow-y: auto;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        #compose-reminder-modal .compose-contact-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }
        #compose-reminder-modal .compose-contact-item:hover {
            background: #f0f9ff;
        }
        #compose-reminder-modal .compose-contact-name {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        #compose-reminder-modal .compose-contact-email {
            margin: 2px 0 0;
            font-size: 11px;
            color: #64748b;
        }
        #compose-reminder-modal .compose-contact-empty {
            padding: 14px 12px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        #compose-reminder-modal .compose-input {
            width: 100%;
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 5px 12px;
            font-size: 13px;
            color: #111827;
            background: #fff;
        }
        #compose-reminder-modal .compose-input:focus {
            outline: none;
            border-color: #93c5fd;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }
        #compose-reminder-modal .compose-input::placeholder {
            color: #9ca3af;
        }
        #compose-reminder-modal .compose-editor-wrap {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }
        #compose-reminder-modal .compose-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2px;
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }
        #compose-reminder-modal .compose-toolbar select {
            height: 28px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            font-size: 12px;
            padding: 0 6px;
            margin-right: 4px;
            background: #fff;
            color: #374151;
        }
        #compose-reminder-modal .compose-tool-btn {
            min-width: 28px;
            height: 28px;
            border: none;
            background: transparent;
            color: #4b5563;
            border-radius: 3px;
            font-size: 13px;
            line-height: 1;
            padding: 0 6px;
            cursor: pointer;
        }
        #compose-reminder-modal .compose-tool-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }
        #compose-reminder-modal .compose-editor {
            min-height: 220px;
            max-height: 340px;
            overflow-y: auto;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.5;
            color: #111827;
            outline: none;
            white-space: pre-wrap;
        }
        #compose-reminder-modal .compose-editor:empty:before {
            content: attr(data-placeholder);
            color: #9ca3af;
            pointer-events: none;
        }
        #compose-reminder-modal .compose-attach-row {
            margin-top: 14px;
        }
        #compose-reminder-modal .btn-compose-attach {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #3b82f6;
            border: 1px solid #3b82f6;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            padding: 7px 14px;
            cursor: pointer;
        }
        #compose-reminder-modal .btn-compose-attach:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
        #compose-reminder-modal .compose-attach-hint {
            margin: 6px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }
        #compose-reminder-modal .compose-attach-previews {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }
        #compose-reminder-modal .compose-attach-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #f8fafc;
            overflow: hidden;
            position: relative;
        }
        #compose-reminder-modal .compose-attach-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            z-index: 2;
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 50%;
            background: #ef4444;
            color: #fff;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        #compose-reminder-modal .compose-attach-thumb {
            height: 90px;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        #compose-reminder-modal .compose-attach-thumb iframe,
        #compose-reminder-modal .compose-attach-thumb img {
            width: 100%;
            height: 100%;
            border: 0;
            object-fit: cover;
            background: #fff;
            pointer-events: none;
        }
        #compose-reminder-modal .compose-attach-thumb .attach-icon {
            font-size: 36px;
            color: #64748b;
        }
        #compose-reminder-modal .compose-attach-meta {
            padding: 8px 10px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        #compose-reminder-modal .compose-attach-name {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #compose-reminder-modal .compose-attach-type {
            margin: 2px 0 0;
            font-size: 10px;
            color: #6b7280;
        }
        #compose-reminder-modal .compose-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px 18px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        #compose-reminder-modal .compose-footer-right {
            display: flex;
            gap: 8px;
        }
        #compose-reminder-modal .btn-compose-discard {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ef4444;
            border: 1px solid #ef4444;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            padding: 8px 14px;
            cursor: pointer;
        }
        #compose-reminder-modal .btn-compose-discard:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
        }
        #compose-reminder-modal .btn-compose-send {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #14b8a6;
            border: 1px solid #14b8a6;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
        }
        #compose-reminder-modal .btn-compose-send:hover {
            background: #0d9488;
            border-color: #0d9488;
            color: #fff;
        }
        #compose-reminder-modal .btn-compose-send:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        #compose-reminder-modal .compose-send-loader {
            display: none;
            position: absolute;
            inset: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.82);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
            border-radius: inherit;
        }
        #compose-reminder-modal.compose-sending .compose-send-loader {
            display: flex;
        }
        #compose-reminder-modal .compose-send-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid #ccfbf1;
            border-top-color: #14b8a6;
            border-radius: 50%;
            animation: reminder-spin 0.75s linear infinite;
        }
        #compose-reminder-modal .compose-send-loader-text {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #0f766e;
        }
        @keyframes reminder-spin {
            to { transform: rotate(360deg); }
        }
    </style>
    @include('partials.searchable-filter-multiselect-styles')
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
                                        <!-- Base Style - Compact start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="d-flex justify-content-between align-items-start pt-2">
                                                    <div style="width: 100%;">
                                                        <div class="row no-gutters">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Shipment no" selected>Shipment no</option>
                                                                    <option value="Customer" selected>Customer</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Port of destination" selected>Port of destination</option>
                                                                    <option value="Status" selected>Status</option>
                                                                    <option value="Created by" selected>Created by</option>
                                                                </select>
                                                            </div>

                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select id="filter-account-manager" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager->name }}">{{ $manager->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" id="filter-shipment-no" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select id="filter-customer" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select id="filter-vessel" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" id="filter-port-destination" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-Status" class="custom-col" style="flex: 0 0 180px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Status</span>
                                                                    <select id="filter-status" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($statuses as $status)
                                                                            <option value="{{ $status }}">{{ $status }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Created-by" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Created by</span>
                                                                    <select id="filter-created-by" class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->name }}">{{ $creator->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <a class="clear-filters">Clear filters</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="table-scroll-wrapper">
                                                    <table id="offices-table" class="table office-table mb-0">
                                                        <colgroup>
                                                            <col style="width: 120px">
                                                            <col style="width: 160px">
                                                            <col style="width: 130px">
                                                            <col style="width: 90px">
                                                            <col style="width: 140px">
                                                            <col style="width: 90px">
                                                            <col style="width: 90px">
                                                            <col style="width: 80px">
                                                            <col style="width: 80px">
                                                            <col style="width: 100px">
                                                            <col style="width: 130px">
                                                            <col style="width: 90px">
                                                            <col style="width: 110px">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment no</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>ETD</th>
                                                                <th>ETA</th>
                                                                <th>Status</th>
                                                                <th>Mark as arrived</th>
                                                                <th>Rem. sent</th>
                                                                <th>Send reminder</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($shipments as $shipment)
                                                            @php
                                                                $departureDisplay = $shipment->departure_port_code ?: $shipment->partyDisplay($shipment->departure, $partyNames);
                                                                $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
                                                                $etd = $shipment->service_etd;
                                                                $eta = $shipment->service_eta;
                                                                $etaOverdue = $eta && $eta->startOfDay()->lte(now()->startOfDay());
                                                                $lastReminderSent = $shipment->last_reminder_sent_at
                                                                    ? \Carbon\Carbon::parse($shipment->last_reminder_sent_at)->format('d.m.Y')
                                                                    : '';
                                                            @endphp
                                                            <tr
                                                                data-customers="{{ $shipment->customer_names->implode(',') }}"
                                                                data-vessels="{{ $shipment->vessel_names->implode(',') }}"
                                                                data-shipment-number="{{ $shipment->shipment_number }}"
                                                                data-destination="{{ $shipment->destination_display }}"
                                                                data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
                                                                data-created-by="{{ $shipment->creator?->name ?? '' }}"
                                                                data-status="{{ $shipment->status ?? '' }}"
                                                            >
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <a href="{{ route('shipments.edit', $shipment->id) }}">{{ $shipment->shipment_number }}</a>
                                                                        @if ($shipment->hasOpenIrregularities())
                                                                            <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>{{ $shipment->customer_display }}</td>
                                                                <td>{{ $shipment->vessel_display }}</td>
                                                                <td>{{ $shipment->service ?? '—' }}</td>
                                                                <td>{{ $consigneeDisplay }}</td>
                                                                <td>{{ $departureDisplay ?: '—' }}</td>
                                                                <td>{{ $shipment->destination_display }}</td>
                                                                <td>{{ $etd?->format('d.m.Y') ?? '—' }}</td>
                                                                <td @if($etaOverdue) style="color: #ff5252; font-weight: 500;" @endif>{{ $eta?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>
                                                                    <span class="{{ $shipment->statusBadgeClass() }}" style="padding: 4px 8px; font-weight: 500;">{{ $shipment->status ?? '—' }}</span>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-teal py-1 px-2 mark-arrived-btn"
                                                                        style="font-size: 11px; height: 26px; border-color: #ddd; background: #fff;"
                                                                        data-shipment-id="{{ $shipment->id }}"
                                                                        data-shipment-number="{{ $shipment->shipment_number }}"
                                                                        data-mark-arrived-url="{{ route('shipments.mark-as-arrived', $shipment->id) }}">Mark as arrived</button>
                                                                </td>
                                                                <td class="reminder-sent-date" data-shipment-id="{{ $shipment->id }}">{{ $lastReminderSent }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-teal py-1 pl-2 pr-2 send-reminder-btn"
                                                                        style="font-size: 10px; height: 24px; border-color: #ddd; color: #666;"
                                                                        data-shipment-id="{{ $shipment->id }}"
                                                                        data-preview-url="{{ route('shipments.delivery-status-reminder-mail.preview', $shipment->id) }}"
                                                                        data-send-url="{{ route('shipments.delivery-status-reminder-mail.send', $shipment->id) }}"
                                                                        data-eml-url="{{ route('shipments.delivery-status-reminder-mail', $shipment->id) }}"
                                                                        data-eml-filename="delivery-status-request-{{ $shipment->shipment_number }}.eml">Send reminder</button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="13" class="text-center py-4 text-muted">No shipments found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
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
<!-- Compose Reminder Modal -->
<div class="modal fade" id="compose-reminder-modal" tabindex="-1" role="dialog" aria-labelledby="composeReminderLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="compose-send-loader" aria-live="polite" aria-busy="true">
                <div class="compose-send-spinner" role="status"></div>
                <p class="compose-send-loader-text">Sending email...</p>
            </div>
            <div class="compose-header">
                <h5 class="compose-title" id="composeReminderLabel">Compose New Message</h5>
            </div>
            <div class="compose-body">
                <div class="compose-field">
                    <input type="text" id="reminder-mail-to" class="compose-input" placeholder="To:">
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="reminder-mail-cc" class="compose-input" placeholder="Cc:">
                        <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-cc" title="Contact directory">
                            <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="reminder-mail-cc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="reminder-mail-bcc" class="compose-input" placeholder="Bcc:">
                        <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-bcc" title="Contact directory">
                            <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="reminder-mail-bcc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field">
                    <input type="text" id="reminder-mail-subject" class="compose-input" placeholder="Subject:">
                </div>
                <div class="compose-editor-wrap">
                    <div class="compose-toolbar">
                        <select id="reminder-font-size" title="Text style">
                            <option value="3">A Normal text</option>
                            <option value="2">Small</option>
                            <option value="4">Large</option>
                            <option value="5">Heading</option>
                        </select>
                        <button type="button" class="compose-tool-btn" data-cmd="bold" title="Bold"><strong>B</strong></button>
                        <button type="button" class="compose-tool-btn" data-cmd="italic" title="Italic"><em>I</em></button>
                        <button type="button" class="compose-tool-btn" data-cmd="underline" title="Underline"><u>U</u></button>
                        <button type="button" class="compose-tool-btn" data-cmd="fontSize" data-value="2" title="Small">Small</button>
                        <button type="button" class="compose-tool-btn" data-cmd="formatBlock" data-value="blockquote" title="Quote"><i class="ti-quote-left"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertUnorderedList" title="Bulleted list"><i class="ti-list"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="ti-list-ol"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="outdent" title="Outdent"><i class="ti-shift-left-alt"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="indent" title="Indent"><i class="ti-shift-right-alt"></i></button>
                    </div>
                    <div id="reminder-mail-body" class="compose-editor" contenteditable="true" data-placeholder="Your Message Here...."></div>
                </div>
                <div class="compose-attach-row">
                    <button type="button" class="btn-compose-attach" id="reminder-attachment-btn">
                        <i class="ti-clip"></i> Attachment
                    </button>
                    <p class="compose-attach-hint">Maximum 20MB per file</p>
                    <div class="compose-attach-previews" id="reminder-attach-previews"></div>
                    <input type="file" id="reminder-attachment-input" multiple style="display:none;"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip">
                </div>
            </div>
            <div class="compose-footer">
                <button type="button" class="btn-compose-discard" id="reminder-mail-discard">
                    <i class="ti-close"></i> Discard
                </button>
                <div class="compose-footer-right">
                    <button type="button" class="btn-compose-send" id="reminder-mail-send">
                        <i class="ti-email"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Compose Reminder Modal -->

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
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    @include('partials.searchable-filter-multiselect-script')
    <!-- date-range-picker js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function() {
            initializeSearchableFilterMultiselect(
                '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by'
            );

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

            $('#filter-multiselect').multiselect('selectAll', false);
            $('#filter-multiselect').multiselect('updateButtonText');

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Port of destination', id: 'col-Port-of-destination'},
                    {val: 'Status', id: 'col-Status'},
                    {val: 'Created by', id: 'col-Created-by'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            toggleFilterVisibility();

            var table = $('#offices-table').DataTable({
                "dom": 'rt<"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "targets": [10, 11, 12], "orderable": false }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                },
                "drawCallback": function() {
                    this.api().columns.adjust();
                }
            });

            $(window).on('resize', function() {
                table.columns.adjust();
            });

            function rowData($row, key) {
                return String($row.attr('data-' + key) || '');
            }

            function getFilterText(selector) {
                return String($(selector).val() || '').toLowerCase().trim();
            }

            function matchesSelectedValues(selectedValues, rowValue) {
                if (!selectedValues || selectedValues.length === 0) {
                    return true;
                }

                return selectedValues.indexOf(String(rowValue || '')) !== -1;
            }

            function matchesAnySelectedValues(selectedValues, rowValuesString) {
                if (!selectedValues || selectedValues.length === 0) {
                    return true;
                }

                var rowValues = rowValuesString.split(',').map(function(value) {
                    return value.trim();
                }).filter(Boolean);

                return selectedValues.some(function(selectedValue) {
                    return rowValues.indexOf(selectedValue) !== -1;
                });
            }

            function matchesContains(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '').toLowerCase().indexOf(filterValue) !== -1;
            }

            $('#filter-shipment-no, #filter-port-destination, #filter-customer, #filter-vessel, #filter-account-manager, #filter-status, #filter-created-by').on('change keyup', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);

                if (!matchesAnySelectedValues($('#filter-customer').val() || [], rowData($row, 'customers'))) {
                    return false;
                }

                if (!matchesAnySelectedValues($('#filter-vessel').val() || [], rowData($row, 'vessels'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#filter-account-manager').val() || [], rowData($row, 'account-manager'))) {
                    return false;
                }

                if (!matchesSelectedValues($('#filter-created-by').val() || [], rowData($row, 'created-by'))) {
                    return false;
                }

                var selectedStatuses = $('#filter-status').val() || [];
                if (!matchesSelectedValues(selectedStatuses, rowData($row, 'status'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-shipment-no'), rowData($row, 'shipment-number'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-port-destination'), rowData($row, 'destination'))) {
                    return false;
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                clearSearchableFilterMultiselect(
                    '#filter-account-manager, #filter-customer, #filter-vessel, #filter-status, #filter-created-by'
                );
                $('.filter-input:not(select)').val('').trigger('keyup');
                table.columns().search('').draw();
            });

            // ── Compose reminder modal state ─────────────────────
            var pendingReminderMail = null;
            var reminderContactsUrl = @json(route('api.mail-contacts'));
            var reminderContactsSearchTimer = null;

            function closeReminderContactPickers() {
                $('#compose-reminder-modal .compose-contact-picker').removeClass('open');
                $('#compose-reminder-modal .compose-contact-btn').removeClass('active');
            }

            function appendReminderEmail(fieldId, email) {
                email = $.trim(email || '');
                if (!email) {
                    return;
                }

                var $field = $('#' + fieldId);
                var current = $.trim($field.val() || '');
                var parts = current
                    ? current.split(/[;,]+/).map(function(part) {
                        return $.trim(part);
                    }).filter(Boolean)
                    : [];

                var exists = parts.some(function(part) {
                    return part.toLowerCase() === email.toLowerCase();
                });

                if (!exists) {
                    parts.push(email);
                }

                $field.val(parts.join(', ')).trigger('change');
            }

            function renderReminderContacts($picker, contacts) {
                var $list = $picker.find('.compose-contact-list').empty();

                if (!contacts.length) {
                    $list.append('<li class="compose-contact-empty">No contacts found</li>');
                    return;
                }

                contacts.forEach(function(contact) {
                    var $item = $('<li class="compose-contact-item"></li>')
                        .attr('data-email', contact.email || '')
                        .append($('<p class="compose-contact-name"></p>').text(contact.name || contact.email || 'Contact'))
                        .append($('<p class="compose-contact-email"></p>').text(contact.email || ''));
                    $list.append($item);
                });
            }

            function loadReminderContacts($picker, query) {
                $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Loading...</li>');

                $.ajax({
                    url: reminderContactsUrl,
                    method: 'GET',
                    dataType: 'json',
                    data: { q: query || '' }
                })
                    .done(function(response) {
                        renderReminderContacts($picker, (response && response.results) || []);
                    })
                    .fail(function() {
                        $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Could not load contacts</li>');
                    });
            }

            $(document).on('click', '#compose-reminder-modal .compose-contact-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $button = $(this);
                var fieldId = $button.data('target-field');
                var $picker = $('#compose-reminder-modal .compose-contact-picker[data-for="' + fieldId + '"]');
                var alreadyOpen = $picker.hasClass('open');

                closeReminderContactPickers();
                if (alreadyOpen) {
                    return;
                }

                $button.addClass('active');
                $picker.addClass('open');
                $picker.find('.compose-contact-search').val('').focus();
                loadReminderContacts($picker, '');
            });

            $(document).on('input', '#compose-reminder-modal .compose-contact-search', function() {
                var $picker = $(this).closest('.compose-contact-picker');
                var query = $.trim($(this).val() || '');

                clearTimeout(reminderContactsSearchTimer);
                reminderContactsSearchTimer = setTimeout(function() {
                    loadReminderContacts($picker, query);
                }, 200);
            });

            $(document).on('click', '#compose-reminder-modal .compose-contact-item', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var email = $(this).attr('data-email');
                var fieldId = $(this).closest('.compose-contact-picker').attr('data-for');
                appendReminderEmail(fieldId, email);
                closeReminderContactPickers();
            });

            $(document).on('click', '#compose-reminder-modal', function(e) {
                if ($(e.target).closest('.compose-contact-btn, .compose-contact-picker').length) {
                    return;
                }
                closeReminderContactPickers();
            });

            $('#compose-reminder-modal').on('hidden.bs.modal', function() {
                closeReminderContactPickers();
            });

            function plainTextToReminderHtml(text) {
                return String(text || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br>');
            }

            function reminderEditorToPlainText(html) {
                return $('<div>').html(
                    String(html || '')
                        .replace(/<br\s*\/?>/gi, '\n')
                        .replace(/<\/p>/gi, '\n')
                        .replace(/<\/div>/gi, '\n')
                        .replace(/<[^>]+>/g, '')
                ).text();
            }

            function formatReminderFileSize(bytes) {
                if (bytes < 1024) {
                    return bytes + ' B';
                }
                if (bytes < 1024 * 1024) {
                    return (bytes / 1024).toFixed(1) + ' KB';
                }
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function clearReminderAttachments() {
                if (pendingReminderMail && pendingReminderMail.attachments) {
                    pendingReminderMail.attachments.forEach(function(item) {
                        if (item.previewUrl) {
                            URL.revokeObjectURL(item.previewUrl);
                        }
                    });
                }
                $('#reminder-attach-previews').empty();
                $('#reminder-attachment-input').val('');
            }

            function renderReminderAttachments() {
                var $previews = $('#reminder-attach-previews').empty();
                var attachments = pendingReminderMail ? pendingReminderMail.attachments : [];

                (attachments || []).forEach(function(item) {
                    var isPdf = item.file.type === 'application/pdf' || /\.pdf$/i.test(item.file.name);
                    var isImage = /^image\//.test(item.file.type) || /\.(png|jpe?g|gif|webp)$/i.test(item.file.name);
                    var canPreview = !!item.previewUrl;
                    var typeLabel = isPdf ? 'PDF' : (isImage ? 'Image' : 'File');
                    var $card = $('<div class="compose-attach-card"></div>')
                        .attr('data-preview-url', item.previewUrl || '')
                        .attr('title', canPreview ? ('Preview ' + item.file.name) : item.file.name)
                        .css('cursor', canPreview ? 'pointer' : 'default');
                    var $remove = $('<button type="button" class="compose-attach-remove" title="Remove attachment">&times;</button>')
                        .attr('data-key', item.key);
                    var $thumb = $('<div class="compose-attach-thumb"></div>');

                    if (item.previewUrl && isPdf) {
                        $thumb.append(
                            $('<iframe></iframe>')
                                .attr('src', item.previewUrl)
                                .attr('title', item.file.name)
                                .attr('loading', 'lazy')
                        );
                    } else if (item.previewUrl && isImage) {
                        $thumb.append($('<img alt="">').attr('src', item.previewUrl));
                    } else {
                        $thumb.append('<i class="ti-file attach-icon"></i>');
                    }

                    var $meta = $('<div class="compose-attach-meta"></div>')
                        .append($('<p class="compose-attach-name"></p>').text(item.file.name))
                        .append(
                            $('<p class="compose-attach-type"></p>').text(
                                typeLabel + ' · ' + formatReminderFileSize(item.file.size)
                                + (canPreview ? ' · Click to preview' : '')
                            )
                        );

                    $card.append($remove, $thumb, $meta);
                    $previews.append($card);
                });
            }

            // Rich-text toolbar for reminder modal
            $(document).on('click', '#compose-reminder-modal .compose-tool-btn', function(e) {
                e.preventDefault();
                var cmd = $(this).data('cmd');
                var val = $(this).data('value') || null;
                document.execCommand(cmd, false, val);
                $('#reminder-mail-body').focus();
            });

            $(document).on('change', '#reminder-font-size', function() {
                document.execCommand('fontSize', false, $(this).val());
                $('#reminder-mail-body').focus();
            });

            $(document).on('click', '#reminder-attachment-btn', function() {
                $('#reminder-attachment-input').trigger('click');
            });

            $(document).on('change', '#reminder-attachment-input', function() {
                if (!pendingReminderMail) {
                    return;
                }

                Array.prototype.forEach.call(this.files || [], function(file, index) {
                    if (file.size > 20 * 1024 * 1024) {
                        alert(file.name + ' is larger than the 20MB limit.');
                        return;
                    }

                    var canPreview = file.type === 'application/pdf'
                        || /^image\//.test(file.type)
                        || /\.(pdf|png|jpe?g|gif|webp)$/i.test(file.name);
                    pendingReminderMail.attachments.push({
                        key: 'reminder-local-' + Date.now() + '-' + index,
                        file: file,
                        previewUrl: canPreview ? URL.createObjectURL(file) : null
                    });
                });

                renderReminderAttachments();
                this.value = '';
            });

            $(document).on('click', '#reminder-attach-previews .compose-attach-remove', function() {
                if (!pendingReminderMail) {
                    return;
                }

                var key = String($(this).data('key'));
                pendingReminderMail.attachments = pendingReminderMail.attachments.filter(function(item) {
                    if (String(item.key) !== key) {
                        return true;
                    }
                    if (item.previewUrl) {
                        URL.revokeObjectURL(item.previewUrl);
                    }
                    return false;
                });
                renderReminderAttachments();
            });

            $(document).on('click', '#reminder-attach-previews .compose-attach-card', function(e) {
                if ($(e.target).closest('.compose-attach-remove').length) {
                    return;
                }

                var previewUrl = $(this).attr('data-preview-url');
                if (previewUrl) {
                    window.open(previewUrl, '_blank');
                }
            });

            // Open compose modal when Send reminder is clicked
            $(document).on('click', '.send-reminder-btn', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var previewUrl = $btn.data('preview-url');
                var sendUrl = $btn.data('send-url');
                var shipmentId = $btn.data('shipment-id');
                var originalText = $btn.text();

                if (!previewUrl) {
                    return;
                }

                $btn.prop('disabled', true).text('Preparing...');

                $.ajax({
                    url: previewUrl,
                    method: 'GET',
                    dataType: 'json',
                    headers: { 'Accept': 'application/json' }
                })
                    .done(function(response) {
                        if (!response || !response.success || !response.preview) {
                            alert((response && response.message) || 'Could not prepare reminder email.');
                            return;
                        }

                        var preview = response.preview;
                        pendingReminderMail = {
                            shipmentId: shipmentId,
                            sendUrl: sendUrl || null,
                            attachments: []
                        };

                        $('#reminder-mail-to').val(preview.to || '');
                        $('#reminder-mail-cc').val(preview.cc || '');
                        $('#reminder-mail-bcc').val(preview.bcc || '');
                        $('#reminder-mail-subject').val(preview.subject || '');
                        $('#reminder-mail-body').html(plainTextToReminderHtml(preview.body || ''));
                        renderReminderAttachments();

                        $('#compose-reminder-modal').modal('show');
                    })
                    .fail(function(xhr) {
                        var message = 'Could not prepare reminder email.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text(originalText);
                    });
            });

            // Discard
            $(document).on('click', '#reminder-mail-discard', function() {
                clearReminderAttachments();
                pendingReminderMail = null;
                $('#compose-reminder-modal').modal('hide');
            });

            // Send
            $(document).on('click', '#reminder-mail-send', function() {
                if (!pendingReminderMail || !pendingReminderMail.sendUrl) {
                    alert('Email is not available.');
                    return;
                }

                var $btn = $(this);
                var to = $.trim($('#reminder-mail-to').val() || '');
                var cc = $.trim($('#reminder-mail-cc').val() || '');
                var bcc = $.trim($('#reminder-mail-bcc').val() || '');
                var subject = $.trim($('#reminder-mail-subject').val() || '');
                var body = reminderEditorToPlainText($('#reminder-mail-body').html());

                if (!to) {
                    alert('Please enter at least one recipient in To.');
                    return;
                }
                if (!subject) {
                    alert('Please enter a subject.');
                    return;
                }

                var $modal = $('#compose-reminder-modal');
                var originalHtml = $btn.html();
                $modal.addClass('compose-sending');
                $btn.prop('disabled', true).html('<i class="ti-reload"></i> Sending...');
                $('#reminder-mail-discard, #reminder-attachment-btn').prop('disabled', true);

                var formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('to', to);
                formData.append('cc', cc);
                formData.append('bcc', bcc);
                formData.append('subject', subject);
                formData.append('body', body);
                (pendingReminderMail.attachments || []).forEach(function(item) {
                    formData.append('files[]', item.file, item.file.name);
                });

                $.ajax({
                    url: pendingReminderMail.sendUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false
                })
                    .done(function(response) {
                        if (!response || !response.success) {
                            alert((response && response.message) || 'Could not send email.');
                            return;
                        }

                        var shipmentId = pendingReminderMail.shipmentId;
                        clearReminderAttachments();
                        $modal.modal('hide');
                        pendingReminderMail = null;

                        // Update "Rem. sent" date column
                        var today = new Date();
                        var formatted = ('0' + today.getDate()).slice(-2) + '.' +
                            ('0' + (today.getMonth() + 1)).slice(-2) + '.' +
                            today.getFullYear();
                        $('.reminder-sent-date[data-shipment-id="' + shipmentId + '"]').text(formatted);

                        if (typeof swal === 'function') {
                            swal({
                                title: 'Email sent',
                                text: response.message || 'Reminder email sent successfully.',
                                type: 'success',
                                timer: 4000
                            });
                        } else {
                            alert(response.message || 'Reminder email sent successfully.');
                        }
                    })
                    .fail(function(xhr) {
                        var message = (xhr.responseJSON && xhr.responseJSON.message)
                            || 'Could not send email. Please try again.';
                        alert(message);
                    })
                    .always(function() {
                        $modal.removeClass('compose-sending');
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#reminder-mail-discard, #reminder-attachment-btn').prop('disabled', false);
                    });
            });

            $(document).on('click', '.mark-arrived-btn', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var markArrivedUrl = $btn.data('mark-arrived-url');
                var shipmentNumber = $btn.data('shipment-number');
                var $row = $btn.closest('tr');

                if (!markArrivedUrl) {
                    return;
                }

                function submitMarkAsArrived() {
                    $btn.prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: markArrivedUrl,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .done(function(response) {
                            if (!response || !response.success) {
                                alert((response && response.message) || 'Could not mark shipment as arrived.');
                                return;
                            }

                            table.row($row).remove().draw(false);

                            if (typeof swal === 'function') {
                                swal('Completed', response.message || 'Shipment marked as arrived and completed.', 'success');
                            }
                        })
                        .fail(function(xhr) {
                            var message = 'Could not mark shipment as arrived.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            alert(message);
                        })
                        .always(function() {
                            $btn.prop('disabled', false).text('Mark as arrived');
                        });
                }

                if (typeof swal !== 'function') {
                    if (confirm('Mark shipment ' + shipmentNumber + ' as arrived and set status to Completed?')) {
                        submitMarkAsArrived();
                    }
                    return;
                }

                swal({
                    title: 'Mark as arrived?',
                    text: 'Shipment ' + shipmentNumber + ' will be marked as arrived and status set to Completed.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        submitMarkAsArrived();
                    }
                });
            });
        });
    </script>
@endsection
