@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <!-- Select 2 css -->

    <style>
        .office-table {
            width: 1980px !important;
            min-width: 1980px !important;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }
        .office-table tbody td {
            padding: 6px 8px;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .office-table th, .office-table td {
            white-space: nowrap;
        }
        .office-table .cell-ellipsis {
            display: block;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
        .office-table td.stock-no-cell {
            max-width: none;
            overflow: hidden;
        }
        .office-table .stock-no-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-width: 0;
            max-width: 100%;
        }
        .office-table .stock-no-row a {
            min-width: 0;
            flex: 1 1 0%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .office-table .stock-no-flags {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }
        .office-table td.dataTables_empty,
        .office-table td[colspan] {
            overflow: visible;
            white-space: normal;
            text-overflow: clip;
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
        .custom-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }
        .filter-row {
            margin-bottom: 8px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: visible;
            width: 100%;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #ffffff;
            margin-bottom: 0;
            padding: 0 10px;
            white-space: nowrap;
            font-weight: 700;
            border-right: 1px solid #5a7fa0;
            height: 100%;
            display: flex;
            align-items: center;
            background: #6992b5;
            background-color: #6992b5;
            min-width: fit-content;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 11px;
            padding: 0 10px !important;
            background: transparent !important;
            width: 100%;
            color: #1e293b;
        }
        .filter-group .multiselect-native-select {
            flex: 1;
            min-width: 0;
        }
        .filter-group .multiselect-native-select .btn-group {
            width: 100%;
        }
        .filter-group .multiselect-native-select .multiselect {
            height: 30px;
            padding: 4px 26px 4px 10px;
            overflow: hidden;
            border: 0;
            border-radius: 0;
            background: #fff;
            color: #1e293b;
            font-size: 11px;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .filter-group .multiselect-native-select .multiselect-container {
            width: max(100%, 280px);
            max-height: 420px;
            overflow-y: auto;
            padding: 6px 0;
            z-index: 1050;
        }
        .filter-group .multiselect-native-select .multiselect-container .input-group {
            width: calc(100% - 12px);
            margin: 0 6px 6px;
        }
        .filter-group .multiselect-native-select .multiselect-container label {
            padding-top: 7px;
            padding-bottom: 7px;
            color: #263238;
            font-size: 12px;
            white-space: normal;
        }
        .filter-group .multiselect-native-select .multiselect-container input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #176b87;
        }
        .filter-group .multiselect-native-select .multiselect-container .multiselect-reset a {
            color: #176b87;
            font-weight: 600;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
            min-height: 30px !important;
            max-height: 30px !important;
            overflow: hidden !important;
        }
        .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            max-height: 28px !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
            font-size: 11px !important;
            color: #1e293b !important;
            line-height: 1.25 !important;
        }
        .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered,
        .filter-group .select2-container--default .select2-search--inline .select2-search__field {
            font-size: 11px !important;
            padding-left: 5px !important;
        }
        .filter-group .select2-container--default .select2-search--inline .select2-search__field::placeholder {
            font-size: 11px !important;
            color: #94a3b8 !important;
        }
        .clear-filters {
            font-size: 11px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            height: 32px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-weight: 500;
        }

        .select2-selection__clear,
        .select2-selection__choice__remove {
            display: none !important;
        }
        .label {
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        .label-stock {
            background-color: #d4edda !important;
            color: #155724 !important;
            border: 1px solid #c3e6cb;
        }
        .label-pending {
            background-color: #ffeeba !important;
            color: #856404 !important;
            border: 1px solid #ffeeba;
        }
        .shipment-badge {
            background-color: #ffeeba;
            color: #333;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
        }
        .icon-doc-blue {
            color: #4682b4;
            margin-left: 5px;
        }
        .icon-warning-red {
            color: #ff5252;
            margin-right: 5px;
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
            padding: 6px 10px 6px 6px;
            display: block;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
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
            color: #495057 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            margin-top: 0 !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice span {
            color: #495057 !important;
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
            padding: 0 !important;
        }
        .main-body .page-wrapper {
            padding: 0 !important;
        }
        /* Stocks list: lock page scroll; only table body scrolls; full-bleed width */
        body.stocks-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.stocks-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.stocks-list-page .pcoded-inner-content,
        body.stocks-list-page .main-body,
        body.stocks-list-page .page-wrapper,
        body.stocks-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .stocks-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 64px);
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
        }
        body.stock-bulk-footer-visible .stocks-list-card {
            height: calc(100vh - 120px);
        }
        .stocks-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .stocks-filters-fixed {
            flex-shrink: 0;
            background: #fff;
            position: relative;
            z-index: 40;
            padding-bottom: 6px;
        }
        .stocks-filters-toolbar {
            display: none;
        }
        .stocks-table-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Mobile: unlock page if needed; collapse filters; keep table scrollable */
        @media (max-width: 991.98px) {
            body.stocks-list-page {
                overflow: hidden !important;
                height: 100vh;
            }
            body.stocks-list-page .pcoded-inner-content,
            body.stocks-list-page .main-body,
            body.stocks-list-page .page-wrapper,
            body.stocks-list-page .page-body {
                height: 100%;
                overflow: hidden !important;
            }
            .stocks-list-card {
                height: calc(100vh - 64px) !important;
                margin: 0 !important;
            }
            body.stock-bulk-footer-visible .stocks-list-card {
                height: calc(100vh - 120px) !important;
            }
            .stocks-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 4px 0 8px;
            }
            .stocks-filters-toolbar .stocks-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            /* Always show filters stacked on mobile; toolbar toggles collapse only */
            .stocks-filters-fields {
                display: flex !important;
                flex-direction: column;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                margin-bottom: 4px;
                border-bottom: 1px solid #eef2f7;
            }
            body.stocks-filters-collapsed .stocks-filters-fields {
                display: none !important;
            }
            .stocks-filters-fields .custom-col[style*="margin-left: auto"],
            .stocks-filters-fields .custom-col.d-flex.justify-content-end {
                display: none !important;
            }
            /* Hide column picker (desktop-only); must win over .custom-col { display:block } below */
            .stocks-filters-fields .custom-col[style*="flex: 0 0 50px"],
            .stocks-filters-fields .btn-filter-toggle,
            .stocks-filters-fields #filter-multiselect,
            .stocks-filters-fields .mc-column-picker,
            .stocks-filters-fields .mc-column-picker-native,
            .stocks-filters-fields .custom-col:has(.mc-column-picker),
            .stocks-filters-fields .custom-col:has(#filter-multiselect) {
                display: none !important;
            }
            .stocks-filters-fields .filter-row {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100%;
            }
            .stocks-filters-fields .custom-col,
            .stocks-filters-fields .custom-col[style*="flex"] {
                flex: 0 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 8px !important;
                display: block !important;
                visibility: visible !important;
            }
            .stocks-filters-fields .custom-col:has(.mc-column-picker),
            .stocks-filters-fields .custom-col:has(#filter-multiselect),
            .stocks-filters-fields .custom-col[style*="flex: 0 0 50px"] {
                display: none !important;
                width: 0 !important;
                max-width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }
            #btn-stocks-filters-toggle.is-collapsed {
                background: transparent !important;
                color: #008080 !important;
            }
            .stocks-table-area {
                flex: 1 1 auto;
                min-height: 45vh;
            }
            .stocks-table-area .table-scroll-wrapper,
            .stocks-table-area .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (min-width: 992px) {
            .stocks-filters-toolbar {
                display: none !important;
            }
            .stocks-filters-fields {
                display: flex !important;
                max-height: none !important;
                overflow: visible !important;
            }
            body.stocks-filters-collapsed .stocks-filters-fields {
                display: flex !important;
            }
        }
        .stocks-table-area .dataTables_wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100%;
            padding-bottom: 0 !important;
        }
        .stocks-table-area .table-scroll-wrapper {
            flex: 1;
            min-height: 0;
            overflow: auto !important;
            display: block;
            width: 100%;
            position: relative;
        }

        /* Table visibility fixes */
        .dt-responsive {
            width: 100%;
        }
        .table-scroll-wrapper {
            width: 100%;
            position: relative;
        }
        .office-table {
            width: 1980px !important;
            min-width: 1980px !important;
            max-width: none !important;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }
        .table-scroll-wrapper .office-table {
            width: 1980px !important;
            min-width: 1980px !important;
        }
        .office-table th:nth-child(1),
        .office-table td:nth-child(1) { width: 40px; min-width: 40px; }
        .office-table th:nth-child(2),
        .office-table td:nth-child(2) { width: 90px; min-width: 90px; }
        .office-table th:nth-child(3),
        .office-table td:nth-child(3) { width: 180px; min-width: 180px; }
        .office-table th:nth-child(4),
        .office-table td:nth-child(4) { width: 240px; min-width: 240px; }
        .office-table th:nth-child(5),
        .office-table td:nth-child(5) { width: 180px; min-width: 180px; }
        .office-table th:nth-child(6),
        .office-table td:nth-child(6) { width: 110px; min-width: 110px; }
        .office-table th:nth-child(7),
        .office-table td:nth-child(7) { width: 200px; min-width: 200px; }
        .office-table th:nth-child(8),
        .office-table td:nth-child(8) { width: 200px; min-width: 200px; }
        .office-table th:nth-child(9),
        .office-table td:nth-child(9) { width: 60px; min-width: 60px; }
        .office-table th:nth-child(10),
        .office-table td:nth-child(10) { width: 90px; min-width: 90px; }
        .office-table th:nth-child(11),
        .office-table td:nth-child(11) { width: 100px; min-width: 100px; }
        .office-table th:nth-child(12),
        .office-table td:nth-child(12) { width: 60px; min-width: 60px; }
        .office-table th:nth-child(13),
        .office-table td:nth-child(13) { width: 130px; min-width: 130px; }
        .office-table th:nth-child(14),
        .office-table td:nth-child(14) { width: 150px; min-width: 150px; }
        .office-table th:nth-child(15),
        .office-table td:nth-child(15) { width: 120px; min-width: 120px; }
        .office-table thead {
            position: sticky !important;
            top: 0 !important;
            z-index: 20 !important;
            background-color: #fdfdfd !important;
        }
        .office-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 21 !important;
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
        }
        /* Hide sorting icons for checkbox column */
        .office-table thead th:first-child:after,
        .office-table thead th:first-child:before {
            display: none !important;
        }
        .office-table thead th:first-child {
            padding-right: 10px !important;
        }

        /* Pagination look: partials/list-pagination-footer-styles (in-flow under table) */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            float: none !important;
            width: 100%;
        }
        .dataTables_wrapper {
            padding-bottom: 0 !important;
        }

        .landed-badge {
            background: #dcf0fa;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 5px;
            display: inline-block;
        }

        .stock-bulk-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.06);
            padding: 10px 20px;
            display: none;
        }

        .stock-bulk-footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .stock-bulk-footer-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .stock-bulk-footer-actions .btn-teal {
            font-size: 11px;
            font-weight: 600;
            padding: 6px 14px;
            height: 32px;
            border-radius: 3px;
        }

        .stock-bulk-icon-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #64748b;
            border-radius: 3px;
        }

        .stock-bulk-icon-btn:hover {
            background: #f1f5f9;
            color: #008080;
            border-color: #cbd5e1;
        }

        .stock-bulk-footer-stats {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 11px;
            color: #64748b;
        }

        .stock-bulk-footer-stats strong {
            color: #1f2937;
            font-weight: 600;
        }

        body.stock-bulk-footer-visible {
            padding-bottom: 104px;
        }

        body:not(.stock-bulk-footer-visible) {
            padding-bottom: 48px;
        }

        #bulk-create-shipment:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .stock-copy-toast {
            position: fixed;
            right: 20px;
            bottom: 60px;
            z-index: 1100;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #008080;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .stock-copy-toast.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        body.stock-bulk-footer-visible .stock-copy-toast {
            bottom: 116px;
        }
    </style>
    <x-lists.multiselect-assets />
@endsection

@section('content')
<!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    @include('layouts.partials.pcoded-shell-start')
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 12px; margin-bottom: 10px;">
                                            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        </div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 12px; margin-bottom: 10px;">
                                            <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        </div>
                                    @endif

                                        <!-- Base Style - Compact start -->
                                        <div class="card stocks-list-card">
                                            <div class="card-block">
                                                <x-lists.page-header
                                                    title="Stock list"
                                                    subtitle="Search, filter, and manage warehouse stock"
                                                    icon="ti-package"
                                                    :count="$crrs->total()"
                                                    countLabel="stocks"
                                                >
                                                    <x-slot:actions>
                                                        <a href="{{ route('create-crr') }}" class="btn btn-teal btn-sm d-none d-lg-inline-flex">Create CRR</a>
                                                    </x-slot:actions>
                                                </x-lists.page-header>
                                                <div class="stocks-filters-fixed">
                                                <div class="stocks-filters-toolbar">
                                                    <button type="button" id="btn-stocks-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                        <i class="ti-filter"></i> <span class="stocks-filters-toggle-label">Hide filters</span>
                                                    </button>
                                                    <div class="stocks-filters-toolbar-actions">
                                                        <button type="button" id="btn-export-pdf-mobile" class="btn btn-outline-teal btn-sm"><i class="ti-download"></i> Export</button>
                                                        <a href="{{ route('create-crr') }}" class="btn btn-outline-teal btn-sm">Create CRR</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 stocks-filters-fields">
                                                    <div style="width: 100%;">
                                                        <!-- Row 1 -->
                                                        <div class="row custom-row filter-row">
                                                            <div class="custom-col" style="flex: 0 0 50px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Customer" selected>Customer</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Hub/Agent" selected>Hub/Agent</option>
                                                                    <option value="Status" selected>Status</option>
                                                                    <option value="PO number" selected>PO number</option>
                                                                    <option value="Supplier" selected>Supplier</option>
                                                                    <option value="Stock number" selected>Stock number</option>
                                                                    <option value="Service reference" selected>Service reference</option>
                                                                    <option value="Shipment no" selected>Shipment no</option>
                                                                    <option value="Transit id" selected>Transit id</option>
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Office" selected>Office</option>
                                                                </select>
                                                            </div>
                                                            <div id="col-Hub-Agent" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Hub/Agent</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach($hubAgentOptions as $hubAgentOption)
                                                                            <option value="{{ $hubAgentOption }}">{{ $hubAgentOption }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Status" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Status</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                                                            <option value="{{ $label }}">{{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Stock-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Stock no.</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Service-reference" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service ref.</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div class="custom-col d-flex justify-content-end" style="flex: 0 0 auto; margin-left: auto;">
                                                                <button id="btn-export-pdf" class="btn btn-outline-teal btn-sm" style="height: 32px; padding: 0 15px;"><i class="ti-download"></i> Export</button>
                                                                <a href="{{ route('create-crr') }}"><button class="btn btn-outline-teal btn-sm ml-2" style="height: 32px; padding: 0 15px;">Create CRR</button></a>
                                                            </div>
                                                            
                                                        </div>

                                                        <!-- Row 2 -->
                                                        <div class="row custom-row filter-row">
                                                            <div id="col-PO-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">PO no.</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="full PO no.">
                                                                </div>
                                                            </div>
                                                            <div id="col-Supplier" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Supplier</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no.</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Transit-id" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Transit id</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach($accountManagers as $accountManager)
                                                                            <option value="{{ $accountManager }}">{{ $accountManager }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Office" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Office</span>
                                                                    <select class="form-control filter-input stock-filter-multiselect" multiple="multiple">
                                                                        @foreach($offices as $office)
                                                                            <option value="{{ $office }}">{{ $office }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="custom-col">
                                                                <x-lists.clear-filters id="clear-stocks-filters" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="stocks-table-area">
                                                <table id="offices-table"
                                                        class="office-table">
                                                        <colgroup>
                                                            <col style="width: 40px">
                                                            <col style="width: 90px">
                                                            <col style="width: 180px">
                                                            <col style="width: 240px">
                                                            <col style="width: 180px">
                                                            <col style="width: 110px">
                                                            <col style="width: 200px">
                                                            <col style="width: 200px">
                                                            <col style="width: 60px">
                                                            <col style="width: 90px">
                                                            <col style="width: 100px">
                                                            <col style="width: 60px">
                                                            <col style="width: 130px">
                                                            <col style="width: 150px">
                                                            <col style="width: 120px">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th><input type="checkbox"></th>
                                                                <th>Hub</th>
                                                                <th>Stock no</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Delivery</th>
                                                                <th>PO numbers</th>
                                                                <th>Supplier</th>
                                                                <th>Items</th>
                                                                <th>Weight</th>
                                                                <th>Value</th>
                                                                <th>Cur.</th>
                                                                <th>Transit id</th>
                                                                <th>Shipment</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @include('Stock.partials.rows')

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="stocks-pagination" class="pagination-sticky-footer">
                                                    @include('partials.list-pagination-footer-inner', ['paginator' => $crrs])
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')

    <div id="stock-bulk-footer" class="stock-bulk-footer">
        <div class="stock-bulk-footer-inner">
            <div class="stock-bulk-footer-actions">
                <button type="button" class="btn btn-teal btn-sm" id="bulk-create-shipment">
                    Create shipment (<span class="bulk-action-count">0</span>)
                </button>
                <button type="button" class="btn btn-teal btn-sm" id="bulk-create-customer-request">
                    Create customer request (<span class="bulk-action-count">0</span>)
                </button>
                <button type="button" class="stock-bulk-icon-btn" id="bulk-copy-selected" title="Copy selected rows">
                    <i class="ti-layers"></i>
                </button>
                <button type="button" class="stock-bulk-icon-btn" id="bulk-print-selected" title="Print selected stocks">
                    <i class="ti-printer"></i>
                </button>
            </div>
            <div class="stock-bulk-footer-stats">
                <span>Total selected: <strong id="bulk-stat-selected">0</strong></span>
                <span>Total items: <strong id="bulk-stat-items">0</strong></span>
                <span>Total weight: <strong id="bulk-stat-weight">0.00 kg</strong></span>
                <span>Total volume: <strong id="bulk-stat-cbm">0.00 CBM</strong></span>
            </div>
        </div>
    </div>

    <!-- jquery slimscroll js -->

    <!-- modernizr js -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('stocks-list-page');

            // Checkbox multiselects for stock filters
            $('.stock-filter-multiselect').multiselect({
                enableCaseInsensitiveFiltering: true,
                includeResetOption: true,
                resetText: 'Clear',
                filterPlaceholder: 'Type here',
                maxHeight: 420,
                buttonWidth: '100%',
                nonSelectedText: 'Click here',
                numberDisplayed: 1,
                nSelectedText: 'selected',
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Click here';
                    }

                    var firstSelection = $(options[0]).text();
                    return options.length === 1 ? firstSelection : firstSelection + ', ...';
                },
                buttonTitle: function(options) {
                    var labels = [];
                    options.each(function() {
                        labels.push($(this).text());
                    });

                    return labels.join(', ');
                },
                onChange: function() {
                    if (window.stocksListFilters) {
                        window.stocksListFilters.load(1);
                    }
                },
                onSelectAll: function() {
                    if (window.stocksListFilters) {
                        window.stocksListFilters.load(1);
                    }
                },
                onDeselectAll: function() {
                    if (window.stocksListFilters) {
                        window.stocksListFilters.load(1);
                    }
                }
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: false,
                buttonWidth: '100%',
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
            toggleFilterVisibility();

            function isStocksMobile() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function ensureStocksMobileFiltersVisible() {
                if (!isStocksMobile()) {
                    return;
                }
                [
                    'col-Customer',
                    'col-Vessel',
                    'col-Hub-Agent',
                    'col-Status',
                    'col-PO-number',
                    'col-Supplier',
                    'col-Stock-number',
                    'col-Service-reference',
                    'col-Shipment-no',
                    'col-Transit-id',
                    'col-Account-manager',
                    'col-Office'
                ].forEach(function (id) {
                    $('#' + id).show().css('display', '');
                });
                $('.stocks-filters-fields .custom-col[style*="flex: 0 0 50px"]').hide();
                $('#filter-multiselect').closest('.btn-group').find('.multiselect-container').removeClass('show').hide();
            }

            function toggleFilterVisibility() {
                if (isStocksMobile()) {
                    ensureStocksMobileFiltersVisible();
                    if (typeof table !== 'undefined' && table.columns) {
                        setTimeout(adjustStockTableLayout, 50);
                    }
                    return;
                }

                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Hub/Agent', id: 'col-Hub-Agent'},
                    {val: 'Status', id: 'col-Status'},
                    {val: 'PO number', id: 'col-PO-number'},
                    {val: 'Supplier', id: 'col-Supplier'},
                    {val: 'Stock number', id: 'col-Stock-number'},
                    {val: 'Service reference', id: 'col-Service-reference'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Transit id', id: 'col-Transit-id'},
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Office', id: 'col-Office'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.indexOf(filter.val) !== -1) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });

                if (typeof table !== 'undefined' && table.columns) {
                    setTimeout(adjustStockTableLayout, 50);
                }
            }

            ensureStocksMobileFiltersVisible();
            $(window).on('resize.stocksFilters', ensureStocksMobileFiltersVisible);

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt>',
                "lengthChange": false,
                "paging": false,
                "info": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [0] },
                    { "searchable": false, "targets": [0] },
                    { "targets": 0, "width": "40px" },
                    { "targets": 3, "width": "240px" },
                    { "targets": 4, "width": "180px" },
                    { "targets": 6, "width": "200px" },
                    { "targets": 7, "width": "200px" }
                ]
            });

            function getStockTableScrollHeight() {
                var isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                var $tableArea = $('.stocks-table-area');
                var areaHeight = $tableArea.length ? $tableArea.innerHeight() : 0;
                // Pagination is in-flow sibling under table-area — use full area height.
                var available = areaHeight - 2;

                if (isMobile) {
                    var paginationHeight = $('#stocks-pagination').outerHeight() || 52;
                    var bulkHeight = $('body').hasClass('stock-bulk-footer-visible')
                        ? ($('#stock-bulk-footer').outerHeight() || 56)
                        : 0;
                    var topOffset = $tableArea.length && $tableArea.offset()
                        ? $tableArea.offset().top
                        : 160;
                    available = window.innerHeight - topOffset - paginationHeight - bulkHeight;
                    return Math.max(260, available);
                }

                if (available < 180) {
                    var topOffsetFallback = $tableArea.length ? $tableArea.offset().top : 220;
                    var paginationHeightFallback = $('#stocks-pagination').outerHeight() || 52;
                    available = window.innerHeight - topOffsetFallback - paginationHeightFallback;
                }

                return Math.max(180, available);
            }

            function adjustStockTableLayout() {
                var height = getStockTableScrollHeight();
                $('.stocks-table-area .table-scroll-wrapper').css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });
            }

            $('#btn-stocks-filters-toggle').on('click', function () {
                $('body').toggleClass('stocks-filters-collapsed');
                var collapsed = $('body').hasClass('stocks-filters-collapsed');
                $(this).toggleClass('is-collapsed', collapsed);
                $(this).find('.stocks-filters-toggle-label').text(collapsed ? 'Show filters' : 'Hide filters');
                if (!collapsed) {
                    ensureStocksMobileFiltersVisible();
                }
                setTimeout(adjustStockTableLayout, 50);
                setTimeout(adjustStockTableLayout, 200);
            });

            $('#btn-export-pdf-mobile').on('click', function () {
                $('#btn-export-pdf').trigger('click');
            });

            $(window).on('resize', function() {
                adjustStockTableLayout();
            });

            setTimeout(adjustStockTableLayout, 100);
            setTimeout(adjustStockTableLayout, 400);

            window.stocksListFilters = bindAjaxListFilters({
                tableSelector: '#offices-table',
                paginationSelector: '#stocks-pagination',
                indexUrl: @json(route('stocks')),
                existingTable: table,
                clearSelector: '#clear-stocks-filters',
                getParams: function (page) {
                    return {
                        hub_agent: $('#col-Hub-Agent select').val() || [],
                        customer: $('#col-Customer select').val() || [],
                        vessel: $('#col-Vessel select').val() || [],
                        status: $('#col-Status select').val() || [],
                        account_manager: $('#col-Account-manager select').val() || [],
                        office: $('#col-Office select').val() || [],
                        stock_number: $.trim($('#col-Stock-number input').val() || ''),
                        po_number: $.trim($('#col-PO-number input').val() || ''),
                        supplier: $.trim($('#col-Supplier input').val() || ''),
                        supplier_reference: $.trim($('#col-Service-reference input').val() || ''),
                        shipment: $.trim($('#col-Shipment-no input').val() || ''),
                        transit_id: $.trim($('#col-Transit-id input').val() || ''),
                        page: page || 1
                    };
                },
                textSelectors: '#col-Stock-number input, #col-PO-number input, #col-Supplier input, #col-Service-reference input, #col-Shipment-no input, #col-Transit-id input',
                resetFields: function () {
                    clearSearchableFilterMultiselect('.stock-filter-multiselect', false);
                    $('#col-Stock-number input, #col-PO-number input, #col-Supplier input, #col-Service-reference input, #col-Shipment-no input, #col-Transit-id input').val('');
                },
                afterDraw: function () {
                    $('#offices-table thead input[type="checkbox"]').prop('checked', false);
                    updateBulkFooter();
                    adjustStockTableLayout();
                }
            });

            $(document).on('change', '.dataTables_scrollHead thead input[type="checkbox"], #offices-table thead input[type="checkbox"]', function() {
                var isChecked = $(this).prop('checked');
                $('.dataTables_scrollHead thead input[type="checkbox"], #offices-table thead input[type="checkbox"]').prop('checked', isChecked);
                $('#offices-table tbody .row-checkbox').prop('checked', isChecked);
                updateBulkFooter();
            });

            $(document).on('change', '.row-checkbox', function() {
                updateBulkFooter();
            });

            table.on('draw', function() {
                updateBulkFooter();
                adjustStockTableLayout();
            });

            function getSelectedRows() {
                var rows = [];
                $('.row-checkbox:checked').each(function() {
                    rows.push($(this).closest('tr'));
                });
                return rows;
            }

            function getSelectedIds() {
                return $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function escapeHtml(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function formatCopyNumber(value, decimals) {
                var num = parseFloat(value);
                if (isNaN(num)) {
                    return '';
                }

                var fixed = num.toFixed(decimals);
                var parts = fixed.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                return parts.join('.');
            }

            function formatCopyVolumeWeight(cbm) {
                var volume = parseFloat(cbm);
                if (isNaN(volume) || volume <= 0) {
                    return '0.00';
                }

                return formatCopyNumber(Math.round(volume * 167 * 100) / 100, 2);
            }

            function getCopyRowData($row) {
                var weight = parseFloat(rowData($row, 'weight')) || 0;
                var volume = parseFloat(rowData($row, 'cbm')) || 0;
                var valueRaw = rowData($row, 'value');
                var valueNum = valueRaw === '' ? NaN : parseFloat(valueRaw);

                return {
                    hub: rowData($row, 'hub-agent') || rowData($row, 'hub-agent-raw') || '',
                    vessel: rowData($row, 'vessel') || '',
                    supplier: rowData($row, 'supplier') || '',
                    poNumbers: rowData($row, 'po-numbers') || '',
                    items: rowData($row, 'items') || '0',
                    weight: weight,
                    weightText: formatCopyNumber(weight, 2),
                    valueText: isNaN(valueNum) ? '' : formatCopyNumber(valueNum, 2),
                    currency: rowData($row, 'currency') || '',
                    volume: volume,
                    volumeText: formatCopyNumber(volume, 2),
                    vw: parseFloat(formatCopyVolumeWeight(volume)) || 0,
                    vwText: formatCopyVolumeWeight(volume),
                    dgr: rowData($row, 'dgr') || '',
                    oversized: rowData($row, 'oversized') || ''
                };
            }

            function buildSelectedRowsCopyPayload(rows) {
                var headers = ['Hub', 'Vessel', 'Supplier', 'PO numbers', 'Items', 'Weight', 'Value', 'Cur.', 'Volume', 'VW', 'DG', 'Oversized'];
                var plainLines = [headers.join('\t')];
                var totalWeight = 0;
                var totalVolume = 0;
                var totalVw = 0;
                var bodyHtml = '';

                rows.forEach(function($row) {
                    var row = getCopyRowData($row);
                    totalWeight += row.weight;
                    totalVolume += row.volume;
                    totalVw += row.vw;

                    var cells = [
                        row.hub,
                        row.vessel,
                        row.supplier,
                        row.poNumbers,
                        row.items,
                        row.weightText,
                        row.valueText,
                        row.currency,
                        row.volumeText,
                        row.vwText,
                        row.dgr,
                        row.oversized
                    ];

                    plainLines.push(cells.join('\t'));
                    bodyHtml += '<tr>' + cells.map(function(cell) {
                        return '<td style="border:1px solid #9ca3af;padding:4px 6px;vertical-align:top;">' + escapeHtml(cell) + '</td>';
                    }).join('') + '</tr>';
                });

                var weightTotalText = formatCopyNumber(totalWeight, 2);
                var volumeTotalText = formatCopyNumber(totalVolume, 2);
                var vwTotalText = formatCopyNumber(totalVw, 2);

                plainLines.push('');
                plainLines.push('Weight: ' + weightTotalText);
                plainLines.push('Volume: ' + volumeTotalText);
                plainLines.push('VW: ' + vwTotalText);

                var html = ''
                    + '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#1e3a5f;">'
                    + '<thead><tr>'
                    + headers.map(function(header) {
                        return '<th style="border:1px solid #9ca3af;padding:4px 6px;text-align:left;font-weight:700;color:#1e3a8a;background:#ffffff;">' + escapeHtml(header) + '</th>';
                    }).join('')
                    + '</tr></thead>'
                    + '<tbody>' + bodyHtml + '</tbody>'
                    + '</table>'
                    + '<div style="margin-top:8px;font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#1e3a5f;font-weight:700;line-height:1.5;">'
                    + '<div>Weight: ' + escapeHtml(weightTotalText) + '</div>'
                    + '<div>Volume: ' + escapeHtml(volumeTotalText) + '</div>'
                    + '<div>VW: ' + escapeHtml(vwTotalText) + '</div>'
                    + '</div>';

                return {
                    plain: plainLines.join('\n'),
                    html: html
                };
            }

            function copyTextToClipboard(text, html) {
                function fallbackCopy(plainText) {
                    var textarea = document.createElement('textarea');
                    textarea.value = plainText;
                    textarea.setAttribute('readonly', '');
                    textarea.style.position = 'fixed';
                    textarea.style.top = '0';
                    textarea.style.left = '0';
                    textarea.style.width = '2em';
                    textarea.style.height = '2em';
                    textarea.style.padding = '0';
                    textarea.style.border = 'none';
                    textarea.style.outline = 'none';
                    textarea.style.boxShadow = 'none';
                    textarea.style.background = 'transparent';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();

                    var success = false;
                    try {
                        success = document.execCommand('copy');
                    } catch (err) {
                        success = false;
                    }

                    document.body.removeChild(textarea);
                    return success;
                }

                function fallbackHtmlCopy(plainText, htmlText) {
                    var container = document.createElement('div');
                    container.contentEditable = 'true';
                    container.style.position = 'fixed';
                    container.style.left = '-9999px';
                    container.style.top = '0';
                    container.innerHTML = htmlText || plainText;
                    document.body.appendChild(container);

                    var range = document.createRange();
                    range.selectNodeContents(container);
                    var selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    var success = false;
                    try {
                        success = document.execCommand('copy');
                    } catch (err) {
                        success = false;
                    }

                    selection.removeAllRanges();
                    document.body.removeChild(container);
                    return success || fallbackCopy(plainText);
                }

                if (html && navigator.clipboard && window.ClipboardItem && window.isSecureContext) {
                    var item = new ClipboardItem({
                        'text/plain': new Blob([text], { type: 'text/plain' }),
                        'text/html': new Blob([html], { type: 'text/html' })
                    });

                    return navigator.clipboard.write([item]).catch(function() {
                        return fallbackHtmlCopy(text, html) ? Promise.resolve() : Promise.reject();
                    });
                }

                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(text).catch(function() {
                        return fallbackCopy(text) ? Promise.resolve() : Promise.reject();
                    });
                }

                return (html ? fallbackHtmlCopy(text, html) : fallbackCopy(text))
                    ? Promise.resolve()
                    : Promise.reject();
            }

            var copyToastTimer = null;

            function showCopyNotification(rowCount) {
                var label = rowCount === 1 ? 'row' : 'rows';
                var $toast = $('#stock-copy-toast');

                if (!$toast.length) {
                    $toast = $('<div id="stock-copy-toast" class="stock-copy-toast" role="status" aria-live="polite"></div>');
                    $('body').append($toast);
                }

                $toast.html('<i class="ti-check"></i> Copied ' + rowCount + ' ' + label + ' to clipboard');

                if (copyToastTimer) {
                    clearTimeout(copyToastTimer);
                }

                $toast.addClass('is-visible');

                copyToastTimer = setTimeout(function() {
                    $toast.removeClass('is-visible');
                }, 2500);
            }

            function normalizeHubKey(value) {
                return String(value || '')
                    .trim()
                    .toLowerCase()
                    .replace(/\s+/g, ' ');
            }

            function getHubKeyFromRow($row) {
                var hubCode = normalizeHubKey($row.attr('data-hub-agent'));
                var hubAgent = normalizeHubKey($row.attr('data-hub-agent-raw'));

                return hubCode || hubAgent || '';
            }

            function selectedStocksHaveMixedHubs($checked) {
                var hubKeys = [];

                $checked.each(function() {
                    var hubKey = getHubKeyFromRow($(this).closest('tr')) || '__empty__';
                    if (hubKeys.indexOf(hubKey) === -1) {
                        hubKeys.push(hubKey);
                    }
                });

                return hubKeys.length > 1;
            }

            function selectedStocksHaveInProgress($checked) {
                var found = false;

                $checked.each(function() {
                    var status = String($(this).closest('tr').attr('data-status') || '').trim().toLowerCase();
                    if (status === 'in progress') {
                        found = true;
                        return false;
                    }
                });

                return found;
            }

            function updateBulkFooter() {
                var $checked = $('.row-checkbox:checked');
                var count = $checked.length;

                if (count === 0) {
                    $('#stock-bulk-footer').hide();
                    $('body').removeClass('stock-bulk-footer-visible');
                    $('.dataTables_scrollHead thead input[type="checkbox"], #offices-table thead input[type="checkbox"]').prop('checked', false);
                    setTimeout(adjustStockTableLayout, 50);
                    return;
                }

                var totalItems = 0;
                var totalWeight = 0;
                var totalCbm = 0;

                $checked.each(function() {
                    var $row = $(this).closest('tr');
                    totalItems += parseInt($row.attr('data-items') || 0, 10) || 0;
                    totalWeight += parseFloat($row.attr('data-weight') || 0) || 0;
                    totalCbm += parseFloat($row.attr('data-cbm') || 0) || 0;
                });

                $('.bulk-action-count').text(count);
                $('#bulk-stat-selected').text(count);
                $('#bulk-stat-items').text(totalItems);
                $('#bulk-stat-weight').text(totalWeight.toFixed(2) + ' kg');
                $('#bulk-stat-cbm').text(totalCbm.toFixed(2) + ' CBM');

                var hasMixedHubs = selectedStocksHaveMixedHubs($checked);
                var hasInProgress = selectedStocksHaveInProgress($checked);
                var $createShipmentBtn = $('#bulk-create-shipment');
                var createShipmentTitle = '';

                if (hasInProgress) {
                    createShipmentTitle = 'In Progress stock cannot be used to create a shipment.';
                } else if (hasMixedHubs) {
                    createShipmentTitle = 'All selected stock items must belong to the same hub.';
                }

                $createShipmentBtn.prop('disabled', hasMixedHubs || hasInProgress);
                $createShipmentBtn.attr('title', createShipmentTitle);

                $('#stock-bulk-footer').show();
                $('body').addClass('stock-bulk-footer-visible');

                var totalVisible = $('#offices-table tbody .row-checkbox').length;
                var allChecked = totalVisible > 0 && $checked.length === totalVisible;
                $('.dataTables_scrollHead thead input[type="checkbox"], #offices-table thead input[type="checkbox"]').prop('checked', allChecked);
                setTimeout(adjustStockTableLayout, 50);
            }

            $('#bulk-create-shipment').on('click', function() {
                if ($(this).prop('disabled')) {
                    return;
                }

                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    return;
                }

                window.location.href = '{{ route('create-shipment') }}?crr_ids=' + selectedIds.join(',');
            });

            $('#bulk-create-customer-request').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    return;
                }

                alert('Create customer request for ' + selectedIds.length + ' selected stock item(s).');
            });

            $('#bulk-copy-selected').on('click', function() {
                var selectedRows = getSelectedRows();

                if (selectedRows.length === 0) {
                    alert('Please select at least one item to copy.');
                    return;
                }

                var payload = buildSelectedRowsCopyPayload(selectedRows);

                copyTextToClipboard(payload.plain, payload.html).then(function() {
                    showCopyNotification(selectedRows.length);
                }).catch(function() {
                    alert('Could not copy to clipboard. Please copy manually:\n\n' + payload.plain);
                });
            });

            $('#bulk-print-selected').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    alert('Please select at least one item to print.');
                    return;
                }

                window.open('{{ route("stocks.print") }}?ids=' + selectedIds.join(','), '_blank');
            });

            $('#btn-export-pdf').on('click', function() {
                var selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    alert('Please select at least one item to export.');
                    return;
                }

                window.open('{{ route("stocks.print") }}?ids=' + selectedIds.join(','), '_blank');
            });
        });
    </script>
@endpush
