@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <!-- Select 2 css -->

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
            align-items: stretch;
            background: #f8fafc;
            height: calc(100vh - 4rem);
            max-height: calc(100vh - 4rem);
            min-height: 0;
            margin: -20px;
            overflow: hidden;
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
            gap: 16px;
            background: linear-gradient(180deg, #fff8f8, #fff5f5);
            border: 1px solid #fecaca;
            border-radius: 10px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
            padding: 12px 14px;
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
            flex: 0 0 140px;
            max-width: 140px;
            min-width: 120px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu {
            z-index: 9999 !important;
        }

        .stock-item-code {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* 2. Main Content Area — header + tabs pinned; only tab body scrolls */
        .stock-main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding-bottom: 0;
            min-width: 0;
            min-height: 0;
            height: 100%;
        }

        .stock-main-content > form {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            min-width: 0;
            overflow: hidden;
        }

        .stock-tab-content {
            display: none;
            flex: 1 1 auto;
            min-height: 0;
            flex-direction: column;
            overflow: hidden;
        }

        .stock-tab-content.active {
            display: flex !important;
        }

        /* Summary Header — in-flow (shipment-edit visual language; not fixed) */
        .summary-header {
            background:
                linear-gradient(135deg, rgba(240, 250, 251, 0.95) 0%, #ffffff 48%, #f8fafc 100%);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            border: 1px solid rgba(0, 136, 199, 0.14);
            border-left: 4px solid #008080;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            margin: 8px 8px 0;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            width: auto;
            min-height: 0;
            height: auto;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04);
            overflow: hidden;
        }
        .summary-header::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -4%;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 174, 239, 0.10), transparent 68%);
            pointer-events: none;
        }
        .summary-header-main {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            min-width: 0;
            flex: 1 1 auto;
            position: relative;
            z-index: 1;
        }
        .summary-header-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(145deg, #00aeef 0%, #008080 55%, #0e1d4a 100%);
            color: #fff;
            font-size: 21px;
            flex-shrink: 0;
            box-shadow:
                0 4px 12px rgba(0, 128, 128, 0.32),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
            line-height: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .summary-header-icon i {
            color: #fff !important;
            line-height: 1;
        }
        .header-meta-group {
            display: flex;
            gap: 10px 12px;
            flex-wrap: wrap;
            min-width: 0;
            flex: 1 1 auto;
            align-items: stretch;
        }

        @media (max-width: 991.98px) {
            .stock-edit-wrapper {
                margin: 0 !important;
                min-height: auto;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
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
                height: auto !important;
                padding-bottom: 0;
            }

            .page-body:has(.stock-edit-wrapper) {
                padding-bottom: calc(72px + env(safe-area-inset-bottom)) !important;
            }

            .edit-footer {
                position: fixed !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100% !important;
                max-width: 100vw !important;
                margin: 0 !important;
                padding: 12px 16px max(12px, env(safe-area-inset-bottom)) !important;
                flex-wrap: wrap;
                gap: 10px;
                z-index: 1040 !important;
                box-sizing: border-box !important;
            }

            .summary-header {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: auto !important;
                height: auto !important;
                min-height: 0;
                flex-wrap: wrap;
                gap: 12px;
                padding: 12px;
                border-radius: 12px 12px 0 0;
                margin: 8px 8px 0;
            }

            .header-meta-group {
                flex-wrap: wrap;
                gap: 10px 12px !important;
                width: 100%;
            }

            .summary-header .header-actions,
            .summary-header .summary-actions {
                width: 100%;
                margin-left: 0;
            }

            .stock-tabs-container {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: auto !important;
                margin: 0 8px !important;
                padding: 0 12px !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                border-radius: 0 !important;
            }

            .stock-tabs {
                display: inline-flex;
                flex-wrap: nowrap;
                gap: 20px;
                min-width: max-content;
            }

            .stock-form-scroll {
                padding: 8px !important;
                margin: 0 8px !important;
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
                height: auto !important;
                margin-top: 5px !important;
                padding: 12px !important;
                order: 3;
                border-left: none;
                border-top: 1px solid #e2e8f0;
                overflow: visible !important;
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

        .header-meta-group,
        .summary-info-group {
            display: flex;
            align-items: stretch;
            gap: 10px 12px;
            flex-wrap: wrap;
        }

        .meta-item,
        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
        }

        .meta-label,
        .summary-label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-value,
        .summary-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        .meta-value-primary,
        .summary-value-bold {
            color: #0e1d4a !important;
            font-size: 15px !important;
            font-weight: 800 !important;
            letter-spacing: 0.02em;
        }

        .meta-value-with-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .meta-value-with-icon .meta-cal-icon {
            color: #008080;
            font-size: 12px;
            opacity: 0.95;
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
            border: 1px solid #cce7f5;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            color: #0e1d4a;
            background: linear-gradient(180deg, #f8fcff, #eef7fc);
            display: inline-block;
            font-weight: 600;
        }

        .summary-flag-landed {
            background: #dcf0fa;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            display: inline-block;
            font-weight: 600;
        }

        .status-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .header-inline-edit {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-inline-display {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .header-inline-select {
            display: none;
            min-width: 150px;
        }

        .flag-icon {
            width: 18px;
            height: 12px;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid #eee;
        }

        /* Header Buttons */
        .header-actions,
        .summary-actions {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex-shrink: 0;
            margin-left: auto;
            z-index: 1;
        }

        .btn-premium {
            font-size: 11px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            letter-spacing: 0.01em;
        }

        .btn-outline-custom,
        .btn-header-outline {
            border: 1px solid #d1d5db;
            color: #334155;
            background: #fff;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover:not(:disabled),
        .btn-header-outline:hover:not(:disabled) {
            border-color: #00aeef;
            color: #0e1d4a;
            background: #f0faff;
        }

        .btn-header-outline:disabled,
        .btn-outline-custom:disabled {
            opacity: 0.55;
            cursor: default;
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

        /* Tabs — flush under summary (no gap) */
        .stock-tabs-container {
            background: #fff;
            border: 1px solid rgba(0, 136, 199, 0.14);
            border-top: 1px solid #e8edf2;
            border-bottom: 1px solid #e8edf2;
            border-left: 4px solid #008080;
            padding: 0 14px;
            position: relative;
            z-index: 5;
            flex-shrink: 0;
            margin: 0 8px;
            border-radius: 0;
            box-shadow: none;
        }

        .stock-tabs {
            display: flex;
            gap: 24px;
        }

        .stock-tab {
            padding: 10px 4px;
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
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: #008080;
        }

        /* Form Scroll Area — flush under tabs */
        .stock-form-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px 8px 16px;
            background: transparent;
            margin: 0 8px;
            border: 1px solid rgba(0, 136, 199, 0.14);
            border-top: none;
            border-left: 4px solid #008080;
            border-radius: 0 0 12px 12px;
            background: #f8fafc;
            -webkit-overflow-scrolling: touch;
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
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 4px;
        }

        .field-input {
            width: 100%;
            height: var(--mc-control-height, 34px);
            min-height: var(--mc-control-height, 34px);
            padding: 0 10px;
            font-size: var(--mc-control-font-size, 13px);
            border: 1px solid #e2e8f0;
            border-radius: var(--mc-control-radius, 8px);
            background: #fff;
            position: relative;
            line-height: 1.25;
            box-sizing: border-box;
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

        /* Footer — in-flow under form (shipment-edit style) */
        .edit-footer {
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            position: relative;
            flex-shrink: 0;
            width: 100%;
            left: auto;
            right: auto;
            bottom: auto;
            z-index: 5;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06);
            margin-top: auto;
        }

        .edit-footer .btn-save-custom,
        .btn-save-custom {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .edit-footer .btn-save-custom:hover:not(:disabled),
        .btn-save-custom:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.34);
            color: #fff;
        }

        .edit-footer .btn-save-custom:disabled,
        .btn-save-custom:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
        }

        .edit-footer .btn-cancel-custom,
        .btn-cancel-custom {
            color: #008080;
            font-size: 13px;
            text-decoration: none;
            font-weight: 700;
            background: transparent;
            border: none;
            padding: 0;
            box-shadow: none;
        }

        .edit-footer .btn-cancel-custom:hover,
        .btn-cancel-custom:hover {
            text-decoration: underline;
            color: #0e1d4a;
        }

        /* 3. Right Panel Sidebar — match main column height */
        .stock-right-panel {
            width: 400px;
            background: #f8fafc;
            border-left: 1px solid #e2e8f0;
            padding: 8px 8px 8px 4px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow: hidden;
            overflow-x: hidden;
            margin-top: 0;
            align-self: stretch;
            height: 100%;
            min-height: 0;
            min-width: 0;
            flex: 0 0 400px;
            box-sizing: border-box;
        }

        .stock-right-panel > .panel-card {
            display: flex;
            flex-direction: column;
            min-height: 0;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            overflow-x: hidden;
        }

        /* Documents + Activity share column height; dropzone always visible */
        .stock-right-panel > #crr-documents-panel {
            flex: 1 1 46%;
            min-height: 240px;
            max-height: none;
        }

        .stock-right-panel > #crr-activity-panel {
            flex: 1 1 54%;
            min-height: 0;
        }

        .stock-right-panel .panel-title,
        .stock-right-panel .crr-docs-header,
        .stock-right-panel .panel-tabs,
        .stock-right-panel > #crr-activity-panel > .panel-tabs {
            flex-shrink: 0;
        }

        #crr-doc-list {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
        }

        /* Keep upload zone pinned at bottom of documents card (never clipped) */
        #crr-documents-panel #crr-dropzone,
        #crr-documents-panel .dropzone-placeholder {
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            margin-top: 10px;
            order: 10;
        }

        #crr-activity-panel #panel-contents {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #crr-activity-panel .panel-tab-content {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none !important;
            overflow-y: auto;
        }

        .panel-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px 12px;
            border: 1px solid rgba(214, 227, 238, 0.95);
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 8px 22px rgba(14, 29, 74, 0.05);
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 0 0 10px;
            padding: 0 0 10px;
            border-bottom: 1px solid #e8edf2;
        }

        .panel-title__label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 800;
            color: #0e1d4a;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            line-height: 1;
        }

        .panel-title__label::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.16);
            flex-shrink: 0;
        }

        .panel-title__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 22px;
            padding: 0 8px;
            border-radius: 999px;
            background: #e8f6fc;
            border: 1px solid rgba(0, 136, 199, 0.18);
            color: #0088c7;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1;
        }

        .crr-docs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding: 0 2px 6px;
            border-bottom: 1px solid #eef2f6;
            gap: 8px;
            min-width: 0;
        }

        .crr-docs-header span {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .crr-docs-header .crr-docs-internal-label {
            width: auto;
            min-width: 0;
            text-align: right;
            margin-right: 0;
            flex: 0 0 auto;
        }

        #crr-documents-panel {
            overflow-x: hidden !important;
            min-width: 0;
            max-width: 100%;
        }

        #crr-doc-list {
            overflow-x: hidden;
            overflow-y: auto;
            min-width: 0;
            max-width: 100%;
        }

        .doc-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px 6px;
            margin: 0 0 2px;
            border-bottom: none;
            border-radius: 8px;
            transition: background 0.15s ease;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .doc-item:hover {
            background: #f5fafb;
        }

        .doc-item:last-child {
            border-bottom: 0;
        }

        .doc-main {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            padding-top: 1px;
            overflow: hidden;
        }

        .doc-name {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #008080 !important;
            line-height: 1.35;
            text-decoration: none;
            word-break: break-word;
            overflow-wrap: anywhere;
            max-width: 100%;
        }

        .doc-name:hover {
            text-decoration: underline;
            color: #00aeef !important;
        }

        .no-docs-msg {
            text-align: center;
            padding: 24px 12px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        /* Activity tabs */
        .panel-tabs {
            display: flex;
            gap: 2px;
            padding: 0 4px;
            background: linear-gradient(180deg, #f8fafc, #fff);
            border-bottom: 1px solid #e8edf2;
            flex-shrink: 0;
        }

        .stock-right-panel .panel-tab {
            flex: 1;
            text-align: center;
            padding: 12px 6px;
            font-size: 12px !important;
            font-weight: 700;
            color: #94a3b8;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color 0.15s ease, border-color 0.15s ease;
            white-space: nowrap;
        }

        .stock-right-panel .panel-tab:hover {
            color: #008080;
        }

        .stock-right-panel .panel-tab.active {
            color: #0e1d4a !important;
            border-bottom-color: #00aeef !important;
            font-weight: 800;
        }

        #crr-activity-panel {
            padding: 0 !important;
            overflow: hidden;
        }

        #crr-activity-panel #panel-contents {
            padding: 0;
        }

        #crr-activity-panel .panel-tab-content {
            padding: 12px 14px;
        }

        .change-log-item {
            border-bottom: none;
            padding: 10px 10px;
            margin: 0 0 6px;
            border-radius: 10px;
            background: linear-gradient(180deg, #fbfdff, #f8fafc);
            border: 1px solid #eef2f6;
        }

        .change-log-item:last-child {
            margin-bottom: 0;
        }

        .change-log-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .change-log-body {
            flex: 1;
            min-width: 0;
        }

        .change-log-title {
            font-size: 13px;
            font-weight: 700;
            color: #008080;
            line-height: 1.35;
        }

        .change-log-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.4;
        }

        .change-log-meta {
            text-align: right;
            min-width: 100px;
            flex-shrink: 0;
        }

        .change-log-user {
            font-size: 12px;
            color: #0e1d4a;
            font-weight: 700;
            line-height: 1.3;
        }

        .change-log-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
            line-height: 1.3;
        }

        .panel-empty-msg {
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
            padding: 28px 12px;
            font-weight: 500;
        }

        /* Drag & Drop Placeholder */
        .dropzone-placeholder {
            border: 1.5px dashed #94c9e3;
            border-radius: 10px;
            padding: 16px 12px;
            text-align: center;
            margin-top: 10px;
            background: linear-gradient(180deg, #f5fbfe, #eef8fc);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dropzone-placeholder:hover {
            border-color: #00aeef;
            background: #e8f6fc;
            box-shadow: 0 4px 12px rgba(0, 174, 239, 0.12);
        }

        .dropzone-text {
            font-size: 12px;
            color: #0088c7;
            font-weight: 700;
            margin: 0;
        }

        .dropzone-placeholder .dropzone-icon {
            font-size: 20px;
            color: #00aeef;
            display: block;
            margin-bottom: 6px;
        }

        .doc-type-select {
            display: block;
            margin-top: 4px;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 13px;
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
            max-width: 100% !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single {
            border: 0 !important;
            background: transparent !important;
            background-color: transparent !important;
            height: 22px !important;
            min-height: 22px !important;
            max-height: 22px !important;
            display: block !important;
            align-items: unset !important;
            max-width: 100% !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            line-height: 22px;
            padding: 0 16px 0 0 !important;
            background: transparent !important;
            background-color: transparent !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }

        #crr-documents-panel .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 22px;
            width: 14px;
            right: 0;
            top: 0;
        }

        .select2-dropdown.doc-type-select2-dropdown {
            z-index: 20060;
            min-width: 220px;
            font-size: 13px;
        }

        .select2-dropdown.doc-type-select2-dropdown .select2-results__options {
            max-height: 280px;
        }

        .doc-side {
            flex: 0 0 70px;
            width: 70px;
            max-width: 70px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            min-width: 0;
        }

        .doc-side-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
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
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.3;
            white-space: nowrap;
            text-align: right;
            min-height: 16px;
        }

        /* Select2 Overrides matches previous ones */
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #e2e8f0 !important;
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #334155 !important;
            font-size: var(--mc-control-font-size, 13px) !important;
            padding-left: 10px !important;
            background-color: #fff !important;
        }


        /* CRR Form Specific Styles from Create-CRR */
        .stock-grid-card {
            background: #fff;
            border: 1px solid rgba(214, 227, 238, 0.95);
            border-radius: 14px;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 10px 28px rgba(14, 29, 74, 0.05);
            margin: 18px 0 20px;
            overflow: hidden;
        }

        .crr-table-header {
            font-size: 13px;
            font-weight: 700;
            color: #0e1d4a;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 14px 16px;
            background:
                linear-gradient(135deg, rgba(240, 250, 251, 0.95) 0%, #ffffff 55%, #f8fafc 100%);
            border-bottom: 1px solid #e8edf2;
        }

        .crr-table-header__title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex-wrap: wrap;
        }

        .crr-table-header__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(145deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 15px;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0, 128, 128, 0.25);
        }

        .crr-table-header__icon i {
            color: #fff !important;
            line-height: 1;
        }

        .crr-table-header__label {
            font-size: 14px;
            font-weight: 800;
            color: #0e1d4a;
            letter-spacing: 0.01em;
        }

        #package-summary-text,
        .package-summary-badge {
            display: inline-flex;
            align-items: center;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #008080 !important;
            background: rgba(0, 174, 239, 0.08);
            border: 1px solid rgba(0, 136, 199, 0.18);
            border-radius: 999px;
            padding: 4px 12px;
            line-height: 1.3;
        }

        .crr-data-table {
            width: 100%;
            margin-top: 0;
            background: #fff;
            border: none;
            border-collapse: collapse;
        }

        .crr-data-table th {
            background: linear-gradient(180deg, #e8f6fc 0%, #f0fafd 100%);
            padding: 11px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #0e1d4a;
            border-bottom: 1px solid #d6e3ee;
            text-align: left;
            white-space: nowrap;
            vertical-align: middle;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .crr-data-table td {
            padding: 10px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background: #fff;
        }

        .crr-data-table tbody tr:hover > td {
            background: #f8fcfd;
        }

        .crr-data-table tbody tr.empty-row td {
            padding: 28px 16px;
            color: #94a3b8;
            font-size: 13px;
            background: #fafbfc;
        }

        .stock-grid-card .table-responsive {
            margin: 0 !important;
            padding: 0 4px 8px;
        }

        .stock-grid-card .costs-table-wrap {
            margin-bottom: 0 !important;
        }

        /* Costs grid: keep columns aligned; hub/currency Select2 need room */
        #costsTable {
            min-width: 1180px;
            table-layout: fixed;
        }

        #costsTable th:nth-child(1),
        #costsTable td:nth-child(1) {
            width: 36px;
        }

        #costsTable th:nth-child(4),
        #costsTable td:nth-child(4),
        #costsTable th:nth-child(6),
        #costsTable td:nth-child(6) {
            width: 88px;
        }

        #costsTable th:nth-child(5),
        #costsTable td:nth-child(5) {
            width: 110px;
        }

        #costsTable th:nth-child(9),
        #costsTable td:nth-child(9) {
            width: 170px;
            min-width: 150px;
        }

        #costsTable th:nth-child(10),
        #costsTable td:nth-child(10) {
            width: 90px;
        }

        #costsTable th:nth-child(11),
        #costsTable td:nth-child(11),
        #costsTable th:nth-child(12),
        #costsTable td:nth-child(12) {
            width: 48px;
            text-align: center;
        }

        #costsTable .select2.select2-container {
            width: 100% !important;
            max-width: 100%;
        }

        .select2-dropdown.cost-hub-select2-dropdown {
            min-width: 280px !important;
        }

        .select2-dropdown.cost-currency-select2-dropdown {
            min-width: 120px !important;
        }

        .crr-input {
            width: 100%;
            height: var(--mc-control-height, 34px);
            min-height: var(--mc-control-height, 34px);
            padding: 0 10px;
            font-size: var(--mc-control-font-size, 13px);
            border: 1px solid #d1d5db;
            border-radius: var(--mc-control-radius, 8px);
            outline: none;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            line-height: 1.25;
            box-sizing: border-box;
        }

        .crr-input:focus {
            border-color: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.12);
        }

        .crr-input[readonly] {
            background-color: #f8fafc;
            cursor: not-allowed;
            color: #64748b;
            font-weight: 600;
        }

        .btn-outline-teal {
            border: 1px solid #0088c7;
            background: #fff;
            color: #0088c7;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
            transition: all 0.15s ease;
        }

        .btn-outline-teal:hover {
            background: #e8f6fc;
            color: #0e1d4a;
            border-color: #00aeef;
        }

        .dgr-container {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            background: linear-gradient(180deg, #fff8f8, #fff5f5);
            border: 1px solid #fecaca;
            border-radius: 10px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
            padding: 12px 14px;
            margin: 4px 0 8px;
        }

        .irregularity-sub-row .dgr-container,
        .dgr-container--irregularity {
            background: linear-gradient(180deg, #fffbeb, #fff8e8);
            border-color: #fde68a;
        }

        /* Selected value (input box) */
        .select2-container .select2-selection--single .select2-selection__rendered {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            display: flex !important;
            align-items: center !important;
            line-height: 1.25 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            height: 100% !important;
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

        /* ========== MarineCaddie v2 visual polish (keep structure/IDs/classes intact) ========== */
        /* Shell page-body uses p-4/md:p-6/lg:p-7 — cancel so stock edit is full-bleed */
        .page-body:has(.stock-edit-wrapper) {
            padding: 0 !important;
        }

        .stock-edit-wrapper {
            background:
                radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.07), transparent 50%),
                radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.04), transparent 45%),
                #f5f7fb !important;
            margin: 0 !important;
            width: 100%;
            height: calc(100vh - 4rem) !important;
            max-height: calc(100vh - 4rem) !important;
            overflow: hidden !important;
            align-items: stretch !important;
        }

        .summary-header {
            /* in-flow card — pinned via flex, flush with tabs */
            top: auto !important;
            left: auto !important;
            right: auto !important;
            height: auto !important;
            min-height: 0;
            gap: 12px;
            padding: 12px 14px !important;
            position: relative !important;
            flex-shrink: 0 !important;
            z-index: 6 !important;
            margin: 8px 8px 0 !important;
            border-radius: 12px 12px 0 0 !important;
            border-bottom: none !important;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04) !important;
        }

        .summary-flag,
        .summary-flag-landed {
            border-radius: 999px !important;
            font-weight: 600 !important;
        }

        .btn-header-outline,
        .btn-outline-custom {
            border-radius: 8px !important;
        }

        .btn-header-outline:disabled,
        .btn-outline-custom:disabled {
            opacity: 0.55;
            cursor: default;
        }

        .btn-more-circle {
            background: #0088c7 !important;
        }

        .stock-tabs-container {
            background: #ffffff !important;
            border: 1px solid rgba(0, 136, 199, 0.14) !important;
            border-top: 1px solid #e8edf2 !important;
            border-bottom: 1px solid #e8edf2 !important;
            border-left: 4px solid #008080 !important;
            box-shadow: none !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            position: relative !important;
            flex-shrink: 0 !important;
            z-index: 5 !important;
            margin: 0 8px !important;
            padding: 0 14px !important;
            border-radius: 0 !important;
        }

        .stock-tab {
            font-weight: 600 !important;
            color: #64748b !important;
            transition: color 0.15s ease;
            padding: 10px 4px !important;
        }

        .stock-tab:hover {
            color: #0e1d4a !important;
        }

        .stock-tab.active {
            color: #0088c7 !important;
        }

        .stock-tab.active::after {
            background: #00aeef !important;
            height: 3px !important;
            border-radius: 3px 3px 0 0;
            bottom: 0 !important;
        }

        .stock-form-scroll {
            background: transparent !important;
            padding: 8px 4px 12px !important;
            margin: 0 !important;
            border: none !important;
            border-radius: 0 !important;
        }

        .edit-form-row {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0 !important;
            box-shadow: none;
            gap: 0 !important;
            width: 100% !important;
            max-width: none !important;
            box-sizing: border-box;
            margin: 0 -8px !important;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .edit-form-row.crr-pillars > .edit-form-col {
            display: flex;
            flex: 1 1 0 !important;
            min-width: 0 !important;
            overflow: visible !important;
            padding: 0 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        .stock-edit-wrapper .crr-pillar {
            width: 100%;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            padding: 14px 14px 12px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
            overflow: visible !important;
            position: relative;
            z-index: 1;
        }

        .stock-edit-wrapper .crr-pillar__title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 14px;
            padding: 0 0 10px 10px;
            border-bottom: 1px solid #e8eef4;
            border-left: 3px solid #00aeef;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .stock-edit-wrapper .crr-section-shell,
        .stock-edit-wrapper .stock-grid-card {
            margin-top: 8px;
            margin-bottom: 18px;
            padding: 14px 14px 16px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 40%);
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
        }

        .stock-edit-wrapper .crr-section-shell .crr-table-header,
        .stock-edit-wrapper .stock-grid-card .crr-table-header {
            margin: 0 0 12px !important;
            padding: 0 0 10px 10px !important;
            border-bottom: 1px solid #e8eef4;
            border-left: 3px solid #00aeef;
            background: transparent;
        }

        .edit-form-col {
            flex: 1 1 0 !important;
            min-width: 0 !important;
            overflow: visible !important;
        }

        /* Keep Select2 full-width inside Bootstrap-shim columns (e.g. currency row) */
        .stock-main-content .select2.select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Prevent residual overflow; keep Y scroll on form area only */
        .stock-edit-wrapper {
            max-width: 100%;
        }

        .stock-main-content {
            overflow-x: hidden !important;
            overflow-y: hidden !important;
            max-width: 100%;
            min-height: 0 !important;
        }

        .stock-form-scroll {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            max-width: 100%;
            min-height: 0 !important;
            flex: 1 1 auto !important;
        }

        .stock-main-content .row > [class*='col-'] {
            min-width: 0;
            overflow: visible !important;
        }

        #country-of-origin-host {
            position: relative !important;
            overflow: visible !important;
            z-index: 5;
        }

        #country-of-origin-host .select2-container--open,
        #country-of-origin-host .select2-dropdown {
            z-index: 10060 !important;
        }

        /* Flag + country name on one row (Tailwind preflight makes img block) */
        .mc-country-option {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px;
            white-space: nowrap;
            line-height: 1.2;
        }

        .mc-country-option .country-select-flag,
        .country-select-flag {
            display: inline-block !important;
            width: 20px !important;
            height: 15px !important;
            margin: 0 !important;
            flex-shrink: 0;
            vertical-align: middle;
            border: 1px solid #eee;
        }

        .mc-country-option__label {
            display: inline !important;
        }

        /* Country Select2 — match stock field height */
        .stock-main-content [data-country-select] + .select2-container .select2-selection--single {
            min-height: var(--mc-control-height, 34px) !important;
            height: var(--mc-control-height, 34px) !important;
            display: flex !important;
            align-items: center !important;
        }

        .stock-main-content [data-country-select] + .select2-container .select2-selection__rendered {
            line-height: 1.25 !important;
            padding-left: 10px !important;
            font-size: var(--mc-control-font-size, 13px) !important;
        }

        .stock-main-content [data-country-select] + .select2-container .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
        }

        .form-group-title {
            color: #0088c7 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 14px !important;
        }

        .form-group-title i {
            color: #00aeef !important;
        }

        .field-label {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: var(--mc-label-font-size, 13px) !important;
        }

        .field-input,
        .stock-main-content .select2-container--default .select2-selection--single,
        .stock-main-content .select2-container--default .select2-selection--multiple {
            border: 1px solid #d6e3ee !important;
            border-radius: 8px !important;
            background: #fff !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .field-input:focus,
        .stock-main-content .select2-container--default.select2-container--focus .select2-selection--single,
        .stock-main-content .select2-container--default.select2-container--open .select2-selection--single,
        .stock-main-content .select2-container--default.select2-container--focus .select2-selection--multiple,
        .stock-main-content .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #00aeef !important;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.15) !important;
            outline: none !important;
        }

        .field-input-static {
            color: #0088c7 !important;
            font-weight: 600 !important;
        }

        .icon-input-wrapper i {
            color: #0088c7 !important;
        }

        .edit-table-container {
            background: #fff;
            border: 1px solid rgba(214, 227, 238, 0.9);
            border-radius: 12px;
            padding: 14px 12px 8px;
            box-shadow: 0 10px 28px rgba(14, 29, 74, 0.05);
            margin-top: 14px !important;
            width: 100%;
            box-sizing: border-box;
        }

        .edit-table-title {
            color: #0e1d4a !important;
            font-weight: 700 !important;
        }

        .edit-table th {
            background: #e8f6fc !important;
            color: #0e1d4a !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 10px !important;
        }

        .edit-footer {
            background: rgba(255, 255, 255, 0.98) !important;
            border-top: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06) !important;
            backdrop-filter: blur(8px);
            gap: 20px !important;
            padding: 12px 28px !important;
            position: relative !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            width: 100% !important;
            flex-shrink: 0 !important;
        }

        .edit-footer .btn-save-custom,
        .btn-save-custom {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            background-color: transparent !important;
            background-image: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 28px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        }

        .edit-footer .btn-save-custom:hover:not(:disabled),
        .btn-save-custom:hover:not(:disabled) {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            background-image: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            filter: none;
            transform: translateY(-1px);
            color: #fff !important;
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.34) !important;
        }

        .edit-footer .btn-cancel-custom,
        .btn-cancel-custom {
            color: #008080 !important;
            font-weight: 700 !important;
            font-size: 13px !important;
        }

        .edit-footer .btn-cancel-custom:hover,
        .btn-cancel-custom:hover {
            color: #0e1d4a !important;
            text-decoration: underline;
        }

        .stock-right-panel {
            background:
                linear-gradient(180deg, rgba(248, 250, 252, 0.4) 0%, transparent 40%),
                transparent !important;
            border-left: none !important;
            gap: 12px !important;
            width: 340px !important;
            flex: 0 0 340px !important;
            padding: 8px 8px 8px 4px !important;
            margin-top: 0 !important;
            align-self: stretch !important;
            height: 100% !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        .stock-right-panel > .panel-card {
            display: flex !important;
            flex-direction: column !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        .stock-right-panel > #crr-documents-panel {
            flex: 1 1 46% !important;
            min-height: 240px !important;
            max-height: none !important;
        }

        .stock-right-panel > #crr-activity-panel {
            flex: 1 1 54% !important;
            min-height: 0 !important;
        }

        #crr-doc-list {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow-y: auto !important;
        }

        #crr-documents-panel #crr-dropzone,
        #crr-documents-panel .dropzone-placeholder {
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            margin-top: 10px !important;
        }

        #crr-activity-panel #panel-contents {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        #crr-activity-panel .panel-tab-content.active,
        #crr-activity-panel .panel-tab-content {
            max-height: none !important;
            overflow-y: auto !important;
            min-height: 0 !important;
        }

        .panel-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 8px 22px rgba(14, 29, 74, 0.05) !important;
            overflow: hidden;
            background: #fff !important;
            padding: 14px 12px !important;
        }

        /* Activity card keeps flush tabs (inline padding: 0) */
        .stock-right-panel .panel-card[style*="padding: 0"],
        .stock-right-panel .panel-card[style*="padding:0"],
        #crr-activity-panel {
            padding: 0 !important;
        }

        .panel-card::before {
            display: none !important;
            content: none !important;
        }

        .panel-title {
            margin-bottom: 10px !important;
        }

        .panel-title__label {
            color: #0e1d4a !important;
            font-weight: 800 !important;
        }

        .panel-title__count {
            background: #e8f6fc !important;
            color: #0088c7 !important;
        }

        .dropzone-placeholder {
            border: 1.5px dashed #94c9e3 !important;
            border-radius: 10px !important;
            background: linear-gradient(180deg, #f5fbfe, #eef8fc) !important;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }

        .dropzone-placeholder:hover {
            border-color: #00aeef !important;
            background: #e8f6fc !important;
        }

        .dropzone-text {
            color: #0088c7 !important;
            font-weight: 700 !important;
        }

        .dropzone-placeholder .dropzone-icon {
            color: #00aeef !important;
        }

        .doc-item {
            border-bottom: none !important;
        }

        .doc-name {
            color: #008080 !important;
            font-weight: 700 !important;
        }

        .stock-right-panel .panel-tab.active {
            color: #0e1d4a !important;
            border-bottom-color: #00aeef !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0088c7 !important;
        }

        #crr-form-errors {
            border-radius: 10px !important;
            box-shadow: 0 6px 16px rgba(185, 28, 28, 0.08);
        }

            @media (max-width: 991.98px) {
            html,
            body.stock-edit-page {
                overflow-x: hidden;
                overflow-y: auto;
                height: auto;
            }

            body.stock-edit-page .pcoded-content,
            body.stock-edit-page .pcoded-inner-content,
            body.stock-edit-page .page-wrapper,
            body.stock-edit-page .page-body {
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
                min-height: 0 !important;
            }

            body.stock-edit-page .page-body:has(.stock-edit-wrapper) {
                padding-bottom: calc(72px + env(safe-area-inset-bottom)) !important;
            }

            .stock-edit-wrapper {
                display: flex !important;
                flex-direction: column !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
            }

            .stock-main-content {
                order: 1 !important;
                flex: none !important;
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
            }

            .stock-main-content > form {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
            }

            .stock-tab-content,
            .stock-tab-content.active {
                display: block !important;
                flex: none !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                overflow-y: visible !important;
            }

            #line-items.stock-tab-content,
            #irregularities.stock-tab-content {
                padding: 12px !important;
            }

            .stock-form-scroll {
                flex: none !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
            }

            .summary-header {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                height: auto !important;
                min-height: 0 !important;
                flex-wrap: wrap !important;
                overflow: visible !important;
                padding: 8px 10px !important;
                gap: 8px !important;
                border-radius: 10px 10px 0 0 !important;
                margin: 4px 4px 0 !important;
                border-bottom: none !important;
                z-index: 1 !important;
            }

            .summary-header::before {
                display: none !important;
            }

            .summary-header-main {
                align-items: center !important;
                gap: 8px !important;
                width: 100% !important;
            }

            .summary-header-icon {
                width: 32px !important;
                height: 32px !important;
                border-radius: 8px !important;
                font-size: 15px !important;
                box-shadow: 0 2px 8px rgba(0, 128, 128, 0.24) !important;
            }

            .summary-header .header-meta-group {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 6px !important;
                width: auto !important;
                flex: 1 1 auto !important;
                align-items: stretch !important;
            }

            .summary-header .meta-item {
                padding: 5px 7px !important;
                border-radius: 8px !important;
                gap: 1px !important;
                min-width: 0 !important;
            }

            .summary-header .meta-label {
                font-size: 8px !important;
                letter-spacing: 0.04em !important;
                line-height: 1.2 !important;
            }

            .summary-header .meta-value {
                font-size: 11px !important;
                line-height: 1.25 !important;
            }

            .summary-header .meta-value-primary {
                font-size: 12px !important;
                line-height: 1.2 !important;
            }

            .summary-header .meta-value-with-icon {
                gap: 4px !important;
            }

            .summary-header .meta-value-with-icon .meta-cal-icon {
                font-size: 10px !important;
            }

            .summary-header .summary-flag,
            .summary-header .summary-flag-landed {
                padding: 2px 7px !important;
                font-size: 10px !important;
            }

            .summary-header .header-inline-edit .ti-pencil-alt {
                font-size: 12px !important;
            }

            .summary-header .flags-pills {
                gap: 4px !important;
            }

            .summary-header .header-actions,
            .summary-header .summary-actions {
                width: 100% !important;
                margin-left: 0 !important;
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 6px !important;
            }

            .summary-header .btn-header-outline,
            .summary-header .btn-outline-custom,
            .summary-header .btn-premium {
                width: 100% !important;
                padding: 6px 8px !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                justify-content: center !important;
                text-align: center !important;
            }

            .stock-tabs-container {
                position: sticky !important;
                top: 4rem !important;
                left: auto !important;
                right: auto !important;
                padding: 0 12px !important;
                margin: 0 8px !important;
                z-index: 20 !important;
                border-radius: 0 !important;
            }

            .stock-form-scroll {
                padding: 8px !important;
                margin: 0 8px !important;
            }

            .edit-form-row {
                flex-direction: column;
                padding: 12px !important;
                gap: 8px !important;
            }

            .edit-footer {
                position: fixed !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100% !important;
                max-width: 100vw !important;
                margin: 0 !important;
                padding: 12px 16px max(12px, env(safe-area-inset-bottom)) !important;
                z-index: 1040 !important;
                box-sizing: border-box !important;
            }

            .stock-right-panel {
                order: 2 !important;
                width: 100% !important;
                flex: none !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                margin-top: 0 !important;
                border-left: none !important;
                padding: 8px 12px 16px !important;
            }

            .stock-right-panel > .panel-card,
            .stock-right-panel > #crr-documents-panel,
            .stock-right-panel > #crr-activity-panel {
                flex: none !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
            }

            .stock-right-panel > #crr-documents-panel {
                min-height: 0 !important;
            }

            #crr-doc-list,
            #crr-activity-panel .panel-tab-content {
                max-height: min(42vh, 320px) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive {
                margin-top: 12px !important;
            }
        }

        @media (max-width: 575.98px) {
            .summary-header {
                padding: 6px 8px !important;
            }

            .summary-header .header-meta-group {
                grid-template-columns: 1fr 1fr !important;
                gap: 5px !important;
            }

            .summary-header .meta-item {
                padding: 4px 6px !important;
            }

            .summary-header .meta-value-primary {
                font-size: 11px !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('stock-edit-page');</script>
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
                                        <div class="summary-header-main">
                                            <span class="summary-header-icon" aria-hidden="true">
                                                <i class="icofont icofont-box"></i>
                                            </span>
                                            <div class="header-meta-group">
                                                <div class="meta-item">
                                                    <span class="meta-label">Stock number</span>
                                                    <span class="meta-value meta-value-primary">{{ $crr->stock_number }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <span class="meta-label">Registration date</span>
                                                    <span class="meta-value meta-value-with-icon">
                                                        <i class="ti-calendar meta-cal-icon" aria-hidden="true"></i>
                                                        <span>{{ $crr->created_at->format('d.m.Y') }}</span>
                                                    </span>
                                                </div>
                                                <div class="meta-item">
                                                    <span class="meta-label">Registered by</span>
                                                    <span class="meta-value text-primary">{{ $crr->registeredBy?->name ?? '—' }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <span class="meta-label">Account manager</span>
                                                    <span class="meta-value text-primary" id="summary-account-manager">
                                                        {{ $crr->customerVessel?->customer?->responsible?->accountManager?->name ?? '—' }}
                                                    </span>
                                                </div>
                                                <div class="meta-item">
                                                    <span class="meta-label">Flags</span>
                                                    <div class="header-inline-edit" id="flags-edit-container">
                                                        <div class="header-inline-display flags-display">
                                                            <div class="flags-pills" style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                                                                <div class="summary-flag-landed" id="header-landed-flag" {!! $crr->is_landed_goods ? '' : 'style="display: none;"' !!}>Landed</div>
                                                                @php
                                                                    $stockFlags = $crr->flags ?? \App\Models\Crr::defaultFlags();
                                                                @endphp
                                                                @forelse ($stockFlags as $flag)
                                                                    <span class="summary-flag">{{ $flag }}</span>
                                                                @empty
                                                                    <span class="text-muted" style="font-size: 11px; font-weight: 500;">—</span>
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
                                                <div class="meta-item">
                                                    <span class="meta-label">Status</span>
                                                    <div class="header-inline-edit" id="status-edit-container">
                                                        <div class="header-inline-display status-display">
                                                            <span class="status-badge stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</span>
                                                            <i class="ti-pencil-alt" style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                        </div>
                                                        <div class="header-inline-select status-select-wrapper" style="display: none; min-width: 150px;">
                                                            <select class="select2-status-inline" name="status">
                                                                @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                                                    <option value="{{ $value }}" {{ $crr->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header-actions summary-actions">
                                            <button type="button"
                                                id="accept-crr-btn"
                                                class="btn btn-premium btn-outline-custom btn-header-outline"
                                                data-stock-number="{{ $crr->stock_number }}"
                                                data-accept-url="{{ route('stocks.crr.update-accept', $crr->id) }}"
                                                {{ $crr->accept ? 'disabled' : '' }}>
                                                {{ $crr->accept ? 'Accepted' : 'Accept CRR' }}
                                            </button>
                                            <a href="{{ route('stocks.print-labels', $crr->id) }}" target="_blank"
                                                class="btn btn-premium btn-outline-custom btn-header-outline js-print-with-location"
                                                style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Print
                                                labels</a>
                                        </div>
                                    </div>

                                    <!-- Tabs -->
                                    <div class="stock-tabs-container">
                                        <div class="stock-tabs">
                                            <div class="stock-tab active" data-tab="stock-details">Stock details</div>
                                            <div class="stock-tab" data-tab="line-items">Line items</div>
                                            <div class="stock-tab" data-tab="irregularities">Irregularities</div>
                                        </div>
                                    </div>

                                    <!-- Main Form Content -->
                                    <div id="stock-details" class="stock-tab-content active">
                                        <div class="stock-form-scroll">
                                            <div class="edit-form-row crr-pillars">
                                                <!-- Column 1 -->
                                                <div class="edit-form-col">
                                                    <div class="crr-pillar">
                                                    <div class="crr-pillar__title">Vessel &amp; PO</div>
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
                                                    </div>{{-- .crr-pillar --}}
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="edit-form-col">
                                                    <div class="crr-pillar">
                                                    <div class="crr-pillar__title">Supplier &amp; delivery</div>
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
                                                    </div>{{-- .crr-pillar --}}
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="edit-form-col">
                                                    <div class="crr-pillar">
                                                    <div class="crr-pillar__title">Hub &amp; customs</div>
                                                    <div class="field-group">
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
                                                            <div class="field-group" id="country-of-origin-host">
                                                                <label class="field-label">Country of origin</label>
                                                                <x-forms.country-select
                                                                    name="country_of_origin"
                                                                    :countries="$countries"
                                                                    :value="$crr->country_of_origin"
                                                                    valueKey="name"
                                                                    wrapperClass=""
                                                                    class="field-input"
                                                                    placeholder="Select country"
                                                                    :allowClear="true"
                                                                />
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
                                                                <label class="field-label">Currency <span class="text-danger">*</span></label>
                                                                <select class="field-input select2" name="currency"
                                                                    id="edit_currency_select" required>
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
                                                                <label class="field-label">Customs value <span class="text-danger">*</span></label>
                                                                <input type="text" step="0.01" class="field-input"
                                                                    name="customs_value" id="edit_customs_value"
                                                                    value="{{ $crr->customs_value }}" required>
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
                                                    </div>{{-- .crr-pillar --}}
                                                </div>
                                            </div>

                                            <!-- Packages Section -->
                                            <div class="stock-grid-card crr-section-shell" id="packages-section-card">
                                            <div class="crr-table-header">
                                                <div class="crr-table-header__title">
                                                    <span class="crr-table-header__icon" aria-hidden="true"><i class="icofont icofont-box"></i></span>
                                                    <span class="crr-table-header__label">Packages</span>
                                                    <span id="package-summary-text" class="package-summary-badge">(Total : 0.00 kg, 0 Packages, 0.0000 CBM)</span>
                                                </div>
                                                <button type="button" class="btn btn-outline-teal btn-add-package">Add item</button>
                                            </div>
                                            <div id="packages-validation-error" style="display:none; margin: 10px 16px 0; padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; font-size: 12px; position: relative; padding-right: 32px;">
                                                <span id="packages-validation-error-text"></span>
                                                <button type="button" id="packages-validation-error-close" title="Close" aria-label="Close" style="position: absolute; top: 6px; right: 8px; border: none; background: transparent; color: #b91c1c; font-size: 16px; line-height: 1; cursor: pointer; padding: 2px 4px;">&times;</button>
                                            </div>
                                            <div class="table-responsive">
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
                                                                      <div class="dgr-container dgr-container--irregularity">
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
                                                                         <div class="dgr-field small">
                                                                             <label class="field-label">UN number</label>
                                                                             <input type="text" class="field-input"
                                                                                 name="packages[{{ $index }}][un_number]"
                                                                                 value="{{ $pkg->un_number }}" placeholder="">
                                                                         </div>
                                                                         <div class="dgr-field small">
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
                                            </div><!-- /packages-section-card -->

                                            <!-- Costs Section -->
                                            <div class="stock-grid-card crr-section-shell" id="costs-section-card">
                                            <div class="crr-table-header">
                                                <div class="crr-table-header__title">
                                                    <span class="crr-table-header__icon" aria-hidden="true"><i class="icofont icofont-money"></i></span>
                                                    <span class="crr-table-header__label">Costs</span>
                                                </div>
                                                <button type="button" class="btn btn-outline-teal btn-add-cost">Add cost</button>
                                            </div>
                                            <div class="table-responsive costs-table-wrap">
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
                                                            <th class="text-center">Copy</th>
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
                                                                        <optgroup label="Hubs">
                                                                            @foreach($hubs as $h)
                                                                                <option value="{{ $h->code }}"
                                                                                    data-city="{{ $h->city }}"
                                                                                    data-country="{{ $h->country }}"
                                                                                    data-code="{{ $h->code }}" {{ ($cost->hub_agent == $h->code || $cost->hub_agent == $h->hub_name) ? 'selected' : '' }}>
                                                                                    {{ $h->code }} - {{ $h->hub_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                        <optgroup label="Agents">
                                                                            @foreach($agents as $agent)
                                                                                <option value="{{ $agent->code }}"
                                                                                    data-city="{{ $agent->city }}"
                                                                                    data-country="{{ optional($agent->country)->name }}"
                                                                                    data-code="{{ $agent->code }}" {{ ($cost->hub_agent == $agent->code || $cost->hub_agent == $agent->agent_name) ? 'selected' : '' }}>
                                                                                    {{ $agent->code }} - {{ $agent->agent_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </optgroup>
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
                                                                <td colspan="12" class="text-center py-4 text-muted">No costs
                                                                    added yet. Click "Add cost" to start.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            </div><!-- /costs-section-card -->
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

                                    <!-- Footer (in-flow, shipment style) -->
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
                                    <div class="panel-title">
                                        <span class="panel-title__label">Documents</span>
                                        <span class="panel-title__count"><span class="doc-count">{{ $crr->documents->count() }}</span></span>
                                    </div>
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
                                                    <a href="{{ $doc->fileUrl() }}" class="doc-name js-doc-preview" data-preview-url="{{ $doc->fileUrl() }}" data-title="{{ $doc->file_name }}" title="{{ $doc->file_name }}">
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
                                            <div class="no-docs-msg">No documents uploaded yet.</div>
                                        @endforelse
                                    </div>

                                    <div class="dropzone-placeholder" id="crr-dropzone">
                                        <i class="ti-upload dropzone-icon"></i>
                                        <div class="dropzone-text">Drag files here or click to browse</div>
                                    </div>
                                    <input type="file" id="crr-file-input" style="display: none;" multiple>
                                </div>

                                <!-- Activity Panel -->
                                <div class="panel-card" id="crr-activity-panel">
                                    <div class="panel-tabs">
                                        <div class="panel-tab active" data-panel="change-log">Change log</div>
                                        <div class="panel-tab" data-panel="location-history">Location history</div>
                                        <div class="panel-tab" data-panel="comments">Comments</div>
                                    </div>
                                    <div id="panel-contents">
                                        <div id="change-log" class="panel-tab-content active">
                                            @forelse ($crr->changeLogs as $changeLog)
                                                <div class="change-log-item">
                                                    <div class="change-log-row">
                                                        <div class="change-log-body">
                                                            <div class="change-log-title">{{ $changeLog->title }}</div>
                                                            @if ($changeLog->description)
                                                                <div class="change-log-desc">
                                                                    {{ $changeLog->description }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="change-log-meta">
                                                            <div class="change-log-user">
                                                                {{ $changeLog->user?->name ?? 'System' }}
                                                            </div>
                                                            <div class="change-log-time">
                                                                {{ $changeLog->created_at->format('d.m.Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="panel-empty-msg">
                                                    No changes recorded yet.
                                                </div>
                                            @endforelse
                                        </div>
                                        <div id="location-history" class="panel-tab-content" style="display: none;">
                                            <div class="panel-empty-msg">No location history available</div>
                                        </div>
                                        <div id="comments" class="panel-tab-content" style="display: none;">
                                            <div class="panel-empty-msg">No comments available</div>
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
                                    <x-forms.country-select
                                        name="country_id"
                                        id="modal-supplier-country"
                                        :countries="$countries"
                                        wrapperClass=""
                                        class="field-input modal-supplier-country"
                                        placeholder="Select an option"
                                        :allowClear="true"
                                        dropdownParent="#add-supplier-modal"
                                    />
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
                                    <x-forms.country-select
                                        name="office_country_id"
                                        id="modal-office-country"
                                        :countries="$countries"
                                        wrapperClass=""
                                        class="field-input modal-supplier-country"
                                        placeholder="Select an option"
                                        :allowClear="true"
                                        dropdownParent="#add-supplier-modal"
                                    />
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

    <div class="modal fade" id="pdf-preview-modal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="pdfPreviewModalLabel" style="font-size: 14px; font-weight: 600;">Document preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" style="height: 80vh; background: #f3f4f6; position: relative;">
                    <iframe id="pdf-preview-frame" title="PDF preview" style="width: 100%; height: 100%; border: 0;" src="about:blank"></iframe>
                    <div id="pdf-preview-mobile-fallback" style="display:none; height:100%; align-items:center; justify-content:center; flex-direction:column; gap:12px; padding:24px; text-align:center;">
                        <p style="margin:0; color:#374151; font-size:14px;">This device cannot show PDFs inside the page.</p>
                        <a id="pdf-preview-mobile-open" href="#" target="_blank" rel="noopener" class="btn btn-sm" style="background:#008080;border-color:#008080;color:#fff;">Open PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Refresh exchange rates in the background when Stock edit opens.
            $.ajax({
                url: @json(route('currency.update')),
                type: 'GET',
                dataType: 'json'
            });

            // Mobile: pin save bar to viewport bottom (documents panel follows form in DOM)
            $('body').addClass('stock-edit-page');

            var $stockEditFooter = $('.stock-edit-wrapper .edit-footer');
            var $stockEditForm = $('#crrEditForm');

            function syncStockEditFooterPlacement() {
                if (!$stockEditFooter.length || !$stockEditForm.length) {
                    return;
                }

                var isMobile = window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches;

                if (isMobile) {
                    if (!$stockEditFooter.parent().is('body')) {
                        $stockEditFooter.appendTo('body');
                    }
                    return;
                }

                if (!$stockEditFooter.parent().is($stockEditForm)) {
                    $stockEditForm.append($stockEditFooter);
                }
            }

            syncStockEditFooterPlacement();
            $(window).on('resize.stockEditFooter', syncStockEditFooterPlacement);

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
                width: '100%',
                dropdownParent: $(document.body)
            });

            $('.select2-incoterm').select2({
                placeholder: 'Select incoterm',
                allowClear: true,
                width: '100%',
                dropdownParent: $(document.body)
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
                dropdownParent: $(document.body),
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
                dropdownParent: $(document.body),
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
                width: '100%',
                dropdownParent: $(document.body)
            });

            // Country of origin — host parent only (verified: body/body-pin misalign on this page)
            (function initStockCountrySelect() {
                var $country = $('#country_of_origin');
                var $host = $('#country-of-origin-host');
                if (!$country.length || !$host.length) {
                    return;
                }

                function resolveFlag($option) {
                    if (!$option || !$option.length) return null;
                    var flagUrl = $option.attr('data-flag-url') || $option.data('flagUrl');
                    if (flagUrl && String(flagUrl).indexOf('http') === 0) return flagUrl;
                    var iso = $option.attr('data-iso') || $option.data('iso');
                    if (iso && String(iso).length <= 3) {
                        return 'https://flagcdn.com/w20/' + String(iso).toLowerCase() + '.png';
                    }
                    return null;
                }

                function formatCountry(state) {
                    if (!state.id) return state.text;
                    var flagUrl = resolveFlag($(state.element));
                    if (!flagUrl) return state.text;
                    var $row = $('<span class="mc-country-option"></span>');
                    $row.append($('<img>', {
                        src: flagUrl,
                        class: 'country-select-flag',
                        alt: '',
                        css: {
                            display: 'inline-block',
                            width: '20px',
                            height: '15px',
                            marginRight: '8px',
                            verticalAlign: 'middle',
                            border: '1px solid #eee',
                            flexShrink: '0'
                        }
                    }));
                    $row.append($('<span class="mc-country-option__label"></span>').text(state.text));
                    return $row;
                }

                if ($country.hasClass('select2-hidden-accessible')) {
                    try { $country.select2('destroy'); } catch (e) {}
                }

                $host.css({ position: 'relative', overflow: 'visible' });

                $country.select2({
                    placeholder: 'Select country',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $host,
                    templateResult: formatCountry,
                    templateSelection: formatCountry
                });

                $country.next('.select2.select2-container').css('width', '100%');
            })();

            // Supplier Select2 is initialized in the quick-add script below.

            // --- Package Logic ---
            var packageIndex = {{ count($crr->packages) }};
            $('.btn-add-package').on('click', function () {
                $('#packagesTable tbody .empty-row').remove();
                let row = `<tr data-index="${packageIndex}"><td>${packageIndex + 1}</td><td><input name="packages[${packageIndex}][length]"class="crr-input pkg-dim pkg-l"step="0.01"></td><td><input name="packages[${packageIndex}][width]"class="crr-input pkg-dim pkg-w"step="0.01"></td><td><input name="packages[${packageIndex}][height]"class="crr-input pkg-dim pkg-h"step="0.01"></td><td><input name="packages[${packageIndex}][weight]"class="crr-input pkg-weight"step="0.01"></td><td><input name="packages[${packageIndex}][cbm]"class="crr-input pkg-cbm"readonly value="0"></td><td><input name="packages[${packageIndex}][warehouse_location]"class="crr-input"></td><td><input name="packages[${packageIndex}][remarks]" class="crr-input"></td><td class="text-center"><input type="checkbox" class="pkg-is-irregular" name="packages[${packageIndex}][is_delivery_irregularity]"></td><td class="text-center"><input name="packages[${packageIndex}][is_dgr]" class="pkg-is-dgr" type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_not_stackable]"type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_medicine]"type="checkbox"></td><td class="text-center"><input name="packages[${packageIndex}][is_xray]"type="checkbox"></td><td class="text-center"><button class="btn btn-link p-0 btn-copy-row text-primary"type="button"><i class="icofont icofont-copy-alt"></i></button></td><td class="text-center"><button class="btn btn-link p-0 btn-remove-row text-danger"type="button"><i class="icofont icofont-trash"></i></button></td></tr><tr class="irregularity-sub-row" data-index="${packageIndex}" style="display: none;"><td colspan="2"></td><td colspan="13"><div class="dgr-container" style="background: #fff9e6; border: 1px solid #ffeeba;"><i class="icofont icofont-warning dgr-warning-icon" style="color: #f0ad4e;"></i><div class="dgr-field" style="flex: 1;"><label class="crr-label">Delivery irregularities</label><select class="form-control select2 select2-irregularities" name="packages[${packageIndex}][delivery_irregularities][]" multiple="multiple"><option value="Damaged packaging - no repacking required">Damaged packaging - no repacking required</option><option value="Damaged packaging - repacking required">Damaged packaging - repacking required</option><option value="Missing DG label / marking on package">Missing DG label / marking on package</option><option value="Missing documentation - Commercial invoice / Packing list">Missing documentation - Commercial invoice / Packing list</option><option value="Missing documentation - DG">Missing documentation - DG</option><option value="Missing documentation - Other">Missing documentation - Other</option><option value="Missing label on packaging">Missing label on packaging</option><option value="Packaging not fit for airfreight">Packaging not fit for airfreight</option><option value="Packaging not fumigated">Packaging not fumigated</option><option value="Packaging not heat treated">Packaging not heat treated</option><option value="Vessel Name / PO Number not mentioned on packaging (label)">Vessel Name / PO Number not mentioned on packaging (label)</option><option value="Vessel Name / PO Number not mentioned on supplier documentation">Vessel Name / PO Number not mentioned on supplier documentation</option></select></div></div></td></tr><tr data-index="${packageIndex}"class="dgr-sub-row"style="display:none"><td colspan="2"></td><td colspan="7"><div class="dgr-container"><i class="icofont dgr-warning-icon icofont-warning"></i><div class="dgr-field"><label class="field-label">Dangerous goods description</label> <input name="packages[${packageIndex}][dgr_description]"class="field-input"placeholder=""></div><div class="dgr-field small"><label class="field-label">UN number</label> <input name="packages[${packageIndex}][un_number]"class="field-input"placeholder=""></div><div class="dgr-field small"><label class="field-label">Class</label> <input name="packages[${packageIndex}][dgr_class]"class="field-input"placeholder=""></div></div></td></tr>`;
                let $row = $(row); $('#packagesTable tbody').append($row); $row.filter('.irregularity-sub-row').find('.select2-irregularities').select2({placeholder: 'Select irregularities', allowClear: false, width: '100%'}).next('.select2-container').addClass('select2-irreg-container');
                packageIndex++;
                updatePackageSummary();
            });

            // --- Cost Logic ---
            var costIndex = {{ count($crr->costs) }};
            @php
                $costHubsForJs = $hubs->values();
                $costAgentsForJs = $agents->map(static function ($agent) {
                    return [
                        'code' => $agent->code,
                        'agent_name' => $agent->agent_name,
                        'city' => $agent->city,
                        'country' => optional($agent->country)->name,
                    ];
                })->values();
                $costCurrenciesForJs = $currencies->values();
            @endphp
            var costHubs = @json($costHubsForJs);
            var costAgents = @json($costAgentsForJs);
            var costCurrencies = @json($costCurrenciesForJs);

            function buildCostHubAgentOptions() {
                var hubOptions = '<option></option><optgroup label="Hubs">';
                costHubs.forEach(function (hub) {
                    hubOptions += '<option value="' + (hub.code || '') + '" data-city="' + (hub.city || '') + '" data-country="' + (hub.country || '') + '" data-code="' + (hub.code || '') + '">' +
                        (hub.code ? hub.code + ' - ' : '') + (hub.hub_name || '') +
                        '</option>';
                });
                hubOptions += '</optgroup><optgroup label="Agents">';
                costAgents.forEach(function (agent) {
                    hubOptions += '<option value="' + (agent.code || '') + '" data-city="' + (agent.city || '') + '" data-country="' + (agent.country || '') + '" data-code="' + (agent.code || '') + '">' +
                        (agent.code ? agent.code + ' - ' : '') + (agent.agent_name || '') +
                        '</option>';
                });
                hubOptions += '</optgroup>';
                return hubOptions;
            }

            function initCostHubSelect($el) {
                if (!$el || !$el.length) {
                    return;
                }
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: 'Select hub/agent',
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $(document.body),
                    dropdownCssClass: 'cost-hub-select2-dropdown',
                    templateResult: formatHub,
                    templateSelection: function (hub) {
                        return hub.text;
                    }
                });
            }

            function initCostCurrencySelect($el) {
                if (!$el || !$el.length) {
                    return;
                }
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: 'Select currency',
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $(document.body),
                    dropdownCssClass: 'cost-currency-select2-dropdown'
                });
            }

            $('.btn-add-cost').on('click', function () {
                $('#costsTable tbody .empty-row').remove();

                let hubOptions = buildCostHubAgentOptions();

                let currencyOptions = '<option></option>';
                costCurrencies.forEach(function (cur) {
                    currencyOptions += `<option value="${cur}">${cur}</option>`;
                });

                let row = ` <tr data-index="${costIndex}"><td>${costIndex + 1}</td><td><input type="text" class="crr-input" name="costs[${costIndex}][type]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][carrier]"></td><td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value]"></td><td><select class="crr-input select2-cost-currency" name="costs[${costIndex}][currency]">${currencyOptions}</select></td><td><input type="text" step="0.01" class="crr-input" name="costs[${costIndex}][net_value_usd]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][invoice_no]"></td><td><input type="text" class="crr-input" name="costs[${costIndex}][remarks]"></td><td><select class="crr-input select2-cost-hub" name="costs[${costIndex}][hub_agent]">${hubOptions}</select></td><td><input type="text" class="crr-input" name="costs[${costIndex}][tag]"></td><td class="text-center"><button type="button" class="btn btn-link text-primary p-0 btn-copy-row"><i  class="icofont icofont-copy-alt"></i></button></td><td class="text-center"><button type="button" class="btn btn-link text-danger p-0 btn-remove-row"><i class="icofont icofont-trash"></i></button></td></tr>`;
                let $row = $(row);
                $('#costsTable tbody').append($row);

                initCostHubSelect($row.find('.select2-cost-hub'));
                initCostCurrencySelect($row.find('.select2-cost-currency'));

                costIndex++;
            });

            // Initialize Select2 for existing cost rows
            $('.select2-cost-hub').each(function () {
                initCostHubSelect($(this));
            });

            $('.select2-cost-currency').each(function () {
                initCostCurrencySelect($(this));
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

                    newRow.find('.select2-cost-hub').each(function () {
                        initCostHubSelect($(this));
                    });

                    newRow.find('.select2-cost-currency').each(function () {
                        initCostCurrencySelect($(this));
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
                    $('#costsTable tbody').append('<tr class="empty-row"><td colspan="12" class="text-center py-4 text-muted">No costs added yet. Click "Add cost" to start.</td></tr>');
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

            // Main Tab Switching — desktop: inner scroll; mobile: page scroll
            function isStockEditMobileLayout() {
                return !!(window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches);
            }

            function activateStockTab(tabId) {
                $('.stock-tab').removeClass('active');
                $('.stock-tab[data-tab="' + tabId + '"]').addClass('active');
                $('.stock-tab-content').removeClass('active').hide().css('display', 'none');

                var $panel = $('#' + tabId);
                var isMobile = isStockEditMobileLayout();
                var hasInnerScroll = $panel.find('.stock-form-scroll').length > 0;

                if (isMobile) {
                    $panel.addClass('active').css({
                        display: 'block',
                        flex: 'none',
                        minHeight: '',
                        maxHeight: 'none',
                        overflow: 'visible',
                        overflowY: 'visible'
                    }).show();
                    return;
                }

                $panel
                    .addClass('active')
                    .css({
                        display: 'flex',
                        flexDirection: 'column',
                        flex: '1 1 auto',
                        minHeight: 0,
                        overflow: hasInnerScroll ? 'hidden' : 'auto'
                    })
                    .show();
            }

            $('.stock-tab').on('click', function () {
                activateStockTab($(this).data('tab'));
            });
            activateStockTab($('.stock-tab.active').data('tab') || 'stock-details');
            $(window).on('resize.stockEditTabs', function () {
                var activeTab = $('.stock-tab.active').data('tab') || 'stock-details';
                activateStockTab(activeTab);
            });

            // Right Panel Tab Switching
            $('.panel-tab').on('click', function () {
                var panelId = $(this).data('panel');
                $('.panel-tab').removeClass('active');
                $(this).addClass('active');
                $('.panel-tab-content').removeClass('active').hide();
                $('#' + panelId)
                    .addClass('active')
                    .css({ display: 'block', flex: '1 1 auto', minHeight: 0, overflowY: 'auto', maxHeight: 'none' })
                    .show();
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
                            <a href="${escapeHtml(doc.file_url)}" class="doc-name js-doc-preview" data-preview-url="${escapeHtml(doc.file_url)}" data-title="${escapeHtml(doc.file_name)}" title="${escapeHtml(doc.file_name)}">
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

            // Document PDF/image preview.
            // Desktop: blob URL in iframe. Mobile Chrome/Safari: open native PDF viewer (iframe stays blank).
            function revokePdfPreviewBlob() {
                var $frame = $('#pdf-preview-frame');
                var previous = $frame.data('blobUrl');
                if (previous) {
                    URL.revokeObjectURL(previous);
                    $frame.removeData('blobUrl');
                }
            }

            function isMobilePdfPreview() {
                var ua = navigator.userAgent || '';
                if (/Android|iPhone|iPad|iPod|Mobile/i.test(ua)) {
                    return true;
                }
                return !!(window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches);
            }

            function showMobilePdfFallback(objectUrl, sourceUrl) {
                var $frame = $('#pdf-preview-frame');
                var $fallback = $('#pdf-preview-mobile-fallback');
                var $open = $('#pdf-preview-mobile-open');
                var openUrl = objectUrl || sourceUrl;

                $frame.hide().attr('src', 'about:blank');
                $fallback.css('display', 'flex');
                $open.attr('href', openUrl);

                var opened = window.open(openUrl, '_blank');
                if (!opened && sourceUrl && sourceUrl !== openUrl) {
                    window.open(sourceUrl, '_blank');
                }
            }

            function showDesktopPdfPreview(objectUrl) {
                var $frame = $('#pdf-preview-frame');
                $('#pdf-preview-mobile-fallback').hide();
                $frame.show().attr('src', objectUrl);
            }

            function loadPdfIntoPreviewFrame(url) {
                var $frame = $('#pdf-preview-frame');
                revokePdfPreviewBlob();
                $frame.attr('src', 'about:blank');
                $('#pdf-preview-mobile-fallback').hide();
                $frame.show();

                return fetch(url, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/pdf,image/*,*/*' }
                }).then(function (res) {
                    if (!res.ok) {
                        throw new Error('Failed to load document (' + res.status + ')');
                    }
                    return res.blob();
                }).then(function (blob) {
                    var type = (blob && blob.type) ? blob.type : '';
                    var isImage = type.indexOf('image/') === 0;
                    if (!isImage && type.indexOf('pdf') === -1) {
                        blob = new Blob([blob], { type: 'application/pdf' });
                        type = 'application/pdf';
                    }
                    var objectUrl = URL.createObjectURL(blob);
                    $frame.data('blobUrl', objectUrl);

                    if (!isImage && isMobilePdfPreview()) {
                        showMobilePdfFallback(objectUrl, url);
                        return;
                    }

                    showDesktopPdfPreview(objectUrl);
                });
            }

            $(document).on('click', '.js-doc-preview', function (e) {
                e.preventDefault();
                var url = $(this).data('preview-url') || $(this).attr('href');
                var title = $(this).data('title') || 'Document preview';
                if (!url) {
                    return;
                }
                $('#pdfPreviewModalLabel').text(title);
                $('#pdf-preview-modal').modal('show');
                loadPdfIntoPreviewFrame(url).catch(function (err) {
                    if (typeof swal === 'function') {
                        swal({ title: 'Preview failed', text: err.message || 'Could not open document.', type: 'error' });
                    } else {
                        alert(err.message || 'Could not open document.');
                    }
                });
            });

            $('#pdf-preview-modal').on('hidden.bs.modal', function () {
                revokePdfPreviewBlob();
                $('#pdf-preview-mobile-fallback').hide();
                $('#pdf-preview-frame').show().attr('src', 'about:blank');
            });

            // Delete document
            function deleteStockDocument(btn) {
                var docId = btn.data('id');
                var docItem = btn.closest('.doc-item');

                $.ajax({
                    url: "{{ url('/stocks/documents') }}/" + docId,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (typeof swal === 'function') {
                            swal.close();
                        }
                        if (response.success) {
                            var $select = docItem.find('.doc-type-select');
                            if ($select.hasClass('select2-hidden-accessible')) {
                                $select.select2('destroy');
                            }
                            docItem.fadeOut(300, function () {
                                $(this).remove();
                                updateDocCount(-1);
                                if (docList.children('.doc-item').length === 0) {
                                    docList.append('<div class="no-docs-msg" style="text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">No documents uploaded yet.</div>');
                                }
                            });
                            toastr.success('Document deleted');
                        } else {
                            toastr.error('Delete failed');
                        }
                    },
                    error: function () {
                        if (typeof swal === 'function') {
                            swal.close();
                        }
                        toastr.error('An error occurred');
                    }
                });
            }

            $(document).on('click', '.delete-doc', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                var message = 'Are you sure you want to delete this document?';

                if (typeof swal !== 'function') {
                    if (confirm(message)) {
                        deleteStockDocument($btn);
                    }
                    return;
                }

                swal({
                    title: 'Delete document?',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        deleteStockDocument($btn);
                    }
                });
            });

            function updateDocCount(delta) {
                let current = parseInt(docCountBadge.text()) || 0;
                docCountBadge.text(current + delta);
            }

            // Datepicker Initialization
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                beforeShow: function (input, inst) {
                    $('.crr-pillar').css('z-index', '');
                    $(input).closest('.crr-pillar').css('z-index', 40);
                    setTimeout(function () {
                        $('#ui-datepicker-div').css('z-index', 10060);
                    }, 0);
                },
                onClose: function () {
                    $(this).closest('.crr-pillar').css('z-index', '');
                }
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

        function initModalSupplierSelect2() {
            $('.modal-supplier-country').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            });

            if (window.MarineCaddieInitCountrySelect) {
                window.MarineCaddieInitCountrySelect($modal);
            }

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
            dropdownParent: $(document.body),
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
            var $currency = $('#edit_currency_select');
            var $customsValue = $('#edit_customs_value');
            var $currencySelection = $currency.next('.select2-container').find('.select2-selection');

            $rows.find('.pkg-l, .pkg-w, .pkg-h, .pkg-weight').css('border-color', '');
            $customsValue.css('border-color', '');
            $currencySelection.css('border-color', '');

            if (!$.trim(String($currency.val() || ''))) {
                e.preventDefault();
                $currencySelection.css('border-color', '#dc3545');
                $errorText.text('Currency is required.');
                $errorBox.show();
                $('html, body').animate({
                    scrollTop: $currency.closest('.field-group').offset().top - 100
                }, 300);
                return false;
            }

            var customsValue = $.trim(String($customsValue.val() || ''));
            if (customsValue === '' || isNaN(parseFloat(customsValue))) {
                e.preventDefault();
                $customsValue.css('border-color', '#dc3545');
                $errorText.text('Customs value is required.');
                $errorBox.show();
                $('html, body').animate({
                    scrollTop: $customsValue.offset().top - 100
                }, 300);
                return false;
            }

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