<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Customer;
use App\Models\CustomerVessel;
use App\Models\Shipment;
use App\Models\ShipmentStockSnapshot;
use App\Repositories\Contracts\ShipmentStockSnapshotRepositoryInterface;
use Illuminate\Support\Collection;

class ShipmentStockSnapshotService
{
    public function __construct(
        private ShipmentStockSnapshotRepositoryInterface $stockSnapshots,
    ) {}

    public function snapshotShipmentStocks(Shipment $shipment): void
    {
        if ($shipment->stockSnapshots()->exists()) {
            return;
        }

        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
        ]);

        foreach ($shipment->crrs as $index => $crr) {
            $packages = $crr->packages;
            $customerVessel = $crr->customerVessel;

            $this->stockSnapshots->create([
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'original_crr_id' => $crr->id,
                'sort_order' => $index,
                'hub_code' => $crr->hub_code,
                'vessel_name' => $crr->vessel_name,
                'po_numbers' => $crr->po_numbers,
                'supplier' => $crr->supplier,
                'stock_number' => $crr->stock_number,
                'pieces_count' => $packages->count(),
                'total_weight' => round((float) $packages->sum('weight'), 2),
                'total_cbm' => round((float) $packages->sum('cbm'), 4),
                'customs_value' => $crr->customs_value,
                'currency' => $crr->currency,
                'status_label' => Crr::getStatusLabels()[(int) $crr->status] ?? 'Completed',
                'snapshot_data' => [
                    'crr' => $crr->getAttributes(),
                    'packages' => $packages->map->getAttributes()->values()->all(),
                    'customer_vessel' => $customerVessel ? [
                        'vessel' => $customerVessel->vessel,
                        'vessel_imo' => $customerVessel->vessel_imo,
                        'not_in_transit' => $customerVessel->not_in_transit,
                        'customer' => $customerVessel->customer ? [
                            'customer_name' => $customerVessel->customer->customer_name,
                        ] : null,
                    ] : null,
                ],
            ]);
        }
    }

    public function resolveStockCrrs(Shipment $shipment): Collection
    {
        if ($shipment->status === 'Completed') {
            $shipment->loadMissing('stockSnapshots');

            if ($shipment->stockSnapshots->isNotEmpty()) {
                $snapshots = $shipment->stockSnapshots
                    ->sortBy('sort_order')
                    ->values();
                $resolvedIds = $this->stockSnapshots->latestCrrIdsByStockNumbers($snapshots->pluck('stock_number'));

                return $snapshots->map(
                    fn (ShipmentStockSnapshot $snapshot) => $this->snapshotToCrrModel($snapshot, $resolvedIds)
                );
            }
        }

        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
        ]);

        return $shipment->crrs;
    }

    public function applyResolvedStockCrrs(Shipment $shipment): Collection
    {
        $crrs = $this->resolveStockCrrs($shipment);
        $shipment->setRelation('crrs', $crrs);

        return $crrs;
    }

    public function snapshotToCrrModel(ShipmentStockSnapshot $snapshot, ?Collection $resolvedIds = null): Crr
    {
        $data = $snapshot->snapshot_data ?? [];
        $crrAttributes = $data['crr'] ?? [];

        $crrAttributes['id'] = $snapshot->original_crr_id
            ?? $resolvedIds?->get($snapshot->stock_number);
        $crrAttributes['stock_number'] = $snapshot->stock_number ?? ($crrAttributes['stock_number'] ?? null);
        $crrAttributes['vessel_name'] = $snapshot->vessel_name ?? ($crrAttributes['vessel_name'] ?? null);
        $crrAttributes['supplier'] = $snapshot->supplier ?? ($crrAttributes['supplier'] ?? null);
        $crrAttributes['po_numbers'] = $snapshot->po_numbers ?? ($crrAttributes['po_numbers'] ?? null);
        $crrAttributes['customs_value'] = $snapshot->customs_value ?? ($crrAttributes['customs_value'] ?? null);
        $crrAttributes['currency'] = $snapshot->currency ?? ($crrAttributes['currency'] ?? null);
        $crrAttributes['status'] = Crr::STATUS_COMPLETED;

        $crr = (new Crr())->forceFill($crrAttributes);
        $crr->exists = true;

        $packages = collect($data['packages'] ?? [])->map(function (array $packageAttributes) use ($crr) {
            $package = new CrrPackage($packageAttributes);
            $package->exists = true;
            $package->setRelation('crr', $crr);

            return $package;
        });

        $crr->setRelation('packages', $packages);

        if (!empty($data['customer_vessel'])) {
            $vesselData = $data['customer_vessel'];
            $customerVessel = new CustomerVessel([
                'vessel' => $vesselData['vessel'] ?? $snapshot->vessel_name,
                'vessel_imo' => $vesselData['vessel_imo'] ?? null,
                'not_in_transit' => $vesselData['not_in_transit'] ?? false,
            ]);
            $customerVessel->exists = true;

            if (!empty($vesselData['customer']['customer_name'])) {
                $customer = new Customer([
                    'customer_name' => $vesselData['customer']['customer_name'],
                ]);
                $customer->exists = true;
                $customerVessel->setRelation('customer', $customer);
            }

            $crr->setRelation('customerVessel', $customerVessel);
        }

        return $crr;
    }
}
