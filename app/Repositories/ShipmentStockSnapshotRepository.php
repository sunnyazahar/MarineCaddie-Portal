<?php

namespace App\Repositories;

use App\Models\Crr;
use App\Models\ShipmentStockSnapshot;
use App\Repositories\Contracts\ShipmentStockSnapshotRepositoryInterface;
use Illuminate\Support\Collection;

class ShipmentStockSnapshotRepository implements ShipmentStockSnapshotRepositoryInterface
{
    public function create(array $attributes): ShipmentStockSnapshot
    {
        return ShipmentStockSnapshot::create($attributes);
    }

    public function latestCrrIdsByStockNumbers(Collection $stockNumbers): Collection
    {
        return Crr::query()
            ->whereIn('stock_number', $stockNumbers->filter()->unique())
            ->orderByDesc('id')
            ->get(['id', 'stock_number'])
            ->unique('stock_number')
            ->pluck('id', 'stock_number');
    }
}
