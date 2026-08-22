<style>
    body.edit-vessel-page {
        padding-bottom: 84px;
    }

    .page-body:has(.edit-vessel-page) {
        padding: 0 !important;
        margin: 0 !important;
    }

    .edit-vessel-page {
        width: 100%;
        margin: 0;
        padding: 12px 0 28px;
        background:
            radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
            radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
            #f5f7fb;
        box-sizing: border-box;
    }

    .edit-vessel-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 0 0 10px;
        padding: 4px 16px 0;
    }

    .edit-vessel-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .edit-vessel-hero-icon {
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

    .edit-vessel-kicker {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .edit-vessel-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0e1d4a;
        line-height: 1.2;
    }

    .edit-vessel-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
        max-width: 40rem;
    }

    .edit-vessel-sub strong {
        color: #0e1d4a;
        font-weight: 700;
    }

    .edit-vessel-back {
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
    }

    .edit-vessel-back:hover {
        border-color: #00aeef;
        background: #e8f6fc;
        color: #0088c7;
        text-decoration: none;
    }

    .edit-vessel-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 16px 12px;
    }

    .edit-vessel-meta-pill {
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

    .edit-vessel-meta-pill strong {
        color: #0e1d4a;
        font-weight: 800;
    }

    .edit-vessel-meta-pill.is-active {
        background: #ecfdf5;
        border-color: #6ee7b7;
        color: #065f46;
    }

    .edit-vessel-meta-pill.is-warning {
        background: #fffbeb;
        border-color: #fcd34d;
        color: #92400e;
    }

    .edit-vessel-meta-pill.is-muted {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .tabs-container {
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

    .tab-item {
        flex: 0 0 auto;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .tab-item.active {
        color: #0e1d4a;
        background: linear-gradient(180deg, #f0fafb 0%, #ffffff 100%);
        border-color: #94c9e3;
        box-shadow: inset 0 -2px 0 #008080;
    }

    .edit-vessel-card {
        position: relative;
        margin: 0;
        background: #fff;
        border-top: 1px solid #d6e3ee;
        border-bottom: 1px solid #d6e3ee;
        box-shadow: 0 8px 24px rgba(14, 29, 74, 0.04);
        overflow: visible;
    }

    .edit-vessel-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
        pointer-events: none;
    }

    .tab-content-custom {
        display: none;
    }

    .tab-content-custom.active {
        display: block;
    }

    .form-pillar-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        padding: 20px 16px 24px;
        align-items: stretch;
    }

    .form-pillar-container--4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .form-pillar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
        border: 1px solid #d6e3ee;
        border-radius: 14px;
        padding: 14px 14px 12px;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
        min-width: 0;
    }

    .form-section-header {
        margin: 0 0 4px;
        padding: 0 0 10px 10px;
        border-bottom: 1px solid #e8eef4;
        border-left: 3px solid #00aeef;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #0e1d4a;
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

    #vesselForm .form-control-custom,
    #vesselForm .form-textarea-custom {
        width: 100%;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        background: #fff;
        color: #0e1d4a;
        font-size: 13px;
    }

    #vesselForm .form-control-custom {
        height: var(--mc-control-height, 34px);
        padding: 0 10px;
    }

    #vesselForm .form-textarea-custom {
        padding: 8px 10px;
        min-height: 72px;
        resize: vertical;
        line-height: 1.4;
    }

    #vesselForm .form-control-custom:focus,
    #vesselForm .form-textarea-custom:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    #vesselForm .form-control-custom.error,
    #vesselForm .form-textarea-custom.error {
        border-color: #dc2626 !important;
    }

    body.edit-vessel-page #vesselForm .select2-container--default .select2-selection--single {
        border: 1px solid #d6e3ee !important;
        border-radius: 8px !important;
        background: #fff !important;
    }

    .input-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .vessel-checkbox-stack {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 12px;
    }

    .vessel-checkbox-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: #475569;
    }

    .vessel-checkbox-item input {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        accent-color: #008080;
        flex-shrink: 0;
    }

    .vessel-contact-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .vessel-contact-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .vessel-contact-card-header label {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #008080;
        white-space: nowrap;
    }

    .vessel-contact-card-header .contact-select-wrap {
        flex: 1;
        min-width: 0;
    }

    .btn-remove-vessel-contact {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 0 4px;
    }

    .btn-remove-vessel-contact:hover {
        color: #dc2626;
    }

    .vessel-contact-checkboxes {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .vessel-contact-checkboxes label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 12px;
        color: #475569;
        font-weight: 500;
        cursor: pointer;
    }

    .vessel-contact-checkboxes input {
        width: 16px;
        height: 16px;
        accent-color: #008080;
    }

    .btn-add-vessel-contact {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        font-size: 13px;
        font-weight: 600;
        color: #008080;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-add-vessel-contact:hover {
        color: #006666;
        text-decoration: underline;
    }

    .vessel-tab-placeholder {
        padding: 32px 20px;
        font-size: 14px;
        color: #64748b;
    }

    .error-message {
        color: #dc2626;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 500;
    }

    body.edit-vessel-page .vessel-edit-footer {
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

    body.edit-vessel-page .vessel-edit-footer .btn-save-custom {
        background: linear-gradient(135deg, #00aeef 0%, #008080 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 10px 28px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer;
    }

    body.edit-vessel-page .vessel-edit-footer .btn-cancel-custom {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    body.edit-vessel-page .vessel-edit-footer .audit-info {
        margin-left: auto;
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
    }

    @media (max-width: 1399.98px) {
        .form-pillar-container--4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .form-pillar-container,
        .form-pillar-container--4 {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        body.edit-vessel-page .vessel-edit-footer {
            left: 0 !important;
            width: 100vw !important;
            padding: 12px 16px !important;
            flex-wrap: wrap;
        }

        body.edit-vessel-page .vessel-edit-footer .audit-info {
            width: 100%;
            text-align: left;
            margin-left: 0;
        }

        .vessel-contact-card-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
