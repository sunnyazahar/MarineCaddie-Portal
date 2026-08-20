@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <x-lists.base-styles bodyClass="other-companies-filters-open" toolbarClass="other-companies-filters-toolbar" />
    <x-lists.multiselect-assets />
    <style>
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
        }
        .table-other-companies th {
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            background: #f8fafd;
        }
        .table-other-companies td {
            padding: 8px 10px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .table-other-companies tr:hover td {
            background-color: #f9fafb;
        }
        .company-link {
            color: #01a9ac;
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

        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        .other-companies-add-mobile {
            display: none;
        }

        .other-companies-add-desktop {
            height: 30px;
            padding: 0 15px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            border-radius: 3px;
            font-size: 12px;
            margin-left: auto;
            display: flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }

        .table-other-companies {
            min-width: 0;
        }

        .list-ajax-table-wrapper .table-other-companies,
        .dataTables_wrapper .table-other-companies {
            min-width: 900px;
        }

        @media (max-width: 991.98px) {
            .other-companies-add-mobile {
                display: inline-flex !important;
                align-items: center;
                font-size: 11px;
                padding: 6px 12px;
                border-radius: 2px;
                background: #fff;
                color: #1b5e6f;
                border: 1px solid #1b5e6f;
                font-weight: 600;
                text-decoration: none;
                white-space: nowrap;
            }

            .other-companies-add-desktop {
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
                                        <div class="card">
                                            <x-lists.filter-toolbar
                                                toggle-id="btn-other-companies-filters-toggle"
                                                body-class="other-companies-filters-open"
                                                toolbar-class="other-companies-filters-toolbar"
                                            >
                                                <x-slot:actions>
                                                    <a class="other-companies-add-mobile" href="{{ route('other-companies.create') }}">Add other company</a>
                                                </x-slot:actions>
                                            </x-lists.filter-toolbar>

                                            <x-lists.filter-bar>
                                                <x-lists.filter-field label="Name" width="250px">
                                                    <input type="text" id="filter-company-name" class="form-control filter-input" placeholder="type here">
                                                </x-lists.filter-field>
                                                <x-lists.filter-field label="Code" width="100px">
                                                    <input type="text" id="filter-company-code" class="form-control filter-input" placeholder="type here">
                                                </x-lists.filter-field>
                                                <x-lists.filter-field label="Address" width="220px">
                                                    <input type="text" id="filter-company-address" class="form-control filter-input" placeholder="type here">
                                                </x-lists.filter-field>
                                                <x-lists.filter-field label="City" width="120px">
                                                    <input type="text" id="filter-company-city" class="form-control filter-input" placeholder="type here">
                                                </x-lists.filter-field>
                                                <x-lists.filter-field label="Country" width="150px">
                                                    <select id="filter-company-country" class="form-control filter-input company-filter-multiselect" multiple="multiple">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}">{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </x-lists.filter-field>
                                                <x-lists.hide-inactive id="filter-hide-inactive" :checked="true" />
                                                <x-lists.clear-filters id="clear-company-filters" />
                                                <a href="{{ route('other-companies.create') }}" class="other-companies-add-desktop">Add other company</a>
                                            </x-lists.filter-bar>

                                            <x-lists.ajax-table
                                                table-id="other-companies-table"
                                                table-class="table-other-companies"
                                                pagination-id="other-companies-pagination"
                                                :paginator="$companies->links()"
                                                min-width="900px"
                                            >
                                                <x-slot:head>
                                                    @include('Other Companies.partials.table-head-row')
                                                </x-slot:head>
                                                @include('Other Companies.partials.rows')
                                            </x-lists.ajax-table>
                                        </div>
    @include('layouts.partials.pcoded-shell-end')
     <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

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
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#other-companies-table').DataTable({
                "dom": 'rt',
                "paging": false,
                "info": false,
                "lengthChange": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "orderable": false, "targets": 9 }
                ]
            });

            $(window).on('resize', function () {
                table.columns.adjust();
            });

            setTimeout(function () {
                table.columns.adjust();
            }, 100);

            window.otherCompaniesListFilters = bindAjaxListFilters({
                tableSelector: '#other-companies-table',
                paginationSelector: '#other-companies-pagination',
                indexUrl: @json(route('other-companies.index')),
                existingTable: table,
                multiselectSelector: '.company-filter-multiselect',
                clearSelector: '#clear-company-filters',
                getParams: function (page) {
                    return {
                        name: $.trim($('#filter-company-name').val() || ''),
                        code: $.trim($('#filter-company-code').val() || ''),
                        address: $.trim($('#filter-company-address').val() || ''),
                        city: $.trim($('#filter-company-city').val() || ''),
                        country: $('#filter-company-country').val() || [],
                        page: page || 1
                    };
                },
                textSelectors: '#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city',
                resetFields: function () {
                    $('#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city').val('');
                    clearSearchableFilterMultiselect('.company-filter-multiselect', false);
                    $('#filter-hide-inactive').prop('checked', true);
                },
                resetClickScope: '.filter-item',
                afterDraw: function () {
                    table.columns.adjust();
                }
            });

            $(document).on('click', '.delete-other-company', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this company';
                var $row = $(this).closest('tr');

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
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/other-companies') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Company deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                if (window.otherCompaniesListFilters) {
                                    window.otherCompaniesListFilters.load(1);
                                }
                            } else {
                                swal('Error', response.message || 'Error deleting company.', 'error');
                            }
                        },
                        error: function(xhr) {
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
