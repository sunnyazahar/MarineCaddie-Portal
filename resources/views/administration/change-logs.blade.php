@extends('layouts.app')

@section('styles')

    <style>
        .change-logs-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 20px;
        }
        .change-logs-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            background: #fafafa;
        }
        .change-logs-filters .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 160px;
        }
        .change-logs-filters .filter-group.date-range-group {
            min-width: 220px;
        }
        .change-logs-filters label {
            font-size: 11px;
            color: #666;
            margin: 0;
        }
        .change-logs-filters .form-control {
            height: 32px;
            font-size: 12px;
            border-radius: 3px;
        }
        .change-logs-filters .btn-reset {
            height: 32px;
            color: #01a9ac;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }
        .change-logs-table {
            width: 100%;
            margin: 0;
            font-size: 12px;
        }
        .change-logs-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .change-logs-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            color: #334155;
        }
        .change-logs-table .title {
            color: #0ea5e9;
            font-weight: 600;
        }
        .change-logs-table .desc {
            color: #64748b;
            margin-top: 2px;
        }
        .change-logs-table a.record-link {
            color: #01a9ac;
            text-decoration: none;
            font-weight: 500;
        }
        .change-logs-table a.record-link:hover {
            text-decoration: underline;
            color: #01a9ac;
        }
        .change-logs-pagination {
            padding: 12px 16px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .change-logs-pagination .meta {
            font-size: 12px;
            color: #64748b;
        }
        .change-logs-pagination .pager {
            display: flex;
            gap: 8px;
        }
        .change-logs-pagination .pager button {
            height: 30px;
            min-width: 70px;
            border: 1px solid #d1d5db;
            background: #fff;
            border-radius: 3px;
            font-size: 12px;
            color: #334155;
            cursor: pointer;
        }
        .change-logs-pagination .pager button:disabled {
            opacity: 0.5;
            cursor: default;
        }
        .entity-badge {
            display: inline-block;
            background: #eef6f7;
            color: #1b5e6f;
            border-radius: 3px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
        }
        .change-logs-loading {
            position: relative;
        }
        .change-logs-loading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.55);
            pointer-events: none;
        }
    </style>
@endsection

@section('content')
    <div class="theme-loader">
        <div class="ball-scale">
            <div class="contain">
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            @include('layouts.top-menu')
            @include('layouts.left-menu')

            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-body">
                                <div class="change-logs-card">
                                    <div id="change-logs-filter-form" class="change-logs-filters">
                                        <div class="filter-group" style="min-width: 200px; flex: 1;">
                                            <label>Search</label>
                                            <input type="text" id="filter-search" class="form-control" placeholder="Field, title or value" autocomplete="off">
                                        </div>
                                        <div class="filter-group">
                                            <label>Entity</label>
                                            <select id="filter-entity-type" class="form-control">
                                                <option value="">All entities</option>
                                                @foreach ($entityTypes as $class => $label)
                                                    <option value="{{ $class }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="filter-group">
                                            <label>Changed by</label>
                                            <select id="filter-user-id" class="form-control">
                                                <option value="">All users</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="filter-group date-range-group">
                                            <label>Date range</label>
                                            <input type="text" id="filter-date-range" class="form-control" placeholder="Select date range" autocomplete="off" readonly>
                                        </div>
                                        <button type="button" id="filter-reset" class="btn-reset">Reset</button>
                                    </div>

                                    <div id="change-logs-results">
                                        <div class="table-responsive">
                                            <table class="change-logs-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 140px;">Date</th>
                                                        <th style="width: 130px;">Entity</th>
                                                        <th>Record</th>
                                                        <th>Change</th>
                                                        <th style="width: 140px;">Changed by</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="change-logs-tbody">
                                                    <tr>
                                                        <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">
                                                            Loading...
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="change-logs-pagination" id="change-logs-pagination" style="display: none;">
                                            <div class="meta" id="change-logs-meta"></div>
                                            <div class="pager">
                                                <button type="button" id="change-logs-prev">Previous</button>
                                                <button type="button" id="change-logs-next">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            var searchUrl = @json(route('administration.change-logs.search'));
            var currentPage = 1;
            var lastPage = 1;
            var searchTimer = null;
            var activeRequest = null;
            var dateFrom = '';
            var dateTo = '';
            var $dateRange = $('#filter-date-range');

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function filters() {
                return {
                    search: $.trim($('#filter-search').val() || ''),
                    entity_type: $('#filter-entity-type').val() || '',
                    user_id: $('#filter-user-id').val() || '',
                    date_from: dateFrom,
                    date_to: dateTo,
                    page: currentPage
                };
            }

            function renderRows(rows) {
                var $tbody = $('#change-logs-tbody');
                $tbody.empty();

                if (!rows.length) {
                    $tbody.append(
                        '<tr><td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">No change logs found.</td></tr>'
                    );
                    return;
                }

                rows.forEach(function (row) {
                    var recordHtml = row.record_url
                        ? '<a href="' + escapeHtml(row.record_url) + '" class="record-link">' + escapeHtml(row.record_name) + '</a>'
                        : escapeHtml(row.record_name);

                    var descHtml = row.description
                        ? '<div class="desc">' + escapeHtml(row.description) + '</div>'
                        : '';

                    $tbody.append(
                        '<tr>' +
                            '<td>' + escapeHtml(row.date || '') + '</td>' +
                            '<td><span class="entity-badge">' + escapeHtml(row.entity_label || '') + '</span></td>' +
                            '<td>' + recordHtml + '</td>' +
                            '<td><div class="title">' + escapeHtml(row.title || '') + '</div>' + descHtml + '</td>' +
                            '<td>' + escapeHtml(row.user_name || 'System') + '</td>' +
                        '</tr>'
                    );
                });
            }

            function renderMeta(meta) {
                currentPage = meta.current_page || 1;
                lastPage = meta.last_page || 1;

                if (!meta.total) {
                    $('#change-logs-pagination').hide();
                    return;
                }

                var from = meta.from || 0;
                var to = meta.to || 0;
                $('#change-logs-meta').text('Showing ' + from + ' to ' + to + ' of ' + meta.total + ' entries');
                $('#change-logs-prev').prop('disabled', currentPage <= 1);
                $('#change-logs-next').prop('disabled', currentPage >= lastPage);
                $('#change-logs-pagination').show();
            }

            function fetchLogs() {
                if (activeRequest) {
                    activeRequest.abort();
                }

                $('#change-logs-results').addClass('change-logs-loading');

                activeRequest = $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    dataType: 'json',
                    data: filters(),
                    success: function (response) {
                        renderRows(response.data || []);
                        renderMeta(response.meta || {});
                    },
                    error: function (xhr) {
                        if (xhr.statusText === 'abort') {
                            return;
                        }
                        $('#change-logs-tbody').html(
                            '<tr><td colspan="5" style="text-align: center; color: #b91c1c; padding: 30px;">Failed to load change logs.</td></tr>'
                        );
                        $('#change-logs-pagination').hide();
                    },
                    complete: function () {
                        $('#change-logs-results').removeClass('change-logs-loading');
                        activeRequest = null;
                    }
                });
            }

            function queueFetch(resetPage) {
                if (resetPage) {
                    currentPage = 1;
                }
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchLogs, 300);
            }

            function clearDateRange() {
                dateFrom = '';
                dateTo = '';
                $dateRange.val('');

                var picker = $dateRange.data('daterangepicker');
                if (picker) {
                    picker.setStartDate(moment());
                    picker.setEndDate(moment());
                }
            }

            $dateRange.daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    format: 'DD.MM.YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $dateRange.on('apply.daterangepicker', function (ev, picker) {
                dateFrom = picker.startDate.format('YYYY-MM-DD');
                dateTo = picker.endDate.format('YYYY-MM-DD');
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
                queueFetch(true);
            });

            $dateRange.on('cancel.daterangepicker', function () {
                clearDateRange();
                queueFetch(true);
            });

            $('#filter-entity-type, #filter-user-id').on('change', function () {
                queueFetch(true);
            });

            $('#filter-search').on('input', function () {
                queueFetch(true);
            });

            $('#filter-reset').on('click', function () {
                $('#filter-search').val('');
                $('#filter-entity-type').val('');
                $('#filter-user-id').val('');
                clearDateRange();
                queueFetch(true);
            });

            $('#change-logs-prev').on('click', function () {
                if (currentPage <= 1) return;
                currentPage -= 1;
                fetchLogs();
            });

            $('#change-logs-next').on('click', function () {
                if (currentPage >= lastPage) return;
                currentPage += 1;
                fetchLogs();
            });

            fetchLogs();
        });
    </script>
@endsection
