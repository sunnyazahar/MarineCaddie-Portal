<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\Shipment;
use App\Services\ShipmentPdfFingerprintService;
use Tests\RegressionTestCase;

class ShipmentPdfFingerprintServiceTest extends RegressionTestCase
{
    public function test_manifest_fingerprint_changes_for_stock_add_remove(): void
    {
        $first = Crr::create(['stock_number' => 'STK-A', 'content' => 'Shipspares', 'accept' => false]);
        $second = Crr::create(['stock_number' => 'STK-B', 'content' => 'Shipspares', 'accept' => false]);
        $shipment = $this->makeShipment([$first]);

        $before = app(ShipmentPdfFingerprintService::class)->manifestFingerprint($shipment);

        $shipment->setRelation('crrs', collect([$first, $second]));
        $afterAdd = app(ShipmentPdfFingerprintService::class)->manifestFingerprint($shipment);

        $this->assertNotSame($before, $afterAdd);

        $shipment->setRelation('crrs', collect([$first]));
        $afterRemove = app(ShipmentPdfFingerprintService::class)->manifestFingerprint($shipment);

        $this->assertSame($before, $afterRemove);
    }

    public function test_manifest_fingerprint_ignores_stock_field_edits(): void
    {
        $crr = Crr::create(['stock_number' => 'STK-EDIT', 'content' => 'Shipspares', 'accept' => false]);
        $shipment = $this->makeShipment([$crr]);
        $before = app(ShipmentPdfFingerprintService::class)->manifestFingerprint($shipment);

        $crr->stock_number = 'STK-EDIT-CHANGED';
        $crr->supplier = 'New supplier';
        $shipment->setRelation('crrs', collect([$crr]));

        $this->assertSame(
            $before,
            app(ShipmentPdfFingerprintService::class)->manifestFingerprint($shipment)
        );
    }

    public function test_manifest_fingerprint_changes_for_consignee_name_address_country_port(): void
    {
        $shipment = $this->makeShipment();
        $service = app(ShipmentPdfFingerprintService::class);
        $before = $service->manifestFingerprint($shipment);

        $shipment->consignee = 'hub:99';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->manifestFingerprint($shipment);
        $shipment->consignee_address = 'New street 12';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->manifestFingerprint($shipment);
        $shipment->consignee_country = 'India';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->manifestFingerprint($shipment);
        $shipment->consignee_port_code = 'BOM';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));
    }

    public function test_manifest_fingerprint_changes_for_service_and_additional_service(): void
    {
        $shipment = $this->makeShipment();
        $service = app(ShipmentPdfFingerprintService::class);
        $before = $service->manifestFingerprint($shipment);

        $shipment->service = 'Truck';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->manifestFingerprint($shipment);
        $shipment->additional_service = 'Express';
        $this->assertNotSame($before, $service->manifestFingerprint($shipment));
    }

    public function test_manifest_fingerprint_ignores_unrelated_departure_and_consignee_fields(): void
    {
        $shipment = $this->makeShipment();
        $service = app(ShipmentPdfFingerprintService::class);
        $before = $service->manifestFingerprint($shipment);

        $shipment->departure = 'hub:2';
        $shipment->departure_port_code = 'SIN';
        $shipment->consignee_city = 'Mumbai';
        $shipment->consignee_att = 'John';
        $shipment->consignee_email = 'john@test.com';
        $shipment->deadline_arrival = '2026-09-01';

        $this->assertSame($before, $service->manifestFingerprint($shipment));
    }

    public function test_pre_alert_fingerprint_changes_for_stock_add_remove(): void
    {
        $first = Crr::create(['stock_number' => 'STK-PA-A', 'content' => 'Shipspares', 'accept' => false]);
        $second = Crr::create(['stock_number' => 'STK-PA-B', 'content' => 'Shipspares', 'accept' => false]);
        $shipment = $this->makeShipment([$first]);
        $service = app(ShipmentPdfFingerprintService::class);

        $before = $service->preAlertFingerprint($shipment);

        $shipment->setRelation('crrs', collect([$first, $second]));
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));

        $shipment->setRelation('crrs', collect([$first]));
        $this->assertSame($before, $service->preAlertFingerprint($shipment));
    }

    public function test_pre_alert_fingerprint_changes_for_consignee_name_address_country_port(): void
    {
        $service = app(ShipmentPdfFingerprintService::class);

        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);
        $shipment->consignee = 'hub:99';
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);
        $shipment->consignee_address = 'New street 12';
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);
        $shipment->consignee_country = 'India';
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);
        $shipment->consignee_port_code = 'BOM';
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));
    }

    public function test_pre_alert_fingerprint_changes_for_service_details_tab_data(): void
    {
        $service = app(ShipmentPdfFingerprintService::class);
        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);

        $shipment->repacked_items = 3;
        $shipment->repacked_weight = 12.5;
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));

        $shipment = $this->makeShipment();
        $before = $service->preAlertFingerprint($shipment);
        $shipment->service = 'Truck';
        $this->assertNotSame($before, $service->preAlertFingerprint($shipment));
    }

    public function test_pre_alert_fingerprint_ignores_stock_field_edits_and_departure_comments(): void
    {
        $crr = Crr::create(['stock_number' => 'STK-PA-EDIT', 'content' => 'Shipspares', 'accept' => false]);
        $shipment = $this->makeShipment([$crr]);
        $service = app(ShipmentPdfFingerprintService::class);
        $before = $service->preAlertFingerprint($shipment);

        $crr->supplier = 'New supplier';
        $crr->stock_number = 'STK-PA-CHANGED';
        $shipment->departure = 'hub:2';
        $shipment->additional_service = 'Express';
        $shipment->consignee_city = 'Mumbai';
        $shipment->comments_consignee = 'Updated comment';

        $this->assertSame($before, $service->preAlertFingerprint($shipment));
    }

    /**
     * @param list<Crr> $crrs
     */
    private function makeShipment(array $crrs = []): Shipment
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-FP-' . uniqid(),
            'status' => 'In process',
            'service' => 'Airfreight',
            'consignee' => 'hub:1',
        ]);
        $shipment->additional_service = 'Normal';
        $shipment->consignee_address = 'Old street';
        $shipment->consignee_country = 'Singapore';
        $shipment->consignee_port_code = 'SIN';
        $shipment->setRelation('crrs', collect($crrs));

        return $shipment;
    }
}
