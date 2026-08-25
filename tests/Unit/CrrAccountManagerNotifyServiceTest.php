<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Services\CrrAccountManagerNotifyService;
use Illuminate\Support\Facades\Mail;
use Tests\RegressionTestCase;

class CrrAccountManagerNotifyServiceTest extends RegressionTestCase
{
    public function test_skips_when_there_are_no_changes(): void
    {
        Mail::fake();

        $crr = Crr::create([
            'stock_number' => 'STK-AM-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ]);

        $sent = app(CrrAccountManagerNotifyService::class)->notifyOfChanges($crr, []);

        $this->assertFalse($sent);
        Mail::assertNothingSent();
    }

    public function test_skips_stock_item_created_events(): void
    {
        Mail::fake();

        $crr = Crr::create([
            'stock_number' => 'STK-AM-CREATE',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ]);

        $sent = app(CrrAccountManagerNotifyService::class)->notifyOfChanges($crr, [[
            'title' => 'Stock item created',
            'description' => null,
        ]]);

        $this->assertFalse($sent);
        Mail::assertNothingSent();
    }

    public function test_skips_when_account_manager_email_is_missing(): void
    {
        Mail::fake();

        $crr = Crr::create([
            'stock_number' => 'STK-AM-2',
            'vessel_name' => 'Unknown Vessel',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ]);

        $sent = app(CrrAccountManagerNotifyService::class)->notifyOfChanges($crr, [[
            'title' => 'Location edited',
            'description' => 'From empty to WH-A',
        ]]);

        $this->assertFalse($sent);
        Mail::assertNothingSent();
    }
}
