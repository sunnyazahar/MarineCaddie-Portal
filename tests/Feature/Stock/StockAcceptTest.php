<?php

namespace Tests\Feature\Stock;

use App\Models\Crr;
use Tests\RegressionTestCase;

class StockAcceptTest extends RegressionTestCase
{
    public function test_accept_sets_accept_only_and_preserves_status(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'AMS-ACCEPT-1',
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
            'status' => Crr::STATUS_NEW,
            'status_label' => 'New',
        ]);

        $crr->refresh();
        $this->assertTrue($crr->accept);
        $this->assertSame(Crr::STATUS_NEW, (int) $crr->status);
    }
}
