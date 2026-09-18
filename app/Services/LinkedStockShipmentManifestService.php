<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\Shipment;
use Illuminate\Support\Collection;

class LinkedStockShipmentManifestService
{
    /**
     * Manifest revisions are created only via the manual Generate action on the
     * shipment edit page. Stock changes no longer auto-generate PDFs.
     */
    public function regenerateForCrr(Crr $crr): int
    {
        return 0;
    }

    /**
     * @return Collection<int, Shipment>
     */
    public function eligibleShipments(Crr $crr): Collection
    {
        return $crr->shipments()
            ->whereNotIn('shipments.status', ['Completed', 'Cancelled'])
            ->get();
    }
}
