<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentManifest;
use App\Services\LinkedStockShipmentManifestService;
use App\Services\ShipmentChangeLogService;
use App\Services\ShipmentManifestService;
use Mockery;
use Tests\RegressionTestCase;

class LinkedStockShipmentManifestServiceTest extends RegressionTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_eligible_shipments_skip_completed_and_cancelled(): void
    {
        $crr = $this->createCrr('STK-MANIFEST-1');

        $active = Shipment::create(['shipment_number' => 'SHIP-ACTIVE', 'status' => 'In process']);
        $transit = Shipment::create(['shipment_number' => 'SHIP-TRANSIT', 'status' => 'In transit']);
        $completed = Shipment::create(['shipment_number' => 'SHIP-DONE', 'status' => 'Completed']);
        $cancelled = Shipment::create(['shipment_number' => 'SHIP-CXL', 'status' => 'Cancelled']);

        $active->crrs()->attach($crr);
        $transit->crrs()->attach($crr);
        $completed->crrs()->attach($crr);
        $cancelled->crrs()->attach($crr);

        $service = app(LinkedStockShipmentManifestService::class);
        $eligible = $service->eligibleShipments($crr->fresh());

        $this->assertEqualsCanonicalizing(
            ['SHIP-ACTIVE', 'SHIP-TRANSIT'],
            $eligible->pluck('shipment_number')->all()
        );
    }

    public function test_regenerate_calls_generate_only_for_eligible_shipments(): void
    {
        $crr = $this->createCrr('STK-MANIFEST-2');

        $active = Shipment::create(['shipment_number' => 'SHIP-REGEN', 'status' => 'In process']);
        $completed = Shipment::create(['shipment_number' => 'SHIP-SKIP', 'status' => 'Completed']);
        $active->crrs()->attach($crr);
        $completed->crrs()->attach($crr);

        $manifest = new ShipmentManifest([
            'shipment_id' => $active->id,
            'version' => 2,
            'file_name' => 'manifest 1',
            'file_path' => 'shipment_manifests/1/manifest-1.pdf',
        ]);

        $manifestService = Mockery::mock(ShipmentManifestService::class);
        $manifestService->shouldReceive('generate')
            ->once()
            ->andReturn($manifest);

        $changeLog = Mockery::mock(ShipmentChangeLogService::class);
        $changeLog->shouldReceive('log')->once();

        $service = new LinkedStockShipmentManifestService($manifestService, $changeLog);
        $count = $service->regenerateForCrr($crr->fresh());

        $this->assertSame(1, $count);
    }

    private function createCrr(string $stockNumber, array $attributes = []): Crr
    {
        return Crr::create(array_merge([
            'stock_number' => $stockNumber,
            'content' => 'Shipspares',
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ], $attributes));
    }
}
