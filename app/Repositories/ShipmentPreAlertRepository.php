<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\ShipmentPreAlert;
use App\Repositories\Contracts\ShipmentPreAlertRepositoryInterface;

class ShipmentPreAlertRepository implements ShipmentPreAlertRepositoryInterface
{
    public function lockShipment(int $shipmentId): void
    {
        Shipment::query()->whereKey($shipmentId)->lockForUpdate()->first();
    }

    public function nextVersionForShipment(int $shipmentId): int
    {
        return (int) ShipmentPreAlert::query()
            ->where('shipment_id', $shipmentId)
            ->max('version') + 1;
    }

    public function create(array $attributes): ShipmentPreAlert
    {
        return ShipmentPreAlert::create($attributes);
    }
}
