{{-- Loaded last in layout so page-local form CSS cannot shrink control typography --}}
<style id="mc-form-control-typography">
    :root {
        --mc-control-font-size: 14px;
        --mc-label-font-size: 13px;
        --mc-meta-label-font-size: 10px;
        --mc-datatable-header-font-size: 13px;
        --shipment-input-font-size: var(--mc-control-font-size);
    }

    /* Inputs / selects / custom field classes */
    html body input[type="text"]:not([hidden]):not(.select2-search__field),
    html body input[type="email"],
    html body input[type="number"],
    html body input[type="tel"],
    html body input[type="url"],
    html body input[type="search"],
    html body input[type="password"],
    html body input[type="date"],
    html body input[type="time"],
    html body input[type="datetime-local"],
    html body select.form-control:not([multiple]):not(.select2-hidden-accessible),
    html body .form-control:not(textarea):not([multiple]):not(.select2-hidden-accessible),
    html body .form-control-sm:not(textarea):not([multiple]):not(.select2-hidden-accessible),
    html body .form-control-sm-custom:not(textarea):not([multiple]):not(.select2-hidden-accessible),
    html body .field-input:not(textarea),
    html body .crr-input:not(textarea),
    html body .mc-input:not(textarea),
    html body .filter-input:not(textarea):not([multiple]) {
        font-size: var(--mc-control-font-size, 14px) !important;
        line-height: 1.3 !important;
    }

    html body textarea,
    html body textarea.form-control,
    html body textarea.form-control-sm,
    html body textarea.form-control-sm-custom,
    html body textarea.field-input,
    html body textarea.crr-input,
    html body textarea.mc-input,
    html body textarea.form-control-notes,
    html body .comment-textarea {
        font-size: var(--mc-control-font-size, 14px) !important;
        line-height: 1.45 !important;
    }

    /* Select2 selected value */
    html body .select2-container--default .select2-selection--single .select2-selection__rendered,
    html body .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    html body .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: var(--mc-control-font-size, 14px) !important;
    }

    html body .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered,
    html body .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    html body .filter-group .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: 11px !important;
        line-height: 1.25 !important;
    }

    html body .select2-container--default .select2-results__option {
        font-size: var(--mc-control-font-size, 14px) !important;
    }

    /* Form labels — create/edit/modals */
    html body label,
    html body .crr-label,
    html body .field-label,
    html body .form-label,
    html body .form-label-custom,
    html body .form-group > label,
    html body .form-group-custom > label,
    html body .cs-pillar label,
    html body .cs-pillar .form-group > label,
    html body #addUserModal label,
    html body #editUserModal label,
    html body #add-supplier-modal label,
    html body #add-supplier-modal .crr-label {
        font-size: var(--mc-label-font-size, 13px) !important;
        line-height: 1.35 !important;
    }

    /* Keep compact filter chrome labels as-is (already styled in filter-chrome) */
    html body .filter-group > .filter-label,
    html body .filter-group .filter-label {
        font-size: 11px !important;
    }

    /* Summary / meta header labels (stock + shipment edit, etc.) */
    html body .meta-label,
    html body .summary-label {
        font-size: var(--mc-meta-label-font-size, 10px) !important;
        font-weight: 700 !important;
        color: #64748b !important;
        margin-bottom: 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }

    /* DataTables / list table headers — project-wide */
    html body table.dataTable thead th,
    html body .dataTables_wrapper table thead th,
    html body .dataTables_scrollHead thead th,
    html body .dataTables_scrollHeadInner thead th,
    html body table.office-table thead th,
    html body .office-table thead th,
    html body #offices-table thead th,
    html body #agents-table thead th,
    html body #customers-table thead th,
    html body #hubs-table thead th,
    html body #suppliers-table thead th,
    html body #other-companies-table thead th,
    html body #vessels-table thead th,
    html body #etl-table thead th,
    html body #change-logs-table thead th,
    html body #exports-table thead th,
    html body #exrates-table thead th {
        font-size: var(--mc-datatable-header-font-size, 13px) !important;
    }

    @media (max-width: 991.98px) {
        :root {
            --mc-control-font-size: 14px;
            --mc-label-font-size: 13px;
            --mc-datatable-header-font-size: 13px;
        }
    }
</style>
