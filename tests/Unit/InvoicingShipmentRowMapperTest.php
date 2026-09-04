<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Shipment;
use App\Services\InvoicingShipmentRowMapper;
use Tests\RegressionTestCase;

class InvoicingShipmentRowMapperTest extends RegressionTestCase
{
    public function test_gross_weight_uses_service_details_repacked_weight_when_set(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'INV-GROSS-REPACK',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'repacked_weight' => 1583.33,
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-GROSS-STK-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crr->packages()->create(['weight' => 300]);
        $shipment->crrs()->attach($crr->id);

        $detail = app(InvoicingShipmentRowMapper::class)->mapDetail($shipment);

        $this->assertSame('1583.33', $detail['gross_wt']);
        $this->assertSame('1710', $detail['chargeable_wt']);
    }

    public function test_gross_weight_falls_back_to_stock_items_total_when_repacked_blank_or_zero(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'INV-GROSS-STOCK',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'repacked_weight' => 0,
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-GROSS-STK-2',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crr->packages()->create(['weight' => 300]);
        $shipment->crrs()->attach($crr->id);

        $detail = app(InvoicingShipmentRowMapper::class)->mapDetail($shipment);

        $this->assertSame('300', $detail['gross_wt']);
    }

    public function test_unsaved_line_item_defaults_are_blank_before_bill_generate(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'INV-TYPE-BLANK',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $detail = app(InvoicingShipmentRowMapper::class)->mapDetail($shipment);
        $line = $detail['line_items'][0];

        $this->assertFalse($detail['is_saved']);
        $this->assertSame('', $line['description']);
        $this->assertSame('', $line['remarks']);
        $this->assertSame('', $line['qty']);
        $this->assertSame('', $line['qty_type']);
        $this->assertSame('', $line['rate']);
        $this->assertSame('', $line['currency']);
        $this->assertSame('0.00', $line['amount']);
    }

    public function test_party_name_uses_linked_stock_customer_name(): void
    {
        $customer = \App\Models\Customer::create(['customer_name' => 'CAMPBELL SHIPPING']);
        \App\Models\CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'ANGEL',
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-PARTY-STK-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'vessel_name' => 'ANGEL',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PARTY-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($crr->id);

        $row = app(InvoicingShipmentRowMapper::class)->map($shipment->fresh());

        $this->assertSame('CAMPBELL SHIPPING', $row['party_name']);
        $this->assertFalse($row['invoice_generated']);
    }
}
