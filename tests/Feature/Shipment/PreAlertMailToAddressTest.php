<?php

namespace Tests\Feature\Shipment;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Customer;
use App\Models\CustomerVessel;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\ShipmentOnBoardLeg;
use App\Services\PreAlertMailService;
use ReflectionMethod;
use Tests\RegressionTestCase;

class PreAlertMailToAddressTest extends RegressionTestCase
{
    public function test_on_board_delivery_pre_alert_to_uses_vessel_customer_email(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Vessel Owner Ltd',
            'email' => 'vessel.owner@marinecaddie.test',
        ]);

        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Owner Vessel',
        ]);

        $crr = Crr::create([
            'stock_number' => 'OBD-STK-1',
            'vessel_name' => 'Owner Vessel',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $onBoardShipment = Shipment::create([
            'shipment_number' => 'OBD-MAIL-1',
            'status' => 'In process',
            'service' => 'On-board delivery',
        ]);
        $onBoardShipment->crrs()->attach($crr->id);
        $onBoardShipment->load('crrs.customerVessel.customer');

        $airfreightShipment = Shipment::create([
            'shipment_number' => 'AIR-MAIL-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);
        $airfreightShipment->crrs()->attach($crr->id);

        $method = new ReflectionMethod(PreAlertMailService::class, 'buildToAddresses');
        $service = app(PreAlertMailService::class);

        $onBoardTo = $method->invoke($service, $onBoardShipment, [
            'name' => 'Singapore Agent',
            'email' => 'agent@marinecaddie.test',
        ]);

        $airfreightTo = $method->invoke($service, $airfreightShipment, [
            'name' => 'Singapore Agent',
            'email' => 'agent@marinecaddie.test',
        ]);

        $this->assertSame(['vessel.owner@marinecaddie.test'], array_column($onBoardTo, 'email'));
        $this->assertSame(['Vessel Owner Ltd'], array_column($onBoardTo, 'name'));
        $this->assertSame(['agent@marinecaddie.test'], array_column($airfreightTo, 'email'));
    }

    public function test_on_board_delivery_pre_alert_body_notifies_owner_without_shipped_to(): void
    {
        $customer = Customer::create([
            'customer_name' => 'CAMPBELL SHIPPING',
            'email' => 'ops@campbell.test',
        ]);

        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'ANGEL',
        ]);

        $crr = Crr::create([
            'stock_number' => 'DEL-24119010',
            'vessel_name' => 'ANGEL',
            'content' => 'Ship spares',
            'supplier' => 'GUMA TECH MARINE SERVICES',
            'po_numbers' => ['123', '1324'],
            'customs_value' => 206.60,
            'currency' => 'USD',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        CrrPackage::create([
            'crr_id' => $crr->id,
            'weight' => 11,
            'cbm' => 0,
        ]);

        Port::create([
            'type' => 'seaport',
            'iata_code' => 'DXB',
            'city' => 'Dubai',
            'country_name' => 'United Arab Emirates',
            'is_active' => true,
        ]);
        Port::create([
            'type' => 'seaport',
            'iata_code' => 'SIN',
            'city' => 'Singapore',
            'country_name' => 'Singapore',
            'is_active' => true,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'AZA-33967-0926',
            'status' => 'In process',
            'service' => 'On-board delivery',
            'departure_port_code' => 'DXB',
            'consignee_port_code' => 'SIN',
            'consignee_city' => 'Singapore',
            'customer_reference' => 'CAMP-REF-1',
        ]);
        $shipment->crrs()->attach($crr->id);

        ShipmentOnBoardLeg::create([
            'shipment_id' => $shipment->id,
            'sort_order' => 0,
            'departure_date' => '2026-09-04',
            'delivery_date' => '2026-09-05',
            'delivery_time' => null,
        ]);

        $shipment->load(['crrs.customerVessel.customer', 'crrs.packages', 'onBoardLegs']);

        $method = new ReflectionMethod(PreAlertMailService::class, 'buildBody');
        $body = $method->invoke(
            app(PreAlertMailService::class),
            $shipment,
            [
                'vesselLine' => 'M/V ANGEL (IMO: 9590620) in transit',
                'totals' => [
                    'packages' => 1,
                    'weight' => 11,
                    'volume_weight' => 0.22,
                    'cbm' => 0,
                ],
            ],
            ['name' => 'Singapore Agent', 'email' => 'agent@test.com'],
            'Azahar',
            'sunnyazahar@gmail.com'
        );

        $this->assertStringContainsString(
            'This is to notify owner / management CAMPBELL SHIPPING about shipment from Dubai to Singapore with the below details',
            $body
        );
        $this->assertStringContainsString('Shipment Ref. AZA-33967-0926', $body);
        $this->assertStringContainsString('Vessel: M/V ANGEL (IMO: 9590620) in transit', $body);
        $this->assertStringContainsString('Departure date: 04.09.2026', $body);
        $this->assertStringContainsString('Delivery date: 05.09.2026', $body);
        $this->assertStringContainsString('Delivery time:', $body);
        $this->assertStringContainsString('Customer reference: CAMP-REF-1', $body);
        $this->assertStringContainsString('Cargo / Item details:', $body);
        $this->assertStringContainsString('<table', $body);
        $this->assertStringContainsString('>Supplier</th>', $body);
        $this->assertStringContainsString('>PO number</th>', $body);
        $this->assertStringContainsString('GUMA TECH MARINE SERVICES', $body);
        $this->assertStringContainsString('123, 1324', $body);
        $this->assertStringContainsString('206.60 USD', $body);
        $this->assertStringNotContainsString('>Description</th>', $body);
        $this->assertStringNotContainsString('>Stock no</th>', $body);
        $this->assertStringNotContainsString('>Location</th>', $body);
        $this->assertStringContainsString('Total pieces in consignment: 1 pcs', $body);
        $this->assertStringContainsString('Gross Weight: 11.00 kg', $body);
        $this->assertStringContainsString('Estimated volume weight: 0.22 kg', $body);
        $this->assertStringContainsString('Repacked as: 1 item(s) / 11.00 kg', $body);
        $this->assertStringNotContainsString('Supplier: GUMA TECH MARINE SERVICES', $body);
        $this->assertStringNotContainsString('Service Details:', $body);
        $this->assertStringNotContainsString('Total packages:', $body);
        $this->assertStringNotContainsString('Shipped to:', $body);
        $this->assertStringNotContainsString('Please do the needful.', $body);
        $this->assertStringNotContainsString('Please find attached', $body);
        $this->assertStringContainsString("With kind regards,\r\n\r\nAzahar\r\nsunnyazahar@gmail.com\r\nMarinecaddie", $body);
    }
}
