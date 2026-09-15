@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="vessels-filters-open" toolbarClass="vessels-filters-toolbar" />
    <x-lists.multiselect-assets />

    <style>
        body.vessels-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.vessels-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.vessels-list-page .pcoded-inner-content,
        body.vessels-list-page .main-body,
        body.vessels-list-page .page-wrapper,
        body.vessels-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .vessels-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 64px);
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
            background: #fff;
        }
        .vessels-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .vessels-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .vessels-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .vessels-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .btn-vessels-tracker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            white-space: nowrap;
            border: none;
            border-radius: 8px;
            background: linear-gradient(145deg, #00aeef 0%, #008080 100%);
            box-shadow: 0 2px 8px rgba(0, 128, 128, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-vessels-tracker:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }
        .btn-vessels-tracker-mobile {
            display: none;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 8px;
            background: #fff;
            color: #008080;
            border: 1px solid #008080;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-vessels-tracker-mobile:hover {
            background: #008080;
            color: #fff;
            text-decoration: none;
        }

        .vessels-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #vessels-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }
        #vessels-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #0e1d4a;
            background: linear-gradient(180deg, #f0fafb 0%, #f8fafc 100%);
            border-bottom: 2px solid #008080;
            white-space: nowrap;
        }
        #vessels-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #vessels-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #vessels-table tbody tr:last-child td {
            border-bottom: none;
        }

        .vessel-name-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        .vessel-name-link,
        .vessel-customer-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .vessel-name-link:hover,
        .vessel-customer-link:hover {
            color: #006666;
            text-decoration: underline;
        }
        .vessel-alias-meta {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 280px;
        }

        .vessel-imo {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            color: #0e1d4a;
            letter-spacing: 0.02em;
        }

        .vessel-type-chip {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            color: #0e1d4a;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vessel-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            min-width: 72px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            border: 1px solid transparent;
        }
        .vessel-status-pill.is-active {
            color: #065f46;
            background: #ecfdf5;
            border-color: #6ee7b7;
        }
        .vessel-status-pill.is-inactive {
            color: #64748b;
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .vessel-status-pill.is-blocked {
            color: #92400e;
            background: #fffbeb;
            border-color: #fcd34d;
        }

        .vessel-muted {
            color: #94a3b8;
            font-weight: 600;
        }

        .vessel-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            color: #64748b;
            background: transparent;
            border: 1px solid transparent;
            transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease;
        }
        .vessel-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #vessels-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .btn-vessels-tracker {
                display: none !important;
            }
            .btn-vessels-tracker-mobile {
                display: inline-flex !important;
                align-items: center;
            }
            .filter-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }
            .vessel-alias-meta {
                max-width: 180px;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('vessels-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card vessels-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Vessels"
                subtitle="Browse vessels across customers — filter by name, IMO, or type"
                icon="ti-anchor"
                :count="$vessels->total()"
                countLabel="vessels"
            >
                <x-slot:actions>
                    <a href="{{ route('vessels.live-tracker') }}" class="btn-vessels-tracker">
                        <i class="ti-location-pin"></i> Live tracker
                    </a>
                </x-slot:actions>
            </x-lists.page-header>

            <div class="vessels-filters-area" data-mc-filter-persist-key="vessels-list-filter-values-v1">
                <x-lists.filter-toolbar
                    toggle-id="btn-vessels-filters-toggle"
                    body-class="vessels-filters-open"
                    toolbar-class="vessels-filters-toolbar"
                >
                    <x-slot:actions>
                        <a href="{{ route('vessels.live-tracker') }}" class="btn-vessels-tracker-mobile">Live tracker</a>
                    </x-slot:actions>
                </x-lists.filter-toolbar>

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Vessel name" width="200px">
                        <input type="text" id="vesselNameFilter" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="IMO" width="140px">
                        <input type="text" id="imoFilter" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Type" width="180px">
                        <select id="typeFilter" class="form-control filter-input vessel-type-select">
                            <option value=""></option>
                            @foreach ($vesselTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.clear-filters id="clear-vessel-filters" />
                </x-lists.filter-bar>
            </div>

            <div class="vessels-table-area list-ajax-table-wrapper">
                <table id="vessels-table">
                    <thead>
                        @include('Vessels.partials.table-head-row')
                    </thead>
                    <tbody>
                        @include('Vessels.partials.rows')
                    </tbody>
                </table>
            </div>

            <div id="vessels-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $vessels])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('body').addClass('vessels-list-page');

            $('#typeFilter').select2({
                placeholder: 'Select type',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity,
                dropdownCssClass: 'mc-filter-select2-dropdown'
            });

            function fixVesselsFilterSelect2Width() {
                $('.filter-row .select2-container').css('width', '100%');
            }

            var table = $('#vessels-table').DataTable({
                dom: 'rt',
                paging: false,
                info: false,
                lengthChange: false,
                responsive: false,
                searching: false,
                ordering: true,
                order: [],
                autoWidth: false,
                scrollX: false,
                columnDefs: [
                    { orderable: false, targets: 5 }
                ],
                language: {
                    emptyTable: 'No vessels found.'
                }
            });

            $(document).on('click', '.list-filters-toggle[data-body-class="vessels-filters-open"]', function () {
                setTimeout(function () {
                    fixVesselsFilterSelect2Width();
                    table.columns.adjust();
                }, 50);
            });

            $(window).on('resize', function () {
                fixVesselsFilterSelect2Width();
                table.columns.adjust();
            });

            setTimeout(function () {
                fixVesselsFilterSelect2Width();
                table.columns.adjust();
            }, 100);

            window.vesselsListFilters = bindAjaxListFilters({
                tableSelector: '#vessels-table',
                paginationSelector: '#vessels-pagination',
                indexUrl: @json(route('vessels.index')),
                existingTable: table,
                clearSelector: '#clear-vessel-filters',
                getParams: function (page) {
                    return {
                        name: $.trim($('#vesselNameFilter').val() || ''),
                        imo: $.trim($('#imoFilter').val() || ''),
                        type: $.trim($('#typeFilter').val() || ''),
                        page: page || 1
                    };
                },
                textSelectors: '#vesselNameFilter, #imoFilter',
                changeSelectors: '#typeFilter',
                resetFields: function () {
                    $('#vesselNameFilter, #imoFilter').val('');
                    $('#typeFilter').val(null).trigger('change');
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });
        });
    </script>
@endpush
