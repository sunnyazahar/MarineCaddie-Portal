<?php

namespace Tests\Feature\Shipment;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Hub;
use App\Models\Shipment;
use App\Models\ShipmentTruckLeg;
use Tests\RegressionTestCase;

class ShipmentFinalizeStockDuplicationTest extends RegressionTestCase
{
    public function test_hub_airfreight_complete_does_not_create_destination_stocks(): void
    {
        $user = $this->createAdminUser();

        $hub = Hub::create([
            'hub_name' => 'Amsterdam Hub',
            'code' => 'AMS',
        ]);

        $original = Crr::create([
            'stock_number' => 'AMS-COMPLETE-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'hub_agent' => 'OLD',
            'accept' => false,
        ]);

        CrrPackage::create([
            'crr_id' => $original->id,
            'weight' => 12.5,
            'cbm' => 0.4,
            'warehouse_location' => 'BIN-A',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-COMPLETE-1',
            'status' => 'In process',
            'service' => 'Airfreight',
            'consignee' => 'hub:' . $hub->id,
            'deadline_arrival' => '2026-09-01',
        ]);
        $shipment->crrs()->attach($original->id);

        $response = $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-COMPLETE-1',
            'action' => 'complete',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status' => 'Completed',
        ]);

        $original->refresh();
        $this->assertSame(Crr::STATUS_COMPLETED, (int) $original->status);
        $this->assertSame([(int) $original->id], array_map('intval', $shipment->fresh()->crrs()->pluck('crrs.id')->all()));

        $this->assertSame(
            0,
            Crr::query()
                ->where('duplicated_from_crr_id', $original->id)
                ->count()
        );
    }

    public function test_hub_truck_transit_creates_destination_stocks_after_complete(): void
    {
        $user = $this->createAdminUser();

        $hub = Hub::create([
            'hub_name' => 'Amsterdam Hub',
            'code' => 'AMS',
        ]);

        $original = Crr::create([
            'stock_number' => 'AMS-TRANSIT-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'internal_shipment' => 'SHIP-TRANSIT-1',
            'accept' => false,
        ]);

        CrrPackage::create([
            'crr_id' => $original->id,
            'weight' => 5,
            'cbm' => 0.1,
            'warehouse_location' => 'BIN-A',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-TRANSIT-1',
            'status' => 'In process',
            'service' => 'Truck',
            'consignee' => 'hub:' . $hub->id,
            'deadline_arrival' => '2026-08-01',
        ]);
        $shipment->crrs()->attach($original->id);

        ShipmentTruckLeg::create([
            'shipment_id' => $shipment->id,
            'cmr' => 'CMR-TRUCK-1',
            'arrival_date' => '2026-09-15',
            'sort_order' => 0,
        ]);

        $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-TRANSIT-1',
            'action' => 'complete',
        ])->assertOk();

        $this->assertSame(
            0,
            Crr::query()
                ->where('duplicated_from_crr_id', $original->id)
                ->count()
        );

        $response = $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-TRANSIT-1',
            'action' => 'transit',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status' => 'Completed',
        ]);

        // Transit keeps prior status (Complete already set Completed).
        $this->assertSame('Completed', $shipment->fresh()->status);

        $duplicate = Crr::query()
            ->where('duplicated_from_crr_id', $original->id)
            ->first();

        $this->assertNotNull($duplicate);
        $this->assertSame(Crr::STATUS_ACTIVE, (int) $duplicate->status);
        $this->assertNull($duplicate->internal_shipment);
        $this->assertSame('CMR', $duplicate->transit_type);
        $this->assertSame('AMS', $duplicate->hub_agent);
        $this->assertSame('SHIP-TRANSIT-1', $duplicate->packages()->value('warehouse_location'));
        $this->assertSame('2026-09-15', optional($duplicate->expected_delivery_date)->format('Y-m-d') ?? (string) $duplicate->expected_delivery_date);
        $this->assertSame('2026-09-15', optional($duplicate->actual_delivery_date)->format('Y-m-d') ?? (string) $duplicate->actual_delivery_date);
        // Shipment keeps showing the completed original stock, not the destination copy.
        $this->assertSame([(int) $original->id], $shipment->fresh()->crrs()->pluck('crrs.id')->map(fn ($id) => (int) $id)->all());
        $this->assertSame(Crr::STATUS_COMPLETED, (int) $original->fresh()->status);
    }

    public function test_multi_leg_prior_duplicate_stock_does_not_block_transit_on_new_shipment(): void
    {
        $user = $this->createAdminUser();

        $bom = Hub::create(['hub_name' => 'BOM Hub', 'code' => 'BOM']);
        $sin = Hub::create(['hub_name' => 'SIN Hub', 'code' => 'SIN']);

        // Leg-1 destination stock (already a duplicate), now used as source on leg-2.
        $bomStock = Crr::create([
            'stock_number' => 'DXB-MULTI-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'hub_agent' => 'BOM',
            'duplicated_from_crr_id' => 999,
            'accept' => false,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-MULTI-2',
            'status' => 'In process',
            'service' => 'Sea freight',
            'departure' => 'hub:' . $bom->id,
            'consignee' => 'hub:' . $sin->id,
        ]);
        $shipment->crrs()->attach($bomStock->id);

        // Attaching sets internal_shipment to this new shipment number (same as live attach flow).
        $bomStock->update(['internal_shipment' => 'SHIP-MULTI-2']);

        $service = app(\App\Services\ShipmentTransitStockDuplicationService::class);
        $this->assertFalse($service->hasDestinationStocksForShipment($shipment->fresh()));

        $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-MULTI-2',
            'action' => 'complete',
        ])->assertOk();

        $response = $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-MULTI-2',
            'action' => 'transit',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status' => 'Completed',
        ]);

        $this->assertSame('Completed', $shipment->fresh()->status);

        $sinStock = Crr::query()
            ->where('duplicated_from_crr_id', $bomStock->id)
            ->first();

        $this->assertNotNull($sinStock);
        $this->assertSame(Crr::STATUS_ACTIVE, (int) $sinStock->status);
        $this->assertNull($sinStock->internal_shipment);
        $this->assertSame('SIN', $sinStock->hub_agent);
        $this->assertSame('DXB-MULTI-1', $sinStock->stock_number);
        $this->assertTrue($service->hasDestinationStocksForShipment($shipment->fresh()));
    }

    public function test_transit_keeps_in_transit_status_unchanged(): void
    {
        $user = $this->createAdminUser();
        $hub = Hub::create(['hub_name' => 'DEL Hub', 'code' => 'DEL']);

        $original = Crr::create([
            'stock_number' => 'ICN-KEEP-STATUS-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_COMPLETED,
            'hub_agent' => 'BOM',
            'accept' => false,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-KEEP-STATUS-1',
            'status' => 'In transit',
            'service' => 'Airfreight',
            'consignee' => 'hub:' . $hub->id,
        ]);
        $shipment->crrs()->attach($original->id);

        $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-KEEP-STATUS-1',
            'action' => 'transit',
        ])->assertOk()->assertJson([
            'success' => true,
            'status' => 'In transit',
        ]);

        $this->assertSame('In transit', $shipment->fresh()->status);
        $this->assertNotNull(
            Crr::query()->where('duplicated_from_crr_id', $original->id)->first()
        );
    }

    public function test_manual_status_completed_defers_hub_courier_destination_stocks(): void
    {
        $user = $this->createAdminUser();

        $hub = Hub::create([
            'hub_name' => 'Amsterdam Hub',
            'code' => 'AMS',
        ]);

        $original = Crr::create([
            'stock_number' => 'AMS-STATUS-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'accept' => false,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-STATUS-1',
            'status' => 'In process',
            'service' => 'Courier',
            'consignee' => 'hub:' . $hub->id,
        ]);
        $shipment->crrs()->attach($original->id);

        $response = $this->actingAsVerified($user)->postJson(route('shipments.update-status', $shipment->id), [
            'status' => 'Completed',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status' => 'Completed',
        ]);

        $this->assertSame(Crr::STATUS_COMPLETED, (int) $original->fresh()->status);
        $this->assertSame(
            0,
            Crr::query()
                ->where('duplicated_from_crr_id', $original->id)
                ->count()
        );
    }

    public function test_complete_does_not_create_destination_stocks_for_non_deferred_service(): void
    {
        $user = $this->createAdminUser();

        $hub = Hub::create([
            'hub_name' => 'Amsterdam Hub',
            'code' => 'AMS',
        ]);

        $original = Crr::create([
            'stock_number' => 'AMS-RELEASE-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'internal_shipment' => 'SHIP-RELEASE-1',
            'accept' => false,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-RELEASE-1',
            'status' => 'In process',
            'service' => 'Release',
            'consignee' => 'hub:' . $hub->id,
        ]);
        $shipment->crrs()->attach($original->id);

        $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
            'shipment_number' => 'SHIP-RELEASE-1',
            'action' => 'complete',
        ])->assertOk();

        $this->assertSame(
            0,
            Crr::query()
                ->where('duplicated_from_crr_id', $original->id)
                ->count()
        );
        $this->assertSame([(int) $original->id], $shipment->fresh()->crrs()->pluck('crrs.id')->map(fn ($id) => (int) $id)->all());
        $this->assertSame(Crr::STATUS_COMPLETED, (int) $original->fresh()->status);
    }

    public function test_transit_allows_release_hand_carry_and_on_board(): void
    {
        $user = $this->createAdminUser();
        $hub = Hub::create(['hub_name' => 'Amsterdam Hub', 'code' => 'AMS']);

        foreach (['Release', 'Hand Carry', 'On-board delivery'] as $index => $service) {
            $original = Crr::create([
                'stock_number' => 'AMS-NO-TRANSIT-' . $index,
                'content' => 'Shipspares',
                'status' => Crr::STATUS_IN_PROGRESS,
                'accept' => false,
            ]);

            $shipment = Shipment::create([
                'shipment_number' => 'SHIP-NO-TRANSIT-' . $index,
                'status' => 'In process',
                'service' => $service,
                'consignee' => 'hub:' . $hub->id,
            ]);
            $shipment->crrs()->attach($original->id);

            $this->actingAsVerified($user)->postJson(route('shipments.finalize', $shipment->id), [
                'shipment_number' => $shipment->shipment_number,
                'action' => 'transit',
            ])->assertOk()->assertJson([
                'success' => true,
                'status' => 'In process',
            ]);

            $this->assertSame('In process', $shipment->fresh()->status);

            $this->assertNotNull(
                Crr::query()
                    ->where('duplicated_from_crr_id', $original->id)
                    ->first()
            );
        }
    }
}
