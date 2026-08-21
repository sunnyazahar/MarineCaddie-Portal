<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Hub;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use App\Services\OperationsDashboardService;
use Tests\RegressionTestCase;

class OperationsDashboardTest extends RegressionTestCase
{
    public function test_admin_receives_global_accurate_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $activeStock = $this->createCrr('STK-001', ['status' => Crr::STATUS_ACTIVE, 'accept' => false]);
        $this->createCrr('STK-002', ['status' => Crr::STATUS_COMPLETED, 'accept' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-001',
            'status' => 'In transit',
            'deadline_arrival' => today()->subDay(),
            'pre_alert_reminder' => today(),
            'service' => 'Courier',
        ]);
        $shipment->crrs()->attach($activeStock);
        $shipment->irregularities()->createMany([
            ['status' => 'Open'],
            ['status' => null],
        ]);

        $dashboard = app(OperationsDashboardService::class)->build($admin);

        $this->assertSame(1, $dashboard['kpis']['activeStocks']);
        $this->assertSame(1, $dashboard['kpis']['unacceptedStocks']);
        $this->assertSame(1, $dashboard['kpis']['activeShipments']);
        $this->assertSame(1, $dashboard['kpis']['overdueArrivals']);
        $this->assertSame(1, $dashboard['kpis']['preAlertsDue']);
        $this->assertSame(2, $dashboard['kpis']['openIrregularities']);
        $this->assertCount(1, $dashboard['overdueShipments']);
    }

    public function test_all_roles_see_the_same_global_dashboard_data(): void
    {
        $user = User::factory()->create(['role' => 'Operations', 'is_active' => true]);
        $this->createCrr('STK-GLOBAL', ['status' => Crr::STATUS_ACTIVE]);
        Shipment::create(['shipment_number' => 'SHIP-GLOBAL', 'status' => 'In transit']);

        $dashboard = app(OperationsDashboardService::class)->build($user);

        $this->assertFalse($dashboard['isScoped']);
        $this->assertTrue($dashboard['hasAssignments']);
        $this->assertSame(1, $dashboard['kpis']['activeStocks']);
        $this->assertSame(1, $dashboard['kpis']['activeShipments']);
    }

    public function test_operations_user_sees_every_shipment_not_only_their_own(): void
    {
        $user = User::factory()->create(['role' => 'Operations', 'is_active' => true]);
        Shipment::create([
            'shipment_number' => 'SHIP-OWNED',
            'status' => 'In process',
            'created_by' => $user->id,
        ]);
        Shipment::create(['shipment_number' => 'SHIP-OTHER', 'status' => 'In process']);

        $service = app(OperationsDashboardService::class);
        $dashboard = $service->build($user);

        $this->assertEqualsCanonicalizing(['SHIP-OWNED', 'SHIP-OTHER'], $service->visibleShipments($user)->pluck('shipment_number')->all());
        $this->assertSame(2, $dashboard['kpis']['activeShipments']);
    }

    public function test_operations_agent_and_supplier_roles_see_all_records(): void
    {
        $hub = Hub::withoutEvents(fn () => Hub::create(['hub_name' => 'Assigned Hub', 'code' => 'HUB-A']));
        $otherHub = Hub::withoutEvents(fn () => Hub::create(['hub_name' => 'Other Hub', 'code' => 'HUB-B']));
        $agent = Agent::withoutEvents(fn () => Agent::create(['agent_name' => 'Assigned Agent', 'code' => 'AG-A']));
        $supplier = Supplier::withoutEvents(fn () => Supplier::create(['supplier_name' => 'Assigned Supplier']));

        $hubStock = $this->createCrr('STK-HUB', ['hub_agent' => $hub->code, 'status' => Crr::STATUS_ACTIVE]);
        $this->createCrr('STK-OTHER', ['hub_agent' => $otherHub->code, 'status' => Crr::STATUS_ACTIVE]);
        $this->createCrr('STK-AGENT', ['hub_agent' => $agent->code, 'status' => Crr::STATUS_ACTIVE]);
        $this->createCrr('STK-SUPPLIER', ['supplier' => $supplier->supplier_name, 'status' => Crr::STATUS_ACTIVE]);

        $hubShipment = Shipment::create(['shipment_number' => 'SHIP-HUB', 'status' => 'In transit']);
        $hubShipment->crrs()->attach($hubStock);
        Shipment::create(['shipment_number' => 'SHIP-OTHER', 'status' => 'In transit']);
        Shipment::create([
            'shipment_number' => 'SHIP-AGENT',
            'status' => 'In transit',
            'departure' => 'agent:' . $agent->id,
        ]);
        $supplierShipment = Shipment::create(['shipment_number' => 'SHIP-SUPPLIER', 'status' => 'In transit']);
        $supplierShipment->crrs()->attach($this->createCrr('STK-SUPPLIER-LINK', ['supplier' => $supplier->supplier_name, 'status' => Crr::STATUS_ACTIVE]));

        $operations = User::factory()->create(['role' => 'Operations']);
        $accounts = User::factory()->create(['role' => 'Accounts']);
        $agentUser = User::factory()->create(['role' => 'Agents']);
        $supplierUser = User::factory()->create(['role' => 'Supplier']);

        $service = app(OperationsDashboardService::class);
        $expectedStocks = ['STK-HUB', 'STK-OTHER', 'STK-AGENT', 'STK-SUPPLIER', 'STK-SUPPLIER-LINK'];
        $expectedShipments = ['SHIP-HUB', 'SHIP-OTHER', 'SHIP-AGENT', 'SHIP-SUPPLIER'];

        foreach ([$operations, $accounts, $agentUser, $supplierUser] as $user) {
            $this->assertEqualsCanonicalizing($expectedStocks, $service->visibleCrrs($user)->pluck('stock_number')->all());
            $this->assertEqualsCanonicalizing($expectedShipments, $service->visibleShipments($user)->pluck('shipment_number')->all());
        }
    }

    public function test_dashboard_renders_live_data_and_action_links(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $stock = $this->createCrr('STK-RENDER', [
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-RENDER',
            'status' => 'In transit',
            'deadline_arrival' => today()->subDay(),
        ]);
        $shipment->crrs()->attach($stock);

        $this->actingAs($admin);
        $dashboard = app(OperationsDashboardService::class)->build($admin, 7);
        $html = view('home', compact('dashboard'))->render();

        $this->assertStringContainsString('Operations overview', $html);
        $this->assertStringContainsString('dash-hero', $html);
        $this->assertStringContainsString('SHIP-RENDER', $html);
        $this->assertStringContainsString('STK-RENDER', $html);
        $this->assertStringContainsString(route('shipments.edit', $shipment->id), $html);
        $this->assertStringContainsString(route('stocks.edit', $stock->id), $html);
    }

    private function createCrr(string $stockNumber, array $attributes = []): Crr
    {
        return Crr::create(array_merge([
            'stock_number' => $stockNumber,
            'content' => 'Shipspares',
            'status' => Crr::STATUS_NEW,
            'accept' => false,
            'flags' => [],
        ], $attributes));
    }
}
