<?php

namespace App\Repositories;

use App\Models\Crr;
use App\Models\CrrCost;
use App\Models\CrrPackage;
use App\Repositories\Contracts\ShipmentTransitStockRepositoryInterface;
use Illuminate\Support\Collection;

class ShipmentTransitStockRepository implements ShipmentTransitStockRepositoryInterface
{
    public function existingDuplicates(array $originalIds, string $shipmentNumber): Collection
    {
        return Crr::query()
            ->whereIn('duplicated_from_crr_id', $originalIds)
            ->where('internal_shipment', $shipmentNumber)
            ->get()
            ->keyBy('duplicated_from_crr_id');
    }

    public function createCrr(array $attributes): Crr
    {
        return Crr::create($attributes);
    }

    public function createCrrPackage(array $attributes): void
    {
        CrrPackage::create($attributes);
    }

    public function createCrrCost(array $attributes): void
    {
        CrrCost::create($attributes);
    }
}
