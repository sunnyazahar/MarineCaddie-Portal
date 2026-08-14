@extends('layouts.app')

@section('styles')
<style>
    .dashboard-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:22px; }
    .dashboard-heading h3 { margin:0; font-weight:700; color:#263544; }
    .dashboard-heading p { margin:5px 0 0; color:#718096; }
    .period-selector { min-width:145px; }
    .ops-kpi { border:0; border-radius:12px; box-shadow:0 4px 18px rgba(38,53,68,.08); overflow:hidden; }
    .ops-kpi .card-block { padding:19px; }
    .ops-kpi-label { color:#718096; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
    .ops-kpi-value { color:#263544; font-size:28px; font-weight:700; line-height:1.2; margin-top:7px; }
    .ops-kpi-icon { align-items:center; background:#eff6ff; border-radius:10px; color:#1b8bf9; display:flex; height:46px; justify-content:center; width:46px; }
    .ops-kpi.is-warning .ops-kpi-icon { background:#fff7ed; color:#d97706; }
    .ops-kpi.is-danger .ops-kpi-icon { background:#fff1f2; color:#e54a39; }
    .ops-kpi.is-success .ops-kpi-icon { background:#ecfdf5; color:#159467; }
    .dashboard-card { border:0; border-radius:12px; box-shadow:0 4px 18px rgba(38,53,68,.08); }
    .dashboard-card .card-header { border-bottom:1px solid #edf2f7; padding:18px 20px; }
    .dashboard-card .card-header h5 { font-weight:700; margin:0; }
    .chart-wrap { height:290px; padding:18px; position:relative; }
    .dashboard-table th { color:#718096; font-size:11px; letter-spacing:.35px; text-transform:uppercase; white-space:nowrap; }
    .dashboard-table td { vertical-align:middle; }
    .dashboard-empty { color:#94a3b8; padding:32px 16px; text-align:center; }
    .scope-alert { background:#fff8e7; border:1px solid #f6d884; border-radius:10px; color:#775e14; margin-bottom:20px; padding:14px 16px; }
    @media (max-width:767px) {
        .dashboard-heading { align-items:flex-start; flex-direction:column; }
        .period-selector { width:100%; }
        .chart-wrap { height:245px; }
    }
</style>
@endsection

@section('content')
@include('layouts.partials.pcoded-shell-start')
                                    <div class="dashboard-heading">
                                        <div>
                                            <h3>Shipment Dashboard</h3>
                                            <p>Global operational overview.</p>
                                        </div>
                                        <form method="GET" action="{{ route('dashboard') }}" class="period-selector">
                                            <select name="period" class="form-control" onchange="this.form.submit()" aria-label="Dashboard period">
                                                @foreach([7, 30, 90] as $days)
                                                    <option value="{{ $days }}" {{ $dashboard['period'] === $days ? 'selected' : '' }}>Last {{ $days }} days</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>

                                    @php
                                        $kpiCards = [
                                            ['label' => 'Active stocks', 'value' => $dashboard['kpis']['activeStocks'], 'icon' => 'icon-package', 'class' => 'is-success'],
                                            ['label' => 'Active shipments', 'value' => $dashboard['kpis']['activeShipments'], 'icon' => 'icon-truck', 'class' => ''],
                                            ['label' => 'Overdue arrivals', 'value' => $dashboard['kpis']['overdueArrivals'], 'icon' => 'icon-alert-triangle', 'class' => 'is-danger'],
                                            ['label' => 'Pre-alerts due', 'value' => $dashboard['kpis']['preAlertsDue'], 'icon' => 'icon-bell', 'class' => 'is-warning'],
                                            ['label' => 'Unaccepted stocks', 'value' => $dashboard['kpis']['unacceptedStocks'], 'icon' => 'icon-clock', 'class' => 'is-warning'],
                                            ['label' => 'Pickup queue', 'value' => $dashboard['kpis']['pickupQueue'], 'icon' => 'icon-log-out', 'class' => ''],
                                            ['label' => 'Urgent stocks', 'value' => $dashboard['kpis']['urgentStocks'], 'icon' => 'icon-zap', 'class' => 'is-danger'],
                                            ['label' => 'Open irregularities', 'value' => $dashboard['kpis']['openIrregularities'], 'icon' => 'icon-alert-circle', 'class' => 'is-danger'],
                                            ['label' => 'Reminders sent today', 'value' => $dashboard['kpis']['remindersToday'], 'icon' => 'icon-mail', 'class' => 'is-success'],
                                        ];
                                    @endphp

                                    <div class="row">
                                        @foreach($kpiCards as $card)
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <div class="card ops-kpi {{ $card['class'] }}">
                                                    <div class="card-block">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <div class="ops-kpi-label">{{ $card['label'] }}</div>
                                                                <div class="ops-kpi-value">{{ number_format($card['value']) }}</div>
                                                            </div>
                                                            <div class="ops-kpi-icon"><i class="feather {{ $card['icon'] }} f-24"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-8 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header"><h5>New operational activity</h5></div>
                                                <div class="chart-wrap"><canvas id="activity-chart"></canvas></div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header"><h5>Active shipment services</h5></div>
                                                <div class="chart-wrap">
                                                    @if($dashboard['serviceSeries']->isEmpty())
                                                        <div class="dashboard-empty">No active shipment services in this scope.</div>
                                                    @else
                                                        <canvas id="service-chart"></canvas>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header"><h5>Stock status</h5></div>
                                                <div class="chart-wrap"><canvas id="stock-status-chart"></canvas></div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header"><h5>Shipment status</h5></div>
                                                <div class="chart-wrap"><canvas id="shipment-status-chart"></canvas></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-7 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5>Overdue shipment arrivals</h5>
                                                    <a href="{{ route('shipment-follow-up') }}">View all</a>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table dashboard-table mb-0">
                                                        <thead><tr><th>Shipment</th><th>Vessel</th><th>Customer</th><th>Deadline</th><th>Status</th></tr></thead>
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
                                                                    <td class="text-danger">{{ optional($shipment->deadline_arrival)->format('d M Y') }}</td>
                                                                    <td><span class="{{ $shipment->statusBadgeClass() }}">{{ $shipment->status ?: 'Unknown' }}</span></td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="5" class="dashboard-empty">No overdue shipments.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-md-12">
                                            <div class="card dashboard-card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5>Stock follow-up</h5>
                                                    <a href="{{ route('stock-follow-up') }}">View all</a>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table dashboard-table mb-0">
                                                        <thead><tr><th>Stock</th><th>Customer</th><th>Priority</th><th>Status</th></tr></thead>
                                                        <tbody>
                                                            @forelse($dashboard['stockFollowUps'] as $crr)
                                                                <tr>
                                                                    <td><a href="{{ route('stocks.edit', $crr->id) }}">{{ $crr->stock_number }}</a></td>
                                                                    <td>{{ $crr->customerVessel?->customer?->customer_name ?: '—' }}</td>
                                                                    <td>{{ $crr->priority ?: '—' }}</td>
                                                                    <td><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}</span></td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="4" class="dashboard-empty">No stocks need acceptance.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
@include('layouts.partials.pcoded-shell-end')

<script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/chart.js/dist/Chart.js') }}"></script>
<script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
<script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
<script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } }
        };
        var stockColors = ['#ffffff', '#edbe9a', '#70eab5', '#f7f776', '#e54a39', '#cfd1d0', '#8392a5'];
        var shipmentColors = ['#ffffff', '#edbe9a', '#f7f776', '#1b8bf9', '#70eab5', '#cfd1d0'];
        var serviceColors = ['#1b8bf9', '#70eab5', '#edbe9a', '#7c5cff', '#ef6c9e', '#19b5a5', '#8392a5'];

        function doughnut(id, series, colors) {
            var canvas = document.getElementById(id);
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: series.map(function (item) { return item.label; }),
                    datasets: [{ data: series.map(function (item) { return item.value; }), backgroundColor: colors, borderColor: '#dce3ea', borderWidth: 1 }]
                },
                options: commonOptions
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
                        { label: 'Stocks', data: @json($dashboard['trend']['stocks']), borderColor: '#159467', backgroundColor: 'rgba(112,234,181,.16)', pointRadius: 2, fill: true },
                        { label: 'Shipments', data: @json($dashboard['trend']['shipments']), borderColor: '#1b8bf9', backgroundColor: 'rgba(27,139,249,.10)', pointRadius: 2, fill: true }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: commonOptions.legend,
                    scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] }
                }
            });
        }
    });
</script>
@endsection
