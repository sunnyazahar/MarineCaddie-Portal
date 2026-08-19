<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface OperationsDashboardRepositoryInterface
{
    public function visibleCrrsQuery(?User $user = null): Builder;

    public function visibleShipmentsQuery(?User $user = null): Builder;

    public function openIrregularitiesCountForShipmentQuery(Builder $visibleShipmentIdsQuery): int;

    public function remindersTodayCountForShipmentQuery(Builder $visibleShipmentIdsQuery): int;
}
