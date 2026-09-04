/* Reminder compose modal — matches the manifest email composer. */
#compose-reminder-modal .modal-dialog {
    max-width: 860px;
    margin: 1.75rem auto;
}
#compose-reminder-modal .modal-content {
    position: relative;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 10px 40px rgba(15, 23, 42, .12);
}
#compose-reminder-modal .compose-header {
    padding: 16px 20px 12px;
    border-bottom: 1px solid #e5e7eb;
}
#compose-reminder-modal .compose-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #374151;
}
#compose-reminder-modal .compose-body {
    padding: 16px 20px 8px;
}
#compose-reminder-modal .compose-field {
    margin-bottom: 10px;
}
#compose-reminder-modal .compose-field-contact {
    position: relative;
}
#compose-reminder-modal .compose-field-with-icon {
    display: flex;
    align-items: center;
    gap: 8px;
}
#compose-reminder-modal .compose-field-with-icon .compose-input {
    flex: 1;
    min-width: 0;
}
#compose-reminder-modal .compose-input {
    width: 100%;
    height: 30px;
    padding: 5px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: #fff;
    color: #111827;
    font-size: 13px;
}
#compose-reminder-modal .compose-input:focus {
    outline: none;
    border-color: #93c5fd;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, .15);
}
#compose-reminder-modal .compose-input::placeholder {
    color: #9ca3af;
}
#compose-reminder-modal .compose-contact-btn {
    width: 34px;
    height: 30px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
}
#compose-reminder-modal .compose-contact-btn:hover,
#compose-reminder-modal .compose-contact-btn.active {
    background: #e0f2fe;
    border-color: #93c5fd;
    color: #0369a1;
}
#compose-reminder-modal .compose-contact-btn i {
    font-size: 15px;
    line-height: 1;
}
#compose-reminder-modal .compose-contact-picker {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 4px);
    z-index: 20;
    width: min(360px, calc(100vw - 80px));
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .14);
}
#compose-reminder-modal .compose-contact-picker.open {
    display: block;
}
#compose-reminder-modal .compose-contact-search {
    width: 100%;
    padding: 10px 12px;
    border: 0;
    border-bottom: 1px solid #e5e7eb;
    font-size: 12px;
    outline: none;
}
#compose-reminder-modal .compose-contact-list {
    max-height: 220px;
    overflow-y: auto;
    margin: 0;
    padding: 0;
    list-style: none;
}
#compose-reminder-modal .compose-contact-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
}
#compose-reminder-modal .compose-contact-item:hover {
    background: #f0f9ff;
}
#compose-reminder-modal .compose-contact-name {
    margin: 0;
    color: #111827;
    font-size: 12px;
    font-weight: 600;
}
#compose-reminder-modal .compose-contact-email {
    margin: 2px 0 0;
    color: #64748b;
    font-size: 11px;
}
#compose-reminder-modal .compose-contact-empty {
    padding: 14px 12px;
    color: #94a3b8;
    font-size: 12px;
    text-align: center;
}
#compose-reminder-modal .compose-editor-wrap {
    overflow: hidden;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: #fff;
}
#compose-reminder-modal .compose-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 2px;
    padding: 8px 10px;
    border-bottom: 1px solid #e5e7eb;
    background: #fafafa;
}
#compose-reminder-modal .compose-toolbar select {
    height: 28px;
    margin-right: 4px;
    padding: 0 6px;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    background: #fff;
    color: #374151;
    font-size: 12px;
}
#compose-reminder-modal .compose-tool-btn {
    min-width: 28px;
    height: 28px;
    padding: 0 6px;
    border: 0;
    border-radius: 3px;
    background: transparent;
    color: #4b5563;
    font-size: 13px;
    line-height: 1;
    cursor: pointer;
}
#compose-reminder-modal .compose-tool-btn:hover {
    background: #e5e7eb;
    color: #111827;
}
@include('partials.compose-color-tools-styles')
#compose-reminder-modal .compose-editor {
    min-height: 220px;
    max-height: 340px;
    overflow-y: auto;
    padding: 12px 14px;
    color: #111827;
    font-size: 13px;
    line-height: 1.5;
    white-space: pre-wrap;
    outline: none;
}
#compose-reminder-modal .compose-editor:empty::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    pointer-events: none;
}
#compose-reminder-modal .compose-attach-row {
    margin-top: 14px;
}
#compose-reminder-modal .btn-compose-attach {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px solid #3b82f6;
    border-radius: 4px;
    background: #3b82f6;
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
}
#compose-reminder-modal .btn-compose-attach:hover {
    border-color: #2563eb;
    background: #2563eb;
    color: #fff;
}
#compose-reminder-modal .compose-attach-hint {
    margin: 6px 0 0;
    color: #9ca3af;
    font-size: 12px;
}
#compose-reminder-modal .compose-attach-previews {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 10px;
    margin-top: 12px;
}
#compose-reminder-modal .compose-attach-card {
    position: relative;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #f8fafc;
}
#compose-reminder-modal .compose-attach-remove {
    position: absolute;
    top: 6px;
    right: 6px;
    z-index: 2;
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    background: #ef4444;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
}
#compose-reminder-modal .compose-attach-thumb {
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #e5e7eb;
}
#compose-reminder-modal .compose-attach-thumb iframe,
#compose-reminder-modal .compose-attach-thumb img {
    width: 100%;
    height: 100%;
    border: 0;
    object-fit: cover;
    background: #fff;
    pointer-events: none;
}
#compose-reminder-modal .compose-attach-thumb .attach-icon {
    color: #64748b;
    font-size: 36px;
}
#compose-reminder-modal .compose-attach-meta {
    padding: 8px 10px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
}
#compose-reminder-modal .compose-attach-name {
    overflow: hidden;
    margin: 0;
    color: #1f2937;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    text-overflow: ellipsis;
}
#compose-reminder-modal .compose-attach-type {
    margin: 2px 0 0;
    color: #6b7280;
    font-size: 10px;
}
#compose-reminder-modal .compose-input.compose-input-readonly {
    background: #f8fafc;
    color: #475569;
    cursor: default;
}
#compose-reminder-modal .compose-from-hint {
    margin: -4px 0 10px;
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
}
#compose-reminder-modal .btn-compose-draft {
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
#compose-reminder-modal .btn-compose-draft:hover {
    background: #e5e7eb;
    color: #111827;
}
#compose-reminder-modal .compose-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px 18px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
}
#compose-reminder-modal .compose-footer-right {
    display: flex;
    gap: 8px;
}
#compose-reminder-modal .btn-compose-discard,
#compose-reminder-modal .btn-compose-send {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 4px;
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
}
#compose-reminder-modal .btn-compose-discard {
    border: 1px solid #ef4444;
    background: #ef4444;
}
#compose-reminder-modal .btn-compose-discard:hover {
    border-color: #dc2626;
    background: #dc2626;
    color: #fff;
}
#compose-reminder-modal .btn-compose-send {
    padding-right: 16px;
    padding-left: 16px;
    border: 1px solid #14b8a6;
    background: #14b8a6;
}
#compose-reminder-modal .btn-compose-send:hover {
    border-color: #0d9488;
    background: #0d9488;
    color: #fff;
}
#compose-reminder-modal .btn-compose-send:disabled {
    opacity: .7;
    cursor: not-allowed;
}
#compose-reminder-modal .compose-send-loader {
    display: none;
    position: absolute;
    inset: 0;
    z-index: 30;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 12px;
    border-radius: inherit;
    background: rgba(255, 255, 255, .82);
}
#compose-reminder-modal.compose-sending .compose-send-loader {
    display: flex;
}
#compose-reminder-modal .compose-send-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid #ccfbf1;
    border-top-color: #14b8a6;
    border-radius: 50%;
    animation: reminder-spin .75s linear infinite;
}
#compose-reminder-modal .compose-send-loader-text {
    margin: 0;
    color: #0f766e;
    font-size: 13px;
    font-weight: 600;
}
@keyframes reminder-spin {
    to { transform: rotate(360deg); }
}
