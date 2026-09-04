@extends('layouts.app')

@section('styles')

    <style>
        /* Master Layout Structure Adjustments for Global Sidebar */
        .master-container {
            --shipment-input-font-size: var(--mc-control-font-size, 13px);
            --shipment-input-height: var(--mc-control-height, 34px);
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            height: calc(100vh - 4rem);
            max-height: calc(100vh - 4rem);
            background:
                radial-gradient(120% 80% at 100% 0%, rgba(0, 174, 239, 0.07), transparent 55%),
                radial-gradient(90% 60% at 0% 100%, rgba(14, 29, 74, 0.05), transparent 50%),
                #f3f6f9;
            overflow: hidden;
            margin: 0;
            width: 100%;
            min-width: 0;
        }

        /* Unified readable input typography (inputs / selects / textareas / Select2) */
        .master-container .form-control-sm-custom,
        .master-container .form-control,
        .master-container .form-control-notes,
        .master-container .comment-textarea,
        .master-container .stock-repacked-input,
        .master-container input[type="text"],
        .master-container input[type="email"],
        .master-container input[type="number"],
        .master-container input[type="tel"],
        .master-container input[type="url"],
        .master-container input[type="search"],
        .master-container input[type="password"],
        .master-container input[type="date"],
        .master-container input[type="time"],
        .master-container input:not([type]),
        .master-container select,
        .master-container textarea {
            font-size: var(--shipment-input-font-size) !important;
            line-height: 1.35 !important;
        }

        .master-container .form-control-sm-custom,
        .master-container input.form-control:not(textarea),
        .master-container select.form-control,
        .master-container select.form-control-sm-custom {
            height: var(--shipment-input-height);
            min-height: var(--shipment-input-height);
        }

        body.shipment-edit-page .select2-dropdown,
        body.shipment-edit-page .select2-results__option,
        body.shipment-edit-page .select2-search__field {
            font-size: var(--shipment-input-font-size, 13px) !important;
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
            flex: 1 1 auto;
            min-width: 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 0 12px;
            padding-bottom: 0px;
        }

        #workflow-page-body {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Summary Header */
        .summary-header {
            background:
                linear-gradient(135deg, rgba(240, 250, 251, 0.95) 0%, #ffffff 48%, #f8fafc 100%);
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            border: 1px solid rgba(0, 136, 199, 0.14);
            border-left: 4px solid #008080;
            border-radius: 12px;
            margin: 10px 0 10px;
            flex-shrink: 0;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 8px 24px rgba(14, 29, 74, 0.05);
            position: relative;
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
            html,
            body.shipment-edit-page {
                overflow-x: hidden !important;
                overflow-y: auto !important;
                height: auto !important;
            }

            body.shipment-edit-page .pcoded-content {
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
            }

            body.shipment-edit-page #app,
            body.shipment-edit-page #app > main,
            body.shipment-edit-page .main-body,
            body.shipment-edit-page .page-wrapper {
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
            }

            .master-container {
                height: auto !important;
                max-height: none !important;
                min-height: 0 !important;
                margin: 0;
                overflow: visible !important;
                flex-direction: column;
            }

            .left-sidebar {
                display: none;
            }

            .main-content-area {
                overflow: visible !important;
                padding: 0 4px 5px;
                width: 100%;
                max-width: 100%;
                flex: none !important;
                height: auto !important;
                max-height: none !important;
            }

            .shipment-right-panel {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                flex: none !important;
                height: auto !important;
                max-height: none !important;
                margin-top: 0 !important;
                border-left: none;
                border-top: 1px solid #e2e8f0;
                box-shadow: none;
                overflow: visible !important;
            }

            .shipment-right-panel > .sidebar-section.p-0 {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
                height: auto !important;
            }

            .shipment-right-panel > .sidebar-section.p-0 > .p-3 {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
                height: auto !important;
            }

            #shipment-docs-scroll,
            #shipment-log-scroll,
            #shipment-comments-scroll {
                max-height: none !important;
                overflow: visible !important;
            }

            .shipment-right-panel > .sidebar-section,
            .shipment-right-panel > .sidebar-section.p-0 {
                margin: 8px;
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

            body.shipment-edit-page .page-body:has(.master-container) {
                padding-bottom: calc(72px + env(safe-area-inset-bottom)) !important;
            }

            body.shipment-edit-page .pcoded-inner-content,
            body.shipment-edit-page .page-body {
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
                min-height: 0 !important;
            }

            .form-scroller {
                flex: none !important;
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                padding: 12px !important;
            }

            .tab-panel,
            .tab-panel.active,
            .doc-panel,
            .doc-panel.active {
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
            }

            .summary-header {
                flex-wrap: wrap;
                gap: 12px;
                padding: 12px;
                height: auto;
                align-items: flex-start;
            }

            .header-meta-group {
                flex-wrap: wrap;
                gap: 10px 16px;
                width: 100%;
            }

            .summary-header .header-actions,
            .header-actions {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: flex-start;
            }

            .header-actions > .shipment-manifest-action,
            .header-actions > #send-prealert-btn {
                flex: 1 1 calc(50% - 4px);
                min-width: 0;
                max-width: calc(50% - 4px);
            }

            .header-actions > .shipment-manifest-action {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .header-actions > .shipment-manifest-action > .btn-premium,
            .header-actions > #send-prealert-btn,
            .header-actions > #complete-prealert-btn {
                width: 100%;
            }

            .header-actions > #finalize-shipment-btn {
                flex: 1 1 100%;
                max-width: 100%;
                width: 100%;
            }

            .header-actions > #complete-prealert-btn {
                flex: 1 1 100%;
                max-width: 100%;
                width: 100%;
            }

            .header-actions .btn-premium {
                flex: unset;
                min-width: 0;
                white-space: nowrap;
                font-size: 11px;
                padding: 8px 10px;
            }

            .header-actions .manifest-rev-badge {
                position: static;
                transform: none;
                left: auto;
                top: auto;
                display: inline-block;
                margin-top: 6px;
                align-self: center;
            }

            .custom-nav-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                flex-wrap: nowrap;
                gap: 0;
                padding: 0 4px;
            }

            .nav-tab-item {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 10px 14px;
            }

            /* Stack the 3 desktop columns */
            .form-grid-3 {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
                margin-bottom: 16px;
            }

            .form-grid-3 .form-col {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            /* Nested half/third fields → full width */
            .form-grid-3 .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .form-grid-3 .row > [class*="col-"] {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .form-grid-3 .form-group-custom {
                margin-bottom: 14px;
                overflow: visible;
            }

            .form-grid-3 .form-group-custom label {
                overflow: visible;
                line-height: 1.3;
                padding-top: 1px;
            }

            .address-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            .stock-items-wrapper .stock-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                flex-wrap: nowrap;
                white-space: nowrap;
            }

            .stock-table-container,
            .table-responsive,
            #tab-prices-costs .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .content-scroll-area,
            .tab-content-scroll {
                overflow-x: hidden;
            }

            .input-with-icon .form-control-sm-custom {
                padding-right: 28px;
            }

            .form-grid-3 .select2-container {
                width: 100% !important;
            }
        }

        @media (max-width: 767.98px) {
            .meta-item {
                flex: 1 1 130px;
                min-width: 0;
            }

            .header-actions .btn-premium {
                font-size: 11px;
                padding: 8px 10px;
            }

            /* Prices / customs / notes tabs often use Bootstrap cols */
            #tab-prices-costs .row,
            #tab-customs .row,
            #tab-repacking-details .row,
            #tab-notes .row,
            #tab-milestones .row,
            .stock-panel .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            #tab-prices-costs .row > [class*="col-"],
            #tab-customs .row > [class*="col-"],
            #tab-repacking-details .row > [class*="col-"],
            #tab-notes .row > [class*="col-"],
            #tab-milestones .row > [class*="col-"] {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .stock-panel .row > [class*="col-md-"],
            .stock-panel .row > [class*="col-"] {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        .meta-item {
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
        .meta-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .meta-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .meta-value-shipment {
            color: #0e1d4a !important;
            font-size: 15px !important;
            font-weight: 800 !important;
            letter-spacing: 0.02em;
        }
        .meta-item-shipment-select {
            min-width: 240px;
            max-width: 280px;
        }
        .meta-item-shipment-select .pre-alert-shipment-input {
            height: 34px;
            min-height: 34px;
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            padding: 0 10px;
            font-size: 14px;
            font-weight: 800;
            color: #0e1d4a;
        }
        .meta-item-shipment-select .pre-alert-shipment-input::placeholder {
            font-weight: 600;
            color: #94a3b8;
        }
        .meta-item-shipment-select .pre-alert-shipment-input:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
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
        .status-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
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
        .header-inline-edit {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header-inline-edit--locked .header-inline-display {
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.55;
        }
        .workflow-page-body--hidden {
            display: none !important;
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
            position: relative;
            z-index: 30;
        }
        /* Header status/flags Select2 is attached to body so it is not clipped by
           .summary-header / .main-content-area overflow:hidden. */
        body > .select2-container--open {
            z-index: 10060 !important;
        }
        .header-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            flex-shrink: 0;
            margin-left: auto;
            position: relative;
            z-index: 1;
        }
        .header-actions .btn {
            white-space: nowrap;
        }

        .shipment-manifest-action {
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
        }

        .manifest-rev-badge {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: calc(100% + 5px);
            white-space: nowrap;
            font-size: 10px;
            font-weight: 600;
            color: #fe8a7d;
            background: #fff5f4;
            border: 1.5px solid #fe8a7d;
            border-radius: 4px;
            padding: 1px 8px;
            letter-spacing: 0.4px;
            z-index: 2;
        }

        /* Tabs Styling */
        .custom-nav-tabs {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            gap: 2px;
            padding: 4px 6px 0;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.03);
            flex-shrink: 0;
        }
        .nav-tab-item {
            padding: 11px 18px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, background 0.2s, border-color 0.2s;
            border-radius: 8px 8px 0 0;
            letter-spacing: 0.01em;
        }
        .nav-tab-item:hover {
            color: #008080;
            background: rgba(0, 128, 128, 0.05);
        }
        .nav-tab-item.active {
            background: #fff;
            color: #0e1d4a;
            border-bottom: 2px solid #00aeef;
            box-shadow: 0 -1px 0 #fff;
        }

        /* Main Form Area Grid */
        .form-scroller {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
            padding: 18px 18px 24px;
            min-height: 0;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-top: none;
            border-radius: 0;
            box-shadow: none;
        }

        /* Footer — pinned to bottom of main column (form scrolls above) */
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
            margin-top: auto;
            left: auto;
            right: auto;
            bottom: auto;
            z-index: 5;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06);
        }

        .main-content-area > .edit-footer {
            margin-top: auto;
        }

        .edit-footer .btn-save-custom {
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
        .edit-footer .btn-save-custom:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.34);
        }

        .edit-footer .btn-save-custom:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
        }

        .edit-footer .btn-cancel-custom {
            color: #008080;
            font-size: 13px;
            text-decoration: none;
            font-weight: 700;
            background: transparent;
            border: none;
            padding: 0;
            box-shadow: none;
        }

        .edit-footer .btn-cancel-custom:hover {
            text-decoration: underline;
            color: #0e1d4a;
        }
        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 24px;
            min-width: 0;
            align-items: stretch;
        }
        .form-grid-3 .form-col {
            min-width: 0;
            display: flex;
        }
        .form-grid-3 .cs-pillar {
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
        .form-grid-3 .cs-pillar--locked {
            pointer-events: none;
            background: linear-gradient(180deg, #f4f7fa 0%, #f8fafc 48%);
            opacity: 0.78;
        }
        .form-grid-3 .cs-pillar--locked::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.28);
            cursor: not-allowed;
            z-index: 8;
            pointer-events: auto;
        }
        .form-grid-3 .cs-pillar--locked .select2-container,
        .form-grid-3 .cs-pillar--locked .form-control-sm-custom,
        .form-grid-3 .cs-pillar--locked .datepicker {
            background-color: #eef2f6 !important;
        }
        .form-grid-3 .cs-field--locked {
            position: relative;
            pointer-events: none;
            opacity: 0.78;
        }
        .form-grid-3 .cs-field--locked::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.28);
            cursor: not-allowed;
            z-index: 8;
            pointer-events: auto;
        }
        .form-grid-3 .cs-field--locked textarea,
        .form-grid-3 .cs-field--locked .form-control {
            background-color: #eef2f6 !important;
        }
        .form-grid-3 .cs-pillar__title {
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
        .form-grid-3 .cs-pillar .form-group-custom {
            margin-bottom: 12px;
        }
        .stock-items-wrapper.cs-section-shell {
            margin-top: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 40%);
        }
        @media (max-width: 1399.98px) {
            .form-grid-3 {
                gap: 14px;
            }
        }
        /* Keep documents/hub panel pinned on the right; only compress the form columns */
        @media (max-width: 1199.98px) {
            .form-grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .form-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #0e1d4a;
            margin-bottom: 15px;
            padding: 0 0 8px 10px;
            border-bottom: 1px solid #eef2f7;
            border-left: 3px solid #00aeef;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.01em;
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
            font-size: var(--mc-label-font-size, 13px);
            color: #475569;
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
        }
        .form-control-sm-custom {
            height: var(--shipment-input-height, 34px);
            font-size: var(--shipment-input-font-size, 14px);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 100%;
            padding: 0 10px;
            background: #fff;
            color: #0f172a;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control-sm-custom:focus {
            outline: none;
            border-color: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.15);
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

        /* Stock Items / Service / Irregularities section */
        .stock-items-wrapper {
            margin-top: 20px;
            border: 1px solid rgba(214, 227, 238, 0.95);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 10px 28px rgba(14, 29, 74, 0.05);
        }
        .stock-tabs {
            display: flex;
            gap: 4px;
            padding: 8px 10px 0;
            background:
                linear-gradient(135deg, rgba(240, 250, 251, 0.95) 0%, #ffffff 55%, #f8fafc 100%);
            border-bottom: 1px solid #e8edf2;
        }
        .stock-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            border-radius: 10px 10px 0 0;
            border: 1px solid transparent;
            border-bottom: none;
            margin-bottom: -1px;
            transition: color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
            user-select: none;
        }
        .stock-tab:hover {
            color: #008080;
            background: rgba(255, 255, 255, 0.7);
        }
        .stock-tab.active {
            background: #fff;
            color: #0e1d4a;
            border-color: #e8edf2;
            box-shadow: 0 -2px 0 #00aeef inset;
        }
        .stock-tab__label {
            letter-spacing: 0.01em;
        }
        .stock-tab__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 20px;
            padding: 0 7px;
            border-radius: 999px;
            background: #e8f6fc;
            border: 1px solid rgba(0, 136, 199, 0.18);
            color: #0088c7;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }
        .stock-tab.active .stock-tab__count {
            background: linear-gradient(145deg, #00aeef 0%, #008080 100%);
            border-color: transparent;
            color: #fff;
        }
        .stock-tab.stock-tab--disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
        #stock-panel-service.stock-panel--locked {
            position: relative;
            pointer-events: none;
            opacity: 0.78;
        }
        #stock-panel-service.stock-panel--locked::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 0 0 14px 14px;
            background: rgba(255, 255, 255, 0.28);
            cursor: not-allowed;
            z-index: 8;
            pointer-events: auto;
        }
        #stock-panel-service.stock-panel--locked .form-control,
        #stock-panel-service.stock-panel--locked .form-control-sm-custom,
        #stock-panel-service.stock-panel--locked .stock-repacked-input {
            background-color: #eef2f6 !important;
        }
        .stock-panel {
            display: none;
            background: #fff;
        }
        .stock-panel.active {
            display: block;
            animation: mc-tab-swap 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .stock-table-container {
            padding: 0;
            min-height: 150px;
        }
        .stock-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .stock-table th {
            background: linear-gradient(180deg, #e8f6fc 0%, #f0fafd 100%);
            padding: 11px 12px;
            font-size: 11px;
            font-weight: 700;
            color: #0e1d4a;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #d6e3ee;
            white-space: nowrap;
        }
        .stock-table td {
            padding: 11px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .stock-table tbody tr:hover > td {
            background: #f8fcfd;
        }
        .stock-table tbody tr#empty-row td {
            padding: 36px 16px;
            color: #94a3b8;
            font-size: 13px;
            background: linear-gradient(180deg, #fbfdff, #f8fafc);
        }
        .stock-table a.text-primary {
            color: #008080 !important;
            font-weight: 700;
            text-decoration: none;
        }
        .stock-table a.text-primary:hover {
            color: #00aeef !important;
            text-decoration: underline;
        }
        .stock-totals {
            padding: 12px 14px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            background: linear-gradient(180deg, #f8fcfd, #f4fafb);
            border-top: 1px solid #e8edf2;
        }
        .stock-total-chip {
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
            min-width: 108px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e2eef2;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.03);
        }
        .stock-total-chip__label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1;
        }
        .stock-total-chip__value {
            font-size: 13px;
            font-weight: 800;
            color: #0e1d4a;
            line-height: 1.2;
        }
        .stock-totals .ml-auto {
            margin-left: auto;
        }
        .stock-repacked-section {
            padding: 14px 16px 16px;
            background: #fff;
            border-top: 1px solid #eef2f6;
        }
        .stock-repacked-heading {
            font-size: 11px;
            font-weight: 800;
            color: #0e1d4a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .stock-repacked-heading::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.16);
        }
        .stock-repacked-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
        .stock-repacked-field {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e8edf2;
        }
        .stock-repacked-field-label {
            margin: 0;
            font-size: var(--mc-label-font-size, 13px);
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
        }
        .stock-repacked-input {
            width: 110px;
            max-width: 100%;
            height: 30px;
            padding: 4px 8px;
            font-size: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
        }

        /* Service details panel */
        #service-details-placeholder {
            margin: 18px 16px;
            padding: 28px 20px !important;
            border-radius: 12px;
            border: 1px dashed #cfe8ee;
            background:
                linear-gradient(180deg, #f8fcfd 0%, #f3fafb 100%);
            color: #64748b !important;
            font-size: 13px;
            font-weight: 600;
        }
        #stock-panel-service [id^="service-details-"] {
            padding: 14px 14px 10px !important;
        }
        .airfreight-flight-row,
        .sea-freight-leg-row,
        .truck-leg-row,
        .courier-leg-row,
        .release-leg-row,
        .hand-carry-leg-row,
        .on-board-leg-row {
            position: relative;
            padding: 12px 44px 10px 12px;
            margin-bottom: 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, #fbfdff, #f8fafc);
            border: 1px solid #e8edf2;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.03);
        }

        .airfreight-flight-row .flight-remove-btn,
        .sea-freight-leg-row .sea-leg-remove-btn,
        .truck-leg-row .truck-leg-remove-btn,
        .courier-leg-row .courier-leg-remove-btn,
        .release-leg-row .release-leg-remove-btn,
        .on-board-leg-row .on-board-leg-remove-btn,
        .hand-carry-leg-row .hand-carry-leg-remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            flex: none !important;
            width: 28px;
            height: 28px;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #94a3b8 !important;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
            line-height: 1 !important;
            text-decoration: none !important;
            transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .airfreight-flight-row .flight-remove-btn i,
        .sea-freight-leg-row .sea-leg-remove-btn i,
        .truck-leg-row .truck-leg-remove-btn i,
        .courier-leg-row .courier-leg-remove-btn i,
        .release-leg-row .release-leg-remove-btn i,
        .on-board-leg-row .on-board-leg-remove-btn i,
        .hand-carry-leg-row .hand-carry-leg-remove-btn i {
            font-size: 12px !important;
            line-height: 1 !important;
            color: inherit !important;
        }

        .airfreight-flight-row .flight-remove-btn:hover,
        .sea-freight-leg-row .sea-leg-remove-btn:hover,
        .truck-leg-row .truck-leg-remove-btn:hover,
        .courier-leg-row .courier-leg-remove-btn:hover,
        .release-leg-row .release-leg-remove-btn:hover,
        .on-board-leg-row .on-board-leg-remove-btn:hover,
        .hand-carry-leg-row .hand-carry-leg-remove-btn:hover {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.12);
        }
        #stock-panel-service .d-flex.justify-content-end.pt-2 a.text-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 8px;
            background: #e8f6fc;
            border: 1px solid rgba(0, 136, 199, 0.18);
            color: #0088c7 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        #stock-panel-service .d-flex.justify-content-end.pt-2 a.text-primary:hover {
            background: #dff3fb;
            color: #006f9e !important;
            border-color: rgba(0, 136, 199, 0.28);
            text-decoration: none !important;
        }

        /* Irregularities panel */
        #stock-panel-irregularities #irregularities-container {
            padding: 14px !important;
        }
        .irregularity-item {
            padding: 14px 14px 10px;
            margin-bottom: 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            border: 1px solid #e8edf2;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.03);
            border-bottom: 1px solid #e8edf2 !important;
        }
        .irregularity-item:last-child {
            margin-bottom: 0;
        }
        #stock-panel-irregularities > .d-flex.justify-content-end {
            padding: 0 14px 14px !important;
            border-top: 1px solid #eef2f6;
            background: linear-gradient(180deg, #f8fcfd, #fff);
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
        #add-airfreight-flight-btn:hover,
        #add-sea-freight-leg-btn:hover,
        #add-truck-leg-btn:hover,
        #add-courier-leg-btn:hover,
        #add-release-leg-btn:hover,
        #add-on-board-leg-btn:hover,
        #add-hand-carry-leg-btn:hover {
            text-decoration: none !important;
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
        /* Right Sidebar — always pinned to the right of main content on desktop */
        .shipment-right-panel {
            --shipment-right-font: 13px;
            --shipment-right-font-sm: 12px;
            --shipment-right-font-xs: 11px;
            width: 400px;
            min-width: 400px;
            max-width: 400px;
            background:
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            flex: 0 0 400px;
            position: relative;
            z-index: 20;
            overflow: hidden;
            align-self: stretch;
            height: 100%;
            box-shadow: -6px 0 18px rgba(14, 29, 74, 0.04);
            font-size: var(--shipment-right-font);
        }
        .shipment-right-panel > .sidebar-section {
            flex-shrink: 0;
        }
        .shipment-right-panel > .sidebar-section.p-0 {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            margin: 0 12px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(14, 29, 74, 0.04);
        }
        .shipment-right-panel > .sidebar-section.p-0 > .p-3 {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar-section {
            padding: 16px 18px;
            border-bottom: none;
            overflow: visible;
            margin: 12px 12px 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(14, 29, 74, 0.03);
        }
        .sidebar-title {
            font-size: var(--shipment-right-font-sm, 12px);
            font-weight: 800;
            color: #0e1d4a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sidebar-title::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.18);
            flex-shrink: 0;
        }
        .customer-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: var(--shipment-right-font, 13px);
            color: #008080;
            margin-bottom: 8px;
            font-weight: 600;
            padding: 8px 10px;
            border-radius: 8px;
            background: linear-gradient(180deg, #f8fcfd, #f0fafb);
            border: 1px solid #d9eef0;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        .customer-item:hover,
        .customer-item.is-active {
            background: #e6f7f8;
            border-color: #b6e4e8;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(14, 29, 74, 0.06);
        }
        .customer-item i {
            color: #00aeef;
            font-size: 13px;
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
            font-size: var(--shipment-right-font-xs, 11px);
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
            font-size: var(--shipment-right-font-xs, 11px);
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
            background: linear-gradient(180deg, #f8fafc, #fff);
            padding: 0 4px;
            gap: 2px;
        }
        .doc-tab {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
            font-size: var(--shipment-right-font, 13px);
            font-weight: 700;
            color: #94a3b8;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color 0.15s ease, border-color 0.15s ease;
        }
        .doc-tab:hover {
            color: #008080;
        }
        .doc-tab.active {
            color: #0e1d4a;
            border-bottom: 2px solid #00aeef;
        }

        #doc-panel-docs .shipment-docs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-right: 4px;
        }

        #doc-panel-docs .shipment-docs-header span {
            font-size: var(--shipment-right-font-sm, 12px);
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
            font-size: var(--shipment-right-font, 13px);
            font-weight: 600;
            color: #008080 !important;
            line-height: 18px;
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
            font-size: var(--shipment-right-font, 13px);
        }

        #doc-panel-docs .doc-type-select,
        #doc-panel-docs .shipment-doc-type-select {
            display: block;
            margin-top: 4px;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: var(--shipment-right-font-sm, 12px);
            font-weight: 500;
            padding: 0;
            max-width: 100%;
            width: 100%;
            height: auto;
            cursor: pointer;
            appearance: auto;
        }

        #doc-panel-docs .select2-container {
            display: block;
            margin-top: 4px;
            width: 100% !important;
        }

        #doc-panel-docs .select2-container .select2-selection--single {
            border: 0 !important;
            background: transparent !important;
            height: 18px !important;
            min-height: 18px !important;
            outline: none;
        }

        #doc-panel-docs .select2-container .select2-selection--single .select2-selection__rendered {
            color: #64748b;
            font-size: var(--shipment-input-font-size, 13px);
            font-weight: 500;
            line-height: 18px;
            padding: 0 16px 0 0;
        }

        #doc-panel-docs .select2-container .select2-selection--single .select2-selection__arrow {
            height: 18px;
            width: 14px;
            right: 0;
            top: 0;
        }

        .select2-dropdown.doc-type-select2-dropdown {
            z-index: 20060;
            min-width: 220px;
            font-size: var(--shipment-input-font-size, 13px);
        }

        .select2-dropdown.doc-type-select2-dropdown .select2-results__options {
            max-height: 280px;
        }

        #doc-panel-docs .doc-type-label {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: var(--shipment-right-font-sm, 12px);
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
            font-size: var(--shipment-right-font-xs, 11px);
            color: #94a3b8;
            line-height: 1.2;
            white-space: nowrap;
            text-align: right;
            min-height: 14px;
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
            font-size: var(--shipment-right-font, 13px);
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
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            letter-spacing: 0.01em;
        }
        .btn-outline-custom {
            border: 1px solid #d1d5db;
            color: #334155;
            background: #fff;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
        }
        .btn-outline-custom:hover {
            border-color: #00aeef;
            color: #0e1d4a;
            background: #f0faff;
        }
        #send-manifest-btn.send-manifest-pending,
        #send-prealert-btn.send-prealert-pending,
        #complete-prealert-btn.complete-prealert-pending {
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 55%, #115e59 100%) !important;
            border: 2px solid rgba(255, 255, 255, 0.55) !important;
            color: #fff !important;
            box-shadow:
                0 0 0 4px rgba(13, 148, 136, 0.18),
                0 6px 16px rgba(15, 118, 110, 0.35);
            animation: send-mail-btn-pulse 1.8s ease-in-out infinite;
            overflow: hidden;
            transform-origin: center;
        }
        #send-manifest-btn.send-manifest-pending::before,
        #send-prealert-btn.send-prealert-pending::before,
        #complete-prealert-btn.complete-prealert-pending::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                110deg,
                transparent 0%,
                transparent 40%,
                rgba(255, 255, 255, 0.35) 50%,
                transparent 60%,
                transparent 100%
            );
            transform: translateX(-120%);
            animation: send-mail-btn-shine 2.4s ease-in-out infinite;
            pointer-events: none;
        }
        #send-manifest-btn.send-manifest-pending::after,
        #send-prealert-btn.send-prealert-pending::after,
        #complete-prealert-btn.complete-prealert-pending::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 10px;
            border: 2px solid rgba(153, 246, 228, 0.55);
            box-shadow: 0 0 12px rgba(45, 212, 191, 0.35);
            animation: send-mail-btn-ring 1.8s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }
        #send-manifest-btn.send-manifest-pending:hover,
        #send-prealert-btn.send-prealert-pending:hover,
        #complete-prealert-btn.complete-prealert-pending:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%) !important;
            border-color: rgba(255, 255, 255, 0.7) !important;
            color: #fff !important;
        }
        #send-manifest-btn:disabled,
        #send-prealert-btn:disabled,
        #complete-prealert-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
            animation: none;
            transform: none;
        }
        #send-manifest-btn:disabled::before,
        #send-prealert-btn:disabled::before,
        #complete-prealert-btn:disabled::before,
        #send-manifest-btn:disabled::after,
        #send-prealert-btn:disabled::after,
        #complete-prealert-btn:disabled::after {
            display: none;
        }
        @keyframes send-mail-btn-pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow:
                    0 0 0 4px rgba(13, 148, 136, 0.16),
                    0 6px 14px rgba(15, 118, 110, 0.28);
            }
            50% {
                transform: scale(1.04);
                box-shadow:
                    0 0 0 7px rgba(13, 148, 136, 0.1),
                    0 8px 20px rgba(15, 118, 110, 0.4);
            }
        }
        @keyframes send-mail-btn-shine {
            0%, 35% { transform: translateX(-120%); }
            65%, 100% { transform: translateX(120%); }
        }
        @keyframes send-mail-btn-ring {
            0%, 100% {
                opacity: 0.7;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.03);
            }
        }
        /* Select2 Style Overrides - Robust Reset for Background */
        .master-container .select2-container--default .select2-selection--single,
        .master-container .select2-container--default .select2-selection--multiple,
        .master-container .select2-container--default.select2-container--focus .select2-selection--single,
        .master-container .select2-container--default.select2-container--focus .select2-selection--multiple {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            height: var(--shipment-input-height, 34px) !important;
            min-height: var(--shipment-input-height, 34px) !important;
            transition: all 0.2s;
        }

        .master-container .select2-container--default .select2-selection--single:hover,
        .master-container .select2-container--default .select2-selection--multiple:hover {
            border-color: #008080 !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__rendered,
        .master-container .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #0f172a !important;
            line-height: 1.25 !important;
            font-size: var(--shipment-input-font-size, 13px) !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-left: 10px !important;
            padding-right: 24px !important;
            height: 100% !important;
            background-color: transparent !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            display: flex !important;
            align-items: center !important;
            max-width: 100%;
            box-sizing: border-box !important;
        }
        .master-container .select2-container--default .select2-selection--single {
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
        }
        .master-container .select2-selection__rendered .mc-country-option {
            max-width: 100%;
            overflow: hidden;
        }
        .master-container .select2-selection__rendered .mc-country-option__label {
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: var(--shipment-input-font-size, 13px) !important;
        }
        /* Prevent native <select> from showing under Select2 (layout leak) */
        .master-container select.select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            white-space: nowrap !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
            font-size: var(--shipment-input-font-size, 13px) !important;
        }

        .master-container .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
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
            font-size: 12px !important;
            border-radius: 2px !important;
            padding: 1px 6px !important;
            margin-top: 4px !important;
        }

        .tab-panel, .doc-panel {
            display: none;
        }
        .tab-panel.active, .doc-panel.active {
            display: block;
            animation: mc-tab-swap 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
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

        @media (min-width: 992px) {
            .pcoded-content {
                overflow: hidden !important;
            }

            body.shipment-edit-page {
                overflow: hidden !important;
            }
        }

        .main-body .page-wrapper {
            padding: 0 !important;
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
            font-size: var(--shipment-right-font, 13px);
            font-weight: 700;
            margin-bottom: 2px;
            display: block;
        }
        .log-desc {
            color: #374151;
            font-size: var(--shipment-right-font-sm, 12px);
            line-height: 1.4;
        }
        .log-user {
            color: #111827;
            font-size: var(--shipment-right-font-sm, 12px);
            font-weight: 500;
            display: block;
        }
        .log-timestamp {
            color: #9ca3af;
            font-size: var(--shipment-right-font-xs, 11px);
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
            font-size: var(--shipment-right-font-sm, 12px);
        }
        .comment-time {
            color: #9ca3af;
            font-size: var(--shipment-right-font-sm, 12px);
        }
        .comment-body {
            color: #111827;
            font-size: var(--shipment-right-font, 13px);
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
            font-size: var(--shipment-right-font, 13px);
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
            font-size: var(--shipment-right-font-sm, 12px);
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
            font-size: var(--shipment-right-font-sm, 12px);
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
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            font-size: 13px !important;
            line-height: 1.25 !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            background-color: transparent !important;
            background: transparent !important;
            color: #4b5563 !important;
        }
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }
        #stock-items-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
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
            min-width: 1100px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        @media (min-width: 1200px) {
            #stock-items-modal .office-table {
                min-width: 1500px;
            }
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

        /* Compose New Message modal (Send manifest) */
        #compose-manifest-mail-modal .modal-dialog {
            max-width: 860px;
            margin: 1.75rem auto;
        }
        #compose-manifest-mail-modal .modal-content {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
        }
        #compose-manifest-mail-modal .compose-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        #compose-manifest-mail-modal .compose-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }
        #compose-manifest-mail-modal .compose-body {
            padding: 16px 20px 8px;
        }
        #compose-manifest-mail-modal .compose-field {
            margin-bottom: 10px;
        }
        #compose-manifest-mail-modal .compose-field-with-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #compose-manifest-mail-modal .compose-field-with-icon .compose-input {
            flex: 1;
            min-width: 0;
        }
        #compose-manifest-mail-modal .compose-contact-btn {
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
        #compose-manifest-mail-modal .compose-contact-btn:hover,
        #compose-manifest-mail-modal .compose-contact-btn.active {
            background: #e0f2fe;
            border-color: #93c5fd;
            color: #0369a1;
        }
        #compose-manifest-mail-modal .compose-contact-btn i {
            font-size: 15px;
            line-height: 1;
        }
        #compose-manifest-mail-modal .compose-contact-picker {
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
        #compose-manifest-mail-modal .compose-field-contact {
            position: relative;
        }
        #compose-manifest-mail-modal .compose-contact-picker.open {
            display: block;
        }
        #compose-manifest-mail-modal .compose-contact-search {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 12px;
            font-size: 12px;
            outline: none;
        }
        #compose-manifest-mail-modal .compose-contact-list {
            max-height: 220px;
            overflow-y: auto;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        #compose-manifest-mail-modal .compose-contact-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }
        #compose-manifest-mail-modal .compose-contact-item:hover {
            background: #f0f9ff;
        }
        #compose-manifest-mail-modal .compose-contact-name {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        #compose-manifest-mail-modal .compose-contact-email {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0;
        }
        #compose-manifest-mail-modal .compose-contact-empty {
            padding: 14px 12px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        #compose-manifest-mail-modal .compose-input {
            width: 100%;
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 5px 12px;
            font-size: 13px;
            color: #111827;
            background: #fff;
        }
        #compose-manifest-mail-modal .compose-input:focus {
            outline: none;
            border-color: #93c5fd;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }
        #compose-manifest-mail-modal .compose-input::placeholder {
            color: #9ca3af;
        }
        #compose-manifest-mail-modal .compose-editor-wrap {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }
        #compose-manifest-mail-modal .compose-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2px;
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }
        #compose-manifest-mail-modal .compose-toolbar select {
            height: 28px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            font-size: 12px;
            padding: 0 6px;
            margin-right: 4px;
            background: #fff;
            color: #374151;
        }
        #compose-manifest-mail-modal .compose-tool-btn {
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
        #compose-manifest-mail-modal .compose-tool-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }
        @include('partials.compose-color-tools-styles')
        #compose-manifest-mail-modal .compose-editor {
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
        #compose-manifest-mail-modal .compose-editor table {
            width: 100%;
            border-collapse: collapse;
            font-size: inherit !important;
            font-family: inherit !important;
            line-height: inherit !important;
            margin: 8px 0;
            white-space: normal;
        }
        #compose-manifest-mail-modal .compose-editor th,
        #compose-manifest-mail-modal .compose-editor td {
            border: 0.5px solid #ccc;
            padding: 5px 4px;
            text-align: left;
            vertical-align: top;
            font-size: inherit !important;
            font-family: inherit !important;
            line-height: inherit !important;
        }
        #compose-manifest-mail-modal .compose-editor th {
            background: #f3f4f6;
            font-weight: bold;
        }
        #compose-manifest-mail-modal .compose-editor:empty:before {
            content: attr(data-placeholder);
            color: #9ca3af;
            pointer-events: none;
        }
        #compose-manifest-mail-modal .compose-attach-row {
            margin-top: 14px;
        }
        #compose-manifest-mail-modal .btn-compose-attach {
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
        #compose-manifest-mail-modal .btn-compose-attach:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
        #compose-manifest-mail-modal .compose-attach-hint {
            margin: 6px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }
        #compose-manifest-mail-modal .compose-attach-previews {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }
        #compose-manifest-mail-modal .compose-attach-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #f8fafc;
            overflow: hidden;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
            position: relative;
        }
        #compose-manifest-mail-modal .compose-attach-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        }
        #compose-manifest-mail-modal .compose-attach-remove {
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
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        #compose-manifest-mail-modal .compose-attach-remove:hover {
            background: #dc2626;
        }
        #compose-manifest-mail-modal .compose-attach-thumb {
            height: 90px;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        #compose-manifest-mail-modal .compose-attach-thumb iframe,
        #compose-manifest-mail-modal .compose-attach-thumb img {
            width: 100%;
            height: 100%;
            border: 0;
            object-fit: cover;
            background: #fff;
            pointer-events: none;
        }
        #compose-manifest-mail-modal .compose-attach-thumb .attach-icon {
            font-size: 36px;
            color: #64748b;
        }
        #compose-manifest-mail-modal .compose-attach-meta {
            padding: 8px 10px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        #compose-manifest-mail-modal .compose-attach-name {
            font-size: 11px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        #compose-manifest-mail-modal .compose-attach-type {
            font-size: 10px;
            color: #6b7280;
            margin: 2px 0 0;
        }
        #compose-manifest-mail-modal .compose-attach-empty {
            margin-top: 10px;
            font-size: 12px;
            color: #9ca3af;
        }
        #compose-manifest-mail-modal .compose-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px 18px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        #compose-manifest-mail-modal .compose-footer-right {
            display: flex;
            gap: 8px;
        }
        #compose-manifest-mail-modal .compose-input.compose-input-readonly {
            background: #f8fafc;
            color: #475569;
            cursor: default;
        }
        #compose-manifest-mail-modal .compose-from-hint {
            margin: -4px 0 10px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }
        #compose-manifest-mail-modal .btn-compose-discard {
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
        #compose-manifest-mail-modal .btn-compose-discard:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
        }
        #compose-manifest-mail-modal .btn-compose-discard:disabled {
            opacity: .7;
            cursor: not-allowed;
        }
        #compose-manifest-mail-modal .btn-compose-draft {
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
        }
        #compose-manifest-mail-modal .btn-compose-draft:hover {
            background: #e5e7eb;
            color: #111827;
        }
        #compose-manifest-mail-modal .btn-compose-send {
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
        }
        #compose-manifest-mail-modal .btn-compose-send:hover {
            background: #0d9488;
            border-color: #0d9488;
            color: #fff;
        }
        #compose-manifest-mail-modal .btn-compose-send:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        #compose-manifest-mail-modal .modal-content {
            position: relative;
        }
        #compose-manifest-mail-modal .compose-send-loader {
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
        #compose-manifest-mail-modal.compose-sending .compose-send-loader {
            display: flex;
        }
        #compose-manifest-mail-modal .compose-send-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid #ccfbf1;
            border-top-color: #14b8a6;
            border-radius: 50%;
            animation: compose-spin 0.75s linear infinite;
        }
        #compose-manifest-mail-modal .compose-send-loader-text {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #0f766e;
        }
        @keyframes compose-spin {
            to { transform: rotate(360deg); }
        }

        /*
         * Service details — must come AFTER desktop flex rules so cascade wins.
         * Each service has different fields; all leg rows stack on mobile.
         */
        @media (max-width: 991.98px) {
            #styleSelector {
                display: none !important;
            }

            #service-details-airfreight,
            #service-details-sea-freight,
            #service-details-truck,
            #service-details-courier,
            #service-details-release,
            #service-details-hand-carry,
            #service-details-on-board {
                overflow-x: hidden;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .airfreight-flight-row,
            .sea-freight-leg-row,
            .truck-leg-row,
            .courier-leg-row,
            .release-leg-row,
            .on-board-leg-row,
            .hand-carry-leg-row {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: flex-start !important;
                gap: 12px 10px !important;
                padding: 12px 44px 12px 12px !important;
                margin-bottom: 12px !important;
                border: 1px solid #e8edf2 !important;
                border-radius: 12px !important;
                position: relative;
            }

            /* Airfreight: Airway bill / Flight / Dep / Arr / Time */
            .airfreight-flight-row > .flight-field,
            .airfreight-flight-row > .flight-field-time,
            /* Sea freight: BOL / Container / IMO / Vessel / ETD / ETA / Time */
            .sea-freight-leg-row > .sea-leg-field,
            .sea-freight-leg-row > .sea-leg-field-time,
            /* Truck: CMR / Freight co / Dep / Arr / Time */
            .truck-leg-row > .truck-leg-field,
            .truck-leg-row > .truck-leg-field-time,
            /* Courier: AWB / Carrier / Dep / Arr / Time */
            .courier-leg-row > .courier-leg-field,
            .courier-leg-row > .courier-leg-field-time,
            /* Release: Freight co / Delivery date / Time */
            .release-leg-row > .release-leg-field,
            .release-leg-row > .release-leg-field-time,
            /* On-board: Dep / Delivery date / Time */
            .on-board-leg-row > .on-board-leg-field,
            .on-board-leg-row > .on-board-leg-field-time,
            /* Hand carry: Dep / Arr / Time / Contact name / phone / checkbox */
            .hand-carry-leg-row > .hand-carry-leg-field,
            .hand-carry-leg-row > .hand-carry-leg-field-time {
                flex: 1 1 calc(50% - 5px) !important;
                min-width: calc(50% - 5px) !important;
                max-width: calc(50% - 5px) !important;
                width: auto !important;
            }

            .hand-carry-leg-row > .hand-carry-leg-checkbox {
                flex: 1 1 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                margin-top: 4px;
            }

            .airfreight-flight-row label,
            .sea-freight-leg-row label,
            .truck-leg-row label,
            .courier-leg-row label,
            .release-leg-row label,
            .on-board-leg-row label,
            .hand-carry-leg-row label {
                white-space: normal !important;
                overflow: visible !important;
                line-height: 1.25 !important;
                word-break: normal !important;
                overflow-wrap: anywhere;
            }

            .airfreight-flight-row .flight-remove-btn,
            .sea-freight-leg-row .sea-leg-remove-btn,
            .truck-leg-row .truck-leg-remove-btn,
            .courier-leg-row .courier-leg-remove-btn,
            .release-leg-row .release-leg-remove-btn,
            .on-board-leg-row .on-board-leg-remove-btn,
            .hand-carry-leg-row .hand-carry-leg-remove-btn {
                position: absolute !important;
                top: 10px !important;
                right: 10px !important;
                margin: 0 !important;
                flex: none !important;
            }

            .airfreight-flight-row .form-control-sm-custom,
            .sea-freight-leg-row .form-control-sm-custom,
            .truck-leg-row .form-control-sm-custom,
            .courier-leg-row .form-control-sm-custom,
            .release-leg-row .form-control-sm-custom,
            .on-board-leg-row .form-control-sm-custom,
            .hand-carry-leg-row .form-control-sm-custom,
            .airfreight-flight-row .input-with-icon,
            .sea-freight-leg-row .input-with-icon,
            .truck-leg-row .input-with-icon,
            .courier-leg-row .input-with-icon,
            .release-leg-row .input-with-icon,
            .on-board-leg-row .input-with-icon,
            .hand-carry-leg-row .input-with-icon {
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        @media (max-width: 767.98px) {
            .airfreight-flight-row > .flight-field,
            .airfreight-flight-row > .flight-field-time,
            .sea-freight-leg-row > .sea-leg-field,
            .sea-freight-leg-row > .sea-leg-field-time,
            .truck-leg-row > .truck-leg-field,
            .truck-leg-row > .truck-leg-field-time,
            .courier-leg-row > .courier-leg-field,
            .courier-leg-row > .courier-leg-field-time,
            .release-leg-row > .release-leg-field,
            .release-leg-row > .release-leg-field-time,
            .on-board-leg-row > .on-board-leg-field,
            .on-board-leg-row > .on-board-leg-field-time,
            .hand-carry-leg-row > .hand-carry-leg-field,
            .hand-carry-leg-row > .hand-carry-leg-field-time,
            .hand-carry-leg-row > .hand-carry-leg-checkbox {
                flex: 1 1 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }
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

@include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])
                                    @php
                                        $createPreAlertMode = $createPreAlertMode ?? false;
                                        $transitMode = $transitMode ?? false;
                                        $workflowEditMode = $createPreAlertMode || $transitMode;
                                        $workflowPageUrl = $createPreAlertMode ? route('create-pre-alert') : ($transitMode ? route('transit') : '');
                                        $workflowReturnTo = $createPreAlertMode ? 'create-pre-alert' : ($transitMode ? 'transit' : '');
                                        $shipmentRouteId = (int) ($shipment->getKey() ?? 0);
                                        $workflowAwaitingShipment = $workflowEditMode && $shipmentRouteId <= 0;
                                    @endphp
                                    <div class="master-container">
                                        <!-- Main Content Area -->
                                        <div class="main-content-area">
                                            <!-- Summary Header -->
                                            <div class="summary-header">
                                                <input type="hidden" id="shipment-current-status" value="{{ $shipment->status }}">
                                                <div class="summary-header-main">
                                                    <span class="summary-header-icon" aria-hidden="true">
                                                        <i class="icofont icofont-ship"></i>
                                                    </span>
                                                <div class="header-meta-group">
                                                    <div class="meta-item{{ $workflowEditMode ? ' meta-item-shipment-select' : '' }}">
                                                        <span class="meta-label">Shipment no</span>
                                                        @if ($workflowEditMode)
                                                            <input type="text"
                                                                id="pre-alert-shipment-input"
                                                                name="pre_alert_shipment_no"
                                                                class="form-control-sm-custom pre-alert-shipment-input"
                                                                value="{{ $shipment->shipment_number }}"
                                                                placeholder="Shipment no"
                                                                autocomplete="off">
                                                        @else
                                                            <span class="meta-value meta-value-shipment">{{ $shipment->shipment_number }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Creation date</span>
                                                        <span class="meta-value meta-value-with-icon">
                                                            <i class="ti-calendar meta-cal-icon" aria-hidden="true"></i>
                                                            <span>{{ $shipment->created_at?->format('d.m.Y') ?? '—' }}</span>
                                                        </span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Created by</span>
                                                        <span class="meta-value text-primary">{{ $shipment->creator?->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Account manager</span>
                                                        <span class="meta-value text-primary">{{ $shipment->accountManager?->name ?? '—' }}</span>
                                                    </div>
                                                    @php
                                                        $shipmentFlags = array_values(array_intersect(
                                                            $shipment->flags ?? \App\Models\Shipment::defaultFlags(),
                                                            \App\Models\Shipment::availableFlags()
                                                        ));
                                                        $selectedShipmentFlag = $shipmentFlags[0] ?? null;
                                                    @endphp
                                                    @unless ($workflowEditMode)
                                                    <div class="meta-item">
                                                        <span class="meta-label">Flags</span>
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
                                                    @endunless
                                                    <div class="meta-item">
                                                        <span class="meta-label">Status</span>
                                                        <div class="header-inline-edit{{ $workflowAwaitingShipment ? ' header-inline-edit--locked' : '' }}" id="status-edit-container">
                                                            <div class="header-inline-display status-display">
                                                                @if ($shipment->status)
                                                                    <span class="status-badge {{ $shipment->statusBadgeClass() }}">{{ $shipment->status }}</span>
                                                                @else
                                                                    <span class="text-muted" style="font-size: 11px; font-weight: 500;">—</span>
                                                                @endif
                                                                <i class="ti-pencil-alt" style="color: #64748b; font-size: 15px; cursor: pointer;"></i>
                                                            </div>
                                                            <div class="header-inline-select status-select-wrapper">
                                                                <select class="select2-status-inline" name="header_status">
                                                                    @php
                                                                        // Manual picker: In process is create default (legacy-only in picker if current).
                                                                        $manualShipmentStatuses = ['In transit', 'Delivered', 'Completed', 'Cancelled'];
                                                                        $headerStatusOptions = $manualShipmentStatuses;
                                                                        if ($shipment->status && ! in_array($shipment->status, $manualShipmentStatuses, true)) {
                                                                            array_unshift($headerStatusOptions, $shipment->status);
                                                                        }
                                                                    @endphp
                                                                    @foreach ($headerStatusOptions as $statusOption)
                                                                        <option value="{{ $statusOption }}" {{ $shipment->status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="header-actions">
                                                    @php
                                                        $manifestMailPending = $shipment->needsManifestMailSend();
                                                        $preAlertMailPending = $shipment->needsPreAlertMailSend();
                                                        $hasPreAlertPdf = $shipmentRouteId > 0 && $shipment->preAlerts->isNotEmpty();
                                                        $canCompletePreAlert = $hasPreAlertPdf && $shipment->status === 'In process';
                                                    @endphp
                                                    @php
                                                        $latestManifest = $shipment->latestManifest();
                                                        $manifestRevision = ($latestManifest && $latestManifest->version >= 2) ? ($latestManifest->version - 1) : null;
                                                    @endphp
                                                    @unless ($workflowEditMode)
                                                    <div class="shipment-manifest-action">
                                                        <button type="button"
                                                            id="send-manifest-btn"
                                                            class="btn btn-premium btn-outline-custom{{ $manifestMailPending ? ' send-manifest-pending' : '' }}"
                                                            data-eml-url="{{ route('shipments.manifest-mail', $shipmentRouteId) }}"
                                                            data-eml-filename="manifest-mail-{{ $shipment->shipment_number }}.eml"
                                                            data-manifest-mail-pending="{{ $manifestMailPending ? '1' : '0' }}"
                                                            @disabled(! $manifestMailPending)
                                                            title="{{ $manifestMailPending ? 'New manifest ready — send email' : 'Send manifest after a new manifest is generated' }}">Send manifest</button>
                                                        @if($manifestRevision)
                                                            <span class="manifest-rev-badge">MI Rev - {{ $manifestRevision }}</span>
                                                        @endif
                                                    </div>
                                                    @endunless
                                                    @if ($transitMode)
                                                    <button type="button"
                                                        id="finalize-shipment-btn"
                                                        class="btn btn-premium btn-outline-custom"
                                                        @disabled($workflowAwaitingShipment || !empty($transitDestinationStocksReady))
                                                        title="{{ $workflowAwaitingShipment ? 'Load a shipment first' : (!empty($transitDestinationStocksReady) ? 'Destination stocks already created' : '') }}">Transit</button>
                                                    @else
                                                    <button type="button"
                                                        id="send-prealert-btn"
                                                        class="btn btn-premium btn-outline-custom{{ $preAlertMailPending ? ' send-prealert-pending' : '' }}"
                                                        data-pre-alert-mail-pending="{{ $preAlertMailPending ? '1' : '0' }}"
                                                        @disabled($workflowAwaitingShipment || ! $preAlertMailPending)
                                                        title="{{ $workflowAwaitingShipment ? 'Load a shipment first' : ($preAlertMailPending ? 'New pre-alert ready — send email' : 'Send pre-alert after a new pre-alert is generated') }}">Send pre-alert</button>
                                                    <button type="button"
                                                        id="complete-prealert-btn"
                                                        class="btn btn-premium btn-outline-custom"
                                                        data-has-pre-alert-pdf="{{ $hasPreAlertPdf ? '1' : '0' }}"
                                                        data-pre-alert-completed="{{ $canCompletePreAlert ? '0' : ($hasPreAlertPdf ? '1' : '0') }}"
                                                        @disabled($workflowAwaitingShipment || ! $canCompletePreAlert)
                                                        title="{{ $workflowAwaitingShipment ? 'Load a shipment first' : (! $hasPreAlertPdf ? 'Generate a pre-alert PDF before completing' : ($canCompletePreAlert ? 'Complete pre-alert and set shipment to In transit' : 'Pre-alert already completed')) }}">Complete Pre alert</button>
                                                    @endif
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

                                            <div id="workflow-page-body" class="{{ $workflowAwaitingShipment ? 'workflow-page-body--hidden' : '' }}">
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
                                                <form id="shipment-edit-form" method="POST" action="{{ $shipmentRouteId ? route('shipments.update', $shipmentRouteId) : url()->current() }}" data-mc-skip-save-state>
                                                    @csrf
                                                    @method('PUT')
                                                    @if ($workflowReturnTo)
                                                        <input type="hidden" name="return_to" value="{{ $workflowReturnTo }}">
                                                    @endif
                                                    <input type="hidden" name="account_manager" value="{{ $shipment->account_manager_id }}">
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
                                            </div>{{-- #workflow-page-body --}}

                                            <div class="edit-footer">
                                                <button type="submit" form="shipment-edit-form" id="shipment-save-changes-btn" class="btn-save-custom" disabled>Save changes</button>
                                                <a href="{{ route('shipments') }}" class="btn-cancel-custom">Cancel</a>
                                            </div>
                                        </div>

                                        <!-- Right Sidebar -->
                                        <div class="shipment-right-panel{{ $workflowAwaitingShipment ? ' workflow-page-body--hidden' : '' }}">
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
                                                        <div id="shipment-manifests-list">
                                                        @foreach ($shipment->manifests as $manifest)
                                                        <div class="doc-item shipment-manifest-doc" data-id="{{ $manifest->id }}">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ route('shipments.manifests.show', [$shipmentRouteId, $manifest->id]) }}"
                                                                   data-title="{{ $manifest->displayLabel() }}">
                                                                    {{ ucfirst($manifest->displayLabel()) }}
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
                                                                   data-pdf-url="{{ route('shipments.pre-alerts.show', [$shipmentRouteId, $preAlert->id]) }}"
                                                                   data-title="{{ $preAlert->displayLabel() }}">
                                                                    {{ ucfirst($preAlert->displayLabel()) }}
                                                                </a>
                                                                <span class="doc-type-label">Pre-alert</span>
                                                        </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox" class="doc-internal-check">
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
                                                        @if ($combinedPoDocuments->count() > 0)
                                                        <div class="doc-item shipment-combined-po-doc" id="shipment-combined-po-doc">
                                                            <div class="doc-main">
                                                                <a href="#"
                                                                   class="doc-name po-document-link"
                                                                   data-pdf-url="{{ route('shipments.combined-po-documents', $shipmentRouteId) }}"
                                                                   data-title="Combined PO documents">
                                                                    Combined PO documents
                                                                </a>
                                                                <span class="doc-type-label">PO · {{ $combinedPoDocuments->unique('file_path')->count() }} PDF(s) merged</span>
                                                        </div>
                                                            <div class="doc-side">
                                                                <div class="doc-side-row">
                                                                    <div class="doc-internal checkbox-fade fade-in-primary">
                                                                        <label>
                                                                            <input type="checkbox"
                                                                                   class="doc-internal-check shipment-combined-po-attach-checkbox"
                                                                                   id="shipment-combined-po-attach-checkbox"
                                                                                   checked>
                                                                            <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                                                                        </label>
                                                            </div>
                                                                    <i class="ti-trash doc-trash" style="visibility: hidden;"></i>
                                                            </div>
                                                                <span class="doc-date">{{ $combinedPoDocuments->max('created_at')?->format('d.m.Y') }}</span>
                                                        </div>
                                                        </div>
                                                        @endif
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
                                    </div>{{-- .master-container --}}
@include('layouts.partials.pcoded-shell-end')

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
                <button type="button"
                    class="btn btn-premium btn-teal btn-sm"
                    id="finalize-shipment-complete-btn"
                    @disabled($shipment->status === 'Completed')
                    title="{{ $shipment->status === 'Completed' ? 'Shipment already completed' : '' }}">Complete</button>
                <button type="button"
                    class="btn btn-premium btn-outline-custom btn-sm"
                    id="finalize-shipment-transit-btn"
                    @disabled(!empty($transitDestinationStocksReady))
                    title="{{ !empty($transitDestinationStocksReady) ? 'Destination stocks already created' : '' }}">Transit</button>
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
            <div class="modal-body p-0" style="height: 80vh; background: #f3f4f6; position: relative;">
                <iframe id="pdf-preview-frame" title="PDF preview" style="width: 100%; height: 100%; border: 0;" src=""></iframe>
                <div id="pdf-preview-mobile-fallback" style="display:none; height:100%; align-items:center; justify-content:center; flex-direction:column; gap:12px; padding:24px; text-align:center;">
                    <p style="margin:0; color:#374151; font-size:14px;">This device cannot show PDFs inside the page.</p>
                    <a id="pdf-preview-mobile-open" href="#" target="_blank" rel="noopener" class="btn btn-premium btn-teal btn-sm">Open PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="compose-manifest-mail-modal" tabindex="-1" role="dialog" aria-labelledby="composeManifestMailLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="compose-send-loader" aria-live="polite" aria-busy="true">
                <div class="compose-send-spinner" role="status"></div>
                <p class="compose-send-loader-text">Sending email...</p>
            </div>
            <div class="compose-header">
                <h5 class="compose-title" id="composeManifestMailLabel">Compose New Message</h5>
            </div>
            <div class="compose-body">
                <div class="compose-field">
                    <input type="text" id="compose-mail-from" class="compose-input compose-input-readonly" readonly placeholder="From:">
                </div>
                <p class="compose-from-hint">Emails are sent from the configured mailbox. Replies go to your login email.</p>
                <div class="compose-field">
                    <input type="text" id="compose-mail-to" class="compose-input" placeholder="To:">
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="compose-mail-cc" class="compose-input" placeholder="Cc:">
                        <button type="button" class="compose-contact-btn" data-target-field="compose-mail-cc" title="Contact directory">
                        <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="compose-mail-cc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="compose-mail-bcc" class="compose-input" placeholder="Bcc:">
                        <button type="button" class="compose-contact-btn" data-target-field="compose-mail-bcc" title="Contact directory">
                        <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="compose-mail-bcc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field">
                    <input type="text" id="compose-mail-subject" class="compose-input" placeholder="Subject:">
                </div>
                <div class="compose-editor-wrap">
                    <div class="compose-toolbar">
                        <select id="compose-font-size" title="Text style">
                            <option value="3">A Normal text</option>
                            <option value="2">Small</option>
                            <option value="4">Large</option>
                            <option value="5">Heading</option>
                        </select>
                        <button type="button" class="compose-tool-btn" data-cmd="bold" title="Bold"><strong>B</strong></button>
                        <button type="button" class="compose-tool-btn" data-cmd="italic" title="Italic"><em>I</em></button>
                        <button type="button" class="compose-tool-btn" data-cmd="underline" title="Underline"><u>U</u></button>
                        @include('partials.compose-color-tools')
                        <button type="button" class="compose-tool-btn" data-cmd="fontSize" data-value="2" title="Small">Small</button>
                        <button type="button" class="compose-tool-btn" data-cmd="formatBlock" data-value="blockquote" title="Quote"><i class="ti-quote-left"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertUnorderedList" title="Bulleted list"><i class="ti-list"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="ti-list-ol"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="outdent" title="Outdent"><i class="ti-shift-left-alt"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="indent" title="Indent"><i class="ti-shift-right-alt"></i></button>
                        <button type="button" class="compose-tool-btn" id="compose-insert-link" title="Insert link"><i class="ti-link"></i></button>
                        <button type="button" class="compose-tool-btn" id="compose-insert-image" title="Insert image"><i class="ti-image"></i></button>
                    </div>
                    <div id="compose-mail-body" class="compose-editor" contenteditable="true" data-placeholder="Your Message Here...."></div>
                </div>
                <div class="compose-attach-row">
                    <button type="button" class="btn-compose-attach" id="compose-attachment-btn">
                        <i class="ti-clip"></i> Attachment
                    </button>
                    <p class="compose-attach-hint">Max. 32MB</p>
                    <div class="compose-attach-previews" id="compose-attach-previews"></div>
                    <p class="compose-attach-empty" id="compose-attach-empty" style="display:none;">No attachments</p>
                    <input type="file" id="compose-attachment-input" multiple style="display:none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip">
                </div>
            </div>
            <div class="compose-footer">
                <button type="button" class="btn-compose-discard" id="compose-mail-discard" data-dismiss="modal">
                    <i class="ti-close"></i> Discard
                </button>
                <div class="compose-footer-right">
                    <button type="button" class="btn-compose-draft" id="compose-mail-draft" title="Open in your mail app from your email address">
                        <i class="ti-share"></i> Open in mail app
                    </button>
                    <button type="button" class="btn-compose-send" id="compose-mail-send">
                        <i class="ti-email"></i> Send from server
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jquery slimscroll js -->

<!-- modernizr js -->

<script>
    $(document).ready(function() {
        $('body').addClass('shipment-edit-page');

        var $shipmentEditFooter = $('.master-container .edit-footer');
        var $shipmentEditMain = $('.master-container .main-content-area');

        function syncShipmentEditFooterPlacement() {
            if (!$shipmentEditFooter.length || !$shipmentEditMain.length) {
                return;
            }

            var isMobile = window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches;

            if (isMobile) {
                if (!$shipmentEditFooter.parent().is('body')) {
                    $shipmentEditFooter.appendTo('body');
                }
            } else if (!$shipmentEditFooter.parent().is($shipmentEditMain)) {
                $shipmentEditMain.append($shipmentEditFooter);
            }

            // Keep footer last in the column so margin-top: auto pins it to the bottom.
            if ($shipmentEditFooter.parent().is($shipmentEditMain)) {
                $shipmentEditMain.append($shipmentEditFooter);
            }

            if (typeof window.syncShipmentSaveButtonState === 'function') {
                window.syncShipmentSaveButtonState();
            }
        }

        syncShipmentEditFooterPlacement();
        $(window).on('resize.shipmentEditFooter', syncShipmentEditFooterPlacement);

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
            $(this).select2({
                placeholder: "Select",
                allowClear: false,
                width: '100%'
            });
        });
        
        // Initialize Datepickers (raise pillar z-index so calendar isn't clipped)
        $('.datepicker').each(function() {
            $(this).datepicker({
                dateFormat: 'dd.mm.yy',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-10:c+10',
                beforeShow: function(input) {
                    $('.cs-pillar').css('z-index', '');
                    $(input).closest('.cs-pillar').css('z-index', 40);
                },
                onClose: function() {
                    $(this).closest('.cs-pillar').css('z-index', '');
                }
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
        var manifestPrepareUrl = @json(route('shipments.manifest-mail.prepare', $shipmentRouteId));
        var manifestMailPending = @json((bool) $shipment->needsManifestMailSend());
        var preAlertMailPending = @json((bool) $shipment->needsPreAlertMailSend());
        var shipmentComposeSender = @json([
            'name' => auth()->user()?->name,
            'email' => auth()->user()?->email,
        ]);
        var preAlertPrepareUrl = @json(route('shipments.pre-alert-mail.prepare', $shipmentRouteId));
        var manifestDeleteUrlTemplate = @json(route('shipments.manifests.delete', [$shipmentRouteId, '__MANIFEST__']));
        var preAlertDeleteUrlTemplate = @json(route('shipments.pre-alerts.delete', [$shipmentRouteId, '__PREALERT__']));
        var finalizeShipmentUrl = @json(route('shipments.finalize', $shipmentRouteId));
        var completePreAlertUrl = @json(route('shipments.complete-pre-alert', $shipmentRouteId));
        var shipmentsListUrl = @json(route('shipments'));
        var combinedPoDocumentCount = {{ $combinedPoDocuments->count() > 0 ? 1 : 0 }};
        var uploadedDocumentCount = {{ $shipment->documents->count() }};
        var preAlertDocumentCount = {{ $shipment->preAlerts->count() }};
        var hasPreAlertPdf = @json((bool) ($hasPreAlertPdf ?? ($shipmentRouteId > 0 && $shipment->preAlerts->isNotEmpty())));

        function setManifestMailPendingState(isPending) {
            manifestMailPending = !!isPending;
            var $btn = $('#send-manifest-btn');
            if (!$btn.length) {
                return;
            }

            $btn
                .toggleClass('send-manifest-pending', manifestMailPending)
                .attr('data-manifest-mail-pending', manifestMailPending ? '1' : '0')
                .prop('disabled', !manifestMailPending)
                .attr('title', manifestMailPending
                    ? 'New manifest ready — send email'
                    : 'Send manifest after a new manifest is generated');
        }

        function restoreSendManifestButton(originalText) {
            var $btn = $('#send-manifest-btn');
            if (originalText) {
                $btn.text(originalText);
            }
            setManifestMailPendingState(manifestMailPending);
        }

        function syncCompletePreAlertButtonState() {
            var $completeBtn = $('#complete-prealert-btn');
            if (!$completeBtn.length) {
                return;
            }

            if ($completeBtn.text().trim() === 'Completing...') {
                return;
            }

            hasPreAlertPdf = preAlertDocumentCount > 0 || $('.shipment-prealert-doc').length > 0;
            var shipmentLoaded = @json((int) $shipmentRouteId) > 0;
            var workflowAwaitingShipment = @json((bool) ($workflowAwaitingShipment ?? false));
            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            var isInProcess = status === 'In process';
            var canComplete = hasPreAlertPdf && isInProcess;
            var disabled = workflowAwaitingShipment || !shipmentLoaded || !canComplete;

            $completeBtn
                .prop('disabled', disabled)
                .attr('data-has-pre-alert-pdf', hasPreAlertPdf ? '1' : '0')
                .attr('data-pre-alert-completed', canComplete ? '0' : (hasPreAlertPdf ? '1' : '0'))
                .attr('title', workflowAwaitingShipment || !shipmentLoaded
                    ? 'Load a shipment first'
                    : (!hasPreAlertPdf
                        ? 'Generate a pre-alert PDF before completing'
                        : (!isInProcess
                            ? 'Pre-alert already completed'
                            : 'Complete pre-alert and set shipment to In transit')));
        }

        function applyShipmentFinalizeCompleteResponse(response) {
            var nextStatus = (response && response.status) || 'In transit';

            $('.header-meta-group .status-badge')
                .removeClass('stock-status-new stock-status-stock shipment-status-in-transit stock-status-in-progress stock-status-pending stock-status-cancelled stock-status-completed stock-status-archived stock-status-unknown')
                .addClass('stock-status-badge ' + stockStatusBadgeClass(nextStatus))
                .text(nextStatus);
            $('.select2-status-inline').val(nextStatus).trigger('change.select2');
            $('#shipment-current-status').val(nextStatus);

            ((response && response.stocks) || []).forEach(function(stock) {
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
            syncFinalizeChoiceButtonState();
            syncCompletePreAlertButtonState();
            syncServiceDetailsTabLock();
        }

        function isShipmentStatusInProcess() {
            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            return status === 'In process';
        }

        function shouldLockServiceDetailsTab() {
            return isShipmentStatusInProcess() && !@json((bool) ($workflowEditMode ?? false));
        }

        function syncServiceDetailsTabLock() {
            var locked = shouldLockServiceDetailsTab();
            var $tab = $('.stock-tab[data-panel="stock-panel-service"]');
            var $panel = $('#stock-panel-service');

            $tab
                .toggleClass('stock-tab--disabled', locked)
                .attr('title', locked ? 'Service details not available. Please create pre alert.' : '');

            $panel.toggleClass('stock-panel--locked', locked);

            if (locked && $tab.hasClass('active')) {
                activateStockPanel('stock-panel-items');
            }

            if ($panel.length) {
                $panel.find('select').each(function() {
                    if (locked) {
                        $(this).on('select2:opening.serviceDetailsLock', function(e) {
                            e.preventDefault();
                        });
                    } else {
                        $(this).off('select2:opening.serviceDetailsLock');
                    }
                });
            }

            syncAddStockItemsButtonState();
        }

        function shouldPromptTransitAfterPreAlertComplete() {
            if (!@json((bool) ($createPreAlertMode ?? false))) {
                return false;
            }

            if (@json((bool) ($transitDestinationStocksReady ?? false))) {
                return false;
            }

            return @json((int) $shipmentRouteId) > 0;
        }

        function promptTransitAfterPreAlertComplete() {
            if (!shouldPromptTransitAfterPreAlertComplete()) {
                return;
            }

            var message = 'Pre-alert completed. Transit is still pending for destination.';
            var transitPageUrl = @json(route('transit', ['shipment' => $shipmentRouteId > 0 ? $shipmentRouteId : 0]));

            if (typeof swal === 'function') {
                swal({
                    title: 'Transit required',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Transit now',
                    cancelButtonText: 'Later',
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm || !transitPageUrl) {
                        return;
                    }

                    if (typeof window.shipmentEditMarkAllowLeave === 'function') {
                        window.shipmentEditMarkAllowLeave();
                    }
                    window.location.href = transitPageUrl;
                });
            } else if (window.confirm(message + ' Open Transit page now?')) {
                if (typeof window.shipmentEditMarkAllowLeave === 'function') {
                    window.shipmentEditMarkAllowLeave();
                }
                window.location.href = transitPageUrl;
            }
        }

        function submitCompletePreAlert($triggerBtn) {
            var $btn = $triggerBtn && $triggerBtn.length ? $triggerBtn : $('#complete-prealert-btn');
            if (!$btn.length || $btn.prop('disabled')) {
                return;
            }

            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Completing...');

            $.ajax({
                url: completePreAlertUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    shipment_number: @json($shipment->shipment_number)
                },
                headers: {
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success) {
                        if (typeof swal === 'function') {
                            swal({
                                title: 'Could not complete',
                                text: (response && response.message) || 'Could not complete shipment.',
                                type: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert((response && response.message) || 'Could not complete shipment.');
                        }
                        $btn.prop('disabled', false).text(originalText);
                        syncCompletePreAlertButtonState();
                        return;
                    }

                    applyShipmentFinalizeCompleteResponse(response);
                    $btn.text(originalText);

                    if (@json((bool) ($createPreAlertMode ?? false))) {
                        promptTransitAfterPreAlertComplete();
                    } else if (typeof swal === 'function') {
                        swal({
                            title: 'Completed',
                            text: response.message || 'Pre-alert completed. Shipment status is now In transit.',
                            type: 'success',
                            timer: 3000
                        });
                    }
                })
                .fail(function(xhr) {
                    var message = 'Could not complete shipment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    if (typeof swal === 'function') {
                        swal({
                            title: 'Could not complete',
                            text: message,
                            type: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert(message);
                    }

                    $btn.prop('disabled', false).text(originalText);
                    syncCompletePreAlertButtonState();
                });
        }

        function setPreAlertMailPendingState(isPending) {
            preAlertMailPending = !!isPending;
            var $btn = $('#send-prealert-btn');
            if ($btn.length) {
                $btn
                    .toggleClass('send-prealert-pending', preAlertMailPending)
                    .attr('data-pre-alert-mail-pending', preAlertMailPending ? '1' : '0')
                    .prop('disabled', !preAlertMailPending)
                    .attr('title', preAlertMailPending
                        ? 'New pre-alert ready — send email'
                        : 'Send pre-alert after a new pre-alert is generated');
            }
        }

        function restoreSendPreAlertButton(originalText) {
            var $btn = $('#send-prealert-btn');
            if (originalText) {
                $btn.text(originalText);
            }
            setPreAlertMailPendingState(preAlertMailPending);
        }

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
                if (response && typeof response.manifest_mail_pending !== 'undefined') {
                    setManifestMailPendingState(!!response.manifest_mail_pending);
                }
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
                if (response && typeof response.pre_alert_mail_pending !== 'undefined') {
                    setPreAlertMailPendingState(!!response.pre_alert_mail_pending);
                }
                syncCompletePreAlertButtonState();
                prependShipmentChangeLog(response && response.change_log);
            });
        });

        @php
            $latestManifest = $shipment->latestManifest();
            $manifestCoreAttachmentSources = [];
            if ($latestManifest) {
                $manifestCoreAttachmentSources[] = [
                    'url' => route('shipments.manifests.show', [$shipmentRouteId, $latestManifest->id]),
                    'filename' => str_replace(' ', '-', $latestManifest->displayLabel()) . '-' . $shipment->shipment_number . '.pdf',
                ];
            } elseif ($shipment->crrs->isNotEmpty()) {
                $manifestCoreAttachmentSources[] = [
                    'url' => route('shipments.combined-manifest-documents', $shipmentRouteId),
                    'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
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

        function isCombinedPoAttachChecked() {
            var $cb = $('#shipment-combined-po-attach-checkbox');
            if (!$cb.length) {
                return combinedPoDocumentCount > 0;
            }

            return !!$cb.prop('checked');
        }

        function applyDocsListAttachmentFilters(attachments) {
            var list = (attachments || []).filter(function(item) {
                if (!item) {
                    return false;
                }

                if (item.document_id) {
                    var $cb = $('.shipment-doc-attach-checkbox[data-doc-id="' + item.document_id + '"]');
                    return $cb.length > 0 && !!$cb.prop('checked');
                }

                if (String(item.key || '') === 'combined_po') {
                    return isCombinedPoAttachChecked();
                }

                return true;
            });

            list = mergeCheckedDocumentAttachments(list);

            if (combinedPoDocumentCount > 0 && isCombinedPoAttachChecked()) {
                var hasCombined = list.some(function(item) {
                    return item && String(item.key || '') === 'combined_po';
                });

                if (!hasCombined) {
                    list.push({
                        key: 'combined_po',
                        url: @json(route('shipments.combined-po-documents', $shipmentRouteId)),
                        filename: @json('combined-po-documents-' . $shipment->shipment_number . '.pdf')
                    });
                }
            } else {
                list = list.filter(function(item) {
                    return !(item && String(item.key || '') === 'combined_po');
                });
            }

            return list;
        }

        function buildMailAttachmentSources(coreSources) {
            return applyDocsListAttachmentFilters(coreSources || []);
        }

        function mergeCheckedDocumentAttachments(attachments) {
            var list = (attachments || []).slice();

            getCheckedUploadedDocumentAttachments().forEach(function(item) {
                var alreadyIncluded = list.some(function(existing) {
                    return existing && existing.document_id && String(existing.document_id) === String(item.document_id);
                });

                if (alreadyIncluded) {
                    return;
                }

                var entry = $.extend({
                    key: 'document-' + item.document_id
                }, item);

                // Keep manual uploads above Combined PO (last).
                var combinedIndex = -1;
                for (var i = 0; i < list.length; i++) {
                    if (list[i] && String(list[i].key || '') === 'combined_po') {
                        combinedIndex = i;
                        break;
                    }
                }

                if (combinedIndex >= 0) {
                    list.splice(combinedIndex, 0, entry);
                } else {
                    list.push(entry);
                }
            });

            return list;
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
            if (preview.bcc) {
                params.push('bcc=' + encodeURIComponent(preview.bcc));
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

        var pendingManifestMail = null;
        var mailContactsUrl = @json(route('api.mail-contacts'));
        var mailContactsCache = [];
        var mailContactsSearchTimer = null;

        function closeComposeContactPickers() {
            $('#compose-manifest-mail-modal .compose-contact-picker').removeClass('open');
            $('#compose-manifest-mail-modal .compose-contact-btn').removeClass('active');
        }

        function appendEmailToField(fieldId, email) {
            email = $.trim(email || '');
            if (!email) {
                return;
            }

            var $field = $('#' + fieldId);
            var current = $.trim($field.val() || '');
            var parts = current
                ? current.split(/[;,]+/).map(function(part) { return $.trim(part); }).filter(Boolean)
                : [];

            var exists = parts.some(function(part) {
                return part.toLowerCase() === email.toLowerCase();
            });

            if (!exists) {
                parts.push(email);
            }

            $field.val(parts.join(', ')).trigger('change');
        }

        function renderComposeContactList($picker, contacts) {
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

        function loadComposeContacts($picker, query) {
            $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Loading...</li>');

            $.ajax({
                url: mailContactsUrl,
                method: 'GET',
                dataType: 'json',
                data: { q: query || '' }
            })
                .done(function(response) {
                    mailContactsCache = (response && response.results) || [];
                    renderComposeContactList($picker, mailContactsCache);
                })
                .fail(function() {
                    $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Could not load contacts</li>');
                });
        }

        $(document).on('click', '#compose-manifest-mail-modal .compose-contact-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);
            var fieldId = $btn.data('target-field');
            var $picker = $('#compose-manifest-mail-modal .compose-contact-picker[data-for="' + fieldId + '"]');
            var alreadyOpen = $picker.hasClass('open');

            closeComposeContactPickers();

            if (alreadyOpen) {
                return;
            }

            $btn.addClass('active');
            $picker.addClass('open');
            $picker.find('.compose-contact-search').val('').focus();
            loadComposeContacts($picker, '');
        });

        $(document).on('input', '#compose-manifest-mail-modal .compose-contact-search', function() {
            var $picker = $(this).closest('.compose-contact-picker');
            var query = $.trim($(this).val() || '');

            clearTimeout(mailContactsSearchTimer);
            mailContactsSearchTimer = setTimeout(function() {
                loadComposeContacts($picker, query);
            }, 200);
        });

        $(document).on('click', '#compose-manifest-mail-modal .compose-contact-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var email = $(this).attr('data-email');
            var fieldId = $(this).closest('.compose-contact-picker').attr('data-for');
            appendEmailToField(fieldId, email);
            closeComposeContactPickers();
        });

        $(document).on('click', '#compose-manifest-mail-modal', function(e) {
            if ($(e.target).closest('.compose-contact-btn, .compose-contact-picker').length) {
                return;
            }
            closeComposeContactPickers();
        });

        $('#compose-manifest-mail-modal').on('hidden.bs.modal', function() {
            closeComposeContactPickers();
        });

        function plainTextToEditorHtml(text) {
            var parts = String(text || '').split(/(<table[\s\S]*?<\/table>|<font[\s\S]*?<\/font>|<span[\s\S]*?<\/span>|<mark[\s\S]*?<\/mark>)/i);
            return parts.map(function(part) {
                if (/^<(table|font|span|mark)\b/i.test(part)) {
                    return part;
                }
                return $('<div>').text(part).html()
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
            }).join('');
        }

        function editorHtmlToPlainText(html) {
            var $tmp = $('<div>').html(html || '');
            var blocks = [];
            $tmp.find('table, font[color], span[style], mark').each(function() {
                var style = String(this.getAttribute('style') || '').toLowerCase();
                var keep = this.tagName === 'TABLE'
                    || this.tagName === 'FONT'
                    || this.tagName === 'MARK'
                    || style.indexOf('color') !== -1
                    || style.indexOf('background') !== -1;
                if (!keep) {
                    return;
                }
                blocks.push(this.outerHTML);
                $(this).replaceWith('%%MAIL_HTML_' + (blocks.length - 1) + '%%');
            });
            $tmp.find('strong, b').each(function() {
                $(this).replaceWith('**' + $(this).text() + '**');
            });
            $tmp.find('br').replaceWith('\n');
            $tmp.find('div, p, li').each(function() {
                $(this).append('\n');
            });
            var text = $.trim($tmp.text().replace(/\n{3,}/g, '\n\n'));
            blocks.forEach(function(blockHtml, index) {
                text = text.replace('%%MAIL_HTML_' + index + '%%', '\n' + blockHtml + '\n');
            });
            return $.trim(text.replace(/\n{3,}/g, '\n\n'));
        }

        function isPdfAttachment(item) {
            var name = ((item && (item.filename || item.name || item.label)) || '').toLowerCase();
            var url = ((item && (item.url || item.previewUrl)) || '').toLowerCase();
            var mime = ((item && item.mime) || '').toLowerCase();
            return mime === 'application/pdf'
                || name.indexOf('.pdf') !== -1
                || url.indexOf('.pdf') !== -1
                || url.indexOf('/manifest') !== -1
                || url.indexOf('/pre-alert') !== -1
                || url.indexOf('/documents') !== -1;
        }

        function isImageAttachment(item) {
            var name = ((item && (item.filename || item.name || item.label)) || '').toLowerCase();
            var mime = ((item && item.mime) || '').toLowerCase();
            return mime.indexOf('image/') === 0 || /\.(png|jpe?g|gif|webp)$/.test(name);
        }

        function buildComposeAttachCard(item, index) {
            var label = (item && (item.filename || item.name || item.label)) || ('Attachment ' + (index + 1));
            var url = (item && (item.url || item.previewUrl)) || '';
            var key = (item && item.key) ? String(item.key) : ('attach-' + index);
            var isPdf = isPdfAttachment(item);
            var isImage = isImageAttachment(item) || (item && item.previewUrl && /^image\//.test(item.mime || ''));
            var typeLabel = isPdf ? 'PDF' : (isImage ? 'Image' : 'File');
            var canPreview = !!url;

            var $card = $('<div class="compose-attach-card" role="button" tabindex="0"></div>')
                .attr('data-attach-index', index)
                .attr('data-attach-key', key)
                .attr('data-attach-url', url)
                .attr('data-attach-title', label)
                .attr('title', canPreview ? ('Preview ' + label) : label);

            var $remove = $('<button type="button" class="compose-attach-remove" title="Remove attachment">&times;</button>')
                .attr('data-attach-key', key)
                .attr('aria-label', 'Remove ' + label);

            var $thumb = $('<div class="compose-attach-thumb"></div>');
            if (url && isPdf) {
                var pdfSrc = url;
                if (url.indexOf('blob:') === 0) {
                    // blob PDFs preview directly; hash params can break some browsers
                    pdfSrc = url;
                } else if (url.indexOf('#') < 0) {
                    pdfSrc = url + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
                }
                $thumb.append(
                    $('<iframe></iframe>')
                        .attr('src', pdfSrc)
                        .attr('title', label)
                        .attr('loading', 'lazy')
                );
            } else if (url && isImage) {
                $thumb.append($('<img>').attr('src', url).attr('alt', label));
            } else {
                $thumb.append($('<i class="ti-file attach-icon"></i>'));
            }

            var $meta = $('<div class="compose-attach-meta"></div>');
            $meta.append($('<p class="compose-attach-name"></p>').text(label));
            $meta.append($('<p class="compose-attach-type"></p>').text(typeLabel + (canPreview ? ' · Click to preview' : '')));

            $card.append($remove).append($thumb).append($meta);
            return $card;
        }

        function renderComposeAttachments(attachments) {
            var $previews = $('#compose-attach-previews').empty();
            var list = attachments || [];

            if (!list.length) {
                $('#compose-attach-empty').show();
                return;
            }

            $('#compose-attach-empty').hide();
            list.forEach(function(item, index) {
                if (!item.key) {
                    item.key = item.document_id ? ('document-' + item.document_id) : ('attach-' + index);
                }
                $previews.append(buildComposeAttachCard(item, index));
            });
        }

        function fillComposeManifestModal(preview, attachments) {
            preview = preview || {};
            var fromName = preview.from_name || shipmentComposeSender.name || '';
            var fromEmail = preview.from || shipmentComposeSender.email || '';
            var fromDisplay = fromName && fromEmail
                ? fromName + ' <' + fromEmail + '>'
                : (fromEmail || fromName || '');
            $('#compose-mail-from').val(fromDisplay);
            $('#compose-mail-to').val(preview.to || '');
            $('#compose-mail-cc').val(preview.cc || '');
            $('#compose-mail-bcc').val(preview.bcc || '');
            $('#compose-mail-subject').val(preview.subject || '');
            $('#compose-mail-body').html(plainTextToEditorHtml(preview.body || ''));
            renderComposeAttachments(attachments);
        }

        function getComposeExcludedAttachmentKeys() {
            var excluded = [];

            if (pendingManifestMail) {
                var original = pendingManifestMail.originalAttachments || [];
                var currentKeys = {};
                (pendingManifestMail.attachments || []).forEach(function(item) {
                    if (item && item.key) {
                        currentKeys[String(item.key)] = true;
                    }
                });

                excluded = original
                    .map(function(item) { return item && item.key ? String(item.key) : ''; })
                    .filter(function(key) { return key !== '' && !currentKeys[key]; });
            }

            // Docs-list Combined PO checkbox gates auto-attach (server always prepares it).
            if (combinedPoDocumentCount > 0 && !isCombinedPoAttachChecked() && excluded.indexOf('combined_po') === -1) {
                excluded.push('combined_po');
            }

            return excluded;
        }

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

            // Prefer native Chrome PDF viewer in a new tab (iframe PDF is blank on Android/iOS).
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

        function openComposeAttachmentPreview(url, title) {
            if (!url) {
                return;
            }

            $('#pdfPreviewModalLabel').text(title || 'Attachment preview');

            var $compose = $('#compose-manifest-mail-modal');
            var $preview = $('#pdf-preview-modal');

            // Temporarily hide compose so the PDF modal is fully usable, then restore it.
            $compose.one('hidden.bs.modal', function() {
                loadPdfIntoPreviewFrame(url).catch(function (err) {
                    if (typeof swal === 'function') {
                        swal({ title: 'Preview failed', text: err.message || 'Could not open document.', type: 'error' });
                    } else {
                        alert(err.message || 'Could not open document.');
                    }
                });
                $preview.modal('show');
            });
            $preview.one('hidden.bs.modal', function() {
                revokePdfPreviewBlob();
                $('#pdf-preview-mobile-fallback').hide();
                $('#pdf-preview-frame').show().attr('src', 'about:blank');
                if (pendingManifestMail) {
                    $compose.modal('show');
                }
            });
            $compose.modal('hide');
        }

        function readComposeManifestPreview() {
            return {
                to: $.trim($('#compose-mail-to').val() || ''),
                cc: $.trim($('#compose-mail-cc').val() || ''),
                bcc: $.trim($('#compose-mail-bcc').val() || ''),
                subject: $.trim($('#compose-mail-subject').val() || ''),
                body: editorHtmlToPlainText($('#compose-mail-body').html())
            };
        }

        function openComposeMailModal(response, mailType) {
            var attachments = applyDocsListAttachmentFilters((response.attachments || []).map(function(item, index) {
                var copy = $.extend({}, item);
                if (!copy.key) {
                    copy.key = copy.document_id ? ('document-' + copy.document_id) : ('attach-' + index);
                }
                return copy;
            }));

            pendingManifestMail = {
                type: mailType || 'manifest',
                preview: response.preview || {},
                open_url: response.open_url || null,
                send_url: response.send_url || null,
                eml_url: response.eml_url || null,
                eml_filename: response.eml_filename || null,
                originalAttachments: attachments.slice(),
                attachments: attachments.slice()
            };

            $('#composeManifestMailLabel').text(
                pendingManifestMail.type === 'prealert' ? 'Pre-alert email' : 'Manifest email'
            );
            fillComposeManifestModal(pendingManifestMail.preview, pendingManifestMail.attachments);
            $('#compose-manifest-mail-modal').modal('show');
        }

        function openComposeManifestModal(response) {
            openComposeMailModal(response, 'manifest');
        }

        $(document).on('click', '#compose-manifest-mail-modal .compose-tool-btn[data-cmd]', function(e) {
            e.preventDefault();
            var cmd = $(this).data('cmd');
            var value = $(this).data('value') || null;
            $('#compose-mail-body').focus();
            document.execCommand(cmd, false, value);
        });

        $(document).on('change', '#compose-font-size', function() {
            $('#compose-mail-body').focus();
            document.execCommand('fontSize', false, $(this).val());
        });

        $(document).on('click', '#compose-insert-link', function(e) {
            e.preventDefault();
            var url = window.prompt('Enter URL');
            if (!url) {
                return;
            }
            $('#compose-mail-body').focus();
            document.execCommand('createLink', false, url);
        });

        $(document).on('click', '#compose-insert-image', function(e) {
            e.preventDefault();
            var url = window.prompt('Enter image URL');
            if (!url) {
                return;
            }
            $('#compose-mail-body').focus();
            document.execCommand('insertImage', false, url);
        });

        $(document).on('click', '#compose-attachment-btn', function() {
            $('#compose-attachment-input').trigger('click');
        });

        $(document).on('click', '#compose-attach-previews .compose-attach-remove', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var key = String($(this).attr('data-attach-key') || '');
            if (!pendingManifestMail || !key) {
                return;
            }

            pendingManifestMail.attachments = (pendingManifestMail.attachments || []).filter(function(item) {
                if (String(item.key || '') !== key) {
                    return true;
                }
                if (item.previewUrl && String(item.previewUrl).indexOf('blob:') === 0) {
                    try { URL.revokeObjectURL(item.previewUrl); } catch (err) {}
                }
                if (item.url && String(item.url).indexOf('blob:') === 0 && item.url !== item.previewUrl) {
                    try { URL.revokeObjectURL(item.url); } catch (err) {}
                }
                return false;
            });

            renderComposeAttachments(pendingManifestMail.attachments);
        });

        $(document).on('click', '#compose-attach-previews .compose-attach-card', function(e) {
            e.preventDefault();
            if ($(e.target).closest('.compose-attach-remove').length) {
                return;
            }
            var url = $(this).attr('data-attach-url');
            var title = $(this).attr('data-attach-title') || 'Attachment preview';
            if (!url) {
                return;
            }
            openComposeAttachmentPreview(url, title);
        });

        $(document).on('keydown', '#compose-attach-previews .compose-attach-card', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') {
                return;
            }
            e.preventDefault();
            $(this).trigger('click');
        });

        $(document).on('change', '#compose-attachment-input', function() {
            var files = this.files || [];
            if (!files.length) {
                return;
            }

            if (!pendingManifestMail) {
                pendingManifestMail = {
                    preview: {},
                    attachments: [],
                    originalAttachments: []
                };
            }

            pendingManifestMail.attachments = pendingManifestMail.attachments || [];

            Array.prototype.forEach.call(files, function(file, index) {
                var objectUrl = URL.createObjectURL(file);
                var isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
                var isImage = /^image\//.test(file.type) || /\.(png|jpe?g|gif|webp)$/i.test(file.name);

                var localItem = {
                    key: 'local-' + Date.now() + '-' + index,
                    filename: file.name,
                    mime: file.type || '',
                    previewUrl: objectUrl,
                    url: objectUrl,
                    local: true,
                    file: file
                };

                // Keep explicit type hints for card rendering
                if (isPdf) {
                    localItem.filename = file.name.match(/\.pdf$/i) ? file.name : (file.name + '.pdf');
                } else if (isImage && !/\.(png|jpe?g|gif|webp)$/i.test(file.name)) {
                    // leave as-is; mime drives image preview
                }

                pendingManifestMail.attachments.push(localItem);
            });

            renderComposeAttachments(pendingManifestMail.attachments);
            this.value = '';
        });

        $(document).on('click', '#compose-mail-discard', function() {
            pendingManifestMail = null;
            $('#compose-mail-to, #compose-mail-cc, #compose-mail-bcc, #compose-mail-subject').val('');
            $('#compose-mail-body').html('');
            $('#compose-attachment-list').empty();
            $('#compose-manifest-mail-modal').removeClass('compose-sending').modal('hide');
        });

        $(document).on('click', '#compose-mail-draft', function() {
            if (!pendingManifestMail || !pendingManifestMail.open_url) {
                alert('Draft is not available for this shipment.');
                return;
            }

            var mailType = pendingManifestMail.type || 'manifest';
            var $btn = mailType === 'prealert' ? $('#send-prealert-btn') : $('#send-manifest-btn');
            var preview = readComposeManifestPreview();

            if (mailType === 'prealert') {
                preAlertMailPreview = preview;
            } else {
                manifestMailPreview = preview;
            }
            pendingManifestMail.preview = preview;

            var mailWindow = openMailDraftWindow();
            sendShipmentMail($btn, preview, pendingManifestMail.open_url, mailWindow);
            $('#compose-manifest-mail-modal').modal('hide');
        });

        $(document).on('click', '#compose-mail-send', function() {
            if (!pendingManifestMail || !pendingManifestMail.send_url) {
                alert('Email is not available for this shipment.');
                return;
            }

            var mailType = pendingManifestMail.type || 'manifest';
            var $btn = $(this);
            var preview = readComposeManifestPreview();
            if (!preview.to) {
                alert('Please enter at least one recipient in To.');
                return;
            }
            if (!preview.subject) {
                alert('Please enter a subject.');
                return;
            }

            if (mailType === 'prealert') {
                preAlertMailPreview = preview;
            } else {
                manifestMailPreview = preview;
            }
            pendingManifestMail.preview = preview;

            var formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('to', preview.to);
            formData.append('cc', preview.cc || '');
            formData.append('bcc', preview.bcc || '');
            formData.append('subject', preview.subject);
            formData.append('body', preview.body || '');

            var documentIds = (pendingManifestMail.attachments || [])
                .filter(function(item) { return item && item.document_id && !item.local; })
                .map(function(item) { return item.document_id; });
            documentIds.forEach(function(id) {
                formData.append('document_ids[]', id);
            });

            getComposeExcludedAttachmentKeys().forEach(function(key) {
                formData.append('exclude_attachments[]', key);
            });

            (pendingManifestMail.attachments || []).forEach(function(item) {
                if (item && item.local && item.file) {
                    formData.append('files[]', item.file, item.filename || item.file.name);
                }
            });

            var originalText = $btn.html();
            var $modal = $('#compose-manifest-mail-modal');
            $modal.addClass('compose-sending');
            $btn.prop('disabled', true).html('<i class="ti-reload"></i> Sending...');
            $('#compose-mail-draft, #compose-mail-discard, #compose-attachment-btn').prop('disabled', true);

            $.ajax({
                url: pendingManifestMail.send_url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            })
                .done(function(response) {
                    if (!response || !response.success) {
                        alert((response && response.message) || 'Could not send email.');
                        return;
                    }

                    $modal.modal('hide');
                    pendingManifestMail = null;

                    if (mailType === 'manifest') {
                        setManifestMailPendingState(false);
                    } else if (mailType === 'prealert') {
                        setPreAlertMailPendingState(false);
                    }

                    prependShipmentChangeLog(response.change_log);

                    if (typeof swal === 'function') {
                        swal({
                            title: 'Email sent',
                            text: response.message || 'Your email was sent successfully.',
                            type: 'success',
                            timer: 4000
                        });
                    } else {
                        alert(response.message || 'Your email was sent successfully.');
                    }
                })
                .fail(function(xhr) {
                    var message = (xhr.responseJSON && xhr.responseJSON.message)
                        || 'Could not send email. Please try again.';
                    alert(message);
                })
                .always(function() {
                    $modal.removeClass('compose-sending');
                    $btn.prop('disabled', false).html(originalText);
                    $('#compose-mail-draft, #compose-mail-discard, #compose-attachment-btn').prop('disabled', false);
                });
        });

        function buildMailOpenUrl(baseUrl, documentIds, excludeAttachments) {
            if (!baseUrl) {
                return baseUrl;
            }

            var parts = [];
            if (documentIds && documentIds.length) {
                parts.push('document_ids=' + encodeURIComponent(documentIds.join(',')));
            }
            if (excludeAttachments && excludeAttachments.length) {
                parts.push('exclude_attachments=' + encodeURIComponent(excludeAttachments.join(',')));
            }

            if (!parts.length) {
                return baseUrl;
            }

            var separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';
            return baseUrl + separator + parts.join('&');
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

            var openUrlWithDocuments = buildMailOpenUrl(
                openUrl,
                getCheckedUploadedDocumentIds(),
                getComposeExcludedAttachmentKeys()
            );
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Opening mail...');

            if (window.MarineMailBusy) {
                window.MarineMailBusy.show('Opening mail app...');
                window.setTimeout(function () {
                    window.MarineMailBusy.hide();
                }, 8000);
            }

            if (mailWindow && !mailWindow.closed) {
                mailWindow.location.href = openUrlWithDocuments;
            } else {
                window.open(openUrlWithDocuments, '_blank');
            }

            if (typeof swal === 'function') {
                swal({
                    title: 'Email opening',
                    html: 'Your mail app is opening with To, Cc and Subject.<br><br>' +
                        'A complete draft with <strong>PDF attachments</strong> is also downloading — ' +
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
                        alert((response && response.message) || 'Could not prepare manifest email.');
                        restoreSendManifestButton(originalText);
                        return;
                    }

                    manifestMailPreview = response.preview;
                    openComposeManifestModal(response);
                    restoreSendManifestButton(originalText);
                })
                .fail(function(xhr) {
                    var message = 'Could not prepare manifest email.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                    restoreSendManifestButton(originalText);
                });
        });

        function showServiceDetailsUnavailableAlert() {
            var text = 'Service details not available. Please create pre alert.';

            if (typeof swal === 'function') {
                swal({
                    title: 'Service details',
                    text: text,
                    type: 'info',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(text);
            }
        }

        function showServiceDetailsRequiredAlert(message) {
            var text = message || 'Add service details before generating a pre-alert PDF.';

            if (typeof swal === 'function') {
                swal({
                    title: 'Service details required',
                    text: text,
                    type: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(text);
            }
        }

        function serviceDetailsHasData() {
            var service = $('select[name="service"]').val();
            var panelMap = {
                'Airfreight': '#service-details-airfreight',
                'Sea freight': '#service-details-sea-freight',
                'Truck': '#service-details-truck',
                'Courier': '#service-details-courier',
                'Release': '#service-details-release',
                'Hand Carry': '#service-details-hand-carry',
                'On-board delivery': '#service-details-on-board'
            };
            var panelSelector = panelMap[service];
            if (!panelSelector || !$(panelSelector).length) {
                return false;
            }

            var hasData = false;
            $(panelSelector).find('input, select, textarea').each(function() {
                var $el = $(this);
                if ($el.is(':disabled') || $el.attr('type') === 'hidden') {
                    return;
                }

                if ($el.is(':checkbox, :radio')) {
                    if ($el.is(':checked')) {
                        hasData = true;
                        return false;
                    }
                    return;
                }

                if ($.trim(String($el.val() || '')) !== '') {
                    hasData = true;
                    return false;
                }
            });

            return hasData;
        }

        function prepareAndOpenPreAlertMail($triggerBtn, restoreCallback) {
            if (!$('#shipment-edit-form').length) {
                alert('Shipment form is not available.');
                return;
            }

            if (!$('#crr-ids-container input[name="crr_ids[]"]').length) {
                alert('Please add at least one stock item before sending a pre-alert.');
                return;
            }

            if (!serviceDetailsHasData()) {
                showServiceDetailsRequiredAlert('Please fill in Service details before completing the pre-alert. The pre-alert PDF will not be generated until service details are added.');
                return;
            }

            var originalText = $triggerBtn.text();
            $triggerBtn.prop('disabled', true).text('Preparing pre-alert...');
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
                        var failMessage = (response && response.message) || 'Could not prepare pre-alert email.';
                        if (response && response.code === 'missing_service_details') {
                            showServiceDetailsRequiredAlert(failMessage);
                        } else {
                            alert(failMessage);
                        }
                        restoreCallback(originalText);
                        return;
                    }

                    preAlertMailPreview = response.preview;
                    hasPreAlertPdf = true;
                    preAlertDocumentCount = Math.max(preAlertDocumentCount, 1);
                    syncCompletePreAlertButtonState();
                    openComposeMailModal(response, 'prealert');
                    restoreCallback(originalText);
                })
                .fail(function(xhr) {
                    var message = 'Could not prepare pre-alert email.';
                    var code = null;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.code) {
                        code = xhr.responseJSON.code;
                    }

                    if (code === 'missing_service_details' || /service details/i.test(message)) {
                        showServiceDetailsRequiredAlert(message);
                    } else {
                        alert(message);
                    }
                    restoreCallback(originalText);
                });
        }

        $(document).on('click', '#send-prealert-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            prepareAndOpenPreAlertMail($(this), restoreSendPreAlertButton);
        });

        $(document).on('click', '#complete-prealert-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }

            if (!hasPreAlertPdf && preAlertDocumentCount <= 0 && $('.shipment-prealert-doc').length === 0) {
                if (typeof swal === 'function') {
                    swal({
                        title: 'Pre-alert required',
                        text: 'Generate a pre-alert PDF before completing.',
                        type: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Generate a pre-alert PDF before completing.');
                }
                return;
            }

            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            if (status !== 'In process') {
                if (typeof swal === 'function') {
                    swal({
                        title: 'Already completed',
                        text: 'Pre-alert has already been completed for this shipment.',
                        type: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Pre-alert has already been completed for this shipment.');
                }
                return;
            }

            if (typeof swal === 'function') {
                swal({
                    title: 'Complete Pre alert?',
                    text: 'Shipment status will be set to In transit and selected stocks will be marked Completed. No duplicate stocks will be created.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Complete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    submitCompletePreAlert($btn);
                });
            } else {
                if (confirm('Complete pre-alert? Shipment will be In transit and stocks Completed. No duplicate stocks will be created.')) {
                    submitCompletePreAlert($btn);
                }
            }
        });

        function openFinalizeTransitModal() {
            $('#finalize-shipment-number').val(@json($shipment->shipment_number));
            $('#finalize-consignee-code').val($.trim($('#consignee-party-code').val()) || '');
            $('#finalize-shipment-transit-modal').modal('show');
        }

        $(document).on('click', '#finalize-shipment-btn', function(e) {
            e.preventDefault();
            if ($(this).prop('disabled')) {
                return;
            }
            openFinalizeTransitModal();
        });

        $(document).on('click', '#finalize-shipment-transit-btn', function() {
            if ($(this).prop('disabled')) {
                return;
            }

            openFinalizeTransitModal();
        });

        $(document).on('click', '#finalize-shipment-complete-btn', function() {
            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }

            var originalText = $btn.text();

            $btn.prop('disabled', true).text('Completing...');

            $.ajax({
                url: finalizeShipmentUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    shipment_number: @json($shipment->shipment_number),
                    consignee_code: $.trim($('#consignee-party-code').val()) || '',
                    action: 'complete'
                },
                headers: {
                    'Accept': 'application/json'
                }
            })
                .done(function(response) {
                    if (!response || !response.success) {
                        alert((response && response.message) || 'Could not complete shipment.');
                        $btn.prop('disabled', false).text(originalText);
                        return;
                    }

                    applyShipmentFinalizeCompleteResponse(response);
                    $btn.text(originalText);
                })
                .fail(function(xhr) {
                    var message = 'Could not complete shipment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
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
                        syncFinalizeChoiceButtonState();
                        syncServiceDetailsTabLock();
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

        // Tab Switching Logic for Shipment Tabs
        $('.nav-tab-item').on('click', function() {
            var target = $(this).data('target');
            var $panel = $('#tab-' + target);
            
            $('.nav-tab-item').removeClass('active');
            $(this).addClass('active');
            
            $('.tab-panel').removeClass('active');
            if ($panel.length) {
                void $panel[0].offsetWidth;
                $panel.addClass('active');
            }
        });

        // Stock items / Service details / Irregularities tabs
        function activateStockPanel(panelId) {
            var $tab = $('.stock-tab[data-panel="' + panelId + '"]');
            var $panel = $('#' + panelId);
            if (!$tab.length || !$panel.length) {
                return;
            }

            $('.stock-tab').removeClass('active');
            $tab.addClass('active');
            $('.stock-panel').removeClass('active');
            void $panel[0].offsetWidth;
            $panel.addClass('active');
            if (typeof window.mcPulseTabPane === 'function') {
                window.mcPulseTabPane($panel[0]);
            }
        }

        function scrollFormToServiceDetails() {
            var $target = $('.stock-items-wrapper');
            if (!$target.length) {
                return;
            }

            var $scroller = $('.form-scroller');
            var canScrollScroller = $scroller.length
                && $scroller.css('overflow-y') === 'auto'
                && $scroller[0].scrollHeight > $scroller[0].clientHeight + 8;

            if (canScrollScroller) {
                var top = $scroller.scrollTop() + $target.offset().top - $scroller.offset().top - 8;
                $scroller.stop(true).animate({ scrollTop: Math.max(0, top) }, 550);
                return;
            }

            $('html, body').stop(true).animate({
                scrollTop: Math.max(0, $target.offset().top - 72)
            }, 550);
        }

        $('.stock-tab').on('click', function() {
            if ($(this).hasClass('stock-tab--disabled')) {
                showServiceDetailsUnavailableAlert();
                return;
            }

            activateStockPanel($(this).data('panel'));
        });

        if (@json((bool) ($createPreAlertMode ?? false)) && @json((int) $shipmentRouteId) > 0) {
            window.setTimeout(function() {
                activateStockPanel('stock-panel-service');
                scrollFormToServiceDetails();
            }, 280);
        }

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

        function setPortCodeSelect($select, code) {
            if (typeof window.MarineCaddieSetPortCodeSelect === 'function') {
                window.MarineCaddieSetPortCodeSelect($select, code);
                return;
            }

            code = $.trim((code || '').toString());
            if (!code) {
                $select.val(null).trigger('change');
                return;
            }

            if ($select.find('option').filter(function () {
                return $(this).val() === code;
            }).length === 0) {
                $select.append(new Option(code, code, true, true));
            }

            $select.val(code).trigger('change');
        }

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
                setPortCodeSelect($('#departure-port-code'), '');
                return;
            }

            if (data.type === 'hub' || String(data.id).indexOf('hub:') === 0) {
                setPortCodeSelect($('#departure-port-code'), data.hub_code || hubDepartureCodes[data.id] || '');
                return;
            }

            setPortCodeSelect($('#departure-port-code'), data.port_code || '');
        }

        if ($('#pre-alert-shipment-input').length) {
            (function bindWorkflowShipmentSearch() {
                var $input = $('#pre-alert-shipment-input');
                var workflowPageUrl = @json($workflowPageUrl);
                var apiUrl = @json(route('api.shipments'));
                var currentId = @json($shipmentRouteId);

                function queryValue() {
                    return $.trim($input.val() || '');
                }

                function canSearch(q) {
                    if (!q || /^[A-Za-z0-9]{1,3}-$/.test(q)) {
                        return false;
                    }
                    if (/^[A-Za-z0-9]{2,3}-/.test(q)) {
                        return q.length >= 6;
                    }
                    return q.length >= 5;
                }

                function loadShipment(id) {
                    id = parseInt(id, 10);
                    if (!id || id === parseInt(currentId, 10)) {
                        return;
                    }
                    window.location.href = workflowPageUrl + '?shipment=' + encodeURIComponent(id);
                }

                $input.on('keydown', function(e) {
                    if (e.key !== 'Enter') {
                        return;
                    }
                    e.preventDefault();
                    var q = queryValue();
                    if (!canSearch(q)) {
                        return;
                    }
                    $.getJSON(apiUrl, { q: q }).done(function(data) {
                        var items = data.results || [];
                        if (items.length) {
                            loadShipment(items[0].id);
                            return;
                        }
                        if (typeof swal === 'function') {
                            swal({
                                title: 'Shipment not found',
                                text: 'No shipment matches "' + q + '".',
                                type: 'warning'
                            });
                        }
                    });
                });

                if (!currentId && $input[0] && $input[0].setSelectionRange) {
                    var val = $input.val() || '';
                    $input[0].setSelectionRange(val.length, val.length);
                }
            })();
        }

        if (!@json((bool) $shipmentRouteId)) {
            $('#shipment-edit-form').on('submit', function(e) {
                e.preventDefault();
            });
            $('#send-manifest-btn, #send-prealert-btn, #complete-prealert-btn, #finalize-shipment-btn').prop('disabled', true);
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
            setPortCodeSelect($('#departure-port-code'), '');
        });

        if ($('.cs-pillar--locked, .cs-field--locked').length) {
            $('.cs-pillar--locked :input, .cs-field--locked :input').attr('tabindex', '-1');
            $('.cs-pillar--locked select, .cs-field--locked select').on('select2:opening', function(e) {
                e.preventDefault();
            });
        }

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
            setPortCodeSelect($('#consignee-port-code'), data.port_code || '');
            $('#consignee-email').val(data.email || '');
            $('#consignee-att').val(data.contact_person || '');
            var $specialConsiderations = $('textarea[name="special_considerations_destination"]');
            if (!$specialConsiderations.closest('.cs-field--locked, .cs-pillar--locked').length) {
                $specialConsiderations.val(data.special_considerations || '');
            }
            applyConsigneeCode(data);
        }).on('select2:clear', function() {
            $('#consignee-address, #consignee-city, #consignee-district, #consignee-zip, #location, #consignee-email, #consignee-att').val('');
            setPortCodeSelect($('#consignee-port-code'), '');
            $('#consignee-country').val('').trigger('change');
            $('#consignee-party-code').val('');
            var $specialConsiderationsClear = $('textarea[name="special_considerations_destination"]');
            if (!$specialConsiderationsClear.closest('.cs-field--locked, .cs-pillar--locked').length) {
                $specialConsiderationsClear.val('');
            }
        });

        var shipmentUpdateStatusUrl = @json(route('shipments.update-status', $shipmentRouteId));
        var shipmentUpdateFlagsUrl = @json(route('shipments.update-flags', $shipmentRouteId));
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
                // Re-bind if previously attached under a clipping parent.
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                dropdownParent: dropdownParent
            });
        }

        function initHeaderFlagsSelect2($select, dropdownParent) {
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: dropdownParent,
                placeholder: 'Select flag',
                allowClear: false
            });
        }

        $('#status-edit-container .status-display').on('click', function(e) {
            if ($('#status-edit-container').hasClass('header-inline-edit--locked')) {
                return;
            }
            e.stopPropagation();
            closeHeaderInlineEditors('#status-edit-container');
            $(this).hide();
            $('#status-edit-container .status-select-wrapper').show();

            var $select = $('.select2-status-inline');
            // Body parent: correct under-field position + not clipped behind tabs.
            initHeaderSelect2($select, $(document.body));
            $select.select2('open');
        });

        $('#flags-edit-container .flags-display').on('click', function(e) {
            e.stopPropagation();
            closeHeaderInlineEditors('#flags-edit-container');
            $(this).hide();
            $('#flags-edit-container .flags-select-wrapper').show();

            var $select = $('.select2-flags-inline');
            initHeaderFlagsSelect2($select, $(document.body));
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

        function nextIrregularityIndex() {
            var max = -1;
            $('#irregularities-container .irregularity-item').each(function() {
                var idx = parseInt($(this).attr('data-irregularity-index'), 10);
                if (!isNaN(idx) && idx > max) {
                    max = idx;
                }
            });
            return max + 1;
        }

        $('#add-irregularity-btn').on('click', function() {
            var idx = nextIrregularityIndex();
            var newItem = `
                <div class="irregularity-item" data-irregularity-index="${idx}">
                    <div class="row">
                        <div class="col-md-2 pr-1"><div class="form-group-custom"><label>Date</label><input type="text" name="irregularities[${idx}][irregularity_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY"></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Irregularity</label><select name="irregularities[${idx}][irregularity_type]" class="form-control-sm-custom select2">${irregularityTypeOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Party responsible</label><select name="irregularities[${idx}][party_responsible]" class="form-control-sm-custom select2">${partyResponsibleOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Consequence</label><select name="irregularities[${idx}][consequence]" class="form-control-sm-custom select2">${consequenceOptionsHtml}</select></div></div>
                        <div class="col-md-2 px-1"><div class="form-group-custom"><label>Extra cost for MT (USD)</label><input type="text" name="irregularities[${idx}][extra_cost_mt_usd]" class="form-control-sm-custom"></div></div>
                        <div class="col-md-2 pl-1 d-flex align-items-end"><div class="form-group-custom flex-grow-1 mr-2"><label>Status</label><select name="irregularities[${idx}][status]" class="form-control-sm-custom select2">${irregularityStatusOptionsHtml}</select></div><button type="button" class="btn btn-link text-muted p-0 mb-2 remove-irregularity"><i class="ti-trash"></i></button></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1"><div class="form-group-custom"><label>Cause of irregularity</label><textarea name="irregularities[${idx}][cause_of_irregularity]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 px-1"><div class="form-group-custom"><label>Action taken</label><textarea name="irregularities[${idx}][action_taken]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 pl-1"><div class="form-group-custom"><label>Customer response</label><textarea name="irregularities[${idx}][customer_response]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1"><div class="form-group-custom"><label>Hub/agent comments</label><textarea name="irregularities[${idx}][hub_agent_comments]" class="form-control" style="font-size:11px;height:80px;"></textarea></div></div>
                        <div class="col-md-4 px-1"><div class="form-group-custom"><label>Handled by</label><input type="text" name="irregularities[${idx}][handled_by]" class="form-control-sm-custom"></div></div>
                    </div>
                </div>`;
            var $newItem = $(newItem);
            $('#irregularities-container').append($newItem);
            $newItem.find('.select2').select2({ placeholder: '', allowClear: false, width: '100%' });
            $newItem.find('.datepicker').datepicker({
                dateFormat: 'dd.mm.yy',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-10:c+10',
                beforeShow: function(input) {
                    $('.cs-pillar').css('z-index', '');
                    $(input).closest('.cs-pillar').css('z-index', 40);
                },
                onClose: function() {
                    $(this).closest('.cs-pillar').css('z-index', '');
                }
            });
            refreshIrregularityTabCount();
        });

        $(document).on('click', '.remove-irregularity', function() {
            $(this).closest('.irregularity-item').remove();
            refreshIrregularityTabCount();
        });

        function refreshIrregularityTabCount() {
            var count = $('#irregularities-container .irregularity-item').length;
            $('[data-irregularity-tab-count]').text(count);
        }

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

        var serviceTimeInputSelector = [
            '.flight-time-input',
            '.sea-leg-time-input',
            '.truck-leg-time-input',
            '.courier-leg-time-input',
            '.release-leg-time-input',
            '.hand-carry-leg-time-input',
            '.on-board-leg-time-input'
        ].join(', ');

        function formatServiceTimeMask(value) {
            var digits = String(value || '').replace(/\D/g, '').slice(0, 4);
            if (digits.length <= 2) {
                return digits;
            }
            return digits.slice(0, 2) + ':' + digits.slice(2);
        }

        $(document).on('input', serviceTimeInputSelector, function() {
            var $input = $(this);
            var formatted = formatServiceTimeMask($input.val());
            if ($input.val() !== formatted) {
                $input.val(formatted);
            }
        });

        $(document).on('blur', serviceTimeInputSelector, function() {
            var $input = $(this);
            var digits = String($input.val() || '').replace(/\D/g, '');
            if (!digits.length) {
                $input.val('');
                return;
            }
            if (digits.length === 1) {
                digits = '0' + digits;
            }
            if (digits.length === 2) {
                digits = digits + '00';
            }
            if (digits.length === 3) {
                digits = digits + '0';
            }
            $input.val(formatServiceTimeMask(digits));
        });

        $(document).on('keydown', serviceTimeInputSelector, function(e) {
            // Allow navigation / edit keys; block non-digit except colon (auto) and modifiers
            if (e.ctrlKey || e.metaKey || e.altKey) {
                return;
            }
            var allowed = [
                'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'
            ];
            if (allowed.indexOf(e.key) !== -1) {
                return;
            }
            if (/^\d$/.test(e.key)) {
                return;
            }
            e.preventDefault();
        });

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
                            <label>Flight Number</label>
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
            syncFinalizeChoiceButtonState();
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
            var firstLabel = rowIndex === 0 ? 'Bill of lading' : 'Port of departure';
            return `
                <div class="sea-freight-leg-row">
                    <div class="sea-leg-field sea-leg-first-field">
                        <div class="form-group-custom mb-0">
                            <label>${firstLabel}</label>
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

        function updateSeaFreightLegLabels() {
            $('#sea-freight-legs-container .sea-freight-leg-row').each(function(index) {
                var label = index === 0 ? 'Bill of lading' : 'Port of departure';
                $(this).find('.sea-leg-first-field label').text(label);
            });
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
            updateSeaFreightLegLabels();
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

        function stockRowConsigneeSummary(row) {
            var $row = $(row);
            var stockNo = $.trim($row.find('td').eq(4).text()) || '—';
            var vessel = $.trim($row.find('td').eq(1).text()) || '—';
            var pcs = $.trim($row.find('td').eq(5).text()) || '0';
            var weight = $.trim($row.find('td').eq(6).text()) || '0';

            return stockNo + ' / ' + vessel + ' / ' + pcs + ' pcs / ' + weight + ' kg';
        }

        function appendCommentsToDepartureHub(lines) {
            var summaries = (lines || []).filter(function(line) {
                return $.trim(String(line || '')) !== '';
            });
            if (!summaries.length) {
                return;
            }

            var $field = $('#comments_departure_hub, textarea[name="comments_departure_hub"]').first();
            if (!$field.length || $field.closest('.cs-field--locked, .cs-pillar--locked').length) {
                return;
            }

            var existing = $.trim($field.val() || '');
            $field.val(existing ? existing + '\n' + summaries.join('\n') : summaries.join('\n'));
        }

        function refreshStockItemsTable() {
            var count = $('#stock-items-table tbody tr.selected-stock-row').length;
            $('.stock-tab[data-panel="stock-panel-items"] [data-stock-tab-count]').text(count);
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
            var contactPerson = $.trim($('#consignee-att').val() || '');
            if (!contactPerson) {
                e.preventDefault();
                if (typeof swal === 'function') {
                    swal({
                        title: 'Validation error',
                        text: 'Please enter the contact person.',
                        type: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Please enter the contact person.');
                }
                $('#consignee-att').focus();
                return false;
            }

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
            var isLocked = status === 'Completed' || status === 'Cancelled';
            var serviceDetailsLocked = status === 'In process' && !@json((bool) ($workflowEditMode ?? false));
            $('#add-stock-items-btn').prop('disabled', isLocked);
            $('#repacked_items, #repacked_weight').prop('disabled', isLocked || serviceDetailsLocked);
            $('#stock_repacked_items, #stock_repacked_weight').prop('disabled', isLocked);
        }

        function bindRepackedInputSanitizers(selector) {
            $(selector).filter('[id$="_items"], [id="repacked_items"], [id="stock_repacked_items"]').on('input', function() {
                var sanitized = $(this).val().replace(/\D/g, '');
                if ($(this).val() !== sanitized) {
                    $(this).val(sanitized);
                }
            });

            $(selector).filter('[id$="_weight"], [id="repacked_weight"], [id="stock_repacked_weight"]').on('input', function() {
                var val = $(this).val().replace(/[^\d.]/g, '');
                var parts = val.split('.');
                if (parts.length > 2) {
                    val = parts[0] + '.' + parts.slice(1).join('');
                }
                if ($(this).val() !== val) {
                    $(this).val(val);
                }
            }).on('blur', function() {
                var raw = $.trim($(this).val());
                if (raw === '' || raw === '.') {
                    $(this).val('');
                    return;
                }
                var num = parseFloat(raw);
                if (!isNaN(num)) {
                    $(this).val(num.toFixed(2));
                }
            });
        }

        bindRepackedInputSanitizers('#repacked_items, #repacked_weight, #stock_repacked_items, #stock_repacked_weight');

       

        function syncFinalizeChoiceButtonState() {
            var status = ($('#shipment-current-status').val() || $('.header-meta-group .status-badge').text().trim());
            var isCompleted = status === 'Completed';
            var transitDestinationStocksReady = @json((bool) ($transitDestinationStocksReady ?? false));
            var $completeBtn = $('#finalize-shipment-complete-btn');
            var $transitBtn = $('#finalize-shipment-transit-btn');

            $completeBtn.prop('disabled', isCompleted);
            $completeBtn.attr('title', isCompleted ? 'Shipment already completed' : '');

            var $finalizeBtn = $('#finalize-shipment-btn');
            var shipmentLoaded = @json((int) $shipmentRouteId) > 0;
            var workflowAwaitingShipment = @json((bool) ($workflowAwaitingShipment ?? false));
            $finalizeBtn.prop('disabled', workflowAwaitingShipment || !shipmentLoaded || transitDestinationStocksReady);
            $finalizeBtn.attr(
                'title',
                workflowAwaitingShipment || !shipmentLoaded
                    ? 'Load a shipment first'
                    : (transitDestinationStocksReady ? 'Destination stocks already created' : '')
            );

            $transitBtn.prop('disabled', transitDestinationStocksReady);
            $transitBtn.attr(
                'title',
                transitDestinationStocksReady ? 'Destination stocks already created' : ''
            );
        }

        syncAddStockItemsButtonState();
        syncFinalizeChoiceButtonState();
        syncCompletePreAlertButtonState();
        syncServiceDetailsTabLock();

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
            var $visible = $('#stock-items-modal-table tbody tr:visible .modal-row-checkbox:not(:disabled)');
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
            $('#stock-items-modal-table tbody tr:visible .modal-row-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
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
            $('.modal-row-checkbox:checked:not(:disabled)').each(function() {
                var $row = $(this).closest('tr');
                var status = String($row.attr('data-status') || '').trim().toLowerCase();
                if (status === 'in progress') {
                    return;
                }
                selectedIds.push(String($(this).val()));
            });

            if (selectedIds.length === 0) {
                showStockModalError('Please select at least one stock item.');
                return;
            }

            if (!updateRealtimeHubValidation()) {
                return;
            }

            var addedLines = [];

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
                var $addedRow = $('#stock-items-table tbody tr.selected-stock-row[data-crr-id="' + id + '"]').last();
                addedLines.push('Added stock: ' + stockRowConsigneeSummary($addedRow));
            });
            appendCommentsToDepartureHub(addedLines);
            refreshStockItemsTable();
            syncCrrHiddenInputs();
            $('#stock-items-modal').modal('hide');
        });

        $(document).on('click', '.remove-stock-item', function() {
            var $row = $(this).closest('tr.selected-stock-row');
            appendCommentsToDepartureHub(['Removed stock: ' + stockRowConsigneeSummary($row)]);
            $row.remove();
            refreshStockItemsTable();
            syncCrrHiddenInputs();
        });
        
        // Tab Logic for Document Tabs
        $('.doc-tab').on('click', function() {
            var target = $(this).data('target');
            var $panel = $('#doc-panel-' + target);
            
            $('.doc-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.doc-panel').removeClass('active');
            if ($panel.length) {
                void $panel[0].offsetWidth;
                $panel.addClass('active');
            }
        });

        $(document).on('click', '.po-document-link', function(e) {
            e.preventDefault();
            var pdfUrl = $(this).data('pdf-url');
            var title = $(this).data('title') || 'Combined PO documents';
            if (!pdfUrl) {
                return;
            }
            $('#pdfPreviewModalLabel').text(title);
            $('#pdf-preview-modal').modal('show');
            loadPdfIntoPreviewFrame(pdfUrl).catch(function (err) {
                if (typeof swal === 'function') {
                    swal({ title: 'Preview failed', text: err.message || 'Could not open document.', type: 'error' });
                } else {
                    alert(err.message || 'Could not open document.');
                }
            });
        });

        $('#pdf-preview-modal').on('hidden.bs.modal', function() {
            revokePdfPreviewBlob();
            $('#pdf-preview-mobile-fallback').hide();
            $('#pdf-preview-frame').show().attr('src', 'about:blank');
        });

        var shipmentDocDropzone = $('#shipment-doc-dropzone');
        var shipmentDocFileInput = $('#shipment-doc-file-input');
        var shipmentDocList = $('#shipment-documents-list');
        var shipmentDocumentTypeOptions = @json($shipmentDocumentTypeOptions ?? \App\Models\ShipmentDocument::fileTypeOptions());

        function initShipmentDocTypeSelect($select) {
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
            initShipmentDocTypeSelect(shipmentDocList.find('.shipment-uploaded-doc[data-id="' + doc.id + '"] .shipment-doc-type-select'));
            updateShipmentDocumentTabCount();
        }

        $('.shipment-doc-type-select').each(function() {
            initShipmentDocTypeSelect($(this));
        });

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
                url: '{{ route('shipments.documents.upload', $shipmentRouteId) }}',
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
                    var $row = shipmentDocList.find('.shipment-uploaded-doc[data-id="' + docId + '"]');
                    var $select = $row.find('.shipment-doc-type-select');
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    $row.remove();
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

                var dirty = !allowLeave && formSnapshot() !== initialSnapshot;

                $saveBtn.prop('disabled', !dirty);
                $saveBtn.addClass('mc-save-btn');
                $saveBtn.toggleClass('mc-save-btn--dirty', dirty);
                $saveBtn.toggleClass('mc-save-btn--idle', !dirty);
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
        @include('partials.compose-color-tools-script')
    });
</script>
@endsection
