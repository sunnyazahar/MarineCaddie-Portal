    <style>
        body.create-supplier-page {
            padding-bottom: 84px;
        }

        .page-body:has(.create-supplier-page) {
            padding: 0 !important;
            margin: 0 !important;
        }

        .create-supplier-page {
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

        .create-supplier-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 0 0 14px;
            padding: 4px 16px 0;
        }

        .create-supplier-hero-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .create-supplier-hero-icon {
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

        .create-supplier-kicker {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #0088c7;
        }

        .create-supplier-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0e1d4a;
            line-height: 1.2;
        }

        .create-supplier-sub {
            margin: 6px 0 0;
            font-size: 13px;
            color: #64748b;
            max-width: 36rem;
        }

        .create-supplier-back {
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

        .create-supplier-back:hover {
            border-color: #00aeef;
            background: #e8f6fc;
            color: #0088c7;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .create-supplier-card {
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

        .create-supplier-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
            pointer-events: none;
        }

        .sup-form-container {
            width: 100%;
            box-sizing: border-box;
            padding: 20px 16px 24px !important;
            background: transparent;
        }

        .sup-pillars {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            align-items: stretch;
        }

        .sup-pillar-col {
            display: flex;
            min-width: 0;
        }

        .sup-pillar {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
            border: 1px solid #d6e3ee;
            border-radius: 14px;
            padding: 14px 14px 12px;
            box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .sup-pillar__title {
            margin: 0 0 4px;
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

        .form-group-custom {
            margin-bottom: 0;
        }

        .form-label-custom {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .address-sub-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 10px;
        }

        #supplierForm .sup-pillar .form-control-custom,
        #supplierForm .sup-pillar .form-textarea-custom,
        #supplierForm .sup-pillar .select-custom {
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: #fff;
            color: #0e1d4a;
        }

        #supplierForm .sup-pillar .form-control-custom,
        #supplierForm .sup-pillar .select-custom {
            height: var(--mc-control-height, 34px);
            padding: 0 10px;
        }

        #supplierForm .sup-pillar .form-control-custom:focus,
        #supplierForm .sup-pillar .form-textarea-custom:focus,
        #supplierForm .sup-pillar .select-custom:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        #supplierForm .sup-pillar .form-textarea-custom {
            padding: 8px 10px;
            min-height: 72px;
            resize: vertical;
            line-height: 1.4;
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

        .sup-form-alert {
            margin-bottom: 16px;
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

        #supplierForm .form-control-custom.error,
        #supplierForm .form-textarea-custom.error {
            border-color: #dc2626 !important;
        }

        .select2-container--default.error .select2-selection--single {
            border-color: #dc2626 !important;
        }

        body.create-supplier-page #supplierForm .select2-container--default .select2-selection--single,
        body.create-supplier-page #supplierForm .select2-container--default .select2-selection--multiple {
            border: 1px solid #d6e3ee !important;
            border-radius: 8px !important;
            background: #fff !important;
        }

        body.create-supplier-page #supplierForm .select2-container--default.select2-container--focus .select2-selection--single,
        body.create-supplier-page #supplierForm .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #0088c7 !important;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
        }

        body.create-supplier-page .create-supplier-footer {
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

        body.create-supplier-page .create-supplier-footer .btn-save-custom {
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

        body.create-supplier-page .create-supplier-footer .btn-save-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.32);
        }

        body.create-supplier-page .create-supplier-footer .btn-cancel-custom {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        body.create-supplier-page .create-supplier-footer .btn-cancel-custom:hover {
            color: #008080 !important;
            text-decoration: none !important;
        }

        body.create-supplier-page .sup-pillar-col,
        body.create-supplier-page .sup-form-container,
        body.create-supplier-page .create-supplier-card,
        body.create-supplier-page .sup-pillars {
            overflow: visible !important;
        }

        @media (max-width: 1199.98px) {
            .sup-pillars {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .sup-pillars {
                grid-template-columns: 1fr;
            }

            .address-sub-grid {
                grid-template-columns: 1fr !important;
            }

            body.create-supplier-page .create-supplier-footer {
                left: 0 !important;
                width: 100vw !important;
                padding: 12px 16px !important;
                flex-wrap: wrap;
            }

            body.create-supplier-page .create-supplier-footer .btn-save-custom {
                flex: 1 1 auto;
            }

            .create-supplier-hero {
                padding: 4px 12px 0;
            }

            .sup-form-container {
                padding: 16px 12px 20px !important;
            }
        }
    </style>
