<style>
    body.office-user-page {
        padding-bottom: 84px;
    }

    .page-body:has(.office-user-page) {
        padding: 0 !important;
        margin: 0 !important;
    }

    .office-user-page {
        width: 100%;
        margin: 0;
        padding: 12px 0 28px;
        background:
            radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
            radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
            #f5f7fb;
        box-sizing: border-box;
    }

    .office-user-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 0 0 14px;
        padding: 4px 16px 0;
    }

    .office-user-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .office-user-hero-icon {
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

    .office-user-kicker {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .office-user-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0e1d4a;
        line-height: 1.2;
    }

    .office-user-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
        max-width: 40rem;
    }

    .office-user-back {
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

    .office-user-back:hover {
        border-color: #00aeef;
        background: #e8f6fc;
        color: #0088c7;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .office-user-card {
        position: relative;
        max-width: 760px;
        margin: 0 16px;
        background: #fff;
        border: 1px solid #d6e3ee;
        box-shadow: 0 8px 24px rgba(14, 29, 74, 0.06);
        overflow: visible;
    }

    .office-user-card.is-tabbed {
        max-width: min(1100px, calc(100% - 32px));
    }

    .office-user-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
        pointer-events: none;
    }

    .office-user-tabs {
        display: flex;
        flex-wrap: nowrap;
        gap: 4px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 12px 14px 0;
        border-bottom: 1px solid #e8eef4;
    }

    .office-user-tab {
        flex: 0 0 auto;
        padding: 8px 14px;
        border: 1px solid transparent;
        border-radius: 8px 8px 0 0;
        background: transparent;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        white-space: nowrap;
        cursor: pointer;
        transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease;
    }

    .office-user-tab:hover {
        color: #0e1d4a;
        background: #f8fafc;
    }

    .office-user-tab.active {
        color: #0e1d4a;
        background: linear-gradient(180deg, #f0fafb 0%, #ffffff 100%);
        border-color: #d6e3ee;
        border-bottom-color: #fff;
        box-shadow: inset 0 -2px 0 #008080;
    }

    .office-user-form-container {
        padding: 20px 18px 24px;
    }

    .office-user-tab-pane {
        display: none;
    }

    .office-user-tab-pane.active {
        display: block;
    }

    .office-user-pillar {
        background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
        border: 1px solid #d6e3ee;
        border-radius: 14px;
        padding: 14px 14px 12px;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
    }

    .office-user-pillar__title {
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

    .office-user-fields {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .form-group-custom {
        margin-bottom: 0;
    }

    .office-user-page .office-user-form .form-control-custom {
        width: 100%;
        height: var(--mc-control-height, 34px);
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        color: #0e1d4a;
    }

    .office-user-page .office-user-form .form-control-custom:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    .office-user-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 4px;
        padding-top: 4px;
    }

    .office-user-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        flex-shrink: 0;
        accent-color: #008080;
    }

    .office-user-checkbox label {
        margin: 0;
        font-size: 13px;
        color: #475569;
        line-height: 1.35;
        cursor: pointer;
    }

    .office-user-form-alert {
        margin: 0 16px 12px;
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }

    .office-user-vessels-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 0 2px;
    }

    .office-user-vessels-toolbar label {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .office-user-vessels-search {
        height: var(--mc-control-height, 34px);
        min-width: 180px;
        max-width: 260px;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        color: #0e1d4a;
    }

    .office-user-vessels-search:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    .office-user-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
    }

    .office-user-vessels-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .office-user-vessels-table th {
        text-align: left;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #0e1d4a;
        border-bottom: 2px solid #008080;
        background: linear-gradient(180deg, #f0fafb 0%, #f8fafc 100%);
        white-space: nowrap;
    }

    .office-user-vessels-table td {
        padding: 9px 14px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .office-user-vessels-table tbody tr:hover td {
        background: #f5fbfe;
    }

    .office-user-vessels-table .vessel-checkbox {
        width: 16px;
        height: 16px;
        accent-color: #008080;
    }

    .office-user-empty-pane {
        padding: 32px 16px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    body.office-user-page .office-user-footer {
        position: fixed !important;
        left: var(--spacing-sidebar, 13.25rem) !important;
        right: 0 !important;
        bottom: 0 !important;
        width: calc(100vw - var(--spacing-sidebar, 13.25rem)) !important;
        margin: 0 !important;
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

    body.office-user-page .office-user-footer .btn-save-custom {
        background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 10px 28px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28);
    }

    body.office-user-page .office-user-footer .btn-cancel-custom {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    body.office-user-page .office-user-footer .btn-cancel-custom:hover {
        color: #008080 !important;
    }

    body.office-user-page .office-user-footer .audit-info {
        margin-left: auto;
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
    }

    body.office-user-page .office-user-footer .audit-info b {
        color: #64748b;
    }

    @media (max-width: 991.98px) {
        .office-user-card,
        .office-user-card.is-tabbed {
            max-width: none;
            margin: 0 12px;
        }

        .office-user-hero {
            padding: 4px 12px 0;
        }

        body.office-user-page .office-user-footer {
            left: 0 !important;
            width: 100vw !important;
            padding: 12px 16px !important;
            flex-wrap: wrap;
        }

        body.office-user-page .office-user-footer .audit-info {
            width: 100%;
            text-align: left;
            margin-left: 0;
        }

        body.office-user-page .office-user-footer .btn-save-custom {
            flex: 1 1 auto;
        }

        .office-user-vessels-search {
            max-width: none;
            flex: 1 1 auto;
        }
    }
</style>
