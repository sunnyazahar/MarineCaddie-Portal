{{-- Loaded last in layout so page-local filter CSS cannot wash out label colors --}}
<style id="mc-filter-chrome">
    html body .filter-group {
        border: 1px solid #9ec9d6 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        box-shadow: 0 1px 2px rgba(14, 29, 74, 0.06) !important;
        padding: 0 !important;
        overflow: hidden !important;
        align-items: stretch !important;
    }

    html body .filter-group:focus-within {
        border-color: #0088c7 !important;
        box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.18) !important;
    }

    html body .filter-group > .filter-label,
    html body .filter-group .filter-label {
        background: #6992b5 !important;
        background-color: #6992b5 !important;
        background-image: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        letter-spacing: 0.02em !important;
        border-right: 1px solid #5a7fa0 !important;
        margin: 0 !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        height: auto !important;
        text-shadow: none !important;
    }

    html body .filter-group .filter-input {
        color: #0e1d4a !important;
        font-weight: 600 !important;
        background: transparent !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }

    html body .filter-group .filter-input::placeholder {
        color: #64748b !important;
        opacity: 1 !important;
    }

    html body .filter-group .filter-date-icon,
    html body .filter-group > i.ti-calendar {
        color: #0088c7 !important;
        opacity: 1 !important;
        margin: 0 !important;
        flex: 0 0 28px !important;
        width: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    html body .searchable-filter-wrapper .mc-filter-summary {
        color: #0e1d4a !important;
        font-weight: 600 !important;
        font-size: 12px !important;
    }

    html body .searchable-filter-wrapper .mc-filter-summary.is-placeholder {
        color: #64748b !important;
        font-weight: 500 !important;
    }

    html body .btn-clear-filters,
    html body a.clear-filters,
    html body .clear-filters {
        color: #0088c7 !important;
        font-weight: 700 !important;
    }
</style>
