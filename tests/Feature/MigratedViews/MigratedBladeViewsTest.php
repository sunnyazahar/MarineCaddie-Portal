<?php

namespace Tests\Feature\MigratedViews;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Concerns\RendersMigratedBladeViews;
use Tests\RegressionTestCase;
use Tests\Support\MigratedViewStubs;

class MigratedBladeViewsTest extends RegressionTestCase
{
    use RendersMigratedBladeViews;

    public function test_app_layout_uses_vite_tailwind_v2(): void
    {
        $contents = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $this->assertStringContainsString("@vite(['resources/css/app.css', 'resources/js/app.js'])", $contents);
        $this->assertStringNotContainsString('bootstrap.min.css', $contents);
        $this->assertStringNotContainsString('assets/css/style.css', $contents);
    }

    public function test_guest_layout_for_auth(): void
    {
        $this->assertFileExists(resource_path('views/layouts/guest.blade.php'));
        $this->assertStringContainsString('layouts.guest', file_get_contents(resource_path('views/auth/login.blade.php')));
    }

    public function test_filters_use_select2(): void
    {
        $script = file_get_contents(resource_path('views/partials/searchable-filter-multiselect-script.blade.php'));
        $this->assertStringContainsString('select2(', $script);
        $this->assertStringNotContainsString('.multiselect(', $script);
    }

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
            'data-list-page-header="1"',
            'Stock list',
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
            'btn-stock-followup-filters-toggle',
            'list-filters-toolbar',
            'filter-account-manager',
            'filter-customer',
            'clear-stock-followup-filters',
            'stock-followup-pagination',
            'pagination-sticky-footer',
            'list-pagination-meta',
            'data-list-page-header="1"',
            'Stock follow-up',
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
            'pagination-sticky-footer',
            'list-pagination-meta',
            'data-list-page-header="1"',
            'Pick up work list',
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
            'data-list-page-header="1"',
            'shipments-pagination',
            'pagination-sticky-footer',
            'list-pagination-meta',
        ]);
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'filter-row'));
    }

    public function test_shipments_index_does_not_use_conflicting_base_styles_component(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/shipments.blade.php'));

        $this->assertStringNotContainsString('x-lists.base-styles', $contents);
        $this->assertStringContainsString('shipments-filters-toolbar', $contents);
        $this->assertStringContainsString('Create shipment', $contents);
        $this->assertStringContainsString('x-lists.page-header', $contents);
        $this->assertStringContainsString('list-pagination-footer-styles', $contents);
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
            'data-list-page-header="1"',
            'Shipment follow-up',
            'pagination-sticky-footer',
            'list-pagination-meta',
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
            'data-list-page-header="1"',
            'Cost follow-up',
            'pagination-sticky-footer',
            'list-pagination-meta',
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
            'data-list-page-header="1"',
            'Pre-alert reminders',
            'pagination-sticky-footer',
            'list-pagination-meta',
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
            'Stock/Create-CRR.blade.php',
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

    public function test_create_crr_blade_uses_pillar_shell_and_motion_hooks(): void
    {
        $contents = file_get_contents(resource_path('views/Stock/Create-CRR.blade.php'));

        $this->assertStringContainsString('create-crr-page', $contents);
        $this->assertStringContainsString('crr-pillar', $contents);
        $this->assertStringContainsString('crr-section-shell', $contents);
        $this->assertStringContainsString('create-crr-footer', $contents);
        $this->assertStringContainsString('create-crr-hero-icon', $contents);
    }

    public function test_shipment_edit_details_form_uses_pillar_shell(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/partials/edit-shipment-details-form.blade.php'));

        $this->assertStringContainsString('cs-pillars', $contents);
        $this->assertStringContainsString('cs-pillar__title">Departure', $contents);
        $this->assertStringContainsString('cs-pillar__title">Consignee', $contents);
        $this->assertStringContainsString('Account &amp; comments', $contents);
        $this->assertStringContainsString('cs-section-shell', $contents);
    }

    public function test_shipment_irregularity_fields_use_indexed_array_names(): void
    {
        $files = [
            'Shipment/partials/edit-shipment-details-form.blade.php',
            'Shipment/edit.blade.php',
            'Shipment/create.blade.php',
        ];

        foreach ($files as $file) {
            $contents = file_get_contents(resource_path('views/' . $file));
            $this->assertStringNotContainsString(
                'irregularities[][',
                $contents,
                "PHP treats irregularities[][field] as one row per field in {$file}"
            );
            $this->assertMatchesRegularExpression(
                '/irregularities\[(\{\{\s*\$irIndex\s*\}\}|0|\$\{idx\})\]\[/',
                $contents,
                "Expected indexed irregularities[n][field] names in {$file}"
            );
        }

        // Document why indexed names matter: [][a]&[][b] becomes two rows.
        parse_str('irregularities[][a]=1&irregularities[][b]=2', $broken);
        parse_str('irregularities[0][a]=1&irregularities[0][b]=2', $fixed);
        $this->assertCount(2, $broken['irregularities']);
        $this->assertCount(1, $fixed['irregularities']);
        $this->assertSame(['a' => '1', 'b' => '2'], $fixed['irregularities'][0]);
    }

    public function test_mc_compat_handles_alert_dismiss(): void
    {
        $contents = file_get_contents(resource_path('js/mc-compat.js'));

        $this->assertStringContainsString('data-dismiss="alert"', $contents);
        $this->assertStringContainsString('data-bs-dismiss="alert"', $contents);
        $this->assertStringContainsString('closest(\'.alert\')', $contents);
    }

    public function test_users_blade_uses_list_header_and_pagination_footer(): void
    {
        $contents = file_get_contents(resource_path('views/Users/users.blade.php'));

        $this->assertStringContainsString('x-lists.page-header', $contents);
        $this->assertStringContainsString('list-pagination-footer-styles', $contents);
        $this->assertStringContainsString('pagination-sticky-footer', $contents);
        $this->assertStringContainsString('list-pagination-footer-inner', $contents);
        $this->assertStringContainsString('addUserModal', $contents);
        $this->assertStringContainsString('btn-add-user', $contents);
    }
}
