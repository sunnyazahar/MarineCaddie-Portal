<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentIrregularity;
use App\Models\ShipmentPreAlertReminderSend;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OperationsDashboardService
{
    public function build(User $user, int $period = 30): array
    {
        $period = in_array($period, [7, 30, 90], true) ? $period : 30;
        $crrs = $this->visibleCrrs($user);
        $shipments = $this->visibleShipments($user);
        $activeCrrStatuses = [Crr::STATUS_NEW, Crr::STATUS_PENDING, Crr::STATUS_ACTIVE, Crr::STATUS_IN_PROGRESS];
        $activeShipmentStatuses = ['Draft', 'Pending', 'In process', 'In transit', 'Delivered'];

        $stockStatusCounts = (clone $crrs)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $shipmentStatusCounts = (clone $shipments)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stockStatusSeries = collect(Crr::getStatusLabels())->map(fn ($label, $status) => [
            'label' => $label,
            'value' => (int) ($stockStatusCounts[$status] ?? 0),
            'class' => Crr::statusBadgeClass($status),
        ])->values();

        $shipmentStatusSeries = collect(['Draft', 'Pending', 'In process', 'In transit', 'Delivered', 'Completed'])
            ->map(fn ($status) => [
                'label' => $status,
                'value' => (int) ($shipmentStatusCounts[$status] ?? 0),
                'class' => Shipment::statusColorClass($status),
            ]);

        $periodStart = today()->subDays($period - 1);
        $stockDaily = (clone $crrs)
            ->whereDate('created_at', '>=', $periodStart)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $shipmentDaily = (clone $shipments)
            ->whereDate('created_at', '>=', $periodStart)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $trendLabels = collect(range(0, $period - 1))
            ->map(fn ($offset) => $periodStart->copy()->addDays($offset)->format('Y-m-d'));

        $visibleShipmentIds = (clone $shipments)->select('shipments.id');

        return [
            'period' => $period,
            'isScoped' => false,
            'hasAssignments' => true,
            'kpis' => [
                'activeStocks' => (clone $crrs)->whereIn('status', $activeCrrStatuses)->count(),
                'unacceptedStocks' => (clone $crrs)
                    ->where('accept', false)
                    ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
                    ->count(),
                'pickupQueue' => (clone $crrs)
                    ->whereJsonContains('flags', 'Pick up')
                    ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
                    ->count(),
                'urgentStocks' => (clone $crrs)
                    ->whereIn('priority', ['Urgent', 'Critical', 'Prevent offhire'])
                    ->whereIn('status', $activeCrrStatuses)
                    ->count(),
                'activeShipments' => (clone $shipments)->whereIn('status', $activeShipmentStatuses)->count(),
                'overdueArrivals' => (clone $shipments)
                    ->whereIn('status', $activeShipmentStatuses)
                    ->whereNotNull('deadline_arrival')
                    ->whereDate('deadline_arrival', '<', today())
                    ->count(),
                'preAlertsDue' => (clone $shipments)
                    ->whereNotIn('status', ['Completed', 'Cancelled'])
                    ->whereNotNull('pre_alert_reminder')
                    ->whereDate('pre_alert_reminder', '<=', today())
                    ->count(),
                'openIrregularities' => ShipmentIrregularity::query()
                    ->whereIn('shipment_id', clone $visibleShipmentIds)
                    ->where(fn ($query) => $query
                        ->whereNull('status')
                        ->orWhere('status', '!=', 'Closed'))
                    ->count(),
                'remindersToday' => ShipmentPreAlertReminderSend::query()
                    ->whereIn('shipment_id', $visibleShipmentIds)
                    ->whereDate('created_at', today())
                    ->count(),
            ],
            'stockStatusSeries' => $stockStatusSeries,
            'shipmentStatusSeries' => $shipmentStatusSeries,
            'serviceSeries' => (clone $shipments)
                ->whereIn('status', $activeShipmentStatuses)
                ->whereNotNull('service')
                ->selectRaw('service, COUNT(*) as total')
                ->groupBy('service')
                ->orderByDesc('total')
                ->pluck('total', 'service')
                ->map(fn ($total, $service) => ['label' => $service, 'value' => (int) $total])
                ->values(),
            'trend' => [
                'labels' => $trendLabels->map(fn ($day) => Carbon::parse($day)->format('d M')),
                'stocks' => $trendLabels->map(fn ($day) => (int) ($stockDaily[$day] ?? 0)),
                'shipments' => $trendLabels->map(fn ($day) => (int) ($shipmentDaily[$day] ?? 0)),
            ],
            'overdueShipments' => (clone $shipments)
                ->with(['accountManager', 'crrs.customerVessel.customer'])
                ->whereIn('status', $activeShipmentStatuses)
                ->whereNotNull('deadline_arrival')
                ->whereDate('deadline_arrival', '<', today())
                ->orderBy('deadline_arrival')
                ->limit(8)
                ->get(),
            'stockFollowUps' => (clone $crrs)
                ->with(['customerVessel.customer', 'packages'])
                ->where('accept', false)
                ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
                ->latest('updated_at')
                ->limit(8)
                ->get(),
        ];
    }

    public function visibleCrrs(?User $user = null): Builder
    {
        return Crr::query();
    }

    public function visibleShipments(?User $user = null): Builder
    {
        return Shipment::query();
    }
}
