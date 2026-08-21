@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <x-lists.base-styles bodyClass="vessels-filters-open" toolbarClass="vessels-filters-toolbar" />
    <x-lists.multiselect-assets />
    <style>
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
        }
        .table-other-companies th {
            text-align: left;
            padding: 8px 5px;
            font-size: 13px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            background: #f8fafd;
        }
        .table-other-companies td {
            padding: 4px 8px;
            font-size: 13px !important;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .table-other-companies tr:hover td {
            background-color: #f9fafb;
        }

        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        #vessels-table {
            min-width: 720px !important;
            width: 100% !important;
        }

        #vessels-table th,
        #vessels-table td {
            font-size: 13px !important;
            line-height: 1.35;
            white-space: nowrap;
        }

        .filter-row .select2-container--default .select2-selection--single {
            height: 28px !important;
            min-height: 28px !important;
            font-size: 11px !important;
            background-color: #fff !important;
            border: none !important;
            border-radius: 0 !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding: 0 10px !important;
            color: #333 !important;
            background-color: #fff !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
            font-style: italic;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent !important;
        }

        /* Hide DataTables scrollX cloned header */
        #vessels-table_wrapper .dataTables_scrollBody > table > thead,
        #vessels-table_wrapper .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        #vessels-table_wrapper .dataTables_scrollBody thead tr,
        #vessels-table_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
            background: transparent !important;
        }
        #vessels-table_wrapper .dataTables_scrollBody thead th:before,
        #vessels-table_wrapper .dataTables_scrollBody thead th:after {
            display: none !important;
            content: none !important;
        }

        @media (max-width: 991.98px) {
            .filter-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }

            .filter-row .select2-container .select2-selection--single {
                height: 28px !important;
                min-height: 28px !important;
            }

            .card-block {
                padding: 12px !important;
            }

            #vessels-table th,
            #vessels-table td {
                font-size: 11px !important;
                padding: 8px 10px !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                padding-top: 8px;
                font-size: 11px !important;
            }

            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
            }

            .dt-responsive,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endsection

@section('content')
<!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    @include('layouts.partials.pcoded-shell-start')
                                        <div class="card" style="border-radius: 0; box-shadow: none; border: 1px solid #eef2f7;">
                                            <div class="card-block" style="padding: 15px;">
                                                <x-lists.filter-toolbar
                                                    toggle-id="btn-vessels-filters-toggle"
                                                    body-class="vessels-filters-open"
                                                    toolbar-class="vessels-filters-toolbar"
                                                />

                                                <x-lists.filter-bar>
                                                    <x-lists.filter-field label="Vessel name" width="200px">
                                                        <input type="text" id="vesselNameFilter" class="form-control filter-input" placeholder="type here">
                                                    </x-lists.filter-field>
                                                    <x-lists.filter-field label="IMO" width="120px">
                                                        <input type="text" id="imoFilter" class="form-control filter-input" placeholder="type here">
                                                    </x-lists.filter-field>
                                                    <x-lists.filter-field label="Type" width="150px">
                                                        <select id="typeFilter" class="form-control filter-input select2">
                                                            <option value="">Click here</option>
                                                            @foreach($vesselTypes as $type)
                                                                <option value="{{ $type }}">{{ $type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </x-lists.filter-field>
                                                    <x-lists.clear-filters id="clear-vessel-filters" />
                                                </x-lists.filter-bar>

                                                <div class="dt-responsive">
                                                    <x-lists.ajax-table
                                                        table-id="vessels-table"
                                                        table-class="table-other-companies"
                                                        pagination-id="vessels-pagination"
                                                        :paginator="$vessels->links()"
                                                        min-width="720px"
                                                    >
                                                        <x-slot:head>
                                                            <tr>
                                                                <th style="width: 25%;">Vessel name</th>
                                                                <th style="width: 15%;">IMO</th>
                                                                <th style="width: 15%;">Type</th>
                                                                <th style="width: 45%;">Connected customers</th>
                                                            </tr>
                                                        </x-slot:head>
                                                        @include('Vessels.partials.rows')
                                                    </x-lists.ajax-table>
                                                </div>
                                            </div>
                                        </div>
    @include('layouts.partials.pcoded-shell-end')


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: '100%'
            });

            function fixVesselsFilterSelect2Width() {
                $('.filter-row .select2-container').css('width', '100%');
            }

            var table = $('#vessels-table').DataTable({
                "dom": 'rt',
                "paging": false,
                "info": false,
                "lengthChange": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "scrollX": true
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
