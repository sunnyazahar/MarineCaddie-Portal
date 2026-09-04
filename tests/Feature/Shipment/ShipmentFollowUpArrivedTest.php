<?php

namespace Tests\Feature\Shipment;

use App\Models\Crr;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use Tests\RegressionTestCase;

class ShipmentFollowUpArrivedTest extends RegressionTestCase
{
    public function test_follow_up_lists_completed_shipment_until_marked_arrived(): void
    {
        $user = $this->createAdminUser();

        Shipment::create([
            'shipment_number' => 'FU-IN-TRANSIT-1',
            'status' => 'In transit',
        ]);
        $completedOpen = Shipment::create([
            'shipment_number' => 'FU-COMPLETED-OPEN-1',
            'status' => 'Completed',
            'arrived_at' => null,
        ]);
        Shipment::create([
            'shipment_number' => 'FU-COMPLETED-ARRIVED-1',
            'status' => 'Completed',
            'arrived_at' => now(),
        ]);
        Shipment::create([
            'shipment_number' => 'FU-DRAFT-1',
            'status' => 'Draft',
        ]);

        $numbers = app(ShipmentRepositoryInterface::class)
            ->buildShipmentFollowUpQuery([])
            ->pluck('shipment_number')
            ->all();

        $this->assertContains('FU-IN-TRANSIT-1', $numbers);
        $this->assertContains('FU-COMPLETED-OPEN-1', $numbers);
        $this->assertNotContains('FU-COMPLETED-ARRIVED-1', $numbers);
        $this->assertNotContains('FU-DRAFT-1', $numbers);

        $crr = Crr::create([
            'stock_number' => 'FU-STK-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $completedOpen->crrs()->attach($crr->id);

        $this->actingAsVerified($user)
            ->postJson(route('shipments.mark-as-arrived', $completedOpen->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertNotNull($completedOpen->fresh()->arrived_at);

        $numbersAfter = app(ShipmentRepositoryInterface::class)
            ->buildShipmentFollowUpQuery([])
            ->pluck('shipment_number')
            ->all();

        $this->assertContains('FU-IN-TRANSIT-1', $numbersAfter);
        $this->assertNotContains('FU-COMPLETED-OPEN-1', $numbersAfter);
    }

    public function test_mark_as_arrived_completes_in_transit_and_sets_arrived_at(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'FU-STK-2',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'FU-ARRIVE-1',
            'status' => 'In transit',
        ]);
        $shipment->crrs()->attach($crr->id);

        $this->actingAsVerified($user)
            ->postJson(route('shipments.mark-as-arrived', $shipment->id))
            ->assertOk()
            ->assertJsonPath('status', 'Completed');

        $shipment->refresh();
        $this->assertSame('Completed', $shipment->status);
        $this->assertNotNull($shipment->arrived_at);
        $this->assertSame(Crr::STATUS_COMPLETED, (int) $crr->fresh()->status);
    }
}
