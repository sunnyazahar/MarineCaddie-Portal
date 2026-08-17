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
            min-width: 1580px;
            table-layout: fixed;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        .followup-table-area,
        .followup-table-area.table-responsive,
        .card-block > .table-responsive,
        .dt-responsive.table-responsive {
            overflow: visible !important;
        }
        #offices-table thead {
            position: sticky !important;
            top: 0 !important;
            z-index: 20 !important;
            background-color: #fdfdfd !important;
        }
        #offices-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 21 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 13px;
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
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #offices-table .consignee-row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow: hidden;
            white-space: nowrap !important;
        }
        #offices-table .consignee-hub-agent {
            font-weight: 600;
            font-size: 12px;
            color: #05354B;
        }
        #offices-table .consignee-hub-icon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 12px !important;
            width: 12px !important;
            min-width: 12px !important;
            margin: 0 4px 0 0 !important;
            font-size: 12px;
            color: #05354B;
            line-height: 1;
        }
        #offices-table .consignee-hub-agent-text {
            display: block !important;
            flex: 1 1 0%;
            min-width: 0 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            line-height: 1.2;
        }
        #offices-table td.consignee-cell {
            overflow: hidden;
            padding-left: 8px !important;
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
        #offices-table td:nth-child(4) { width: 160px; min-width: 160px; }
        #offices-table th:nth-child(5),
        #offices-table td:nth-child(5) { width: 240px; min-width: 240px; }
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
        .followup-table-area {
            min-height: 0;
        }
        .table-scroll-wrapper {
            overflow: auto !important;
            max-height: calc(100vh - 280px);
            width: 100%;
            position: relative;
            -webkit-overflow-scrolling: touch;
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
        .btn-outline-teal:hover,
        .btn-outline-teal:focus,
        .btn-outline-teal:active,
        .send-reminder-btn:hover,
        .send-reminder-btn:focus,
        .send-reminder-btn:active {
            background-color: #008080 !important;
            border-color: #008080 !important;
            color: #ffffff !important;
        }
        .send-reminder-btn {
            color: #008080 !important;
            border-color: #008080 !important;
            background-color: #ffffff !important;
        }
        .followup-filters-toolbar {
            display: none;
        }
        .followup-filters-fields {
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .followup-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 4px 0 8px;
            }
            .followup-filters-fields {
                display: none !important;
                flex-direction: column;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                margin-bottom: 8px;
                border-bottom: 1px solid #eef2f7;
            }
            body.followup-filters-open .followup-filters-fields {
                display: flex !important;
            }
            #btn-followup-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }
            .followup-filters-fields .mr-2,
            .followup-filters-fields .btn-filter-toggle {
                display: none !important;
            }
            .followup-filters-fields .row.no-gutters {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100%;
            }
            .followup-filters-fields .custom-col,
            .followup-filters-fields .custom-col[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
            }
            .followup-filters-fields .filter-group {
                width: 100%;
                max-width: 100%;
            }
            .followup-filters-fields .clear-filters {
                margin: 4px 0 8px;
            }
            .table-scroll-wrapper,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                width: 100% !important;
                max-width: 100%;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .pagination-sticky-footer {
                justify-content: center !important;
                padding: 8px 12px !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                justify-content: center;
            }
        }

        @media (min-width: 992px) {
            .followup-filters-toolbar {
                display: none !important;
            }
            .followup-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.followup-filters-open .followup-filters-fields {
                display: flex !important;
            }
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
        #compose-reminder-modal .compose-input.compose-input-readonly {
            background: #f8fafc;
            color: #475569;
            cursor: default;
        }
        #compose-reminder-modal .compose-from-hint {
            margin: -4px 0 10px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }
        #compose-reminder-modal .btn-compose-draft {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            padding: 8px 14px;
            cursor: pointer;
        }
        #compose-reminder-modal .btn-compose-draft:hover {
            background: #e5e7eb;
            color: #111827;
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

        .table thead th {
            padding: 10px 5px;
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
                                                <div class="followup-filters-toolbar">
                                                    <button type="button" id="btn-followup-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="followup-filters-toggle-label">Show filters</span>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 followup-filters-fields">
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
                                                <div class="followup-table-area">
                                                    <table id="offices-table" class="table office-table mb-0">
                                                        <colgroup>
                                                            <col style="width: 120px">
                                                            <col style="width: 160px">
                                                            <col style="width: 130px">
                                                            <col style="width: 160px">
                                                            <col style="width: 240px">
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
                                                                $consigneeType = explode(':', (string) $shipment->consignee, 2)[0];
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
                                                                <td class="consignee-cell" title="{{ $consigneeDisplay }}">
                                                                    @if ($consigneeType === 'hub')
                                                                        <span class="consignee-row consignee-hub-agent"><i class="ti-home consignee-hub-icon" title="Hub"></i><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
                                                                    @elseif ($consigneeType === 'agent')
                                                                        <span class="consignee-row consignee-hub-agent"><i class="ti-user consignee-hub-icon" title="Agent"></i><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
                                                                    @else
                                                                        <span class="consignee-row"><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
                                                                    @endif
                                                                </td>
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
                                                                        style="font-size: 10px; height: 24px;"
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
@include('Shipment.partials.reminder-compose-modal')
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
                enableFiltering: false,
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

            var followupFilterIds = [
                'col-Account-manager',
                'col-Shipment-no',
                'col-Customer',
                'col-Vessel',
                'col-Port-of-destination',
                'col-Status',
                'col-Created-by'
            ];

            function isFollowupMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureFollowupMobileFiltersVisible() {
                if (!isFollowupMobile()) {
                    return;
                }
                followupFilterIds.forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.followup-filters-fields .mr-2').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                if (isFollowupMobile()) {
                    ensureFollowupMobileFiltersVisible();
                    return;
                }

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
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }

            toggleFilterVisibility();
            ensureFollowupMobileFiltersVisible();

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "columnDefs": [
                    { "targets": 3, "width": "160px" },
                    { "targets": 4, "width": "240px" },
                    { "targets": [10, 11, 12], "orderable": false }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            function getFollowupTableScrollHeight() {
                var paginationHeight = $('.pagination-sticky-footer').outerHeight() || 48;
                var $wrapper = $('.table-scroll-wrapper');
                var topOffset = $wrapper.length && $wrapper.offset()
                    ? $wrapper.offset().top
                    : 220;
                return Math.max(180, window.innerHeight - topOffset - paginationHeight - 8);
            }

            function adjustFollowupTableLayout() {
                var height = getFollowupTableScrollHeight();
                $('.table-scroll-wrapper').css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });
            }

            $('#btn-followup-filters-toggle').on('click', function () {
                $('body').toggleClass('followup-filters-open');
                var isOpen = $('body').hasClass('followup-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.followup-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                if (isOpen) {
                    ensureFollowupMobileFiltersVisible();
                }
                setTimeout(adjustFollowupTableLayout, 50);
                setTimeout(adjustFollowupTableLayout, 200);
            });

            $(window).on('resize', function() {
                toggleFilterVisibility();
                ensureFollowupMobileFiltersVisible();
                adjustFollowupTableLayout();
            });

            table.on('draw', function() {
                adjustFollowupTableLayout();
            });

            setTimeout(adjustFollowupTableLayout, 100);
            setTimeout(adjustFollowupTableLayout, 400);

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

            @include('Shipment.partials.reminder-compose-script')

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
