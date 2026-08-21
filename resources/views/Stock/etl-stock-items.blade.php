@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <style>
        /* Filter Bar Styles */
        .filter-container {
            background: #fff;
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .filter-item {
            display: flex;
            align-items: center;
            border: 1px solid #bacdd2;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
            height: 38px;
        }
        .filter-item-label {
            padding: 0 12px;
            font-size: 11px;
            color: #55626a;
            background: #fff;
            border-right: 1px solid #bacdd2;
            height: 100%;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }
        .filter-item-content {
            padding: 0 10px;
            display: flex;
            align-items: center;
            height: 100%;
        }
        .filter-item-content .select2-container--default .select2-selection--single {
            border: none !important;
            height: 34px !important;
        }
        .filter-item-content .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            font-size: 13px !important;
        }
        .type-segments {
            display: flex;
            align-items: center;
            gap: 15px;
            height: 100%;
        }
        .type-segment {
            font-size: 12px;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none !important;
            margin-bottom: 0;
            padding: 0;
        }
        .type-segment.active {
            background-color: transparent !important;
            color: #1f2937 !important;
            font-weight: normal !important;
        }
        .type-segment input[type="radio"] {
            display: block !important;
            width: 16px;
            height: 16px;
            accent-color: #008080;
            margin: 0;
        }
        .dangerous-goods-box {
            display: flex;
            align-items: center;
            border: 1px solid #bacdd2;
            border-radius: 4px;
            height: 38px;
            padding: 0 12px;
            background: #fff;
            gap: 10px;
        }
        .dangerous-goods-label {
            font-size: 11px;
            color: #55626a;
        }
        .custom-checkbox-styled {
            width: 18px;
            height: 18px;
            border: 1px solid #bacdd2;
            border-radius: 2px;
            cursor: pointer;
            accent-color: #008080;
        }

        /* High Density Table Styles */
        #etl-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #etl-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #fdfdfd !important;
            color: #4b5563;
            font-size: 11px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); 
        }
        #etl-table tbody td {
            padding: 6px 8px;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
        }
        .badge-stock {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
        }
        .table-link {
            color: #2563eb;
            text-decoration: none;
        }
        .table-link:hover {
            text-decoration: underline;
        }
        .copy-icon {
            color: #9ca3af;
            font-size: 12px;
            margin-right: 5px;
            cursor: pointer;
        }
        .copy-icon:hover {
            color: #4b5563;
        }
        #etl-table .custom-control-input:checked ~ .custom-control-label::before {
            border-color: #008080;
            background-color: #008080;
        }
        .clear-filters-link {
            color: #008080;
            font-size: 12px;
            text-decoration: none;
            margin-left: 10px;
        }
        .clear-filters-link:hover {
            text-decoration: underline;
        }
        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 150px);
            width: 100%;
            position: relative;
        }
        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        
        /* Select2 Reset */
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
            min-height: 32px !important;
            padding-bottom: 2px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #4b5563 !important;
            line-height: normal !important;
            font-size: 12px !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            margin-top: 0 !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
            color: #4b5563 !important;
            padding: 1px 8px !important;
            margin-top: 4px !important;
            font-size: 11px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice span {
            color: #4b5563 !important;
        }
    </style>
    @include('partials.searchable-filter-multiselect-styles')
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
                                <div class=""> 
                                    <div class="page-header">
                                      
                                   <div class="card">
                                       <div class="filter-container mt-5">
                                           <div class="filter-item" style="min-width: 250px;">
                                               <span class="filter-item-label">Hub</span>
                                               <div class="filter-item-content" style="flex: 1;">
                                                   <select id="filter-hub" class="form-control searchable-filter-multiselect" multiple="multiple">
                                                       <option value="ABJ">ABJ</option>
                                                   </select>
                                               </div>
                                           </div>

                                           <div class="filter-item">
                                               <span class="filter-item-label">Type</span>
                                               <div class="filter-item-content">
                                                   <div class="type-segments">
                                                       <label class="type-segment active">
                                                           <input type="radio" name="stock_type" checked> ETL
                                                       </label>
                                                       <label class="type-segment">
                                                           <input type="radio" name="stock_type"> KTL
                                                       </label>
                                                       <label class="type-segment">
                                                           <input type="radio" name="stock_type"> RTL
                                                       </label>
                                                   </div>
                                               </div>
                                           </div>

                                           <div class="filter-item" style="min-width: 200px;">
                                               <span class="filter-item-label">Status</span>
                                               <div class="filter-item-content" style="flex: 1;">
                                                   <select id="filter-status" class="form-control searchable-filter-multiselect" multiple="multiple">
                                                       <option value="Stock">Stock</option>
                                                       <option value="Pending">Pending</option>
                                                   </select>
                                               </div>
                                           </div>

                                           <div class="dangerous-goods-box">
                                               <span class="dangerous-goods-label">Hide dangerous goods</span>
                                               <input type="checkbox" class="custom-checkbox-styled" id="hideDgr">
                                           </div>

                                           <a href="#" class="clear-filters-link" style="color: #436d7a; font-weight: 500;">Clear filters</a>
                                       </div>
                                       
                                       <div class="table-scroll-wrapper">
                                           <table id="etl-table" class="table">
                                               <thead>
                                                   <tr>
                                                       <th style="width: 30px;">
                                                           <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" class="custom-control-input" id="checkAll">
                                                               <label class="custom-control-label" for="checkAll"></label>
                                                           </div>
                                                       </th>
                                                       <th>Hub</th>
                                                       <th>Stock number</th>
                                                       <th>Customer</th>
                                                       <th>Vessel</th>
                                                       <th>PO number</th>
                                                       <th>Supplier</th>
                                                       <th>Items</th>
                                                       <th>Weight</th>
                                                       <th>Value</th>
                                                       <th>Transit Id</th>
                                                       <th>Shipping no</th>
                                                       <th>Status</th>
                                                   </tr>
                                               </thead>
                                               <tbody>
                                                   @php
                                                       $samples = [
                                                           ['hub' => 'ABJ', 'stock' => 'PUS1-61513145', 'customer' => 'Priano Marchelli S.P.A.', 'vessel' => 'Varoli Piazza (for Priano Marc...', 'po' => 'VP/25/0086A', 'supplier' => 'Dintec Co., Ltd', 'items' => 1, 'weight' => 10, 'value' => '987.60 USD', 'transit' => 'AWB: 07158174410'],
                                                           ['hub' => 'ABJ', 'stock' => 'PUS1-61513033', 'customer' => 'Priano Marchelli S.P.A.', 'vessel' => 'Varoli Piazza (for Priano Marc...', 'po' => 'VP/25/51345D', 'supplier' => 'Panasia Co., Ltd.', 'items' => 1, 'weight' => 1, 'value' => '648.00 USD', 'transit' => 'AWB: 07158174410'],
                                                           ['hub' => 'ABJ', 'stock' => 'PUS1-61506066', 'customer' => 'Priano Marchelli S.P.A.', 'vessel' => 'Varoli Piazza (for Priano Marc...', 'po' => 'VP/25/0057, VP/2...', 'supplier' => 'DINTEC Co., Ltd.', 'items' => 2, 'weight' => 50, 'value' => '9 986.79 USD', 'transit' => 'AWB: 07158174410'],
                                                       ];
                                                   @endphp
                                                   @foreach($samples as $i => $row)
                                                   <tr>
                                                       <td>
                                                           <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" class="custom-control-input" id="check{{$i}}">
                                                               <label class="custom-control-label" for="check{{$i}}"></label>
                                                           </div>
                                                       </td>
                                                       <td>{{$row['hub']}}</td>
                                                       <td>
                                                           <i class="ti-file copy-icon"></i>
                                                           <a href="{{ route('stocks.edit', $row['stock']) }}" class="table-link">{{$row['stock']}}</a>
                                                       </td>
                                                       <td>{{$row['customer']}}</td>
                                                       <td>{{$row['vessel']}}</td>
                                                       <td>{{$row['po']}}</td>
                                                       <td>{{$row['supplier']}}</td>
                                                       <td>{{$row['items']}}</td>
                                                       <td>{{$row['weight']}}</td>
                                                       <td>{{$row['value']}}</td>
                                                       <td>{{$row['transit']}}</td>
                                                       <td></td>
                                                       <td><span class="stock-status-badge stock-status-stock">Stock</span></td>
                                                   </tr>
                                                   @endforeach
                                                   @for($i=3; $i < 200; $i++)
                                                   <tr>
                                                       <td>
                                                           <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" class="custom-control-input" id="check{{$i}}">
                                                               <label class="custom-control-label" for="check{{$i}}"></label>
                                                           </div>
                                                       </td>
                                                       <td>ABJ</td>
                                                       <td>
                                                           <i class="ti-file copy-icon"></i>
                                                           <a href="#" class="table-link">PUS1-6150{{1000+$i}}</a>
                                                       </td>
                                                       <td>Priano Marchelli S.P.A.</td>
                                                       <td>Varoli Piazza (for Priano Marc...</td>
                                                       <td>VP/25/00{{10+$i}}</td>
                                                       <td>DINTEC Co., Ltd.</td>
                                                       <td>{{rand(1,5)}}</td>
                                                       <td>{{rand(5,100)}}</td>
                                                       <td>{{number_format(rand(500, 10000), 2)}} USD</td>
                                                       <td>AWB: 0715817441{{rand(0,9)}}</td>
                                                       <td></td>
                                                       <td><span class="stock-status-badge stock-status-stock">Stock</span></td>
                                                   </tr>
                                                   @endfor
                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
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

    @include('partials.searchable-filter-multiselect-script')

    <script>
        $(document).ready(function() {
            initializeSearchableFilterMultiselect('#filter-hub, #filter-status');

            // Initialize DataTable
            var table = $('#etl-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [0] }
                ]
            });

            $.fn.dataTable.ext.search.push(function(settings, data) {
                if (settings.nTable.id !== 'etl-table') {
                    return true;
                }

                var selectedHubs = $('#filter-hub').val() || [];
                var selectedStatuses = $('#filter-status').val() || [];
                var hubMatches = selectedHubs.length === 0 || selectedHubs.indexOf(String(data[1] || '').trim()) !== -1;
                var rowStatus = $('<div>').html(data[12] || '').text().trim();
                var statusMatches = selectedStatuses.length === 0 || selectedStatuses.indexOf(rowStatus) !== -1;

                return hubMatches && statusMatches;
            });

            $('#filter-hub, #filter-status').on('change', function() {
                table.draw();
            });

            $('.clear-filters-link').on('click', function(event) {
                event.preventDefault();
                clearSearchableFilterMultiselect('#filter-hub, #filter-status');
                $('#hideDgr').prop('checked', false);
                table.search('').columns().search('').draw();
            });

            // Handle Check All
            $('#checkAll').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('#etl-table tbody input[type="checkbox"]').prop('checked', isChecked);
            });

            // Handle Row Selection
            $('#etl-table tbody').on('change', 'input[type="checkbox"]', function() {
                var allChecked = $('#etl-table tbody input[type="checkbox"]').length === $('#etl-table tbody input[type="checkbox"]:checked').length;
                $('#checkAll').prop('checked', allChecked);
            });
            
            // Handle Type Segment click
            $('.type-segment').on('click', function() {
                $(this).find('input').prop('checked', true);
            });
        });
    </script>
@endsection
