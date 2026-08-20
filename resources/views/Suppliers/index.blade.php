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
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
    <x-lists.base-styles />
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
        .filter-input {
            height: 28px;
            font-size: 11px;
            border-radius: 2px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            font-size: 11px !important;
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            color: #495057 !important;
            background-color: transparent !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            top: 1px !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Custom Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
            margin-left: 2px !important;
            border: 1px solid #eee !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            background: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 11px !important;
            color: #666 !important;
            padding-top: 10px !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 10px !important;
        }

        .table-other-companies {
            min-width: 0;
        }

        .dt-responsive .table-other-companies,
        .dataTables_wrapper .table-other-companies {
            min-width: 900px;
        }

        /* Hide DataTables scrollX cloned header inside scroll body (prevents double header) */
        #suppliers-table_wrapper .dataTables_scrollBody > table > thead,
        #suppliers-table_wrapper .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        #suppliers-table_wrapper .dataTables_scrollBody thead tr,
        #suppliers-table_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
            background: transparent !important;
        }
        #suppliers-table_wrapper .dataTables_scrollBody thead th:before,
        #suppliers-table_wrapper .dataTables_scrollBody thead th:after {
            display: none !important;
            content: none !important;
        }

        @media (max-width: 991.98px) {
            .filter-input {
                width: 100%;
            }

            .card-block {
                padding: 12px !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                padding-top: 8px !important;
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
                                                <x-lists.inline-toolbar toolbarClass="suppliers-toolbar">
                                                    <x-slot:search>
                                                        <span class="filter-label" style="font-size: 10px; font-weight: 600; margin-bottom: 2px; display: block;">Search</span>
                                                        <input type="text" id="supplier-search-filter" class="form-control filter-input" placeholder="type here">
                                                    </x-slot:search>
                                                    <x-slot:actions>
                                                        <a href="#" style="border: 1px solid #ced4da; padding: 4px 10px; border-radius: 2px; color: #666; font-size: 14px;">
                                                            <i class="ti-download"></i>
                                                        </a>
                                                        <a class="btn btn-primary" href="{{ route('suppliers.create') }}"
                                                           style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                           Add supplier
                                                        </a>
                                                    </x-slot:actions>
                                                </x-lists.inline-toolbar>

                                                <div class="dt-responsive">
                                                    <x-lists.ajax-table
                                                        table-id="suppliers-table"
                                                        table-class="table-other-companies"
                                                        pagination-id="suppliers-pagination"
                                                        :paginator="$suppliers->links()"
                                                    >
                                                        <x-slot:head>
                                                            <tr>
                                                                <th>Supplier name</th>
                                                                <th>Address</th>
                                                                <th>City</th>
                                                                <th>Country</th>
                                                                <th>Phone number</th>
                                                                <th>Email</th>
                                                                <th style="width: 50px;"></th>
                                                            </tr>
                                                        </x-slot:head>
                                                        @include('Suppliers.partials.rows')
                                                    </x-lists.ajax-table>
                                                </div>
                                            </div>
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
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>
@endsection

@push('scripts')
    @include('partials.searchable-filter-multiselect-script')

    <script>
        $(document).ready(function() {
            var table = $('#suppliers-table').DataTable({
                "lengthChange": false,
                "paging": false,
                "info": false,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "dom": 'rt'
            });

            $(window).on('resize', function () {
                table.columns.adjust();
            });

            setTimeout(function () {
                table.columns.adjust();
            }, 100);

            window.suppliersListFilters = bindAjaxListFilters({
                tableSelector: '#suppliers-table',
                paginationSelector: '#suppliers-pagination',
                indexUrl: @json(route('suppliers.index')),
                existingTable: table,
                getParams: function (page) {
                    return {
                        search: $.trim($('#supplier-search-filter').val() || ''),
                        page: page || 1
                    };
                },
                textSelectors: '#supplier-search-filter',
                resetFields: function () {
                    $('#supplier-search-filter').val('');
                },
                afterDraw: function () {
                    table.columns.adjust();
                }
            });

            // Delete supplier AJAX
            $(document).on('click', '.delete-supplier', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this supplier';
                var $row = $(this).closest('tr');

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
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/Suppliers') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Supplier deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                if (window.suppliersListFilters) {
                                    window.suppliersListFilters.load(1);
                                }
                            } else {
                                swal('Error', response.message || 'Error deleting supplier.', 'error');
                            }
                        },
                        error: function(xhr) {
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
