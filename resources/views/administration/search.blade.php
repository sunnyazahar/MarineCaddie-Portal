@extends('layouts.app')

@section('styles')
    <x-lists.base-styles bodyClass="administration-search-filters-open" toolbarClass="administration-search-filters-toolbar" />

    <style>
        body.administration-search-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.administration-search-page .pcoded-content {
            overflow: hidden !important;
        }
        body.administration-search-page .pcoded-inner-content,
        body.administration-search-page .main-body,
        body.administration-search-page .page-wrapper,
        body.administration-search-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .administration-search-list-card {
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
        .administration-search-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }

        .administration-search-filters-area {
            flex-shrink: 0;
            margin-bottom: 8px;
            width: 100%;
        }
        .administration-search-filters-area .filter-row {
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }
        .administration-search-filters-area .filter-item {
            flex: 1 1 560px;
            min-width: 0;
        }
        .administration-search-filters-area .filter-group {
            height: 34px;
        }
        .administration-search-filters-area .filter-input {
            font-size: 12px;
        }
        .administration-search-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-shrink: 0;
        }
        .administration-search-btn {
            height: 34px;
            padding: 0 18px !important;
            border-radius: 6px !important;
            background: #008080 !important;
            border-color: #008080 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            box-shadow: 0 8px 18px rgba(0, 128, 128, 0.14);
        }
        .administration-search-btn:hover,
        .administration-search-btn:focus {
            background: #006d6d !important;
            border-color: #006d6d !important;
        }

        .administration-search-state {
            flex-shrink: 0;
            margin-bottom: 8px;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
        }
        .administration-search-state.is-loading {
            border-style: solid;
            border-color: #d9eef0;
            background: #f0fafb;
            color: #0f5f5f;
        }
        .administration-search-state.is-warning {
            border-style: solid;
            border-color: #facc15;
            background: #fffbeb;
            color: #854d0e;
        }
        .administration-search-state.is-error {
            border-style: solid;
            border-color: #fca5a5;
            background: #fef2f2;
            color: #b91c1c;
        }
        .administration-search-state.is-success {
            border-style: solid;
            border-color: #b7e0e0;
            background: #f0fafb;
            color: #0f5f5f;
        }

        .administration-search-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #administration-search-table {
            width: 100%;
            min-width: 1480px;
            border-collapse: collapse;
        }
        #administration-search-table thead th {
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
        #administration-search-table tbody td {
            padding: 10px 14px !important;
            vertical-align: top !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #administration-search-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #administration-search-table tbody tr:last-child td {
            border-bottom: none;
        }

        .administration-search-name {
            color: #0f172a;
            font-weight: 700;
            line-height: 1.35;
        }
        .administration-search-name-link {
            color: inherit;
            text-decoration: none;
        }
        .administration-search-name-link:hover,
        .administration-search-name-link:focus {
            color: #118689;
            text-decoration: underline;
        }
        .administration-search-ref {
            color: #475569;
            font-weight: 600;
        }
        .administration-search-cell-text {
            color: #475569;
            font-weight: 600;
            line-height: 1.35;
        }
        .administration-search-details {
            color: #64748b;
            font-size: 12px;
            line-height: 1.45;
        }
        .administration-search-contact-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        .administration-search-contact-link:hover,
        .administration-search-contact-link:focus {
            color: #1d4ed8;
            text-decoration: underline;
        }
        .administration-search-country {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-weight: 600;
            line-height: 1.35;
        }
        .administration-search-country-flag {
            width: 18px;
            height: 13px;
            border-radius: 3px;
            object-fit: cover;
            box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.22);
            background: #fff;
        }
        .administration-search-type-badge {
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
        .administration-search-status {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #dbe3ee;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .administration-search-copy-cell {
            text-align: center;
        }
        .administration-search-copy-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 1px solid #d6e3ee;
            border-radius: 999px;
            background: #fff;
            color: #118689;
            transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        }
        .administration-search-copy-btn:hover,
        .administration-search-copy-btn:focus {
            border-color: #118689;
            background: #f0fafb;
            color: #0f5f5f;
            box-shadow: 0 0 0 3px rgba(17, 134, 137, 0.12);
            outline: none;
        }
        .administration-search-copy-btn:disabled,
        .administration-search-copy-btn.is-disabled {
            cursor: not-allowed;
            color: #94a3b8;
            background: #f8fafc;
            border-color: #e2e8f0;
            box-shadow: none;
        }
        .administration-search-copy-btn.is-copied {
            border-color: #16a34a;
            background: #f0fdf4;
            color: #15803d;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
        }
        .administration-search-copy-toast {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 1055;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.94);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px);
            transition: opacity 0.18s ease, transform 0.18s ease;
        }
        .administration-search-copy-toast.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 991.98px) {
            body.administration-search-page {
                overflow: auto !important;
                height: auto;
            }
            body.administration-search-page .pcoded-inner-content,
            body.administration-search-page .main-body,
            body.administration-search-page .page-wrapper,
            body.administration-search-page .page-body {
                height: auto;
                overflow: visible !important;
            }
            .administration-search-list-card {
                height: auto;
                min-height: calc(100vh - 64px);
            }
            .administration-search-list-card > .card-block {
                overflow: visible;
            }
            .administration-search-filters-area .filter-row {
                display: flex !important;
                max-height: none;
                overflow: visible;
                width: 100%;
                gap: 12px;
                padding: 12px;
            }
            .administration-search-filters-area .filter-item {
                width: 100% !important;
                flex: 1 1 100% !important;
            }
            .administration-search-filters-area .filter-group {
                height: 40px;
            }
            .administration-search-filters-area .filter-input {
                font-size: 14px;
            }
            .administration-search-table-area {
                overflow: auto;
            }
            .administration-search-actions {
                width: 100%;
                margin-left: 0;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 10px;
            }
            .administration-search-btn {
                flex: 1 1 auto;
                min-height: 40px;
            }
            #administration-search-reset {
                flex: 0 0 auto;
                display: inline-flex;
                align-items: center;
                min-height: 40px;
                white-space: nowrap;
            }
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('administration-search-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card administration-search-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Administration search"
                subtitle="Search offices, hubs, agents, other companies, suppliers, customers, and vessels in one table"
                icon="ti-search"
                :count="0"
                countLabel="results"
            />

            <div class="administration-search-filters-area" data-mc-filter-persist-key="administration-search-filter-values-v1">
                <x-lists.filter-toolbar
                    toggle-id="btn-administration-search-filters-toggle"
                    body-class="administration-search-filters-open"
                    toolbar-class="administration-search-filters-toolbar"
                />

                <x-lists.filter-bar>
                    <x-lists.filter-field label="Search" width="100%">
                        <input
                            type="text"
                            id="administration-search-query"
                            name="administration_search_query"
                            class="form-control filter-input"
                            placeholder="Try marinecaddie, a supplier email, customer name, or vessel IMO"
                            autocomplete="off"
                        >
                    </x-lists.filter-field>

                    <div class="administration-search-actions">
                        <button type="button" id="administration-search-submit" class="btn btn-primary btn-sm administration-search-btn">
                            Search
                        </button>
                        <a href="#" id="administration-search-reset" class="btn-clear-filters">Clear</a>
                    </div>
                </x-lists.filter-bar>
            </div>

            <div id="administration-search-state" class="administration-search-state is-empty">
                Search administration records and see office, hub, agent, other company, supplier, customer, and vessel results in one table.
            </div>

            <div id="administration-search-results" class="administration-search-table-area list-ajax-table-wrapper" hidden>
                <table id="administration-search-table">
                    <thead>
                        <tr>
                            <th style="width: 23%;">Name</th>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 12%;">City</th>
                            <th style="width: 14%;">Country</th>
                            <th style="width: 16%;">Email</th>
                            <th style="width: 12%;">Phone</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 120px;">Type</th>
                            <th style="width: 92px; text-align: center;">Copy</th>
                        </tr>
                    </thead>
                    <tbody id="administration-search-tbody">
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Search administration records to load results.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script>
        $(function () {
            var searchUrl = @json(route('administration.search.lookup'));
            var activeRequest = null;
            var $query = $('#administration-search-query');
            var $submit = $('#administration-search-submit');
            var $state = $('#administration-search-state');
            var $results = $('#administration-search-results');
            var $tbody = $('#administration-search-tbody');
            var $count = $('.list-page-header-count strong').first();
            var filterStore = typeof window.mcEnsureFilterPersistence === 'function'
                ? window.mcEnsureFilterPersistence({ root: '.administration-search-filters-area[data-mc-filter-persist-key]' })
                : null;
            var copyToastTimer = null;

            if (filterStore) {
                filterStore.restore();
            }

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function setResultCount(value) {
                $count.text(parseInt(value || 0, 10));

                if (window.mcPulseListCount) {
                    window.mcPulseListCount();
                }
            }

            function setState(message, tone) {
                tone = tone || 'empty';

                $state
                    .removeClass('is-empty is-loading is-warning is-error is-success')
                    .addClass('is-' + tone)
                    .html(message)
                    .prop('hidden', false);
            }

            function setLoading(isLoading) {
                $submit
                    .prop('disabled', !!isLoading)
                    .text(isLoading ? 'Searching...' : 'Search');

                if (isLoading) {
                    setState('Searching administration records...', 'loading');
                }
            }

            function fallbackCopy(text) {
                var textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'fixed';
                textarea.style.top = '0';
                textarea.style.left = '0';
                textarea.style.width = '2em';
                textarea.style.height = '2em';
                textarea.style.padding = '0';
                textarea.style.border = 'none';
                textarea.style.outline = 'none';
                textarea.style.boxShadow = 'none';
                textarea.style.background = 'transparent';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                var success = false;

                try {
                    success = document.execCommand('copy');
                } catch (err) {
                    success = false;
                }

                document.body.removeChild(textarea);

                return success;
            }

            function copyTextToClipboard(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(text).catch(function () {
                        return fallbackCopy(text) ? Promise.resolve() : Promise.reject();
                    });
                }

                return fallbackCopy(text) ? Promise.resolve() : Promise.reject();
            }

            function showCopyToast(message) {
                var $toast = $('#administration-search-copy-toast');

                if (!$toast.length) {
                    $toast = $('<div id="administration-search-copy-toast" class="administration-search-copy-toast" role="status" aria-live="polite"></div>');
                    $('body').append($toast);
                }

                $toast.html('<i class="ti-check"></i> ' + escapeHtml(message));

                if (copyToastTimer) {
                    clearTimeout(copyToastTimer);
                }

                $toast.addClass('is-visible');

                copyToastTimer = setTimeout(function () {
                    $toast.removeClass('is-visible');
                }, 2200);
            }

            function renderNameValue(row) {
                var name = escapeHtml(row.name || '—');

                if (row.url) {
                    return '<a href="' + escapeHtml(row.url) + '" class="administration-search-name-link">' + name + '</a>';
                }

                return name;
            }

            function renderTextValue(value) {
                return '<span class="administration-search-cell-text">' + escapeHtml(value || '—') + '</span>';
            }

            function renderEmailValue(row) {
                var email = $.trim(row.email || '');

                if (!email) {
                    return renderTextValue('—');
                }

                return '<a href="mailto:' + escapeHtml(email) + '" class="administration-search-contact-link">' + escapeHtml(email) + '</a>';
            }

            function renderCountryValue(row) {
                var country = $.trim(row.country || '');
                var flagUrl = $.trim(row.country_flag_url || '');

                if (!country) {
                    return renderTextValue('—');
                }

                return '<span class="administration-search-country">' +
                    (flagUrl ? '<img src="' + escapeHtml(flagUrl) + '" class="administration-search-country-flag" alt="">' : '') +
                    '<span>' + escapeHtml(country) + '</span>' +
                '</span>';
            }

            function renderCopyAction(row) {
                var copyValue = $.trim(row.copy_value || '');
                var copyLabel = row.name ? 'Copy address for ' + row.name : 'Copy address';

                if (!copyValue) {
                    return '<button type="button" class="administration-search-copy-btn is-disabled" disabled title="Address unavailable" aria-label="Address unavailable"><i class="icofont icofont-copy-alt"></i></button>';
                }

                return '<button type="button" class="administration-search-copy-btn" data-copy-value="' + encodeURIComponent(copyValue) + '" title="Copy full address" aria-label="' + escapeHtml(copyLabel) + '"><i class="icofont icofont-copy-alt"></i></button>';
            }

            function renderRows(rows) {
                $tbody.empty();

                if (!rows.length) {
                    $tbody.append('<tr><td colspan="9" class="text-center py-4 text-muted">No administration results found.</td></tr>');
                    return;
                }

                $.each(rows, function (_, row) {
                    $tbody.append(
                        '<tr>' +
                            '<td><div class="administration-search-name">' + renderNameValue(row) + '</div></td>' +
                            '<td>' + renderTextValue(row.code || '—') + '</td>' +
                            '<td>' + renderTextValue(row.city || '—') + '</td>' +
                            '<td>' + renderCountryValue(row) + '</td>' +
                            '<td>' + renderEmailValue(row) + '</td>' +
                            '<td>' + renderTextValue(row.phone || '—') + '</td>' +
                            '<td><span class="administration-search-status">' + escapeHtml(row.status || '—') + '</span></td>' +
                            '<td><span class="administration-search-type-badge">' + escapeHtml(row.type || '—') + '</span></td>' +
                            '<td class="administration-search-copy-cell">' + renderCopyAction(row) + '</td>' +
                        '</tr>'
                    );
                });

                if (window.mcPulseListRows) {
                    window.mcPulseListRows('#administration-search-table');
                }
            }

            function resetScreen() {
                setResultCount(0);
                $results.prop('hidden', true);
                $tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">Search administration records to load results.</td></tr>');
                setState('Search administration records and see mixed results in one table with their type.', 'empty');
            }

            $(document).on('click', '.administration-search-copy-btn', function () {
                var $button = $(this);
                var encodedCopyValue = $button.attr('data-copy-value') || '';
                var copyValue = '';

                try {
                    copyValue = $.trim(decodeURIComponent(encodedCopyValue));
                } catch (err) {
                    copyValue = $.trim(encodedCopyValue);
                }

                if ($button.prop('disabled') || copyValue === '') {
                    return;
                }

                copyTextToClipboard(copyValue).then(function () {
                    $button.addClass('is-copied');
                    showCopyToast('Address copied');

                    window.setTimeout(function () {
                        $button.removeClass('is-copied');
                    }, 1400);
                }).catch(function () {
                    alert('Could not copy address. Please copy manually:\n\n' + copyValue);
                });
            });

            function runSearch(query) {
                query = $.trim(query || '');

                if (activeRequest) {
                    activeRequest.abort();
                }

                if (query === '') {
                    if (filterStore) {
                        filterStore.clear();
                    }

                    resetScreen();
                    return;
                }

                if (filterStore) {
                    filterStore.save();
                }

                setLoading(true);

                activeRequest = $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        q: query
                    },
                    success: function (response) {
                        var rows = response.rows || [];

                        if (response.matched && rows.length) {
                            renderRows(rows);
                            $results.prop('hidden', false);
                            setResultCount(response.total || rows.length);

                            var returned = response.returned || rows.length;
                            var total = response.total || rows.length;
                            var suffix = total > returned ? ' Showing top ' + returned + '.' : '.';

                            setState(
                                'Found <strong>' + total + '</strong> administration result' + (total === 1 ? '' : 's') + ' for <strong>' + escapeHtml(query) + '</strong>.' + suffix,
                                'success'
                            );
                            return;
                        }

                        setResultCount(0);
                        $results.prop('hidden', true);

                        if (response.scopeBlocked) {
                            setState('Your role cannot search administration records from this page.', 'warning');
                            return;
                        }

                        if (response.sensitiveBlocked) {
                            setState('Sensitive credential details cannot be searched from administration search.', 'warning');
                            return;
                        }

                        if (response.changeLogsHidden) {
                            setState('Administration change logs ka data is search page par nahi dikhaya jata.', 'warning');
                            return;
                        }

                        if (response.administrationOnly) {
                            setState('This page only supports office, hub, agent, other company, supplier, customer, and vessel search.', 'warning');
                            return;
                        }

                        setState('No administration result found. Try a broader keyword like marinecaddie or a specific email, IMO, code, or record name.', 'empty');
                    },
                    error: function (xhr) {
                        if (xhr.statusText === 'abort') {
                            return;
                        }

                        setResultCount(0);
                        $results.prop('hidden', true);
                        setState('Administration search could not load right now. Please try again.', 'error');
                    },
                    complete: function () {
                        setLoading(false);
                        activeRequest = null;
                    }
                });
            }

            $('#administration-search-submit').on('click', function () {
                runSearch($query.val());
            });

            $query.on('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();
                runSearch($query.val());
            });

            $query.on('input', function () {
                if ($.trim($query.val() || '') !== '') {
                    return;
                }

                if (filterStore) {
                    filterStore.clear();
                }

                resetScreen();
            });

            $('#administration-search-reset').on('click', function (event) {
                event.preventDefault();

                if (activeRequest) {
                    activeRequest.abort();
                }

                $query.val('').trigger('focus');

                if (filterStore) {
                    filterStore.clear();
                }

                resetScreen();
            });

            var initialQuery = $.trim($query.val() || '');
            if (initialQuery !== '') {
                runSearch(initialQuery);
                return;
            }

            resetScreen();
        });
    </script>
@endpush
