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
            font-size: 11px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .table-other-companies tr:hover td {
            background-color: #f9fafb;
        }
        .hub-status-toggle {
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
        .hub-status-toggle.is-active {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }
        .hub-status-toggle.is-inactive {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }
        .hub-status-toggle:hover {
            filter: brightness(0.97);
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }
        .filter-label {
            font-size: 10px;
            
            margin-bottom: 2px;
            display: block;
            font-weight: 600;
        }
        .filter-input {
            height: 28px;
            font-size: 11px;
            border-radius: 2px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }
        .clear-filters {
            font-size: 11px;
            color: #3b82f6;
            text-decoration: none;
            cursor: pointer;
        }
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #eef2f7 !important;
            padding: 10px 15px !important;
        }
        .table tbody td {
            padding: 10px 15px !important;
            border-top: 1px solid #f8fafd !important;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-row {
            margin-right: -10px;
            margin-left: -10px;
        }
        .custom-col {
            padding-right: 10px;
            padding-left: 10px;
            flex: 0 0 11.5%;
            max-width: 11.5%;
        }
        @media (max-width: 992px) {
            .custom-col {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
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
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
        .country-flag {
            width: 18px;
            margin-right: 6px;
            vertical-align: middle;
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
                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <!-- Hub Index Design start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="d-flex align-items-center pt-2 pb-2" style="border-bottom: 2px solid #eef2f7; margin-bottom: 10px; padding-left: 10px; gap: 15px;">
                                                    <div class="form-group mb-0" style="width: 150px;">
                                                        <span class="filter-label">Name</span>
                                                        <input type="text" id="filter-hub-name" class="form-control filter-input" placeholder="type here">
                                                    </div>
                                                    
                                                    <div class="form-group mb-0" style="width: 120px;">
                                                        <span class="filter-label">Code</span>
                                                        <input type="text" id="filter-hub-code" class="form-control filter-input" placeholder="type here">
                                                    </div>

                                                    <div class="form-group mb-0" style="width: 150px;">
                                                        <span class="filter-label">Address</span>
                                                        <div style="position: relative;">
                                                            <input type="text" id="filter-hub-address" class="form-control filter-input" placeholder="type here" style="padding-right: 30px;">
                                                            <div style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                                                <i class="ti-more-alt" style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-0" style="width: 150px;">
                                                        <span class="filter-label">City</span>
                                                        <input type="text" id="filter-hub-city" class="form-control filter-input" placeholder="type here">
                                                    </div>

                                                    <div class="form-group mb-0" style="width: 150px;">
                                                        <span class="filter-label">Country</span>
                                                        <select id="filter-hub-country" class="form-control filter-input select2">
                                                            <option value=""></option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country }}">{{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="d-flex align-items-center" style="margin-top: 18px; border: 1px solid #ced4da; padding: 4px 8px; border-radius: 2px; height: 30px;">
                                                        <span style="font-size: 11px; margin-right: 8px; ">Hide inactive</span>
                                                        <input type="checkbox" id="filter-hide-inactive" checked style="width: 14px; height: 14px;">
                                                    </div>

                                                    <a href="#" id="clear-hub-filters" class="clear-filters" style="margin-top: 18px;">Clear filters</a>

                                                    <div class="d-flex align-items-center ml-auto" style="gap: 8px; margin-top: 18px;">
                                                        <a href="#" style="border: 1px solid #ced4da; padding: 4px 10px; border-radius: 2px; color: #666; font-size: 14px;">
                                                            <i class="ti-download"></i>
                                                        </a>
                                                        <a class="btn btn-primary" href="{{ route('hub.create') }}" 
                                                           style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                           Add hub
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="dt-responsive">
                                                    <table id="offices-table" class="table-other-companies">
                                                        <thead style="background: #fdfdfd;">
                                                            <tr>
                                                                <th style=" font-weight: 600;">Hub name</th>
                                                                <th style=" font-weight: 600;">Code</th>
                                                                <th style=" font-weight: 600;">City</th>
                                                                <th style=" font-weight: 600;">Country</th>
                                                                <th style=" font-weight: 600;">Phone number</th>
                                                                <th style=" font-weight: 600;">E-mail</th>
                                                                <th style=" font-weight: 600;">Status</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($hubs as $hub)
                                                                @php
                                                                    $addressSearch = trim(implode(' ', array_filter([
                                                                        $hub->hub_address,
                                                                        $hub->office_address,
                                                                        $hub->district_state,
                                                                        $hub->zip_code,
                                                                    ])));
                                                                    $isInactive = (bool) $hub->hide_in_portal;
                                                                @endphp
                                                                <tr
                                                                    data-hub-name="{{ $hub->hub_name }}"
                                                                    data-code="{{ $hub->code }}"
                                                                    data-address="{{ $addressSearch }}"
                                                                    data-city="{{ $hub->city }}"
                                                                    data-country="{{ $hub->country }}"
                                                                    data-is-inactive="{{ $isInactive ? '1' : '0' }}"
                                                                >
                                                                    <td><a href="{{ route('hub.show', $hub->id) }}" style="color: #3b82f6;">{{ $hub->hub_name }}</a></td>
                                                                    <td>{{ $hub->code }}</td>
                                                                    <td>{{ $hub->city }}</td>
                                                                    <td>
                                                                        @if($hub->country)
                                                                            @if(!empty($countryFlags[$hub->country]))
                                                                                <img src="{{ $countryFlags[$hub->country] }}" class="country-flag" alt="">
                                                                            @endif
                                                                            {{ $hub->country }}
                                                                        @else
                                                                            —
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $hub->phone_number }}</td>
                                                                    <td>{{ $hub->email }}</td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="hub-status-toggle {{ $isInactive ? 'is-inactive' : 'is-active' }}"
                                                                            data-id="{{ $hub->id }}"
                                                                            data-name="{{ $hub->hub_name }}"
                                                                            data-status="{{ $isInactive ? 'inactive' : 'active' }}"
                                                                            data-url="{{ route('hub.status.update', $hub->id) }}"
                                                                            title="Click to change status">
                                                                            {{ $isInactive ? 'Inactive' : 'Active' }}
                                                                        </button>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <a href="{{ route('hub.show', $hub->id) }}" style="color: #ccc; margin-right: 8px;"><i class="ti-pencil"></i></a>
                                                                        <a href="javascript:void(0)" class="delete-hub" data-id="{{ $hub->id }}" data-name="{{ $hub->hub_name }}" style="color: #ccc;" title="Delete hub"><i class="ti-trash"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Hub Index Design end -->
                                    </div>
                                        <!-- Base Style - Compact end -->
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
                width: '100%'
            });

            var table = $('#offices-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [7] }
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

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

            function matchesCountry(filterValue, rowValue) {
                if (!filterValue) {
                    return true;
                }

                return String(rowValue || '') === filterValue;
            }

            $('#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city, #filter-hub-country, #filter-hide-inactive').on('change keyup', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
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

                if (!matchesContains(getFilterText('#filter-hub-name'), rowData($row, 'hub-name'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-hub-code'), rowData($row, 'code'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-hub-address'), rowData($row, 'address'))) {
                    return false;
                }

                if (!matchesContains(getFilterText('#filter-hub-city'), rowData($row, 'city'))) {
                    return false;
                }

                if (!matchesCountry($('#filter-hub-country').val(), rowData($row, 'country'))) {
                    return false;
                }

                return true;
            });

            $('#clear-hub-filters').on('click', function(e) {
                e.preventDefault();
                $('#filter-hub-name, #filter-hub-code, #filter-hub-address, #filter-hub-city').val('');
                $('#filter-hub-country').val(null).trigger('change');
                $('#filter-hide-inactive').prop('checked', true);
                table.search('').columns().search('').draw();
            });

            $(document).on('click', '.hub-status-toggle', function() {
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
                }, function(isConfirm) {
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
                        success: function(response) {
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
                            table.row($row).invalidate('dom').draw(false);

                            swal({
                                title: 'Status updated',
                                text: response.message,
                                type: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            $button.prop('disabled', false);
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while updating the hub status.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

            $(document).on('click', '.delete-hub', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this hub';
                var $row = $(this).closest('tr');

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
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/hubs') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Hub deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $row.fadeOut(400, function() {
                                    table.row($row).remove().draw(false);
                                });
                            } else {
                                swal('Error', response.message || 'Error deleting hub.', 'error');
                            }
                        },
                        error: function(xhr) {
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
@endsection
