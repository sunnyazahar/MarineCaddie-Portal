@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="other-companies-filters-open" toolbarClass="other-companies-filters-toolbar" />
    <x-lists.multiselect-assets />

    <style>
        body.other-companies-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.other-companies-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.other-companies-list-page .pcoded-inner-content,
        body.other-companies-list-page .main-body,
        body.other-companies-list-page .page-wrapper,
        body.other-companies-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .other-companies-list-card {
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
        .other-companies-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .other-companies-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .other-companies-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .other-companies-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .btn-other-companies-add {
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
        .btn-other-companies-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }
        .other-companies-add-desktop {
            margin-left: auto;
        }

        .other-companies-add-mobile {
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
        .other-companies-add-mobile:hover {
            background: #008080;
            color: #fff;
            text-decoration: none;
        }

        .other-companies-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #other-companies-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1080px;
        }
        #other-companies-table thead th {
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
        #other-companies-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #other-companies-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #other-companies-table tbody tr:last-child td {
            border-bottom: none;
        }

        .oc-name-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .oc-name-link:hover {
            color: #006666;
            text-decoration: underline;
        }
        .oc-email-link {
            color: #0088c7;
            text-decoration: none;
        }
        .oc-email-link:hover {
            text-decoration: underline;
        }

        .oc-country-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
            max-width: 100%;
        }
        .oc-country-flag {
            display: block;
            width: 20px;
            height: 15px;
            flex-shrink: 0;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
        }
        .oc-country-name {
            min-width: 0;
            line-height: 1.3;
        }

        .oc-status-pill {
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

        .oc-action-icons {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }
        .oc-action-btn {
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
        .oc-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #other-companies-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .other-companies-add-mobile {
                display: inline-flex !important;
                align-items: center;
            }
            .other-companies-add-desktop {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('other-companies-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 mx-2 mt-2" role="alert" style="font-size: 12px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card other-companies-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Other companies"
                subtitle="Manage third-party companies used across shipments and billing"
                icon="ti-layout-grid2"
                :count="$companies->total()"
                countLabel="companies"
            />

            <div class="other-companies-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-other-companies-filters-toggle"
                    body-class="other-companies-filters-open"
                    toolbar-class="other-companies-filters-toolbar"
                >
                    <x-slot:actions>
                        @if ($canWriteAdministration)
                            <a class="other-companies-add-mobile" href="{{ route('other-companies.create') }}">Add other company</a>
                        @endif
                    </x-slot:actions>
                </x-lists.filter-toolbar>

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Name" width="180px">
                        <input type="text" id="filter-company-name" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Code" width="100px">
                        <input type="text" id="filter-company-code" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Address" width="200px">
                        <input type="text" id="filter-company-address" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="City" width="130px">
                        <input type="text" id="filter-company-city" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Country" width="180px">
                        <select id="filter-company-country" class="form-control filter-input company-filter-multiselect" multiple="multiple">
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.clear-filters id="clear-company-filters" />
                    @if ($canWriteAdministration)
                        <a href="{{ route('other-companies.create') }}" class="btn-other-companies-add other-companies-add-desktop">
                            <i class="ti-plus"></i> Add other company
                        </a>
                    @endif
                </x-lists.filter-bar>
            </div>

            <div class="other-companies-table-area list-ajax-table-wrapper">
                <table id="other-companies-table">
                    <thead>
                        @include('Other Companies.partials.table-head-row')
                    </thead>
                    <tbody>
                        @include('Other Companies.partials.rows')
                    </tbody>
                </table>
            </div>

            <div id="other-companies-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $companies])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('body').addClass('other-companies-list-page');

            var currentCompanyPage = 1;
            var table = $('#other-companies-table').DataTable({
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
                    { orderable: false, targets: 9 }
                ],
                language: {
                    emptyTable: 'No companies found.'
                }
            });

            window.otherCompaniesListFilters = bindAjaxListFilters({
                tableSelector: '#other-companies-table',
                paginationSelector: '#other-companies-pagination',
                indexUrl: @json(route('other-companies.index')),
                existingTable: table,
                multiselectSelector: '.company-filter-multiselect',
                clearSelector: '#clear-company-filters',
                getParams: function (page) {
                    currentCompanyPage = page || 1;
                    return {
                        name: $.trim($('#filter-company-name').val() || ''),
                        code: $.trim($('#filter-company-code').val() || ''),
                        address: $.trim($('#filter-company-address').val() || ''),
                        city: $.trim($('#filter-company-city').val() || ''),
                        country: $('#filter-company-country').val() || [],
                        page: currentCompanyPage
                    };
                },
                textSelectors: '#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city',
                resetFields: function () {
                    $('#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city').val('');
                    clearSearchableFilterMultiselect('.company-filter-multiselect', false);
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });

            $(document).on('click', '.delete-other-company', function () {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this company';

                swal({
                    title: 'Delete company?',
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
                        url: '{{ url('/other-companies') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Company deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                if (window.otherCompaniesListFilters) {
                                    window.otherCompaniesListFilters.load(currentCompanyPage);
                                }
                            } else {
                                swal('Error', response.message || 'Error deleting company.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the company.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
