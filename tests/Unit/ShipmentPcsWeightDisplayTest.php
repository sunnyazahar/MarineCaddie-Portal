<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Shipment;
use Tests\RegressionTestCase;

class ShipmentPcsWeightDisplayTest extends RegressionTestCase
{
    public function test_total_pcs_and_weight_sum_linked_packages(): void
    {
        $crrA = Crr::create([
            'stock_number' => 'PCS-A-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crrB = Crr::create([
            'stock_number' => 'PCS-B-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        CrrPackage::create(['crr_id' => $crrA->id, 'weight' => 10.5]);
        CrrPackage::create(['crr_id' => $crrA->id, 'weight' => 4.5]);
        CrrPackage::create(['crr_id' => $crrB->id, 'weight' => 2]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-PCS-1',
            'status' => 'In process',
        ]);
        $shipment->crrs()->attach([$crrA->id, $crrB->id]);

        $shipment->load('crrs.packages');

        $this->assertSame(3, $shipment->total_pcs);
        $this->assertSame('3', $shipment->total_pcs_display);
        $this->assertSame('17', $shipment->total_weight_display);
    }

    public function test_empty_packages_show_dash(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-PCS-2',
            'status' => 'In process',
        ]);

        $shipment->load('crrs.packages');

        $this->assertSame(0, $shipment->total_pcs);
        $this->assertSame('—', $shipment->total_pcs_display);
        $this->assertSame('—', $shipment->total_weight_display);
    }
}
