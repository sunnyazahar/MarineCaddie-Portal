@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <x-lists.base-styles bodyClass="change-logs-filters-open" toolbarClass="change-logs-filters-toolbar" />

    <style>
        body.change-logs-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.change-logs-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.change-logs-list-page .pcoded-inner-content,
        body.change-logs-list-page .main-body,
        body.change-logs-list-page .page-wrapper,
        body.change-logs-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .change-logs-list-card {
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
        .change-logs-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .change-logs-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .change-logs-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .change-logs-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }

        .change-logs-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #change-logs-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 960px;
        }
        #change-logs-table thead th {
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
        #change-logs-table tbody td {
            padding: 10px 14px !important;
            vertical-align: top !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #change-logs-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #change-logs-table tbody tr:last-child td {
            border-bottom: none;
        }

        #change-logs-table .change-log-title {
            color: #008080;
            font-weight: 700;
            line-height: 1.35;
        }
        #change-logs-table .change-log-desc {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
            line-height: 1.4;
        }
        #change-logs-table a.record-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        #change-logs-table a.record-link:hover {
            color: #006666;
            text-decoration: underline;
        }

        .entity-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e6f5f5;
            border: 1px solid #b7e0e0;
            color: #0f5f5f;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        #change-logs-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }

        .change-logs-pager {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .change-logs-pager-btn {
            color: #0f172a !important;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #dbe3ee;
            background: #fff;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .change-logs-pager-btn:hover:not(:disabled) {
            background: #e6f5f5 !important;
            border-color: #008080 !important;
            color: #008080 !important;
        }
        .change-logs-pager-btn:disabled {
            color: #94a3b8 !important;
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            cursor: default;
        }

        .change-logs-filters-area .filter-group .filter-date-icon {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('change-logs-list-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card change-logs-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Administration change logs"
                subtitle="Track edits across offices, companies, customers, vessels, and contacts"
                icon="ti-time"
                :count="0"
                countLabel="entries"
            />

            <div class="change-logs-filters-area">
                <x-lists.filter-toolbar
                    toggle-id="btn-change-logs-filters-toggle"
                    body-class="change-logs-filters-open"
                    toolbar-class="change-logs-filters-toolbar"
                />

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Search" width="220px">
                        <input type="text" id="filter-search" class="form-control filter-input" placeholder="Field, title or value" autocomplete="off">
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Entity" width="180px">
                        <select id="filter-entity-type" class="form-control filter-input change-log-select2">
                            <option value=""></option>
                            @foreach ($entityTypes as $class => $label)
                                <option value="{{ $class }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Changed by" width="180px">
                        <select id="filter-user-id" class="form-control filter-input change-log-select2">
                            <option value=""></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </x-lists.filter-field>
                    <x-lists.filter-field label="Date range" width="220px">
                        <input type="text" id="filter-date-range" class="form-control filter-input" placeholder="Select range" autocomplete="off" readonly>
                        <i class="ti-calendar filter-date-icon" aria-hidden="true"></i>
                    </x-lists.filter-field>
                    <x-lists.clear-filters id="filter-reset" />
                </x-lists.filter-bar>
            </div>

            <div id="change-logs-results" class="change-logs-table-area list-ajax-table-wrapper">
                <table id="change-logs-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Date</th>
                            <th style="width: 130px;">Entity</th>
                            <th style="width: 22%;">Record</th>
                            <th>Change</th>
                            <th style="width: 140px;">Changed by</th>
                        </tr>
                    </thead>
                    <tbody id="change-logs-tbody">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="change-logs-pagination" class="pagination-sticky-footer">
                <div class="list-pagination-meta" id="change-logs-meta">
                    <strong>Loading...</strong>
                </div>
                <div class="list-pagination-links change-logs-pager">
                    <button type="button" id="change-logs-prev" class="change-logs-pager-btn" disabled>Previous</button>
                    <button type="button" id="change-logs-next" class="change-logs-pager-btn" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
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
                        '<tr><td colspan="5" class="text-center py-4 text-muted">No change logs found.</td></tr>'
                    );
                    return;
                }

                rows.forEach(function (row, index) {
                    var recordHtml = row.record_url
                        ? '<a href="' + escapeHtml(row.record_url) + '" class="record-link">' + escapeHtml(row.record_name) + '</a>'
                        : escapeHtml(row.record_name);

                    var descHtml = row.description
                        ? '<div class="change-log-desc">' + escapeHtml(row.description) + '</div>'
                        : '';

                    var $tr = $(
                        '<tr>' +
                            '<td>' + escapeHtml(row.date || '') + '</td>' +
                            '<td><span class="entity-badge">' + escapeHtml(row.entity_label || '') + '</span></td>' +
                            '<td>' + recordHtml + '</td>' +
                            '<td><div class="change-log-title">' + escapeHtml(row.title || '') + '</div>' + descHtml + '</td>' +
                            '<td>' + escapeHtml(row.user_name || 'System') + '</td>' +
                        '</tr>'
                    );

                    $tbody.append($tr);
                });

                if (window.mcPulseListRows) {
                    window.mcPulseListRows('#change-logs-table');
                }
            }

            function renderMeta(meta) {
                currentPage = meta.current_page || 1;
                lastPage = meta.last_page || 1;
                var total = meta.total || 0;

                $('.list-page-header-count strong').text(total);
                if (window.mcPulseListCount) {
                    window.mcPulseListCount();
                }

                if (!total) {
                    $('#change-logs-meta').html('<strong>No results</strong>');
                    $('#change-logs-prev, #change-logs-next').prop('disabled', true);
                    return;
                }

                var from = meta.from || 0;
                var to = meta.to || 0;
                $('#change-logs-meta').html('<strong>Showing ' + from + '–' + to + ' of ' + total + '</strong>');
                $('#change-logs-prev').prop('disabled', currentPage <= 1);
                $('#change-logs-next').prop('disabled', currentPage >= lastPage);
            }

            function fetchLogs() {
                if (activeRequest) {
                    activeRequest.abort();
                }

                if (window.mcSetListLoading) {
                    window.mcSetListLoading('#change-logs-table', true);
                }

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
                            '<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load change logs.</td></tr>'
                        );
                        $('#change-logs-meta').html('<strong>—</strong>');
                        $('#change-logs-prev, #change-logs-next').prop('disabled', true);
                    },
                    complete: function () {
                        if (window.mcSetListLoading) {
                            window.mcSetListLoading('#change-logs-table', false);
                        }
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

            $('#filter-entity-type, #filter-user-id').select2({
                placeholder: 'Click here',
                allowClear: true,
                width: '100%'
            });

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

            $dateRange.closest('.filter-group').find('.filter-date-icon').on('click', function () {
                $dateRange.trigger('focus');
                $dateRange.data('daterangepicker').show();
            });

            $('#filter-entity-type, #filter-user-id').on('change', function () {
                queueFetch(true);
            });

            $('#filter-search').on('input', function () {
                queueFetch(true);
            });

            $('#filter-reset').on('click', function (e) {
                e.preventDefault();
                $('#filter-search').val('');
                $('#filter-entity-type').val(null).trigger('change');
                $('#filter-user-id').val(null).trigger('change');
                clearDateRange();
                queueFetch(true);
            });

            $('#change-logs-prev').on('click', function () {
                if (currentPage <= 1) {
                    return;
                }
                currentPage -= 1;
                fetchLogs();
            });

            $('#change-logs-next').on('click', function () {
                if (currentPage >= lastPage) {
                    return;
                }
                currentPage += 1;
                fetchLogs();
            });

            $(document).on('click', '.list-filters-toggle[data-body-class="change-logs-filters-open"]', function () {
                setTimeout(function () {
                    $('#filter-entity-type, #filter-user-id').each(function () {
                        $(this).next('.select2-container').css('width', '100%');
                    });
                }, 50);
            });

            fetchLogs();
        });
    </script>
@endpush
