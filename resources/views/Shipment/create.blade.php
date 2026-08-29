@extends('layouts.app')

@section('styles')

    <!-- Select 2 css -->

    <!-- Date-range picker css  -->

    <style>
        /*
         * Create shipment: global .card is overflow:hidden + this page used h-100,
         * which clipped Stock items / Save and left a blank white void.
         * Keep controls at --mc-control-height (34px); ignore legacy inline 30px.
         */
        .page-body .create-shipment-card.card {
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }
        .page-body .create-shipment-card > .card-block {
            overflow: visible !important;
        }
        #shipment-form .input-group,
        #shipment-form .input-group[style*="height"] {
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            max-height: var(--mc-control-height, 34px) !important;
            flex-wrap: nowrap !important;
            align-items: stretch !important;
        }
        #shipment-form .input-group > .form-control {
            height: 100% !important;
            min-height: 0 !important;
            max-height: none !important;
        }
        #shipment-form .input-group > .select2-container {
            height: 100% !important;
            width: 100% !important;
        }
        #shipment-form .input-group .select2-container--default .select2-selection--single {
            height: 100% !important;
            min-height: 100% !important;
            max-height: 100% !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        #shipment-form .input-group-addon {
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            max-height: var(--mc-control-height, 34px) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 10px !important;
            box-sizing: border-box !important;
        }
        #shipment-form .select2-container--default .select2-selection--single {
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            max-height: var(--mc-control-height, 34px) !important;
        }
        #shipment-form .form-group {
            overflow: visible;
        }
        #shipment-form textarea.form-control {
            height: auto !important;
            min-height: 72px;
            max-height: none !important;
        }

        /* —— Create shipment visual layout (no behavior change) —— */
        .page-body .create-shipment-card > .card-block {
            padding: 18px 20px 20px !important;
        }

        #shipment-form > .row.cs-pillars {
            margin-left: -10px;
            margin-right: -10px;
            align-items: stretch;
        }

        #shipment-form > .row.cs-pillars > .custom-col {
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 16px;
            display: flex;
        }

        .cs-pillar {
            width: 100%;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            padding: 14px 14px 12px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
        }

        .cs-pillar__title {
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

        #shipment-form .cs-pillar label,
        #shipment-form .cs-pillar .form-group > label,
        #shipment-form .cs-pillar .form-check-label {
            font-size: 11px !important;
            font-weight: 600;
            color: #475569 !important;
            margin-bottom: 4px;
            display: inline-block;
            letter-spacing: 0.01em;
        }

        #shipment-form .cs-pillar .form-group {
            margin-bottom: 12px !important;
        }

        #shipment-form .cs-pillar .form-control,
        #shipment-form .cs-pillar .filter-input {
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            border-color: #d6e3ee !important;
            background: #fff !important;
            color: #0e1d4a;
        }

        #shipment-form .cs-pillar textarea.form-control,
        #shipment-form .cs-pillar textarea.filter-input {
            height: auto !important;
            min-height: 72px !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
            line-height: 1.4;
            resize: vertical;
        }

        #shipment-form .cs-pillar .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        #shipment-form .cs-pillar .input-group-addon {
            background: #f0f9fc !important;
            border: 1px solid #d6e3ee !important;
            border-left: none !important;
            color: #0088c7 !important;
            border-radius: 0 8px 8px 0 !important;
        }

        #shipment-form .cs-pillar .select2-container--default .select2-selection--single {
            border-radius: 8px !important;
            border-color: #d6e3ee !important;
        }

        #shipment-form .cs-pillar .input-group .select2-container--default .select2-selection--single {
            border-radius: 8px 0 0 8px !important;
        }

        #shipment-form .cs-pillar .form-check {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 0 0 12px;
            padding: 8px 10px !important;
            background: #f5f9fc;
            border: 1px solid #e4edf4;
            border-radius: 8px;
        }

        #shipment-form .cs-pillar .form-check input[type="checkbox"] {
            margin-top: 1px;
            flex: 0 0 auto;
            accent-color: #0088c7;
        }

        #shipment-form .cs-pillar .form-check-label {
            margin: 0 !important;
            line-height: 1.35;
            color: #334155 !important;
        }

        .cs-tabs-shell {
            margin-top: 8px;
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 10px 28px rgba(14, 29, 74, 0.05);
        }

        .cs-tabs-shell > .nav-tabs.md-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin: 0 !important;
            padding: 8px 10px 0;
            border-bottom: 1px solid #e8edf2 !important;
            background: linear-gradient(135deg, rgba(240, 250, 251, 0.95) 0%, #ffffff 55%, #f8fafc 100%) !important;
        }

        .cs-tabs-shell > .nav-tabs .nav-item {
            margin: 0;
        }

        .cs-tabs-shell > .nav-tabs .nav-link {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px 12px !important;
            margin: 0;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            background: transparent !important;
            border: 1px solid transparent !important;
            border-bottom: none !important;
            border-radius: 10px 10px 0 0 !important;
            line-height: 1.2;
        }

        .cs-tabs-shell > .nav-tabs .nav-link.active {
            color: #0e1d4a !important;
            background: #fff !important;
            border-color: #e8edf2 !important;
            border-bottom-color: #fff !important;
            box-shadow: 0 -1px 0 #fff;
        }

        .cs-tabs-shell > .nav-tabs .nav-item .slide {
            display: none !important;
        }

        .cs-tabs-shell > .tab-content {
            border: none !important;
            background: #fff;
        }

        #shipment-form .stock-totals {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 14px;
            padding: 12px 14px;
            margin: 0;
            border-top: 1px solid #eef2f7;
            background: #f8fafc;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }

        #shipment-form .stock-totals > span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #475569;
        }

        #shipment-form .stock-totals > span > span {
            color: #0e1d4a;
            font-weight: 700;
        }

        #shipment-form .stock-totals .ml-auto {
            margin-left: auto;
        }

        #shipment-form #add-stock-items-btn {
            height: 34px !important;
            padding: 0 14px !important;
            border-radius: 8px !important;
            font-weight: 700;
            border-color: #0088c7 !important;
            color: #0088c7 !important;
        }

        #shipment-form #add-stock-items-btn:hover {
            background: #0088c7 !important;
            color: #fff !important;
        }

        #shipment-form #stock-items-table thead th {
            background: #f8fafc !important;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 10px 12px;
        }

        /* Footer — fixed full-width under content (appended to body in JS) */
        body.create-shipment-page {
            padding-bottom: 84px;
        }

        body.create-shipment-page .create-shipment-footer.edit-footer {
            position: fixed !important;
            left: var(--spacing-sidebar, 13.25rem) !important;
            right: 0 !important;
            bottom: 0 !important;
            top: auto !important;
            margin: 0 !important;
            width: calc(100vw - var(--spacing-sidebar, 13.25rem)) !important;
            max-width: none !important;
            min-width: 0 !important;
            padding: 12px 28px !important;
            box-sizing: border-box !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(8px);
            display: flex !important;
            align-items: center !important;
            gap: 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 0 !important;
            z-index: 1040 !important;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06);
        }

        body.create-shipment-page .create-shipment-footer .btn-save-custom {
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

        body.create-shipment-page .create-shipment-footer .btn-save-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.34);
            color: #fff;
        }

        body.create-shipment-page .create-shipment-footer .btn-cancel-custom {
            color: #008080;
            font-size: 13px;
            text-decoration: none;
            font-weight: 700;
            background: transparent;
            border: none;
            padding: 0;
            box-shadow: none;
        }

        body.create-shipment-page .create-shipment-footer .btn-cancel-custom:hover {
            text-decoration: underline;
            color: #0e1d4a;
        }

        @media (max-width: 991.98px) {
            body.create-shipment-page .create-shipment-footer.edit-footer {
                left: 0 !important;
                width: 100vw !important;
                padding: 12px 16px !important;
            }
        }

        @media (max-width: 767.98px) {
            #shipment-form .stock-totals .ml-auto {
                margin-left: 0;
                width: 100%;
            }

            #shipment-form #add-stock-items-btn {
                width: 100%;
            }

            body.create-shipment-page .create-shipment-footer.edit-footer {
                flex-wrap: wrap;
                gap: 12px;
            }
        }

        /* High Density Table Styles */
        #offices-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        #offices-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }

        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
        }

        #offices-table th,
        #offices-table td {
            white-space: nowrap !important;
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

        .filter-group {
            display: flex;
            align-items: stretch;
            border: 1px solid #ced4da;
            padding: 0 !important;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
        }

        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin: 0 !important;
            padding: 0 10px !important;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #e2e8f0;
            height: auto;
            display: inline-flex;
            align-items: center;
            background: #f8fafc;
            flex: 0 0 auto;
        }

        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 8px !important;
            background: transparent !important;
            width: 100%;
            min-width: 0;
            flex: 1 1 auto;
        }

        .filter-group .select2-container {
            flex: 1 1 auto;
            min-width: 0;
            width: 100% !important;
        }

        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
            min-height: 30px !important;
        }

        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 8px !important;
        }

        .filter-group i {
            color: #0088c7;
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

        /* Mobile/tablet: row negative margins + thin custom-col padding clipped labels & calendar icons */
        @media (max-width: 991.98px) {
            #shipment-form .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            #shipment-form .row > [class*="col-"],
            #shipment-form .custom-col,
            #shipment-form .form-group[class*="col-"] {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            #shipment-form > .row > .col-md-4.custom-col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            #shipment-form .col-md-6,
            #shipment-form .col-md-6.pr-1,
            #shipment-form .col-md-6.pl-1,
            #shipment-form .col-md-4.pr-1,
            #shipment-form .col-md-4.px-1,
            #shipment-form .col-md-4.pl-1,
            #shipment-form .form-group.col-md-6 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .page-body > .row > .col-md-12.pr-2,
            .page-body .create-shipment-card > .card-block {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            #shipment-form > .row.cs-pillars > .custom-col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            #shipment-form label {
                overflow: visible;
                white-space: normal;
                line-height: 1.3;
            }

            #shipment-form .input-group {
                flex-wrap: nowrap;
                width: 100%;
                max-width: 100%;
            }

            #shipment-form .input-group .form-control {
                min-width: 0;
            }

            #shipment-form .input-group-addon {
                flex: 0 0 auto;
                max-width: none;
                padding: 0 10px !important;
            }

            #shipment-form .select2-container {
                width: 100% !important;
                max-width: 100%;
            }

            .airfreight-flight-row,
            .sea-freight-leg-row,
            .truck-leg-row {
                flex-wrap: wrap;
            }

            .airfreight-flight-row .flight-field-time,
            .sea-freight-leg-row .sea-leg-field-time,
            .truck-leg-row .truck-leg-field-time {
                flex: 1 1 120px;
                max-width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .modal-body .filter-group[style*="width"] {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100%;
            }
        }

        #shipment-form .filter-input:not(textarea) {
            height: var(--mc-control-height, 34px);
            font-size: 12px;
            border-radius: 8px;
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

        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 220px);
            width: 100%;
            position: relative;
        }

        .office-table {
            min-width: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        @media (min-width: 1200px) {
            .office-table {
                min-width: 1500px;
            }
        }
        .table-scroll-wrapper .office-table,
        .dataTables_wrapper .office-table {
            min-width: 1100px;
        }

        .office-table thead th {
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

        .office-table tbody td {
            padding: 6px 8px;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
        }

        .office-table th,
        .office-table td {
            white-space: nowrap;
        }

        .office-table thead th:first-child:after,
        .office-table thead th:first-child:before {
            display: none !important;
        }

        .office-table thead th:first-child {
            padding-right: 10px !important;
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
            height: var(--mc-control-height, 34px) !important;
            min-height: var(--mc-control-height, 34px) !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            min-height: var(--mc-control-height, 34px) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #4b5563 !important;
            line-height: 1.25 !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            height: 100% !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        #shipment-form .select2-container--default .select2-selection--single {
            position: relative !important;
        }

        #shipment-form .select2-container--default .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute !important;
            top: 0 !important;
            bottom: 0 !important;
            right: 6px !important;
            height: auto !important;
            width: 20px !important;
            margin: 0 !important;
            transform: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #shipment-form .select2-container--default .select2-selection--single .select2-selection__arrow b,
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            position: static !important;
            top: auto !important;
            left: auto !important;
            margin: 0 !important;
            transform: none !important;
            border-color: #666 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
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

        .btn-filter-toggle:hover,
        .btn-filter-toggle:focus,
        .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 150px);
            width: 100%;
            position: relative;
        }

        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.03);
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

        .form-group-custom {
            margin-bottom: 12px;
        }
        .form-group-custom label {
            font-size: 13px !important;
            color: #3c485a;
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

</style>
@endsection

@section('content')
    <script>document.body.classList.add('create-shipment-page');</script>
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
                                <!-- Base Style - Compact start -->
                                <div class="row">
                                    <!-- Left Card: Pillars + Tabs + Actions -->
                                    <div class="col-md-12 pr-2">
                                        <div class="card create-shipment-card mb-0">
                                            <div class="card-block">
                                                <form id="shipment-form" method="POST" action="{{ route('shipments.store') }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="{{ old('status', 'In process') }}">
                                                    <div id="crr-ids-container"></div>

                                                    @if (session('success'))
                                                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 12px;">
                                                            {{ session('success') }}
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                    @endif
                                                <div class="row cs-pillars">
                                                    <!-- Pillar 1: Departure -->
                                                    <div class="col-md-4 custom-col">
                                                        <div class="cs-pillar">
                                                        <div class="cs-pillar__title">Departure</div>
                                                        <div class="row">
                                                        <div class="form-group col-md-12 mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Departure</label>
                                                            <div class="input-group mb-0" style="height: 30px;">
                                                                <select id="departure-select" name="departure" class="form-control select2-departure">
                                                                    <option></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <x-forms.port-select
                                                            name="departure_port_code"
                                                            id="departure-port-code"
                                                            label="Port code"
                                                            wrapperClass="form-group col-md-12 mb-2"
                                                            labelClass="mb-0"
                                                            class="form-control"
                                                        />
                                                        <div class="form-group col-md-6 mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Service <span class="text-danger">*</span></label>
                                                            <select name="service" class="form-control select2" required>
                                                                <option></option>
                                                                <option {{ old('service') === 'Courier' ? 'selected' : '' }}>Courier</option>
                                                                <option {{ old('service') === 'Airfreight' ? 'selected' : '' }}>Airfreight</option>
                                                                <option {{ old('service') === 'Sea freight' ? 'selected' : '' }}>Sea freight</option>
                                                                <option {{ old('service') === 'Truck' ? 'selected' : '' }}>Truck</option>
                                                                <option {{ old('service') === 'Release' ? 'selected' : '' }}>Release</option>
                                                                <option {{ old('service') === 'Hand Carry' ? 'selected' : '' }}>Hand Carry</option>
                                                                <option {{ old('service') === 'On-board delivery' ? 'selected' : '' }}>On-board delivery</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-6 mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Additional service </label>
                                                            <select name="additional_service" class="form-control select2">
                                                                <option></option>
                                                                @php
                                                                    $additionalServiceOptions = [
                                                                        'Console',
                                                                        'Economy',
                                                                        'Express',
                                                                        'Weekly console',
                                                                        'Normal',
                                                                        'Barge station',
                                                                        'Truck',
                                                                        'Via ship chandler',
                                                                        'On arrival',
                                                                        'Major storing',
                                                                        'Full container load (FCL)',
                                                                        'Less container load (LCL)',
                                                                    ];
                                                                @endphp
                                                                @foreach ($additionalServiceOptions as $additionalServiceOption)
                                                                    <option value="{{ $additionalServiceOption }}" {{ old('additional_service') === $additionalServiceOption ? 'selected' : '' }}>{{ $additionalServiceOption }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                        <div class="row">
                                                            <div class="col-md-6 pr-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Preferred
                                                                        shipment date</label>
                                                                    <div class="input-group mb-0" style="height: 30px;">
                                                                        <input type="text" name="preferred_shipment_date"
                                                                            class="form-control filter-input datepicker"
                                                                            placeholder="DD.MM.YYYY" value="{{ old('preferred_shipment_date') }}">
                                                                        <span class="input-group-addon"
                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                            <i class="ti-calendar"
                                                                                style="font-size: 12px;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            
                                                            <div class="col-md-6 pl-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Deadline
                                                                        arrival</label>
                                                                    <div class="input-group mb-0" style="height: 30px;">
                                                                        <input type="text" name="deadline_arrival"
                                                                            class="form-control filter-input datepicker"
                                                                            placeholder="DD.MM.YYYY" value="{{ old('deadline_arrival') }}">
                                                                        <span class="input-group-addon"
                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                            <i class="ti-calendar"
                                                                                style="font-size: 12px;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 pr-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Vessel
                                                                        ETA</label>
                                                                    <div class="input-group mb-0" style="height: 30px;">
                                                                        <input type="text" name="vessel_eta"
                                                                            class="form-control filter-input datepicker"
                                                                            placeholder="DD.MM.YYYY" value="{{ old('vessel_eta') }}">
                                                                        <span class="input-group-addon"
                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                            <i class="ti-calendar"
                                                                                style="font-size: 12px;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 pl-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Vessel
                                                                        ETD</label>
                                                                    <div class="input-group mb-0" style="height: 30px;">
                                                                        <input type="text" name="vessel_etd"
                                                                            class="form-control filter-input datepicker"
                                                                            placeholder="DD.MM.YYYY" value="{{ old('vessel_etd') }}">
                                                                        <span class="input-group-addon"
                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                            <i class="ti-calendar"
                                                                                style="font-size: 12px;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 pr-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Pre-alert
                                                                        reminder</label>
                                                                    <div class="input-group mb-0" style="height: 30px;">
                                                                        <input type="text" name="pre_alert_reminder"
                                                                            class="form-control filter-input datepicker"
                                                                            placeholder="DD.MM.YYYY" value="{{ old('pre_alert_reminder') }}">
                                                                        <span class="input-group-addon"
                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                            <i class="ti-calendar"
                                                                                style="font-size: 12px;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 pl-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Customer
                                                                        reference</label>
                                                                    <input type="text" name="customer_reference" class="form-control filter-input"
                                                                        placeholder="" value="{{ old('customer_reference') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-check p-0 mt-2">
                                                            <input type="checkbox" id="not_conso" name="not_applicable_for_consolidation" value="1"
                                                                {{ old('not_applicable_for_consolidation') ? 'checked' : '' }}
                                                                style="width: 14px; height: 14px; vertical-align: middle;">
                                                            <label class="form-check-label ml-1" for="not_conso"
                                                                style="font-size: 11px; vertical-align: middle;">Not
                                                                applicable for consolidation</label>
                                                        </div>
                                                        </div>
                                                    </div>

                                                    <!-- Pillar 2: Consignee -->
                                                    <div class="col-md-4 custom-col">
                                                        <div class="cs-pillar">
                                                        <div class="cs-pillar__title">Consignee</div>
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Consignee</label>
                                                            <div class="input-group mb-0" style="height: 30px;">
                                                                <select id="consignee-select" name="consignee" class="form-control select2-consignee">
                                                                    <option></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Consignee
                                                                address</label>
                                                            <textarea id="consignee-address" name="consignee_address" class="form-control filter-input" rows="3"
                                                                style="height: auto !important; min-height: 80px;">{{ old('consignee_address') }}</textarea>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4 pr-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0"
                                                                        style="font-size: 11px;">City</label>
                                                                    <input type="text" id="consignee-city" name="consignee_city" class="form-control filter-input"
                                                                        placeholder="" value="{{ old('consignee_city') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 px-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0"
                                                                        style="font-size: 11px;">District</label>
                                                                    <input type="text" id="consignee-district" name="consignee_district" class="form-control filter-input"
                                                                        placeholder="" value="{{ old('consignee_district') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pl-1">
                                                                <div class="form-group mb-2">
                                                                    <label class="mb-0" style="font-size: 11px;">Zip
                                                                        code</label>
                                                                    <input type="text" id="consignee-zip" name="consignee_zip" class="form-control filter-input"
                                                                        placeholder="" value="{{ old('consignee_zip') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <x-forms.country-select
                                                            name="consignee_country"
                                                            id="consignee-country"
                                                            label="Consignee country"
                                                            :countries="$countries"
                                                            valueKey="name"
                                                            wrapperClass="form-group mb-2"
                                                            labelClass="mb-0"
                                                            class="form-control"
                                                            placeholder="Select country"
                                                            :allowClear="true"
                                                        />
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Contact person <span class="text-danger">*</span></label>
                                                            <input type="text" id="consignee-att" name="consignee_att" class="form-control filter-input"
                                                                placeholder="" value="{{ old('consignee_att') }}" required>
                                                        </div>
                                                        <x-forms.port-select
                                                            name="consignee_port_code"
                                                            id="consignee-port-code"
                                                            label="Port code"
                                                            wrapperClass="form-group mb-2"
                                                            labelClass="mb-0"
                                                            class="form-control"
                                                            :required="true"
                                                        />
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Location</label>
                                                            <input type="text" id="location" name="location" class="form-control filter-input"
                                                                placeholder="" value="{{ old('location') }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Consignee
                                                                email</label>
                                                            <input type="email" id="consignee-email" name="consignee_email" class="form-control filter-input"
                                                                placeholder="" value="{{ old('consignee_email') }}">
                                                        </div>
                                                        </div>
                                                    </div>

                                                    <!-- Pillar 3: Account Manager -->
                                                    <div class="col-md-4 custom-col">
                                                        <div class="cs-pillar">
                                                        <div class="cs-pillar__title">Account &amp; comments</div>
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Account
                                                                manager</label>
                                                            <select id="account-manager-select" name="account_manager" class="form-control select2-account-manager">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label class="mb-0" style="font-size: 11px;">Special
                                                                considerations for destination</label>
                                                            <textarea name="special_considerations_destination" class="form-control filter-input" rows="2"
                                                                style="height: auto !important; min-height: 50px;">{{ old('special_considerations_destination') }}</textarea>
                                                        </div>
                                                        <div class="form-check p-0 mb-3">
                                                            <input type="checkbox" id="skip_instruction_dest" name="skip_instruction_dest" value="1"
                                                                {{ old('skip_instruction_dest') ? 'checked' : '' }}
                                                                style="width: 14px; height: 14px; vertical-align: middle;">
                                                            <label class="form-check-label ml-1" for="skip_instruction_dest"
                                                                style="font-size: 11px; vertical-align: middle;">Don't show
                                                                on shipping instruction</label>
                                                        </div>
                                                        <div class="form-group mb-1">
                                                            <label class="mb-0" style="font-size: 11px;">Comments to
                                                                departure hub</label>
                                                            <textarea name="comments_departure_hub" class="form-control filter-input" rows="2"
                                                                style="height: auto !important; min-height: 50px;">{{ old('comments_departure_hub') }}</textarea>
                                                        </div>
                                                        <div class="form-check p-0 mb-3">
                                                            <input type="checkbox" id="skip_instruction_hub" name="skip_instruction_hub" value="1"
                                                                {{ old('skip_instruction_hub') ? 'checked' : '' }}
                                                                style="width: 14px; height: 14px; vertical-align: middle;">
                                                            <label class="form-check-label ml-1" for="skip_instruction_hub"
                                                                style="font-size: 11px; vertical-align: middle;">Don't show
                                                                on shipping instruction</label>
                                                        </div>
                                                        <div class="form-group mb-1">
                                                            <label class="mb-0" style="font-size: 11px;">Comments to
                                                                consignee</label>
                                                            <textarea name="comments_consignee" class="form-control filter-input" rows="2"
                                                                style="height: auto !important; min-height: 50px;">{{ old('comments_consignee') }}</textarea>
                                                        </div>
                                                        <div class="form-check p-0 mb-3">
                                                            <input type="checkbox" id="skip_prealert" name="skip_prealert" value="1"
                                                                {{ old('skip_prealert') ? 'checked' : '' }}
                                                                style="width: 14px; height: 14px; vertical-align: middle;">
                                                            <label class="form-check-label ml-1" for="skip_prealert"
                                                                style="font-size: 11px; vertical-align: middle;">Don't show
                                                                on pre-alert</label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check p-0">
                                                                    <input type="checkbox" id="project_logistics" name="project_logistics" value="1"
                                                                        {{ old('project_logistics') ? 'checked' : '' }}
                                                                        style="width: 14px; height: 14px; vertical-align: middle;">
                                                                    <label class="form-check-label ml-1"
                                                                        for="project_logistics"
                                                                        style="font-size: 11px; vertical-align: middle;">Project
                                                                        logistics</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check p-0">
                                                                    <input type="checkbox" id="port_agency" name="port_agency" value="1"
                                                                        {{ old('port_agency') ? 'checked' : '' }}
                                                                        style="width: 14px; height: 14px; vertical-align: middle;">
                                                                    <label class="form-check-label ml-1" for="port_agency"
                                                                        style="font-size: 11px; vertical-align: middle;">Port
                                                                        agency</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tabbed Section -->
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="cs-tabs-shell">
                                                        <ul class="nav nav-tabs md-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab"
                                                                    href="#stock-items" role="tab">Stock
                                                                    items (0)</a>
                                                                <div class="slide" style="background: #008080;"></div>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab"
                                                                    href="#service-details" role="tab">Service
                                                                    details</a>
                                                                <div class="slide" style="background: #008080;"></div>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#irregularities"
                                                                    role="tab">Irregularities</a>
                                                                <div class="slide" style="background: #008080;"></div>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content p-0">
                                                            <div class="tab-pane active" id="stock-items" role="tabpanel">
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover mb-0"
                                                                        id="stock-items-table" style="font-size: 11px;">
                                                                        <thead>
                                                                            <tr style="background: #f9fafb;">
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Hub</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Vessel</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    PO no</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Supplier</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Stock no</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Pcs</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Weight</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    CBM</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Value</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Status</th>
                                                                                <th
                                                                                    style="font-weight: 500; font-size: 11px;">
                                                                                    Remove</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr id="empty-row">
                                                                                <td colspan="11"
                                                                                    class="text-center py-4 text-muted">No
                                                                                    stock items added yet.</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="stock-totals">
                                                                    <span>Total packages: <span id="stock-total-packages">0 pcs</span></span>
                                                                    <span>Total weight: <span id="stock-total-weight">0.00 kg</span></span>
                                                                    <span>Total CBM: <span id="stock-total-cbm">0.00</span></span>
                                                                    <span>Total value: <span id="stock-total-value">0.00</span></span>
                                                                    <div class="ml-auto">
                                                                        <button type="button" id="add-stock-items-btn" class="btn btn-outline-teal px-3 py-1 ml-2"
                                                                            style="font-size: 11px; height: 30px;">Add stock items</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane" id="service-details" role="tabpanel">
                                                                <div id="service-details-placeholder" class="p-4 text-center text-muted">Select a service type to enter service details.</div>
                                                                <div id="service-details-airfreight" class="p-3" style="display: none;">
                                                                    <div id="airfreight-flights-container">
                                                                        @if (old('flights'))
                                                                            @foreach (old('flights') as $index => $flight)
                                                                                @include('Shipment.partials.airfreight-flight-row', ['flight' => (object) $flight, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-airfreight-flight-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add flight</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-sea-freight" class="p-3" style="display: none;">
                                                                    <div id="sea-freight-legs-container">
                                                                        @if (old('sea_legs'))
                                                                            @foreach (old('sea_legs') as $index => $leg)
                                                                                @include('Shipment.partials.sea-freight-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-sea-freight-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add leg</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-truck" class="p-3" style="display: none;">
                                                                    <div id="truck-legs-container">
                                                                        @if (old('truck_legs'))
                                                                            @foreach (old('truck_legs') as $index => $leg)
                                                                                @include('Shipment.partials.truck-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-truck-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add truck</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-courier" class="p-3" style="display: none;">
                                                                    <div id="courier-legs-container">
                                                                        @if (old('courier_legs'))
                                                                            @foreach (old('courier_legs') as $index => $leg)
                                                                                @include('Shipment.partials.courier-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-courier-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add courier</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-release" class="p-3" style="display: none;">
                                                                    <div id="release-legs-container">
                                                                        @if (old('release_legs'))
                                                                            @foreach (old('release_legs') as $index => $leg)
                                                                                @include('Shipment.partials.release-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-release-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add release</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-hand-carry" class="p-3" style="display: none;">
                                                                    <div id="hand-carry-legs-container">
                                                                        @if (old('hand_carry_legs'))
                                                                            @foreach (old('hand_carry_legs') as $index => $leg)
                                                                                @include('Shipment.partials.hand-carry-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-hand-carry-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add hand carry</a>
                                                                    </div>
                                                                </div>
                                                                <div id="service-details-on-board" class="p-3" style="display: none;">
                                                                    <div id="on-board-legs-container">
                                                                        @if (old('on_board_legs'))
                                                                            @foreach (old('on_board_legs') as $index => $leg)
                                                                                @include('Shipment.partials.on-board-leg-row', ['leg' => (object) $leg, 'index' => $index])
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex justify-content-end pt-2">
                                                                        <a href="#" id="add-on-board-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add delivery</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane" id="irregularities" role="tabpanel">
                                                                @php
                                                                    $irregularityTypeOptions = [
                                                                        'Customer complaint',
                                                                        'Shipment missing (and found)',
                                                                        'Delayed shipment',
                                                                        'Damage to shipment',
                                                                        'Incorrect or missing shipping documentation',
                                                                        'Cross label',
                                                                        'Shipment short shipped',
                                                                        'Shipment misrouted',
                                                                        'Slow or unclear communication by agent',
                                                                        'Quotation unclear or incomplete',
                                                                        'Other',
                                                                        'No cost provided by agent',
                                                                        'Send pre-alert in wrong format',
                                                                        'Billing discrepancy',
                                                                    ];
                                                                    $partyResponsibleOptions = [
                                                                        'Marinetrans',
                                                                        'Departing Hub',
                                                                        'Receiving Agent',
                                                                        'Customer',
                                                                        'Carrier',
                                                                    ];
                                                                    $consequenceOptions = [
                                                                        'Deadline and delivery met',
                                                                        'Original deadline missed, but vessel/destination reached',
                                                                        'Deadline and vessel missed',
                                                                        'Official customer claim',
                                                                    ];
                                                                    $statusOptions = [
                                                                        'Not started',
                                                                        'In process',
                                                                        'Closed',
                                                                    ];
                                                                @endphp
                                                                <div class="p-3" id="irregularities-container">
                                                                    <!-- Irregularity Item 1 -->
                                                                    <div class="irregularity-item border-bottom pb-4 mb-4" data-irregularity-index="0">
                                                                        <div class="row">
                                                                            <div class="col-md-2 pr-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Date</label>
                                                                                    <div class="input-group mb-0"
                                                                                        style="height: 30px;">
                                                                                        <input type="text" name="irregularities[0][irregularity_date]"
                                                                                            class="form-control filter-input datepicker"
                                                                                            placeholder="DD.MM.YYYY">
                                                                                        <span class="input-group-addon"
                                                                                            style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                                                            <i class="ti-calendar"
                                                                                                style="font-size: 12px;"></i>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Irregularity</label>
                                                                                    <select name="irregularities[0][irregularity_type]" class="form-control select2">
                                                                                        <option></option>
                                                                                        @foreach ($irregularityTypeOptions as $option)
                                                                                            <option>{{ $option }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Party
                                                                                        responsible</label>
                                                                                    <select name="irregularities[0][party_responsible]" class="form-control select2">
                                                                                        <option></option>
                                                                                        @foreach ($partyResponsibleOptions as $option)
                                                                                            <option>{{ $option }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Consequence</label>
                                                                                    <select name="irregularities[0][consequence]" class="form-control select2">
                                                                                        <option></option>
                                                                                        @foreach ($consequenceOptions as $option)
                                                                                            <option>{{ $option }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Extra cost
                                                                                        for MT (USD)</label>
                                                                                    <input type="text" name="irregularities[0][extra_cost_mt_usd]"
                                                                                        class="form-control filter-input"
                                                                                        placeholder="">
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                class="col-md-2 pl-1 d-flex align-items-end">
                                                                                <div
                                                                                    class="form-group mb-2 flex-grow-1 mr-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Status</label>
                                                                                    <select name="irregularities[0][status]" class="form-control select2">
                                                                                        <option></option>
                                                                                        @foreach ($statusOptions as $option)
                                                                                            <option>{{ $option }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <button
                                                                                    class="btn btn-link text-muted p-0 mb-3"
                                                                                    style="height: 30px;">
                                                                                    <i class="ti-trash"
                                                                                        style="font-size: 18px;"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-2">
                                                                            <div class="col-md-4 pr-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Cause of
                                                                                        irregularity</label>
                                                                                    <textarea name="irregularities[0][cause_of_irregularity]"
                                                                                        class="form-control filter-input"
                                                                                        rows="3"
                                                                                        style="height: auto !important; min-height: 80px;"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Action
                                                                                        taken</label>
                                                                                    <textarea name="irregularities[0][action_taken]"
                                                                                        class="form-control filter-input"
                                                                                        rows="3"
                                                                                        style="height: auto !important; min-height: 80px;"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4 pl-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Customer
                                                                                        response</label>
                                                                                    <textarea name="irregularities[0][customer_response]"
                                                                                        class="form-control filter-input"
                                                                                        rows="3"
                                                                                        style="height: auto !important; min-height: 80px;"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-2">
                                                                            <div class="col-md-4 pr-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Hub/agent
                                                                                        comments</label>
                                                                                    <textarea name="irregularities[0][hub_agent_comments]"
                                                                                        class="form-control filter-input"
                                                                                        rows="3"
                                                                                        style="height: auto !important; min-height: 80px;"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4 px-1">
                                                                                <div class="form-group mb-2">
                                                                                    <label class="mb-0 text-muted"
                                                                                        style="font-size: 11px;">Handled
                                                                                        by</label>
                                                                                    <input type="text" name="irregularities[0][handled_by]"
                                                                                        class="form-control filter-input"
                                                                                        placeholder="">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="p-3 d-flex justify-content-end"
                                                                    style="border-top: 1px solid #f3f4f6;">
                                                                    <button type="button" id="add-irregularity-btn"
                                                                        class="btn btn-outline-teal px-3 py-1"
                                                                        style="font-size: 11px; height: 32px; border-radius: 4px;">Add
                                                                        irregularity</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

        <div class="edit-footer create-shipment-footer">
            <button type="submit" form="shipment-form" class="btn-save-custom">Save</button>
            <a href="{{ route('shipments') }}" class="btn-cancel-custom">Cancel</a>
        </div>

                                        <!-- Base Style - Compact end -->
    @include('layouts.partials.pcoded-shell-end')

    <!-- jquery slimscroll js -->

    <!-- modernizr js -->

    <!-- date-range-picker js -->

    <script>
        (function () {
            var sessionError = @json(session('error'));
            var validationErrors = @json($errors->all());
            var message = '';

            if (sessionError && String(sessionError).trim() !== '') {
                message = String(sessionError).trim();
            } else if (Array.isArray(validationErrors) && validationErrors.length > 0) {
                message = String(validationErrors[0] || '').trim();
            }

            if (!message) {
                return;
            }

            function showValidationAlert() {
                if (typeof swal === 'function') {
                    try {
                        swal('Validation error', message, 'error');
                        return;
                    } catch (e) {
                        try {
                            swal({
                                title: 'Validation error',
                                text: message,
                                type: 'error',
                                confirmButtonText: 'OK'
                            });
                            return;
                        } catch (ignored) {}
                    }
                }

                alert(message);
            }

            if (document.readyState === 'complete') {
                setTimeout(showValidationAlert, 0);
            } else {
                window.addEventListener('load', function () {
                    setTimeout(showValidationAlert, 0);
                });
            }
        })();
    </script>

    <script>
        window.shipmentDepartureParties = @json($departurePartiesByCrrId ?? []);
        window.preselectedCrrIds = @json($preselectedCrrIds ?? []);
        window.preselectedDepartureParty = @json($preselectedDepartureParty);
    </script>

    <script>
        $(document).ready(function () {
            $('body').addClass('create-shipment-page');

            var $footer = $('.create-shipment-footer');
            if ($footer.length) {
                $footer.appendTo('body');
            }

            function showStockModalError(message) {
                $('#stock-items-modal-error').text(message).show();
            }

            function clearStockModalError() {
                $('#stock-items-modal-error').hide().text('');
            }

            function showValidationSwal(message) {
                if (typeof swal === 'function') {
                    try {
                        swal('Validation error', message, 'error');
                        return;
                    } catch (e) {
                        try {
                            swal({
                                title: 'Validation error',
                                text: message,
                                type: 'error',
                                confirmButtonText: 'OK'
                            });
                            return;
                        } catch (ignored) {}
                    }
                }

                alert(message);
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

                // Modal table layout: [0]=checkbox, [1]=hub
                var hubText = normalizeHubKey($row.find('td').eq(1).text());
                return hubText && hubText !== '—' ? hubText : '';
            }

            function collectSelectedHubKeysForValidation() {
                var selectedHubKeys = [];

                // Hubs already added in selected stock table
                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    var existingHubKey = getHubKeyFromSelectedStockRow($(this));
                    if (existingHubKey && selectedHubKeys.indexOf(existingHubKey) === -1) {
                        selectedHubKeys.push(existingHubKey);
                    }
                });

                // Hubs currently checked in modal
                $('.modal-row-checkbox:checked').each(function () {
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

            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: false
            });

            // Flag display formatting for Country
            // Departure select2 (hubs / agents / customers)
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
                var $res = $(
                    '<div style="line-height:1.1;"><div style="font-weight:600;">' + item.text + '</div>' + (subtitle ? '<div style="font-size:11px;color:#6b7280;">' + subtitle + '</div>' : '') + '</div>'
                );
                return $res;
            }

            function formatPartySelection(item) {
                return item.text || item.id;
            }

            var hubDepartureCodes = @json($hubs->mapWithKeys(fn ($hub) => ['hub:' . $hub->id => $hub->code ?? '']));

            function setPortCodeSelect($select, code) {
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

            $('#departure-select').select2({
                placeholder: 'Type departure',
                allowClear: false,
                width: '100%',
                ajax: {
                    url: '/laravel/api/parties',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        console.log('API Response:', data);
                        return { results: data };
                    },
                    error: function(xhr, status, error) {
                        console.error('API Error:', error);
                    }
                },
                templateResult: formatParty,
                templateSelection: formatPartySelection,
                minimumInputLength: 0
            }).on('select2:select', function (e) {
                applyDeparturePortCode(e.params.data);
            }).on('select2:clear', function (e) {
                setPortCodeSelect($('#departure-port-code'), '');
            });

            // Consignee select2 (hubs / agents / offices / other_companies / suppliers / customers)
            $('#consignee-select').select2({
                placeholder: 'Type consignee',
                allowClear: false,
                width: '100%',
                ajax: {
                    url: '/laravel/api/consignees',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        console.log('Consignee API Response:', data);
                        return { results: data };
                    },
                    error: function(xhr, status, error) {
                        console.error('Consignee API Error:', error);
                    }
                },
                templateResult: formatParty,
                templateSelection: formatPartySelection,
                minimumInputLength: 0
            }).on('select2:select', function (e) {
                var data = e.params.data;
                $('#consignee-address').val(data.address || '');
                $('#consignee-city').val(data.city || '');
                $('#consignee-district').val(data.district || '');
                $('#consignee-zip').val(data.zip || '');
                $('#consignee-country').val(data.country || '').trigger('change');
                setPortCodeSelect($('#consignee-port-code'), data.port_code || '');
                $('#consignee-email').val(data.email || '');
                $('#consignee-att').val(data.contact_person || '');
                $('textarea[name="special_considerations_destination"]').val(data.special_considerations || '');
            }).on('select2:clear', function (e) {
                $('#consignee-address').val('');
                $('#consignee-city').val('');
                $('#consignee-district').val('');
                $('#consignee-zip').val('');
                $('#consignee-country').val('').trigger('change');
                setPortCodeSelect($('#consignee-port-code'), '');
                $('#location').val('');
                $('#consignee-email').val('');
                $('#consignee-att').val('');
                $('textarea[name="special_considerations_destination"]').val('');
            });

            // Account Manager select2 (all office user types)
            $('#account-manager-select').select2({
                placeholder: 'Type account manager',
                allowClear: false,
                width: '100%',
                ajax: {
                    url: '/laravel/api/account-managers',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    error: function(xhr, status, error) {
                        console.error('Account Manager API Error:', error);
                    }
                },
                templateResult: formatParty,
                templateSelection: formatPartySelection,
                minimumInputLength: 0
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
                    { val: 'Account manager', id: 'col-Account-manager' },
                    { val: 'Show ETL shipments', id: 'col-Show-ETL-shipments' },
                    { val: 'Shipment no', id: 'col-Shipment-no' },
                    { val: 'Customer', id: 'col-Customer' },
                    { val: 'Vessel', id: 'col-Vessel' },
                    { val: 'Port of destination', id: 'col-Port-of-destination' },
                    { val: 'Created by', id: 'col-Created-by' }
                ];

                allFilters.forEach(function (filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }

            // Initialize Datepickers
            $('.datepicker').each(function () {
                $(this).datepicker({
                    dateFormat: 'dd.mm.yy',
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: 'c-10:c+10'
                });
            });

            // Dynamic Irregularities logic
            var irregularityTypeOptions = @json($irregularityTypeOptions);
            var irregularityTypeOptionsHtml = '<option></option>' + irregularityTypeOptions.map(function(option) {
                return '<option>' + option + '</option>';
            }).join('');
            var partyResponsibleOptions = @json($partyResponsibleOptions);
            var partyResponsibleOptionsHtml = '<option></option>' + partyResponsibleOptions.map(function(option) {
                return '<option>' + option + '</option>';
            }).join('');
            var consequenceOptions = @json($consequenceOptions);
            var consequenceOptionsHtml = '<option></option>' + consequenceOptions.map(function(option) {
                return '<option>' + option + '</option>';
            }).join('');
            var statusOptions = @json($statusOptions);
            var statusOptionsHtml = '<option></option>' + statusOptions.map(function(option) {
                return '<option>' + option + '</option>';
            }).join('');

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

            $('#add-irregularity-btn').on('click', function () {
                const idx = nextIrregularityIndex();
                const newItem = `
                        <div class="irregularity-item border-bottom pb-4 mb-4" data-irregularity-index="${idx}">
                            <div class="row">
                                <div class="col-md-2 pr-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Date</label>
                                        <div class="input-group mb-0" style="height: 30px;">
                                            <input type="text" name="irregularities[${idx}][irregularity_date]" class="form-control filter-input datepicker" placeholder="DD.MM.YYYY">
                                            <span class="input-group-addon" style="background: transparent; border: 1px solid #ced4da; border-left: none; color: #008080; height: 30px; display: flex; align-items: center; padding: 0 8px;">
                                                <i class="ti-calendar" style="font-size: 12px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Irregularity</label>
                                        <select name="irregularities[${idx}][irregularity_type]" class="form-control select2">
                                            ${irregularityTypeOptionsHtml}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Party responsible</label>
                                        <select name="irregularities[${idx}][party_responsible]" class="form-control select2">
                                            ${partyResponsibleOptionsHtml}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Consequence</label>
                                        <select name="irregularities[${idx}][consequence]" class="form-control select2">
                                            ${consequenceOptionsHtml}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Extra cost for MT (USD)</label>
                                        <input type="text" name="irregularities[${idx}][extra_cost_mt_usd]" class="form-control filter-input" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-2 pl-1 d-flex align-items-end">
                                    <div class="form-group mb-2 flex-grow-1 mr-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Status</label>
                                        <select name="irregularities[${idx}][status]" class="form-control select2">
                                            ${statusOptionsHtml}
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-link text-muted p-0 mb-3 remove-irregularity" style="height: 30px;">
                                        <i class="ti-trash" style="font-size: 18px;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4 pr-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Cause of irregularity</label>
                                        <textarea name="irregularities[${idx}][cause_of_irregularity]" class="form-control filter-input" rows="3" style="height: auto !important; min-height: 80px;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Action taken</label>
                                        <textarea name="irregularities[${idx}][action_taken]" class="form-control filter-input" rows="3" style="height: auto !important; min-height: 80px;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Customer response</label>
                                        <textarea name="irregularities[${idx}][customer_response]" class="form-control filter-input" rows="3" style="height: auto !important; min-height: 80px;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4 pr-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Hub/agent comments</label>
                                        <textarea name="irregularities[${idx}][hub_agent_comments]" class="form-control filter-input" rows="3" style="height: auto !important; min-height: 80px;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 px-1">
                                    <div class="form-group mb-2">
                                        <label class="mb-0 text-muted" style="font-size: 11px;">Handled by</label>
                                        <input type="text" name="irregularities[${idx}][handled_by]" class="form-control filter-input" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>`;

                const $newItem = $(newItem);
                $('#irregularities-container').append($newItem);

                // Re-initialize Select2 for new item
                $newItem.find('.select2').select2({
                    placeholder: "",
                    allowClear: false,
                    dropdownAutoWidth: true,
                    width: '100%'
                });

                // Re-initialize Datepicker for new item
                $newItem.find('.datepicker').datepicker({
                    dateFormat: 'dd.mm.yy',
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: 'c-10:c+10'
                });
            });

            // Removal logic
            $(document).on('click', '.remove-irregularity, .ti-trash', function () {
                $(this).closest('.irregularity-item').fadeOut(300, function () {
                    $(this).remove();
                });
            });

            function initFlightDatepickers($container) {
                $container.find('.datepicker').each(function () {
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

            $(document).on('input', serviceTimeInputSelector, function () {
                var $input = $(this);
                var formatted = formatServiceTimeMask($input.val());
                if ($input.val() !== formatted) {
                    $input.val(formatted);
                }
            });

            $(document).on('blur', serviceTimeInputSelector, function () {
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

            $(document).on('keydown', serviceTimeInputSelector, function (e) {
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
                $('#airfreight-flights-container .airfreight-flight-row').each(function (index) {
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

            $('select[name="service"]').on('change', toggleServiceDetailsPanel);
            toggleServiceDetailsPanel();

            $('#add-airfreight-flight-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildAirfreightFlightRowHtml());
                $('#airfreight-flights-container').append($row);
                initFlightDatepickers($row);
                updateAirfreightFlightLabels();
            });

            $(document).on('click', '.remove-airfreight-flight', function () {
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
                $('#sea-freight-legs-container .sea-freight-leg-row').each(function (index) {
                    var label = index === 0 ? 'Bill of lading' : 'Port of departure';
                    $(this).find('.sea-leg-first-field label').text(label);
                });
            }

            $('#add-sea-freight-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildSeaFreightLegRowHtml());
                $('#sea-freight-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-sea-freight-leg', function () {
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

            $('#add-truck-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildTruckLegRowHtml());
                $('#truck-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-truck-leg', function () {
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

            $('#add-courier-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildCourierLegRowHtml());
                $('#courier-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-courier-leg', function () {
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

            $('#add-release-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildReleaseLegRowHtml());
                $('#release-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-release-leg', function () {
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

            $('#add-hand-carry-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildHandCarryLegRowHtml());
                $('#hand-carry-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-hand-carry-leg', function () {
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

            $('#add-on-board-leg-btn').on('click', function (e) {
                e.preventDefault();
                var $row = $(buildOnBoardLegRowHtml());
                $('#on-board-legs-container').append($row);
                initFlightDatepickers($row);
            });

            $(document).on('click', '.remove-on-board-leg', function () {
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

            // Calendar Icon Interaction
            $(document).on('click', '.input-group-addon, .ti-calendar', function () {
                var $input = $(this).closest('.input-group').find('.datepicker');
                if ($input.length > 0) {
                    $input.focus();
                    return;
                }
                $input = $(this).siblings('input.datepicker');
                if ($input.length > 0) {
                    $input.focus();
                }
            });

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            $('.clear-filters').on('click', function () {
                $('.select2').val(null).trigger('change');
                $('.filter-input:not(select)').val('').trigger('change');
                $('input[type="checkbox"]').prop('checked', false);
                table.columns().search('').draw();
            });

            function setAccountManagerFromStock(accountManagerId, accountManagerName) {
                var $select = $('#account-manager-select');

                if (!accountManagerId) {
                    $select.val(null).trigger('change');
                    return;
                }

                if ($select.find('option[value="' + accountManagerId + '"]').length === 0) {
                    $select.append(new Option(accountManagerName, accountManagerId, true, true));
                }

                $select.val(accountManagerId).trigger('change');
            }

            function setDepartureFromParty(party) {
                if (!party || !party.id) {
                    return;
                }

                var $select = $('#departure-select');
                var $existingOption = $select.find('option[value="' + party.id + '"]');

                if ($existingOption.length === 0) {
                    $select.append(new Option(party.text, party.id, true, true));
                } else {
                    $existingOption.text(party.text);
                }

                $select.val(party.id).trigger('change');
                $select.trigger({
                    type: 'select2:select',
                    params: {
                        data: party
                    }
                });
                applyDeparturePortCode(party);
            }

            function getDeparturePartyForCrrId(crrId) {
                if (window.shipmentDepartureParties && window.shipmentDepartureParties[crrId]) {
                    return window.shipmentDepartureParties[crrId];
                }

                if (window.shipmentDepartureParties && window.shipmentDepartureParties[String(crrId)]) {
                    return window.shipmentDepartureParties[String(crrId)];
                }

                var $modalRow = $('#stock-items-modal-table tbody tr[data-id="' + crrId + '"]');
                return parseDepartureParty($modalRow.attr('data-departure-party'));
            }

            function parseDepartureParty(partyJson) {
                if (!partyJson) {
                    return null;
                }

                try {
                    return JSON.parse(partyJson);
                } catch (error) {
                    return null;
                }
            }

            function updateDepartureFromSelectedStocks() {
                var party = null;

                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    var crrId = $(this).attr('data-crr-id');
                    party = getDeparturePartyForCrrId(crrId);
                    if (party && party.id) {
                        return false;
                    }
                });

                if (party && party.id) {
                    setDepartureFromParty(party);
                }
            }

            function updateAccountManagerFromSelectedStocks() {
                var accountManagerId = null;
                var accountManagerName = null;

                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    var rowAccountManagerId = $(this).attr('data-account-manager-id');
                    var rowAccountManagerName = $(this).attr('data-account-manager-name');

                    if (rowAccountManagerId) {
                        accountManagerId = rowAccountManagerId;
                        accountManagerName = rowAccountManagerName;
                        return false;
                    }
                });

                setAccountManagerFromStock(accountManagerId, accountManagerName);
            }

            function updateFieldsFromSelectedStocks() {
                updateAccountManagerFromSelectedStocks();
                updateDepartureFromSelectedStocks();
            }

            // Stock Items Modal
            $('#add-stock-items-btn').on('click', function() {
                $('#stock-items-modal').modal('show');
            });

            refreshStockTotals();

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
            }

            $('#stock-items-modal').on('shown.bs.modal', function() {
                clearStockModalError();
                $('#modal-add-selected').prop('disabled', false);
                $('.modal-select2').select2({
                    placeholder: "Click here",
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $('#stock-items-modal')
                });

                $('.modal-select2').off('change').on('change', applyStockModalFilters);
                $('.modal-filter-input').off('input change').on('input change', applyStockModalFilters);
                applyStockModalFilters();
                updateRealtimeHubValidation();
            });

            $('#stock-items-modal').on('hidden.bs.modal', function() {
                clearStockModalError();
                $('#modal-add-selected').prop('disabled', false);
            });

            // Clear filters for modal
            $(document).on('click', '.modal-clear-filters', function() {
                $('.modal-select2').val(null).trigger('change');
                $('.modal-filter-input').val('').trigger('change');
                $('#modal-select-all').prop('checked', false);
                $('.modal-row-checkbox').prop('checked', false);
                applyStockModalFilters();
            });
            
            // Select all checkboxes in modal
            $('#modal-select-all').on('change', function() {
                $('#stock-items-modal-table tbody tr:visible .modal-row-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
                updateRealtimeHubValidation();
            });

            $(document).on('change', '.modal-row-checkbox', function() {
                updateRealtimeHubValidation();
            });
            
            function parseStockTotalNumber(value) {
                var parsed = parseFloat(String(value || '').replace(/[^\d.-]/g, ''));
                return isNaN(parsed) ? 0 : parsed;
            }

            function refreshStockTotals() {
                var totalPcs = 0;
                var totalWeight = 0;
                var totalCbm = 0;
                var totalValue = 0;

                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    totalPcs += parseStockTotalNumber($(this).attr('data-pcs'));
                    totalWeight += parseStockTotalNumber($(this).attr('data-weight'));
                    totalCbm += parseStockTotalNumber($(this).attr('data-cbm'));
                    totalValue += parseStockTotalNumber($(this).attr('data-value'));
                });

                $('#stock-total-packages').text(totalPcs + ' pcs');
                $('#stock-total-weight').text(totalWeight.toFixed(2) + ' kg');
                $('#stock-total-cbm').text(totalCbm.toFixed(2));
                $('#stock-total-value').text(totalValue.toFixed(2));
            }

            function refreshStockItemsTable() {
                var count = $('#stock-items-table tbody tr.selected-stock-row').length;
                var $emptyRow = $('#stock-items-table tbody tr#empty-row');

                if (count === 0) {
                    if ($emptyRow.length === 0) {
                        $('#stock-items-table tbody').append('<tr id="empty-row"><td colspan="11" class="text-center py-4 text-muted">No stock items added yet.</td></tr>');
                    }
                } else {
                    $emptyRow.remove();
                }

                $('.nav-link[href="#stock-items"]').text('Stock items (' + count + ')');
                refreshStockTotals();
            }

            function appendStockRowFromModal(id) {
                if ($('#stock-items-table tbody tr.selected-stock-row[data-crr-id="' + id + '"]').length) {
                    return;
                }

                var $modalRow = $('#stock-items-modal-table tbody tr[data-id="' + id + '"]');
                if ($modalRow.length === 0) {
                    return;
                }

                var hubCode = String($modalRow.attr('data-hub') || '').trim();
                var hubAgent = String($modalRow.attr('data-hub-agent') || '').trim();
                var hub = hubCode || hubAgent || '—';
                var hubKey = (hubCode || hubAgent).toLowerCase();
                var vessel = $modalRow.data('vessel') || '—';
                var po = $modalRow.data('po') || '—';
                var supplier = $modalRow.data('supplier') || '—';
                var stock = $modalRow.data('stock') || '—';
                var items = $modalRow.data('items') || '—';
                var weight = $modalRow.data('weight') || '—';
                var cbm = $modalRow.data('cbm') || '—';
                var value = $modalRow.data('value') || '—';
                var pcsNum = parseStockTotalNumber(items);
                var weightNum = parseStockTotalNumber(weight);
                var cbmNum = parseStockTotalNumber(cbm);
                var valueNum = parseStockTotalNumber(value);
                var accountManagerId = $modalRow.attr('data-account-manager-id') || '';
                var accountManagerName = $modalRow.attr('data-account-manager-name') || '';
                var status = 'In Progress';
                var statusClass = 'stock-status-badge ' + stockStatusBadgeClass(status);

                var rowHtml = '<tr class="selected-stock-row" data-crr-id="' + id + '"'
                    + ' data-hub-key="' + hubKey + '"'
                    + ' data-pcs="' + pcsNum + '"'
                    + ' data-weight="' + weightNum + '"'
                    + ' data-cbm="' + cbmNum + '"'
                    + ' data-value="' + valueNum + '"'
                    + ' data-account-manager-id="' + accountManagerId + '"'
                    + ' data-account-manager-name="' + accountManagerName + '">' +
                    '<td>' + hub + '</td>' +
                    '<td>' + vessel + '</td>' +
                    '<td style="max-width: 150px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: block;">' + po + '</td>' +
                    '<td>' + supplier + '</td>' +
                    '<td>' + stock + '</td>' +
                    '<td>' + items + '</td>' +
                    '<td>' + weight + '</td>' +
                    '<td>' + cbm + '</td>' +
                    '<td>' + value + '</td>' +
                    '<td><span class="' + statusClass + '">' + status + '</span></td>' +
                    '<td><button type="button" class="btn btn-link btn-sm p-0 remove-stock-item" data-crr-id="' + id + '" title="Remove"><i class="icofont icofont-ui-delete text-danger"></i></button></td>' +
                    '</tr>';

                $('#stock-items-table tbody').append(rowHtml);
            }

            // Add selected stock items
            $('#modal-add-selected').on('click', function() {
                clearStockModalError();
                var selectedIds = [];
                $('.modal-row-checkbox:checked:not(:disabled)').each(function() {
                    var $row = $(this).closest('tr');
                    var status = String($row.attr('data-status') || '').trim().toLowerCase();
                    if (status === 'in progress') {
                        return;
                    }
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    showStockModalError('Please select at least one stock item.');
                    return;
                }

                var selectedHubKeys = [];
                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    var existingHubKey = getHubKeyFromSelectedStockRow($(this));
                    if (existingHubKey && selectedHubKeys.indexOf(existingHubKey) === -1) {
                        selectedHubKeys.push(existingHubKey);
                    }
                });

                selectedIds.forEach(function (id) {
                    var $modalRow = $('#stock-items-modal-table tbody tr[data-id="' + id + '"]');
                    if (!$modalRow.length) {
                        return;
                    }

                    var candidateHubKey = getHubKeyFromModalRow($modalRow);
                    if (candidateHubKey && selectedHubKeys.indexOf(candidateHubKey) === -1) {
                        selectedHubKeys.push(candidateHubKey);
                    }
                });

                if (selectedHubKeys.length > 1) {
                    showStockModalError('All selected stock items must belong to the same hub.');
                    return;
                }

                selectedIds.forEach(function(id) {
                    appendStockRowFromModal(id);
                });

                refreshStockItemsTable();
                updateFieldsFromSelectedStocks();
                $('.modal-row-checkbox').prop('checked', false);
                $('#modal-select-all').prop('checked', false);
                $('#stock-items-modal').modal('hide');
            });

            $(document).on('click', '.remove-stock-item', function() {
                $(this).closest('tr.selected-stock-row').remove();
                refreshStockItemsTable();
                updateFieldsFromSelectedStocks();
            });

            $('#shipment-form').on('submit', function() {
                var contactPerson = $.trim($('#consignee-att').val() || '');
                if (!contactPerson) {
                    showValidationSwal('Please enter the contact person.');
                    $('#consignee-att').focus();
                    return false;
                }

                var selectedHubKeys = [];
                $('#stock-items-table tbody tr.selected-stock-row').each(function () {
                    var hubKey = getHubKeyFromSelectedStockRow($(this));
                    if (hubKey && selectedHubKeys.indexOf(hubKey) === -1) {
                        selectedHubKeys.push(hubKey);
                    }
                });

                if (selectedHubKeys.length > 1) {
                    showValidationSwal('All selected stock items must belong to the same hub.');
                    return false;
                }

                $('#crr-ids-container').empty();
                $('#stock-items-table tbody tr.selected-stock-row').each(function() {
                    var crrId = $(this).data('crr-id');
                    if (crrId) {
                        $('#crr-ids-container').append(
                            '<input type="hidden" name="crr_ids[]" value="' + crrId + '">'
                        );
                    }
                });
            });

            window.shipmentCreatePage = {
                initializePreselectedStocks: function () {
                    var preselectedCrrIds = window.preselectedCrrIds || [];

                    if (!preselectedCrrIds.length) {
                        return;
                    }

                    preselectedCrrIds.forEach(function (id) {
                        appendStockRowFromModal(String(id));
                    });

                    refreshStockItemsTable();
                    updateFieldsFromSelectedStocks();

                    if (window.preselectedDepartureParty && window.preselectedDepartureParty.id) {
                        setDepartureFromParty(window.preselectedDepartureParty);
                    }
                }
            };
        });
    </script>

    <!-- Stock Items Modal -->
    <div class="modal fade" id="stock-items-modal" tabindex="-1" role="dialog" aria-labelledby="stockItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title mb-1" id="stockItemsModalLabel" style="font-size: 14px; font-weight: 600;">Select Stock Items</h5>
                        <div id="stock-items-modal-error" style="display:none; font-size: 11px; color: #dc2626; font-weight: 600;"></div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 10px;">
                    <!-- Filter Section -->
                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px; margin-bottom: 10px;">
                        <div class="filter-group" style="width: 210px; min-width: 180px;">
                            <span class="filter-label">Customer</span>
                            <select id="modal-customer-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @php $modalCustomers = []; @endphp
                                @foreach($crrs as $crr)
                                    @php
                                        $customerName = $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '';
                                    @endphp
                                    @if($customerName && !in_array($customerName, $modalCustomers, true))
                                        @php $modalCustomers[] = $customerName; @endphp
                                        <option value="{{ $customerName }}">{{ $customerName }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Vessel</span>
                            <select id="modal-vessel-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @php $modalVessels = []; @endphp
                                @foreach($crrs as $crr)
                                    @php $vesselName = $crr->vessel_name ?? ''; @endphp
                                    @if($vesselName && !in_array($vesselName, $modalVessels, true))
                                        @php $modalVessels[] = $vesselName; @endphp
                                        <option value="{{ $vesselName }}">{{ $vesselName }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Hub/Agent</span>
                            <select id="modal-hub-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                <optgroup label="Hubs">
                                    @foreach($hubs as $hub)
                                        <option value="{{ $hub->code }}">{{ $hub->code }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Agents">
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->code }}">{{ $agent->code }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="filter-group" style="width: 150px; min-width: 140px;">
                            <span class="filter-label">Status</span>
                            <select id="modal-status-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                    @if(!in_array($value, [\App\Models\Crr::STATUS_COMPLETED, \App\Models\Crr::STATUS_CANCELLED], true))
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 140px; min-width: 120px;">
                            <span class="filter-label">Landed goods</span>
                            <select id="modal-landed-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                <option value="">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Stock no.</span>
                            <input id="modal-stock-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">PO no.</span>
                            <input id="modal-po-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <div class="filter-group" style="width: 200px; min-width: 180px;">
                            <span class="filter-label">Supplier</span>
                            <input id="modal-supplier-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <a class="clear-filters modal-clear-filters" style="height: 32px; display: inline-flex; align-items: center; margin-left: auto;"><i class="ti-close"></i> Clear filters</a>
                    </div>

                    <!-- Table Section -->
                    <div class="table-scroll-wrapper" style="max-height: 400px;">
                        <table id="stock-items-modal-table" class="office-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="modal-select-all"></th>
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
                                @forelse($crrs as $crr)
                                @php
                                    $status = $crr->status ?? 'Pending';
                                    $badgeClass = ($status === 'Stock') ? 'label label-stock' : 'label label-pending';
                                    $poNumbers = is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '');
                                    $totalItems = $crr->packages->count();
                                    $totalWeight = $crr->packages->sum('weight');
                                    $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                    $hasDocs = $crr->documents->isNotEmpty();
                                    $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                    $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                    $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities);
                                    $mainAccountManager = $crr->customerVessel?->customer?->responsible?->accountManager;

                                    $departureParty = null;
                                    $departureCandidates = array_values(array_unique(array_filter([
                                        trim((string) ($crr->hub_code ?? '')),
                                        trim((string) ($crr->hub_agent ?? '')),
                                    ])));

                                    foreach ($departureCandidates as $departureCandidate) {
                                        $matchedHub = $hubs->first(function ($hub) use ($departureCandidate) {
                                            return strcasecmp((string) ($hub->code ?? ''), $departureCandidate) === 0
                                                || strcasecmp((string) ($hub->hub_name ?? ''), $departureCandidate) === 0;
                                        });

                                        if ($matchedHub) {
                                            $departureParty = [
                                                'id' => 'hub:' . $matchedHub->id,
                                                'text' => $matchedHub->hub_name,
                                                'type' => 'hub',
                                                'hub_code' => $matchedHub->code,
                                                'port_code' => $matchedHub->port_code,
                                            ];
                                            break;
                                        }

                                        $matchedAgent = $agents->first(function ($agent) use ($departureCandidate) {
                                            return strcasecmp((string) ($agent->code ?? ''), $departureCandidate) === 0
                                                || strcasecmp((string) ($agent->agent_name ?? ''), $departureCandidate) === 0;
                                        });

                                        if ($matchedAgent) {
                                            $departureParty = [
                                                'id' => 'agent:' . $matchedAgent->id,
                                                'text' => $matchedAgent->agent_name,
                                                'type' => 'agent',
                                                'port_code' => $matchedAgent->port_code,
                                            ];
                                            break;
                                        }
                                    }
                                @endphp
                                <tr data-id="{{ $crr->id }}"
                                    data-hub="{{ trim((string) ($crr->hub_code ?? '')) }}"
                                    data-hub-agent="{{ trim((string) ($crr->hub_agent ?? '')) }}"
                                    data-customer="{{ $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '' }}"
                                    data-vessel="{{ $crr->vessel_name ?? '' }}"
                                    data-account-manager-id="{{ $mainAccountManager?->id ?? '' }}"
                                    data-account-manager-name="{{ $mainAccountManager?->name ?? '' }}"
                                    data-departure-party="{{ $departureParty ? e(json_encode($departureParty)) : '' }}"
                                    data-status="{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}"
                                    data-landed="{{ $crr->is_landed_goods ? '1' : '0' }}"
                                    data-stock="{{ $crr->stock_number ?? '' }}"
                                    data-po="{{ is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '') }}"
                                    data-supplier="{{ $crr->supplier ?? '' }}"
                                    data-items="{{ $totalItems }}"
                                    data-weight="{{ $totalWeight > 0 ? number_format($totalWeight, 2, '.', '') : '' }}"
                                    data-cbm="{{ $crr->cbm ?? '' }}"
                                    data-value="{{ $crr->customs_value ? number_format($crr->customs_value, 2, '.', '') : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                            class="modal-row-checkbox"
                                            value="{{ $crr->id }}"
                                            @if((int) $crr->status === \App\Models\Crr::STATUS_IN_PROGRESS) disabled title="In Progress stock cannot be added to a shipment." @endif>
                                    </td>
                                    <td>{{ $crr->hub_code ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span style="color: #008080; font-weight: 500;">{{ $crr->stock_number }}</span>
                                            <div class="d-flex align-items-center" style="gap: 8px;">
                                                @if($crr->is_landed_goods)
                                                    <span class="landed-badge" title="Landed Goods">Landed</span>
                                                @endif
                                                @if($hasDgr)
                                                    <i class="icofont icofont-warning text-danger" title="Dangerous Goods" style="font-size: 15px;"></i>
                                                @endif
                                                @if($hasDocs)
                                                    <i class="icofont icofont-file-alt text-muted" title="Documents Attached" style="font-size: 15px; color: #64748b !important;"></i>
                                                @endif
                                                @if($hasMedicine)
                                                    <i class="icofont icofont-first-aid text-success" title="Medicine" style="font-size: 15px;"></i>
                                                @endif
                                                @if($hasDeliveryIrreg) 
                                                    <i class="icofont icofont-info-circle text-pending" title="Delivery irregularities - missing info" style="font-size: 15px;"></i>
                                                @endif
                                                @if($isNotStackable)
                                                    <i class="icofont icofont-info-square text-warning" title="Non-Stackable Content" style="font-size: 15px;"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '—' }}</td>
                                    <td>{{ $crr->vessel_name ?? '—' }}</td>
                                    <td>{{ $crr->expected_delivery_date ?? '—' }}</td>
                                    <td style="max-width: 150px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: block;">{{ $poNumbers ?: '—' }}</td>
                                    <td>{{ $crr->supplier ?? '—' }}</td>
                                    <td class="text-center">{{ $totalItems }}</td>
                                    <td class="text-right">{{ $totalWeight > 0 ? number_format($totalWeight, 2) : '—' }}</td>
                                    <td class="text-right">{{ $crr->customs_value ? number_format($crr->customs_value, 2) : '—' }}</td>
                                    <td>{{ $crr->currency ?? '—' }}</td>
                                    <td>{{ $crr->transit_id ?? '—' }}</td>
                                    <td>{{ $crr->internal_shipment ?? '—' }}</td>
                                    <td>
                                        @php
                                            $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                        @endphp
                                        <span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="15" class="text-center py-4 text-muted" style="font-size: 12px;">
                                        No stock entries found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 12px;">Cancel</button>
                    <button type="button" class="btn btn-teal" id="modal-add-selected" style="font-size: 12px;">Add Selected</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            if (window.shipmentCreatePage && typeof window.shipmentCreatePage.initializePreselectedStocks === 'function') {
                window.shipmentCreatePage.initializePreselectedStocks();
            }
        });
    </script>
@endsection