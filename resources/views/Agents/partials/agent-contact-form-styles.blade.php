<style>
    body.agent-contact-page {
        padding-bottom: 84px;
    }

    .page-body:has(.agent-contact-page) {
        padding: 0 !important;
        margin: 0 !important;
    }

    .agent-contact-page {
        width: 100%;
        margin: 0;
        padding: 12px 0 28px;
        background:
            radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
            radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
            #f5f7fb;
        box-sizing: border-box;
    }

    .agent-contact-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 0 0 14px;
        padding: 4px 16px 0;
    }

    .agent-contact-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .agent-contact-hero-icon {
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

    .agent-contact-kicker {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .agent-contact-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0e1d4a;
        line-height: 1.2;
    }

    .agent-contact-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
        max-width: 40rem;
    }

    .agent-contact-sub strong {
        color: #0e1d4a;
        font-weight: 700;
    }

    .agent-contact-back {
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

    .agent-contact-back:hover {
        border-color: #00aeef;
        background: #e8f6fc;
        color: #0088c7;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .agent-contact-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 16px 14px;
    }

    .agent-contact-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid #d6e3ee;
        background: #fff;
        font-size: 12px;
        color: #64748b;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04);
    }

    .agent-contact-meta-pill strong {
        color: #0e1d4a;
        font-weight: 700;
    }

    .agent-contact-card {
        position: relative;
        max-width: 760px;
        margin: 0 16px;
        background: #fff;
        border: 1px solid #d6e3ee;
        box-shadow: 0 8px 24px rgba(14, 29, 74, 0.06);
        overflow: visible;
    }

    .agent-contact-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
        pointer-events: none;
    }

    .agent-contact-form-container {
        padding: 20px 18px 24px;
    }

    .agent-contact-pillar {
        background: linear-gradient(180deg, #fbfdff 0%, #ffffff 48%);
        border: 1px solid #d6e3ee;
        border-radius: 14px;
        padding: 14px 14px 12px;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.04), 0 8px 22px rgba(14, 29, 74, 0.04);
    }

    .agent-contact-pillar__title {
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

    .agent-contact-fields {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
        max-width: 480px;
    }

    .agent-contact-page .form-group-custom {
        margin-bottom: 0;
    }

    .agent-contact-page .form-label-custom {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
    }

    .agent-contact-page .agent-contact-form .form-control-custom {
        width: 100%;
        height: var(--mc-control-height, 34px);
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        color: #0e1d4a;
        font-size: 13px;
    }

    .agent-contact-page .agent-contact-form textarea.form-control-custom {
        height: auto;
        min-height: 96px;
        padding: 8px 12px;
        line-height: 1.4;
        resize: vertical;
    }

    .agent-contact-page .agent-contact-form .form-control-custom:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    .agent-contact-page .agent-contact-form .form-control-custom.error {
        border-color: #dc2626 !important;
    }

    .agent-contact-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 4px;
        padding-top: 4px;
    }

    .agent-contact-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        flex-shrink: 0;
        accent-color: #008080;
    }

    .agent-contact-checkbox label {
        margin: 0;
        font-size: 13px;
        color: #475569;
        line-height: 1.35;
        cursor: pointer;
    }

    .agent-contact-form-alert {
        margin: 0 16px 12px;
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }

    .agent-contact-page .error-message {
        color: #dc2626;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 500;
    }

    body.agent-contact-page .agent-contact-footer {
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

    body.agent-contact-page .agent-contact-footer .btn-save-custom {
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

    body.agent-contact-page .agent-contact-footer .btn-cancel-custom {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    body.agent-contact-page .agent-contact-footer .btn-cancel-custom:hover {
        color: #008080 !important;
    }

    body.agent-contact-page .agent-contact-footer .audit-info {
        margin-left: auto;
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
    }

    body.agent-contact-page .agent-contact-footer .audit-info b {
        color: #64748b;
    }

    @media (max-width: 991.98px) {
        .agent-contact-card {
            max-width: none;
            margin: 0 12px;
        }

        .agent-contact-hero,
        .agent-contact-meta {
            padding-left: 12px;
            padding-right: 12px;
            margin-left: 0;
            margin-right: 0;
        }

        body.agent-contact-page .agent-contact-footer {
            left: 0 !important;
            width: 100vw !important;
            padding: 12px 16px !important;
            flex-wrap: wrap;
        }

        body.agent-contact-page .agent-contact-footer .audit-info {
            width: 100%;
            text-align: left;
            margin-left: 0;
        }

        body.agent-contact-page .agent-contact-footer .btn-save-custom {
            flex: 1 1 auto;
        }
    }
</style>
