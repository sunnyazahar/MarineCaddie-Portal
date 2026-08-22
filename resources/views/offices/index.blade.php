@extends('layouts.app')

@section('styles')
    @include('partials.list-pagination-footer-styles')

    <style>
        body.offices-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.offices-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.offices-list-page .pcoded-inner-content,
        body.offices-list-page .main-body,
        body.offices-list-page .page-wrapper,
        body.offices-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .offices-list-card {
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
        .offices-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .offices-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .offices-toolbar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            padding: 8px 10px;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
            border: 1px solid #d6e3ee;
            border-radius: 8px;
        }
        .offices-search-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1 1 auto;
            min-width: 0;
        }
        .offices-search-wrap i {
            color: #008080;
            font-size: 15px;
            flex-shrink: 0;
        }
        .offices-search-input {
            height: var(--mc-control-height, 34px);
            min-width: 200px;
            max-width: 360px;
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            color: #0e1d4a;
            background: #fff;
        }
        .offices-search-input:focus {
            outline: none;
            border-color: #0088c7;
            box-shadow: 0 0 0 3px rgba(0, 136, 199, 0.15);
        }
        .offices-search-input::placeholder {
            color: #94a3b8;
        }

        .btn-offices-add {
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
        .btn-offices-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.32);
        }

        .offices-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        #offices-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 920px;
        }
        #offices-table thead th {
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
        #offices-table tbody td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        #offices-table tbody tr:hover td {
            background: #f5fbfe !important;
        }
        #offices-table tbody tr:last-child td {
            border-bottom: none;
        }

        .office-name-link {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .office-name-link:hover {
            color: #006666;
            text-decoration: underline;
        }
        .office-email-link {
            color: #0088c7;
            text-decoration: none;
        }
        .office-email-link:hover {
            text-decoration: underline;
        }

        .office-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
        }
        .office-status-badge.is-active {
            color: #065f46;
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
        }
        .office-status-badge.is-inactive {
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
        }

        .office-country-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
            max-width: 100%;
        }
        .office-country-flag {
            display: block;
            width: 20px;
            height: 15px;
            flex-shrink: 0;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.04);
        }
        .office-country-name {
            min-width: 0;
            line-height: 1.3;
        }

        .office-action-btn {
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
        .office-action-btn:hover {
            color: #008080;
            background: #e6f5f5;
            border-color: #b7e0e0;
            text-decoration: none;
        }

        #offices-list-footer.pagination-sticky-footer {
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .offices-toolbar {
                padding: 8px;
            }
            .offices-search-input {
                max-width: none;
            }
            .btn-offices-add {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 mx-2 mt-2" role="alert" style="font-size: 12px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card offices-list-card">
        <div class="card-block">
            <x-lists.page-header
                title="Offices"
                subtitle="Manage regional offices, contact details, and invoicing settings"
                icon="ti-home"
                :count="$offices->count()"
                countLabel="offices"
            >
                <x-slot:actions>
                    @if ($canWriteAdministration)
                        <a href="{{ route('offices.create') }}" class="btn-offices-add d-none d-lg-inline-flex">
                            <i class="ti-plus"></i> Add office
                        </a>
                    @endif
                </x-slot:actions>
            </x-lists.page-header>

            <div class="offices-toolbar">
                <div class="offices-search-wrap">
                    <i class="ti-search" aria-hidden="true"></i>
                    <input type="search" class="offices-search-input search-input-custom" placeholder="Search offices…" aria-label="Search offices">
                </div>
                @if ($canWriteAdministration)
                    <a href="{{ route('offices.create') }}" class="btn-offices-add d-lg-none">
                        <i class="ti-plus"></i> Add office
                    </a>
                @endif
            </div>

            <div class="offices-table-area">
                <table id="offices-table" class="office-table">
                    <thead>
                        <tr>
                            <th>Office name</th>
                            <th>Short name</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Phone number</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th style="width: 52px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offices as $office)
                            <tr>
                                <td>
                                    <a href="{{ route('offices.edit', $office->id) }}" class="office-name-link">
                                        {{ $office->office_name }}
                                    </a>
                                </td>
                                <td>{{ $office->office_short_name }}</td>
                                <td>{{ $office->city }}</td>
                                <td>
                                    @if ($office->country)
                                        <span class="office-country-cell">
                                            @if ($office->country->flag_url)
                                                <img
                                                    src="{{ $office->country->flag_url }}"
                                                    class="office-country-flag"
                                                    alt="{{ $office->country->name }}"
                                                >
                                            @endif
                                            <span class="office-country-name">{{ $office->country->name }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $office->phone_number }}</td>
                                <td>
                                    @if ($office->email)
                                        <a href="mailto:{{ $office->email }}" class="office-email-link">{{ $office->email }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($office->status == 1)
                                        <span class="office-status-badge is-active">Active</span>
                                    @else
                                        <span class="office-status-badge is-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('offices.edit', $office->id) }}" class="office-action-btn" title="Edit office">
                                        <i class="ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No offices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="offices-list-footer" class="pagination-sticky-footer">
                <div class="list-pagination-meta">
                    <strong id="offices-visible-meta">Showing {{ $offices->count() }} of {{ $offices->count() }} offices</strong>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script>
        $(document).ready(function () {
            $('body').addClass('offices-list-page');

            var totalOffices = {{ (int) $offices->count() }};
            var $meta = $('#offices-visible-meta');

            function updateOfficesMeta(table) {
                if (!$meta.length) {
                    return;
                }
                var info = table.page.info();
                var filtered = info.recordsDisplay;
                if (filtered === totalOffices) {
                    $meta.text('Showing ' + totalOffices + ' of ' + totalOffices + ' offices');
                } else {
                    $meta.text('Showing ' + filtered + ' of ' + totalOffices + ' offices');
                }
            }

            var table = $('#offices-table').DataTable({
                dom: 't',
                pageLength: 100,
                ordering: true,
                order: [],
                autoWidth: false,
                responsive: false,
                scrollX: false,
                searching: true,
                info: false,
                paging: false,
                language: {
                    emptyTable: 'No offices found.'
                }
            });

            $('.offices-search-input').on('input', function () {
                table.search(this.value).draw();
            });

            table.on('draw', function () {
                updateOfficesMeta(table);
            });

            updateOfficesMeta(table);
        });
    </script>
@endsection
