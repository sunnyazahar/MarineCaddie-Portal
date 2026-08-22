@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="customers-filters-open" toolbarClass="customers-filters-toolbar" />
    <x-lists.multiselect-assets />

    <style>
        body.customers-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.customers-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.customers-list-page .pcoded-inner-content,
        body.customers-list-page .main-body,
        body.customers-list-page .page-wrapper,
        body.customers-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .customers-list-card {
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
        .customers-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .customers-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .customers-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .customers-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .btn-customers-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 16px;
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
        .btn-customers-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }
        .customers-add-desktop {
            margin-left: auto;
        }

        .customers-add-mobile {
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
        .customers-add-mobile:hover {
            background: #008080;
            color: #fff;
            text-decoration: none;
        }

        .customers-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #customers-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }
        #customers-table thead th {
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
        #customers-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #customers-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #customers-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cust-name-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .cust-name-link:hover {
            color: #006666;
            text-decoration: underline;
        }

        .cust-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            min-width: 72px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            color: #065f46;
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
        }

        .cust-action-icons {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }
        .cust-action-btn {
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
        .cust-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #customers-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .customers-add-mobile {
                display: inline-flex !important;
                align-items: center;
            }
            .customers-add-desktop {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('customers-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 mx-2 mt-2" role="alert" style="font-size: 12px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card customers-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Customers"
                subtitle="Manage customer accounts, contacts, and responsible office assignments"
                icon="ti-user"
                :count="$customers->total()"
                countLabel="customers"
            />

            <div class="customers-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-customers-filters-toggle"
                    body-class="customers-filters-open"
                    toolbar-class="customers-filters-toolbar"
                >
                    <x-slot:actions>
                        @if ($canWriteAdministration)
                            <a class="customers-add-mobile" href="{{ route('customers.create') }}">Add customer</a>
                        @endif
                    </x-slot:actions>
                </x-lists.filter-toolbar>

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Search" width="200px">
                        <input type="text" id="filter-customer-search" class="form-control filter-input" placeholder="Name, number, email…">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Office" width="180px">
                        <select id="filter-responsible-office" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                            @foreach ($responsibleOffices as $office)
                                <option value="{{ $office }}">{{ $office }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Account manager" width="180px">
                        <select id="filter-account-manager" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                            @foreach ($accountManagers as $manager)
                                <option value="{{ $manager }}">{{ $manager }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Sales manager" width="180px">
                        <select id="filter-sales-manager" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                            @foreach ($salesManagers as $manager)
                                <option value="{{ $manager }}">{{ $manager }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Country" width="180px">
                        <select id="filter-customer-country" class="form-control filter-input customer-filter-multiselect" multiple="multiple">
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.clear-filters id="clear-customer-filters" />
                    @if ($canWriteAdministration)
                        <a href="{{ route('customers.create') }}" class="btn-customers-add customers-add-desktop">
                            <i class="ti-plus"></i> Add customer
                        </a>
                    @endif
                </x-lists.filter-bar>
            </div>

            <div class="customers-table-area list-ajax-table-wrapper">
                <table id="customers-table">
                    <thead>
                        @include('customers.partials.table-head-row')
                    </thead>
                    <tbody>
                        @include('customers.partials.rows')
                    </tbody>
                </table>
            </div>

            <div id="customers-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $customers])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('body').addClass('customers-list-page');

            var currentCustomerPage = 1;
            var table = $('#customers-table').DataTable({
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
                    emptyTable: 'No customers found.'
                }
            });

            window.customersListFilters = bindAjaxListFilters({
                tableSelector: '#customers-table',
                paginationSelector: '#customers-pagination',
                indexUrl: @json(route('customers.index')),
                existingTable: table,
                multiselectSelector: '.customer-filter-multiselect',
                clearSelector: '#clear-customer-filters',
                getParams: function (page) {
                    currentCustomerPage = page || 1;
                    return {
                        search: $.trim($('#filter-customer-search').val() || ''),
                        responsible_office: $('#filter-responsible-office').val() || [],
                        account_manager: $('#filter-account-manager').val() || [],
                        sales_manager: $('#filter-sales-manager').val() || [],
                        country: $('#filter-customer-country').val() || [],
                        page: currentCustomerPage
                    };
                },
                textSelectors: '#filter-customer-search',
                resetFields: function () {
                    $('#filter-customer-search').val('');
                    clearSearchableFilterMultiselect('.customer-filter-multiselect', false);
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });
        });
    </script>
@endpush
