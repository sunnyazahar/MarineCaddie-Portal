<?php

namespace Tests\Unit;

use App\Models\Crr;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CrrStockListShipmentDisplayTest extends TestCase
{
    public function test_new_stock_inherits_in_progress_shipment_for_same_hub(): void
    {
        $hubInfo = collect([
            'AMS' => ['number' => 'SHIP-AMS-1', 'shipment_id' => 42],
        ]);

        $newStock = new Crr([
            'hub_agent' => 'AMS',
            'status' => Crr::STATUS_NEW,
            'internal_shipment' => null,
        ]);

        $activeStock = new Crr([
            'hub_agent' => 'AMS',
            'status' => Crr::STATUS_ACTIVE,
            'internal_shipment' => '',
        ]);

        $this->assertSame([
            'number' => 'SHIP-AMS-1',
            'inherited' => true,
            'shipment_id' => 42,
        ], $newStock->stockListShipmentColumn($hubInfo));

        $this->assertSame([
            'number' => 'SHIP-AMS-1',
            'inherited' => true,
            'shipment_id' => 42,
        ], $activeStock->stockListShipmentColumn($hubInfo));
    }

    public function test_does_not_override_existing_shipment_or_other_statuses(): void
    {
        $hubInfo = collect([
            'AMS' => ['number' => 'SHIP-AMS-1', 'shipment_id' => 42],
        ]);

        $withOwn = new Crr([
            'hub_agent' => 'AMS',
            'status' => Crr::STATUS_NEW,
            'internal_shipment' => 'OWN-123',
        ]);

        $inProgress = new Crr([
            'hub_agent' => 'AMS',
            'status' => Crr::STATUS_IN_PROGRESS,
            'internal_shipment' => null,
        ]);

        $this->assertSame([
            'number' => 'OWN-123',
            'inherited' => false,
            'shipment_id' => null,
        ], $withOwn->stockListShipmentColumn($hubInfo));

        $this->assertSame([
            'number' => '',
            'inherited' => false,
            'shipment_id' => null,
        ], $inProgress->stockListShipmentColumn($hubInfo));
    }

    public function test_different_hub_does_not_inherit(): void
    {
        $hubInfo = collect([
            'AMS' => ['number' => 'SHIP-AMS-1', 'shipment_id' => 42],
        ]);

        $dxbStock = new Crr([
            'hub_agent' => 'DXB',
            'status' => Crr::STATUS_ACTIVE,
            'internal_shipment' => null,
        ]);

        $this->assertSame([
            'number' => '',
            'inherited' => false,
            'shipment_id' => null,
        ], $dxbStock->stockListShipmentColumn($hubInfo));
    }
}
