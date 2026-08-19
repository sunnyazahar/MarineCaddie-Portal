<?php

namespace App\Repositories\Contracts;

use App\Models\Crr;
use Illuminate\Support\Collection;

interface ShipmentTransitStockRepositoryInterface
{
    /**
     * @param  array<int, int>  $originalIds
     * @return Collection<int, Crr>
     */
    public function existingDuplicates(array $originalIds, string $shipmentNumber): Collection;

    public function createCrr(array $attributes): Crr;

    public function createCrrPackage(array $attributes): void;

    public function createCrrCost(array $attributes): void;
}
