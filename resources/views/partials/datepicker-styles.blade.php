{{-- Shared jQuery UI datepicker theme — loaded after page styles so all calendars match --}}
<style>
    /* MarineCaddie shared calendar (readable type, brand sky/teal) */
    .ui-datepicker {
        background: #fff !important;
        border: 1px solid #d6e3ee !important;
        box-shadow: 0 12px 28px rgba(14, 29, 74, 0.14) !important;
        padding: 0 !important;
        font-family: 'Source Sans 3', 'Open Sans', ui-sans-serif, system-ui, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        z-index: 10050 !important;
        width: 308px !important;
        border-radius: 10px !important;
        overflow: visible !important;
        margin-top: 4px !important;
    }

    .ui-datepicker-header {
        background: #0088c7 !important;
        color: #fff !important;
        padding: 12px 40px !important;
        border: none !important;
        border-radius: 10px 10px 0 0 !important;
        position: relative !important;
        font-size: 15px !important;
    }

    .ui-datepicker-title {
        font-weight: 700 !important;
        text-align: center !important;
        width: 100% !important;
        margin: 0 !important;
        line-height: 1.4 !important;
        color: #fff !important;
        font-size: 15px !important;
    }

    .ui-datepicker-title select {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 6px !important;
        padding: 4px 6px !important;
        margin: 0 3px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        outline: none !important;
        vertical-align: middle !important;
    }

    .ui-datepicker-title select option {
        background: #fff !important;
        color: #0e1d4a !important;
        font-weight: 500 !important;
    }

    .ui-datepicker-prev,
    .ui-datepicker-next {
        cursor: pointer !important;
        color: #fff !important;
        background: rgba(255, 255, 255, 0.14) !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        text-align: center !important;
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        height: auto !important;
        width: auto !important;
        border: none !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
    }

    .ui-datepicker-prev {
        left: 10px !important;
    }

    .ui-datepicker-next {
        right: 10px !important;
    }

    .ui-datepicker-prev:hover,
    .ui-datepicker-next:hover {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #fff !important;
    }

    .ui-datepicker-prev span,
    .ui-datepicker-next span {
        display: none !important;
    }

    .ui-datepicker-prev::before {
        content: 'Prev';
    }

    .ui-datepicker-next::before {
        content: 'Next';
    }

    .ui-datepicker-calendar {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin: 8px 0 12px !important;
        table-layout: fixed !important;
        padding: 0 8px !important;
    }

    .ui-datepicker-calendar th {
        color: #64748b !important;
        font-weight: 700 !important;
        padding: 8px 0 !important;
        text-align: center !important;
        font-size: 11px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
        background: transparent !important;
    }

    .ui-datepicker-calendar th span {
        color: #64748b !important;
        font-weight: 700 !important;
    }

    .ui-datepicker-calendar td {
        padding: 2px !important;
        text-align: center !important;
        border: none !important;
        background: transparent !important;
        vertical-align: middle !important;
        width: 14.28% !important;
        height: 40px !important;
    }

    /* Kill theme sprites that can paint over the day number */
    .ui-datepicker .ui-datepicker-calendar td a,
    .ui-datepicker .ui-datepicker-calendar td span,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-default,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-hover,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-highlight {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        max-width: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        text-decoration: none !important;
        color: #0e1d4a !important;
        border-radius: 8px !important;
        transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 34px !important;
        text-align: center !important;
        border: 1px solid transparent !important;
        background: #fff !important;
        background-image: none !important;
        background-color: #fff !important;
        box-shadow: none !important;
        text-shadow: none !important;
        opacity: 1 !important;
        overflow: visible !important;
        position: relative !important;
        z-index: 1 !important;
    }

    .ui-datepicker .ui-datepicker-calendar td a:hover,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-hover {
        background: #e8f6fc !important;
        background-image: none !important;
        background-color: #e8f6fc !important;
        color: #0088c7 !important;
        border-color: #b6e4f5 !important;
    }

    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-other-month a,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-other-month span {
        color: #94a3b8 !important;
        font-weight: 600 !important;
        background: transparent !important;
        background-image: none !important;
    }

    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-today a,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-today .ui-state-highlight,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-highlight {
        border-color: #00aeef !important;
        color: #0e1d4a !important;
        background: #f0faff !important;
        background-image: none !important;
        background-color: #f0faff !important;
    }

    /* Selected day: solid brand fill + always-visible white number */
    .ui-datepicker .ui-datepicker-calendar td a.ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td a.ui-state-default.ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-current-day a,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-current-day a.ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-current-day a.ui-state-highlight,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-current-day .ui-state-default {
        background: #0088c7 !important;
        background-image: none !important;
        background-color: #0088c7 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        font-weight: 800 !important;
        border-color: #0088c7 !important;
        text-shadow: none !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-today a.ui-state-active,
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-today a.ui-state-default.ui-state-active {
        background: #0088c7 !important;
        background-image: none !important;
        background-color: #0088c7 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        border-color: #0077b0 !important;
    }

    /* Ensure day numbers are never clipped by theme sprites / overflow */
    .ui-datepicker .ui-datepicker-calendar td a::before,
    .ui-datepicker .ui-datepicker-calendar td a::after,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-default::before,
    .ui-datepicker .ui-datepicker-calendar td .ui-state-default::after {
        content: none !important;
        display: none !important;
    }

    .ui-datepicker .ui-datepicker-buttonpane {
        border-top: 1px solid #e8f0f6 !important;
        background: #f8fafc !important;
        margin: 0 !important;
        padding: 8px 10px !important;
        border-radius: 0 0 10px 10px !important;
    }

    .ui-datepicker .ui-datepicker-buttonpane button {
        font-size: 13px !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        border: 1px solid #d6e3ee !important;
        background: #fff !important;
        color: #0088c7 !important;
        padding: 5px 10px !important;
        cursor: pointer !important;
    }
</style>
