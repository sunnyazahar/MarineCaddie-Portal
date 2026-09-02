@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/animate.css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/notification/notification.css') }}">

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
            color: #64748b;
            margin-bottom: 0;
            padding: 0 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            align-items: center;
            background: #f8fafc;
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
            background-color: #f7f776;
            color: #333;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
        }
        .stocks-table-area a.shipment-badge {
            text-decoration: none;
            display: inline-block;
            max-width: 100%;
        }
        .stocks-table-area a.shipment-badge:hover {
            color: #008080;
            text-decoration: underline;
        }
        .icon-doc-blue {
            color: #4682b4;
            margin-left: 5px;
        }
        .icon-warning-red {
            color: #ff5252;
            margin-right: 5px;
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
            height: var(--mc-app-vh, 100vh);
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
            height: calc(var(--mc-app-vh, 100vh) - var(--mc-header-h, 64px));
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
        }
        body.stock-bulk-footer-visible .stocks-list-card {
            height: calc(var(--mc-app-vh, 100vh) - var(--mc-header-h, 64px) - 56px);
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
                height: var(--mc-app-vh, 100svh) !important;
                max-height: var(--mc-app-vh, 100svh) !important;
            }
            body.stocks-list-page .pcoded-inner-content,
            body.stocks-list-page .main-body,
            body.stocks-list-page .page-wrapper,
            body.stocks-list-page .page-body {
                height: 100%;
                overflow: hidden !important;
            }
            .stocks-list-card {
                height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
                max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
                margin: 0 !important;
            }
            body.stock-bulk-footer-visible .stocks-list-card {
                height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
                max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
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
            /* Hidden by default on mobile — Show filters toggles body.stocks-filters-open */
            .stocks-filters-fields {
                flex-direction: column;
                max-height: 38vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
                margin-bottom: 4px;
                border-bottom: 1px solid #eef2f7;
            }
            body.stocks-filters-open .stocks-filters-fields {
                display: flex !important;
            }
            .stocks-filters-fields .custom-col[style*="margin-left: auto"],
            .stocks-filters-fields .custom-col.d-flex.justify-content-end {
                display: none !important;
            }
            /* Hide column picker on mobile — toolbar toggle controls filter visibility */
            .stocks-filters-fields .list-dense-filter-controls {
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
            #btn-stocks-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }
            .stocks-table-area {
                flex: 1 1 auto;
                min-height: 45vh;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            .stocks-table-area .table-scroll-wrapper,
            .stocks-table-area .dataTables_scrollBody {
                flex: 1 1 auto;
                min-height: 0 !important;
                overflow-x: auto !important;
                overflow-y: auto !important;
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

        /* Freeze checkbox (left) + Status (right) during horizontal scroll */
        .office-table thead th.stock-col-check,
        .office-table tbody td.stock-col-check {
            position: sticky !important;
            left: 0 !important;
            background-color: #fff !important;
            box-shadow: 2px 0 4px -2px rgba(15, 23, 42, 0.12);
        }
        .office-table thead th.stock-col-check {
            top: 0 !important;
            z-index: 32 !important;
            background-color: #fdfdfd !important;
        }
        .office-table tbody td.stock-col-check {
            z-index: 12 !important;
        }
        .office-table thead th.stock-col-status,
        .office-table tbody td.stock-col-status {
            position: sticky !important;
            right: 0 !important;
            background-color: #fff !important;
            box-shadow: -2px 0 4px -2px rgba(15, 23, 42, 0.12);
        }
        .office-table thead th.stock-col-status {
            top: 0 !important;
            z-index: 32 !important;
            background-color: #fdfdfd !important;
        }
        .office-table tbody td.stock-col-status {
            z-index: 12 !important;
        }

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
            font-size: 13px;
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

        .stock-export-dropdown {
            position: relative;
            z-index: 5;
            display: inline-flex;
        }

        /* Open menu must sit above "Clear filters" (row 2) */
        .stock-export-dropdown.show {
            z-index: 4000 !important;
        }

        .stock-export-toggle {
            display: inline-flex !important;
            align-items: center;
            gap: 6px;
            height: 32px;
            padding: 0 14px !important;
            font-weight: 600;
            border-radius: 8px !important;
        }

        .stock-export-toggle--compact {
            height: 32px;
        }

        .stock-export-dropdown.show > .stock-export-toggle,
        .stock-export-dropdown .stock-export-toggle[aria-expanded="true"] {
            background: #e8f6fc;
            border-color: #00aeef;
            color: #0e1d4a;
        }

        /* Override theme `.card .card-block .dropdown-menu { top: 38px }` */
        .stocks-list-card .card-block .stock-export-menu.dropdown-menu,
        .stock-export-menu.dropdown-menu {
            min-width: 148px;
            top: 100% !important;
            bottom: auto !important;
            left: auto !important;
            right: 0 !important;
            margin-top: 8px !important;
            padding: 6px !important;
            border: 1px solid #d6e3ee !important;
            border-radius: 10px !important;
            background: #fff !important;
            box-shadow:
                0 12px 28px rgba(14, 29, 74, 0.14),
                0 2px 6px rgba(14, 29, 74, 0.06) !important;
            z-index: 4001 !important;
            transform: none !important;
        }

        .stock-export-menu .stock-export-option {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px;
            padding: 10px 12px !important;
            margin: 0 !important;
            border-radius: 8px;
            color: #0e1d4a !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            text-decoration: none !important;
            white-space: nowrap;
            background: transparent !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .stock-export-menu .stock-export-option + .stock-export-option {
            margin-top: 2px !important;
        }

        .stock-export-menu .stock-export-option > i {
            order: 0;
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            margin: 0 !important;
            float: none !important;
            border-radius: 7px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            line-height: 1;
        }

        .stock-export-menu .stock-export-option > span {
            order: 1;
            flex: 1 1 auto;
        }

        .stock-export-menu .stock-export-option[data-format="pdf"] > i {
            background: #fef2f2;
            color: #dc2626;
        }

        .stock-export-menu .stock-export-option[data-format="excel"] > i {
            background: #ecfdf5;
            color: #059669;
        }

        .stock-export-menu .stock-export-option:hover,
        .stock-export-menu .stock-export-option:focus {
            background: #f0fafb !important;
            color: #0088c7 !important;
        }

        /* Keep export menu from being clipped / covered by filter shells */
        .stocks-filters-fixed,
        .stocks-filters-toolbar,
        .stocks-filters-toolbar-actions,
        .stocks-filters-fields,
        .list-dense-filter-shell,
        .list-dense-filter-fields,
        .list-dense-filter-row,
        .custom-row.filter-row {
            overflow: visible !important;
        }

        .stocks-filters-fields .list-dense-filter-row {
            position: relative;
            z-index: 1;
        }

        .stocks-filters-fields .list-dense-filter-row:first-child {
            z-index: 3;
        }

        .stocks-filters-fields .btn-clear-filters,
        .stocks-filters-fields .clear-filters {
            position: relative;
            z-index: 1;
        }

        .stock-download-growl.alert,
        .stock-download-growl.alert-inverse,
        .stock-download-growl.alert-success,
        .alert.stock-download-growl {
            display: flex !important;
            align-items: center !important;
            gap: 8px;
            min-width: 280px;
            max-width: 420px;
            padding: 12px 14px !important;
            margin-bottom: 0 !important;
            background-color: #008080 !important;
            border-color: #007070 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(0, 128, 128, 0.35) !important;
        }

        .stock-download-growl .stock-dl-pct {
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            margin-left: 4px;
            color: #ffffff !important;
        }

        .stock-download-growl [data-growl="icon"] {
            flex: 0 0 auto;
            float: none !important;
            margin: 0 !important;
            color: #ffffff !important;
            font-size: 16px;
            line-height: 1;
        }

        .stock-download-growl [data-growl="title"],
        .stock-download-growl [data-growl="message"] {
            color: #ffffff !important;
            font-weight: 700;
            float: none !important;
            display: inline !important;
            margin: 0 !important;
            line-height: 1.3;
        }

        .stock-download-growl [data-growl="title"] {
            margin-right: 4px !important;
        }

        .stock-download-growl [data-growl="message"] {
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stock-download-growl [data-growl="url"] {
            display: none !important;
        }

        .stock-download-growl .close,
        .stock-download-growl button.close {
            position: static !important;
            top: auto !important;
            right: auto !important;
            float: none !important;
            order: 10;
            flex: 0 0 auto;
            margin: 0 0 0 auto !important;
            padding: 0 0 0 10px !important;
            align-self: center;
            color: #ffffff !important;
            opacity: 0.95;
            text-shadow: none;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }

        .stock-download-growl .close span {
            color: #ffffff !important;
        }

        .stock-download-growl .close:hover,
        .stock-download-growl .close:focus {
            opacity: 1;
            color: #ffffff !important;
        }

        .stock-export-menu.dropdown-menu:not(.show) {
            display: none !important;
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
                                                        <i class="ti-filter"></i> <span class="stocks-filters-toggle-label">Show filters</span>
                                                    </button>
                                                    <div class="stocks-filters-toolbar-actions">
                                                        @include('Stock.partials.export-dropdown')
                                                        <a href="{{ route('create-crr') }}" class="btn btn-outline-teal btn-sm">Create CRR</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start pt-2 stocks-filters-fields list-dense-filter-bar">
                                                    <div class="list-dense-filter-shell" style="width: 100%;">
                                                        <div class="list-dense-filter-controls stocks-filter-controls">
                                                            <select id="filter-multiselect" multiple="multiple" data-storage-key="stocks-list-filters">
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
                                                        <div class="list-dense-filter-fields">
                                                        <!-- Row 1 -->
                                                        <div class="row custom-row filter-row list-dense-filter-row">
                                                            <div id="col-Hub-Agent" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Hub/Agent</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach($hubAgentOptions as $hubAgentOption)
                                                                            <option value="{{ $hubAgentOption }}">{{ $hubAgentOption }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Status" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Status</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
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
                                                            <div class="custom-col d-flex justify-content-end align-items-center" style="flex: 0 0 auto; margin-left: auto; gap: 8px;">
                                                                @include('Stock.partials.export-dropdown', ['compact' => true])
                                                                <a href="{{ route('create-crr') }}" class="btn btn-outline-teal btn-sm" style="height: 32px; padding: 0 15px; display: inline-flex; align-items: center;">Create CRR</a>
                                                            </div>
                                                            
                                                        </div>

                                                        <!-- Row 2 -->
                                                        <div class="row custom-row filter-row list-dense-filter-row">
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
                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no.</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Transit-id" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Transit id</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="starts with">
                                                                </div>
                                                            </div>
                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
                                                                        @foreach($accountManagers as $accountManager)
                                                                            <option value="{{ $accountManager }}">{{ $accountManager }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Office" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Office</span>
                                                                    <select class="form-control filter-input searchable-filter-multiselect" multiple="multiple">
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
                                                </div>

                                                <div class="stocks-table-area">
                                                <table id="offices-table"
                                                        class="office-table">
                                                        <colgroup>
                                                            <col style="width: 40px">
                                                            <col style="width: 50px">
                                                            <col style="width: 100px">
                                                            <col style="width: 140px">
                                                            <col style="width: 140px">
                                                            <col style="width: 110px">
                                                            <col style="width: 200px">
                                                            <col style="width: 200px">
                                                            <col style="width: 60px">
                                                            <col style="width: 70px">
                                                            <col style="width: 70px">
                                                            <col style="width: 60px">
                                                            <col style="width: 130px">
                                                            <col style="width: 100px">
                                                            <col style="width: 100px">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th class="stock-col-check"><input type="checkbox"></th>
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
                                                                <th class="stock-col-status">Status</th>
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
    <script type="text/javascript" src="{{ asset('files/assets/js/bootstrap-growl.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('body').addClass('stocks-list-page');

            function loadStocksOnFilterChange() {
                if (window.stocksListFilters) {
                    window.stocksListFilters.load(1);
                }
            }

            initializeSearchableFilterMultiselect('.searchable-filter-multiselect', {
                onChange: loadStocksOnFilterChange,
                onSelectAll: loadStocksOnFilterChange,
                onDeselectAll: loadStocksOnFilterChange
            });

            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                includeResetOption: true,
                resetText: 'Clear all',
                storageKey: 'stocks-list-filters',
                onChange: function () {
                    toggleFilterVisibility();
                },
                onSelectAll: function () {
                    toggleFilterVisibility();
                },
                onDeselectAll: function () {
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
                $('.stocks-filter-controls').hide();
                var $panel = $('#filter-multiselect').data('mcColumnPickerPanel');
                if ($panel && $panel.length) {
                    $panel.removeClass('is-open');
                }
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
                $('body').toggleClass('stocks-filters-open');
                var isOpen = $('body').hasClass('stocks-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.stocks-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                if (isOpen) {
                    ensureStocksMobileFiltersVisible();
                }
                setTimeout(adjustStockTableLayout, 50);
                setTimeout(adjustStockTableLayout, 200);
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
                    clearSearchableFilterMultiselect('.searchable-filter-multiselect', false);
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

                return formatCopyNumber(Math.round((volume * 1000000 / 6000) * 100) / 100, 2);
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

            var stockDownloadXhr = null;
            var stockDownloadSimTimer = null;
            var stockDownloadStartTimer = null;
            var $stockDownloadGrowl = null;
            var stockDownloadHideTimer = null;
            var stockDownloadPercent = 0;

            function stopStockDownloadSimulation() {
                if (stockDownloadSimTimer) {
                    clearInterval(stockDownloadSimTimer);
                    stockDownloadSimTimer = null;
                }
            }

            function clearStockDownloadStartTimer() {
                if (stockDownloadStartTimer) {
                    clearTimeout(stockDownloadStartTimer);
                    stockDownloadStartTimer = null;
                }
            }

            function clearStockDownloadHideTimer() {
                if (stockDownloadHideTimer) {
                    clearTimeout(stockDownloadHideTimer);
                    stockDownloadHideTimer = null;
                }
            }

            function abortStockDownload() {
                clearStockDownloadStartTimer();
                stopStockDownloadSimulation();
                if (stockDownloadXhr) {
                    stockDownloadXhr.onabort = null;
                    stockDownloadXhr.abort();
                    stockDownloadXhr = null;
                }
            }

            function dismissStockDownloadGrowl() {
                clearStockDownloadHideTimer();
                if ($stockDownloadGrowl && $stockDownloadGrowl.length) {
                    $stockDownloadGrowl.off('.stockDownload');
                    $stockDownloadGrowl.remove();
                }
                $stockDownloadGrowl = null;
                $('.alert.stock-download-growl').remove();
            }

            function setStockDownloadPercent(percent) {
                var safe = Math.max(stockDownloadPercent, Math.min(100, Math.round(percent)));
                stockDownloadPercent = safe;
                if ($stockDownloadGrowl && $stockDownloadGrowl.length) {
                    $stockDownloadGrowl.find('.stock-dl-pct').text(safe + '%');
                }
            }

            function showStockDownloadNotify(label) {
                dismissStockDownloadGrowl();
                stockDownloadPercent = 0;

                if (typeof $.growl !== 'function') {
                    return;
                }

                $.growl({
                    icon: 'ti-download',
                    title: ' Export ',
                    message: label + ' <strong class="stock-dl-pct">0%</strong>',
                    url: ''
                }, {
                    element: 'body',
                    type: 'inverse',
                    allow_dismiss: true,
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    offset: {
                        x: 30,
                        y: 30
                    },
                    spacing: 10,
                    z_index: 999999,
                    delay: 0,
                    timer: 1000,
                    mouse_over: false,
                    animate: {
                        enter: 'animated rotateIn',
                        exit: 'animated rotateOut'
                    },
                    icon_type: 'class',
                    template: '<div data-growl="container" class="alert stock-download-growl" role="alert">' +
                        '<span data-growl="icon"></span>' +
                        '<span data-growl="title"></span>' +
                        '<span data-growl="message"></span>' +
                        '<a href="#" data-growl="url"></a>' +
                        '<button type="button" class="close" data-growl="dismiss" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>'
                });

                $stockDownloadGrowl = $('.alert.stock-download-growl').last();
                $stockDownloadGrowl.on('click.stockDownload', '[data-growl="dismiss"]', function () {
                    abortStockDownload();
                    dismissStockDownloadGrowl();
                });
            }

            function completeStockDownloadNotify(doneLabel) {
                stockDownloadPercent = 100;
                if ($stockDownloadGrowl && $stockDownloadGrowl.length) {
                    $stockDownloadGrowl.find('.stock-dl-pct').text('100%');
                    $stockDownloadGrowl.find('[data-growl="icon"]')
                        .removeClass('ti-download')
                        .addClass('ti-check');
                    $stockDownloadGrowl.find('[data-growl="message"]').html(
                        doneLabel + ' <strong class="stock-dl-pct">100%</strong>'
                    );
                }

                clearStockDownloadHideTimer();
                stockDownloadHideTimer = setTimeout(function () {
                    if (!$stockDownloadGrowl || !$stockDownloadGrowl.length) {
                        return;
                    }
                    var $el = $stockDownloadGrowl;
                    $el.removeClass('animated rotateIn').addClass('animated rotateOut');
                    setTimeout(function () {
                        if ($stockDownloadGrowl && $stockDownloadGrowl.is($el)) {
                            dismissStockDownloadGrowl();
                        } else {
                            $el.remove();
                        }
                    }, 700);
                }, 800);
            }

            function parseDownloadFilename(disposition, fallback) {
                if (!disposition) {
                    return fallback;
                }
                var utfMatch = /filename\*\s*=\s*UTF-8''([^;]+)/i.exec(disposition);
                if (utfMatch && utfMatch[1]) {
                    try {
                        return decodeURIComponent(utfMatch[1].replace(/["']/g, '').trim());
                    } catch (e) {}
                }
                var match = /filename\s*=\s*("?)([^";]+)\1/i.exec(disposition);
                if (match && match[2]) {
                    return match[2].trim();
                }
                return fallback;
            }

            function saveBlobFile(blob, filename, mimeType) {
                var fileBlob = blob;
                if (mimeType && (!blob.type || blob.type === 'application/octet-stream')) {
                    fileBlob = new Blob([blob], { type: mimeType });
                }

                var objectUrl = window.URL.createObjectURL(fileBlob);
                var link = document.createElement('a');
                link.style.display = 'none';
                link.href = objectUrl;
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                setTimeout(function () {
                    link.remove();
                    window.URL.revokeObjectURL(objectUrl);
                }, 1500);
            }

            function startStockDownloadRequest(url, format, label) {
                var fallbackName = format === 'excel'
                    ? ('Stock-List-' + Date.now() + '.xlsx')
                    : ('Stock-List-' + Date.now() + '.pdf');
                var mimeType = format === 'excel'
                    ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    : 'application/pdf';
                var hasRealProgress = false;

                stopStockDownloadSimulation();
                stockDownloadSimTimer = setInterval(function () {
                    if (hasRealProgress || stockDownloadPercent >= 90) {
                        return;
                    }
                    // Slow crawl only until real progress (or completion) arrives.
                    setStockDownloadPercent(stockDownloadPercent + 1);
                }, 250);

                stockDownloadXhr = new XMLHttpRequest();
                stockDownloadXhr.open('GET', url, true);
                stockDownloadXhr.responseType = 'blob';

                stockDownloadXhr.onprogress = function (event) {
                    if (!event.lengthComputable || !event.total) {
                        return;
                    }
                    hasRealProgress = true;
                    stopStockDownloadSimulation();
                    var pct = Math.floor((event.loaded / event.total) * 100);
                    setStockDownloadPercent(Math.min(99, pct));
                };

                stockDownloadXhr.onload = function () {
                    stopStockDownloadSimulation();
                    var xhr = stockDownloadXhr;
                    stockDownloadXhr = null;

                    if (!xhr || xhr.status < 200 || xhr.status >= 300) {
                        dismissStockDownloadGrowl();
                        alert('Download failed. Please try again.');
                        return;
                    }

                    var filename = parseDownloadFilename(
                        xhr.getResponseHeader('Content-Disposition'),
                        fallbackName
                    );
                    if (format === 'excel' && !/\.xlsx?$/i.test(filename)) {
                        filename += '.xlsx';
                    }
                    if (format === 'pdf' && !/\.pdf$/i.test(filename)) {
                        filename += '.pdf';
                    }

                    setStockDownloadPercent(100);

                    if (format === 'excel') {
                        saveBlobFile(xhr.response, filename, mimeType);
                        completeStockDownloadNotify('Excel downloaded');
                    } else {
                        // PDF: open preview tab from the same completed blob.
                        var objectUrl = window.URL.createObjectURL(
                            new Blob([xhr.response], { type: mimeType })
                        );
                        window.open(objectUrl, '_blank');
                        setTimeout(function () {
                            window.URL.revokeObjectURL(objectUrl);
                        }, 60000);
                        completeStockDownloadNotify('PDF ready');
                    }
                };

                stockDownloadXhr.onerror = function () {
                    stopStockDownloadSimulation();
                    stockDownloadXhr = null;
                    dismissStockDownloadGrowl();
                    alert('Download failed. Please try again.');
                };

                stockDownloadXhr.onabort = function () {
                    stopStockDownloadSimulation();
                    stockDownloadXhr = null;
                };

                stockDownloadXhr.send();
            }

            function downloadStockExport(url, format) {
                abortStockDownload();
                clearStockDownloadHideTimer();

                var label = format === 'excel' ? 'Downloading Excel…' : 'Downloading PDF…';

                // 1) Toaster first
                showStockDownloadNotify(label);
                setStockDownloadPercent(0);

                // 2) Let rotateIn play, then start the actual download with % updates
                clearStockDownloadStartTimer();
                stockDownloadStartTimer = setTimeout(function () {
                    stockDownloadStartTimer = null;
                    startStockDownloadRequest(url, format, label);
                }, 450);
            }

            function closeStockExportDropdowns() {
                $('.stock-export-dropdown').each(function () {
                    var $dropdown = $(this);
                    var $toggle = $dropdown.find('.stock-export-toggle');
                    var $menu = $dropdown.find('.stock-export-menu');

                    $dropdown.removeClass('show');
                    $toggle
                        .removeClass('show')
                        .attr('aria-expanded', 'false')
                        .blur();
                    $menu
                        .removeClass('show')
                        .attr('aria-expanded', 'false')
                        .removeAttr('style')
                        .css('display', 'none');
                });

                // Collapse any leftover open menus from Bootstrap/Popper.
                $('.stock-export-menu').removeClass('show').css('display', 'none');
            }

            function exportSelectedStocks(format) {
                var selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    alert('Please select at least one item to export.');
                    return;
                }

                var url = format === 'excel'
                    ? @json(route('stocks.export-excel')) + '?ids=' + selectedIds.join(',')
                    : @json(route('stocks.print')) + '?ids=' + selectedIds.join(',');

                closeStockExportDropdowns();
                downloadStockExport(url, format);
            }

            // Ensure menu can reopen after we force-hide it.
            $(document).on('show.bs.dropdown', '.stock-export-dropdown', function () {
                $(this).find('.stock-export-menu').css('display', '');
            });

            $(document).on('hide.bs.dropdown', '.stock-export-dropdown', function () {
                $(this).find('.stock-export-menu').css('display', 'none');
            });

            $(document).on('click', '.stock-export-option', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var format = $(this).data('format');
                closeStockExportDropdowns();

                // Run after current Bootstrap dropdown handlers finish.
                setTimeout(function () {
                    closeStockExportDropdowns();
                    exportSelectedStocks(format);
                }, 0);
            });
        });
    </script>
@endpush
