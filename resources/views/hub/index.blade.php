@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="hubs-filters-open" toolbarClass="hubs-filters-toolbar" />
    <x-lists.multiselect-assets />

    <style>
        body.hubs-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.hubs-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.hubs-list-page .pcoded-inner-content,
        body.hubs-list-page .main-body,
        body.hubs-list-page .page-wrapper,
        body.hubs-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .hubs-list-card {
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
        .hubs-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .hubs-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .hubs-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .hubs-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .btn-hubs-add {
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
        .btn-hubs-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }
        .hubs-add-desktop {
            margin-left: auto;
        }

        .hubs-add-mobile {
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
        .hubs-add-mobile:hover {
            background: #008080;
            color: #fff;
            text-decoration: none;
        }

        .hubs-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #hubs-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }
        #hubs-table thead th {
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
        #hubs-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #hubs-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #hubs-table tbody tr:last-child td {
            border-bottom: none;
        }

        .hub-name-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .hub-name-link:hover {
            color: #006666;
            text-decoration: underline;
        }
        .hub-email-link {
            color: #0088c7;
            text-decoration: none;
        }
        .hub-email-link:hover {
            text-decoration: underline;
        }

        .hub-country-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
            max-width: 100%;
        }
        .hub-country-flag {
            display: block;
            width: 20px;
            height: 15px;
            flex-shrink: 0;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
        }
        .hub-country-name {
            min-width: 0;
            line-height: 1.3;
        }

        .hub-status-toggle {
            border: 1px solid transparent;
            padding: 4px 10px;
            border-radius: 999px;
            min-width: 72px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            cursor: pointer;
        }
        .hub-status-toggle.is-active {
            color: #065f46;
            background: #ecfdf5;
            border-color: #6ee7b7;
        }
        .hub-status-toggle.is-inactive {
            color: #64748b;
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .hub-status-toggle:hover {
            filter: brightness(0.97);
        }

        .hub-action-icons {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }
        .hub-action-btn {
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
        .hub-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #hubs-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .hubs-add-mobile {
                display: inline-flex !important;
                align-items: center;
            }
            .hubs-add-desktop {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('hubs-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 mx-2 mt-2" role="alert" style="font-size: 12px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card hubs-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Hubs"
                subtitle="Manage warehouse hubs, contact details, and portal visibility"
                icon="ti-location-pin"
                :count="$hubs->total()"
                countLabel="hubs"
            />

            <div class="hubs-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-hubs-filters-toggle"
                    body-class="hubs-filters-open"
                    toolbar-class="hubs-filters-toolbar"
                >
                    <x-slot:actions>
                        @if ($canWriteAdministration)
                            <a class="hubs-add-mobile" href="{{ route('hub.create') }}">Add hub</a>
                        @endif
                    </x-slot:actions>
                </x-lists.filter-toolbar>

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Name" width="180px">
                        <input type="text" id="filter-hub-name" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Code" width="120px">
                        <input type="text" id="filter-hub-code" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Address" width="200px">
                        <input type="text" id="filter-hub-address" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="City" width="150px">
                        <input type="text" id="filter-hub-city" class="form-control filter-input" placeholder="type here">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Country" width="200px">
                        <select id="filter-hub-country" class="form-control filter-input hub-filter-multiselect" multiple="multiple">
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.hide-inactive id="filter-hide-inactive" :checked="true" />
                    <x-lists.clear-filters id="clear-hub-filters" />
                    @if ($canWriteAdministration)
                        <a href="{{ route('hub.create') }}" class="btn-hubs-add hubs-add-desktop">
                            <i class="ti-plus"></i> Add hub
                        </a>
                    @endif
                </x-lists.filter-bar>
            </div>

            <div class="hubs-table-area list-ajax-table-wrapper">
                <table id="hubs-table">
                    <thead>
                        @include('hub.partials.table-head-row')
                    </thead>
                    <tbody>
                        @include('hub.partials.rows')
                    </tbody>
                </table>
            </div>

            <div id="hubs-pagination" class="pagination-sticky-footer">
                @include('partials.list-pagination-footer-inner', ['paginator' => $hubs])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('body').addClass('hubs-list-page');

            var currentHubPage = 1;
            var table = $('#hubs-table').DataTable({
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
                    { orderable: false, targets: 7 }
                ],
                language: {
                    emptyTable: 'No hubs found.'
                }
            });

            var hubsListFiltersApi = bindAjaxListFilters({
                tableSelector: '#hubs-table',
                paginationSelector: '#hubs-pagination',
                indexUrl: @json(route('hub.index')),
                existingTable: table,
                multiselectSelector: '.hub-filter-multiselect',
                clearSelector: '#clear-hub-filters',
                getParams: function (page) {
                    currentHubPage = page || 1;
                    return {
                        name: $.trim($('#filter-hub-name').val() || ''),
                        code: $.trim($('#filter-hub-code').val() || ''),
                        address: $.trim($('#filter-hub-address').val() || ''),
                        city: $.trim($('#filter-hub-city').val() || ''),
                        country: $('#filter-hub-country').val() || [],
                        hide_inactive: $('#filter-hide-inactive').is(':checked') ? 1 : 0,
                        page: currentHubPage
                    };
                },
                textSelectors: '#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city',
                changeSelectors: '#filter-hide-inactive',
                resetFields: function () {
                    $('#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city').val('');
                    clearSearchableFilterMultiselect('.hub-filter-multiselect', false);
                    $('#filter-hide-inactive').prop('checked', true);
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });

            window.hubsListFilters = hubsListFiltersApi;

            $(document).on('click', '.hub-status-toggle', function () {
                var $button = $(this);
                var $row = $button.closest('tr');
                var currentStatus = String($button.data('status') || 'active').toLowerCase();
                var nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
                var nextStatusLabel = nextStatus === 'active' ? 'Active' : 'Inactive';
                var hubName = $button.data('name') || 'this hub';

                swal({
                    title: nextStatus === 'active' ? 'Activate hub?' : 'Deactivate hub?',
                    text: 'Are you sure you want to mark "' + hubName + '" as ' + nextStatusLabel.toLowerCase() + '?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: nextStatus === 'active' ? 'Yes, activate' : 'Yes, deactivate',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $button.prop('disabled', true);

                    $.ajax({
                        url: $button.data('url'),
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: nextStatus
                        },
                        success: function (response) {
                            if (!response.success) {
                                $button.prop('disabled', false);
                                swal('Error', response.message || 'Unable to update hub status.', 'error');
                                return;
                            }

                            $button
                                .data('status', nextStatus)
                                .attr('data-status', nextStatus)
                                .toggleClass('is-active', !response.is_inactive)
                                .toggleClass('is-inactive', response.is_inactive)
                                .text(response.status)
                                .prop('disabled', false);

                            $row.attr('data-is-inactive', response.is_inactive ? '1' : '0');

                            if (response.is_inactive && $('#filter-hide-inactive').is(':checked')) {
                                hubsListFiltersApi.load(currentHubPage);
                            }

                            swal({
                                title: 'Status updated',
                                text: response.message,
                                type: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function (xhr) {
                            $button.prop('disabled', false);
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while updating the hub status.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.delete-hub', function () {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this hub';

                swal({
                    title: 'Delete hub?',
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
                        url: '{{ url('/hubs') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Hub deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                hubsListFiltersApi.load(currentHubPage);
                            } else {
                                swal('Error', response.message || 'Error deleting hub.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the hub.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
