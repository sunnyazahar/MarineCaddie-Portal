<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentStockSnapshot;
use App\Services\ShipmentStockSnapshotService;
use Tests\RegressionTestCase;

class ShipmentStockSnapshotResolveTest extends RegressionTestCase
{
    public function test_completed_shipment_prefers_live_crr_when_snapshot_stock_number_diverges(): void
    {
        $live = Crr::create([
            'stock_number' => 'ICN-37554698',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_COMPLETED,
            'accept' => false,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-SNAP-1',
            'status' => 'Completed',
        ]);
        $shipment->crrs()->attach($live->id);

        ShipmentStockSnapshot::create([
            'shipment_id' => $shipment->id,
            'shipment_number' => 'SHIP-SNAP-1',
            'original_crr_id' => $live->id,
            'sort_order' => 0,
            'stock_number' => 'BOM-78172374',
            'snapshot_data' => [
                'crr' => [
                    'id' => $live->id,
                    'stock_number' => 'BOM-78172374',
                    'status' => Crr::STATUS_COMPLETED,
                ],
                'packages' => [],
            ],
        ]);

        $resolved = app(ShipmentStockSnapshotService::class)->resolveStockCrrs($shipment->fresh());

        $this->assertCount(1, $resolved);
        $this->assertSame('ICN-37554698', $resolved->first()->stock_number);
        $this->assertSame((int) $live->id, (int) $resolved->first()->id);
    }

    public function test_completed_shipment_uses_snapshot_when_stock_number_still_matches(): void
    {
        $live = Crr::create([
            'stock_number' => 'AMS-MATCH-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_COMPLETED,
            'accept' => false,
            'supplier' => 'Live Supplier',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-SNAP-2',
            'status' => 'Completed',
        ]);
        $shipment->crrs()->attach($live->id);

        ShipmentStockSnapshot::create([
            'shipment_id' => $shipment->id,
            'shipment_number' => 'SHIP-SNAP-2',
            'original_crr_id' => $live->id,
            'sort_order' => 0,
            'stock_number' => 'AMS-MATCH-1',
            'supplier' => 'Frozen Supplier',
            'snapshot_data' => [
                'crr' => [
                    'id' => $live->id,
                    'stock_number' => 'AMS-MATCH-1',
                    'supplier' => 'Frozen Supplier',
                    'status' => Crr::STATUS_COMPLETED,
                ],
                'packages' => [],
            ],
        ]);

        $resolved = app(ShipmentStockSnapshotService::class)->resolveStockCrrs($shipment->fresh());

        $this->assertCount(1, $resolved);
        $this->assertSame('AMS-MATCH-1', $resolved->first()->stock_number);
        $this->assertSame('Frozen Supplier', $resolved->first()->supplier);
    }
}
