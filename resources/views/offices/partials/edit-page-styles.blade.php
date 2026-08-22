<style>
    body.edit-office-page {
        padding-bottom: 84px;
    }

    .page-body:has(.edit-office-page) {
        padding: 0 !important;
        margin: 0 !important;
    }

    .edit-office-page {
        width: 100%;
        margin: 0;
        padding: 12px 0 28px;
        background:
            radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
            radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
            #f5f7fb;
        box-sizing: border-box;
    }

    .edit-office-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 0 0 10px;
        padding: 4px 16px 0;
    }

    .edit-office-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .edit-office-hero-icon {
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

    .edit-office-kicker {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .edit-office-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0e1d4a;
        line-height: 1.2;
    }

    .edit-office-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
        max-width: 40rem;
    }

    .edit-office-back {
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

    .edit-office-back:hover {
        border-color: #00aeef;
        background: #e8f6fc;
        color: #0088c7;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .edit-office-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 16px 12px;
    }

    .edit-office-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #d6e3ee;
        font-size: 12px;
        color: #475569;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
    }

    .edit-office-meta-pill strong {
        color: #0e1d4a;
        font-weight: 800;
    }

    .edit-office-meta-pill.is-active {
        background: #ecfdf5;
        border-color: #6ee7b7;
        color: #065f46;
    }

    .edit-office-meta-pill.is-inactive {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .edit-office-tabs {
        display: flex;
        flex-wrap: nowrap;
        gap: 4px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 16px 0;
        padding: 6px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid #d6e3ee;
        border-radius: 10px 10px 0 0;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
    }

    .edit-office-tab {
        flex: 0 0 auto;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
    }

    .edit-office-tab:hover {
        color: #0e1d4a;
        background: #f8fafc;
        text-decoration: none;
    }

    .edit-office-tab.active {
        color: #0e1d4a;
        background: linear-gradient(180deg, #f0fafb 0%, #ffffff 100%);
        border-color: #94c9e3;
        box-shadow: inset 0 -2px 0 #008080;
    }

    .edit-office-card {
        position: relative;
        margin: 0;
        background: #fff;
        border-top: 1px solid #d6e3ee;
        border-bottom: 1px solid #d6e3ee;
        box-shadow: 0 8px 24px rgba(14, 29, 74, 0.04);
        overflow: visible;
    }

    .edit-office-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
        pointer-events: none;
    }

    .edit-office-alert {
        margin: 16px 16px 0;
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    #office-details.tab-pane.active {
        padding-bottom: 84px;
    }

    .office-form-container {
        padding: 20px 16px 24px;
    }

    .office-pillars {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    .office-pillar-col {
        display: flex;
        min-width: 0;
    }

    .office-pillar {
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

    .office-pillar__title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
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

    .office-section-shell {
        margin-top: 4px;
        padding: 12px 12px 10px;
        background: linear-gradient(180deg, #f8fcfd 0%, #ffffff 100%);
        border: 1px dashed #c5dde8;
        border-radius: 10px;
    }

    .office-section-shell__title {
        margin: 0 0 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
    }

    .form-group-custom {
        margin-bottom: 0;
        position: relative;
        overflow: visible;
    }

    .address-sub-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .address-sub-grid.is-two-col {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #officeEditForm .office-pillar .form-control-custom,
    #officeEditForm .office-pillar .form-textarea-custom,
    #officeEditForm .office-pillar .select-custom {
        width: 100%;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        background: #fff;
        color: #0e1d4a;
    }

    #officeEditForm .office-pillar .form-control-custom,
    #officeEditForm .office-pillar .select-custom {
        height: var(--mc-control-height, 34px);
        padding: 0 10px;
    }

    #officeEditForm .office-pillar .form-control-custom:focus,
    #officeEditForm .office-pillar .form-textarea-custom:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    #officeEditForm .office-pillar .form-textarea-custom {
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

    .btn-add-account {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #008080;
        background: #fff;
        border: 1px solid #94c9e3;
        border-radius: 8px;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-add-account:hover {
        background: #e8f6fc;
        border-color: #00aeef;
    }

    .account-block {
        padding: 12px 0 14px;
        margin-bottom: 12px;
        border-bottom: 1px solid #e8eef4;
    }

    .account-block:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .account-block__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .account-block__title {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #0e1d4a;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .remove-account-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
    }

    .remove-account-btn:hover {
        color: #ef4444;
        background: #fef2f2;
    }

    body.edit-office-page .edit-footer {
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

    body.edit-office-page .edit-footer .btn-save-custom {
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

    body.edit-office-page .edit-footer .btn-cancel-custom {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    body.edit-office-page .edit-footer .btn-cancel-custom:hover {
        color: #008080 !important;
    }

    body.edit-office-page .edit-footer .audit-info {
        margin-left: auto;
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
    }

    body.edit-office-page .edit-footer .audit-info b {
        color: #64748b;
    }

    .edit-office-tab-pane {
        padding: 16px;
    }

    .pane-header-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 12px;
    }

    .btn-pane-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
        border: none;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 128, 128, 0.24);
    }

    .btn-pane-action:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .ops-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
    }

    .ops-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
    }

    .ops-table th {
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

    .ops-table td {
        padding: 10px 14px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .ops-table tbody tr:hover td {
        background: #f5fbfe;
    }

    .ops-name-link {
        color: #008080;
        font-weight: 600;
        text-decoration: none;
    }

    .ops-name-link:hover {
        text-decoration: underline;
    }

    .ops-email-link {
        color: #0088c7;
        text-decoration: none;
    }

    .ops-action-icon {
        color: #94a3b8;
        font-size: 14px;
        transition: color 0.15s ease;
    }

    .ops-action-icon:hover {
        color: #008080;
    }

    .activated-icon {
        color: #008080;
        font-weight: bold;
    }

    .no-data-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 16px;
        color: #94a3b8;
    }

    .no-data-icon {
        font-size: 22px;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    .no-data-text {
        font-size: 13px;
        font-weight: 600;
    }

    .coming-soon-pane {
        padding: 48px 16px;
        text-align: center;
        color: #64748b;
    }

    .select2-dropdown {
        z-index: 10060 !important;
    }

    .custom-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        z-index: 10050;
        align-items: center;
        justify-content: center;
    }

    .custom-modal {
        background: #fff;
        width: 450px;
        max-width: calc(100vw - 24px);
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(14, 29, 74, 0.18);
        overflow: hidden;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #0e1d4a;
        margin-bottom: 6px;
    }

    .modal-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        font-size: 13px;
    }

    .modal-footer {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 24px;
    }

    .btn-modal-save {
        background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
        color: #fff;
        border: none;
        padding: 9px 22px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-modal-cancel {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    @media (max-width: 1399.98px) {
        .office-pillars {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .office-pillars,
        .address-sub-grid {
            grid-template-columns: 1fr !important;
        }

        .edit-office-hero,
        .edit-office-meta,
        .edit-office-tabs {
            padding-left: 12px;
            padding-right: 12px;
        }

        .edit-office-tabs {
            margin-left: 12px;
            margin-right: 12px;
        }

        body.edit-office-page .edit-footer {
            left: 0 !important;
            width: 100vw !important;
            padding: 12px 16px !important;
            flex-wrap: wrap;
        }

        body.edit-office-page .edit-footer .audit-info {
            width: 100%;
            text-align: left;
            margin-left: 0;
        }

        body.edit-office-page .edit-footer .btn-save-custom {
            flex: 1 1 auto;
        }

        .pane-header-actions .btn-pane-action {
            width: 100%;
        }
    }
</style>
