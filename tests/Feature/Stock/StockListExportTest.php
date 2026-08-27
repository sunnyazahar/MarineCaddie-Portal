<?php

namespace Tests\Feature\Stock;

use App\Models\Crr;
use Carbon\Carbon;
use Tests\RegressionTestCase;

class StockListExportTest extends RegressionTestCase
{
    public function test_excel_export_downloads_xls_for_selected_stocks(): void
    {
        $user = $this->createAdminUser();

        Carbon::setTestNow(Carbon::parse('2026-08-18 10:00:00'));

        $crr = Crr::create([
            'stock_number' => 'DXB-EXPORT-1',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
            'vessel_name' => 'TEST VESSEL',
            'supplier' => 'Test Supplier',
            'hub_agent' => 'DXB',
            'flags' => ['Pick up'],
        ]);

        $response = $this->actingAsVerified($user)->get(route('stocks.export-excel', [
            'ids' => (string) $crr->id,
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.ms-excel',
            strtolower((string) $response->headers->get('content-type'))
        );

        $content = $response->getContent();
        $this->assertStringContainsString('Location/Hub', $content);
        $this->assertStringContainsString('Volume volume weight', $content);
        $this->assertStringContainsString('#8DB4E2', $content);
        $this->assertStringContainsString('ss:Bold="1"', $content);
        $this->assertStringContainsString('DXB-EXPORT-1', $content);
        $this->assertStringContainsString('Test Supplier', $content);
        $this->assertStringContainsString('18/08/26', $content); // created_at
        $this->assertStringContainsString('Yes', $content); // Pick up flag

        Carbon::setTestNow();
    }

    public function test_excel_export_rejects_empty_selection(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAsVerified($user)->get(route('stocks.export-excel', [
            'ids' => '',
        ]));

        $response->assertStatus(422);
    }

    public function test_excel_export_orders_rows_by_id_desc(): void
    {
        $user = $this->createAdminUser();

        $older = Crr::create([
            'stock_number' => 'DXB-EXPORT-OLD',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
            'supplier' => 'Older Supplier',
            'hub_agent' => 'DXB',
        ]);

        $newer = Crr::create([
            'stock_number' => 'DXB-EXPORT-NEW',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
            'supplier' => 'Newer Supplier',
            'hub_agent' => 'DXB',
        ]);

        $response = $this->actingAsVerified($user)->get(route('stocks.export-excel', [
            'ids' => $older->id.','.$newer->id,
        ]));

        $response->assertOk();
        $content = $response->getContent();

        $newerPos = strpos($content, 'DXB-EXPORT-NEW');
        $olderPos = strpos($content, 'DXB-EXPORT-OLD');

        $this->assertNotFalse($newerPos);
        $this->assertNotFalse($olderPos);
        $this->assertLessThan($olderPos, $newerPos);
    }
}
