@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="suppliers-filters-open" toolbarClass="suppliers-filters-toolbar" />

    <style>
        body.suppliers-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.suppliers-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.suppliers-list-page .pcoded-inner-content,
        body.suppliers-list-page .main-body,
        body.suppliers-list-page .page-wrapper,
        body.suppliers-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .suppliers-list-card {
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
        .suppliers-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .suppliers-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .suppliers-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .suppliers-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .btn-suppliers-add {
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
        .btn-suppliers-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }
        .suppliers-add-desktop {
            margin-left: auto;
        }

        .suppliers-add-mobile {
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
        .suppliers-add-mobile:hover {
            background: #008080;
            color: #fff;
            text-decoration: none;
        }

        .suppliers-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #suppliers-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }
        #suppliers-table thead th {
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
        #suppliers-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #suppliers-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #suppliers-table tbody tr:last-child td {
            border-bottom: none;
        }

        .sup-name-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .sup-name-link:hover {
            color: #006666;
            text-decoration: underline;
        }
        .sup-email-link {
            color: #0088c7;
            text-decoration: none;
        }
        .sup-email-link:hover {
            text-decoration: underline;
        }

        .sup-country-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
            max-width: 100%;
        }
        .sup-country-flag {
            display: block;
            width: 20px;
            height: 15px;
            flex-shrink: 0;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
        }
        .sup-country-name {
            min-width: 0;
            line-height: 1.3;
        }

        .sup-action-icons {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }
        .sup-action-btn {
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
        .sup-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #suppliers-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .suppliers-add-mobile {
                display: inline-flex !important;
                align-items: center;
            }
            .suppliers-add-desktop {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('suppliers-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 mx-2 mt-2" role="alert" style="font-size: 12px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card suppliers-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Suppliers"
                subtitle="Manage supplier companies used across stock and shipment workflows"
                icon="ti-truck"
                :count="$suppliers->total()"
                countLabel="suppliers"
            />

            <div class="suppliers-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-suppliers-filters-toggle"
                    body-class="suppliers-filters-open"
                    toolbar-class="suppliers-filters-toolbar"
                >
                    <x-slot:actions>
                        @if ($canWriteAdministration)
                            <a class="suppliers-add-mobile" href="{{ route('suppliers.create') }}">Add supplier</a>
                        @endif
                    </x-slot:actions>
                </x-lists.filter-toolbar>

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Search" width="240px">
                        <input type="text" id="supplier-search-filter" class="form-control filter-input" placeholder="Name, address, city, country, email…">
                    </x-lists.filter-field>
                    <x-lists.clear-filters id="clear-supplier-filters" />
                    @if ($canWriteAdministration)
                        <a href="{{ route('suppliers.create') }}" class="btn-suppliers-add suppliers-add-desktop">
                            <i class="ti-plus"></i> Add supplier
                        </a>
                    @endif
                </x-lists.filter-bar>
            </div>

            <div class="suppliers-table-area list-ajax-table-wrapper">
                <table id="suppliers-table">
                    <thead>
                        @include('Suppliers.partials.table-head-row')
                    </thead>
                    <tbody>
                        @include('Suppliers.partials.rows')
                    </tbody>
                </table>
            </div>

            <div id="suppliers-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $suppliers])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    @include('partials.searchable-filter-multiselect-script')

    <script>
        $(document).ready(function () {
            $('body').addClass('suppliers-list-page');

            var currentSupplierPage = 1;
            var table = $('#suppliers-table').DataTable({
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
                    { orderable: false, targets: 6 }
                ],
                language: {
                    emptyTable: 'No suppliers found.'
                }
            });

            window.suppliersListFilters = bindAjaxListFilters({
                tableSelector: '#suppliers-table',
                paginationSelector: '#suppliers-pagination',
                indexUrl: @json(route('suppliers.index')),
                existingTable: table,
                clearSelector: '#clear-supplier-filters',
                getParams: function (page) {
                    currentSupplierPage = page || 1;
                    return {
                        search: $.trim($('#supplier-search-filter').val() || ''),
                        page: currentSupplierPage
                    };
                },
                textSelectors: '#supplier-search-filter',
                resetFields: function () {
                    $('#supplier-search-filter').val('');
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });

            $(document).on('click', '.delete-supplier', function () {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this supplier';

                swal({
                    title: 'Delete supplier?',
                    text: 'Are you sure you want to delete "' + name + '"? This can be restored later.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/Suppliers') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Supplier deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                if (window.suppliersListFilters) {
                                    window.suppliersListFilters.load(currentSupplierPage);
                                }
                            } else {
                                swal('Error', response.message || 'Error deleting supplier.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the supplier.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
