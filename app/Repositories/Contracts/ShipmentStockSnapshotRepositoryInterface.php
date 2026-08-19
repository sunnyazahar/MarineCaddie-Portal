<?php

namespace App\Repositories\Contracts;

use App\Models\ShipmentStockSnapshot;
use Illuminate\Support\Collection;

interface ShipmentStockSnapshotRepositoryInterface
{
    public function create(array $attributes): ShipmentStockSnapshot;

    public function latestCrrIdsByStockNumbers(Collection $stockNumbers): Collection;
}
