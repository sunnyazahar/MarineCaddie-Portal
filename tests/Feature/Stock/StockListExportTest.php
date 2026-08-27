<?php

namespace Tests\Feature\Stock;

use App\Models\Crr;
use Carbon\Carbon;
use Tests\RegressionTestCase;
use ZipArchive;

class StockListExportTest extends RegressionTestCase
{
    public function test_excel_export_downloads_xlsx_for_selected_stocks(): void
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
            'spreadsheetml.sheet',
            strtolower((string) $response->headers->get('content-type'))
        );
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        $binary = $response->getContent();
        $this->assertNotFalse($binary);
        $this->assertSame('PK', substr($binary, 0, 2)); // zip/xlsx signature

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        file_put_contents($tmp, $binary);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($tmp) === true);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($tmp);

        $this->assertNotFalse($sheet);
        $this->assertStringContainsString('Location/Hub', $sheet);
        $this->assertStringContainsString('Volume volume weight', $sheet);
        $this->assertStringContainsString('DXB-EXPORT-1', $sheet);
        $this->assertStringContainsString('Test Supplier', $sheet);
        $this->assertStringContainsString('18/08/26', $sheet);
        $this->assertStringContainsString('Yes', $sheet);

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
        $binary = $response->getContent();
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        file_put_contents($tmp, $binary);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($tmp) === true);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($tmp);

        $newerPos = strpos((string) $sheet, 'DXB-EXPORT-NEW');
        $olderPos = strpos((string) $sheet, 'DXB-EXPORT-OLD');

        $this->assertNotFalse($newerPos);
        $this->assertNotFalse($olderPos);
        $this->assertLessThan($olderPos, $newerPos);
    }
}
