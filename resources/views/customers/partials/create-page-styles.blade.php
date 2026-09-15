    <style>
        body.create-customer-page {
            padding-bottom: 84px;
        }

        .page-body:has(.create-customer-page) {
            padding: 0 !important;
            margin: 0 !important;
        }

        .create-customer-page {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 12px 0 28px;
            background:
                radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
                radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
                #f5f7fb;
            box-sizing: border-box;
        }

        .create-customer-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 0 0 14px;
            padding: 4px 16px 0;
        }

        .create-customer-hero-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .create-customer-hero-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0, 128, 128, 0.28);
        }

        .create-customer-kicker {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0088c7;
        }

        .create-customer-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .create-customer-sub {
            margin: 6px 0 0;
            font-size: 13px;
            color: #64748b;
            max-width: 36rem;
        }

        .create-customer-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #d6e3ee;
            background: #fff;
            color: #0088c7;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(14, 29, 74, 0.04);
            transition: border-color 0.15s ease, background 0.15s ease, transform 0.15s ease;
        }

        .create-customer-back:hover {
            border-color: #00aeef;
            background: #e8f6fc;
            color: #0088c7;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .create-customer-card {
            position: relative;
            width: 100%;
            max-width: none;
            margin: 0;
            background: #fff;
            border: none;
            border-top: 1px solid rgba(214, 227, 238, 0.95);
            border-bottom: 1px solid rgba(214, 227, 238, 0.95);
            border-radius: 0;
            box-shadow: 0 8px 24px rgba(14, 29, 74, 0.04);
            overflow: visible;
        }

        .create-customer-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
            pointer-events: none;
        }

        .cust-form-container {
            width: 100%;
            box-sizing: border-box;
            padding: 0;
            background: transparent;
        }

        .cust-pillars.cust-details-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            padding: 22px 18px 28px;
            align-items: stretch;
        }

        .cust-pillar-card {
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #cfe0ec;
            border-radius: 14px;
            padding: 18px 18px 16px;
            box-shadow:
                0 1px 2px rgba(14, 29, 74, 0.04),
                0 12px 28px rgba(14, 29, 74, 0.06);
            overflow: visible;
            position: relative;
            z-index: 1;
            min-width: 0;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .cust-pillar-card:hover {
            border-color: #94c9e3;
            box-shadow:
                0 2px 4px rgba(14, 29, 74, 0.05),
                0 16px 32px rgba(0, 136, 199, 0.08);
        }

        .cust-pillar-head {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 4px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e8eef4;
        }

        .cust-pillar-head-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(0, 128, 128, 0.22);
        }

        .cust-pillar-head-icon.is-location {
            background: linear-gradient(135deg, #0e1d4a 0%, #0088c7 100%);
            box-shadow: 0 6px 14px rgba(14, 29, 74, 0.2);
        }

        .cust-pillar-head-icon.is-invoice {
            background: linear-gradient(135deg, #e87722 0%, #00aeef 100%);
            box-shadow: 0 6px 14px rgba(232, 119, 34, 0.22);
        }

        .cust-pillar-head-icon.is-team {
            background: linear-gradient(135deg, #008080 0%, #0e1d4a 100%);
            box-shadow: 0 6px 14px rgba(0, 128, 128, 0.22);
        }

        .cust-pillar-head-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .cust-pillar-head-sub {
            margin: 4px 0 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.35;
        }

        .cust-soft-panel {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 4px;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(180deg, #f8fbfd 0%, #f1f7fb 100%);
            border: 1px solid #dce8f1;
        }

        .cust-soft-panel-title {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #0e1d4a;
        }

        .cust-soft-panel-title span {
            margin-left: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0;
            text-transform: none;
            color: #94a3b8;
        }

        .form-group-custom {
            margin-bottom: 0;
            position: relative;
            overflow: visible;
        }

        .form-label-custom {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .input-row.cust-details-input-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .input-row.cust-details-input-row > .form-group-custom {
            flex: 1;
            min-width: 0;
        }

        #customerForm .form-control-custom,
        #customerForm .form-input-custom,
        #customerForm .form-select-custom,
        #customerForm .form-textarea-custom {
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            color: #0e1d4a;
            font-size: 13px;
        }

        #customerForm .form-control-custom,
        #customerForm .form-input-custom,
        #customerForm .form-select-custom {
            height: var(--mc-control-height, 34px);
            padding: 0 10px;
        }

        #customerForm .form-textarea-custom {
            padding: 8px 10px;
            min-height: 72px;
            height: auto;
            overflow-y: hidden;
            resize: none;
            line-height: 1.4;
            box-sizing: border-box;
        }

        #customerForm .form-control-custom:focus,
        #customerForm .form-input-custom:focus,
        #customerForm .form-select-custom:focus,
        #customerForm .form-textarea-custom:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #customerForm .form-control-custom.error,
        #customerForm .form-textarea-custom.error {
            border-color: #dc2626 !important;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 4px;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #008080;
        }

        .checkbox-label {
            font-size: 13px;
            color: #475569;
            line-height: 1.35;
        }

        .cust-form-alert {
            margin: 16px 18px 0;
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 10px;
        }

        .error-message {
            color: #dc2626;
            font-size: 11px;
            margin-top: 4px;
            font-weight: 500;
        }

        .select2-container--default.error .select2-selection--single {
            border-color: #dc2626 !important;
        }

        body.create-customer-page #customerForm .select2-container--default .select2-selection--single,
        body.create-customer-page #customerForm .select2-container--default .select2-selection--multiple {
            border: 1px solid #d6e3ee !important;
            border-radius: 8px !important;
            background: #fff !important;
        }

        body.create-customer-page #customerForm .select2-container--default.select2-container--focus .select2-selection--single,
        body.create-customer-page #customerForm .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #0088c7 !important;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        body.create-customer-page .create-customer-footer {
            position: fixed !important;
            left: var(--spacing-sidebar, 13.25rem) !important;
            right: 0 !important;
            bottom: 0 !important;
            margin: 0 !important;
            width: calc(100vw - var(--spacing-sidebar, 13.25rem)) !important;
            max-width: none !important;
            padding: 12px 28px !important;
            box-sizing: border-box !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(8px);
            display: flex !important;
            align-items: center !important;
            gap: 16px;
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            z-index: 1040 !important;
            box-shadow: 0 -8px 24px rgba(14, 29, 74, 0.06);
        }

        body.create-customer-page .create-customer-footer .btn-save-custom {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
            color: #fff !important;
            border: none !important;
            padding: 10px 28px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        body.create-customer-page .create-customer-footer .btn-save-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.32);
        }

        body.create-customer-page .create-customer-footer .btn-cancel-custom {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        body.create-customer-page .create-customer-footer .btn-cancel-custom:hover {
            color: #008080 !important;
            text-decoration: none !important;
        }

        body.create-customer-page .cust-form-container,
        body.create-customer-page .create-customer-card,
        body.create-customer-page .cust-pillars {
            overflow: visible !important;
        }

        @media (max-width: 1399.98px) {
            .cust-pillars.cust-details-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .cust-pillars.cust-details-grid {
                grid-template-columns: 1fr !important;
                padding: 16px 12px 20px !important;
            }

            .input-row.cust-details-input-row {
                flex-direction: column;
            }

            .cust-pillar-card:hover {
                box-shadow:
                    0 1px 2px rgba(14, 29, 74, 0.04),
                    0 12px 28px rgba(14, 29, 74, 0.06);
            }

            body.create-customer-page .create-customer-footer {
                left: 0 !important;
                width: 100vw !important;
                padding: 12px 16px !important;
                flex-wrap: wrap;
            }

            body.create-customer-page .create-customer-footer .btn-save-custom {
                flex: 1 1 auto;
            }

            .create-customer-hero {
                padding: 4px 12px 0;
            }
        }
    </style>
