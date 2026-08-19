<?php

namespace App\Repositories\Contracts;

use App\Models\ShipmentManifest;

interface ShipmentManifestRepositoryInterface
{
    public function lockShipment(int $shipmentId): void;

    public function nextVersionForShipment(int $shipmentId): int;

    public function create(array $attributes): ShipmentManifest;
}
