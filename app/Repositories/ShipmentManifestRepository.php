<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\ShipmentManifest;
use App\Repositories\Contracts\ShipmentManifestRepositoryInterface;

class ShipmentManifestRepository implements ShipmentManifestRepositoryInterface
{
    public function lockShipment(int $shipmentId): void
    {
        Shipment::query()->whereKey($shipmentId)->lockForUpdate()->first();
    }

    public function nextVersionForShipment(int $shipmentId): int
    {
        return (int) ShipmentManifest::query()
            ->where('shipment_id', $shipmentId)
            ->max('version') + 1;
    }

    public function create(array $attributes): ShipmentManifest
    {
        return ShipmentManifest::create($attributes);
    }
}
