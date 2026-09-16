<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\CustomerVessel;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CrrStockListShipmentDisplayTest extends TestCase
{
    public function test_new_stock_inherits_in_progress_shipment_when_hub_vessel_location_and_customer_match(): void
    {
        $newStock = $this->matchingStock(['status' => Crr::STATUS_NEW]);
        $activeStock = $this->matchingStock(['status' => Crr::STATUS_ACTIVE, 'internal_shipment' => '']);
        $hubInfo = $this->infoFor($newStock, 'SHIP-AMS-1', 42);

        $expected = [
            'number' => 'SHIP-AMS-1',
            'inherited' => true,
            'shipment_id' => 42,
        ];

        $this->assertSame($expected, $newStock->stockListShipmentColumn($hubInfo));
        $this->assertSame($expected, $activeStock->stockListShipmentColumn($hubInfo));
    }

    public function test_does_not_override_existing_shipment_or_other_statuses(): void
    {
        $withOwn = $this->matchingStock([
            'status' => Crr::STATUS_NEW,
            'internal_shipment' => 'OWN-123',
        ]);
        $inProgress = $this->matchingStock([
            'status' => Crr::STATUS_IN_PROGRESS,
            'internal_shipment' => null,
        ]);
        $hubInfo = $this->infoFor($withOwn, 'SHIP-AMS-1', 42);

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

    public function test_different_hub_vessel_location_or_customer_does_not_inherit(): void
    {
        $source = $this->matchingStock();
        $hubInfo = $this->infoFor($source, 'SHIP-AMS-1', 42);
        $empty = [
            'number' => '',
            'inherited' => false,
            'shipment_id' => null,
        ];

        $differentHub = $this->matchingStock(['hub_agent' => 'DXB']);
        $differentVessel = $this->matchingStock(['vessel_name' => 'Other Vessel']);
        $differentLocation = $this->matchingStock(['location' => 'Shed B']);
        $differentCustomer = $this->matchingStock();
        $differentCustomer->setRelation('customerVessel', new CustomerVessel(['customer_id' => 10]));

        $this->assertSame($empty, $differentHub->stockListShipmentColumn($hubInfo));
        $this->assertSame($empty, $differentVessel->stockListShipmentColumn($hubInfo));
        $this->assertSame($empty, $differentLocation->stockListShipmentColumn($hubInfo));
        $this->assertSame($empty, $differentCustomer->stockListShipmentColumn($hubInfo));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function matchingStock(array $overrides = []): Crr
    {
        $stock = new Crr(array_merge([
            'hub_agent' => 'AMS',
            'vessel_name' => 'Angel',
            'location' => 'Shed A',
            'status' => Crr::STATUS_NEW,
            'internal_shipment' => null,
        ], $overrides));
        $stock->setRelation('customerVessel', new CustomerVessel(['customer_id' => 9]));

        return $stock;
    }

    private function infoFor(Crr $stock, string $number, int $shipmentId): Collection
    {
        return collect([
            $stock->inheritedShipmentGroupKey() => ['number' => $number, 'shipment_id' => $shipmentId],
        ]);
    }
}
