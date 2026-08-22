@extends('layouts.app')

@section('content')
@include('layouts.partials.pcoded-shell-start')
@php
    $userName = auth()->user()?->name ?? 'there';
    $period = (int) $dashboard['period'];

    $kpiCards = [
        ['label' => 'Active stocks', 'value' => $dashboard['kpis']['activeStocks'], 'icon' => 'icon-package', 'tone' => 'success', 'href' => route('stocks')],
        ['label' => 'Active shipments', 'value' => $dashboard['kpis']['activeShipments'], 'icon' => 'icon-navigation', 'tone' => 'info', 'href' => route('shipments')],
        ['label' => 'Overdue arrivals', 'value' => $dashboard['kpis']['overdueArrivals'], 'icon' => 'icon-alert-triangle', 'tone' => 'danger', 'href' => route('shipment-follow-up')],
        ['label' => 'Pre-alerts due', 'value' => $dashboard['kpis']['preAlertsDue'], 'icon' => 'icon-bell', 'tone' => 'warning', 'href' => route('pre-alert-reminders')],
        ['label' => 'Unaccepted stocks', 'value' => $dashboard['kpis']['unacceptedStocks'], 'icon' => 'icon-clock', 'tone' => 'warning', 'href' => route('stock-follow-up')],
        ['label' => 'Pickup queue', 'value' => $dashboard['kpis']['pickupQueue'], 'icon' => 'icon-log-out', 'tone' => 'info', 'href' => route('pickup-work-list')],
        ['label' => 'Urgent stocks', 'value' => $dashboard['kpis']['urgentStocks'], 'icon' => 'icon-zap', 'tone' => 'danger', 'href' => route('stock-follow-up')],
        ['label' => 'Open irregularities', 'value' => $dashboard['kpis']['openIrregularities'], 'icon' => 'icon-alert-circle', 'tone' => 'danger', 'href' => route('shipment-follow-up')],
        ['label' => 'Reminders sent today', 'value' => $dashboard['kpis']['remindersToday'], 'icon' => 'icon-mail', 'tone' => 'success', 'href' => route('pre-alert-reminders')],
    ];
@endphp

                                    <div class="dash-page">
                                    <section class="dash-hero">
                                        <div class="dash-hero__copy">
                                            <p class="dash-hero__eyebrow">Operations overview</p>
                                            <h1 class="dash-hero__title"><span id="dash-greeting">Good morning</span>, {{ $userName }}</h1>
                                            <p class="dash-hero__sub">Live global metrics across stocks, shipments, and follow-ups.</p>
                                        </div>
                                        <form method="GET" action="{{ route('dashboard') }}" class="dash-period" aria-label="Dashboard period">
                                            @foreach([7, 30, 90] as $days)
                                                <button
                                                    type="submit"
                                                    name="period"
                                                    value="{{ $days }}"
                                                    class="dash-period__btn{{ $period === $days ? ' is-active' : '' }}"
                                                >{{ $days }}d</button>
                                            @endforeach
                                        </form>
                                    </section>

                                    @if(!empty($dashboard['isScoped']) && empty($dashboard['hasAssignments']))
                                        <div class="dash-alert" role="status">
                                            Your account is scoped but has no active assignments yet. Metrics may look empty until assignments are configured.
                                        </div>
                                    @endif

                                    <div class="dash-kpi-grid">
                                        @foreach($kpiCards as $index => $card)
                                            <a
                                                href="{{ $card['href'] }}"
                                                class="dash-kpi dash-kpi--{{ $card['tone'] }}"
                                                style="--dash-delay: {{ $index * 40 }}ms"
                                            >
                                                <div class="dash-kpi__icon"><i class="feather {{ $card['icon'] }}"></i></div>
                                                <div class="dash-kpi__body">
                                                    <div class="dash-kpi__label">{{ $card['label'] }}</div>
                                                    <div class="dash-kpi__value">{{ number_format($card['value']) }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="dash-section-head">
                                        <div>
                                            <h2>Activity &amp; mix</h2>
                                            <p>New work over the last {{ $period }} days and current status distribution.</p>
                                        </div>
                                    </div>

                                    <div class="dash-grid">
                                        <div class="dash-grid__main">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head">
                                                    <div>
                                                        <h5>New operational activity</h5>
                                                        <span>Stocks vs shipments created</span>
                                                    </div>
                                                </div>
                                                <div class="dash-chart"><canvas id="activity-chart"></canvas></div>
                                            </div>
                                        </div>
                                        <div class="dash-grid__side">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head">
                                                    <div>
                                                        <h5>Active shipment services</h5>
                                                        <span>By service type</span>
                                                    </div>
                                                </div>
                                                <div class="dash-chart">
                                                    @if($dashboard['serviceSeries']->isEmpty())
                                                        <div class="dash-empty">No active shipment services in this scope.</div>
                                                    @else
                                                        <canvas id="service-chart"></canvas>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dash-grid__half">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head">
                                                    <div>
                                                        <h5>Stock status</h5>
                                                        <span>Current stock pipeline</span>
                                                    </div>
                                                </div>
                                                <div class="dash-chart"><canvas id="stock-status-chart"></canvas></div>
                                            </div>
                                        </div>
                                        <div class="dash-grid__half">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head">
                                                    <div>
                                                        <h5>Shipment status</h5>
                                                        <span>Current shipment pipeline</span>
                                                    </div>
                                                </div>
                                                <div class="dash-chart"><canvas id="shipment-status-chart"></canvas></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dash-section-head">
                                        <div>
                                            <h2>Needs attention</h2>
                                            <p>Overdue arrivals and stocks waiting on follow-up.</p>
                                        </div>
                                    </div>

                                    <div class="dash-grid">
                                        <div class="dash-grid__wide">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head dash-panel__head--row">
                                                    <div>
                                                        <h5>Overdue shipment arrivals</h5>
                                                        <span>Past deadline, still open</span>
                                                    </div>
                                                    <a class="dash-link" href="{{ route('shipment-follow-up') }}">View all</a>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table dash-table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment</th>
                                                                <th>Vessel</th>
                                                                <th>Customer</th>
                                                                <th>Deadline</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($dashboard['overdueShipments'] as $shipment)
                                                                @php
                                                                    $shipmentCustomer = $shipment->crrs
                                                                        ->map(fn($crr) => $crr->customerVessel?->customer?->customer_name)
                                                                        ->filter()->unique()->implode(', ');
                                                                @endphp
                                                                <tr>
                                                                    <td><a href="{{ route('shipments.edit', $shipment->id) }}">{{ $shipment->shipment_number }}</a></td>
                                                                    <td>{{ $shipment->vessel_display }}</td>
                                                                    <td>{{ $shipmentCustomer ?: '—' }}</td>
                                                                    <td class="dash-table__danger">{{ optional($shipment->deadline_arrival)->format('d M Y') }}</td>
                                                                    <td><span class="{{ $shipment->statusBadgeClass() }}">{{ $shipment->status ?: 'Unknown' }}</span></td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="5" class="dash-empty">No overdue shipments.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dash-grid__narrow">
                                            <div class="card dash-panel">
                                                <div class="card-header dash-panel__head dash-panel__head--row">
                                                    <div>
                                                        <h5>Stock follow-up</h5>
                                                        <span>Awaiting acceptance</span>
                                                    </div>
                                                    <a class="dash-link" href="{{ route('stock-follow-up') }}">View all</a>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table dash-table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Stock</th>
                                                                <th>Customer</th>
                                                                <th>Priority</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($dashboard['stockFollowUps'] as $crr)
                                                                <tr>
                                                                    <td><a href="{{ route('stocks.edit', $crr->id) }}">{{ $crr->stock_number }}</a></td>
                                                                    <td>{{ $crr->customerVessel?->customer?->customer_name ?: '—' }}</td>
                                                                    <td>{{ $crr->priority ?: '—' }}</td>
                                                                    <td><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</span></td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="4" class="dash-empty">No stocks need acceptance.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
@include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
<script type="text/javascript" src="{{ asset('files/bower_components/chart.js/dist/Chart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function todForHour(hour) {
            if (hour < 12) return 'morning';
            if (hour < 17) return 'afternoon';
            return 'evening';
        }

        function greetingForHour(hour) {
            if (hour < 12) return 'Good morning';
            if (hour < 17) return 'Good afternoon';
            return 'Good evening';
        }

        function refreshDashGreeting() {
            var hour = new Date().getHours();
            var el = document.getElementById('dash-greeting');
            if (el) el.textContent = greetingForHour(hour);
            var hero = document.querySelector('.dash-hero');
            if (hero) hero.setAttribute('data-tod', todForHour(hour));
        }

        refreshDashGreeting();
        setInterval(refreshDashGreeting, 60 * 1000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) refreshDashGreeting();
        });

        var commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, boxWidth: 8, fontColor: '#64748b' } }
        };
        var stockColors = ['#e8f6fc', '#e87722', '#00aeef', '#ffd166', '#ff5a5f', '#94a3b8', '#0e1d4a'];
        var shipmentColors = ['#e8f6fc', '#e87722', '#ffd166', '#00aeef', '#0088c7', '#94a3b8'];
        var serviceColors = ['#0e1d4a', '#00aeef', '#0088c7', '#38bdf8', '#e87722', '#ff5a5f', '#64748b'];

        function doughnut(id, series, colors) {
            var canvas = document.getElementById(id);
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: series.map(function (item) { return item.label; }),
                    datasets: [{
                        data: series.map(function (item) { return item.value; }),
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: Object.assign({}, commonOptions, { cutoutPercentage: 62 })
            });
        }

        doughnut('stock-status-chart', @json($dashboard['stockStatusSeries']), stockColors);
        doughnut('shipment-status-chart', @json($dashboard['shipmentStatusSeries']), shipmentColors);
        doughnut('service-chart', @json($dashboard['serviceSeries']), serviceColors);

        var activityCanvas = document.getElementById('activity-chart');
        if (activityCanvas) {
            new Chart(activityCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($dashboard['trend']['labels']),
                    datasets: [
                        {
                            label: 'Stocks',
                            data: @json($dashboard['trend']['stocks']),
                            borderColor: '#00aeef',
                            backgroundColor: 'rgba(0, 174, 239, 0.14)',
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            borderWidth: 2.5,
                            lineTension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Shipments',
                            data: @json($dashboard['trend']['shipments']),
                            borderColor: '#0e1d4a',
                            backgroundColor: 'rgba(14, 29, 74, 0.10)',
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            borderWidth: 2.5,
                            lineTension: 0.35,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: commonOptions.legend,
                    tooltips: { mode: 'index', intersect: false },
                    scales: {
                        xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#94a3b8' } }],
                        yAxes: [{
                            ticks: { beginAtZero: true, precision: 0, fontColor: '#94a3b8' },
                            gridLines: { color: 'rgba(215, 226, 234, 0.7)', zeroLineColor: 'rgba(215, 226, 234, 0.9)' }
                        }]
                    }
                }
            });
        }
    });
</script>
@endpush
