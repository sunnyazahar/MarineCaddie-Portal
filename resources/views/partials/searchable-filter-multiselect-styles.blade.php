<style>
    .filter-group,
    .filter-item {
        overflow: visible !important;
    }

    .searchable-filter-wrapper {
        flex: 1;
        min-width: 0;
        position: relative;
    }

    .searchable-filter-wrapper .select2-container {
        width: 100% !important;
    }

    /* Closed control — single-line like old Multiselect */
    .searchable-filter-wrapper .select2-container .select2-selection--multiple {
        min-height: 30px !important;
        height: 30px !important;
        max-height: 30px !important;
        padding: 0 22px 0 8px !important;
        overflow: hidden !important;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fff;
        color: #1e293b;
        font-size: 11px;
        cursor: pointer;
    }

    .filter-group .searchable-filter-wrapper .select2-container .select2-selection--multiple,
    .filter-item .searchable-filter-wrapper .select2-container .select2-selection--multiple {
        border: 0 !important;
        border-radius: 0 !important;
    }

    .searchable-filter-wrapper .select2-container--default.select2-container--focus .select2-selection--multiple,
    .searchable-filter-wrapper .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: #0088c7 !important;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.16);
    }

    .filter-group .searchable-filter-wrapper .select2-container--focus .select2-selection--multiple,
    .filter-group .searchable-filter-wrapper .select2-container--open .select2-selection--multiple {
        box-shadow: none;
    }

    .searchable-filter-wrapper .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        line-height: 1.25 !important;
    }

    .searchable-filter-wrapper .select2-selection__choice,
    .searchable-filter-wrapper .select2-selection__clear {
        display: none !important;
    }

    /* Keep Select2 inline search usable for filtering, but visually hidden */
    .searchable-filter-wrapper .select2-search--inline {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        border: 0 !important;
        opacity: 0 !important;
    }

    .searchable-filter-wrapper .mc-filter-summary {
        display: block;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #0e1d4a !important;
        font-size: 12px !important;
        font-weight: 600;
        line-height: 30px;
        pointer-events: none;
    }

    .searchable-filter-wrapper .mc-filter-summary.is-placeholder {
        color: #64748b !important;
        font-weight: 500 !important;
    }

    .searchable-filter-wrapper .select2-selection--multiple {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%230088c7' d='M1 1l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
    }

    /* Dropdown — Multiselect-like panel */
    .select2-container--open .mc-filter-select2-dropdown,
    .mc-filter-select2-dropdown,
    .select2-dropdown.mc-filter-select2-dropdown {
        min-width: 280px !important;
        border: 1px solid #9ec9d6 !important;
        border-radius: 10px !important;
        box-shadow: 0 12px 32px rgba(14, 29, 74, 0.14) !important;
        overflow: hidden;
        z-index: 2000 !important;
        background: #fff !important;
    }

    .mc-filter-dropdown-tools {
        padding: 10px 10px 4px;
        background: linear-gradient(180deg, #f5fbfe, #fff);
        border-bottom: 1px solid #e2eef2;
    }

    .mc-filter-dropdown-search {
        display: block;
        width: 100%;
        height: 34px;
        margin: 0;
        border: 1px solid #9ec9d6 !important;
        border-left: 4px solid #0088c7 !important;
        border-radius: 6px !important;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px !important;
        outline: none !important;
        box-shadow: none !important;
        color: #0e1d4a;
        background: #fff;
    }

    .mc-filter-dropdown-search::placeholder {
        color: #64748b;
        font-weight: 500;
    }

    .mc-filter-select2-dropdown .mc-filter-clear {
        display: block;
        width: 100%;
        padding: 8px 10px 10px;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        color: #0088c7;
        background: #fff;
        border: 0;
        cursor: pointer;
        text-decoration: none;
    }

    .mc-filter-select2-dropdown .mc-filter-clear:hover {
        color: #0e1d4a;
        text-decoration: underline;
    }

    /* Hide Select2's own (often empty) dropdown search for multiples */
    .mc-filter-select2-dropdown > .select2-search--dropdown {
        display: none !important;
    }

    .mc-filter-select2-dropdown .select2-results {
        padding: 0 0 6px;
    }

    .mc-filter-select2-dropdown .select2-results__options {
        max-height: 280px;
        overflow-y: auto;
    }

    .mc-filter-select2-dropdown .select2-results__option {
        padding: 8px 14px !important;
        color: #0e1d4a !important;
        font-size: 12px !important;
        background: #fff !important;
        white-space: normal;
    }

    .mc-filter-select2-dropdown .select2-results__option--highlighted[aria-selected],
    .mc-filter-select2-dropdown .select2-results__option--highlighted {
        background: #e8f6fc !important;
        color: #0e1d4a !important;
    }

    .mc-filter-select2-dropdown .select2-results__option[aria-selected="true"] {
        background: #f0fafd !important;
    }

    .mc-filter-option {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mc-filter-option__check {
        flex: 0 0 14px;
        width: 14px;
        height: 14px;
        border: 1.5px solid #64748b;
        border-radius: 3px;
        background: #fff;
        position: relative;
    }

    .select2-results__option[aria-selected="true"] .mc-filter-option__check {
        border-color: #0088c7;
        background: #0088c7;
    }

    .select2-results__option[aria-selected="true"] .mc-filter-option__check::after {
        content: '';
        position: absolute;
        left: 3px;
        top: 0px;
        width: 5px;
        height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .mc-filter-option__label {
        flex: 1;
        min-width: 0;
        color: #0e1d4a;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        line-height: 1.35;
    }
</style>
