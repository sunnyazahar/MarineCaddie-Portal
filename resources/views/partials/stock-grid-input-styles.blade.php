{{-- Shared Packages / Costs grid control sizing (stock edit + create CRR) --}}
<style>
    :root {
        --mc-stock-grid-input-height: 20px;
        --mc-stock-grid-input-font-size: 11px;
        --mc-stock-grid-input-padding-x: 6px;
    }

    #costsTable .crr-input:not(textarea),
    #packagesTable .crr-input:not(textarea) {
        height: var(--mc-stock-grid-input-height) !important;
        min-height: var(--mc-stock-grid-input-height) !important;
        max-height: var(--mc-stock-grid-input-height) !important;
        padding: 0 var(--mc-stock-grid-input-padding-x) !important;
        font-size: var(--mc-stock-grid-input-font-size) !important;
        border-radius: 0 !important;
        line-height: 1.1 !important;
        box-sizing: border-box !important;
    }

    #costsTable .crr-input:not(textarea):focus,
    #packagesTable .crr-input:not(textarea):focus {
        box-shadow: 0 0 0 2px rgba(0, 174, 239, 0.14) !important;
    }

    #costsTable td,
    #packagesTable td {
        padding: 2px 4px;
        vertical-align: middle;
    }

    #costsTable .select2-container--default .select2-selection--single,
    #packagesTable .select2-container--default .select2-selection--single,
    .crr-form-container #costsTable .select2-container--default .select2-selection--single,
    .crr-form-container #packagesTable .select2-container--default .select2-selection--single,
    .stock-main-content #costsTable .select2-container--default .select2-selection--single,
    .stock-main-content #packagesTable .select2-container--default .select2-selection--single {
        height: var(--mc-stock-grid-input-height) !important;
        min-height: var(--mc-stock-grid-input-height) !important;
        max-height: var(--mc-stock-grid-input-height) !important;
        border-radius: 0 !important;
    }

    #costsTable .select2-container--default .select2-selection--single .select2-selection__rendered,
    #packagesTable .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(var(--mc-stock-grid-input-height) - 2px) !important;
        font-size: var(--mc-stock-grid-input-font-size) !important;
        padding-left: var(--mc-stock-grid-input-padding-x) !important;
    }

    #costsTable .select2-container--default .select2-selection--single .select2-selection__arrow,
    #packagesTable .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(var(--mc-stock-grid-input-height) - 2px) !important;
        top: 1px !important;
    }
</style>
