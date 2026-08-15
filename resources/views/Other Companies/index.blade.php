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
    <style>
        /* Filter Bar Styling */
        .filter-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            padding: 10px 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
            flex-wrap: nowrap;
            overflow-x: auto;
        }
        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .filter-label-custom {
            font-size: 11px;
            color: #666;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .filter-input-custom {
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 4px 8px;
            font-size: 12px;
        }
        .input-group-custom {
            display: flex;
        }
        .btn-input-append {
            height: 30px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 8px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 12px;
        }
        .filter-input-custom.has-append {
            border-radius: 2px 0 0 2px;
        }

        /* Select2 filter dropdowns - match text inputs */
        .filter-row .select2-container--default .select2-selection--single {
            height: 30px !important;
            min-height: 30px !important;
            font-size: 12px !important;
            background-color: #fff !important;
            background: #fff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            color: #333 !important;
            background-color: transparent !important;
            background: transparent !important;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
            font-style: italic;
        }

        .filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 1px !important;
            background: transparent !important;
        }

        .filter-row .select2-container--default .select2-results__option--highlighted,
        .filter-row .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #f3f4f6 !important;
            color: #333 !important;
        }

        .filter-checkbox-group input[type="checkbox"] {
            width: 14px;
            height: 14px;
            cursor: pointer;
            accent-color: #1b5e6f;
        }

        .filter-checkbox-group {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 5px;
            white-space: nowrap;
        }
        .filter-checkbox-group label {
            font-size: 12px;
            color: #333;
            margin-bottom: 0;
            cursor: pointer;
        }
        .btn-clear-filters {
            font-size: 12px;
            color: #01a9ac;
            text-decoration: none;
            margin-bottom: 5px;
            white-space: nowrap;
        }
        .btn-add-other {
            height: 30px;
            padding: 0 15px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            border-radius: 3px;
            font-size: 12px;
            margin-left: auto;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }

        /* Table Styling */
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

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover,
        .btn-outline-teal.is-open {
            background-color: #008080;
            color: #fff;
            border-color: #008080;
        }

        .other-companies-filters-toolbar {
            display: none;
        }

        .other-companies-add-mobile {
            display: none;
        }

        .table-other-companies {
            min-width: 0;
        }

        .table-scroll-wrapper .table-other-companies,
        .dataTables_wrapper .table-other-companies,
        .table-responsive .table-other-companies {
            min-width: 900px;
        }

        @media (max-width: 991.98px) {
            .other-companies-filters-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                padding: 8px 12px;
                background: #fff;
                border-bottom: 1px solid #eee;
            }

            .other-companies-filters-toolbar-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

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

            .filter-row {
                display: none !important;
                flex-direction: column;
                align-items: stretch;
                flex-wrap: nowrap;
                gap: 10px;
                max-height: 42vh;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding: 10px 12px 12px;
            }

            body.other-companies-filters-open .filter-row {
                display: flex !important;
            }

            #btn-other-companies-filters-toggle.is-open {
                background: #008080 !important;
                color: #fff !important;
            }

            .filter-item,
            .filter-item[style*="width"] {
                width: 100% !important;
                max-width: 100% !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 4px;
            }

            .filter-label-custom {
                white-space: normal;
                line-height: 1.3;
            }

            .filter-input-custom,
            .filter-input-custom[style*="width"],
            .filter-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .input-group-custom {
                width: 100%;
            }

            .input-group-custom .filter-input-custom {
                flex: 1;
                min-width: 0;
            }

            .filter-checkbox-group,
            .btn-clear-filters {
                margin-left: 0 !important;
                margin-bottom: 0;
            }

            .btn-add-other,
            .other-companies-add-desktop {
                display: none !important;
            }

            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
                padding: 8px 12px 0;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                display: block;
                margin-top: 4px;
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

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (min-width: 992px) {
            .other-companies-filters-toolbar {
                display: none !important;
            }
            .filter-row {
                display: flex !important;
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
                                            <div class="other-companies-filters-toolbar">
                                                <button type="button" id="btn-other-companies-filters-toggle" class="btn btn-outline-teal btn-sm">
                                                    <i class="ti-filter"></i> <span class="other-companies-filters-toggle-label">Show filters</span>
                                                </button>
                                                <div class="other-companies-filters-toolbar-actions">
                                                    <a class="other-companies-add-mobile" href="{{ route('other-companies.create') }}">Add other company</a>
                                                </div>
                                            </div>
                                            <!-- Filter Bar -->
                                            <div class="filter-row">
                                                <div class="filter-item" style="width: 250px;">
                                                    <label class="filter-label-custom">Name</label>
                                                    <div class="input-group-custom">
                                                        <input type="text" id="filter-company-name" class="filter-input-custom has-append" placeholder="type here">
                                                        <button class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                    </div>
                                                </div>
                                                <div class="filter-item" style="width: 100px;">
                                                    <label class="filter-label-custom">Code</label>
                                                    <input type="text" id="filter-company-code" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 220px;">
                                                    <label class="filter-label-custom">Address</label>
                                                    <input type="text" id="filter-company-address" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 120px;">
                                                    <label class="filter-label-custom">City</label>
                                                    <input type="text" id="filter-company-city" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 150px;">
                                                    <label class="filter-label-custom">Country</label>
                                                    <select id="filter-company-country" class="filter-input-custom select2">
                                                        <option value=""></option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}">{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="filter-checkbox-group">
                                                    <input type="checkbox" id="filter-hide-inactive" checked>
                                                    <label for="filter-hide-inactive">Hide inactive</label>
                                                </div>
                                                <a href="#" id="clear-company-filters" class="btn-clear-filters">Clear filters</a>
                                                <a href="{{ route('other-companies.create') }}" class="btn-add-other other-companies-add-desktop">Add other company</a>
                                            </div>

                                            <!-- Data Table -->
                                            <div class="table-responsive" style="padding: 10px;">
                                                <table id="other-companies-table" class="table-other-companies">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%;">Name</th>
                                                            <th style="width: 5%;">Code</th>
                                                            <th style="width: 10%;">Type</th>
                                                            <th style="width: 18%;">Address</th>
                                                            <th style="width: 8%;">City</th>
                                                            <th style="width: 12%;">Country</th>
                                                            <th style="width: 10%;">Phone number</th>
                                                            <th style="width: 12%;">Email</th>
                                                            <th style="width: 3%;">Status</th>
                                                            <th style="width: 2%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($companies as $company)
                                                        @php
                                                            $addressSearch = trim(implode(' ', array_filter([
                                                                $company->street_address,
                                                                $company->office_street_address,
                                                                $company->district_state,
                                                                $company->zip_code,
                                                            ])));
                                                            $countryName = $company->country->name ?? '';
                                                        @endphp
                                                        <tr
                                                            data-company-name="{{ $company->company_name }}"
                                                            data-code="{{ $company->code }}"
                                                            data-address="{{ $addressSearch }}"
                                                            data-city="{{ $company->city }}"
                                                            data-country="{{ $countryName }}"
                                                            data-is-inactive="0"
                                                        >
                                                            <td><a href="{{ route('other-companies.edit', $company->id) }}" class="company-link">{{ $company->company_name }}</a></td>
                                                            <td>{{ $company->code }}</td>
                                                            <td>{{ $company->company_type }}</td>
                                                            <td>{{ Str::limit($company->street_address, 25) }}</td>
                                                            <td>{{ $company->city }}</td>
                                                            <td>
                                                                @if($company->country)
                                                                    @if($company->country->flag_url)
                                                                        <img src="{{ $company->country->flag_url }}" class="country-flag" alt="">
                                                                    @endif
                                                                    {{ $countryName }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $company->phone_number }}</td>
                                                            <td>
                                                                @if($company->email)
                                                                    <a href="mailto:{{ $company->email }}" class="company-link">{{ Str::limit($company->email, 28) }}</a>
                                                                @endif
                                                            </td>
                                                            <td><span class="ti-check" style="color: green;"></span></td>
                                                            <td>
                                                                <div class="action-icons">
                                                                    <a href="{{ route('other-companies.edit', $company->id) }}"><i class="ti-pencil"></i></a>
                                                                    <a href="javascript:void(0)" class="delete-other-company" data-id="{{ $company->id }}" data-name="{{ $company->company_name }}" title="Delete company">
                                                                        <i class="ti-trash"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="10" style="text-align:center; padding: 30px; color: #999;">No companies found. <a href="{{ route('other-companies.create') }}">Add one</a>.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
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

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: 'resolve'
            });

            var table = $('#other-companies-table').DataTable({
                "dom": 'ft<"d-flex flex-wrap justify-content-between align-items-center"ip>',
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "orderable": false, "targets": [9] }
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            $('#btn-other-companies-filters-toggle').on('click', function () {
                $('body').toggleClass('other-companies-filters-open');
                var isOpen = $('body').hasClass('other-companies-filters-open');
                $(this).toggleClass('is-open', isOpen);
                $(this).find('.other-companies-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                setTimeout(function () {
                    table.columns.adjust();
                }, 50);
            });

            $(window).on('resize', function () {
                table.columns.adjust();
            });

            setTimeout(function () {
                table.columns.adjust();
            }, 100);

            function rowData($row, key) {
                return String($row.attr('data-' + key) || '');
            }

            function getFilterText(selector) {
                return String($(selector).val() || '').toLowerCase().trim();
            }

            function matchesContains(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '').toLowerCase().indexOf(filterValue) !== -1;
            }

            function matchesExact(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '') === filterValue;
            }

            $('#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city, #filter-company-country, #filter-hide-inactive').on('change keyup', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'other-companies-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);

                if ($('#filter-hide-inactive').is(':checked') && rowData($row, 'is-inactive') === '1') {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-company-name'), rowData($row, 'company-name'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-company-code'), rowData($row, 'code'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-company-address'), rowData($row, 'address'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-company-city'), rowData($row, 'city'))) {
                    return false;
                }

                if (!matchesExact($('#filter-company-country').val(), rowData($row, 'country'))) {
                    return false;
                }

                return true;
            });

            $('#clear-company-filters').on('click', function(e) {
                e.preventDefault();
                $('#filter-company-name, #filter-company-code, #filter-company-address, #filter-company-city').val('');
                $('#filter-company-country').val(null).trigger('change');
                $('#filter-hide-inactive').prop('checked', true);
                table.search('').columns().search('').draw();
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

                                $row.fadeOut(400, function() {
                                    table.row($row).remove().draw(false);
                                });
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
@endsection
