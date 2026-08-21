{{-- Shared vendor CSS for authenticated app pages (Tailwind v2). Do not duplicate in feature blades. --}}
<link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
{{-- jQuery UI base (datepicker structure); visual theme overridden in datepicker-styles --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
@include('partials.searchable-filter-multiselect-styles')
<style>
    /* Prevent native multi-select listboxes from breaking filter rows */
    select.form-control[multiple]:not(.select2-hidden-accessible):not(.mc-column-picker-native),
    select[multiple].filter-input:not(.select2-hidden-accessible):not(.mc-column-picker-native) {
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        padding: 4px 8px !important;
        overflow: hidden !important;
    }

    .mc-column-picker-native {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        border: 0 !important;
    }

    .filter-group .searchable-filter-wrapper {
        flex: 1 1 auto;
        min-width: 0;
        width: auto !important;
    }

    .filter-group .select2-container {
        width: 100% !important;
    }

    .filter-group .select2-container .select2-selection--multiple {
        min-height: 30px !important;
        height: 30px !important;
        max-height: 30px !important;
        overflow: hidden !important;
    }

    .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        overflow: hidden !important;
        max-height: 30px !important;
    }

    /* Filter column picker — compact funnel button */
    .mc-column-picker {
        position: relative;
        display: inline-flex;
        width: 36px;
        height: 32px;
        flex: 0 0 36px;
    }

    .mc-column-picker__btn {
        width: 36px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #0088c7;
        border-radius: 4px;
        background: #fff;
        color: #0088c7;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .mc-column-picker__btn:hover {
        background: #e8f6fc;
    }

    .mc-column-picker__panel {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        z-index: 1200;
        min-width: 220px;
        max-height: 320px;
        overflow: auto;
        padding: 8px;
        background: #fff;
        border: 1px solid #d6e3ee;
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(14, 29, 74, 0.14);
    }

    .mc-column-picker__row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 6px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        color: #0e1d4a;
        cursor: pointer;
    }

    .mc-column-picker__row:hover {
        background: #f5f7fb;
    }

    .mc-column-picker__row input {
        margin: 0;
        accent-color: #0088c7;
    }

    .custom-col:has(> #filter-multiselect),
    .custom-col:has(> .mc-column-picker),
    .custom-col:has(.mc-column-picker) {
        flex: 0 0 44px !important;
        width: 44px !important;
        max-width: 44px !important;
    }

    /* Mobile: only the toolbar "Show/Hide filters" control — hide column picker funnel */
    @media (max-width: 991.98px) {
        .mc-column-picker,
        .mc-column-picker-native,
        .custom-col:has(.mc-column-picker),
        .custom-col:has(#filter-multiselect),
        .custom-col[style*="flex: 0 0 50px"] {
            display: none !important;
        }
    }
</style>
