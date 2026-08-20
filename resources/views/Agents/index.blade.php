@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet"
        href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <x-lists.base-styles bodyClass="agents-filters-open" toolbarClass="agents-filters-toolbar" />
    <x-lists.multiselect-assets />
    <style>
        /* Agent-specific list styling */
        .table-agents {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .table-agents th {
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            background: #f8fafd;
            /* Consistent light blue-grey header */
        }

        .table-agents td {
            padding: 6px 10px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            vertical-align: middle;
            background: #fff;
        }

        .table-agents tr:hover td {
            background-color: #f5f7f9 !important;
        }

        .table-agents tr.selected td {
            background-color: #e2e8f0 !important;
        }

        .agent-link {
            color: #016699;
            text-decoration: none;
        }

        .agent-link:hover {
            text-decoration: underline;
        }

        .agent-status-toggle {
            border: 1px solid transparent;
            padding: 3px 10px;
            border-radius: 12px;
            min-width: 66px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            text-align: center;
            cursor: pointer;
        }

        .agent-status-toggle.is-active {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }

        .agent-status-toggle.is-inactive {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .agent-status-toggle:hover {
            filter: brightness(0.97);
        }

        .action-icons {
            display: flex;
            gap: 8px;
            color: #ccc;
            justify-content: flex-end;
        }

        .action-icons i {
            cursor: pointer;
            font-size: 14px;
        }

        .action-icons i:hover {
            color: #666;
        }

        .company-link {
            color: #016699;
            text-decoration: none;
        }

        .company-link:hover {
            text-decoration: underline;
        }

        .country-flag {
            width: 18px;
            margin-right: 6px;
            vertical-align: text-top;
        }

        .action-icons {
            display: flex;
            gap: 10px;
            color: #ccc;
            justify-content: center;
        }

        .action-icons i {
            cursor: pointer;
            font-size: 14px;
        }

        .action-icons i:hover {
            color: #666;
        }

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 5px !important;
            background: #f6f7fb;
            /* Page background color */
        }

        .main-body .page-wrapper {
            padding: 5px !important;
            background: #f6f7fb;
        }

        .table-agents {
            min-width: 900px;
        }

        .agents-add-desktop {
            font-size: 11px;
            padding: 6px 15px;
            border-radius: 2px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            font-weight: 600;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .agents-add-desktop {
                display: none !important;
            }

            .dataTables_wrapper .dataTables_filter {
                display: none !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                padding-top: 8px;
            }

            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
            }

            .table-agents th:first-child,
            .table-agents td:first-child {
                padding-left: 12px;
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
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            @include('layouts.top-menu')
            @include('layouts.left-menu')
            <!-- Page-body start -->
            <br>
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- Main-body start -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- Page-header start -->
                            <div class="page-header">

                            </div>
                            <!-- Page-header end -->

                            <!-- Page-body start -->
                            <div class="page-body">
                                <x-lists.filter-toolbar
                                    toggle-id="btn-agents-filters-toggle"
                                    body-class="agents-filters-open"
                                    toolbar-class="agents-filters-toolbar"
                                >
                                    <x-slot:actions>
                                        @if($canWriteAdministration)
                                            <a class="btn btn-sm agents-add-mobile" href="{{ route('agents.create') }}"
                                                style="font-size: 11px; padding: 6px 12px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                Add Agent
                                            </a>
                                        @endif
                                    </x-slot:actions>
                                </x-lists.filter-toolbar>

                                <x-lists.filter-bar>
                                    <x-lists.filter-field label="Name" width="180px">
                                        <input type="text" id="filter-agent-name" class="form-control filter-input" placeholder="type here">
                                    </x-lists.filter-field>
                                    <x-lists.filter-field label="Code" width="150px">
                                        <input type="text" id="filter-agent-code" class="form-control filter-input" placeholder="type here">
                                    </x-lists.filter-field>
                                    <x-lists.filter-field label="Address" width="200px">
                                        <input type="text" id="filter-agent-address" class="form-control filter-input" placeholder="type here">
                                    </x-lists.filter-field>
                                    <x-lists.filter-field label="City" width="180px">
                                        <input type="text" id="filter-agent-city" class="form-control filter-input" placeholder="type here">
                                    </x-lists.filter-field>
                                    <x-lists.filter-field label="Country" width="200px">
                                        <select id="filter-agent-country" class="form-control filter-input agent-filter-multiselect" multiple="multiple">
                                            @foreach ($countries as $country)
                                                <option value="{{ $country }}">{{ $country }}</option>
                                            @endforeach
                                        </select>
                                    </x-lists.filter-field>
                                    <x-lists.filter-field label="Type" width="180px">
                                        <select id="filter-agent-type" class="form-control filter-input agent-filter-multiselect" multiple="multiple">
                                            @foreach ($agentTypes as $type)
                                                <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                            @endforeach
                                        </select>
                                    </x-lists.filter-field>
                                    <x-lists.hide-inactive id="hide-inactive-check" />
                                    <x-lists.clear-filters id="clear-agent-filters" />
                                    @if($canWriteAdministration)
                                        <a class="agents-add-desktop" href="{{ route('agents.create') }}">
                                            Add Agent
                                        </a>
                                    @endif
                                </x-lists.filter-bar>

                                <x-lists.ajax-table
                                    table-id="agents-table"
                                    wrapper-id="agents-table-wrapper"
                                    table-class="table-agents"
                                    pagination-id="agents-pagination"
                                    :paginator="$agents->links()"
                                    min-width="900px"
                                    head-template-id="agents-table-thead-template"
                                >
                                    <x-slot:head>
                                        @include('Agents.partials.table-head-row')
                                    </x-slot:head>
                                    <x-slot:headTemplate>
                                        @include('Agents.partials.table-head-row')
                                    </x-slot:headTemplate>
                                    @include('Agents.partials.rows')
                                </x-lists.ajax-table>
                            </div>
                            <!-- Page-body end -->
                        </div>
                    </div>
                    <div id="styleSelector">

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script
        src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{--
    <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // ── Standard bindAjaxListFilters pattern ──────────────────────────
            window.agentsListFilters = bindAjaxListFilters({
                tableSelector:      '#agents-table',
                paginationSelector: '#agents-pagination',
                indexUrl:           @json(route('agents.index')),
                multiselectSelector: '.agent-filter-multiselect',
                clearSelector:      '#clear-agent-filters',
                getParams: function (page) {
                    return {
                        name:          $.trim($('#filter-agent-name').val() || ''),
                        code:          $.trim($('#filter-agent-code').val() || ''),
                        address:       $.trim($('#filter-agent-address').val() || ''),
                        city:          $.trim($('#filter-agent-city').val() || ''),
                        country:       $('#filter-agent-country').val() || [],
                        type:          $('#filter-agent-type').val() || [],
                        hide_inactive: $('#hide-inactive-check').is(':checked') ? 1 : 0,
                        page:          page || 1
                    };
                },
                textSelectors:   '#filter-agent-name, #filter-agent-code, #filter-agent-address, #filter-agent-city',
                changeSelectors: '#hide-inactive-check',
                resetFields: function () {
                    $('#filter-agent-name, #filter-agent-code, #filter-agent-address, #filter-agent-city').val('');
                    clearSearchableFilterMultiselect('.agent-filter-multiselect', false);
                    $('#hide-inactive-check').prop('checked', false);
                },
                resetClickScope: '.filter-item'
            });

            $(document).on('click', '.agent-status-toggle', function () {
                var $button = $(this);
                var $row = $button.closest('tr');
                var currentStatus = String($button.data('status') || 'active').toLowerCase();
                var nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
                var nextStatusLabel = nextStatus === 'active' ? 'Active' : 'Inactive';
                var agentName = $button.data('name') || 'this agent';

                swal({
                    title: nextStatus === 'active' ? 'Activate agent?' : 'Deactivate agent?',
                    text: 'Are you sure you want to mark "' + agentName + '" as ' + nextStatusLabel.toLowerCase() + '?',
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
                                swal('Error', response.message || 'Unable to update agent status.', 'error');
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
                                : 'An error occurred while updating the agent status.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.delete-agent', function () {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this agent';
                var $row = $(this).closest('tr');

                swal({
                    title: 'Delete agent?',
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
                        url: '{{ url('/Agents') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Agent deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $row.fadeOut(400, function () {
                                    $row.remove();
                                });
                            } else {
                                swal('Error', response.message || 'Error deleting agent.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the agent.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush