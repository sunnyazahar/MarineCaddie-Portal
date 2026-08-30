<?php

namespace Tests\Feature\Stock;

use App\Models\Crr;
use Tests\RegressionTestCase;

class StockAcceptTest extends RegressionTestCase
{
    public function test_accept_promotes_new_status_to_stock(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'AMS-ACCEPT-NEW',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_NEW,
            'accept' => false,
            'vessel_name' => 'TEST VESSEL',
            'hub_agent' => 'AMS',
            'flags' => ['Follow up'],
        ]);

        $response = $this->actingAsVerified($user)->postJson(
            route('stocks.crr.update-accept', $crr->id)
        );

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'accept' => true,
            'status' => Crr::STATUS_ACTIVE,
            'status_label' => 'Stock',
        ]);

        $crr->refresh();
        $this->assertTrue($crr->accept);
        $this->assertSame(Crr::STATUS_ACTIVE, (int) $crr->status);
    }

    public function test_accept_preserves_non_new_status(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'AMS-ACCEPT-IP',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'accept' => false,
            'vessel_name' => 'TEST VESSEL',
            'hub_agent' => 'AMS',
            'flags' => ['Follow up'],
        ]);

        $response = $this->actingAsVerified($user)->postJson(
            route('stocks.crr.update-accept', $crr->id)
        );

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'accept' => true,
            'status' => Crr::STATUS_IN_PROGRESS,
            'status_label' => 'In Progress',
        ]);

        $crr->refresh();
        $this->assertTrue($crr->accept);
        $this->assertSame(Crr::STATUS_IN_PROGRESS, (int) $crr->status);
    }
}
