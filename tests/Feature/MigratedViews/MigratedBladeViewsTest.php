<?php

namespace Tests\Feature\MigratedViews;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Concerns\RendersMigratedBladeViews;
use Tests\RegressionTestCase;
use Tests\Support\MigratedViewStubs;

class MigratedBladeViewsTest extends RegressionTestCase
{
    use RendersMigratedBladeViews;

    public function test_agents_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Agents.index', [
            'agents' => $this->emptyPaginator(),
            'countries' => ['United Arab Emirates'],
            'agentTypes' => ['hub'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'filter-agent-name',
            'agents-table',
            'clear-agent-filters',
            'btn-agents-filters-toggle',
            'Hide inactive',
        ]);
    }

    public function test_suppliers_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Suppliers.index', [
            'suppliers' => $this->emptyPaginator(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'supplier-search-filter',
            'suppliers-table',
            'Add supplier',
            'list-inline-toolbar',
        ]);
    }

    public function test_vessels_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Vessels.index', [
            'vessels' => $this->emptyPaginator(),
            'vesselTypes' => ['Container'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'vesselNameFilter',
            'vessels-table',
            'clear-vessel-filters',
            'btn-vessels-filters-toggle',
        ]);
    }

    public function test_other_companies_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Other Companies.index', [
            'companies' => $this->emptyPaginator(),
            'countries' => ['Singapore'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'filter-company-name',
            'other-companies-table',
            'clear-company-filters',
            'btn-other-companies-filters-toggle',
        ]);
    }

    public function test_stocks_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Stock.stocks', [
            'crrs' => new LengthAwarePaginator([], 0, 50),
            'customers' => collect(),
            'vessels' => collect(),
            'accountManagers' => collect(),
            'offices' => collect(),
            'hubAgentOptions' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'btn-stocks-filters-toggle',
            'clear-stocks-filters',
            'col-Customer',
            'offices-table',
            'Create CRR',
        ]);
    }

    public function test_stock_follow_up_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Stock.stock-follow-up', [
            'crrs' => $this->emptyPaginator(50),
            'customers' => collect(['Customer A']),
            'accountManagers' => collect(['Manager A']),
        ]);

        $this->assertHtmlContainsAll($html, [
            'filter-account-manager',
            'filter-customer',
            'clear-stock-followup-filters',
            'stock-followup-pagination',
        ]);
    }

    public function test_pickup_work_list_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Stock.pickup-work-list', [
            'crrs' => $this->emptyPaginator(50),
            'handledByMap' => collect(),
            'accountManagers' => collect(),
            'vessels' => collect(),
            'handledByOptions' => collect(),
            'hubAgents' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'clear-pickup-filters',
            'pickup-filters-toolbar',
            'pickup-pagination',
        ]);
    }

    public function test_shipments_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Shipment.shipments', [
            'shipments' => new LengthAwarePaginator([], 0, 50),
            'partyNames' => [],
            'vesselCustomerMap' => [],
            'customers' => collect(),
            'vessels' => collect(),
            'services' => collect(['Airfreight']),
            'statuses' => collect(['Draft']),
            'departureOptions' => collect(),
            'accountManagers' => collect(),
            'creators' => collect(),
            'offices' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'Create shipment',
            'btn-shipments-filters-toggle',
            'clear-shipments-filters',
            'col-Customer',
            'col-Departure-hub',
            'shipments-filters-toolbar',
        ]);
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'filter-row'));
    }

    public function test_shipments_index_does_not_use_conflicting_base_styles_component(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/shipments.blade.php'));

        $this->assertStringNotContainsString('x-lists.base-styles', $contents);
        $this->assertStringContainsString('shipments-filters-toolbar', $contents);
        $this->assertStringContainsString('Create shipment', $contents);
    }

    public function test_shipment_follow_up_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Shipment.shipment-follow-up', [
            'shipments' => $this->emptyPaginator(50),
            'partyNames' => [],
            'customers' => collect(),
            'vessels' => collect(),
            'statuses' => collect(['In transit']),
            'accountManagers' => collect(),
            'creators' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'btn-followup-filters-toggle',
            'clear-followup-filters',
            'col-Customer',
            'filter-shipment-no',
        ]);
    }

    public function test_cost_follow_up_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Shipment.cost-follow-up', [
            'customers' => collect(),
            'vessels' => collect(),
            'statuses' => collect(),
            'accountManagers' => collect(),
            'creators' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'btn-cost-filters-toggle',
            'clear-cost-filters',
            'col-Customer',
        ]);
    }

    public function test_pre_alert_reminders_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Shipment.pre-alert-reminders', [
            'shipments' => $this->emptyPaginator(50),
            'partyNames' => [],
            'customers' => collect(),
            'vessels' => collect(),
            'statuses' => collect(['Draft']),
            'accountManagers' => collect(),
            'creators' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'btn-prealert-filters-toggle',
            'clear-prealert-filters',
        ]);
    }

    public function test_agents_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('Agents.create', [
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');
    }

    public function test_suppliers_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('Suppliers.create', [
            'countries' => MigratedViewStubs::countries(),
            'currencies' => ['USD', 'EUR'],
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');
    }

    public function test_other_companies_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('Other Companies.create', [
            'countries' => MigratedViewStubs::countries(),
            'currencies' => ['USD'],
            'companyTypes' => ['Agent'],
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');
    }

    public function test_customers_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('customers.create', [
            'countries' => MigratedViewStubs::countries(),
            'salesManagers' => collect(),
            'groups' => collect(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country');
        $this->assertCountrySelectPresent($html, 'postal_country');
        $this->assertCountrySelectPresent($html, 'invoice_country');
    }

    public function test_hub_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('hub.create', [
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country');
        $this->assertCountrySelectPresent($html, 'office_country');
    }

    public function test_migrated_edit_blade_sources_use_port_select_component(): void
    {
        $files = [
            'Agents/edit.blade.php',
            'Suppliers/edit.blade.php',
            'Other Companies/edit.blade.php',
            'customers/edit.blade.php',
            'hub/show.blade.php',
            'Shipment/partials/edit-shipment-details-form.blade.php',
        ];

        foreach ($files as $file) {
            $path = resource_path('views/' . $file);
            $this->assertFileExists($path, "Missing migrated view: {$file}");
            $contents = file_get_contents($path);
            $this->assertStringContainsString(
                'x-forms.port-select',
                $contents,
                "Expected x-forms.port-select in {$file}"
            );
            $this->assertStringNotContainsString(
                'formatPortResult',
                $contents,
                "Inline port Select2 JS should be removed from {$file}"
            );
        }
    }

    public function test_shipment_create_blade_renders_port_select_fields(): void
    {
        $html = $this->renderMigratedView('Shipment.create', [
            'countries' => MigratedViewStubs::countries(),
            'crrs' => collect(),
            'hubs' => collect(),
            'agents' => collect(),
            'preselectedCrrIds' => [],
            'departurePartiesByCrrId' => [],
            'preselectedDepartureParty' => null,
        ]);

        $this->assertPortSelectPresent($html, 'departure_port_code');
        $this->assertPortSelectPresent($html, 'consignee_port_code');
        $this->assertCountrySelectPresent($html, 'consignee_country');
    }

    public function test_migrated_edit_blade_sources_use_country_select_component(): void
    {
        $files = [
            'Agents/edit.blade.php',
            'Suppliers/edit.blade.php',
            'Other Companies/edit.blade.php',
            'customers/edit.blade.php',
            'hub/show.blade.php',
            'Shipment/partials/edit-shipment-details-form.blade.php',
            'offices/edit.blade.php',
        ];

        foreach ($files as $file) {
            $path = resource_path('views/' . $file);
            $this->assertFileExists($path, "Missing migrated view: {$file}");
            $contents = file_get_contents($path);
            $this->assertStringContainsString(
                'x-forms.country-select',
                $contents,
                "Expected x-forms.country-select in {$file}"
            );
            $this->assertStringNotContainsString(
                'formatCountry',
                $contents,
                "Inline country Select2 JS should be removed from {$file}"
            );
            $this->assertStringNotContainsString(
                'formatFlag',
                $contents,
                "Inline country flag Select2 JS should be removed from {$file}"
            );
        }
    }
}
