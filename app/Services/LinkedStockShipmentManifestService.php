<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\Shipment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LinkedStockShipmentManifestService
{
    public function __construct(
        private ShipmentManifestService $manifestService,
        private ShipmentChangeLogService $changeLogService,
    ) {}

    /**
     * After a stock change, create a new manifest version on every linked
     * shipment that is not Completed or Cancelled.
     */
    public function regenerateForCrr(Crr $crr): int
    {
        $generated = 0;
        $stockLabel = $crr->stock_number ?: ('#' . $crr->id);

        foreach ($this->eligibleShipments($crr) as $shipment) {
            try {
                $fresh = $shipment->fresh([
                    'crrs.packages',
                    'crrs.customerVessel.customer',
                    'accountManager.office',
                    'creator',
                ]);

                if (! $fresh || $fresh->crrs->isEmpty()) {
                    continue;
                }

                if (in_array($fresh->status, ['Completed', 'Cancelled'], true)) {
                    continue;
                }

                $manifest = $this->manifestService->generate($fresh);
                if (! $manifest) {
                    continue;
                }

                $this->changeLogService->log(
                    $fresh,
                    $manifest->version > 1 ? 'Revision created' : 'Manifest generated',
                    ($manifest->version > 1
                        ? 'Revision ' . $manifest->version
                        : $manifest->file_name . '.pdf')
                    . ' (stock ' . $stockLabel . ' updated)'
                );

                $generated++;
            } catch (\Throwable $e) {
                Log::warning(
                    'Manifest generation after stock update failed for shipment '
                    . ($shipment->shipment_number ?? $shipment->id)
                    . ': ' . $e->getMessage()
                );
            }
        }

        return $generated;
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
