{{-- Loaded last in layout so page-local filter CSS cannot wash out label colors --}}
<style id="mc-filter-chrome">
    html body .filter-group {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        background: #ffffff !important;
        box-shadow: none !important;
        height: 32px !important;
        padding: 0 !important;
        overflow: visible !important;
        align-items: center !important;
    }

    html body .filter-group:focus-within {
        border-color: #ced4da !important;
        box-shadow: none !important;
    }

    html body .filter-group > .filter-label,
    html body .filter-group .filter-label {
        background: #f8fafc !important;
        background-color: #f8fafc !important;
        background-image: none !important;
        color: #64748b !important;
        font-weight: 500 !important;
        font-size: 11px !important;
        letter-spacing: normal !important;
        border-right: 1px solid #e2e8f0 !important;
        margin: 0 !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        height: 100% !important;
        text-shadow: none !important;
    }

    html body .filter-group .filter-input {
        color: #1e293b !important;
        font-weight: 400 !important;
        font-size: 11px !important;
        background: transparent !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    html body .filter-group .filter-input::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }

    html body .filter-group .filter-date-icon,
    html body .filter-group > i.ti-calendar {
        color: #64748b !important;
        opacity: 0.85 !important;
        margin: 0 !important;
        flex: 0 0 28px !important;
        width: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    html body .filter-group .filter-date-icon:hover,
    html body .filter-group > i.ti-calendar:hover {
        color: #008080 !important;
        opacity: 1 !important;
    }

    html body .searchable-filter-wrapper .mc-filter-summary {
        color: #1e293b !important;
        font-weight: 400 !important;
        font-size: 11px !important;
    }

    html body .searchable-filter-wrapper .mc-filter-summary.is-placeholder {
        color: #94a3b8 !important;
        font-weight: 400 !important;
    }

    html body .btn-clear-filters,
    html body a.clear-filters,
    html body .clear-filters {
        color: #008080 !important;
        font-weight: 400 !important;
    }
</style>
