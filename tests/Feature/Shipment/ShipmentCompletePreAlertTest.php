<?php

namespace Tests\Feature\Shipment;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Hub;
use App\Models\Shipment;
use App\Models\ShipmentPreAlert;
use Tests\RegressionTestCase;

class ShipmentCompletePreAlertTest extends RegressionTestCase
{
    public function test_complete_pre_alert_sets_in_transit_and_completes_stocks_without_duplicates(): void
    {
        $user = $this->createAdminUser();

        $hub = Hub::create([
            'hub_name' => 'Amsterdam Hub',
            'code' => 'AMS',
        ]);

        $original = Crr::create([
            'stock_number' => 'AMS-PRE-ALERT-1',
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
            'shipment_number' => 'SHIP-PRE-ALERT-1',
            'status' => 'In process',
            'service' => 'Airfreight',
            'consignee' => 'hub:' . $hub->id,
            'deadline_arrival' => '2026-09-01',
        ]);
        $shipment->crrs()->attach($original->id);

        ShipmentPreAlert::create([
            'shipment_id' => $shipment->id,
            'version' => 1,
            'file_name' => 'pre-alert',
            'file_path' => 'shipments/pre-alerts/test.pdf',
        ]);

        $response = $this->actingAsVerified($user)->postJson(route('shipments.complete-pre-alert', $shipment->id), [
            'shipment_number' => 'SHIP-PRE-ALERT-1',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status' => 'In transit',
        ]);

        $shipment->refresh();
        $original->refresh();

        $this->assertSame('In transit', $shipment->status);
        $this->assertSame(Crr::STATUS_COMPLETED, (int) $original->status);
        $this->assertSame([(int) $original->id], array_map('intval', $shipment->crrs()->pluck('crrs.id')->all()));

        $this->assertSame(
            0,
            Crr::query()
                ->where('duplicated_from_crr_id', $original->id)
                ->count()
        );
    }

    public function test_complete_pre_alert_requires_pre_alert_pdf(): void
    {
        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-PRE-ALERT-2',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        $response = $this->actingAsVerified($user)->postJson(route('shipments.complete-pre-alert', $shipment->id), [
            'shipment_number' => 'SHIP-PRE-ALERT-2',
        ]);

        $response->assertStatus(422)->assertJson([
            'success' => false,
            'message' => 'Generate a pre-alert PDF before completing.',
        ]);
    }

    public function test_complete_pre_alert_rejects_when_not_in_process(): void
    {
        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-PRE-ALERT-3',
            'status' => 'In transit',
            'service' => 'Airfreight',
        ]);

        ShipmentPreAlert::create([
            'shipment_id' => $shipment->id,
            'version' => 1,
            'file_name' => 'pre-alert',
            'file_path' => 'shipments/pre-alerts/test.pdf',
        ]);

        $response = $this->actingAsVerified($user)->postJson(route('shipments.complete-pre-alert', $shipment->id), [
            'shipment_number' => 'SHIP-PRE-ALERT-3',
        ]);

        $response->assertStatus(422)->assertJson([
            'success' => false,
            'message' => 'Pre-alert has already been completed for this shipment.',
        ]);
    }
}
