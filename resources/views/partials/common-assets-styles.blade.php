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

    /* ========== Unified control height (text + Select2 single) ==========
       Use --mc-control-height (34px). Keep filter multi + doc-type selects compact. */
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
    html body .mc-input:not(textarea) {
        height: var(--mc-control-height, 34px) !important;
        min-height: var(--mc-control-height, 34px) !important;
        max-height: var(--mc-control-height, 34px) !important;
        font-size: var(--mc-control-font-size, 14px) !important;
        line-height: 1.25 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        box-sizing: border-box !important;
    }

    /* Textareas — restore multi-line look; never lock to single-line control height */
    html body textarea,
    html body textarea.form-control,
    html body textarea.form-control-sm,
    html body textarea.form-control-sm-custom,
    html body textarea.field-input,
    html body textarea.crr-input,
    html body textarea.mc-input,
    html body textarea.form-control-notes,
    html body .comment-textarea {
        max-height: none !important;
        box-sizing: border-box !important;
    }

    html body .select2-container--default .select2-selection--single,
    html body .filter-group .select2-container--default .select2-selection--single {
        height: var(--mc-control-height, 34px) !important;
        min-height: var(--mc-control-height, 34px) !important;
        max-height: var(--mc-control-height, 34px) !important;
        display: flex !important;
        align-items: center !important;
        box-sizing: border-box !important;
    }

    /* Select2 — vertically center selected text on every page
       (html body = beat most page-local line-height: NNpx overrides) */
    html body .select2-container--default .select2-selection--single .select2-selection__rendered,
    html body .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
        display: flex !important;
        align-items: center !important;
        line-height: 1.25 !important;
        font-size: var(--mc-control-font-size, 14px) !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        height: 100% !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }
    html body .select2-container--default .select2-selection--single .select2-selection__placeholder {
        line-height: 1.25 !important;
    }
    html body .select2-container--default .select2-selection--single .select2-selection__arrow,
    html body .filter-group .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        margin-top: 0 !important;
    }
    html body .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    html body .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
    }

    /* Compact exceptions: document type Select2 under docs lists */
    html body #doc-panel-docs .select2-container--default .select2-selection--single,
    html body #crr-documents-panel .select2-container--default .select2-selection--single {
        height: 22px !important;
        min-height: 22px !important;
        max-height: 22px !important;
    }
    html body #doc-panel-docs .select2-container--default .select2-selection--single .select2-selection__rendered,
    html body #crr-documents-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 12px !important;
        line-height: 1.2 !important;
    }

    /* Visual 1px teal hairline scrollbar (browser clamps native width) */
    * {
        scrollbar-width: thin !important;
        scrollbar-color: #008080 transparent !important;
    }
    *::-webkit-scrollbar {
        -webkit-appearance: none !important;
        width: 5px !important;
        height: 5px !important;
        background: transparent !important;
    }
    *::-webkit-scrollbar-track {
        background: transparent !important;
    }
    *::-webkit-scrollbar-thumb {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: inset -1px 0 0 #008080, inset 0 -1px 0 #008080 !important;
    }
    *::-webkit-scrollbar-thumb:hover {
        box-shadow: inset -1px 0 0 #006666, inset 0 -1px 0 #006666 !important;
    }
</style>
