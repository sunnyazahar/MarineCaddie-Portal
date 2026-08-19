<?php

namespace App\Repositories;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentIrregularity;
use App\Models\ShipmentPreAlertReminderSend;
use App\Models\User;
use App\Repositories\Contracts\OperationsDashboardRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class OperationsDashboardRepository implements OperationsDashboardRepositoryInterface
{
    public function visibleCrrsQuery(?User $user = null): Builder
    {
        return Crr::query();
    }

    public function visibleShipmentsQuery(?User $user = null): Builder
    {
        return Shipment::query();
    }

    public function openIrregularitiesCountForShipmentQuery(Builder $visibleShipmentIdsQuery): int
    {
        return ShipmentIrregularity::query()
            ->whereIn('shipment_id', $visibleShipmentIdsQuery)
            ->where(fn ($query) => $query
                ->whereNull('status')
                ->orWhere('status', '!=', 'Closed'))
            ->count();
    }

    public function remindersTodayCountForShipmentQuery(Builder $visibleShipmentIdsQuery): int
    {
        return ShipmentPreAlertReminderSend::query()
            ->whereIn('shipment_id', $visibleShipmentIdsQuery)
            ->whereDate('created_at', today())
            ->count();
    }
}
