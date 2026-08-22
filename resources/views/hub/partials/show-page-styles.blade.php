<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    body.edit-hub-page {
        padding-bottom: 84px;
    }

    .page-body:has(.edit-hub-page) {
        padding: 0 !important;
        margin: 0 !important;
    }

    .edit-hub-page {
        width: 100%;
        margin: 0;
        padding: 12px 0 28px;
        background:
            radial-gradient(ellipse 70% 40% at 100% 0%, rgba(0, 174, 239, 0.08), transparent 50%),
            radial-gradient(ellipse 50% 30% at 0% 0%, rgba(14, 29, 74, 0.05), transparent 45%),
            #f5f7fb;
        box-sizing: border-box;
    }

    .edit-hub-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin: 0 0 10px;
        padding: 4px 16px 0;
    }

    .edit-hub-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .edit-hub-hero-icon {
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

    .edit-hub-kicker {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .edit-hub-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0e1d4a;
        line-height: 1.2;
    }

    .edit-hub-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
        max-width: 40rem;
    }

    .edit-hub-back {
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

    .edit-hub-back:hover {
        border-color: #00aeef;
        background: #e8f6fc;
        color: #0088c7;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .edit-hub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 16px 12px;
    }

    .edit-hub-meta-pill {
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

    .edit-hub-meta-pill strong {
        color: #0e1d4a;
        font-weight: 800;
    }

    .edit-hub-meta-pill.is-active {
        background: #ecfdf5;
        border-color: #6ee7b7;
        color: #065f46;
    }

    .edit-hub-meta-pill.is-hidden {
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
        transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .tab-item i {
        font-size: 13px;
        opacity: 0.75;
    }

    .tab-item:hover {
        color: #0e1d4a;
        background: #f8fafc;
        text-decoration: none;
    }

    .tab-item.active {
        color: #0e1d4a;
        background: linear-gradient(180deg, #f0fafb 0%, #ffffff 100%);
        border-color: #94c9e3;
        box-shadow: inset 0 -2px 0 #008080;
    }

    .tab-item.active i {
        opacity: 1;
        color: #008080;
    }

    .edit-hub-card {
        position: relative;
        margin: 0;
        background: #fff;
        border-top: 1px solid #d6e3ee;
        border-bottom: 1px solid #d6e3ee;
        box-shadow: 0 8px 24px rgba(14, 29, 74, 0.04);
        overflow: visible;
    }

    .edit-hub-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff5a5f 0%, #e87722 35%, #00aeef 100%);
        pointer-events: none;
    }

    .edit-hub-alert {
        margin: 16px 16px 0;
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
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

    .form-pillar {
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
        line-height: 1.2;
    }

    .form-group-custom {
        margin-bottom: 0;
        position: relative;
        overflow: visible;
    }

    .form-label-custom {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
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
        font-weight: 500;
        color: #475569;
        line-height: 1.35;
        margin-bottom: 0;
    }

    #hubEditForm .form-group-custom:has(> input[type="checkbox"]) {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 10px;
        margin-top: 4px;
    }

    #hubEditForm .form-group-custom:has(> input[type="checkbox"]) .form-label-custom {
        margin-bottom: 0;
        line-height: 1.35;
        font-weight: 500;
    }

    #hubEditForm .form-group-custom:has(> input[type="checkbox"]) input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        flex-shrink: 0;
        accent-color: #008080;
    }

    .hub-checkbox-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 4px;
    }

    #hubEditForm .input-row:has(input[type="checkbox"]) {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #hubEditForm .input-row.rebate-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: start;
    }

    @media (min-width: 768px) {
        #hubEditForm .input-row.rebate-row {
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 200px);
            align-items: end;
        }
    }

    #hubEditForm .input-grid .checkbox-group {
        grid-column: 1 / -1;
        padding-top: 0;
    }

    #hubEditForm .form-input-custom,
    #hubEditForm .form-select-custom,
    #hubEditForm .form-textarea-custom {
        width: 100%;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        background: #fff;
        color: #0e1d4a;
        font-size: 13px;
    }

    #hubEditForm .form-input-custom,
    #hubEditForm .form-select-custom {
        height: var(--mc-control-height, 34px);
        padding: 0 10px;
    }

    #hubEditForm .form-textarea-custom {
        padding: 8px 10px;
        min-height: 72px;
        resize: vertical;
        line-height: 1.4;
    }

    #hubEditForm .form-input-custom:focus,
    #hubEditForm .form-select-custom:focus,
    #hubEditForm .form-textarea-custom:focus {
        outline: none;
        border-color: #0088c7;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.12);
    }

    #hubEditForm .form-input-custom.error,
    #hubEditForm .form-textarea-custom.error {
        border-color: #dc2626 !important;
    }

    .input-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .input-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .input-group-custom {
        position: relative;
        display: flex;
    }

    .input-group-custom .form-input-custom {
        padding-right: 36px;
    }

    .btn-input-append {
        position: absolute;
        right: 1px;
        top: 1px;
        height: calc(var(--mc-control-height, 34px) - 2px);
        width: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: none;
        border-left: 1px solid #e2e8f0;
        border-radius: 0 7px 7px 0;
        color: #64748b;
        cursor: pointer;
    }

    .error-message {
        color: #dc2626;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 500;
    }

    .select2-container--default.error .select2-selection--single,
    .select2-container--default.error .select2-selection--multiple {
        border-color: #dc2626 !important;
    }

    .select2-dropdown {
        z-index: 10060 !important;
    }

    .hub-pane-toolbar {
        display: flex;
        justify-content: flex-end;
        padding: 16px 16px 0;
        position: relative;
        z-index: 2;
    }

    .btn-hub-pane-action {
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
        box-shadow: 0 2px 8px rgba(0, 128, 128, 0.24);
    }

    .btn-hub-pane-action:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .hub-table-wrap {
        padding: 0 16px 20px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .custom-table th {
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

    .custom-table td {
        padding: 10px 14px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .custom-table tbody tr:hover td {
        background: #f5fbfe;
    }

    .table-link {
        color: #008080;
        font-weight: 600;
        text-decoration: none;
    }

    .table-link:hover {
        text-decoration: underline;
    }

    .btn-action-pencil {
        color: #94a3b8;
        font-size: 14px;
        transition: color 0.15s ease;
    }

    .btn-action-pencil:hover {
        color: #008080;
    }

    .upload-area {
        border: 1px dashed #c5dde8;
        border-radius: 10px;
        padding: 22px;
        text-align: center;
        color: #64748b;
        cursor: pointer;
        margin-top: 12px;
        transition: border-color 0.15s ease, background 0.15s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        background: #fbfdff;
    }

    .upload-area:hover {
        border-color: #00aeef;
        background: #e8f6fc;
    }

    .upload-icon {
        font-size: 18px;
        color: #008080;
    }

    .upload-text {
        font-size: 13px;
        color: #475569;
    }

    .file-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 12px;
        border: 1px solid #e8eef4;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #fff;
    }

    .file-name {
        font-size: 13px;
        color: #008080;
        font-weight: 600;
    }

    .file-meta {
        font-size: 11px;
        color: #94a3b8;
    }

    .btn-delete-file {
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        transition: color 0.15s ease, background 0.15s ease;
    }

    .btn-delete-file:hover {
        color: #dc2626;
        background: #fef2f2;
    }

    body.edit-hub-page .hub-edit-footer {
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

    body.edit-hub-page .hub-edit-footer .btn-save-custom {
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

    body.edit-hub-page .hub-edit-footer .btn-cancel-custom {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    body.edit-hub-page .hub-edit-footer .btn-cancel-custom:hover {
        color: #008080 !important;
    }

    body.edit-hub-page .hub-edit-footer .metadata-footer,
    body.edit-hub-page .hub-edit-footer .audit-info {
        margin-left: auto;
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
    }

    @media (max-width: 1199.98px) {
        .form-pillar-container {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 991.98px) {
        .form-pillar-container,
        .form-pillar-container[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            padding: 16px 12px 20px !important;
        }

        .input-row,
        .input-grid {
            grid-template-columns: 1fr !important;
        }

        .edit-hub-hero,
        .edit-hub-meta,
        .tabs-container {
            margin-left: 12px;
            margin-right: 12px;
        }

        body.edit-hub-page .hub-edit-footer {
            left: 0 !important;
            width: 100vw !important;
            padding: 12px 16px !important;
            flex-wrap: wrap;
        }

        body.edit-hub-page .hub-edit-footer .metadata-footer,
        body.edit-hub-page .hub-edit-footer .audit-info {
            width: 100%;
            text-align: left;
            margin-left: 0;
        }

        body.edit-hub-page .hub-edit-footer .btn-save-custom {
            flex: 1 1 auto;
        }

        .hub-pane-toolbar .btn-hub-pane-action {
            width: 100%;
        }
    }
</style>
