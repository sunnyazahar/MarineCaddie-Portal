<?php

namespace App\Repositories\Contracts;

use App\Models\Crr;
use Illuminate\Support\Collection;

interface ShipmentTransitStockRepositoryInterface
{
    /**
     * Destination copies created FROM the given original CRR ids
     * (excludes the originals themselves).
     *
     * @param  array<int, int>  $originalIds
     * @return Collection<int, Crr>
     */
    public function existingDuplicates(array $originalIds): Collection;

    public function createCrr(array $attributes): Crr;

    public function createCrrPackage(array $attributes): void;

    public function createCrrCost(array $attributes): void;
}
