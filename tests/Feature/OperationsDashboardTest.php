<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AdministrationChangeLog;
use App\Models\Crr;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerVessel;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use App\Services\OperationsDashboardService;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
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
        $this->assertSame(1, $dashboard['kpis']['newShipmentsToday']);
        $this->assertSame(0, $dashboard['kpis']['cancelledShipments']);
        $this->assertSame(1, $dashboard['kpis']['overdueArrivals']);
        $this->assertSame(1, $dashboard['kpis']['preAlertsDue']);
        $this->assertSame(2, $dashboard['kpis']['openIrregularities']);
        $this->assertSame(90, $dashboard['assistant']['shipmentCreationWindowDays']);
        $this->assertSame(1, $dashboard['assistant']['shipmentCreationCounts']['today']);
        $this->assertSame(1, $dashboard['assistant']['shipmentCreationCounts']['30']);
        $this->assertCount(90, $dashboard['assistant']['shipmentCreationDaily']);
        $this->assertCount(1, $dashboard['overdueShipments']);
        $this->assertSame('MC Assistant only shows details and summaries here. Create, update, and delete actions are not available.', $dashboard['assistant']['readOnly']);
        $this->assertContains('SHIP-001', collect($dashboard['assistant']['shipments'])->pluck('number')->all());
        $this->assertContains('STK-001', collect($dashboard['assistant']['stocks'])->pluck('number')->all());
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

    public function test_operations_dashboard_assistant_keeps_dashboard_scope(): void
    {
        $operations = User::factory()->create(['role' => 'Operations', 'is_active' => true]);
        $stock = $this->createCrr('STK-OPS-001', ['status' => Crr::STATUS_ACTIVE]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-OPS-001',
            'status' => 'In transit',
        ]);
        $shipment->crrs()->attach($stock);

        $dashboard = app(OperationsDashboardService::class)->build($operations);

        $this->assertSame('dashboard', $dashboard['assistant']['scope']['mode']);
        $this->assertTrue($dashboard['assistant']['scope']['allows']['overview']);
        $this->assertTrue($dashboard['assistant']['scope']['allows']['shipments']);
        $this->assertTrue($dashboard['assistant']['scope']['allows']['administration']);
        $this->assertContains('SHIP-OPS-001', collect($dashboard['assistant']['shipments'])->pluck('number')->all());
        $this->assertContains('STK-OPS-001', collect($dashboard['assistant']['stocks'])->pluck('number')->all());
    }

    public function test_agent_and_supplier_dashboard_assistant_are_limited_to_stock_scope(): void
    {
        $stock = $this->createCrr('CN-SCOPE-001', ['status' => Crr::STATUS_ACTIVE]);
        $shipment = Shipment::create([
            'shipment_number' => 'AZA-SCOPE-001',
            'status' => 'In transit',
            'deadline_arrival' => today()->subDay(),
            'pre_alert_reminder' => today(),
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($stock);
        $shipment->irregularities()->create(['status' => 'Open']);

        $service = app(OperationsDashboardService::class);

        foreach (['Agents', 'Supplier'] as $role) {
            $user = User::factory()->create(['role' => $role, 'is_active' => true]);
            $dashboard = $service->build($user);

            $this->assertSame($role, $dashboard['assistant']['scope']['role']);
            $this->assertSame('stocks-only', $dashboard['assistant']['scope']['mode']);
            $this->assertSame(['stock'], $dashboard['assistant']['scope']['allowedLookupTargets']);
            $this->assertFalse($dashboard['assistant']['scope']['allows']['overview']);
            $this->assertFalse($dashboard['assistant']['scope']['allows']['shipments']);
            $this->assertFalse($dashboard['assistant']['scope']['allows']['administration']);
            $this->assertTrue($dashboard['assistant']['scope']['allows']['stocks']);
            $this->assertTrue($dashboard['assistant']['scope']['allows']['stockFollowUps']);
            $this->assertSame(0, $dashboard['assistant']['kpis']['activeShipments']);
            $this->assertSame(0, $dashboard['assistant']['kpis']['newShipmentsToday']);
            $this->assertSame(0, $dashboard['assistant']['kpis']['overdueArrivals']);
            $this->assertSame(0, $dashboard['assistant']['kpis']['preAlertsDue']);
            $this->assertSame(0, $dashboard['assistant']['shipmentCreationWindowDays']);
            $this->assertSame(['today' => 0, '7' => 0, '30' => 0, '90' => 0], $dashboard['assistant']['shipmentCreationCounts']);
            $this->assertSame([], $dashboard['assistant']['shipmentCreationDaily']);
            $this->assertSame([], $dashboard['assistant']['shipmentStatuses']);
            $this->assertSame([], $dashboard['assistant']['services']);
            $this->assertSame([], $dashboard['assistant']['shipments']);
            $this->assertSame([], $dashboard['assistant']['overdueShipments']);
            $this->assertContains('CN-SCOPE-001', collect($dashboard['assistant']['stocks'])->pluck('number')->all());
        }
    }

    public function test_dashboard_exposes_cancelled_shipment_count_for_assistant(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        Shipment::create(['shipment_number' => 'SHIP-CANCEL-1', 'status' => 'Cancelled']);
        Shipment::create(['shipment_number' => 'SHIP-CANCEL-2', 'status' => 'Canceled']);
        Shipment::create(['shipment_number' => 'SHIP-LIVE-1', 'status' => 'In transit']);

        $dashboard = app(OperationsDashboardService::class)->build($admin, 30);

        $this->assertSame(2, $dashboard['kpis']['cancelledShipments']);
    }

    public function test_dashboard_exposes_today_created_shipment_count_for_assistant(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $shipmentTodayOne = Shipment::create([
            'shipment_number' => 'SHIP-TODAY-1',
            'status' => 'Draft',
        ]);
        $shipmentTodayOne->forceFill([
            'created_at' => now(),
            'updated_at' => now(),
        ])->saveQuietly();

        $shipmentTodayTwo = Shipment::create([
            'shipment_number' => 'SHIP-TODAY-2',
            'status' => 'In process',
        ]);
        $shipmentTodayTwo->forceFill([
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ])->saveQuietly();

        $shipmentOld = Shipment::create([
            'shipment_number' => 'SHIP-OLD-1',
            'status' => 'Completed',
        ]);
        $shipmentOld->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->saveQuietly();

        $dashboard = app(OperationsDashboardService::class)->build($admin, 30);

        $this->assertSame(2, $dashboard['kpis']['newShipmentsToday']);
        $this->assertSame(2, $dashboard['assistant']['kpis']['newShipmentsToday']);
        $this->assertSame(2, $dashboard['assistant']['shipmentCreationCounts']['today']);
        $this->assertSame(3, $dashboard['assistant']['shipmentCreationCounts']['7']);
        $this->assertSame(3, $dashboard['assistant']['shipmentCreationCounts']['30']);
        $this->assertSame(3, $dashboard['assistant']['shipmentCreationCounts']['90']);
        $this->assertCount(90, $dashboard['assistant']['shipmentCreationDaily']);
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
        $this->assertStringContainsString('dash-page', $html);
        $this->assertStringContainsString('dash-hero', $html);
        $this->assertStringContainsString('MC Assistant', $html);
        $this->assertStringContainsString('Ask in simple language about shipments, stocks, offices, hubs, agents, suppliers, customers, contacts, vessels, users, or administration change logs for a specific field, full details, or a dashboard overview.', $html);
        $this->assertStringContainsString('data-role="mc-assistant"', $html);
        $this->assertStringContainsString('Example: Who changed the address for MarineCaddie Dubai Office or what is the role for user sunnyazahar@gmail.com?', $html);
        $this->assertStringContainsString('Open', $html);
        $this->assertStringContainsString('Send', $html);
        $this->assertStringContainsString('Clear chat', $html);
        $this->assertStringContainsString('mcAssistantClear', $html);
        $this->assertNotFalse(strpos($html, 'id="styleSelector"'));
        $this->assertNotFalse(strpos($html, 'id="mcAssistantShell"'));
        $this->assertTrue(
            strpos($html, 'id="styleSelector"') < strpos($html, 'id="mcAssistantShell"'),
            'Dashboard assistant shell should render after the Pcoded shell to avoid mobile clipping.'
        );
        $this->assertStringContainsString('function isReadOnlyActionRequest(text)', $html);
        $this->assertStringContainsString('function detectShipmentCreationCountWindow(text)', $html);
        $this->assertStringContainsString('function isTodayShipmentCreationCountRequest(text)', $html);
        $this->assertStringContainsString('function isTransportDetailsRequest(text)', $html);
        $this->assertStringContainsString('function isFullRecordRequest(text)', $html);
        $this->assertStringContainsString('function isGenericRecordSummaryRequest(text)', $html);
        $this->assertStringContainsString('function renderShipmentCreationWindowSummary(windowInfo)', $html);
        $this->assertStringContainsString('function renderTodayShipmentCreationSummary()', $html);
        $this->assertStringContainsString('function buildShipmentNarrative(item)', $html);
        $this->assertStringContainsString('function textParagraphs(lines)', $html);
        $this->assertStringContainsString('function detectResponseLanguage(text)', $html);
        $this->assertStringContainsString('function syncAssistantChrome(preserveStatus)', $html);
        $this->assertStringContainsString('function scopeAllows(area)', $html);
        $this->assertStringContainsString('function isStockOnlyAssistant()', $html);
        $this->assertStringContainsString('function renderScopeBlockedResponse(scopeKey)', $html);
        $this->assertStringContainsString('function renderShipmentTransportDetail(item)', $html);
        $this->assertStringContainsString('function renderShipmentFieldResponse(item, text)', $html);
        $this->assertStringContainsString('function renderStockFieldResponse(item, text)', $html);
        $this->assertStringContainsString('function renderUnknownFieldResponse(type, item)', $html);
        $this->assertStringContainsString('function splitStandaloneQuestionSegments(text)', $html);
        $this->assertStringContainsString('function renderCombinedResponses(responses, kind, status)', $html);
        $this->assertStringContainsString('function looksLikeSensitiveCredentialRequest(text)', $html);
        $this->assertStringContainsString('function renderSensitiveLookupResponse(type, reason, item)', $html);
        $this->assertStringContainsString('function containsAdministrationEntityHint(text)', $html);
        $this->assertStringContainsString('function administrationIntentSuggestsSpecificField(text)', $html);
        $this->assertStringContainsString('function renderAdministrationDetail(type, item)', $html);
        $this->assertStringContainsString('function renderAdministrationFieldResponse(type, item, text)', $html);
        $this->assertStringContainsString('function responseForAdministrationQuery(type, item, query)', $html);
        $this->assertStringContainsString('function renderChangeLogDetail(item)', $html);
        $this->assertStringContainsString('function renderChangeLogFieldResponse(item, text)', $html);
        $this->assertStringContainsString('function responseForChangeLogQuery(item, query)', $html);
        $this->assertStringContainsString('Linked stock summary', $html);
        $this->assertStringContainsString('Cancelled shipment summary', $html);
        $this->assertStringContainsString('consigenee', $html);
        $this->assertStringContainsString('function detectInitialLanguage()', $html);
        $this->assertStringContainsString("language: 'english'", $html);
        $this->assertStringNotContainsString('push(text);', $html);
        $this->assertStringNotContainsString('<strong>Note:</strong>', $html);
        $this->assertStringNotContainsString('mcAssistantSuggestions', $html);
        $this->assertStringNotContainsString('function shouldShowSuggestions(query)', $html);
        $this->assertStringNotContainsString('mc-assistant-suggestion', $html);
        $this->assertStringNotContainsString('mcAssistantQuickActions', $html);
        $this->assertStringNotContainsString('mc-assistant-chip', $html);
        $this->assertStringNotContainsString('function detailRows(rows)', $html);
        $this->assertStringNotContainsString('mc-assistant-detail-grid', $html);
        $this->assertStringNotContainsString('Main snapshot', $html);
        $this->assertStringContainsString('Main ready hoon.', $html);
        $this->assertStringContainsString('I am ready.', $html);
        $this->assertStringContainsString('What I can help with', $html);
        $this->assertStringContainsString('Please be a bit more specific', $html);
        $this->assertStringContainsString('Complete summary for shipment', $html);
        $this->assertStringContainsString('Answer for shipment', $html);
        $this->assertStringContainsString('Example: Who changed the address for MarineCaddie Dubai Office or what is the role for user sunnyazahar@gmail.com?', $html);
        $this->assertStringContainsString('Ask in simple language about shipments, stocks, offices, hubs, agents, suppliers, customers, contacts, vessels, users, or administration change logs for a specific field, full details, or a dashboard overview.', $html);
        $this->assertStringContainsString('SHIP-RENDER', $html);
        $this->assertStringContainsString('STK-RENDER', $html);
        $this->assertStringContainsString(route('shipments.edit', $shipment->id), $html);
        $this->assertStringContainsString(route('stocks.edit', $stock->id), $html);
        $this->assertStringContainsString('ka complete summary', $html);
        $this->assertStringContainsString('kitna stocks add hai', $html);
        $this->assertStringContainsString('specific field', $html);
        $this->assertStringContainsString('exact field samajh nahi paya', $html);
        $this->assertStringContainsString('Sensitive detail blocked', $html);
        $this->assertStringContainsString('password ya credential jaisi sensitive login details share nahi kar sakta', $html);
        $this->assertStringContainsString('Main yahan action complete nahi kar sakta', $html);
        $this->assertStringContainsString('shipmentCreationCounts', $html);
        $this->assertStringContainsString('shipmentCreationDaily', $html);
        $this->assertStringContainsString('newShipmentsToday', $html);
        $this->assertStringContainsString('MarineCaddie Dubai Office ka address kisne change kiya', $html);
        $this->assertStringContainsString('last changes', $html);
        $this->assertStringContainsString('what happened', $html);
        $this->assertStringContainsString('last change kisne kiya tha', $html);
        $this->assertStringContainsString('shipment-compound-detail', $html);
        $this->assertStringContainsString('compound-response', $html);
        $this->assertStringContainsString('sunnyazahar@gmail.com ka user role batao', $html);
        $this->assertStringContainsString('ANGEL vessel ka IMO batao', $html);
    }

    public function test_dashboard_assistant_formats_string_dates_without_error(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $stock = $this->createCrr('STK-DATE', [
            'status' => Crr::STATUS_ACTIVE,
            'expected_delivery_date' => '2026-09-15',
            'customs_value' => 1450.75,
            'currency' => 'USD',
        ]);

        $dashboard = app(OperationsDashboardService::class)->build($admin, 30);
        $assistantStock = collect($dashboard['assistant']['stocks'])
            ->firstWhere('number', $stock->stock_number);

        $this->assertNotNull($assistantStock);
        $this->assertSame('15 Sep 2026', $assistantStock['expectedDeliveryDate']);
        $this->assertSame('1450.75', $assistantStock['customsValue']);
    }

    public function test_dashboard_assistant_lookup_finds_stock_details_from_natural_language_query(): void
    {
        $admin = $this->createAdminUser();
        $target = $this->createCrr('CN-72656522', [
            'status' => Crr::STATUS_ACTIVE,
            'supplier' => 'North Sea Supply',
            'hub_agent' => 'CN',
            'expected_delivery_date' => '2026-09-22',
            'currency' => 'USD',
            'customs_value' => 9876.54,
        ]);

        foreach (range(1, 14) as $index) {
            $this->createCrr('RECENT-' . $index, ['status' => Crr::STATUS_ACTIVE]);
        }

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'maine pucha CN-72656522 stock ka details batao']));

        $response->assertOk();
        $response->assertJsonPath('matched', true);
        $response->assertJsonPath('type', 'stock');
        $response->assertJsonPath('item.number', $target->stock_number);
        $response->assertJsonPath('item.supplier', 'North Sea Supply');
        $response->assertJsonPath('item.expectedDeliveryDate', '22 Sep 2026');
        $response->assertJsonPath('item.customsValue', '9876.54');

        $updatedAtResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'When was stock CN-72656522 last updated?']));

        $updatedAtResponse->assertOk();
        $updatedAtResponse->assertJsonPath('matched', true);
        $updatedAtResponse->assertJsonPath('type', 'stock');
        $updatedAtResponse->assertJsonPath('item.number', $target->stock_number);
    }

    public function test_dashboard_assistant_lookup_returns_complete_shipment_details_from_natural_language_query(): void
    {
        $admin = $this->createAdminUser();
        $creator = User::factory()->create(['name' => 'Azahar', 'role' => 'Admin', 'is_active' => true]);
        $modifier = User::factory()->create(['name' => 'Nadia Ops', 'role' => 'Operations', 'is_active' => true]);
        $accountManager = Contact::withoutEvents(fn () => Contact::create(['name' => 'Kundan Kumar']));
        $departureHub = Hub::withoutEvents(fn () => Hub::create(['hub_name' => 'MarineCaddie Shipping LLC', 'code' => 'DXB']));
        $consigneeAgent = Agent::withoutEvents(fn () => Agent::create(['agent_name' => 'SAF GLOBAL MARITIME PVT LTD', 'code' => 'BOM']));
        $customer = Customer::withoutEvents(fn () => Customer::create(['customer_name' => 'Campbell Shipping']));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'ANGEL',
        ]));

        Port::create(['type' => 'airport', 'iata_code' => 'DXB', 'city' => 'Dubai', 'country_name' => 'UAE']);
        Port::create(['type' => 'airport', 'iata_code' => 'BOM', 'city' => 'Mumbai', 'country_name' => 'India']);

        $stock = $this->createCrr('ICN-72656522', [
            'status' => Crr::STATUS_COMPLETED,
            'vessel_name' => 'ANGEL',
            'supplier' => 'GUMA TECH MARINE SERVICES',
            'hub_agent' => 'DXB',
            'hub_code' => 'DXB',
            'currency' => 'USD',
            'customs_value' => 22.00,
        ]);
        $stock->packages()->create([
            'weight' => 40.00,
            'cbm' => 0.05,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'AZA-41267-0926',
            'departure' => 'hub:' . $departureHub->id,
            'departure_port_code' => 'DXB',
            'consignee' => 'agent:' . $consigneeAgent->id,
            'consignee_port_code' => 'BOM',
            'consignee_address' => 'Takshashila Complex, Office No. 301, 3rd Floor, RHB Road, Mulund (West)',
            'consignee_city' => 'Mumbai',
            'consignee_district' => 'Mumbai Suburban',
            'consignee_zip' => '400080',
            'consignee_country' => 'India',
            'consignee_att' => 'Sunny',
            'consignee_email' => 'imam@safglobal.in',
            'location' => 'Mulund West',
            'service' => 'Airfreight',
            'additional_service' => 'Express',
            'customer_reference' => 'CAMP-7788',
            'not_applicable_for_consolidation' => true,
            'repacked_items' => 2,
            'repacked_weight' => 41.50,
            'stock_repacked_items' => 1,
            'stock_repacked_weight' => 40.00,
            'deadline_arrival' => '2026-09-08',
            'preferred_shipment_date' => '2026-09-07',
            'vessel_eta' => '2026-09-09',
            'vessel_etd' => '2026-09-10',
            'pre_alert_reminder' => '2026-09-07',
            'account_manager_id' => $accountManager->id,
            'created_by' => $creator->id,
            'special_considerations_destination' => 'Keep receiver informed before arrival.',
            'skip_instruction_dest' => true,
            'comments_departure_hub' => 'Check export packing before dispatch.',
            'skip_instruction_hub' => false,
            'comments_consignee' => 'Share arrival update on same day.',
            'skip_prealert' => true,
            'project_logistics' => true,
            'port_agency' => false,
            'status' => 'Cancelled',
            'flags' => ['Pick up'],
        ]);
        $shipment->forceFill([
            'created_at' => Carbon::parse('2026-09-04 10:30:00'),
            'updated_at' => Carbon::parse('2026-09-05 12:15:00'),
        ])->saveQuietly();
        $shipment->crrs()->attach($stock);
        $shipment->irregularities()->create(['status' => 'Open']);
        $shipment->documents()->create([
            'file_name' => 'Manifest',
            'file_path' => 'shipment_documents/manifest.pdf',
            'file_type' => 'Manifest',
            'is_internal' => true,
        ]);
        $shipment->documents()->create([
            'file_name' => 'Pre-alert',
            'file_path' => 'shipment_documents/pre-alert.pdf',
            'file_type' => 'Pre-alert',
            'is_internal' => false,
        ]);
        $shipment->flights()->create([
            'leg_reference' => 'AWB-99887766',
            'flight_number' => 'EK 600',
            'departure_date' => '2026-09-07',
            'arrival_date' => '2026-09-08',
            'arrival_time' => '19:45',
            'sort_order' => 0,
        ]);
        $shipment->changeLogs()->create([
            'user_id' => $modifier->id,
            'title' => 'Status edited',
            'description' => 'From Draft to Cancelled',
            'created_at' => Carbon::parse('2026-09-05 12:15:00'),
        ]);

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-41267-0926 ka details batao']));

        $response->assertOk();
        $response->assertJsonPath('matched', true);
        $response->assertJsonPath('type', 'shipment');
        $response->assertJsonPath('item.number', 'AZA-41267-0926');
        $response->assertJsonPath('item.status', 'Cancelled');
        $response->assertJsonPath('item.creationDate', '04 Sep 2026');
        $response->assertJsonPath('item.createdBy', 'Azahar');
        $response->assertJsonPath('item.updatedBy', 'Nadia Ops');
        $response->assertJsonPath('item.lastModificationLabel', 'Status edited');
        $response->assertJsonPath('item.lastModificationDescription', 'From Draft to Cancelled');
        $response->assertJsonPath('item.lastModifiedField', 'Status');
        $response->assertJsonPath('item.accountManager', 'Kundan Kumar');
        $response->assertJsonPath('item.flags.0', 'Pick up');
        $response->assertJsonPath('item.departure', 'MarineCaddie Shipping LLC');
        $response->assertJsonPath('item.departurePort', 'DXB, Dubai');
        $response->assertJsonPath('item.service', 'Airfreight');
        $response->assertJsonPath('item.additionalService', 'Express');
        $response->assertJsonPath('item.preferredShipmentDate', '07 Sep 2026');
        $response->assertJsonPath('item.deadlineArrival', '08 Sep 2026');
        $response->assertJsonPath('item.vesselEta', '09 Sep 2026');
        $response->assertJsonPath('item.vesselEtd', '10 Sep 2026');
        $response->assertJsonPath('item.preAlertReminder', '07 Sep 2026');
        $response->assertJsonPath('item.customerReference', 'CAMP-7788');
        $response->assertJsonPath('item.notApplicableForConsolidation', true);
        $response->assertJsonPath('item.customer', 'Campbell Shipping');
        $response->assertJsonPath('item.vessel', 'ANGEL');
        $response->assertJsonPath('item.consignee', 'SAF GLOBAL MARITIME PVT LTD');
        $response->assertJsonPath('item.consigneeAddress', 'Takshashila Complex, Office No. 301, 3rd Floor, RHB Road, Mulund (West)');
        $response->assertJsonPath('item.consigneeCity', 'Mumbai');
        $response->assertJsonPath('item.consigneeDistrict', 'Mumbai Suburban');
        $response->assertJsonPath('item.consigneeZip', '400080');
        $response->assertJsonPath('item.consigneeCountry', 'India');
        $response->assertJsonPath('item.consigneePort', 'BOM, Mumbai');
        $response->assertJsonPath('item.location', 'Mulund West');
        $response->assertJsonPath('item.contactPerson', 'Sunny');
        $response->assertJsonPath('item.consigneeEmail', 'imam@safglobal.in');
        $response->assertJsonPath('item.specialConsiderations', 'Keep receiver informed before arrival.');
        $response->assertJsonPath('item.commentsDepartureHub', 'Check export packing before dispatch.');
        $response->assertJsonPath('item.commentsConsignee', 'Share arrival update on same day.');
        $response->assertJsonPath('item.skipInstructionDestination', true);
        $response->assertJsonPath('item.skipInstructionHub', false);
        $response->assertJsonPath('item.skipPrealert', true);
        $response->assertJsonPath('item.projectLogistics', true);
        $response->assertJsonPath('item.portAgency', false);
        $response->assertJsonPath('item.stockCount', 1);
        $response->assertJsonPath('item.linkedStocks.0', 'ICN-72656522');
        $response->assertJsonPath('item.totalPackages', 1);
        $response->assertJsonPath('item.totalWeight', '40.00');
        $response->assertJsonPath('item.totalCbm', '0.05');
        $response->assertJsonPath('item.totalValue', '22.00');
        $response->assertJsonPath('item.totalValueDisplay', '22.00 USD');
        $response->assertJsonPath('item.stockItems.0.number', 'ICN-72656522');
        $response->assertJsonPath('item.stockItems.0.hub', 'DXB');
        $response->assertJsonPath('item.stockItems.0.supplier', 'GUMA TECH MARINE SERVICES');
        $response->assertJsonPath('item.stockItems.0.packages', 1);
        $response->assertJsonPath('item.stockItems.0.weight', '40.00');
        $response->assertJsonPath('item.stockItems.0.cbm', '0.05');
        $response->assertJsonPath('item.stockItems.0.value', '22.00 USD');
        $response->assertJsonPath('item.documentCount', 2);
        $response->assertJsonPath('item.documents.0.name', 'Manifest');
        $response->assertJsonPath('item.documents.1.name', 'Pre-alert');
        $response->assertJsonPath('item.stockRepackedItems', 1);
        $response->assertJsonPath('item.stockRepackedWeight', '40.00');
        $response->assertJsonPath('item.serviceRepackedItems', 2);
        $response->assertJsonPath('item.serviceRepackedWeight', '41.50');
        $response->assertJsonPath('item.transportHeading', 'Flight details');
        $response->assertJsonPath('item.transportUnitSingular', 'flight leg');
        $response->assertJsonPath('item.transportUnitPlural', 'flight legs');
        $response->assertJsonPath('item.transportLegCount', 1);
        $response->assertJsonPath('item.transportLegs.0.title', 'Flight leg 1');
        $response->assertJsonPath('item.transportLegs.0.referenceLabel', 'AWB');
        $response->assertJsonPath('item.transportLegs.0.reference', 'AWB-99887766');
        $response->assertJsonPath('item.transportLegs.0.carrierLabel', 'Flight');
        $response->assertJsonPath('item.transportLegs.0.carrier', 'EK 600');
        $response->assertJsonPath('item.transportLegs.0.departurePort', 'DXB, Dubai');
        $response->assertJsonPath('item.transportLegs.0.departureDate', '07 Sep 2026');
        $response->assertJsonPath('item.transportLegs.0.arrivalDate', '08 Sep 2026');
        $response->assertJsonPath('item.transportLegs.0.arrivalTime', '19:45');

        $fieldResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-41267-0926 ka kitna stocks add hai']));

        $fieldResponse->assertOk();
        $fieldResponse->assertJsonPath('matched', true);
        $fieldResponse->assertJsonPath('type', 'shipment');
        $fieldResponse->assertJsonPath('item.number', 'AZA-41267-0926');
        $fieldResponse->assertJsonPath('item.stockCount', 1);
        $fieldResponse->assertJsonPath('item.linkedStocks.0', 'ICN-72656522');

        $modifierResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-41267-0926 me last modification kisne kiya tha']));

        $modifierResponse->assertOk();
        $modifierResponse->assertJsonPath('matched', true);
        $modifierResponse->assertJsonPath('type', 'shipment');
        $modifierResponse->assertJsonPath('item.number', 'AZA-41267-0926');
        $modifierResponse->assertJsonPath('item.updatedBy', 'Nadia Ops');

        $modifiedFieldResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-41267-0926 me last modification field kya tha']));

        $modifiedFieldResponse->assertOk();
        $modifiedFieldResponse->assertJsonPath('matched', true);
        $modifiedFieldResponse->assertJsonPath('type', 'shipment');
        $modifiedFieldResponse->assertJsonPath('item.number', 'AZA-41267-0926');
        $modifiedFieldResponse->assertJsonPath('item.lastModifiedField', 'Status');

        $combinedLastChangeResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-41267-0926 me last change kisne kiya tha aur kya hua tha?']));

        $combinedLastChangeResponse->assertOk();
        $combinedLastChangeResponse->assertJsonPath('matched', true);
        $combinedLastChangeResponse->assertJsonPath('type', 'shipment');
        $combinedLastChangeResponse->assertJsonPath('item.number', 'AZA-41267-0926');
        $combinedLastChangeResponse->assertJsonPath('item.updatedBy', 'Nadia Ops');
        $combinedLastChangeResponse->assertJsonPath('item.lastModificationLabel', 'Status edited');
        $combinedLastChangeResponse->assertJsonPath('item.lastModificationDescription', 'From Draft to Cancelled');

        $englishLookupResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Who is the consignee for AZA-41267-0926?']));

        $englishLookupResponse->assertOk();
        $englishLookupResponse->assertJsonPath('matched', true);
        $englishLookupResponse->assertJsonPath('type', 'shipment');
        $englishLookupResponse->assertJsonPath('item.number', 'AZA-41267-0926');
        $englishLookupResponse->assertJsonPath('item.consignee', 'SAF GLOBAL MARITIME PVT LTD');

        $englishCreationDateResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'When was shipment AZA-41267-0926 created?']));

        $englishCreationDateResponse->assertOk();
        $englishCreationDateResponse->assertJsonPath('matched', true);
        $englishCreationDateResponse->assertJsonPath('type', 'shipment');
        $englishCreationDateResponse->assertJsonPath('item.creationDate', '04 Sep 2026');

        $englishCreatedByResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Who created shipment AZA-41267-0926?']));

        $englishCreatedByResponse->assertOk();
        $englishCreatedByResponse->assertJsonPath('matched', true);
        $englishCreatedByResponse->assertJsonPath('type', 'shipment');
        $englishCreatedByResponse->assertJsonPath('item.createdBy', 'Azahar');

        $englishUpdatedByResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Who made the last modification on shipment AZA-41267-0926?']));

        $englishUpdatedByResponse->assertOk();
        $englishUpdatedByResponse->assertJsonPath('matched', true);
        $englishUpdatedByResponse->assertJsonPath('type', 'shipment');
        $englishUpdatedByResponse->assertJsonPath('item.updatedBy', 'Nadia Ops');
    }

    public function test_dashboard_assistant_lookup_exposes_action_based_last_change_titles_for_shipments(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $modifier = User::factory()->create(['name' => 'Mila Ops', 'role' => 'Operations', 'is_active' => true]);

        $shipment = Shipment::create([
            'shipment_number' => 'AZA-27589-0926',
            'status' => 'In transit',
        ]);
        $shipment->forceFill([
            'created_at' => Carbon::parse('2026-09-05 09:00:00'),
            'updated_at' => Carbon::parse('2026-09-06 11:30:00'),
        ])->saveQuietly();
        $shipment->changeLogs()->create([
            'user_id' => $modifier->id,
            'title' => 'Pre-alert completed',
            'description' => 'Status changed from In process to In transit',
            'created_at' => Carbon::parse('2026-09-06 11:30:00'),
        ]);

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-27589-0926 me last modification field kya tha']));

        $response->assertOk();
        $response->assertJsonPath('matched', true);
        $response->assertJsonPath('type', 'shipment');
        $response->assertJsonPath('item.number', 'AZA-27589-0926');
        $response->assertJsonPath('item.updatedBy', 'Mila Ops');
        $response->assertJsonPath('item.lastModificationLabel', 'Pre-alert completed');
        $response->assertJsonPath('item.lastModifiedField', null);
    }

    public function test_stock_only_roles_block_shipment_and_administration_queries_but_allow_stock_queries(): void
    {
        $agentUser = User::factory()->create(['role' => 'Agents', 'is_active' => true]);
        $stock = $this->createCrr('CN-ROLE-001', [
            'status' => Crr::STATUS_ACTIVE,
            'supplier' => 'North Sea Supply',
        ]);
        User::factory()->create([
            'name' => 'Sunny Azahar',
            'email' => 'sunnyazahar@gmail.com',
            'role' => 'Operations',
            'is_active' => true,
        ]);
        $shipment = Shipment::create([
            'shipment_number' => 'AZA-ROLE-001',
            'status' => 'In transit',
        ]);
        $shipment->crrs()->attach($stock);
        Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'Campbell Shipping',
            'email' => 'ops@campbell.example',
        ]));
        $office = Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MarineCaddie Dubai Office',
            'office_short_name' => 'DXB HQ',
            'status' => 'Active',
        ]));
        AdministrationChangeLog::create([
            'loggable_type' => Office::class,
            'loggable_id' => $office->id,
            'field' => 'address',
            'title' => 'Address edited',
            'description' => 'From JLT to Port Rashid',
            'created_at' => Carbon::parse('2026-09-10 14:00:00'),
        ]);

        $shipmentResponse = $this->actingAsVerified($agentUser)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AZA-ROLE-001 shipment ka status batao']));

        $shipmentResponse->assertOk();
        $shipmentResponse->assertJsonPath('matched', false);
        $shipmentResponse->assertJsonPath('scopeBlocked', true);
        $shipmentResponse->assertJsonPath('type', 'shipment');
        $shipmentResponse->assertJsonPath('scope.mode', 'stocks-only');

        $customerResponse = $this->actingAsVerified($agentUser)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Campbell Shipping customer ka email batao']));

        $customerResponse->assertOk();
        $customerResponse->assertJsonPath('matched', false);
        $customerResponse->assertJsonPath('scopeBlocked', true);
        $customerResponse->assertJsonPath('type', 'customer');

        $userResponse = $this->actingAsVerified($agentUser)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'sunnyazahar@gmail.com ka user role kya hai']));

        $userResponse->assertOk();
        $userResponse->assertJsonPath('matched', false);
        $userResponse->assertJsonPath('scopeBlocked', true);
        $userResponse->assertJsonPath('type', 'user');

        $changeLogResponse = $this->actingAsVerified($agentUser)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MarineCaddie Dubai Office ka address kisne change kiya']));

        $changeLogResponse->assertOk();
        $changeLogResponse->assertJsonPath('matched', false);
        $changeLogResponse->assertJsonPath('scopeBlocked', true);
        $changeLogResponse->assertJsonPath('type', 'change_log');

        $stockResponse = $this->actingAsVerified($agentUser)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'CN-ROLE-001 stock ka supplier batao']));

        $stockResponse->assertOk();
        $stockResponse->assertJsonPath('matched', true);
        $stockResponse->assertJsonPath('scopeBlocked', false);
        $stockResponse->assertJsonPath('type', 'stock');
        $stockResponse->assertJsonPath('item.number', 'CN-ROLE-001');
        $stockResponse->assertJsonPath('item.supplier', 'North Sea Supply');
    }

    public function test_dashboard_assistant_lookup_answers_administration_change_log_queries(): void
    {
        $admin = $this->createAdminUser();
        $azahar = User::factory()->create(['name' => 'Azahar', 'role' => 'Operations', 'is_active' => true]);
        $nadia = User::factory()->create(['name' => 'Nadia Ops', 'role' => 'Operations', 'is_active' => true]);
        $imam = User::factory()->create(['name' => 'Imam', 'role' => 'Operations', 'is_active' => true]);

        $office = Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MarineCaddie Dubai Office',
            'office_short_name' => 'DXB HQ',
            'status' => 'Active',
        ]));
        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'Campbell Shipping',
            'email' => 'ops@campbell.example',
        ]));

        AdministrationChangeLog::create([
            'loggable_type' => Office::class,
            'loggable_id' => $office->id,
            'user_id' => $nadia->id,
            'field' => 'address',
            'title' => 'Address edited',
            'description' => 'From JLT Cluster X to Port Rashid',
            'created_at' => Carbon::parse('2026-09-09 10:30:00'),
        ]);
        AdministrationChangeLog::create([
            'loggable_type' => Office::class,
            'loggable_id' => $office->id,
            'user_id' => $imam->id,
            'field' => 'email',
            'title' => 'Email edited',
            'description' => 'From dxb@old.test to dxb@new.test',
            'created_at' => Carbon::parse('2026-09-10 14:00:00'),
        ]);
        AdministrationChangeLog::create([
            'loggable_type' => Customer::class,
            'loggable_id' => $customer->id,
            'user_id' => $nadia->id,
            'field' => 'remarks',
            'title' => 'Remarks edited',
            'description' => 'From blank to Priority boarding',
            'created_at' => Carbon::parse('2026-09-11 09:15:00'),
        ]);
        AdministrationChangeLog::create([
            'loggable_type' => Customer::class,
            'loggable_id' => $customer->id,
            'user_id' => $azahar->id,
            'field' => 'invoice_email',
            'title' => 'Invoice email edited',
            'description' => 'From old@campbell.test to purchasing@campbellshipping.com',
            'created_at' => Carbon::parse('2026-09-11 10:00:00'),
        ]);
        AdministrationChangeLog::create([
            'loggable_type' => Customer::class,
            'loggable_id' => $customer->id,
            'user_id' => $azahar->id,
            'field' => 'phone',
            'title' => 'Phone edited',
            'description' => 'Updated customer phone number',
            'created_at' => Carbon::parse('2026-09-11 11:30:00'),
        ]);

        $addressChangedByResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MarineCaddie Dubai Office ka address kisne change kiya?']));

        $addressChangedByResponse->assertOk();
        $addressChangedByResponse->assertJsonPath('matched', true);
        $addressChangedByResponse->assertJsonPath('type', 'change_log');
        $addressChangedByResponse->assertJsonPath('item.logs.0.recordName', 'MarineCaddie Dubai Office');
        $addressChangedByResponse->assertJsonPath('item.logs.0.field', 'Address');
        $addressChangedByResponse->assertJsonPath('item.logs.0.userName', 'Nadia Ops');
        $this->assertSame('1', $this->assistantFieldValue($addressChangedByResponse, 'Total matching logs'));
        $this->assertSame('Nadia Ops', $this->assistantFieldValue($addressChangedByResponse, 'Latest changed by'));

        $officeLastChangeResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MarineCaddie Dubai Office me last modification kya tha?']));

        $officeLastChangeResponse->assertOk();
        $officeLastChangeResponse->assertJsonPath('matched', true);
        $officeLastChangeResponse->assertJsonPath('type', 'change_log');
        $officeLastChangeResponse->assertJsonPath('item.logs.0.recordName', 'MarineCaddie Dubai Office');
        $officeLastChangeResponse->assertJsonPath('item.logs.0.field', 'Email');
        $officeLastChangeResponse->assertJsonPath('item.logs.0.userName', 'Imam');
        $this->assertSame('2', $this->assistantFieldValue($officeLastChangeResponse, 'Total matching logs'));
        $this->assertSame('Email edited', $this->assistantFieldValue($officeLastChangeResponse, 'Latest change'));

        $customerLastFieldResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Campbell Shipping customer ka last modification field kya tha?']));

        $customerLastFieldResponse->assertOk();
        $customerLastFieldResponse->assertJsonPath('matched', true);
        $customerLastFieldResponse->assertJsonPath('type', 'change_log');
        $customerLastFieldResponse->assertJsonPath('item.logs.0.recordName', 'Campbell Shipping');
        $customerLastFieldResponse->assertJsonPath('item.logs.0.field', 'Phone');
        $this->assertSame('Phone', $this->assistantFieldValue($customerLastFieldResponse, 'Latest changed field'));

        $userSpecificChangeResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Azahar ne CAMPBELL SHIPPING me last changes kya kiye thein?']));

        $userSpecificChangeResponse->assertOk();
        $userSpecificChangeResponse->assertJsonPath('matched', true);
        $userSpecificChangeResponse->assertJsonPath('type', 'change_log');
        $userSpecificChangeResponse->assertJsonPath('item.logs.0.recordName', 'Campbell Shipping');
        $userSpecificChangeResponse->assertJsonPath('item.logs.0.userName', 'Azahar');
        $userSpecificChangeResponse->assertJsonPath('item.logs.0.field', 'Phone');
        $userSpecificChangeResponse->assertJsonPath('item.matchedCount', 2);
        $this->assertSame(['Azahar', 'Azahar'], collect($userSpecificChangeResponse->json('item.logs'))->pluck('userName')->all());

        $windowCountResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'last 30 days me administration change logs kitne hue?']));

        $windowCountResponse->assertOk();
        $windowCountResponse->assertJsonPath('matched', true);
        $windowCountResponse->assertJsonPath('type', 'change_log');
        $windowCountResponse->assertJsonPath('item.matchedCount', 5);
        $this->assertSame('5', $this->assistantFieldValue($windowCountResponse, 'Total matching logs'));
        $this->assertSame('Azahar', $this->assistantFieldValue($windowCountResponse, 'Latest changed by'));
    }

    public function test_dashboard_assistant_lookup_matches_administration_entities_and_maps_key_fields(): void
    {
        $admin = $this->createAdminUser();
        $uae = Country::create(['name' => 'United Arab Emirates']);
        $india = Country::create(['name' => 'India']);

        $office = Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MarineCaddie Dubai Office',
            'office_short_name' => 'DXB HQ',
            'phone_number' => '+971501112223',
            'email' => 'dxb.office@marinecaddie.test',
            'eori_number' => 'AE-EORI-9001',
            'address' => 'JLT Cluster X',
            'city' => 'Dubai',
            'country_id' => $uae->id,
            'postal_address' => 'PO Box 1234',
            'postal_city' => 'Dubai',
            'office_country_id' => $uae->id,
            'invoicing_currency' => 'AED',
            'status' => 'Active',
        ]));
        $office->bankAccounts()->create([
            'bank' => 'Emirates NBD',
            'currency' => 'AED',
            'account_number' => '123456789012',
            'iban' => 'AE070331234567890123456',
            'swift' => 'EBILAEAD',
            'is_main_account' => true,
        ]);
        Contact::withoutEvents(fn () => Contact::create([
            'office_id' => $office->id,
            'name' => 'Nadia Office',
            'email' => 'nadia.office@marinecaddie.test',
            'phone_number' => '+971509998877',
        ]));

        $hub = Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'Dubai Hub',
            'code' => 'DXB',
            'email' => 'hub.dxb@marinecaddie.test',
            'contact_person' => 'Rashid Khan',
            'hub_address' => 'Warehouse 7, JAFZA',
            'city' => 'Dubai',
            'country' => 'United Arab Emirates',
            'port_code' => 'DXB',
            'portal_email' => 'portal.dxb@marinecaddie.test',
            'special_considerations' => 'Check DG handling',
            'scan_gun_password' => 'secret123',
        ]));

        $agent = Agent::withoutEvents(fn () => Agent::create([
            'agent_name' => 'Mumbai Partner',
            'code' => 'BOM',
            'email' => 'agent.bom@marinecaddie.test',
            'contact_person' => 'Priya Singh',
            'agent_address' => 'Nariman Point',
            'city' => 'Mumbai',
            'country_id' => $india->id,
            'port_code' => 'BOM',
            'responsible_manager' => 'Kundan Kumar',
        ]));

        $supplier = Supplier::withoutEvents(fn () => Supplier::create([
            'supplier_name' => 'Guma Tech Marine Services',
            'email' => 'ops@guma.example',
            'contact_person' => 'Adeel',
            'currency' => 'USD',
            'supplier_address' => 'Busan Port Road',
            'city' => 'Busan',
            'port_code' => 'KRPUS',
        ]));

        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'Campbell Shipping',
            'customer_number' => 'CUST-001',
            'email' => 'ops@campbell.example',
            'phone' => '+44 20 1234 5678',
            'contact_person' => 'Laura Moss',
            'show_transport_details' => true,
        ]));
        $customer->addresses()->createMany([
            [
                'type' => 'primary',
                'street' => '12 Dock Street',
                'city' => 'London',
                'state' => 'Greater London',
                'zip_code' => 'E14 9QG',
                'country_id' => $uae->id,
                'port_code' => 'GBLON',
            ],
            [
                'type' => 'invoice',
                'street' => '18 Billing Wharf',
                'city' => 'London',
                'state' => 'Greater London',
                'zip_code' => 'E14 9AB',
                'country_id' => $india->id,
            ],
        ]);
        $customer->invoiceDetail()->create([
            'invoice_recipient_name' => 'Campbell Accounts',
            'invoice_email' => 'finance@campbell.example',
            'invoice_email_cc' => 'ap@campbell.example',
            'currency_code' => 'USD',
            'payment_terms_days' => 30,
        ]);
        $customerAccountManager = Contact::withoutEvents(fn () => Contact::create([
            'office_id' => $office->id,
            'name' => 'Kundan Account',
            'email' => 'kundan.account@marinecaddie.test',
        ]));
        $customer->responsible()->create([
            'account_manager_id' => $customerAccountManager->id,
        ]);
        $customer->notificationSetting()->create([
            'notify_stock_items' => 'Daily',
            'send_automatic_first_mile_email' => true,
            'shipping_free_storage_days' => 7,
        ]);

        $vesselContact = Contact::withoutEvents(fn () => Contact::create([
            'customer_id' => $customer->id,
            'name' => 'Rina Dsouza',
            'email' => 'rina@angel.example',
            'phone_number' => '+91 22 7654 3210',
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'ANGEL',
            'vessel_imo' => '9384721',
            'customer_vessel_code' => 'ANG-01',
            'manager' => 'Captain Noor',
            'home_delivery_port' => 'Mumbai',
            'remarks' => 'Priority dry provisions',
            'contact_id' => $vesselContact->id,
            'contact_stocklists' => true,
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'CS SATIRA',
            'vessel_imo' => '9635456',
            'manager' => 'Preeti Mundhara',
        ]));

        $cases = [
            [
                'query' => 'MarineCaddie Dubai Office office ka phone number batao',
                'type' => 'office',
                'name' => 'MarineCaddie Dubai Office',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('+971501112223', $this->assistantFieldValue($response, 'Phone number'));
                    $bankAccounts = $this->assistantFieldValue($response, 'Bank accounts');
                    $this->assertNotNull($bankAccounts);
                    $this->assertStringContainsString('********9012', $bankAccounts);
                    $this->assertStringNotContainsString('123456789012', $bankAccounts);
                },
            ],
            [
                'query' => 'DXB hub ka scan gun password batao',
                'type' => 'hub',
                'name' => 'Dubai Hub',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('DXB', $response->json('item.identifier'));
                    $this->assertSame('Hidden for security', $this->assistantFieldValue($response, 'Scan gun password'));
                },
            ],
            [
                'query' => 'BOM agent ka responsible manager batao',
                'type' => 'agent',
                'name' => 'Mumbai Partner',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('Kundan Kumar', $this->assistantFieldValue($response, 'Responsible manager'));
                },
            ],
            [
                'query' => 'Guma Tech Marine Services supplier ka currency batao',
                'type' => 'supplier',
                'name' => 'Guma Tech Marine Services',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('USD', $this->assistantFieldValue($response, 'Currency'));
                },
            ],
            [
                'query' => 'Campbell Shipping customer ka complete summary batao',
                'type' => 'customer',
                'name' => 'Campbell Shipping',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('finance@campbell.example', $this->assistantFieldValue($response, 'Invoice email'));
                    $this->assertSame('Kundan Account · DXB HQ · kundan.account@marinecaddie.test', $this->assistantFieldValue($response, 'Account manager'));
                    $this->assertSame('2 vessels: ANGEL · IMO 9384721, CS SATIRA · IMO 9635456', $this->assistantFieldValue($response, 'Vessels'));
                },
            ],
            [
                'query' => 'ANGEL vessel ka IMO batao',
                'type' => 'vessel',
                'name' => 'ANGEL',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('9384721', $this->assistantFieldValue($response, 'Vessel IMO'));
                    $this->assertSame('Rina Dsouza · rina@angel.example · +91 22 7654 3210', $this->assistantFieldValue($response, 'Main contact'));
                },
            ],
            [
                'query' => 'CAMPBELL SHIPPING ke pas total kitne vessels hai',
                'type' => 'customer',
                'name' => 'Campbell Shipping',
                'assert' => function (TestResponse $response): void {
                    $this->assertSame('2', $this->assistantFieldValue($response, 'Total vessels'));
                },
            ],
            [
                'query' => 'CAMPBELL SHIPPING ke kaun se vessels hai',
                'type' => 'customer',
                'name' => 'Campbell Shipping',
                'assert' => function (TestResponse $response): void {
                    $vesselNames = $this->assistantFieldValue($response, 'Vessel names');
                    $this->assertNotNull($vesselNames);
                    $this->assertStringContainsString('ANGEL', $vesselNames);
                    $this->assertStringContainsString('CS SATIRA', $vesselNames);
                },
            ],
        ];

        foreach ($cases as $case) {
            $response = $this->actingAsVerified($admin)
                ->getJson(route('dashboard.assistant-lookup', ['q' => $case['query']]));

            $response->assertOk();
            $response->assertJsonPath('matched', true);
            $response->assertJsonPath('type', $case['type']);
            $response->assertJsonPath('item.name', $case['name']);

            $case['assert']($response);
        }
    }

    public function test_dashboard_assistant_lookup_matches_user_questions_by_email_and_name(): void
    {
        $admin = $this->createAdminUser();

        $office = Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MarineCaddie Dubai Office',
            'office_short_name' => 'DXB HQ',
            'status' => 'Active',
        ]));
        $hub = Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'Dubai Hub',
            'code' => 'DXB',
        ]));
        $agent = Agent::withoutEvents(fn () => Agent::create([
            'agent_name' => 'Mumbai Partner',
            'code' => 'BOM',
        ]));
        $supplier = Supplier::withoutEvents(fn () => Supplier::create([
            'supplier_name' => 'North Sea Supply',
        ]));

        $portalUser = User::factory()->create([
            'name' => 'Sunny Azahar',
            'email' => 'sunnyazahar@gmail.com',
            'phone_number' => '+91 98765 43210',
            'role' => 'Operations',
            'is_active' => true,
        ]);
        $portalUser->offices()->attach($office);
        $portalUser->hubs()->attach($hub);
        $portalUser->agents()->attach($agent);
        $portalUser->suppliers()->attach($supplier);

        $roleResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'sunnyazahar@gmail.com ka user role kya hai?']));

        $roleResponse->assertOk();
        $roleResponse->assertJsonPath('matched', true);
        $roleResponse->assertJsonPath('type', 'user');
        $roleResponse->assertJsonPath('item.name', 'Sunny Azahar');
        $roleResponse->assertJsonPath('item.identifier', 'sunnyazahar@gmail.com');
        $this->assertSame('Operations', $this->assistantFieldValue($roleResponse, 'Role'));

        $usernameResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the username for user sunnyazahar@gmail.com?']));

        $usernameResponse->assertOk();
        $usernameResponse->assertJsonPath('matched', true);
        $usernameResponse->assertJsonPath('type', 'user');
        $this->assertSame('sunnyazahar', $this->assistantFieldValue($usernameResponse, 'Username'));

        $summaryResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Show the complete summary for user Sunny Azahar.']));

        $summaryResponse->assertOk();
        $summaryResponse->assertJsonPath('matched', true);
        $summaryResponse->assertJsonPath('type', 'user');
        $summaryResponse->assertJsonPath('item.name', 'Sunny Azahar');
        $this->assertSame('1 office: MarineCaddie Dubai Office · DXB HQ', $this->assistantFieldValue($summaryResponse, 'Assigned offices'));
        $this->assertSame('1 hub: Dubai Hub · DXB', $this->assistantFieldValue($summaryResponse, 'Assigned hubs'));
        $this->assertSame('1 agent: Mumbai Partner · BOM', $this->assistantFieldValue($summaryResponse, 'Assigned agents'));
        $this->assertSame('1 supplier: North Sea Supply', $this->assistantFieldValue($summaryResponse, 'Assigned suppliers'));
    }

    public function test_dashboard_assistant_lookup_blocks_sensitive_user_password_queries(): void
    {
        $admin = $this->createAdminUser();

        User::factory()->create([
            'name' => 'Azahar',
            'email' => 'sunnyazahar@gmail.com',
            'role' => 'Admin',
            'is_active' => true,
        ]);

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'sunnyazahar@gmail.com ka user ka password kya hai?']));

        $response->assertOk();
        $response->assertJsonPath('matched', false);
        $response->assertJsonPath('type', 'user');
        $response->assertJsonPath('scopeBlocked', false);
        $response->assertJsonPath('sensitiveBlocked', true);
        $response->assertJsonPath('blockedReason', 'credentials');
        $response->assertJsonPath('item', null);
    }

    public function test_dashboard_assistant_lookup_prefers_contact_for_person_queries_and_avoids_partial_vessel_name_matches(): void
    {
        $admin = $this->createAdminUser();
        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'MSC SHIPMANAGEMENT LIMITED',
            'email' => 'ops@msc.example',
        ]));
        $contact = Contact::withoutEvents(fn () => Contact::create([
            'customer_id' => $customer->id,
            'name' => 'Cyrus Dela Cruz',
            'email' => 'cyrus@msc.example',
            'phone_number' => '+63 917 111 2222',
            'description' => 'Procurement contact',
            'is_main_contact' => true,
            'reply_to_email' => 'reply@msc.example',
            'is_cc_enabled' => true,
            'status' => 'Active',
            'category' => 'Operations',
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'MSC VERACRUZ V',
            'vessel_name_alias' => 'MSCVV',
            'vessel_imo' => '9287924',
            'manager' => 'Maria Mylona',
            'account_manager' => 'Pankaj Kumar',
            'contact_id' => $contact->id,
        ]));

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Cyrus Dela Cruz kon hai details batao']));

        $response->assertOk();
        $response->assertJsonPath('matched', true);
        $response->assertJsonPath('type', 'contact');
        $response->assertJsonPath('item.name', 'Cyrus Dela Cruz');
        $response->assertJsonPath('item.identifier', 'cyrus@msc.example');
        $response->assertJsonPath('item.status', 'Active');
        $this->assertSame('Customer', $this->assistantFieldValue($response, 'Linked record type'));
        $this->assertSame('MSC SHIPMANAGEMENT LIMITED', $this->assistantFieldValue($response, 'Linked record name'));
        $this->assertSame('1 vessel: MSC VERACRUZ V · IMO 9287924', $this->assistantFieldValue($response, 'Linked vessels'));
    }

    public function test_dashboard_assistant_lookup_keeps_vessel_contact_fields_on_vessel_target(): void
    {
        $admin = $this->createAdminUser();
        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'Campbell Shipping',
            'email' => 'ops@campbell.example',
        ]));
        $mainContact = Contact::withoutEvents(fn () => Contact::create([
            'customer_id' => $customer->id,
            'name' => 'Rina Dsouza',
            'email' => 'rina@angel.example',
            'phone_number' => '+91 22 7654 3210',
        ]));
        Contact::withoutEvents(fn () => Contact::create([
            'customer_id' => $customer->id,
            'name' => 'Karan Tikare',
            'email' => 'main@kaizenship.net',
            'phone_number' => '+91 72493 03080',
        ]));
        Contact::withoutEvents(fn () => Contact::create([
            'customer_id' => $customer->id,
            'name' => 'Preeti Mundhara',
            'email' => 'preeti.m@campbellshipping.com',
            'phone_number' => '+91 88790 74542',
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'ANGEL',
            'vessel_imo' => '9384721',
            'contact_id' => $mainContact->id,
            'contact_pre_alerts' => false,
        ]));

        $mainContactResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'ANGEL vessel ka main contact batao']));

        $mainContactResponse->assertOk();
        $mainContactResponse->assertJsonPath('matched', true);
        $mainContactResponse->assertJsonPath('type', 'vessel');
        $mainContactResponse->assertJsonPath('item.name', 'ANGEL');
        $this->assertSame('Rina Dsouza · rina@angel.example · +91 22 7654 3210', $this->assistantFieldValue($mainContactResponse, 'Main contact'));

        $preAlertResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the contact pre alerts for vessel ANGEL?']));

        $preAlertResponse->assertOk();
        $preAlertResponse->assertJsonPath('matched', true);
        $preAlertResponse->assertJsonPath('type', 'vessel');
        $preAlertResponse->assertJsonPath('item.name', 'ANGEL');
        $this->assertSame('No', $this->assistantFieldValue($preAlertResponse, 'Contact pre alerts'));
    }

    public function test_dashboard_assistant_lookup_keeps_vessel_customer_fields_on_vessel_target(): void
    {
        $admin = $this->createAdminUser();
        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'MSC SHIPMANAGEMENT LIMITED',
            'email' => 'ops@msc.example',
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'MSC Unific VI',
            'vessel_imo' => '9168843',
            'customer_vessel_code' => 'UNIFIC-06',
            'yearly_customer_reference' => 'MSC-2026-UNI',
        ]));
        CustomerVessel::withoutEvents(fn () => CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'MSC SARA ELENA',
            'vessel_imo' => '9702261',
        ]));

        $customerResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the customer for vessel MSC Unific VI?']));

        $customerResponse->assertOk();
        $customerResponse->assertJsonPath('matched', true);
        $customerResponse->assertJsonPath('type', 'vessel');
        $customerResponse->assertJsonPath('item.name', 'MSC Unific VI');
        $this->assertSame('MSC SHIPMANAGEMENT LIMITED', $this->assistantFieldValue($customerResponse, 'Customer'));

        $vesselCodeResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MSC Unific VI vessel ka customer vessel code batao']));

        $vesselCodeResponse->assertOk();
        $vesselCodeResponse->assertJsonPath('matched', true);
        $vesselCodeResponse->assertJsonPath('type', 'vessel');
        $vesselCodeResponse->assertJsonPath('item.name', 'MSC Unific VI');
        $this->assertSame('UNIFIC-06', $this->assistantFieldValue($vesselCodeResponse, 'Customer vessel code'));

        $referenceResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the yearly customer reference for vessel MSC Unific VI?']));

        $referenceResponse->assertOk();
        $referenceResponse->assertJsonPath('matched', true);
        $referenceResponse->assertJsonPath('type', 'vessel');
        $referenceResponse->assertJsonPath('item.name', 'MSC Unific VI');
        $this->assertSame('MSC-2026-UNI', $this->assistantFieldValue($referenceResponse, 'Yearly customer reference'));

        $customerVesselCountResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MSC SHIPMANAGEMENT LIMITED ke pas total kitne vessels hai']));

        $customerVesselCountResponse->assertOk();
        $customerVesselCountResponse->assertJsonPath('matched', true);
        $customerVesselCountResponse->assertJsonPath('type', 'customer');
        $customerVesselCountResponse->assertJsonPath('item.name', 'MSC SHIPMANAGEMENT LIMITED');
        $this->assertSame('2', $this->assistantFieldValue($customerVesselCountResponse, 'Total vessels'));

        $customerVesselNamesResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MSC SHIPMANAGEMENT LIMITED ke kaun se vessels hai']));

        $customerVesselNamesResponse->assertOk();
        $customerVesselNamesResponse->assertJsonPath('matched', true);
        $customerVesselNamesResponse->assertJsonPath('type', 'customer');
        $customerVesselNamesResponse->assertJsonPath('item.name', 'MSC SHIPMANAGEMENT LIMITED');
        $this->assertSame('MSC Unific VI, MSC SARA ELENA', $this->assistantFieldValue($customerVesselNamesResponse, 'Vessel names'));
    }

    public function test_dashboard_assistant_lookup_matches_customer_names_with_special_characters(): void
    {
        $admin = $this->createAdminUser();
        $country = Country::create(['name' => 'Greece']);

        $customer = Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'AP&A GROUP',
            'email' => 'apa-group@example.test',
            'contact_person' => 'Elena',
            'internal_shipment' => 'Yes',
            'special_considerations' => 'Handle bonded delivery carefully.',
            'un_locode' => 'GRPIR',
            'esea_store_stock_only' => true,
        ]));
        $customer->addresses()->create([
            'type' => 'primary',
            'street' => '12 Port Gate',
            'city' => 'Piraeus',
            'state' => 'Attica',
            'zip_code' => '18531',
            'country_id' => $country->id,
            'port_code' => 'GRPIR',
        ]);

        Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MARINECADDIE INDIA PRIVATE LIMITED',
            'office_short_name' => 'MC INDIA',
            'email' => 'india.office@marinecaddie.test',
            'address' => 'Mumbai',
            'city' => 'Mumbai',
            'country_id' => $country->id,
            'office_country_id' => $country->id,
            'status' => 'Active',
        ]));

        Supplier::withoutEvents(fn () => Supplier::create([
            'supplier_name' => 'MICROFINISH VALVES PVT LTD.,',
            'email' => 'ops@microfinish.test',
            'contact_person' => 'Arun',
        ]));

        $hinglishResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AP&A GROUP customer ka email batao']));

        $hinglishResponse->assertOk();
        $hinglishResponse->assertJsonPath('matched', true);
        $hinglishResponse->assertJsonPath('type', 'customer');
        $hinglishResponse->assertJsonPath('item.name', 'AP&A GROUP');
        $this->assertSame('apa-group@example.test', $this->assistantFieldValue($hinglishResponse, 'Email'));

        $englishResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the email for customer AP&A GROUP?']));

        $englishResponse->assertOk();
        $englishResponse->assertJsonPath('matched', true);
        $englishResponse->assertJsonPath('type', 'customer');
        $englishResponse->assertJsonPath('item.name', 'AP&A GROUP');
        $this->assertSame('apa-group@example.test', $this->assistantFieldValue($englishResponse, 'Email'));

        $internalShipmentResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the internal shipment for customer AP&A GROUP?']));

        $internalShipmentResponse->assertOk();
        $internalShipmentResponse->assertJsonPath('matched', true);
        $internalShipmentResponse->assertJsonPath('type', 'customer');
        $internalShipmentResponse->assertJsonPath('item.name', 'AP&A GROUP');
        $this->assertSame('Yes', $this->assistantFieldValue($internalShipmentResponse, 'Internal shipment'));

        $primaryAddressResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'AP&A GROUP customer ka primary address batao']));

        $primaryAddressResponse->assertOk();
        $primaryAddressResponse->assertJsonPath('matched', true);
        $primaryAddressResponse->assertJsonPath('type', 'customer');
        $primaryAddressResponse->assertJsonPath('item.name', 'AP&A GROUP');
        $this->assertSame('12 Port Gate, Piraeus, Attica, 18531, Greece', $this->assistantFieldValue($primaryAddressResponse, 'Primary address'));
    }

    public function test_dashboard_assistant_lookup_matches_unique_agent_name_without_explicit_type(): void
    {
        $admin = $this->createAdminUser();
        $country = Country::create(['name' => 'Netherlands']);

        Agent::withoutEvents(fn () => Agent::create([
            'agent_name' => 'LOYAL CARGO SERVICES B.V.',
            'code' => 'AMS',
            'email' => 'ops@loyalcargo.example',
            'contact_person' => 'Mira Voss',
            'agent_address' => 'Rijnlanderweg 766J, 2132 NM Hoofddorp',
            'city' => 'Hoofddorp',
            'country_id' => $country->id,
            'port_code' => 'NLAMS',
        ]));

        $addressResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'LOYAL CARGO SERVICES B.V. ka address batao']));

        $addressResponse->assertOk();
        $addressResponse->assertJsonPath('matched', true);
        $addressResponse->assertJsonPath('type', 'agent');
        $addressResponse->assertJsonPath('item.name', 'LOYAL CARGO SERVICES B.V.');
        $this->assertSame('Rijnlanderweg 766J, 2132 NM Hoofddorp', $this->assistantFieldValue($addressResponse, 'Agent address'));

        $typoAddressResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'LOYAL CARGO SERVICES B.V. ka adress batao']));

        $typoAddressResponse->assertOk();
        $typoAddressResponse->assertJsonPath('matched', true);
        $typoAddressResponse->assertJsonPath('type', 'agent');
        $typoAddressResponse->assertJsonPath('item.name', 'LOYAL CARGO SERVICES B.V.');
        $this->assertSame('Rijnlanderweg 766J, 2132 NM Hoofddorp', $this->assistantFieldValue($typoAddressResponse, 'Agent address'));

        $nameOnlyResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'LOYAL CARGO SERVICES B.V.']));

        $nameOnlyResponse->assertOk();
        $nameOnlyResponse->assertJsonPath('matched', true);
        $nameOnlyResponse->assertJsonPath('type', 'agent');
        $nameOnlyResponse->assertJsonPath('item.name', 'LOYAL CARGO SERVICES B.V.');
        $this->assertSame('ops@loyalcargo.example', $this->assistantFieldValue($nameOnlyResponse, 'Email'));
    }

    public function test_dashboard_assistant_lookup_prefers_exact_office_name_and_supports_created_by_queries(): void
    {
        $admin = $this->createAdminUser();
        $creator = User::factory()->create(['name' => 'Sunny Creator', 'role' => 'Admin', 'is_active' => true]);
        $singapore = Country::create(['name' => 'Singapore']);

        Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'office_short_name' => 'SIN HQ',
            'email' => 'sin.office@marinecaddie.test',
            'address' => 'HarbourFront',
            'city' => 'Singapore',
            'country_id' => $singapore->id,
            'office_country_id' => $singapore->id,
            'status' => 'Active',
            'created_by' => $creator->id,
        ]));
        Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MARINECADDIE SHIPPING LLC',
            'office_short_name' => 'MC DXB',
            'email' => 'shagir@marinecaddie.com',
            'address' => 'Dubai Creek',
            'city' => 'Dubai',
            'country_id' => $singapore->id,
            'office_country_id' => $singapore->id,
            'status' => 'Active',
            'created_by' => $creator->id,
        ]));

        Customer::withoutEvents(fn () => Customer::create([
            'customer_name' => 'CAMPBELL SHIPPING',
            'email' => 'ops@marinecaddie-singapore.test',
        ]));

        $response = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MARINECADDIE SINGAPORE PTE LTD kisne add kiya tha']));

        $response->assertOk();
        $response->assertJsonPath('matched', true);
        $response->assertJsonPath('type', 'office');
        $response->assertJsonPath('item.name', 'MARINECADDIE SINGAPORE PTE LTD');
        $this->assertSame('Sunny Creator', $this->assistantFieldValue($response, 'Created by'));

        $emailResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MARINECADDIE SHIPPING LLC office ka email batao']));

        $emailResponse->assertOk();
        $emailResponse->assertJsonPath('matched', true);
        $emailResponse->assertJsonPath('type', 'office');
        $emailResponse->assertJsonPath('item.name', 'MARINECADDIE SHIPPING LLC');
        $this->assertSame('shagir@marinecaddie.com', $this->assistantFieldValue($emailResponse, 'Email'));

        $shortNameResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MARINECADDIE SHIPPING LLC office ka office short name batao']));

        $shortNameResponse->assertOk();
        $shortNameResponse->assertJsonPath('matched', true);
        $shortNameResponse->assertJsonPath('type', 'office');
        $shortNameResponse->assertJsonPath('item.name', 'MARINECADDIE SHIPPING LLC');
        $this->assertSame('MC DXB', $this->assistantFieldValue($shortNameResponse, 'Office short name'));

        $summaryResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'Show the complete summary for office MARINECADDIE SHIPPING LLC.']));

        $summaryResponse->assertOk();
        $summaryResponse->assertJsonPath('matched', true);
        $summaryResponse->assertJsonPath('type', 'office');
        $summaryResponse->assertJsonPath('item.name', 'MARINECADDIE SHIPPING LLC');
    }

    public function test_dashboard_assistant_lookup_prefers_explicit_hub_target_when_office_field_names_overlap(): void
    {
        $admin = $this->createAdminUser();
        $singapore = Country::create(['name' => 'Singapore']);

        Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'MARINECADDIE KOREA CO. LTD',
            'code' => 'ICN',
            'code_description' => 'SOUTH KOREA',
            'contact_person' => 'MARINECADDIE',
            'zip_code' => '07532',
            'country' => 'South Korea',
        ]));
        Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'code' => 'SIN',
            'zip_code' => '819454',
            'country' => 'Singapore',
        ]));
        Office::withoutEvents(fn () => Office::create([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'office_short_name' => 'MC SIN',
            'email' => 'opssin@marinecaddie.test',
            'address' => '119 Airport Cargo Road',
            'city' => 'Singapore',
            'country_id' => $singapore->id,
            'office_country_id' => $singapore->id,
            'status' => 'Active',
        ]));

        $codeDescriptionResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MARINECADDIE SINGAPORE PTE LTD hub ka code description batao']));

        $codeDescriptionResponse->assertOk();
        $codeDescriptionResponse->assertJsonPath('matched', true);
        $codeDescriptionResponse->assertJsonPath('type', 'hub');
        $codeDescriptionResponse->assertJsonPath('item.name', 'MARINECADDIE SINGAPORE PTE LTD');
        $this->assertSame('—', $this->assistantFieldValue($codeDescriptionResponse, 'Code description'));

        $officeAddressResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'MARINECADDIE SINGAPORE PTE LTD hub ka office address batao']));

        $officeAddressResponse->assertOk();
        $officeAddressResponse->assertJsonPath('matched', true);
        $officeAddressResponse->assertJsonPath('type', 'hub');
        $officeAddressResponse->assertJsonPath('item.name', 'MARINECADDIE SINGAPORE PTE LTD');
        $this->assertSame('—', $this->assistantFieldValue($officeAddressResponse, 'Office address'));

        $englishOfficeAddressResponse = $this->actingAsVerified($admin)
            ->getJson(route('dashboard.assistant-lookup', ['q' => 'What is the office address for hub MARINECADDIE SINGAPORE PTE LTD?']));

        $englishOfficeAddressResponse->assertOk();
        $englishOfficeAddressResponse->assertJsonPath('matched', true);
        $englishOfficeAddressResponse->assertJsonPath('type', 'hub');
        $englishOfficeAddressResponse->assertJsonPath('item.name', 'MARINECADDIE SINGAPORE PTE LTD');
        $this->assertSame('—', $this->assistantFieldValue($englishOfficeAddressResponse, 'Office address'));
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

    private function assistantFieldValue(TestResponse $response, string $label): ?string
    {
        $field = collect($response->json('item.fields'))->firstWhere('label', $label);

        return is_array($field) ? ($field['value'] ?? null) : null;
    }
}
