@props([
    'bodyClass' => 'list-filters-open',
    'toolbarClass' => 'list-filters-toolbar',
    'mobileOnlyToolbar' => true,
])

@once('lists.base-styles')
    @push('styles')
        <style>
            .list-filters-toolbar {
                display: none;
            }

            .btn-outline-teal {
                color: #008080;
                border-color: #008080;
                background-color: transparent;
            }

            .btn-outline-teal:hover,
            .btn-outline-teal.is-open {
                background-color: #008080;
                color: #fff;
                border-color: #008080;
            }

            .filter-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 15px;
                background: #fff;
                border-bottom: 1px solid #eee;
                flex-wrap: wrap;
            }

            .filter-item {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 8px;
            }

            .filter-group {
                display: flex;
                align-items: center;
                border: 1px solid #9ec9d6;
                border-radius: 8px;
                height: 32px;
                background: #fff;
                overflow: visible;
                width: 100%;
                box-shadow: 0 1px 2px rgba(14, 29, 74, 0.05);
            }

            .filter-group .filter-label {
                font-size: 11px;
                color: #ffffff;
                margin-bottom: 0;
                padding: 0 10px;
                white-space: nowrap;
                font-weight: 700;
                letter-spacing: 0.02em;
                border-right: 1px solid #5a7fa0;
                height: 100%;
                display: flex;
                align-items: center;
                background: #6992b5;
                background-color: #6992b5;
                min-width: fit-content;
            }

            .filter-group .filter-input {
                border: none !important;
                box-shadow: none !important;
                height: 100% !important;
                font-size: 12px;
                font-weight: 600;
                padding: 0 10px !important;
                background: transparent !important;
                width: 100%;
                color: #0e1d4a;
            }

            .filter-checkbox-group label {
                font-size: 11px;
                color: #334155;
                margin-bottom: 0;
                cursor: pointer;
                order: 2;
                font-weight: 600;
            }

            .btn-clear-filters {
                font-size: 11px;
                color: #0088c7;
                font-weight: 700;
                text-decoration: none;
                white-space: nowrap;
                margin-left: 15px;
            }

            .filter-group .searchable-filter-wrapper,
            .filter-group .select2-container {
                flex: 1;
                min-width: 0;
            }

            .filter-group .select2-container {
                width: 100% !important;
            }

            .filter-group .select2-container .select2-selection--multiple {
                min-height: 30px;
                height: 30px;
                padding: 0 26px 0 4px;
                overflow: hidden;
                border: 0;
                border-radius: 0;
                background: #fff;
                color: #1e293b;
                font-size: 11px;
            }

            .filter-checkbox-group {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-left: 15px;
                white-space: nowrap;
            }

            .filter-checkbox-group input[type="checkbox"] {
                width: 14px;
                height: 14px;
                cursor: pointer;
                accent-color: #1b5e6f;
                order: 1;
            }

            .list-ajax-table-wrapper {
                background: #fff;
                border-radius: 4px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .list-inline-toolbar {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }

            .list-inline-toolbar-search {
                flex: 1;
                min-width: 220px;
                max-width: 360px;
            }

            .list-inline-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            @media (max-width: 991.98px) {
                .list-inline-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 10px;
                }

                .list-inline-toolbar-search {
                    width: 100%;
                    max-width: none;
                }

                .list-inline-toolbar-actions {
                    width: 100%;
                    justify-content: stretch;
                }

                .list-inline-toolbar-actions .btn,
                .list-inline-toolbar-actions a.btn {
                    flex: 1 1 auto;
                    text-align: center;
                }
            }

            @media (max-width: 991.98px) {
                .{{ $toolbarClass }} {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    flex-wrap: wrap;
                    padding: 8px 12px;
                    background: #fff;
                    border-bottom: 1px solid #eee;
                }

                .{{ $toolbarClass }}-actions {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .filter-row {
                    display: none !important;
                    flex-direction: column;
                    align-items: stretch;
                    flex-wrap: nowrap;
                    gap: 10px;
                    max-height: 42vh;
                    overflow-x: hidden;
                    overflow-y: auto;
                    -webkit-overflow-scrolling: touch;
                    padding: 10px 12px 12px;
                }

                body.{{ $bodyClass }} .filter-row {
                    display: flex !important;
                }

                .list-filters-toggle.is-open {
                    background: #008080 !important;
                    color: #fff !important;
                }

                .filter-item,
                .filter-item[style*="margin-left"] {
                    flex-direction: column !important;
                    align-items: stretch !important;
                    margin-left: 0 !important;
                    width: 100%;
                    gap: 4px;
                }

                .filter-checkbox-group,
                .btn-clear-filters {
                    margin-left: 0 !important;
                }
            }

            @media (min-width: 992px) {
                @if ($mobileOnlyToolbar)
                .{{ $toolbarClass }} {
                    display: none !important;
                }
                @endif

                .filter-row {
                    display: flex !important;
                }
            }
        </style>
    @endpush
@endonce
