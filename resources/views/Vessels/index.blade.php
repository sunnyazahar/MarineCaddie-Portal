@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="vessels-filters-open" toolbarClass="vessels-filters-toolbar" />

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
            min-width: 720px;
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
            .filter-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
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
            />

            <div class="vessels-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-vessels-filters-toggle"
                    body-class="vessels-filters-open"
                    toolbar-class="vessels-filters-toolbar"
                />

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
                    { orderable: false, targets: 4 }
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
