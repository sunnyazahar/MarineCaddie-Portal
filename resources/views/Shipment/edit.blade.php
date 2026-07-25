@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <style>
        /* Master Layout Structure Adjustments for Global Sidebar */
        .master-container {
            display: flex;
            height: calc(100vh - 120px); /* Adjusted for pcoded navigation */
            background: #f4f7f6;
            overflow: hidden;
            margin: -20px -5px; /* Pull to edges of page-wrapper */
        }

        /* Left Sidebar - Shipment List */
        .left-sidebar {
            width: 180px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
        }
        .sidebar-header a {
            color: #008080;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
        }
        .shipment-list {
            flex: 1;
            overflow-y: auto;
        }
        .shipment-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f9fafb;
            cursor: pointer;
            transition: background 0.2s;
        }
        .shipment-item:hover {
            background: #f0fdfa;
        }
        .shipment-item.active {
            background: #f0fdfa;
            border-left: 3px solid #008080;
        }
        .item-id {
            font-size: 11px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2px;
        }
        .item-meta {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.3;
        }
        .item-date {
            font-size: 9px;
            color: #9ca3af;
            float: right;
            margin-top: -15px;
        }

        /* Main Content Area */
        .main-content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 0 10px;
            padding-bottom: 72px;
        }

        /* Summary Header */
        .summary-header {
            background: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 5px;
        }
        .header-meta-group {
            display: flex;
            gap: 25px;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        .status-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .summary-flag {
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: #475569;
            background: #fff;
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
        .header-actions {
            display: flex;
            gap: 10px;
        }

        /* Tabs Styling */
        .custom-nav-tabs {
            background: #f4f7f6;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 2px;
            padding: 0 5px;
        }
        .nav-tab-item {
            padding: 10px 20px;
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .nav-tab-item:hover {
            color: #008080;
        }
        .nav-tab-item.active {
            background: #fff;
            color: #008080;
            border-bottom: 2px solid #008080;
            border-radius: 4px 4px 0 0;
        }

        /* Main Form Area Grid */
        .form-scroller {
            flex: 1;
            overflow-y: auto;
            background: #fff;
            padding: 20px;
            padding-bottom: 30px;
        }

        /* Fixed Footer Section */
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

        .edit-footer .btn-save-custom {
            background-color: #008080;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
        }

        .edit-footer .btn-save-custom:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .edit-footer .btn-cancel-custom {
            color: #008080;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
        }

        .edit-footer .btn-cancel-custom:hover {
            text-decoration: underline;
        }
        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .form-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #008080;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .edit-icon {
            color: #9ca3af;
            cursor: pointer;
            font-size: 12px;
        }
        .form-group-custom {
            margin-bottom: 12px;
        }
        .form-group-custom label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
            display: block;
        }
        .form-control-sm-custom {
            height: 30px;
            font-size: 11px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            width: 100%;
            padding: 0 10px;
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #008080;
            font-size: 12px;
            cursor: pointer;
        }

        /* Stock Items Section */
        .stock-items-wrapper {
            margin-top: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .stock-tabs {
            display: flex;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .stock-tab {
            padding: 8px 20px;
            font-size: 10px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
        }
        .stock-tab.active {
            background: #fff;
            color: #008080;
            border-top: 2px solid #008080;
            margin-top: -1px;
        }
        .stock-table-container {
            padding: 0;
            min-height: 150px;
        }
        .stock-table {
            width: 100%;
            font-size: 11px;
        }
        .stock-table th {
            background: #f3f4f6;
            padding: 8px 12px;
            font-weight: 600;
            color: #4b5563;
            text-align: left;
        }
        .stock-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .stock-totals {
            padding: 15px;
            display: flex;
            gap: 30px;
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
            background: #fff;
            border-top: 1px solid #f3f4f6;
        }

        .airfreight-flight-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .airfreight-flight-row .flight-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .airfreight-flight-row .flight-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .airfreight-flight-row .flight-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-airfreight-flight-btn:hover {
            text-decoration: underline !important;
        }

        .sea-freight-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            margin-bottom: 12px;
        }
        .sea-freight-leg-row .sea-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .sea-freight-leg-row .sea-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .sea-freight-leg-row .sea-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-sea-freight-leg-btn:hover {
            text-decoration: underline !important;
        }

        .truck-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .truck-leg-row .truck-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .truck-leg-row .truck-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .truck-leg-row .truck-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-truck-leg-btn:hover {
            text-decoration: underline !important;
        }

        .courier-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .courier-leg-row .courier-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .courier-leg-row .courier-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .courier-leg-row .courier-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-courier-leg-btn:hover {
            text-decoration: underline !important;
        }

        .release-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .release-leg-row .release-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .release-leg-row .release-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .release-leg-row .release-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-release-leg-btn:hover {
            text-decoration: underline !important;
        }

        .on-board-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-bottom: 12px;
        }
        .on-board-leg-row .on-board-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .on-board-leg-row .on-board-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .on-board-leg-row .on-board-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-on-board-leg-btn:hover {
            text-decoration: underline !important;
        }

        .hand-carry-leg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            margin-bottom: 12px;
        }
        .hand-carry-leg-row .hand-carry-leg-field {
            flex: 1 1 0;
            min-width: 0;
        }
        .hand-carry-leg-row .hand-carry-leg-field-time {
            flex: 0 0 90px;
            max-width: 90px;
        }
        .hand-carry-leg-row .hand-carry-leg-checkbox {
            flex: 0 0 auto;
            min-width: 130px;
        }
        .hand-carry-leg-row .hand-carry-leg-remove-btn {
            flex: 0 0 20px;
            margin-bottom: 6px;
            line-height: 1;
        }
        #add-hand-carry-leg-btn:hover {
            text-decoration: underline !important;
        }

        /* Right Sidebar */
        .right-sidebar {
            width: 400px;
            background: #fff;
            border-left: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: relative;
            z-index: 20;
            overflow: visible;
        }
        .sidebar-section {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
            overflow: visible;
        }
        .sidebar-title {
            font-size: 10px;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .not-started {
            color: #6b7280;
            font-size: 11px;
        }
        .customer-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #008080;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .sidebar-info-item {
            position: relative;
            cursor: pointer;
        }
        .sidebar-info-item.is-active {
            color: #006666;
        }
        .sidebar-info-tooltip-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10040;
            background: rgba(15, 23, 42, 0.08);
            pointer-events: none;
        }
        .sidebar-info-tooltip-backdrop.is-visible {
            display: block;
        }
        .sidebar-info-tooltip {
            display: none;
            position: fixed;
            width: 300px;
            max-height: 70vh;
            overflow-x: hidden;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 10px 12px;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            z-index: 10050;
            color: #334155;
            font-weight: 400;
            text-align: left;
            pointer-events: auto;
            touch-action: pan-y;
        }
        .sidebar-info-tooltip.is-visible {
            display: block;
        }
        .sidebar-info-tooltip-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1b5e6f;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .sidebar-tooltip-row {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 6px 10px;
            margin-bottom: 5px;
            font-size: 10px;
            line-height: 1.35;
        }
        .sidebar-tooltip-label {
            color: #64748b;
            font-weight: 600;
        }
        .sidebar-tooltip-value {
            color: #1e293b;
            word-break: break-word;
        }
        .doc-tabs {
            display: flex;
            border-bottom: 1px solid #e8edf2;
        }
        .doc-tab {
            flex: 1;
            text-align: center;
            padding: 10px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            cursor: pointer;
        }
        .doc-tab.active {
            color: #002d5b;
            border-bottom: 2px solid #002d5b;
        }

        #doc-panel-docs .shipment-docs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-right: 4px;
        }

        #doc-panel-docs .shipment-docs-header span {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        #doc-panel-docs .shipment-docs-internal-label {
            width: 100px;
            text-align: left;
            margin-right: 26px;
        }

        #shipment-docs-scroll {
            max-height: 420px;
            overflow-y: auto;
            padding-right: 4px;
        }

        #shipment-log-scroll,
        #shipment-comments-scroll {
            max-height: 420px;
            overflow-y: auto;
            padding-right: 4px;
        }

        #doc-panel-docs .doc-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            margin: 0;
            border-bottom: 1px solid #e8edf2;
        }

        #doc-panel-docs .doc-item:last-child {
            border-bottom: 0;
        }

        #doc-panel-docs .doc-main {
            flex: 1;
            max-width: 60%;
            min-width: 50%;
            padding-top: 1px;
        }
        

        #doc-panel-docs .doc-name,
        #doc-panel-docs a.doc-name {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            color: #008080 !important;
            line-height: 16px;
            text-decoration: none;
            word-break: break-word;
            cursor: pointer;
        }

        #doc-panel-docs .doc-name:hover {
            text-decoration: underline;
        }

        #doc-panel-docs .doc-name-muted {
            color: #94a3b8 !important;
            font-weight: 600;
            font-size: 12px;
        }

        #doc-panel-docs .doc-type-select,
        #doc-panel-docs .shipment-doc-type-select {
            display: block;
            margin-top: 4px;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            padding: 0;
            max-width: 100%;
            width: auto;
            height: auto;
            cursor: pointer;
            appearance: auto;
        }

        #doc-panel-docs .doc-type-label {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
        }

        #doc-panel-docs .doc-side {
            flex: 0 0 auto;
            width: 86px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        #doc-panel-docs .doc-side-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            height: 18px;
            width: 100%;
        }

        #doc-panel-docs .doc-internal {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
        }

        #doc-panel-docs .doc-internal.checkbox-fade {
            display: inline-block;
            line-height: 0;
        }

        #doc-panel-docs .doc-internal.checkbox-fade label {
            margin: 0;
            padding: 0;
            display: inline-flex;
            align-items: center;
            line-height: 0;
        }

        #doc-panel-docs .doc-internal.checkbox-fade .cr {
            width: 13px;
            height: 13px;
            margin: 0;
            margin-top: 0;
        }

        #doc-panel-docs .doc-internal.checkbox-fade input[type="checkbox"]:disabled + .cr {
            opacity: 0.45;
            cursor: not-allowed;
            background: #f8fafc;
        }

        #doc-panel-docs .doc-trash {
            width: 18px;
            height: 18px;
            flex: 0 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
        }

        #doc-panel-docs .doc-trash:hover {
            color: #ef4444;
        }

        #doc-panel-docs .doc-date {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
            white-space: nowrap;
            text-align: right;
            min-height: 12px;
        }

        #doc-panel-docs .dropzone-placeholder,
        #doc-panel-docs .drag-drop-zone {
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 22px 16px;
            text-align: center;
            margin-top: 14px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #doc-panel-docs .dropzone-placeholder:hover,
        #doc-panel-docs .drag-drop-zone:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        #doc-panel-docs .dropzone-text,
        #doc-panel-docs .drag-drop-text {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 8px;
        }

        #doc-panel-docs .dropzone-placeholder .dropzone-icon,
        #doc-panel-docs .drag-drop-zone i {
            font-size: 18px;
            color: #94a3b8;
            display: block;
            margin: 0;
        }

        /* Buttons */
        .btn-premium {
            font-size: 11px;
            font-weight: 600;
            padding: 6px 15px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-outline-custom {
            border: 1px solid #d1d5db;
            color: #4b5563;
            background: #fff;
        }
        .btn-outline-custom:hover {
            border-color: #008080;
            color: #008080;
        }
        /* Select2 Style Overrides - Robust Reset for Background */
        .master-container .select2-container--default .select2-selection--single,
        .master-container .select2-container--default .select2-selection--multiple,
        .master-container .select2-container--default.select2-container--focus .select2-selection--single,
        .master-container .select2-container--default.select2-container--focus .select2-selection--multiple {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            height: 30px !important;
            transition: all 0.2s;
        }

        .master-container .select2-container--default .select2-selection--single:hover,
        .master-container .select2-container--default .select2-selection--multiple:hover {
            border-color: #008080 !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__rendered,
        .master-container .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #4b5563 !important;
            line-height: 28px !important;
            font-size: 11px !important;
            padding-left: 10px !important;
            background-color: transparent !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            right: 5px !important;
            background-color: transparent !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #9ca3af transparent transparent transparent !important;
        }

        .master-container .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #008080 transparent !important;
        }

        /* Multiple select choice reset if needed */
        .master-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080 !important;
            border: none !important;
            color: #fff !important;
            font-size: 10px !important;
            border-radius: 2px !important;
            padding: 1px 6px !important;
            margin-top: 4px !important;
        }

        .tab-panel, .doc-panel {
            display: none;
        }
        .tab-panel.active, .doc-panel.active {
            display: block;
        }

        .btn-save-changes {
            background-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        /* Prices / Costs Specific Styling */
        .prices-costs-header {
            display: flex;
            gap: 40px;
            margin-bottom: 25px;
            align-items: flex-end;
        }
        .header-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .header-field label {
            font-size: 11px;
            color: #618da0;
            margin: 0;
            font-weight: 500;
        }
        .header-field .field-value {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .charge-table-wrapper {
            margin-bottom: 30px;
        }
        .charge-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .charge-table th {
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .charge-table td {
            padding: 6px 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        
        .price-table-header th {
            background-color: #e2efda; /* Light green */
        }
        .cost-table-header th {
            background-color: #fff2cc; /* Light yellow */
        }

        .charge-table input, .charge-table select {
            border: 1px solid #e5e7eb;
            border-radius: 2px;
            height: 28px;
            padding: 0 8px;
            font-size: 11px;
            color: #4b5563;
        }
        .charge-table input:focus {
            border-color: #008080;
            outline: none;
        }
        
        .table-total-row {
            text-align: right;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 12px;
            color: #374151;
        }
        
        .table-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-table-action {
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            background: #fff;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }
        .btn-table-action:hover {
            border-color: #008080;
            color: #008080;
        }
        
        .icon-btn-teal {
            color: #008080;
            cursor: pointer;
            font-size: 14px;
        }
        .icon-btn-delete {
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
        }
        .icon-btn-delete:hover {
            color: #ef4444;
        }

        /* Standard Template Overrides for Master-Detail compatibility */
        .pcoded-inner-content {
            padding: 0 !important;
        }
        .pcoded-content {
            background: #f4f7f6;
        }
        .main-body .page-wrapper {
            padding: 20px 5px !important;
        }

        /* Checkbox and Radio Visibility Fix - High Contrast */
        .checkbox-fade, .radio-fade {
            display: inline-block;
        }
        .checkbox-fade label, .radio-fade label {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: flex-start; /* Better for wrapped text */
            margin-bottom: 8px; /* Default spacing */
            line-height: 1.4; /* Professional line height */
        }
        .checkbox-fade input[type="checkbox"], .radio-fade input[type="radio"] {
            display: none;
        }
        .checkbox-fade .cr, .radio-fade .cr {
            border: 2px solid #d1d5db; /* Thicker border */
            cursor: pointer;
            display: inline-block;
            height: 14px; /* Slightly larger */
            margin-right: 10px;
            position: relative;
            width: 14px;
            background: #fff;
            border-radius: 3px;
            transition: all 0.2s ease;
            flex-shrink: 0;
            margin-top: 2px; /* Align with first line of text */
        }
        .radio-fade .cr {
            border-radius: 50%;
        }
        .checkbox-fade .cr .cr-icon, .radio-fade .cr .cr-icon {
            color: #fff !important; /* White icon on dark background */
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
        .radio-fade .cr .cr-icon {
            font-size: 8px !important;
            margin-left: -4px;
            margin-top: -4px;
        }
        .checkbox-fade input[type="checkbox"]:checked + .cr,
        .radio-fade input[type="radio"]:checked + .cr {
            border-color: #008080 !important;
            background: #008080 !important; /* Solid background for high visibility */
        }
        .checkbox-fade input[type="checkbox"]:checked + .cr .cr-icon,
        .radio-fade input[type="radio"]:checked + .cr .cr-icon {
            transform: scale(1);
            opacity: 1;
        }
        .txt-primary {
            color: #008080 !important;
        }

        /* Premium Datepicker Styling */
        .ui-datepicker {
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 0;
            font-family: inherit;
            font-size: 11px;
            z-index: 9999 !important;
            width: 250px;
            border-radius: 6px;
            overflow: hidden;
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
        .ui-datepicker-prev, .ui-datepicker-next {
            cursor: pointer;
            color: #fff;
            background: rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background: rgba(255,255,255,0.2);
        }
        .ui-datepicker-title select {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 3px;
            padding: 2px 4px;
            margin: 0 2px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }
        .ui-datepicker-title select option {
            background: #fff;
            color: #374151;
            font-weight: normal;
        }
        .ui-datepicker-calendar {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        .ui-datepicker-calendar th {
            color: #6b7280;
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
            color: #374151;
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

        /* Dynamic Custom Fields Styling */
        .custom-field-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }
        .custom-field-row input {
            flex: 1;
            font-size: 11px;
        }
        .btn-remove-field {
            cursor: pointer;
            color: #4b5563;
            transition: color 0.2s;
            font-size: 14px;
            padding: 0 5px;
        }
        .btn-remove-field:hover {
            color: #ef4444;
        }

        /* Repacking Details Styling */
        .repacking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .repacking-table th {
            background-color: #f7fafc;
            color: #4b5563;
            font-weight: 600;
            text-align: left;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .repacking-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .repacking-table.table-nested th {
            background-color: #fcfdfe;
            font-size: 10px;
        }
        
        .address-section {
            margin-top: 30px;
            font-size: 11px;
        }
        .address-label {
            color: #2d5f7c;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .address-value {
            color: #1a1a1a;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .address-link {
            color: #008080;
            text-decoration: none;
            font-size: 10px;
            margin-bottom: 12px;
            display: block;
        }
        .address-grid {
            display: grid;
            grid-template-columns: 100px 100px 100px;
            gap: 20px;
        }

        /* Milestones Styling */
        .milestone-section-title {
            color: #2d5f7c;
            font-size: 13px;
            font-weight: 700;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 15px;
            margin-top: 5px;
        }
        .milestone-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 25px;
        }
        .milestone-table th {
            color: #6b7280;
            font-weight: 500;
            text-align: left;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .milestone-table td {
            padding: 8px 0;
            color: #374151;
        }

        .form-control-notes:hover, .form-control-notes:focus {
            border-color: #008080 !important;
        }

        /* More Actions Dropdown Styling */
        .dropdown-more-container {
            position: relative;
            display: inline-block;
        }
        #btn-more-actions {
            width: 36px;
            height: 36px;
            border-radius: 50% !important;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #008080;
            color: white;
            padding: 0;
        }
        .dropdown-more-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 5px;
            background-color: #fff;
            min-width: 260px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            z-index: 1000;
            padding: 8px 0;
            overflow: hidden;
        }
        .dropdown-more-menu.show {
            display: block;
        }
        .dropdown-more-item {
            padding: 12px 20px;
            color: #111827;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: block;
            text-decoration: none;
        }
        .dropdown-more-item:hover {
            background-color: #f9fafb;
            color: #008080;
        }

        /* Change Log Styling */
        .log-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .log-item:last-child {
            border-bottom: none;
        }
        .log-left {
            flex: 1;
            padding-right: 10px;
        }
        .log-right {
            text-align: right;
            flex-shrink: 0;
        }
        .log-title {
            color: #2d5f7c;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 2px;
            display: block;
        }
        .log-desc {
            color: #374151;
            font-size: 10px;
            line-height: 1.4;
        }
        .log-user {
            color: #111827;
            font-size: 10px;
            font-weight: 500;
            display: block;
        }
        .log-timestamp {
            color: #9ca3af;
            font-size: 9px;
            margin-top: 2px;
            display: block;
        }

        /* Comments Styling */
        .comment-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .comment-user {
            color: #9ca3af;
            font-size: 11px;
        }
        .comment-time {
            color: #9ca3af;
            font-size: 11px;
        }
        .comment-body {
            color: #111827;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .comment-footer-input {
            margin-top: 20px;
            padding: 10px 15px;
            border-top: 1px solid #f3f4f6;
        }
        .comment-textarea-container {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            background: #fff;
        }
        .comment-textarea {
            width: 100%;
            border: none;
            outline: none;
            font-size: 12px;
            color: #374151;
            resize: none;
            min-height: 60px;
        }
        .comment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .btn-subscribe {
            background: transparent;
            border: none;
            color: #2d5f7c;
            font-size: 11px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .btn-post {
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #9ca3af;
            padding: 5px 20px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        /* Stock items modal – match create page layout */
        #stock-items-modal .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            padding: 0 10px;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
        }
        #stock-items-modal .filter-group .filter-label {
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
        #stock-items-modal .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 !important;
            background: transparent !important;
            width: 100%;
        }
        #stock-items-modal .filter-group .select2-container--default .select2-selection--single,
        #stock-items-modal .filter-group .select2-container--default .select2-selection--multiple,
        #stock-items-modal .select2-container--default .select2-selection--single,
        #stock-items-modal .select2-container--default .select2-selection--multiple,
        #stock-items-modal .select2-container--default.select2-container--focus .select2-selection--single,
        #stock-items-modal .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: none !important;
            background-color: transparent !important;
            background: transparent !important;
            height: 30px !important;
            min-height: 30px !important;
            box-shadow: none !important;
        }
        #stock-items-modal .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered,
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__rendered,
        #stock-items-modal .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 25px !important;
            font-size: 11px !important;
            line-height: 30px !important;
            background-color: transparent !important;
            background: transparent !important;
            color: #4b5563 !important;
        }
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 5px !important;
            background-color: transparent !important;
        }
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #9ca3af transparent transparent transparent !important;
        }
        #stock-items-modal .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #008080 transparent !important;
        }
        #stock-items-modal .select2-dropdown {
            border: 1px solid #ced4da !important;
            background-color: #fff !important;
        }
        #stock-items-modal .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #008080 !important;
            color: #fff !important;
        }
        #stock-items-modal .clear-filters {
            font-size: 12px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            align-self: center;
            display: flex;
            align-items: center;
        }
        #stock-items-modal .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
            width: 100%;
            position: relative;
        }
        #stock-items-modal .office-table {
            min-width: 1500px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        #stock-items-modal .office-table thead th {
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
        }
        #stock-items-modal .office-table tbody td {
            padding: 6px 8px;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
            background-color: #fff;
        }
        #stock-items-modal .office-table tbody tr.modal-row-selected td {
            background-color: #e0f2f1 !important;
        }
        #stock-items-modal .office-table tbody tr.modal-row-selected:hover td {
            background-color: #d1ebe9 !important;
        }
        #stock-items-modal .office-table th,
        #stock-items-modal .office-table td {
            white-space: nowrap;
        }
        #stock-items-modal .office-table thead th:first-child:after,
        #stock-items-modal .office-table thead th:first-child:before {
            display: none !important;
        }
        #stock-items-modal .office-table thead th:first-child {
            padding-right: 10px !important;
        }
        #stock-items-modal .label {
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        #stock-items-modal .label-stock {
            background-color: #d4edda !important;
            color: #155724 !important;
            border: 1px solid #c3e6cb;
        }
        #stock-items-modal .label-pending {
            background-color: #ffeeba !important;
            color: #856404 !important;
            border: 1px solid #ffeeba;
        }
        #stock-items-modal .label-danger {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border: 1px solid #f5c6cb;
        }
        #stock-items-modal .label-inverse {
            background-color: #d1d5db !important;
            color: #1f2937 !important;
            border: 1px solid #9ca3af;
        }
        #stock-items-modal .landed-badge {
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
        #stock-items-modal .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        #stock-items-modal .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
            color: white;
        }
        #stock-items-modal .select2-container {
            width: 100% !important;
        }
    </style>
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

<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">
        @include('layouts.top-menu')
        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                @include('layouts.left-menu')
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <div class="main-body">
                            <div class="page-wrapper p-0">
                                <div class="page-body">
                                    <div class="master-container">
                                        <!-- Main Content Area -->
                                        <div class="main-content-area">
                                            <!-- Summary Header -->
                                            <div class="summary-header">
                                                <input type="hidden" id="shipment-current-status" value="{{ $shipment->status }}">
                                                <div class="header-meta-group">
                                                    <div class="meta-item">
                                                        <span class="meta-label">Shipment no</span>
                                                        <span class="meta-value">{{ $shipment->shipment_number }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Creation date</span>
                                                        <span class="meta-value">{{ $shipment->created_at?->format('d.m.Y') ?? '—' }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Created by</span>
                                                        <span class="meta-value text-primary">{{ $shipment->creator?->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Account manager</span>
                                                        <span class="meta-value text-primary">{{ $shipment->accountManager?->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Flags</span>
                                                        @php
                                                            $shipmentFlags = array_values(array_intersect(
                                                                $shipment->flags ?? \App\Models\Shipment::defaultFlags(),
                                                                \App\Models\Shipment::availableFlags()
                                                            ));
                                                            $selectedShipmentFlag = $shipmentFlags[0] ?? null;
                                                        @endphp
                                                        <div class="header-inline-edit" id="flags-edit-container">
                                                            <div class="header-inline-display flags-display">
                                                                <div class="flags-pills" style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                                                                    @if ($selectedShipmentFlag)
                                                                        <span class="summary-flag">{{ $selectedShipmentFlag }}</span>
                                                                    @else
                                                                        <span class="text-muted" style="font-size: 11px; font-weight: 500;">—</span>
                                                                    @endif
                                                                </div>
                                                                <i class="ti-pencil-alt" style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                            </div>
                                                            <div class="header-inline-select flags-select-wrapper">
                                                                <select class="select2-flags-inline" name="header_flags">
                                                                    <option value=""></option>
                                                                    @foreach (\App\Models\Shipment::availableFlags() as $flagOption)
                                                                        <option value="{{ $flagOption }}" {{ $selectedShipmentFlag === $flagOption ? 'selected' : '' }}>{{ $flagOption }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Status</span>
                                                        <div class="header-inline-edit" id="status-edit-container">
                                                            <div class="header-inline-display status-display">
                                                                <span class="status-badge {{ $shipment->statusBadgeClass() }}">{{ $shipment->status }}</span>
                                                                <i class="ti-pencil-alt" style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                            </div>
                                                            <div class="header-inline-select status-select-wrapper">
                                                                <select class="select2-status-inline" name="header_status">
                                                                    @foreach (['In process', 'In transit', 'Delivered', 'Completed', 'Cancelled'] as $statusOption)
                                                                        <option value="{{ $statusOption }}" {{ $shipment->status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="header-actions">
                                                    <button type="button" id="send-manifest-btn" class="btn btn-premium btn-outline-custom"
                                                        data-eml-url="{{ route('shipments.manifest-mail', $shipment->id) }}"
                                                        data-eml-filename="manifest-mail-{{ $shipment->shipment_number }}.eml">Send manifest</button>
                                                    <button type="button" id="send-prealert-btn" class="btn btn-premium btn-outline-custom">Send pre-alert</button>
                                                    <button type="button" id="finalize-shipment-btn" class="btn btn-premium btn-outline-custom">Finalize shipment</button>
                                                    <div class="dropdown-more-container d-none">
                                                        <button type="button" class="btn btn-premium btn-teal" id="btn-more-actions"><i class="ti-more-alt"></i></button>
                                                        <div class="dropdown-more-menu" id="more-actions-menu">
                                                            <a class="dropdown-more-item">Cancel shipment</a>
                                                            <a class="dropdown-more-item">Display packing list</a>
                                                            <a class="dropdown-more-item">Send all documents</a>
                                                            <a class="dropdown-more-item">Set customs reference</a>
                                                            <a class="dropdown-more-item">Generate combined PO document</a>
                                                            <a class="dropdown-more-item">View/calculate emissions</a>
                                                            <a class="dropdown-more-item">Mark as not billable</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Navigation Tabs -->
                                            <div class="custom-nav-tabs">
                                                <div class="nav-tab-item active" data-target="shipment-details">Shipment details</div>
                                                <div class="nav-tab-item" data-target="prices-costs">Prices / costs</div>
                                                <div class="nav-tab-item" data-target="customs">Customs</div>
                                                <div class="nav-tab-item" data-target="repacking-details">Repacking details</div>
                                                <div class="nav-tab-item" data-target="notes">Notes</div>
                                                <div class="nav-tab-item" data-target="milestones">Milestones</div>
                                            </div>

                                            <!-- Scrollable Form Content -->
                                            <div class="form-scroller">
                                                <!-- Shipment Details Tab -->
                                                <div id="tab-shipment-details" class="tab-panel active">
                                                <form id="shipment-edit-form" method="POST" action="{{ route('shipments.update', $shipment->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div id="crr-ids-container">
                                                        @foreach ($shipment->crrs as $crr)
                                                            <input type="hidden" name="crr_ids[]" value="{{ $crr->id }}">
                                                        @endforeach
                                                    </div>
                                                    @include('Shipment.partials.edit-shipment-details-form')
                                                </form>
                                                </div>

                                                <!-- Other Tabs (Placeholders) -->
                                                <div id="tab-prices-costs" class="tab-panel">
                                                    <div class="prices-costs-header">
                                                        <div class="header-field">
                                                            <label>Invoicing currency</label>
                                                            <div style="width: 120px;">
                                                                <select class="form-control-sm-custom select2">
                                                                    <option>EUR</option>
                                                                    <option>USD</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="header-field">
                                                            <label>Insurance suggestion</label>
                                                            <div class="field-value">
                                                                14.36 <i class="ti-layout-grid3 icon-btn-teal"></i>
                                                            </div>
                                                        </div>
                                                        <div class="header-field">
                                                            <label>Stock costs</label>
                                                            <div class="field-value">
                                                                0.00 EUR (0) <i class="ti-files icon-btn-teal"></i>
                                                            </div>
                                                        </div>
                                                        <div class="header-field">
                                                            <label>Chargeable for customer</label>
                                                            <div class="checkbox-fade fade-in-primary">
                                                                <label>
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span class="text-inverse" style="font-size: 11px;">Not chargeable</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Price Charge Table -->
                                                    <div class="charge-table-wrapper">
                                                        <table class="charge-table">
                                                            <thead class="price-table-header">
                                                                <tr>
                                                                    <th style="width: 25%;">Price charge</th>
                                                                    <th style="width: 10%;">Unit price</th>
                                                                    <th style="width: 8%;">Currency</th>
                                                                    <th style="width: 8%;">VAT rate</th>
                                                                    <th style="width: 8%;">Unit</th>
                                                                    <th style="width: 5%;">Qty</th>
                                                                    <th style="width: 10%;">Price</th>
                                                                    <th style="width: 10%;">Price EUR</th>
                                                                    <th style="width: 10%;">Invoice number</th>
                                                                    <th style="width: 10%;">Invoice date</th>
                                                                    <th style="width: 40px;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @for($i=0; $i<3; $i++)
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                                <option></option>
                                                                            </select>
                                                                            <i class="ti-comment-alt icon-btn-teal"></i>
                                                                        </div>
                                                                    </td>
                                                                    <td><input type="text" value="0.00" style="width: 100%;"></td>
                                                                    <td>
                                                                        <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option>USD</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option>0%</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option>unit</option>
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="text" value="1" style="width: 100%; text-align: center;"></td>
                                                                    <td><input type="text" value="0.00" style="width: 100%;"></td>
                                                                    <td><input type="text" value="0.00" style="width: 100%;"></td>
                                                                    <td><input type="text" style="width: 100%;"></td>
                                                                    <td><input type="text" style="width: 100%;"></td>
                                                                    <td><i class="ti-trash icon-btn-delete"></i></td>
                                                                </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                        <div class="table-total-row">
                                                            Total EUR <span style="margin-left: 100px;">0.00</span>
                                                        </div>
                                                        <div class="table-actions">
                                                            <button class="btn btn-table-action">Copy from cost</button>
                                                            <button class="btn btn-table-action" style="color: #008080; border-color: #008080;">Add price</button>
                                                        </div>
                                                    </div>

                                                    <!-- Cost Charge Table -->
                                                    <div class="charge-table-wrapper">
                                                        <table class="charge-table">
                                                            <thead class="cost-table-header">
                                                                <tr>
                                                                    <th style="width: 20%;">Cost charge</th>
                                                                    <th style="width: 8%;">Currency</th>
                                                                    <th style="width: 10%;">Est. net value</th>
                                                                    <th style="width: 10%;">Net value</th>
                                                                    <th style="width: 5%;">Qty</th>
                                                                    <th style="width: 10%;">Out. invoice no</th>
                                                                    <th style="width: 10%;">Unit cost</th>
                                                                    <th style="width: 8%;">Unit</th>
                                                                    <th style="width: 10%;">Inc. invoice no</th>
                                                                    <th style="width: 100px;">Hub/agent/office</th>
                                                                    <th style="width: 10%;">Remarks</th>
                                                                    <th style="width: 30px;"><i class="ti-check"></i></th>
                                                                    <th style="width: 30px;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @for($i=0; $i<3; $i++)
                                                                <tr>
                                                                    <td>
                                                                        <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option></option>
                                                                        </select>
                                                                    </td>
                                                                    <td><select class="form-control-sm-custom select2" style="width: 100%;"><option>USD</option></select></td>
                                                                    <td><input type="text" value="0.00" style="width: 100%;"></td>
                                                                    <td><input type="text" style="width: 100%;"></td>
                                                                    <td><input type="text" value="1" style="width: 100%; text-align: center;"></td>
                                                                    <td>
                                                                        <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option></option>
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="text" value="0.00" style="width: 100%;"></td>
                                                                    <td>
                                                                         <select class="form-control-sm-custom select2" style="width: 100%;">
                                                                            <option></option>
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="text" style="width: 100%;"></td>
                                                                    <td style="text-align: center;"><i class="ti-layout-grid2 icon-btn-teal"></i></td>
                                                                    <td><input type="text" style="width: 100%;"></td>
                                                                    <td>
                                                                        <div class="checkbox-fade fade-in-primary">
                                                                            <label>
                                                                                <input type="checkbox">
                                                                                <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td><i class="ti-trash icon-btn-delete"></i></td>
                                                                </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                        <div class="table-total-row">
                                                            Total USD <span style="margin-left: 100px;">0.00</span>
                                                        </div>
                                                        <div class="table-actions" style="margin-top: 20px;">
                                                            <button class="btn btn-premium btn-teal" style="background: #2d5f7c; padding: 10px 30px;">Save changes</button>
                                                            <button class="btn btn-link text-primary" style="font-size: 11px; font-weight: 600;">Cancel</button>
                                                            <div class="ml-auto d-flex gap-2">
                                                                <button class="btn-table-action">Apply template</button>
                                                                <button class="btn-table-action" style="color: #008080; border-color: #008080;">Add cost</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="tab-customs" class="tab-panel">
                                                    <div class="row">
                                                        <!-- Column 1: Entity Details -->
                                                        <div class="col-md-4">
                                                            <div class="form-group-custom">
                                                                <label>Shipper</label>
                                                                <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                <textarea class="form-control mt-1" style="font-size: 11px; height: 100px;"></textarea>
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label>Consignee</label>
                                                                <textarea class="form-control" style="font-size: 11px; height: 100px;">Marinetrans Benelux B.V - Amsterdam Hub&#10;Changiweg 14&#10;1437 EP Rozenburg&#10;Netherlands</textarea>
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label>Details</label>
                                                                <select class="form-control-sm-custom select2">
                                                                    <option></option>
                                                                </select>
                                                                <textarea class="form-control mt-1" style="font-size: 11px; height: 60px;"></textarea>
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label>Additional details</label>
                                                                <select class="form-control-sm-custom select2">
                                                                    <option></option>
                                                                </select>
                                                                <textarea class="form-control mt-1" style="font-size: 11px; height: 60px;"></textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Column 2: Document Controls -->
                                                        <div class="col-md-4">
                                                            <div class="form-group-custom">
                                                                <label>Document title</label>
                                                                <select class="form-control-sm-custom select2">
                                                                    <option></option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group-custom">
                                                                <label style="display: block;">Grouping</label>
                                                                <div class="radio-fade fade-in-primary d-flex flex-column gap-3 mt-1">
                                                                    <label style="font-size: 11px;">
                                                                        <input type="radio" name="grouping" checked>
                                                                        <span class="cr"><i class="cr-icon ti-target txt-primary"></i></span>
                                                                        <span>One document per stock item</span>
                                                                    </label>
                                                                    <label style="font-size: 11px;">
                                                                        <input type="radio" name="grouping">
                                                                        <span class="cr"><i class="cr-icon ti-target txt-primary"></i></span>
                                                                        <span>Combined</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="checkbox-fade fade-in-primary d-flex flex-column gap-3 mt-3 mb-4">
                                                                <label style="font-size: 11px;">
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span>Include country of origin</span>
                                                                </label>
                                                                <label style="font-size: 11px;">
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span>Include purchase order numbers</span>
                                                                </label>
                                                                <label style="font-size: 11px;">
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span>Include vessel name</span>
                                                                </label>
                                                                <label style="font-size: 11px;">
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span>Include invoice number</span>
                                                                </label>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Document date</label>
                                                                        <div class="input-with-icon">
                                                                            <input type="text" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                                                            <i class="ti-calendar"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Payment terms</label>
                                                                        <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Port of origin</label>
                                                                        <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Port of destination</label>
                                                                        <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Service reference</label>
                                                                        <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>INCO terms</label>
                                                                        <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="custom-fields-container mt-4">
                                                                <label style="display: block; margin-bottom: 5px;">Custom fields</label>
                                                                <div id="custom-fields-list">
                                                                    <!-- Dynamic rows will be appended here -->
                                                                </div>
                                                            </div>
                                                            <button type="button" id="btn-add-custom-field" class="btn btn-premium btn-outline-custom mt-2">Add custom field</button>
                                                        </div>

                                                        <!-- Column 3: Financials & Packing -->
                                                        <div class="col-md-4">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Currency</label>
                                                                        <select class="form-control-sm-custom select2">
                                                                            <option>USD</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group-custom">
                                                                        <label>Exchange rates date</label>
                                                                        <div class="input-with-icon">
                                                                            <input type="text" class="form-control-sm-custom datepicker" value="15.03.2026">
                                                                            <i class="ti-calendar"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group-custom mt-2">
                                                                <label>Freight costs</label>
                                                                <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                                <div class="checkbox-fade fade-in-primary mt-1">
                                                                    <label style="font-size: 11px;">
                                                                        <input type="checkbox">
                                                                        <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        <span>Divide on each stock item based on weight</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group-custom mt-2">
                                                                <label>Insurance costs percentage</label>
                                                                <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                            </div>
                                                            <div class="checkbox-fade fade-in-primary mt-3">
                                                                <label style="font-size: 11px;">
                                                                    <input type="checkbox">
                                                                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                    <span>Packing list should be in a separate file</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-group-custom mt-3">
                                                                <label>Packed as</label>
                                                                <input type="text" class="form-control-sm-custom" style="width: 100%;">
                                                            </div>
                                                            <div class="form-group-custom mt-2">
                                                                <label>Use dimensions from</label>
                                                                <select class="form-control-sm-custom select2">
                                                                    <option></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Bottom Action -->
                                                    <div class="mt-5 mb-3">
                                                        <button class="btn btn-premium btn-save-changes">All changes saved</button>
                                                    </div>
                                                </div>
                                                <div id="tab-repacking-details" class="tab-panel">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <!-- Table 1: Stock Details -->
                                                            <table class="repacking-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 25%;">Stock no</th>
                                                                        <th style="width: 25%;">Label</th>
                                                                        <th style="width: 15%;">Colli no</th>
                                                                        <th style="width: 35%;">T1-Reference (bonded)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>SHA30-61690470</td>
                                                                        <td>MTL-61690470-1</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <!-- Table 2: Packing Details -->
                                                            <table class="repacking-table table-nested">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 15%;">Colli no</th>
                                                                        <th style="width: 15%;"></th>
                                                                        <th style="width: 17.5%; text-align: right;">Length</th>
                                                                        <th style="width: 17.5%; text-align: right;">Width</th>
                                                                        <th style="width: 17.5%; text-align: right;">Height</th>
                                                                        <th style="width: 17.5%; text-align: right;">Weight</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Table content if any -->
                                                                </tbody>
                                                            </table>

                                                            <!-- Consignee Address Section -->
                                                            <div class="address-section">
                                                                <div class="address-label">Consignee name</div>
                                                                <div class="address-value">Marinetrans Benelux B.V - Amsterdam Hub</div>
                                                                
                                                                <a href="#" class="address-link">Delivery address shown on shipment label</a>

                                                                <div class="address-value" style="font-weight: 700; font-size: 13px;">Changiweg 14</div>
                                                                
                                                                <div class="address-grid mt-3">
                                                                    <div>
                                                                        <div class="address-label" style="font-size: 10px;">City</div>
                                                                        <div class="address-value">Rozenburg</div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="address-label" style="font-size: 10px;">District / state</div>
                                                                        <div class="address-value"></div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="address-label" style="font-size: 10px;">Zip code</div>
                                                                        <div class="address-value">1437 EP</div>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-3">
                                                                    <div class="address-label" style="font-size: 10px;">Country</div>
                                                                    <div class="address-value">Netherlands</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="tab-notes" class="tab-panel">
                                                    <div class="p-3">
                                                        <textarea class="form-control-notes" placeholder="Write your note here..." rows="4" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; font-size: 11px; outline: none; transition: border-color 0.2s;"></textarea>
                                                        <button type="button" class="btn btn-premium btn-teal mt-3" style="background: #2d5f7c; padding: 10px 30px;">Add note</button>
                                                    </div>
                                                </div>
                                                <div id="tab-milestones" class="tab-panel">
                                                    <div class="p-3">
                                                        <div class="row">
                                                            <!-- Left Column: Milestones & Events -->
                                                            <div class="col-md-7" style="border-right: 1px solid #f3f4f6; padding-right: 25px;">
                                                                <div class="milestone-section-title">Milestones</div>
                                                                <table class="milestone-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 25%;">Occurrence</th>
                                                                            <th style="width: 25%;">Milestone</th>
                                                                            <th style="width: 25%;">Received</th>
                                                                            <th style="width: 25%;">Source</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data rows would go here -->
                                                                    </tbody>
                                                                </table>

                                                                <div class="milestone-section-title">Events</div>
                                                                <table class="milestone-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 25%;">Received</th>
                                                                            <th style="width: 50%;">Event</th>
                                                                            <th style="width: 25%;">Source</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data rows would go here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <!-- Right Column: Shipment Information -->
                                                            <div class="col-md-5" style="padding-left: 25px;">
                                                                <div class="milestone-section-title">Shipment information</div>
                                                                <table class="milestone-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 40%;">Field</th>
                                                                            <th style="width: 60%;">Values</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data rows would go here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="edit-footer">
                                                <button type="submit" form="shipment-edit-form" id="shipment-save-changes-btn" class="btn-save-custom" disabled>Save changes</button>
                                                <a href="{{ route('shipments') }}" class="btn-cancel-custom">Cancel</a>
                                            </div>
                                        </div>

                                        <!-- Right Sidebar -->
                                        <div class="right-sidebar">
                                            <div class="sidebar-section">
                                                <div class="sidebar-title">Hub/agent status</div>
                                                <div class="not-started">Not started</div>
                                            </div>
                                            <div class="sidebar-section">
                                                <div class="sidebar-title">Customers details</div>
                                                @php
                                                    $sidebarCustomers = $shipment->crrs
                                                        ->map(fn ($crr) => $crr->customerVessel?->customer)
                                                        ->filter()
                                                        ->unique('id')
                                                        ->values();

                                                    $sidebarVessels = $shipment->crrs
                                                        ->map(function ($crr) {
                                                            return [
                                                                'display_name' => $crr->vessel_name ?: $crr->customerVessel?->vessel,
                                                                'vessel' => $crr->customerVessel,
                                                            ];
                                                        })
                                                        ->filter(fn ($item) => !empty($item['display_name']))
                                                        ->unique('display_name')
                                                        ->values();
                                                @endphp
                                                @forelse ($sidebarCustomers as $customer)
                                                    <div class="customer-item sidebar-info-item" tabindex="0">
                                                        @include('Shipment.partials.sidebar-customer-tooltip', ['customer' => $customer])
                                                        <i class="ti-anchor"></i>
                                                        <span>{{ $customer->customer_name }}</span>
                                                    </div>
                                                @empty
                                                    <div class="customer-item text-muted">No customer linked</div>
                                                @endforelse
                                                @foreach ($sidebarVessels as $vesselItem)
                                                    <div class="customer-item sidebar-info-item" tabindex="0">
                                                        @include('Shipment.partials.sidebar-vessel-tooltip', [
                                                            'vessel' => $vesselItem['vessel'],
                                                            'displayName' => $vesselItem['display_name'],
                                                        ])
                                                        <i class="icofont icofont-ship"></i>
                                                        <span>{{ $vesselItem['display_name'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="sidebar-section p-0">
                                                <div class="doc-tabs">
                                                    @php
                                                        $shipmentDocumentCount = ($combinedPoDocuments->count() > 0 ? 1 : 0) + $shipment->manifests->count() + $shipment->preAlerts->count() + $shipment->documents->count();
                                                    @endphp
                                                    <div class="doc-tab active" data-target="docs" id="documents-tab-label">Documents ({{ $shipmentDocumentCount }})</div>
                                                    <div class="doc-tab" data-target="log">Change log</div>
                                                    <div class="doc-tab" data-target="comments">Comments</div>
                                                </div>
                                                <div class="p-3">
                                                    <div id="doc-panel-docs" class="doc-panel active">
                                                        <div class="shipment-docs-header">
                                                            <span>Filename</span>
                                                            <span class="shipment-docs-internal-label">Internal</span>
                                                        </div>
                                                        <div id="shipment-docs-scroll">
                                                        @if ($combinedPoDocuments->count() > 0)
                                                        <div class="doc-item">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ route('shipments.combined-po-documents', $shipment->id) }}"
                                                                   data-title="Combined PO documents">
                                                                    Combined PO documents
                                                                </a>
                                                                <span class="doc-type-label">PO · {{ $combinedPoDocuments->unique('file_path')->count() }} PDF(s) merged</span>
                                                            </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox" class="doc-internal-check" >
                                                                            <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        </label>
                                                                    </div>
                                                                    <i class="ti-trash doc-trash" style="visibility: hidden;"></i>
                                                                </div>
                                                                <span class="doc-date">{{ $combinedPoDocuments->max('created_at')?->format('d.m.Y') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div id="shipment-manifests-list">
                                                        @foreach ($shipment->manifests as $manifest)
                                                        <div class="doc-item shipment-manifest-doc" data-id="{{ $manifest->id }}">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ route('shipments.manifests.show', [$shipment->id, $manifest->id]) }}"
                                                                   data-title="{{ $manifest->displayLabel() }}">
                                                                    {{ $manifest->displayLabel() }}
                                                                </a>
                                                                <span class="doc-type-label">Manifest</span>
                                                            </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox" class="doc-internal-check">
                                                                            <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        </label>
                                                                    </div>
                                                                    <i class="ti-trash doc-trash delete-shipment-manifest" data-id="{{ $manifest->id }}" title="Delete"></i>
                                                                </div>
                                                                <span class="doc-date">{{ $manifest->created_at->format('d.m.Y') }}</span>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        </div>
                                                        <div id="shipment-pre-alerts-list">
                                                        @foreach ($shipment->preAlerts as $preAlert)
                                                        <div class="doc-item shipment-prealert-doc" data-id="{{ $preAlert->id }}">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ route('shipments.pre-alerts.show', [$shipment->id, $preAlert->id]) }}"
                                                                   data-title="{{ $preAlert->displayLabel() }}">
                                                                    {{ $preAlert->displayLabel() }}
                                                                </a>
                                                                <span class="doc-type-label">Pre-alert</span>
                                                            </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox" class="doc-internal-check" disabled title="Generated document">
                                                                            <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        </label>
                                                                    </div>
                                                                    <i class="ti-trash doc-trash delete-shipment-prealert" data-id="{{ $preAlert->id }}" title="Delete"></i>
                                                                </div>
                                                                <span class="doc-date">{{ $preAlert->created_at->format('d.m.Y') }}</span>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        </div>
                                                        <div id="shipment-documents-list">
                                                        @foreach ($shipment->documents as $uploadedDocument)
                                                            @php
                                                                $selectedDocType = $uploadedDocument->file_type ?: 'Unspecified';
                                                            @endphp
                                                        <div class="doc-item shipment-uploaded-doc" data-id="{{ $uploadedDocument->id }}">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ $uploadedDocument->fileUrl() }}"
                                                                   data-title="{{ $uploadedDocument->file_name }}">
                                                                    {{ $uploadedDocument->file_name }}
                                                                </a>
                                                                <select class="doc-type-select shipment-doc-type-select" data-doc-id="{{ $uploadedDocument->id }}">
                                                                    @foreach ($shipmentDocumentTypeOptions as $documentTypeOption)
                                                                        <option value="{{ $documentTypeOption }}" {{ $selectedDocType === $documentTypeOption ? 'selected' : '' }}>{{ $documentTypeOption }}</option>
                                                                    @endforeach
                                                                    @if (! in_array($selectedDocType, $shipmentDocumentTypeOptions, true))
                                                                        <option value="{{ $selectedDocType }}" selected>{{ $selectedDocType }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox"
                                                                                   class="doc-internal-check shipment-doc-attach-checkbox shipment-doc-internal-check"
                                                                                   data-doc-id="{{ $uploadedDocument->id }}"
                                                                                   {{ $uploadedDocument->is_internal ? 'checked' : '' }}>
                                                                            <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        </label>
                                                                    </div>
                                                                    <i class="ti-trash doc-trash delete-shipment-document" data-id="{{ $uploadedDocument->id }}" title="Delete"></i>
                                                                </div>
                                                                <span class="doc-date">{{ $uploadedDocument->created_at->format('d.m.Y') }}</span>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        </div>
                                                        </div>

                                                    <div class="dropzone-placeholder drag-drop-zone" id="shipment-doc-dropzone">
                                                        <div class="dropzone-text drag-drop-text">Drag files here or click to browse</div>
                                                        <i class="ti-upload dropzone-icon"></i>
                                                    </div>
                                                    <input type="file" id="shipment-doc-file-input" accept="application/pdf,.pdf" multiple style="display: none;">
                                                    </div>
                                                    <div id="doc-panel-log" class="doc-panel">
                                                        <div id="shipment-log-scroll">
                                                        @forelse ($shipment->changeLogs as $changeLog)
                                                            <div class="log-item">
                                                                <div class="log-left">
                                                                    <span class="log-title">{{ $changeLog->title }}</span>
                                                                    @if ($changeLog->description)
                                                                        <span class="log-desc @if(str_starts_with($changeLog->title, 'Revision')) text-danger @endif" @if(str_starts_with($changeLog->title, 'Revision')) style="font-weight: 700;" @endif>{{ $changeLog->description }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="log-right">
                                                                    <span class="log-user">{{ $changeLog->user?->name ?? 'System' }}</span>
                                                                    <span class="log-timestamp">{{ $changeLog->created_at->format('d.m.Y H:i') }}</span>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-center py-4 text-muted" style="font-size: 12px;">No changes recorded yet.</div>
                                                        @endforelse
                                                        </div>
                                                    </div>
                                                    <div id="doc-panel-comments" class="doc-panel">
                                                        <div id="shipment-comments-scroll">
                                                        <div class="comment-item">
                                                            <div class="comment-header">
                                                                <span class="comment-user">Helena Kaya</span>
                                                                <span class="comment-time">13.03.2026 21:57</span>
                                                            </div>
                                                            <div class="comment-body">WEEK 12 CHECKEN</div>
                                                        </div>
                                                        </div>
                                                        
                                                        <!-- Comment Input Footer -->
                                                        <div class="comment-footer-input">
                                                            <div class="comment-textarea-container">
                                                                <textarea class="comment-textarea" placeholder="Add a comment..."></textarea>
                                                            </div>
                                                            <div class="comment-actions">
                                                                <div style="width: 100px;">
                                                                    <select class="form-control-sm border-0 bg-transparent p-0" style="font-size: 11px; cursor: pointer;">
                                                                        <option>None</option>
                                                                        <option>Show on Pre-alert reminders list</option>
                                                                    </select>
                                                                </div>
                                                                <button type="button" class="btn-subscribe">
                                                                    <i class="ti-bell"></i> Subscribe
                                                                </button>
                                                                <button type="button" class="btn-post">Post</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Shipment.partials.stock-items-modal')

<div class="modal fade" id="finalize-shipment-choice-modal" tabindex="-1" role="dialog" aria-labelledby="finalizeShipmentChoiceModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" role="document" style="max-width: 360px;">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="finalizeShipmentChoiceModalLabel" style="font-size: 14px; font-weight: 600;">Finalize shipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px 16px;">
                <p class="mb-0" style="font-size: 12px; color: #4b5563;">Choose how you want to finalize this shipment.</p>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-premium btn-teal btn-sm" id="finalize-shipment-complete-btn">Complete</button>
                <button type="button" class="btn btn-premium btn-outline-custom btn-sm" id="finalize-shipment-transit-btn" @disabled($shipment->status !== 'Completed') title="{{ $shipment->status !== 'Completed' ? 'Complete the shipment first' : '' }}">Transit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="finalize-shipment-transit-modal" tabindex="-1" role="dialog" aria-labelledby="finalizeShipmentTransitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="finalizeShipmentTransitModalLabel" style="font-size: 14px; font-weight: 600;">Transit shipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <div class="form-group-custom mb-3">
                    <label>Shipment number</label>
                    <input type="text" id="finalize-shipment-number" class="form-control-sm-custom" value="{{ $shipment->shipment_number }}" readonly>
                </div>
                <div class="form-group-custom mb-0">
                    <label>Consignee code</label>
                    <input type="text" id="finalize-consignee-code" class="form-control-sm-custom" value="{{ $consigneeCode ?? '' }}" placeholder="Enter consignee code">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-premium btn-teal btn-sm" id="finalize-shipment-submit-btn">Submit</button>
            </div>
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
            <div class="modal-body p-0" style="height: 80vh; background: #f3f4f6;">
                <iframe id="pdf-preview-frame" title="PDF preview" style="width: 100%; height: 100%; border: 0;" src=""></iframe>
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
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
<!-- modernizr js -->
<script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/modernizr/feature-detects/css-scrollbars.js') }}"></script>

<!-- i18next.min.js -->
<script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>

<!-- Custom js -->
<script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
<script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
<script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>

<!-- Select 2 js -->
<script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

<script>
    $(document).ready(function() {
        function fixedFooterOffset() {
            var $navbar = $('.pcoded-navbar');
            var sidebarWidth = $navbar.length ? $navbar.outerWidth() : 0;
            $('.edit-footer').css('left', sidebarWidth + 'px');
        }
        fixedFooterOffset();
        $(window).on('resize', fixedFooterOffset);

        var serverErrors = @json($errors->all());
        var serverErrorMessage = @json(session('error'));

        if (serverErrors.length || serverErrorMessage) {
            var message = serverErrorMessage || serverErrors[0] || 'Please check the form and try again.';

            if (typeof swal === 'function') {
                swal({
                    title: 'Validation error',
                    text: message,
                    type: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(message);
            }
        }

        var $sidebarTooltipBackdrop = $('<div id="sidebar-info-tooltip-backdrop" class="sidebar-info-tooltip-backdrop"></div>');
        $('body').append($sidebarTooltipBackdrop);

        var $activeSidebarItem = null;

        function getSidebarTooltip($item) {
            return $item.data('floating-tooltip') || $item.find('.sidebar-info-tooltip').first();
        }

        function returnSidebarTooltipToItem($item) {
            var $tooltip = $item.data('floating-tooltip');
            if (!$tooltip || !$tooltip.length) {
                return;
            }

            $tooltip.removeClass('is-visible').css({
                left: '',
                top: '',
                maxHeight: '',
                width: ''
            }).scrollTop(0);

            $item.append($tooltip);
            $item.removeData('floating-tooltip');
        }

        function hideSidebarInfoTooltip($item) {
            if (!$item || !$item.length) {
                return;
            }

            returnSidebarTooltipToItem($item);
            $item.removeClass('is-active');
        }

        function closeSidebarInfoTooltip() {
            if ($activeSidebarItem) {
                hideSidebarInfoTooltip($activeSidebarItem);
                $activeSidebarItem = null;
            }

            $sidebarTooltipBackdrop.removeClass('is-visible');
        }

        function positionSidebarInfoTooltip($item, $tooltip) {
            if (!$tooltip || !$tooltip.length) {
                return;
            }

            var margin = 12;
            var gap = 12;
            var maxHeight = Math.max(220, Math.min(Math.floor(window.innerHeight * 0.7), window.innerHeight - (margin * 2)));

            $tooltip.addClass('is-visible').css({
                left: '-9999px',
                top: '0',
                width: '300px',
                maxHeight: maxHeight + 'px'
            });

            var itemRect = $item[0].getBoundingClientRect();
            var tipWidth = $tooltip.outerWidth();
            var tipHeight = $tooltip.outerHeight();
            var left = itemRect.left - tipWidth - gap;

            if (left < margin) {
                left = Math.max(margin, itemRect.right + gap);
            }

            if (left + tipWidth > window.innerWidth - margin) {
                left = Math.max(margin, window.innerWidth - tipWidth - margin);
            }

            var top = itemRect.top;
            if (top + tipHeight > window.innerHeight - margin) {
                top = Math.max(margin, window.innerHeight - tipHeight - margin);
            }
            top = Math.max(margin, top);

            $tooltip.css({
                left: left + 'px',
                top: top + 'px',
                maxHeight: maxHeight + 'px'
            });
        }

        function openSidebarInfoTooltip($item) {
            closeSidebarInfoTooltip();

            var $tooltip = $item.find('.sidebar-info-tooltip').first().detach();
            $item.data('floating-tooltip', $tooltip);
            $('body').append($tooltip);

            positionSidebarInfoTooltip($item, $tooltip);

            $item.addClass('is-active');
            $activeSidebarItem = $item;
            $sidebarTooltipBackdrop.addClass('is-visible');
        }

        $('.sidebar-info-item').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $item = $(this);

            if ($activeSidebarItem && $activeSidebarItem[0] === this) {
                closeSidebarInfoTooltip();
                return;
            }

            openSidebarInfoTooltip($item);
        });

        $(document).on('click.sidebarTooltip', function(e) {
            if (!$activeSidebarItem) {
                return;
            }

            var $tooltip = getSidebarTooltip($activeSidebarItem);
            if ($(e.target).closest('.sidebar-info-tooltip, .sidebar-info-item').length) {
                return;
            }

            closeSidebarInfoTooltip();
        });

        $(document).on('wheel.sidebarTooltip touchmove.sidebarTooltip', '.sidebar-info-tooltip', function(e) {
            e.stopPropagation();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebarInfoTooltip();
            }
        });

        $(window).on('resize', function() {
            if ($activeSidebarItem) {
                positionSidebarInfoTooltip($activeSidebarItem, getSidebarTooltip($activeSidebarItem));
            }
        });

        // Initialize Select2
        $('.select2').each(function() {
            if ($(this).hasClass('select2-country')) {
                return;
            }
            $(this).select2({
                placeholder: "Select",
                allowClear: false,
                width: '100%'
            });
        });

        function formatCountry(state) {
            if (!state.id) {
                return state.text;
            }
            var flagUrl = $(state.element).data('flag');
            if (!flagUrl) {
                return state.text;
            }
            return $(
                '<span><img src="' + flagUrl + '" class="img-flag" style="width: 20px; height: 15px; margin-right: 8px; vertical-align: middle;" /> ' + state.text + '</span>'
            );
        }

        $('.select2-country').select2({
            placeholder: 'Select country',
            allowClear: false,
            width: '100%',
            templateResult: formatCountry,
            templateSelection: formatCountry
        });
        
        // Initialize Datepickers
        $('.datepicker').each(function() {
            $(this).datepicker({
                dateFormat: 'dd.mm.yy',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-10:c+10'
            });
        });

        // Trigger datepicker on icon click
        $(document).on('click', '.input-with-icon i.ti-calendar', function() {
            $(this).siblings('input.datepicker').focus();
        });

        // Dynamic Custom Fields Logic
        $('#btn-add-custom-field').on('click', function() {
            var newRow = `
                <div class="custom-field-row">
                    <input type="text" class="form-control-sm-custom" placeholder="Label">
                    <input type="text" class="form-control-sm-custom" placeholder="Value">
                    <i class="ti-close btn-remove-field" title="Remove field"></i>
                </div>`;
            $('#custom-fields-list').append(newRow);
        });

        $(document).on('click', '.btn-remove-field', function() {
            $(this).closest('.custom-field-row').remove();
        });

        // More Actions Dropdown Toggle
        $('#btn-more-actions').on('click', function(e) {
            e.stopPropagation();
            $('#more-actions-menu').toggleClass('show');
        });

        $(document).on('click', function() {
            $('#more-actions-menu').removeClass('show');
        });

        $('#more-actions-menu').on('click', function(e) {
            e.stopPropagation();
        });

        var manifestMailPreview = @json($manifestMailPreview);
        var preAlertMailPreview = @json($preAlertMailPreview ?? null);
        var manifestPrepareUrl = @json(route('shipments.manifest-mail.prepare', $shipment->id));
        var preAlertPrepareUrl = @json(route('shipments.pre-alert-mail.prepare', $shipment->id));
        var manifestDeleteUrlTemplate = @json(route('shipments.manifests.delete', [$shipment->id, '__MANIFEST__']));
        var preAlertDeleteUrlTemplate = @json(route('shipments.pre-alerts.delete', [$shipment->id, '__PREALERT__']));
        var finalizeShipmentUrl = @json(route('shipments.finalize', $shipment->id));
        var shipmentsListUrl = @json(route('shipments'));
        var combinedPoDocumentCount = {{ $combinedPoDocuments->count() > 0 ? 1 : 0 }};
        var uploadedDocumentCount = {{ $shipment->documents->count() }};
        var preAlertDocumentCount = {{ $shipment->preAlerts->count() }};

        function updateDocumentsTabCount(totalCount) {
            $('#documents-tab-label').text('Documents (' + totalCount + ')');
        }

        function serializeShipmentFormForAjax() {
            return $('#shipment-edit-form').serializeArray()
                .filter(function(field) { return field.name !== '_method'; })
                .map(function(field) {
                    return encodeURIComponent(field.name) + '=' + encodeURIComponent(field.value);
                })
                .join('&');
        }

        function prependShipmentChangeLog(log) {
            if (!log) {
                return;
            }

            var $scroll = $('#shipment-log-scroll');
            $scroll.find('.text-muted').filter(function() {
                return $.trim($(this).text()) === 'No changes recorded yet.';
            }).remove();

            var html =
                '<div class="log-item">' +
                    '<div class="log-left">' +
                        '<span class="log-title">' + $('<div>').text(log.title || '').html() + '</span>' +
                        (log.description
                            ? '<span class="log-desc">' + $('<div>').text(log.description).html() + '</span>'
                            : '') +
                    '</div>' +
                    '<div class="log-right">' +
                        '<span class="log-user">' + $('<div>').text(log.user || 'System').html() + '</span>' +
                        '<span class="log-timestamp">' + $('<div>').text(log.timestamp || '').html() + '</span>' +
                    '</div>' +
                '</div>';

            $scroll.prepend(html);
        }

        $(document).on('click', '.delete-shipment-manifest', function() {
            var manifestId = $(this).data('id');
            if (!manifestId || !confirm('Delete this manifest version?')) {
                return;
            }

            $.ajax({
                url: manifestDeleteUrlTemplate.replace('__MANIFEST__', manifestId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(response) {
                $('.shipment-manifest-doc[data-id="' + manifestId + '"]').remove();
                updateShipmentDocumentTabCount();
                prependShipmentChangeLog(response && response.change_log);
            });
        });

        $(document).on('click', '.delete-shipment-prealert', function() {
            var preAlertId = $(this).data('id');
            if (!preAlertId || !confirm('Delete this pre-alert version?')) {
                return;
            }

            $.ajax({
                url: preAlertDeleteUrlTemplate.replace('__PREALERT__', preAlertId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(response) {
                $('.shipment-prealert-doc[data-id="' + preAlertId + '"]').remove();
                preAlertDocumentCount = $('.shipment-prealert-doc').length;
                updateShipmentDocumentTabCount();
                prependShipmentChangeLog(response && response.change_log);
            });
        });

        @php
            $latestManifest = $shipment->manifests->sortByDesc('version')->first();
            $manifestCoreAttachmentSources = [];
            if ($latestManifest) {
                $manifestCoreAttachmentSources[] = [
                    'url' => route('shipments.manifests.show', [$shipment->id, $latestManifest->id]),
                    'filename' => $latestManifest->displayLabel() . '-' . $shipment->shipment_number . '.pdf',
                ];
            } elseif ($shipment->crrs->isNotEmpty()) {
                $manifestCoreAttachmentSources[] = [
                    'url' => route('shipments.combined-manifest-documents', $shipment->id),
                    'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
                ];
            }
            if ($combinedPoDocuments->count() > 0) {
                $manifestCoreAttachmentSources[] = [
                    'url' => route('shipments.combined-po-documents', $shipment->id),
                    'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
                ];
            }
        @endphp
        var manifestCoreAttachmentSources = @json($manifestCoreAttachmentSources);

        function getCheckedUploadedDocumentAttachments() {
            var attachments = [];

            $('#shipment-documents-list .shipment-uploaded-doc').each(function() {
                var $row = $(this);
                if (!$row.find('.shipment-doc-attach-checkbox').prop('checked')) {
                    return;
                }

                var $link = $row.find('.po-document-link');
                var docId = $row.data('id');
                var url = $link.data('pdf-url');
                var filename = $link.data('title') || $.trim($link.text());

                if (!docId || !url || !filename) {
                    return;
                }

                attachments.push({
                    url: url,
                    filename: filename,
                    document_id: docId
                });
            });

            return attachments;
        }

        function getCheckedUploadedDocumentIds() {
            return getCheckedUploadedDocumentAttachments().map(function(source) {
                return source.document_id;
            });
        }

        function buildMailAttachmentSources(coreSources) {
            return (coreSources || []).concat(getCheckedUploadedDocumentAttachments());
        }

        function buildMailEmlUrl(baseUrl, documentIds) {
            if (!baseUrl) {
                return baseUrl;
            }

            if (!documentIds || !documentIds.length) {
                return baseUrl;
            }

            var separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';

            return baseUrl + separator + 'document_ids=' + encodeURIComponent(documentIds.join(','));
        }

        function openShipmentMailto(preview) {
            if (!preview) {
                return;
            }

            var params = [];
            if (preview.to) {
                params.push('to=' + encodeURIComponent(preview.to));
            }
            if (preview.cc) {
                params.push('cc=' + encodeURIComponent(preview.cc));
            }
            if (preview.subject) {
                params.push('subject=' + encodeURIComponent(preview.subject));
            }
            if (preview.body) {
                var body = preview.body;
                if (body.length > 1800) {
                    body = body.substring(0, 1800) + '...';
                }
                params.push('body=' + encodeURIComponent(body));
            }

            if (!params.length) {
                return;
            }

            var link = document.createElement('a');
            link.href = 'mailto:?' + params.join('&');
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function buildMailOpenUrl(baseUrl, documentIds) {
            if (!baseUrl) {
                return baseUrl;
            }

            if (!documentIds || !documentIds.length) {
                return baseUrl;
            }

            var separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';

            return baseUrl + separator + 'document_ids=' + encodeURIComponent(documentIds.join(','));
        }

        function openMailDraftWindow() {
            var win = window.open('', '_blank');
            if (!win) {
                return null;
            }

            win.document.write(
                '<!DOCTYPE html><html><head><title>Opening mail</title></head>' +
                '<body style="font-family:system-ui,sans-serif;padding:40px;text-align:center;color:#334155">' +
                '<p><strong>Preparing email draft...</strong></p>' +
                '<p style="font-size:14px;color:#64748b">Please wait.</p>' +
                '</body></html>'
            );
            win.document.close();

            return win;
        }

        function sendShipmentMail($btn, preview, openUrl, mailWindow) {
            if (!preview) {
                alert('Email is not available for this shipment.');
                return Promise.resolve();
            }

            var openUrlWithDocuments = buildMailOpenUrl(openUrl, getCheckedUploadedDocumentIds());
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Opening mail...');

            if (mailWindow && !mailWindow.closed) {
                mailWindow.location.href = openUrlWithDocuments;
            } else {
                window.open(openUrlWithDocuments, '_blank');
            }

            if (typeof swal === 'function') {
                swal({
                    title: 'Email opening',
                    html: 'Your mail app is opening with To, Cc and Subject.<br><br>' +
                        'A complete draft with <strong>all PDF attachments</strong> is also downloading — ' +
                        'if Mail opens from that file, use that compose window (attachments included). ' +
                        'Otherwise double-click the downloaded <strong>.eml</strong> file in Downloads.',
                    type: 'info',
                    timer: 12000
                });
            }

            return Promise.resolve().finally(function() {
                $btn.prop('disabled', false).text(originalText);
            });
        }

        $(document).on('click', '#send-manifest-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);

            if (!$('#shipment-edit-form').length) {
                alert('Shipment form is not available.');
                return;
            }

            if (!$('#crr-ids-container input[name="crr_ids[]"]').length) {
                alert('Please add at least one stock item before sending a manifest.');
                return;
            }

            if (!manifestMailPreview) {
                alert('Could not prepare manifest email preview. Please save the shipment and try again.');
                return;
            }

            var mailWindow = openMailDraftWindow();
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Preparing manifest...');
            syncCrrHiddenInputs();

            $.ajax({
                url: manifestPrepareUrl,
                method: 'POST',
                dataType: 'json',
                data: serializeShipmentFormForAjax(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success || !response.preview) {
                        if (mailWindow && !mailWindow.closed) {
                            mailWindow.close();
                        }
                        alert((response && response.message) || 'Could not prepare manifest email.');
                        $btn.prop('disabled', false).text(originalText);
                        return;
                    }

                    manifestMailPreview = response.preview;
                    openShipmentMailto(response.preview);
                    var attachmentSources = (response.attachments && response.attachments.length)
                        ? response.attachments
                        : manifestCoreAttachmentSources;

                    sendShipmentMail(
                        $btn,
                        response.preview,
                        response.open_url,
                        mailWindow
                    );
                })
                .fail(function(xhr) {
                    if (mailWindow && !mailWindow.closed) {
                        mailWindow.close();
                    }
                    var message = 'Could not prepare manifest email.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                    $btn.prop('disabled', false).text(originalText);
                });
        });

        $(document).on('click', '#send-prealert-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);

            if (!$('#shipment-edit-form').length) {
                alert('Shipment form is not available.');
                return;
            }

            if (!$('#crr-ids-container input[name="crr_ids[]"]').length) {
                alert('Please add at least one stock item before sending a pre-alert.');
                return;
            }

            if (!preAlertMailPreview) {
                alert('Could not prepare pre-alert email preview. Please save the shipment and try again.');
                return;
            }

            var mailWindow = openMailDraftWindow();
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Preparing pre-alert...');
            syncCrrHiddenInputs();

            $.ajax({
                url: preAlertPrepareUrl,
                method: 'POST',
                dataType: 'json',
                data: serializeShipmentFormForAjax(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success || !response.preview) {
                        if (mailWindow && !mailWindow.closed) {
                            mailWindow.close();
                        }
                        alert((response && response.message) || 'Could not prepare pre-alert email.');
                        $btn.prop('disabled', false).text(originalText);
                        return;
                    }

                    preAlertMailPreview = response.preview;
                    openShipmentMailto(response.preview);

                    sendShipmentMail(
                        $btn,
                        response.preview,
                        response.open_url,
                        mailWindow
                    );
                })
                .fail(function(xhr) {
                    if (mailWindow && !mailWindow.closed) {
                        mailWindow.close();
                    }
                    var message = 'Could not prepare pre-alert email.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                    $btn.prop('disabled', false).text(originalText);
                });
        });

        $(document).on('click', '#finalize-shipment-btn', function(e) {
            e.preventDefault();
            syncFinalizeTransitButtonState();
            $('#finalize-shipment-choice-modal').modal('show');
        });

        $(document).on('click', '#finalize-shipment-transit-btn', function() {
            if ($(this).prop('disabled')) {
                return;
            }

            $('#finalize-shipment-number').val(@json($shipment->shipment_number));
            $('#finalize-consignee-code').val($.trim($('#consignee-party-code').val()) || '');
            $('#finalize-shipment-transit-modal').modal('show');
        });

        $(document).on('click', '#finalize-shipment-complete-btn', function() {
            var $btn = $(this);
            var originalText = $btn.text();

            $btn.prop('disabled', true).text('Completing...');

            $.ajax({
                url: finalizeShipmentUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    shipment_number: @json($shipment->shipment_number),
                    action: 'complete'
                },
                headers: {
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success) {
                        alert((response && response.message) || 'Could not complete shipment.');
                        return;
                    }

                    if (response.status) {
                        $('.header-meta-group .status-badge')
                            .removeClass('stock-status-new stock-status-stock shipment-status-in-transit stock-status-in-progress stock-status-pending stock-status-cancelled stock-status-completed stock-status-archived stock-status-unknown')
                            .addClass('stock-status-badge ' + stockStatusBadgeClass(response.status))
                            .text(response.status);
                        $('.select2-status-inline').val(response.status).trigger('change.select2');
                        $('#shipment-current-status').val(response.status);
                        syncAddStockItemsButtonState();
                        syncFinalizeTransitButtonState();
                    }

                    (response.stocks || []).forEach(function(stock) {
                        var $row = $('#stock-items-table tbody tr.selected-stock-row[data-crr-id="' + stock.id + '"]');
                        if (!$row.length) {
                            return;
                        }

                        $row.find('td').eq(9).html(
                            '<span class="stock-status-badge ' + stockStatusBadgeClass(stock.status) + '">' + stock.status + '</span>'
                        );

                        $('#stock-items-modal-table tbody tr[data-id="' + stock.id + '"]').remove();
                    });

                    syncAddStockItemsButtonState();
                    syncFinalizeTransitButtonState();
                })
                .fail(function(xhr) {
                    var message = 'Could not complete shipment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                })
                .always(function() {
                    $btn.prop('disabled', false).text(originalText);
                });
        });

        $(document).on('click', '#finalize-shipment-submit-btn', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            var consigneeCode = $.trim($('#finalize-consignee-code').val());

            $btn.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: finalizeShipmentUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    shipment_number: $('#finalize-shipment-number').val(),
                    consignee_code: consigneeCode,
                    action: 'transit'
                },
                headers: {
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success) {
                        alert((response && response.message) || 'Could not finalize shipment.');
                        return;
                    }

                    $('#consignee-party-code').val(consigneeCode);
                    if (response.status) {
                        $('.header-meta-group .status-badge')
                            .removeClass('stock-status-new stock-status-stock shipment-status-in-transit stock-status-in-progress stock-status-pending stock-status-cancelled stock-status-completed stock-status-archived stock-status-unknown')
                            .addClass('stock-status-badge ' + stockStatusBadgeClass(response.status))
                            .text(response.status);
                        $('.select2-status-inline').val(response.status).trigger('change.select2');
                        $('#shipment-current-status').val(response.status);
                        syncAddStockItemsButtonState();
                        syncFinalizeTransitButtonState();
                    }

                    $('#finalize-shipment-transit-modal').modal('hide');

                    if (response.reload) {
                        if (typeof window.shipmentEditMarkAllowLeave === 'function') {
                            window.shipmentEditMarkAllowLeave();
                        }
                        window.location.reload();
                    }
                })
                .fail(function(xhr) {
                    var message = 'Could not finalize shipment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                })
                .always(function() {
                    $btn.prop('disabled', false).text(originalText);
                });
        });

        $('#finalize-shipment-transit-modal').on('hidden.bs.modal', function() {
            if (!$('#finalize-shipment-choice-modal').hasClass('show')) {
                $('#finalize-shipment-choice-modal').modal('show');
            }
        });

        // Tab Switching Logic for Shipment Tabs
        $('.nav-tab-item').on('click', function() {
            var target = $(this).data('target');
            
            $('.nav-tab-item').removeClass('active');
            $(this).addClass('active');
            
            $('.tab-panel').removeClass('active');
            $('#tab-' + target).addClass('active');
        });

        // Simple Tab Logic for Stock Tabs
        $('.stock-tab').on('click', function() {
            $('.stock-tab').removeClass('active');
            $(this).addClass('active');
            $('.stock-panel').removeClass('active').hide();
            $('#' + $(this).data('panel')).addClass('active').show();
        });

        function formatParty(item) {
            if (!item.id) return item.text;
            var subtitleParts = [];
            if (item.type_label) {
                subtitleParts.push(item.type_label);
            }
            if (item.subtitle) {
                subtitleParts.push(item.subtitle);
            }
            var subtitle = subtitleParts.join(' · ');
            return $('<div style="line-height:1.1;"><div style="font-weight:600;">' + item.text + '</div>' + (subtitle ? '<div style="font-size:11px;color:#6b7280;">' + subtitle + '</div>' : '') + '</div>');
        }

        function formatPartySelection(item) {
            return item.text || item.id;
        }

        var hubDepartureCodes = @json($hubs->mapWithKeys(fn ($hub) => ['hub:' . $hub->id => $hub->code ?? '']));
        var consigneePartyCodes = @json($consigneePartyCodes ?? []);

        function resolveConsigneeCodeFromSelection(data) {
            if (!data || !data.id) {
                return '';
            }

            var code = $.trim(data.code || '');
            if (code !== '') {
                return code;
            }

            return $.trim(consigneePartyCodes[data.id] || '');
        }

        function applyConsigneeCode(data) {
            $('#consignee-party-code').val(resolveConsigneeCodeFromSelection(data));
        }

        function applyDeparturePortCode(data) {
            if (!data || !data.id) {
                $('#departure-port-code').val('');
                return;
            }

            if (data.type === 'hub' || String(data.id).indexOf('hub:') === 0) {
                $('#departure-port-code').val(data.hub_code || hubDepartureCodes[data.id] || '');
                return;
            }

            $('#departure-port-code').val(data.port_code || '');
        }

        $('#departure-select').select2({
            placeholder: 'Type departure',
            allowClear: false,
            width: '100%',
            ajax: {
                url: '/laravel/api/parties',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data }; }
            },
            templateResult: formatParty,
            templateSelection: formatPartySelection
        }).on('select2:select', function(e) {
            applyDeparturePortCode(e.params.data);
        }).on('select2:clear', function() {
            $('#departure-port-code').val('');
        });

        $('#consignee-select').select2({
            placeholder: 'Type consignee',
            allowClear: false,
            width: '100%',
            ajax: {
                url: '/laravel/api/consignees',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data }; }
            },
            templateResult: formatParty,
            templateSelection: formatPartySelection
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $('#consignee-address').val(data.address || '');
            $('#consignee-city').val(data.city || '');
            $('#consignee-district').val(data.district || '');
            $('#consignee-zip').val(data.zip || '');
            $('#consignee-country').val(data.country || '').trigger('change');
            $('#consignee-port-code').val(data.port_code || '');
            $('#consignee-email').val(data.email || '');
            $('textarea[name="special_considerations_destination"]').val(data.special_considerations || '');
            applyConsigneeCode(data);
        }).on('select2:clear', function() {
            $('#consignee-address, #consignee-city, #consignee-district, #consignee-zip, #consignee-port-code, #location, #consignee-email').val('');
            $('#consignee-country').val('').trigger('change');
            $('#consignee-party-code').val('');
            $('textarea[name="special_considerations_destination"]').val('');
        });

        var shipmentUpdateStatusUrl = @json(route('shipments.update-status', $shipment->id));
        var shipmentUpdateFlagsUrl = @json(route('shipments.update-flags', $shipment->id));
        var stockEditUrlTemplate = {{ Illuminate\Support\Js::from(route('stocks.edit', ['id' => '__CRR_ID__'])) }};
        var lastHeaderStatus = @json($shipment->status);
        var lastHeaderFlags = @json(array_slice($shipmentFlags, 0, 1));
        var suppressFlagsChange = false;
        var flagsConfirmOpen = false;
        var suppressStatusChange = false;
        var statusConfirmOpen = false;

        function renderHeaderFlags(flags) {
            var $pills = $('#flags-edit-container .flags-pills');
            $pills.empty();

            if (!flags || !flags.length) {
                $pills.append('<span class="text-muted" style="font-size: 11px; font-weight: 500;">—</span>');
                return;
            }

            flags.forEach(function(flag) {
                $pills.append('<span class="summary-flag">' + $('<div>').text(flag).html() + '</span>');
            });
        }

        function closeHeaderInlineEditors(exceptContainer) {
            if (exceptContainer !== '#status-edit-container') {
                $('#status-edit-container .status-select-wrapper').hide();
                $('#status-edit-container .status-display').show();
            }
            if (exceptContainer !== '#flags-edit-container') {
                $('#flags-edit-container .flags-select-wrapper').hide();
                $('#flags-edit-container .flags-display').show();
            }
        }

        function initHeaderSelect2($select, dropdownParent) {
            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                width: '100%',
                dropdownParent: dropdownParent
            });
        }

        function initHeaderFlagsSelect2($select, dropdownParent) {
            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                width: '100%',
                dropdownParent: dropdownParent,
                placeholder: 'Select flag',
                allowClear: true
            });
        }

        $('#status-edit-container .status-display').on('click', function(e) {
            e.stopPropagation();
            closeHeaderInlineEditors('#status-edit-container');
            $(this).hide();
            $('#status-edit-container .status-select-wrapper').show();

            var $select = $('.select2-status-inline');
            initHeaderSelect2($select, $('.main-content-area'));
            $select.select2('open');
        });

        $('#flags-edit-container .flags-display').on('click', function(e) {
            e.stopPropagation();
            closeHeaderInlineEditors('#flags-edit-container');
            $(this).hide();
            $('#flags-edit-container .flags-select-wrapper').show();

            var $select = $('.select2-flags-inline');
            initHeaderFlagsSelect2($select, $('.main-content-area'));
            $select.select2('open');
        });

        $(document).on('click', function(e) {
            if ($(e.target).closest('.sweet-alert, .sweet-overlay').length) {
                return;
            }

            if (!$(e.target).closest('#status-edit-container, #flags-edit-container, .select2-container').length) {
                closeHeaderInlineEditors();
            }
        });

        $('.select2-status-inline').on('change', function() {
            if (suppressStatusChange || statusConfirmOpen) {
                return;
            }

            var newStatus = $(this).val();

            if (newStatus === lastHeaderStatus) {
                closeHeaderInlineEditors();
                return;
            }

            confirmStatusChange(newStatus, function() {
                saveShipmentStatus(newStatus);
            });
        });

        function revertHeaderStatusSelection() {
            suppressStatusChange = true;
            $('.select2-status-inline').val(lastHeaderStatus).trigger('change.select2');
            suppressStatusChange = false;
            closeHeaderInlineEditors();
        }

        function confirmStatusChange(newStatusLabel, onConfirm) {
            var message = newStatusLabel === 'Cancelled'
                ? 'Cancel this shipment? Its selected stocks will be returned to Stock status.'
                : 'Change status to "' + newStatusLabel + '"?';

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
                        revertHeaderStatusSelection();
                        return;
                    }

                    onConfirm();
                });
                return;
            }

            if (confirm(message)) {
                onConfirm();
            } else {
                revertHeaderStatusSelection();
            }
        }

        function saveShipmentStatus(newStatus) {
            $.ajax({
                url: shipmentUpdateStatusUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: newStatus
                }
            }).done(function(response) {
                if (!response || !response.success) {
                    statusConfirmOpen = false;
                    alert('Could not update status.');
                    revertHeaderStatusSelection();
                    return;
                }

                statusConfirmOpen = false;
                if (typeof swal === 'function') {
                    swal.close();
                }
                window.location.reload();
            }).fail(function() {
                statusConfirmOpen = false;
                alert('Could not update status.');
                revertHeaderStatusSelection();
            }).always(function() {
                closeHeaderInlineEditors();
            });
        }

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

        function revertHeaderFlagsSelection() {
            var currentFlags = normalizeFlagsValue(lastHeaderFlags);
            suppressFlagsChange = true;
            $('.select2-flags-inline').val(currentFlags[0] || '').trigger('change.select2');
            suppressFlagsChange = false;
            closeHeaderInlineEditors();
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
                        revertHeaderFlagsSelection();
                        return;
                    }

                    onConfirm();
                });
                return;
            }

            if (confirm(message)) {
                onConfirm();
            } else {
                revertHeaderFlagsSelection();
            }
        }

        function saveShipmentFlags(newFlags) {
            $.ajax({
                url: shipmentUpdateFlagsUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    flags: normalizeFlagsValue(newFlags)
                }
            }).done(function(response) {
                if (!response || !response.success) {
                    flagsConfirmOpen = false;
                    alert('Could not update flags.');
                    revertHeaderFlagsSelection();
                    return;
                }

                flagsConfirmOpen = false;
                if (typeof swal === 'function') {
                    swal.close();
                }
                window.location.reload();
            }).fail(function(xhr) {
                flagsConfirmOpen = false;
                var message = 'Could not update flags.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
                revertHeaderFlagsSelection();
            }).always(function() {
                closeHeaderInlineEditors();
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
                closeHeaderInlineEditors();
                return;
            }

            confirmFlagsChange(newFlags, function() {
                saveShipmentFlags(newFlags);
            });
        });

        var irregularityTypeOptions = @json($irregularityTypeOptions);
        var partyResponsibleOptions = @json($partyResponsibleOptions);
        var consequenceOptions = @json($consequenceOptions);
        var statusOptions = @json($statusOptions);
        var irregularityTypeOptionsHtml = '<option></option>' + irregularityTypeOptions.map(function(o) { return '<option>' + o + '</option>'; }).join('');
        var partyResponsibleOptionsHtml = '<option></option>' + partyResponsibleOptions.map(function(o) { return '<option>' + o + '</option>'; }).join('');
        var consequenceOptionsHtml = '<option></option>' + consequenceOptions.map(function(o) { return '<option>' + o + '</option>'; }).join('');
        var irregularityStatusOptionsHtml = '<option></option>' + statusOptions.map(function(o) { return '<option>' + o + '</option>'; }).join('');

        $('#add-irregularity-btn').on('click', function() {
            var newItem = `
                <div class="irregularity-item border-bottom pb-4 mb-4">
                    <div class="row">
                        <div class="col-md-2 pr-1"><div class="form-group-custom"><label>Date</label><input type="text" name="irregularities[][irregularity_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY"></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Irregularity</label><select name="irregularities[][irregularity_type]" class="form-control-sm-custom select2">${irregularityTypeOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Party responsible</label><select name="irregularities[][party_responsible]" class="form-control-sm-custom select2">${partyResponsibleOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Consequence</label><select name="irregularities[][consequence]" class="form-control-sm-custom select2">${consequenceOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Extra cost for MT (USD)</label><input type="text" name="irregularities[][extra_cost_mt_usd]" class="form-control-sm-custom"></div></div>
                        <div class="col-md-2 pl-1 d-flex align-items-end"><div class="form-group-custom flex-grow-1 mr-2"><label>Status</label><select name="irregularities[][status]" class="form-control-sm-custom select2">${irregularityStatusOptionsHtml}</select></div><button type="button" class="btn btn-link text-muted p-0 mb-2 remove-irregularity"><i class="ti-trash"></i></button></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1"><div class="form-group-custom"><label>Cause of irregularity</label><textarea name="irregularities[][cause_of_irregularity]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 px-1"><div class="form-group-custom"><label>Action taken</label><textarea name="irregularities[][action_taken]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 pl-1"><div class="form-group-custom"><label>Customer response</label><textarea name="irregularities[][customer_response]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1"><div class="form-group-custom"><label>Hub/agent comments</label><textarea name="irregularities[][hub_agent_comments]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 px-1"><div class="form-group-custom"><label>Handled by</label><input type="text" name="irregularities[][handled_by]" class="form-control-sm-custom"></div></div>
                    </div>
                </div>`;
            var $newItem = $(newItem);
            $('#irregularities-container').append($newItem);
            $newItem.find('.select2').select2({ placeholder: '', allowClear: false, width: '100%' });
            $newItem.find('.datepicker').datepicker({ dateFormat: 'dd.mm.yy', showOtherMonths: true, selectOtherMonths: true, changeMonth: true, changeYear: true, yearRange: 'c-10:c+10' });
        });

        $(document).on('click', '.remove-irregularity', function() {
            $(this).closest('.irregularity-item').remove();
        });

        function initFlightDatepickers($container) {
            $container.find('.datepicker').each(function() {
                if ($(this).hasClass('hasDatepicker')) {
                    return;
                }
                $(this).datepicker({
                    dateFormat: 'dd.mm.yy',
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: 'c-10:c+10'
                });
            });
        }

        function reindexLegRowNames(containerSelector, rowSelector, prefix) {
            $(containerSelector).find(rowSelector).each(function(index) {
                $(this).find('[name^="' + prefix + '"]').each(function() {
                    var name = $(this).attr('name');
                    var match = name && name.match(/\[([^\]]+)\]$/);
                    if (match) {
                        $(this).attr('name', prefix + '[' + index + '][' + match[1] + ']');
                    }
                });
            });
        }

        function updateAirfreightFlightLabels() {
            $('#airfreight-flights-container .airfreight-flight-row').each(function(index) {
                var label = index === 0 ? 'Airway bill' : 'Departure port';
                $(this).find('.flight-first-field label').text(label);
            });
        }

        function buildAirfreightFlightRowHtml() {
            var rowIndex = $('#airfreight-flights-container .airfreight-flight-row').length;
            var firstLabel = rowIndex === 0 ? 'Airway bill' : 'Departure port';

            return `
                <div class="airfreight-flight-row">
                    <div class="flight-field flight-first-field">
                        <div class="form-group-custom mb-0">
                            <label>${firstLabel}</label>
                            <input type="text" name="flights[${rowIndex}][leg_reference]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="flight-field">
                        <div class="form-group-custom mb-0">
                            <label>Flight number</label>
                            <input type="text" name="flights[${rowIndex}][flight_number]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="flight-field">
                        <div class="form-group-custom mb-0">
                            <label>Departure date</label>
                            <div class="input-with-icon">
                                <input type="text" name="flights[${rowIndex}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flight-field">
                        <div class="form-group-custom mb-0">
                            <label>Arrival date</label>
                            <div class="input-with-icon">
                                <input type="text" name="flights[${rowIndex}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flight-field flight-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Arrival time</label>
                            <input type="text" name="flights[${rowIndex}][arrival_time]" class="form-control-sm-custom flight-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 flight-remove-btn remove-airfreight-flight" title="Remove flight">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        function toggleServiceDetailsPanel() {
            var service = $('select[name="service"]').val();
            $('#service-details-placeholder').hide();
            $('#service-details-airfreight').hide();
            $('#service-details-sea-freight').hide();
            $('#service-details-truck').hide();
            $('#service-details-courier').hide();
            $('#service-details-release').hide();
            $('#service-details-hand-carry').hide();
            $('#service-details-on-board').hide();

            if (service === 'Airfreight') {
                $('#service-details-airfreight').show();
            } else if (service === 'Sea freight') {
                $('#service-details-sea-freight').show();
            } else if (service === 'Truck') {
                $('#service-details-truck').show();
            } else if (service === 'Courier') {
                $('#service-details-courier').show();
            } else if (service === 'Release') {
                $('#service-details-release').show();
            } else if (service === 'Hand Carry') {
                $('#service-details-hand-carry').show();
            } else if (service === 'On-board delivery') {
                $('#service-details-on-board').show();
            } else {
                $('#service-details-placeholder').show();
            }
        }

        $('select[name="service"]').on('change', function() {
            toggleServiceDetailsPanel();
        });
        toggleServiceDetailsPanel();

        $('#add-airfreight-flight-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildAirfreightFlightRowHtml());
            $('#airfreight-flights-container').append($row);
            initFlightDatepickers($row);
            updateAirfreightFlightLabels();
        });

        $(document).on('click', '.remove-airfreight-flight', function() {
            $(this).closest('.airfreight-flight-row').remove();
            reindexLegRowNames('#airfreight-flights-container', '.airfreight-flight-row', 'flights');
            updateAirfreightFlightLabels();
        });

        function buildSeaFreightLegRowHtml() {
            var rowIndex = $('#sea-freight-legs-container .sea-freight-leg-row').length;
            return `
                <div class="sea-freight-leg-row">
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Bill of lading</label>
                            <input type="text" name="sea_legs[${rowIndex}][bill_of_lading]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Container number</label>
                            <input type="text" name="sea_legs[${rowIndex}][container_number]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Transport vessel IMO</label>
                            <input type="text" name="sea_legs[${rowIndex}][transport_vessel_imo]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Transport vessel name</label>
                            <input type="text" name="sea_legs[${rowIndex}][transport_vessel_name]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>ETD</label>
                            <div class="input-with-icon">
                                <input type="text" name="sea_legs[${rowIndex}][etd]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="sea-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>ETA</label>
                            <div class="input-with-icon">
                                <input type="text" name="sea_legs[${rowIndex}][eta]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="sea-leg-field sea-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Arrival time</label>
                            <input type="text" name="sea_legs[${rowIndex}][arrival_time]" class="form-control-sm-custom sea-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 sea-leg-remove-btn remove-sea-freight-leg" title="Remove leg">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-sea-freight-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildSeaFreightLegRowHtml());
            $('#sea-freight-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-sea-freight-leg', function() {
            $(this).closest('.sea-freight-leg-row').remove();
            reindexLegRowNames('#sea-freight-legs-container', '.sea-freight-leg-row', 'sea_legs');
        });

        function buildTruckLegRowHtml() {
            var rowIndex = $('#truck-legs-container .truck-leg-row').length;
            return `
                <div class="truck-leg-row">
                    <div class="truck-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>CMR</label>
                            <input type="text" name="truck_legs[${rowIndex}][cmr]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="truck-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Freight company</label>
                            <input type="text" name="truck_legs[${rowIndex}][freight_company]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="truck-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Departure date</label>
                            <div class="input-with-icon">
                                <input type="text" name="truck_legs[${rowIndex}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="truck-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Arrival date</label>
                            <div class="input-with-icon">
                                <input type="text" name="truck_legs[${rowIndex}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="truck-leg-field truck-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Arrival time</label>
                            <input type="text" name="truck_legs[${rowIndex}][arrival_time]" class="form-control-sm-custom truck-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 truck-leg-remove-btn remove-truck-leg" title="Remove">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-truck-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildTruckLegRowHtml());
            $('#truck-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-truck-leg', function() {
            $(this).closest('.truck-leg-row').remove();
            reindexLegRowNames('#truck-legs-container', '.truck-leg-row', 'truck_legs');
        });

        function buildCourierLegRowHtml() {
            var rowIndex = $('#courier-legs-container .courier-leg-row').length;
            return `
                <div class="courier-leg-row">
                    <div class="courier-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Airway bill</label>
                            <input type="text" name="courier_legs[${rowIndex}][airway_bill]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="courier-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Carrier</label>
                            <input type="text" name="courier_legs[${rowIndex}][carrier]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="courier-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Departure date</label>
                            <div class="input-with-icon">
                                <input type="text" name="courier_legs[${rowIndex}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="courier-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Arrival date</label>
                            <div class="input-with-icon">
                                <input type="text" name="courier_legs[${rowIndex}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="courier-leg-field courier-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Arrival time</label>
                            <input type="text" name="courier_legs[${rowIndex}][arrival_time]" class="form-control-sm-custom courier-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 courier-leg-remove-btn remove-courier-leg" title="Remove">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-courier-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildCourierLegRowHtml());
            $('#courier-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-courier-leg', function() {
            $(this).closest('.courier-leg-row').remove();
            reindexLegRowNames('#courier-legs-container', '.courier-leg-row', 'courier_legs');
        });

        function buildReleaseLegRowHtml() {
            var rowIndex = $('#release-legs-container .release-leg-row').length;
            return `
                <div class="release-leg-row">
                    <div class="release-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Freight company</label>
                            <input type="text" name="release_legs[${rowIndex}][freight_company]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="release-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Delivery date</label>
                            <div class="input-with-icon">
                                <input type="text" name="release_legs[${rowIndex}][delivery_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="release-leg-field release-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Delivery time</label>
                            <input type="text" name="release_legs[${rowIndex}][delivery_time]" class="form-control-sm-custom release-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 release-leg-remove-btn remove-release-leg" title="Remove">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-release-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildReleaseLegRowHtml());
            $('#release-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-release-leg', function() {
            $(this).closest('.release-leg-row').remove();
            reindexLegRowNames('#release-legs-container', '.release-leg-row', 'release_legs');
        });

        function buildHandCarryLegRowHtml() {
            var rowIndex = $('#hand-carry-legs-container .hand-carry-leg-row').length;
            return `
                <div class="hand-carry-leg-row">
                    <div class="hand-carry-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Departure date</label>
                            <div class="input-with-icon">
                                <input type="text" name="hand_carry_legs[${rowIndex}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="hand-carry-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Arrival date</label>
                            <div class="input-with-icon">
                                <input type="text" name="hand_carry_legs[${rowIndex}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="hand-carry-leg-field hand-carry-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Arrival time</label>
                            <input type="text" name="hand_carry_legs[${rowIndex}][arrival_time]" class="form-control-sm-custom hand-carry-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <div class="hand-carry-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Contact name</label>
                            <input type="text" name="hand_carry_legs[${rowIndex}][contact_name]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="hand-carry-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Contact phone</label>
                            <input type="text" name="hand_carry_legs[${rowIndex}][contact_phone]" class="form-control-sm-custom">
                        </div>
                    </div>
                    <div class="hand-carry-leg-field hand-carry-leg-checkbox">
                        <div class="checkbox-fade fade-in-primary mb-0" style="padding-bottom: 6px;">
                            <label class="mb-0 d-flex align-items-center" style="white-space: nowrap;">
                                <input type="checkbox" name="hand_carry_legs[${rowIndex}][onboard_hand_carry]" value="1">
                                <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                <span class="text-inverse" style="font-size: 10px;">Onboard hand carry</span>
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 hand-carry-leg-remove-btn remove-hand-carry-leg" title="Remove">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-hand-carry-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildHandCarryLegRowHtml());
            $('#hand-carry-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-hand-carry-leg', function() {
            $(this).closest('.hand-carry-leg-row').remove();
            reindexLegRowNames('#hand-carry-legs-container', '.hand-carry-leg-row', 'hand_carry_legs');
        });

        function buildOnBoardLegRowHtml() {
            var rowIndex = $('#on-board-legs-container .on-board-leg-row').length;
            return `
                <div class="on-board-leg-row">
                    <div class="on-board-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Departure date</label>
                            <div class="input-with-icon">
                                <input type="text" name="on_board_legs[${rowIndex}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="on-board-leg-field">
                        <div class="form-group-custom mb-0">
                            <label>Delivery date</label>
                            <div class="input-with-icon">
                                <input type="text" name="on_board_legs[${rowIndex}][delivery_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                                <i class="ti-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="on-board-leg-field on-board-leg-field-time">
                        <div class="form-group-custom mb-0">
                            <label>Delivery time</label>
                            <input type="text" name="on_board_legs[${rowIndex}][delivery_time]" class="form-control-sm-custom on-board-leg-time-input" placeholder="hh:mm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 on-board-leg-remove-btn remove-on-board-leg" title="Remove">
                        <i class="ti-close" style="font-size: 14px;"></i>
                    </button>
                </div>`;
        }

        $('#add-on-board-leg-btn').on('click', function(e) {
            e.preventDefault();
            var $row = $(buildOnBoardLegRowHtml());
            $('#on-board-legs-container').append($row);
            initFlightDatepickers($row);
        });

        $(document).on('click', '.remove-on-board-leg', function() {
            $(this).closest('.on-board-leg-row').remove();
            reindexLegRowNames('#on-board-legs-container', '.on-board-leg-row', 'on_board_legs');
        });

        initFlightDatepickers($('#airfreight-flights-container'));
        initFlightDatepickers($('#sea-freight-legs-container'));
        initFlightDatepickers($('#truck-legs-container'));
        initFlightDatepickers($('#courier-legs-container'));
        initFlightDatepickers($('#release-legs-container'));
        initFlightDatepickers($('#hand-carry-legs-container'));
        initFlightDatepickers($('#on-board-legs-container'));

        function showStockModalError(message) {
            $('#stock-items-modal-error').text(message).show();
        }

        function clearStockModalError() {
            $('#stock-items-modal-error').hide().text('');
        }

        function normalizeHubKey(value) {
            return String(value || '')
                .trim()
                .toLowerCase()
                .replace(/\s+/g, ' ');
        }

        function getHubKeyFromSelectedStockRow($row) {
            var hubKey = normalizeHubKey($row.attr('data-hub-key'));
            if (hubKey) {
                return hubKey;
            }

            var hubText = normalizeHubKey($row.find('td').eq(0).text());
            return hubText && hubText !== '—' ? hubText : '';
        }

        function getHubKeyFromModalRow($row) {
            var hubCode = normalizeHubKey($row.attr('data-hub'));
            var hubAgent = normalizeHubKey($row.attr('data-hub-agent'));
            if (hubCode || hubAgent) {
                return hubCode || hubAgent;
            }

            var hubText = normalizeHubKey($row.find('td').eq(1).text());
            return hubText && hubText !== '—' ? hubText : '';
        }

        function collectSelectedHubKeysForValidation() {
            var selectedHubKeys = [];

            $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                var existingHubKey = getHubKeyFromSelectedStockRow($(this));
                if (existingHubKey && selectedHubKeys.indexOf(existingHubKey) === -1) {
                    selectedHubKeys.push(existingHubKey);
                }
            });

            $('.modal-row-checkbox:checked').each(function() {
                var id = $(this).val();
                var $modalRow = $('#stock-items-modal-table tbody tr[data-id="' + id + '"]');
                if (!$modalRow.length) {
                    return;
                }

                var hubKey = getHubKeyFromModalRow($modalRow);
                if (hubKey && selectedHubKeys.indexOf(hubKey) === -1) {
                    selectedHubKeys.push(hubKey);
                }
            });

            return selectedHubKeys;
        }

        function updateRealtimeHubValidation() {
            var selectedHubKeys = collectSelectedHubKeysForValidation();
            var hasMismatch = selectedHubKeys.length > 1;

            if (hasMismatch) {
                showStockModalError('All selected stock items must belong to the same hub.');
            } else {
                clearStockModalError();
            }

            $('#modal-add-selected').prop('disabled', hasMismatch);
            return !hasMismatch;
        }

        function refreshStockItemsTable() {
            var count = $('#stock-items-table tbody tr.selected-stock-row').length;
            $('.stock-tab[data-panel="stock-panel-items"]').text('Stock items (' + count + ')');
            if (count === 0 && $('#stock-items-table tbody tr#empty-row').length === 0) {
                $('#stock-items-table tbody').append('<tr id="empty-row"><td colspan="11" class="text-center py-3 text-muted">No stock items added yet.</td></tr>');
            }
        }

        function syncCrrHiddenInputs() {
            $('#crr-ids-container').empty();
            $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                var crrId = $(this).data('crr-id');
                if (crrId) {
                    $('#crr-ids-container').append('<input type="hidden" name="crr_ids[]" value="' + crrId + '">');
                }
            });
        }

        $('#shipment-edit-form').on('submit', function(e) {
            var selectedHubKeys = [];
            $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                var hubKey = getHubKeyFromSelectedStockRow($(this));
                if (hubKey && selectedHubKeys.indexOf(hubKey) === -1) {
                    selectedHubKeys.push(hubKey);
                }
            });

            if (selectedHubKeys.length > 1) {
                e.preventDefault();
                if (typeof swal === 'function') {
                    swal({
                        title: 'Validation error',
                        text: 'All selected stock items must belong to the same hub.',
                        type: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('All selected stock items must belong to the same hub.');
                }
                return false;
            }

            syncCrrHiddenInputs();
        });

        function syncAddStockItemsButtonState() {
            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            $('#add-stock-items-btn').prop('disabled', status === 'Completed' || status === 'Cancelled');
        }

        function syncFinalizeTransitButtonState() {
            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            var canTransit = status === 'Completed';
            var $transitBtn = $('#finalize-shipment-transit-btn');

            $transitBtn.prop('disabled', !canTransit);
            $transitBtn.attr('title', canTransit ? '' : 'Complete the shipment first');
        }

        syncAddStockItemsButtonState();
        syncFinalizeTransitButtonState();

        $('#add-stock-items-btn').on('click', function() {
            if ($(this).prop('disabled')) {
                return;
            }

            $('#stock-items-modal').modal('show');
        });

        function getSelectedStockCrrIds() {
            var ids = [];
            $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                var crrId = $(this).attr('data-crr-id');
                if (crrId) {
                    ids.push(String(crrId));
                }
            });
            return ids;
        }

        function updateModalRowHighlights() {
            $('#stock-items-modal-table tbody tr').each(function() {
                var $row = $(this);
                if ($row.hasClass('modal-empty-state')) {
                    return;
                }
                var isChecked = $row.find('.modal-row-checkbox').prop('checked');
                $row.toggleClass('modal-row-selected', isChecked);
            });
        }

        function syncModalCheckboxesFromStockTable() {
            var selectedIds = getSelectedStockCrrIds();
            $('.modal-row-checkbox').each(function() {
                var id = String($(this).val());
                $(this).prop('checked', selectedIds.indexOf(id) !== -1);
            });
            updateModalSelectAllState();
            updateModalRowHighlights();
        }

        function updateModalSelectAllState() {
            var $visible = $('#stock-items-modal-table tbody tr:visible .modal-row-checkbox');
            var $checkedVisible = $visible.filter(':checked');
            $('#modal-select-all').prop('checked', $visible.length > 0 && $visible.length === $checkedVisible.length);
        }

        function applyStockModalFilters() {
            var selectedHub = $('#modal-hub-filter').val();
            var selectedCustomer = $('#modal-customer-filter').val();
            var selectedVessel = $('#modal-vessel-filter').val();
            var selectedStatus = $('#modal-status-filter').val();
            var landedFilter = $('#modal-landed-filter').val();
            var stockFilter = ($('#modal-stock-filter').val() || '').toString().toLowerCase();
            var poFilter = ($('#modal-po-filter').val() || '').toString().toLowerCase();
            var supplierFilter = ($('#modal-supplier-filter').val() || '').toString().toLowerCase();

            var visibleRows = 0;
            $('#stock-items-modal-table tbody tr').each(function () {
                var $row = $(this);
                if ($row.hasClass('modal-empty-state')) {
                    return;
                }

                var rowHub = ($row.data('hub') || '').toString().toLowerCase();
                var rowCustomer = ($row.data('customer') || '').toString().toLowerCase();
                var rowVessel = ($row.data('vessel') || '').toString().toLowerCase();
                var rowStatus = ($row.data('status') || '').toString().toLowerCase();
                var rowLanded = ($row.data('landed') || '0').toString();
                var rowStock = ($row.data('stock') || '').toString().toLowerCase();
                var rowPo = ($row.data('po') || '').toString().toLowerCase();
                var rowSupplier = ($row.data('supplier') || '').toString().toLowerCase();

                var matches = rowStatus !== 'completed';

                if (matches && selectedHub && rowHub !== selectedHub.toLowerCase()) {
                    matches = false;
                }
                if (matches && selectedCustomer && rowCustomer !== selectedCustomer.toLowerCase()) {
                    matches = false;
                }
                if (matches && selectedVessel && rowVessel !== selectedVessel.toLowerCase()) {
                    matches = false;
                }
                if (matches && selectedStatus && rowStatus !== selectedStatus.toLowerCase()) {
                    matches = false;
                }
                if (matches && landedFilter) {
                    if (landedFilter === 'yes' && rowLanded !== '1') {
                        matches = false;
                    } else if (landedFilter === 'no' && rowLanded === '1') {
                        matches = false;
                    }
                }
                if (matches && stockFilter && rowStock.indexOf(stockFilter) === -1) {
                    matches = false;
                }
                if (matches && poFilter && rowPo.indexOf(poFilter) === -1) {
                    matches = false;
                }
                if (matches && supplierFilter && rowSupplier.indexOf(supplierFilter) === -1) {
                    matches = false;
                }

                $row.toggle(matches);
                if (matches) {
                    visibleRows++;
                }
            });

            if ($('.modal-empty-state').length === 0) {
                $('#stock-items-modal-table tbody').append('<tr class="modal-empty-state"><td colspan="15" class="text-center py-4 text-muted" style="font-size: 12px;">No matching stock entries found.</td></tr>');
            }

            $('.modal-empty-state').toggle(visibleRows === 0);
            updateModalSelectAllState();
        }

        $('#stock-items-modal').on('shown.bs.modal', function() {
            clearStockModalError();
            $('#modal-add-selected').prop('disabled', false);
            $('.modal-select2').select2({
                placeholder: 'Click here',
                allowClear: false,
                width: '100%',
                dropdownParent: $('#stock-items-modal')
            });

            $('.modal-select2').off('change').on('change', applyStockModalFilters);
            $('.modal-filter-input').off('input change').on('input change', applyStockModalFilters);
            applyStockModalFilters();
            syncModalCheckboxesFromStockTable();
            updateRealtimeHubValidation();
        });

        $('#stock-items-modal').on('hidden.bs.modal', function() {
            clearStockModalError();
            $('#modal-add-selected').prop('disabled', false);
        });

        $(document).on('click', '.modal-clear-filters', function() {
            $('.modal-select2').val(null).trigger('change');
            $('.modal-filter-input').val('').trigger('change');
            applyStockModalFilters();
            syncModalCheckboxesFromStockTable();
            updateRealtimeHubValidation();
        });

        $('#modal-select-all').on('change', function() {
            $('#stock-items-modal-table tbody tr:visible .modal-row-checkbox').prop('checked', $(this).prop('checked'));
            updateModalRowHighlights();
            updateRealtimeHubValidation();
        });

        $(document).on('change', '.modal-row-checkbox', function() {
            updateModalSelectAllState();
            updateModalRowHighlights();
            updateRealtimeHubValidation();
        });

        $('#modal-add-selected').on('click', function() {
            clearStockModalError();
            var selectedIds = [];
            $('.modal-row-checkbox:checked').each(function() {
                selectedIds.push(String($(this).val()));
            });

            if (!updateRealtimeHubValidation()) {
                return;
            }

            $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                var id = String($(this).attr('data-crr-id'));
                if (selectedIds.indexOf(id) === -1) {
                    $(this).remove();
                }
            });

            $('#empty-row').remove();
            selectedIds.forEach(function(id) {
                if ($('#stock-items-table tbody tr.selected-stock-row[data-crr-id="' + id + '"]').length) return;
                var $modalRow = $('#stock-items-modal-table tbody tr[data-id="' + id + '"]');
                if ($modalRow.length === 0) return;
                var hubCode = String($modalRow.attr('data-hub') || '').trim();
                var hubAgent = String($modalRow.attr('data-hub-agent') || '').trim();
                var hub = hubCode || hubAgent || '—';
                var hubKey = normalizeHubKey(hubCode || hubAgent);
                var status = $modalRow.data('status') || 'Pending';
                var statusClass = 'stock-status-badge ' + stockStatusBadgeClass(status);
                var stockNumber = $modalRow.data('stock') || '—';
                var stockEditUrl = stockEditUrlTemplate.replace('__CRR_ID__', encodeURIComponent(id));
                var rowHtml = '<tr class="selected-stock-row" data-crr-id="' + id + '" data-hub-key="' + hubKey + '">' +
                    '<td>' + hub + '</td>' +
                    '<td>' + ($modalRow.data('vessel') || '—') + '</td>' +
                    '<td style="max-width:150px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;display:block;">' + ($modalRow.data('po') || '—') + '</td>' +
                    '<td>' + ($modalRow.data('supplier') || '—') + '</td>' +
                    '<td><a href="' + stockEditUrl + '" class="text-primary">' + $('<div>').text(stockNumber).html() + '</a></td>' +
                    '<td>' + ($modalRow.data('items') || '—') + '</td>' +
                    '<td>' + ($modalRow.data('weight') || '—') + '</td>' +
                    '<td>' + ($modalRow.data('cbm') || '—') + '</td>' +
                    '<td>' + ($modalRow.data('value') || '—') + '</td>' +
                    '<td><span class="' + statusClass + '">' + status + '</span></td>' +
                    '<td style="text-align:center;"><button type="button" class="btn btn-link btn-sm p-0 remove-stock-item"><i class="ti-trash text-muted"></i></button></td>' +
                    '</tr>';
                $('#stock-items-table tbody').append(rowHtml);
            });
            refreshStockItemsTable();
            syncCrrHiddenInputs();
            $('#stock-items-modal').modal('hide');
        });

        $(document).on('click', '.remove-stock-item', function() {
            $(this).closest('tr.selected-stock-row').remove();
            refreshStockItemsTable();
            syncCrrHiddenInputs();
        });

                // Tab Logic for Document Tabs
        $('.doc-tab').on('click', function() {
            var target = $(this).data('target');
            
            $('.doc-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.doc-panel').removeClass('active');
            $('#doc-panel-' + target).addClass('active');
        });

        $(document).on('click', '.po-document-link', function(e) {
            e.preventDefault();
            var pdfUrl = $(this).data('pdf-url');
            var title = $(this).data('title') || 'Combined PO documents';
            if (!pdfUrl) {
                return;
            }
            $('#pdfPreviewModalLabel').text(title);
            $('#pdf-preview-frame').attr('src', pdfUrl);
            $('#pdf-preview-modal').modal('show');
        });

        $('#pdf-preview-modal').on('hidden.bs.modal', function() {
            $('#pdf-preview-frame').attr('src', '');
        });

        var shipmentDocDropzone = $('#shipment-doc-dropzone');
        var shipmentDocFileInput = $('#shipment-doc-file-input');
        var shipmentDocList = $('#shipment-documents-list');
        var shipmentDocumentTypeOptions = @json($shipmentDocumentTypeOptions);

        function updateShipmentDocumentTabCount() {
            var count = $('#shipment-docs-scroll .doc-item').length;
            $('.doc-tab[data-target="docs"]').text('Documents (' + count + ')');
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildDocTypeSelectHtml(docId, selectedType) {
            var options = shipmentDocumentTypeOptions.slice();
            var html = '<select class="doc-type-select shipment-doc-type-select" data-doc-id="' + docId + '">';
            var hasSelected = false;
            options.forEach(function(option) {
                var selected = option === selectedType ? ' selected' : '';
                if (selected) hasSelected = true;
                html += '<option value="' + escapeHtml(option) + '"' + selected + '>' + escapeHtml(option) + '</option>';
            });
            if (selectedType && !hasSelected) {
                html += '<option value="' + escapeHtml(selectedType) + '" selected>' + escapeHtml(selectedType) + '</option>';
            }
            html += '</select>';
            return html;
        }

        function appendShipmentDocumentItem(doc) {
            var selectedType = doc.file_type || 'Unspecified';
            if (selectedType && shipmentDocumentTypeOptions.indexOf(selectedType) === -1) {
                shipmentDocumentTypeOptions.push(selectedType);
            }
            var checked = doc.is_internal ? ' checked' : '';
            var docHtml =
                '<div class="doc-item shipment-uploaded-doc" data-id="' + doc.id + '">' +
                    '<div class="doc-main">' +
                        '<a href="#" class="doc-name po-document-link" data-pdf-url="' + escapeHtml(doc.file_url) + '" data-title="' + escapeHtml(doc.file_name) + '">' +
                            escapeHtml(doc.file_name) +
                        '</a>' +
                        buildDocTypeSelectHtml(doc.id, selectedType) +
                    '</div>' +
                    '<div class="doc-side">' +
                        '<div class="doc-side-row">' +
                            '<div class="doc-internal checkbox-fade fade-in-primary">' +
                                '<label>' +
                                    '<input type="checkbox" class="doc-internal-check shipment-doc-attach-checkbox shipment-doc-internal-check" data-doc-id="' + doc.id + '"' + checked + '>' +
                                    '<span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>' +
                                '</label>' +
                            '</div>' +
                            '<i class="ti-trash doc-trash delete-shipment-document" data-id="' + doc.id + '" title="Delete"></i>' +
                        '</div>' +
                        '<span class="doc-date">' + escapeHtml(doc.date) + '</span>' +
                    '</div>' +
                '</div>';
            shipmentDocList.append(docHtml);
            updateShipmentDocumentTabCount();
        }

        $(document).on('change', '.shipment-doc-type-select', function() {
            var docId = $(this).data('doc-id');
            var fileType = $.trim($(this).val());
            if (!docId) {
                return;
            }
            if (fileType && shipmentDocumentTypeOptions.indexOf(fileType) === -1) {
                shipmentDocumentTypeOptions.push(fileType);
            }
            $.ajax({
                url: '{{ route('shipments.documents.update-type', ':docId') }}'.replace(':docId', docId),
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    file_type: fileType
                },
                error: function() {
                    alert('Could not update document type');
                }
            });
        });

        $(document).on('change', '.shipment-doc-internal-check', function() {
            var docId = $(this).data('doc-id');
            var isInternal = $(this).is(':checked') ? 1 : 0;
            if (!docId) {
                return;
            }
            $.ajax({
                url: '{{ route('shipments.documents.update-internal', ':docId') }}'.replace(':docId', docId),
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_internal: isInternal
                },
                error: function() {
                    alert('Could not update internal flag');
                }
            });
        });

        shipmentDocDropzone.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            shipmentDocFileInput.trigger('click');
        });

        shipmentDocFileInput.on('change', function() {
            handleShipmentDocFiles(this.files);
            shipmentDocFileInput.val('');
        });

        shipmentDocDropzone.on('dragover', function(e) {
            e.preventDefault();
            $(this).css({ 'border-color': '#94a3b8', 'background': '#f8fafc' });
        });

        shipmentDocDropzone.on('dragleave', function(e) {
            e.preventDefault();
            $(this).css({ 'border-color': '#cbd5e1', 'background': '#fff' });
        });

        shipmentDocDropzone.on('drop', function(e) {
            e.preventDefault();
            $(this).css({ 'border-color': '#cbd5e1', 'background': '#fff' });
            handleShipmentDocFiles(e.originalEvent.dataTransfer.files);
        });

        function handleShipmentDocFiles(files) {
            if (!files || files.length === 0) {
                return;
            }
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    alert('Only PDF files are allowed.');
                    continue;
                }
                uploadShipmentDocument(file);
            }
        }

        function uploadShipmentDocument(file) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('shipments.documents.upload', $shipment->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    appendShipmentDocumentItem(response);
                },
                error: function(xhr) {
                    var message = 'Failed to upload document.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.file) {
                        message = xhr.responseJSON.errors.file[0];
                    }
                    alert(message);
                }
            });
        }

        $(document).on('click', '.delete-shipment-document', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var docId = $(this).data('id');
            if (!docId || !confirm('Delete this document?')) {
                return;
            }
            $.ajax({
                url: '{{ route('shipments.documents.delete', ':docId') }}'.replace(':docId', docId),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    shipmentDocList.find('.shipment-uploaded-doc[data-id="' + docId + '"]').remove();
                    updateShipmentDocumentTabCount();
                },
                error: function() {
                    alert('Failed to delete document.');
                }
            });
        });

        // Unsaved changes guard
        (function() {
            var $form = $('#shipment-edit-form');
            if (!$form.length) {
                return;
            }

            var unsavedLeaveMessage = 'There are unsaved changes in the form. Are you sure you want to leave without saving?';
            var initialSnapshot = '';
            var allowLeave = false;
            var $saveBtn = $('#shipment-save-changes-btn');

            function formSnapshot() {
                syncCrrHiddenInputs();
                return $form.serialize();
            }

            function hasUnsavedChanges() {
                return !allowLeave && formSnapshot() !== initialSnapshot;
            }

            function resetBaseline() {
                initialSnapshot = formSnapshot();
                syncSaveButtonState();
            }

            function syncSaveButtonState() {
                if (!$saveBtn.length) {
                    return;
                }
                $saveBtn.prop('disabled', formSnapshot() === initialSnapshot);
            }

            window.syncShipmentSaveButtonState = syncSaveButtonState;

            function markAllowLeave() {
                allowLeave = true;
            }

            window.shipmentEditMarkAllowLeave = markAllowLeave;

            function isLeavingPage(href) {
                try {
                    var target = new URL(href, window.location.origin);
                    if (target.origin !== window.location.origin) {
                        return true;
                    }

                    return target.pathname !== window.location.pathname
                        || target.search !== window.location.search
                        || target.hash !== window.location.hash;
                } catch (error) {
                    return false;
                }
            }

            function shouldInterceptLink($link, href) {
                if (!href || href === '#' || href === '#!' || href.indexOf('javascript:') === 0) {
                    return false;
                }
                if ($link.attr('target') === '_blank' || $link.data('skipUnsavedGuard')) {
                    return false;
                }
                if ($link.hasClass('po-document-link') || $link.attr('data-toggle') === 'tab' || $link.attr('data-toggle') === 'dropdown') {
                    return false;
                }

                return isLeavingPage(href);
            }

            function confirmLeaveWithoutSaving(onConfirm) {
                if (typeof swal !== 'function') {
                    if (confirm(unsavedLeaveMessage)) {
                        onConfirm();
                    }
                    return;
                }

                swal({
                    title: 'Are you sure?',
                    text: unsavedLeaveMessage,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        onConfirm();
                    }
                });
            }

            function leaveToPreviousPage() {
                var referrer = document.referrer;
                if (referrer && isLeavingPage(referrer)) {
                    window.location.href = referrer;
                } else {
                    window.location.href = shipmentsListUrl;
                }
            }

            function proceedWithNavigation(href, linkEl) {
                markAllowLeave();
                var $logoutForm = $('#logout-form');
                if ($logoutForm.length && String($logoutForm.attr('action')) === String(href)) {
                    $logoutForm.get(0).submit();
                    return;
                }
                window.location.href = href;
            }

            function handlePotentialNavigation(linkEl) {
                if (!linkEl || !hasUnsavedChanges()) {
                    return false;
                }

                var $link = $(linkEl);
                var href = linkEl.getAttribute('href');
                if (!shouldInterceptLink($link, href)) {
                    return false;
                }

                confirmLeaveWithoutSaving(function() {
                    proceedWithNavigation(href, linkEl);
                });

                return true;
            }

            $form.on('submit', function() {
                syncCrrHiddenInputs();
                markAllowLeave();
                $saveBtn.prop('disabled', true);
            });

            $form.on('input change', ':input', syncSaveButtonState);
            $(document).on('select2:select select2:unselect select2:clear', '#shipment-edit-form .select2', syncSaveButtonState);
            $(document).on('click', '#modal-add-selected, .remove-stock-item, #btn-add-custom-field, .btn-remove-field, #add-irregularity-btn, .remove-irregularity, [id$="-leg-btn"], .remove-hand-carry-leg, .remove-sea-freight-leg, .remove-truck-leg, .remove-courier-leg, .remove-release-leg, .remove-on-board-leg', function() {
                setTimeout(syncSaveButtonState, 0);
            });

            document.addEventListener('click', function(e) {
                var linkEl = e.target.closest('a[href]');
                if (!linkEl) {
                    return;
                }

                if (!handlePotentialNavigation(linkEl)) {
                    return;
                }

                e.preventDefault();
                e.stopImmediatePropagation();
            }, true);

            $(document).on('submit', 'form', function(e) {
                var $submitForm = $(this);
                if ($submitForm.is('#shipment-edit-form') || !hasUnsavedChanges()) {
                    return;
                }

                e.preventDefault();
                var formEl = this;
                confirmLeaveWithoutSaving(function() {
                    markAllowLeave();
                    formEl.submit();
                });
            });

            $(window).on('keydown', function(e) {
                if (!hasUnsavedChanges()) {
                    return;
                }

                var isRefresh = e.key === 'F5'
                    || ((e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R'));

                if (!isRefresh) {
                    return;
                }

                e.preventDefault();
                confirmLeaveWithoutSaving(function() {
                    markAllowLeave();
                    window.location.reload();
                });
            });

            if (window.history && history.pushState) {
                history.pushState({ shipmentEditGuard: true }, document.title, window.location.href);
                $(window).on('popstate', function() {
                    if (!hasUnsavedChanges()) {
                        return;
                    }

                    history.pushState({ shipmentEditGuard: true }, document.title, window.location.href);
                    confirmLeaveWithoutSaving(function() {
                        markAllowLeave();
                        leaveToPreviousPage();
                    });
                });
            }

            resetBaseline();
        })();

        // Add small timeout to ensure loader is removed if script.js misses it
        setTimeout(function(){
            $('.theme-loader').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 500);
    });
</script>
@endsection
