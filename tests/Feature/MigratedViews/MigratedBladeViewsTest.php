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
            'agents-list-page',
            'filter-agent-name',
            'agents-table',
            'clear-agent-filters',
            'btn-agents-filters-toggle',
            'Hide inactive',
            'list-page-header',
            'Manage agent companies',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_suppliers_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Suppliers.index', [
            'suppliers' => $this->emptyPaginator(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'suppliers-list-card',
            'list-page-header',
            'supplier-search-filter',
            'suppliers-table',
            'clear-supplier-filters',
            'btn-suppliers-filters-toggle',
            'suppliers-pagination',
            'pagination-sticky-footer',
            'Add supplier',
        ]);

        $contents = file_get_contents(resource_path('views/Suppliers/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('<x-lists.ajax-table', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_customers_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('customers.index', [
            'customers' => $this->emptyPaginator(),
            'responsibleOffices' => ['Singapore'],
            'accountManagers' => ['Jane Manager'],
            'salesManagers' => ['John Sales'],
            'countries' => ['Singapore'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'customers-list-card',
            'list-page-header',
            'filter-customer-search',
            'customers-table',
            'clear-customer-filters',
            'btn-customers-filters-toggle',
            'customers-pagination',
            'pagination-sticky-footer',
            'Add customer',
        ]);

        $contents = file_get_contents(resource_path('views/customers/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="offices-table"', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_vessels_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Vessels.index', [
            'vessels' => $this->emptyPaginator(),
            'vesselTypes' => ['Container'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'vessels-list-card',
            'list-page-header',
            'vesselNameFilter',
            'imoFilter',
            'typeFilter',
            'vessels-table',
            'clear-vessel-filters',
            'btn-vessels-filters-toggle',
            'vessels-pagination',
            'pagination-sticky-footer',
            'Vessels',
        ]);

        $contents = file_get_contents(resource_path('views/Vessels/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('<x-lists.ajax-table', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_administration_change_logs_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('administration.change-logs', [
            'users' => collect(),
            'entityTypes' => [
                \App\Models\Customer::class => 'Customer',
                \App\Models\CustomerVessel::class => 'Vessel',
            ],
        ]);

        $this->assertHtmlContainsAll($html, [
            'change-logs-list-page',
            'change-logs-list-card',
            'list-page-header',
            'Administration change logs',
            'filter-search',
            'filter-entity-type',
            'filter-user-id',
            'filter-date-range',
            'change-logs-table',
            'pagination-sticky-footer',
            'filter-reset',
        ]);

        $contents = file_get_contents(resource_path('views/administration/change-logs.blade.php'));
        $this->assertStringContainsString('x-lists.base-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_other_companies_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('Other Companies.index', [
            'companies' => $this->emptyPaginator(),
            'countries' => ['Singapore'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'other-companies-list-card',
            'list-page-header',
            'filter-company-name',
            'other-companies-table',
            'clear-company-filters',
            'btn-other-companies-filters-toggle',
            'other-companies-pagination',
            'pagination-sticky-footer',
        ]);

        $contents = file_get_contents(resource_path('views/Other Companies/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('<x-lists.ajax-table', $contents);
    }

    public function test_hubs_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('hub.index', [
            'hubs' => $this->emptyPaginator(),
            'countries' => ['Singapore'],
            'countryFlags' => ['Singapore' => 'https://example.com/sg.svg'],
        ]);

        $this->assertHtmlContainsAll($html, [
            'hubs-list-card',
            'list-page-header',
            'filter-hub-name',
            'hubs-table',
            'clear-hub-filters',
            'btn-hubs-filters-toggle',
            'Hide inactive',
            'hubs-pagination',
        ]);

        $contents = file_get_contents(resource_path('views/hub/index.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('#offices-table', $contents);
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
            'list-dense-filter-shell',
            'offices-table',
            'Create CRR',
            'data-list-page-header="1"',
            'Stock list',
            'stock-export-dropdown',
            'stock-export-option',
            'stock-export-menu',
            'bootstrap-growl.min.js',
            'animated rotateIn',
            'stock-dl-pct',
            '#008080',
            'icofont-file-pdf',
            'icofont-file-excel',
            'data-format="pdf"',
            'data-format="excel"',
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
            'list-dense-filter-shell',
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
            'list-dense-filter-shell',
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
            'list-dense-filter-shell',
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
            'list-dense-filter-shell',
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
            'list-dense-filter-shell',
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

        $this->assertHtmlContainsAll($html, [
            'create-agent-page',
            'create-agent-hero-icon',
            'agent-pillars',
            'create-agent-footer',
            'Create agent',
            'agentForm',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/create.blade.php'));
        $this->assertStringContainsString('create-page-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_agent_edit_blade_uses_edit_shell(): void
    {
        $agent = MigratedViewStubs::agent();
        $agent->city = 'Dubai';
        $agent->setRelation('country', (object) ['name' => 'United Arab Emirates']);
        $agent->setRelation('documents', collect());
        $agent->setRelation('agentUsers', collect());
        $agent->setRelation('contacts', collect());
        $agent->setRelation('billingExceptions', collect());

        $html = $this->renderMigratedView('Agents.edit', [
            'agent' => $agent,
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertHtmlContainsAll($html, [
            'edit-agent-page',
            'edit-agent-hero-icon',
            'agentEditForm',
            'agent-edit-footer',
            'agent-details',
            'billing-details',
            'Save agent',
            'Add agent user',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/edit.blade.php'));
        $this->assertStringContainsString('edit-page-styles', $contents);
        $this->assertStringContainsString('Agent users / contacts tabs live outside agentEditForm', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
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

        $this->assertHtmlContainsAll($html, [
            'create-supplier-page',
            'create-supplier-hero-icon',
            'create-supplier-footer',
            'supplierForm',
            'Save supplier',
        ]);

        $contents = file_get_contents(resource_path('views/Suppliers/create.blade.php'));
        $this->assertStringContainsString('create-page-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_suppliers_edit_blade_uses_edit_shell(): void
    {
        $supplier = MigratedViewStubs::supplier();
        $supplier->city = 'Rotterdam';
        $supplier->contact_person = 'John Supplier';
        $supplier->setRelation('country', (object) ['name' => 'Netherlands']);
        $supplier->setRelation('contacts', collect());

        $html = $this->renderMigratedView('Suppliers.edit', [
            'supplier' => $supplier,
            'countries' => MigratedViewStubs::countries(),
            'currencies' => ['USD'],
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');
        $this->assertHtmlContainsAll($html, [
            'edit-supplier-page',
            'edit-supplier-hero-icon',
            'edit-supplier-form',
            'supplier-edit-footer',
            'supplier-details',
            'contacts',
            'Save supplier',
            'Add contact',
        ]);

        $contents = file_get_contents(resource_path('views/Suppliers/edit.blade.php'));
        $this->assertStringContainsString('edit-page-styles', $contents);
        $this->assertStringContainsString('Contacts tab lives outside #edit-supplier-form', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
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

        $this->assertHtmlContainsAll($html, [
            'create-other-company-page',
            'create-other-company-hero-icon',
            'create-other-company-footer',
            'companyCreateForm',
            'Save company',
        ]);

        $contents = file_get_contents(resource_path('views/Other Companies/create.blade.php'));
        $this->assertStringContainsString('create-page-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_other_companies_edit_blade_uses_edit_shell(): void
    {
        $otherCompany = MigratedViewStubs::otherCompany();
        $otherCompany->city = 'Dubai';
        $otherCompany->contact_person = 'Jane Doe';
        $otherCompany->setRelation('country', (object) ['name' => 'United Arab Emirates']);
        $otherCompany->setRelation('contacts', collect());

        $html = $this->renderMigratedView('Other Companies.edit', [
            'otherCompany' => $otherCompany,
            'countries' => MigratedViewStubs::countries(),
            'currencies' => ['USD'],
            'companyTypes' => ['Agent'],
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');
        $this->assertHtmlContainsAll($html, [
            'edit-other-company-page',
            'edit-other-company-hero-icon',
            'edit-company-form',
            'other-company-edit-footer',
            'company-details',
            'contacts',
            'Save company',
            'Add contact',
            'delete-other-company-contact',
        ]);

        $contents = file_get_contents(resource_path('views/Other Companies/edit.blade.php'));
        $this->assertStringContainsString('edit-page-styles', $contents);
        $this->assertStringContainsString('Contacts tab lives outside #edit-company-form', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
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

        $this->assertHtmlContainsAll($html, [
            'create-customer-page',
            'create-customer-hero-icon',
            'cust-pillars',
            'create-customer-footer',
            'Save customer',
            'customerForm',
        ]);

        $contents = file_get_contents(resource_path('views/customers/create.blade.php'));
        $this->assertStringContainsString('create-page-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_customers_edit_blade_uses_edit_shell(): void
    {
        $customer = MigratedViewStubs::customer();
        $customer->customer_number = 'FM-001';
        $customer->setRelation('postalAddress', null);
        $customer->setRelation('invoiceAddress', null);
        $customer->setRelation('invoiceDetail', null);
        $customer->setRelation('responsible', (object) [
            'accountManager' => (object) ['name' => 'Jane Manager'],
            'sales_manager_id' => null,
            'account_manager_id' => null,
            'accounting_user_id' => null,
        ]);
        $customer->setRelation('sop', null);
        $customer->setRelation('notificationSetting', null);
        $customer->setRelation('documents', collect());
        $customer->setRelation('vessels', collect());
        $customer->setRelation('contacts', collect());
        $customer->setRelation('group', null);

        $html = $this->renderMigratedView('customers.edit', [
            'customer' => $customer,
            'countries' => MigratedViewStubs::countries(),
            'salesManagers' => collect(),
            'groups' => collect(),
            'id' => $customer->id,
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country');
        $this->assertCountrySelectPresent($html, 'postal_country');
        $this->assertCountrySelectPresent($html, 'invoice_country');
        $this->assertHtmlContainsAll($html, [
            'edit-customer-page',
            'edit-customer-hero-icon',
            'customerForm',
            'customer-edit-footer',
            'customer-details',
            'contacts',
            'sop',
            'vessels',
            'notification-settings',
            'Save customer',
            'Add contact',
            'Add vessel',
        ]);

        $contents = file_get_contents(resource_path('views/customers/edit.blade.php'));
        $this->assertStringContainsString('edit-page-styles', $contents);
        $this->assertStringContainsString('Contacts / Vessels tabs live outside #customerForm', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_hub_create_blade_renders_port_select(): void
    {
        $html = $this->renderMigratedView('hub.create', [
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country');
        $this->assertCountrySelectPresent($html, 'office_country');

        $this->assertHtmlContainsAll($html, [
            'create-hub-page',
            'create-hub-hero-icon',
            'hub-pillars',
            'create-hub-footer',
            'Create hub',
            'hubForm',
        ]);

        $contents = file_get_contents(resource_path('views/hub/create.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
    }

    public function test_hub_show_blade_uses_edit_shell(): void
    {
        $hub = MigratedViewStubs::hub();
        $hub->contact_person = 'Hub Contact';
        $hub->city = 'Singapore';
        $hub->country = 'Singapore';
        $hub->setRelation('documents', collect());
        $hub->setRelation('pricingDocuments', collect());
        $hub->setRelation('hubUsers', collect());
        $hub->setRelation('contacts', collect());

        $html = $this->renderMigratedView('hub.show', [
            'hub' => $hub,
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertPortSelectPresent($html, 'port_code');
        $this->assertCountrySelectPresent($html, 'country');
        $this->assertHtmlContainsAll($html, [
            'edit-hub-page',
            'edit-hub-hero-icon',
            'hubEditForm',
            'hub-edit-footer',
            'hub-details',
            'billing-details',
            'Save hub',
            'Add hub user',
            '/hubs/' . $hub->id . '/users/create',
            'Add contact',
            '/hubs/' . $hub->id . '/contacts/create',
        ]);

        $contents = file_get_contents(resource_path('views/hub/show.blade.php'));
        $this->assertStringContainsString('show-page-styles', $contents);
        $this->assertStringContainsString('Hub Users / Contacts tabs live outside hubEditForm', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_hub_user_create_blade_uses_form_shell(): void
    {
        $hub = MigratedViewStubs::hub();
        $hub->city = 'Singapore';
        $hub->country = 'Singapore';

        $html = $this->renderMigratedView('hub.users.create', [
            'hub' => $hub,
        ]);

        $this->assertHtmlContainsAll($html, [
            'hub-user-page',
            'hub-user-hero-icon',
            'hub-user-pillar',
            'hubUserForm',
            'hub-user-footer',
            'Add hub user',
            'hub-users',
            'show_in_scan_gun',
        ]);

        $contents = file_get_contents(resource_path('views/hub/users/create.blade.php'));
        $this->assertStringContainsString('hub-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_agent_user_create_blade_uses_form_shell(): void
    {
        $agent = MigratedViewStubs::agent();
        $agent->city = 'Amsterdam';
        $agent->setRelation('country', (object) ['name' => 'Netherlands']);

        $html = $this->renderMigratedView('Agents.Users.create', [
            'agent' => $agent,
        ]);

        $this->assertHtmlContainsAll($html, [
            'agent-user-page',
            'agent-user-hero-icon',
            'agent-user-pillar',
            'agentUserForm',
            'agent-user-footer',
            'Add agent user',
            'agent-users',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/Users/create.blade.php'));
        $this->assertStringContainsString('agent-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_agent_user_edit_blade_uses_form_shell(): void
    {
        $agent = MigratedViewStubs::agent();
        $agent->city = 'Amsterdam';
        $agent->setRelation('country', (object) ['name' => 'Netherlands']);

        $user = new \App\Models\AgentUser([
            'name' => 'Jane Agent',
            'email' => 'jane@example.com',
            'phone_number' => '+31 20 1234567',
            'description' => 'Operations',
        ]);
        $user->id = 5;
        $user->agent_id = $agent->id;

        $html = $this->renderMigratedView('Agents.Users.edit', [
            'agent' => $agent,
            'user' => $user,
        ]);

        $this->assertHtmlContainsAll($html, [
            'agent-user-page',
            'agent-user-hero-icon',
            'agentUserForm',
            'agent-user-footer',
            'Edit agent user',
            'Jane Agent',
            'agent-users',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/Users/edit.blade.php'));
        $this->assertStringContainsString('agent-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_agent_contact_create_blade_uses_form_shell(): void
    {
        $agent = MigratedViewStubs::agent();
        $agent->city = 'Amsterdam';
        $agent->setRelation('country', (object) ['name' => 'Netherlands']);

        $html = $this->renderMigratedView('Agents.contacts.create', [
            'agent' => $agent,
        ]);

        $this->assertHtmlContainsAll($html, [
            'agent-contact-page',
            'agent-contact-hero-icon',
            'agent-contact-pillar',
            'agentContactForm',
            'agent-contact-footer',
            'Add contact',
            '#contacts',
            'is_main_contact',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/contacts/create.blade.php'));
        $this->assertStringContainsString('agent-contact-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_agent_contact_edit_blade_uses_form_shell(): void
    {
        $agent = MigratedViewStubs::agent();
        $agent->city = 'Amsterdam';
        $agent->setRelation('country', (object) ['name' => 'Netherlands']);

        $contact = new \App\Models\Contact([
            'name' => 'John Contact',
            'email' => 'john@example.com',
            'phone_number' => '+31 20 7654321',
            'description' => 'Sales',
            'is_main_contact' => true,
        ]);
        $contact->id = 8;
        $contact->agent_id = $agent->id;

        $html = $this->renderMigratedView('Agents.contacts.edit', [
            'agent' => $agent,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'agent-contact-page',
            'agentContactForm',
            'agent-contact-footer',
            'Edit contact',
            'John Contact',
            '#contacts',
        ]);

        $contents = file_get_contents(resource_path('views/Agents/contacts/edit.blade.php'));
        $this->assertStringContainsString('agent-contact-form-styles', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_hub_contact_create_blade_uses_form_shell(): void
    {
        $hub = MigratedViewStubs::hub();
        $hub->city = 'Singapore';
        $hub->country = 'Singapore';

        $html = $this->renderMigratedView('hub.contacts.create', [
            'hub' => $hub,
        ]);

        $this->assertHtmlContainsAll($html, [
            'hub-contact-page',
            'hub-contact-hero-icon',
            'hub-contact-pillar',
            'hubContactForm',
            'hub-contact-footer',
            'Add contact',
            '#contacts',
            'is_main_contact',
        ]);

        $contents = file_get_contents(resource_path('views/hub/contacts/create.blade.php'));
        $this->assertStringContainsString('hub-contact-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_other_company_contact_create_blade_uses_form_shell(): void
    {
        $otherCompany = MigratedViewStubs::otherCompany();
        $otherCompany->city = 'Mundra';
        $otherCompany->setRelation('country', (object) ['name' => 'India']);

        $html = $this->renderMigratedView('Other Companies.contacts.create', [
            'otherCompany' => $otherCompany,
        ]);

        $this->assertHtmlContainsAll($html, [
            'oc-contact-page',
            'oc-contact-hero-icon',
            'oc-contact-pillar',
            'ocContactForm',
            'oc-contact-footer',
            'Add contact',
            '#contacts',
            'is_main_contact',
        ]);

        $contents = file_get_contents(resource_path('views/Other Companies/contacts/create.blade.php'));
        $this->assertStringContainsString('oc-contact-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_other_company_contact_edit_blade_uses_form_shell(): void
    {
        $otherCompany = MigratedViewStubs::otherCompany();
        $otherCompany->city = 'Mundra';
        $otherCompany->setRelation('country', (object) ['name' => 'India']);

        $contact = new \App\Models\Contact([
            'name' => 'Jane Contact',
            'email' => 'jane@example.com',
            'phone_number' => '+91 9876543210',
            'description' => 'Operations',
            'is_main_contact' => true,
        ]);
        $contact->id = 12;
        $contact->other_company_id = $otherCompany->id;

        $html = $this->renderMigratedView('Other Companies.contacts.edit', [
            'otherCompany' => $otherCompany,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'oc-contact-page',
            'ocContactForm',
            'oc-contact-footer',
            'Edit contact',
            'Jane Contact',
            '#contacts',
        ]);

        $contents = file_get_contents(resource_path('views/Other Companies/contacts/edit.blade.php'));
        $this->assertStringContainsString('oc-contact-form-styles', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_supplier_contact_create_blade_uses_form_shell(): void
    {
        $supplier = MigratedViewStubs::supplier();
        $supplier->city = 'Rotterdam';
        $supplier->un_locode = 'NLRTM';
        $supplier->setRelation('country', (object) ['name' => 'Netherlands']);

        $html = $this->renderMigratedView('Suppliers.contacts.create', [
            'supplier' => $supplier,
        ]);

        $this->assertHtmlContainsAll($html, [
            'supplier-contact-page',
            'supplier-contact-hero-icon',
            'supplier-contact-pillar',
            'supplierContactForm',
            'supplier-contact-footer',
            'Add contact',
            '#contacts',
            'is_main_contact',
        ]);

        $contents = file_get_contents(resource_path('views/Suppliers/contacts/create.blade.php'));
        $this->assertStringContainsString('supplier-contact-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_supplier_contact_edit_blade_uses_form_shell(): void
    {
        $supplier = MigratedViewStubs::supplier();
        $supplier->city = 'Rotterdam';
        $supplier->un_locode = 'NLRTM';
        $supplier->setRelation('country', (object) ['name' => 'Netherlands']);

        $contact = new \App\Models\Contact([
            'name' => 'Supplier Contact',
            'email' => 'contact@supplier.com',
            'phone_number' => '+31 10 1234567',
            'description' => 'Purchasing',
            'is_main_contact' => true,
        ]);
        $contact->id = 15;
        $contact->supplier_id = $supplier->id;

        $html = $this->renderMigratedView('Suppliers.contacts.edit', [
            'supplier' => $supplier,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'supplier-contact-page',
            'supplierContactForm',
            'supplier-contact-footer',
            'Edit contact',
            'Supplier Contact',
            '#contacts',
        ]);

        $contents = file_get_contents(resource_path('views/Suppliers/contacts/edit.blade.php'));
        $this->assertStringContainsString('supplier-contact-form-styles', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_customer_contact_create_blade_uses_form_shell(): void
    {
        $customer = MigratedViewStubs::customer();
        $customer->customer_number = 'FM-004';
        $customer->primaryAddress->city = 'Dubai';

        $html = $this->renderMigratedView('contacts.create', [
            'customer' => $customer,
        ]);

        $this->assertHtmlContainsAll($html, [
            'customer-contact-page',
            'customer-contact-hero-icon',
            'customer-contact-pillar',
            'customerContactForm',
            'customer-contact-footer',
            'Add contact',
            '#contacts',
            'is_main_contact',
            $customer->customer_name,
        ]);

        $contents = file_get_contents(resource_path('views/contacts/create.blade.php'));
        $this->assertStringContainsString('customer-contact-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_customer_contact_edit_blade_uses_form_shell(): void
    {
        $customer = MigratedViewStubs::customer();
        $customer->customer_number = 'FM-004';
        $customer->primaryAddress->city = 'Dubai';

        $contact = new \App\Models\Contact([
            'name' => 'Customer Contact',
            'email' => 'contact@customer.com',
            'phone_number' => '+971 4 1234567',
            'description' => 'Operations',
            'is_main_contact' => true,
        ]);
        $contact->id = 20;
        $contact->customer_id = $customer->id;

        $html = $this->renderMigratedView('contacts.edit', [
            'customer' => $customer,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'customer-contact-page',
            'customerContactForm',
            'customer-contact-footer',
            'Edit contact',
            'Customer Contact',
            '#contacts',
        ]);

        $contents = file_get_contents(resource_path('views/contacts/edit.blade.php'));
        $this->assertStringContainsString('customer-contact-form-styles', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
    }

    public function test_customer_vessel_edit_blade_uses_edit_shell(): void
    {
        $customer = MigratedViewStubs::customer();
        $customer->customer_number = 'FM-004';

        $vessel = new \App\Models\CustomerVessel([
            'vessel' => 'MV Stub Vessel',
            'vessel_name_alias' => 'Stub Alias',
            'vessel_imo' => '1234567',
            'customer_vessel_code' => 'V-001',
            'vessel_type_alias' => 'MV',
            'account_manager' => 'Jane Manager',
            'inactive_vessel' => false,
            'sanction_blocked' => false,
            'financially_blocked' => false,
        ]);
        $vessel->id = 13;
        $vessel->customer_id = $customer->id;
        $vessel->setRelation('customer', $customer);

        $html = $this->renderMigratedView('customers.customer-vessels', [
            'vessel' => $vessel,
            'customerContacts' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'edit-vessel-page',
            'edit-vessel-hero-icon',
            'edit-vessel-meta',
            'vesselForm',
            'vessel-edit-footer',
            'Save vessel',
            'MV Stub Vessel',
            'Stub Customer',
            '#vessels',
            'vessel-details',
            'account-manager-select',
        ]);

        $contents = file_get_contents(resource_path('views/customers/customer-vessels.blade.php'));
        $this->assertStringContainsString('vessel-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
        $this->assertStringNotContainsString('id="offices-table"', $contents);
    }

    public function test_customer_vessel_create_blade_uses_form_shell(): void
    {
        $customer = MigratedViewStubs::customer();
        $customer->customer_number = 'FM-004';
        $customer->primaryAddress->city = 'Dubai';

        $html = $this->renderMigratedView('customers.customer-vessels-add', [
            'customer' => $customer,
            'customerContacts' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'create-vessel-page',
            'edit-vessel-page',
            'edit-vessel-hero-icon',
            'Add vessel',
            'vesselForm',
            'vessel-edit-footer',
            'Save vessel',
            'Stub Customer',
            '#vessels',
            'vessel-details',
            'account-manager-select',
        ]);

        $contents = file_get_contents(resource_path('views/customers/customer-vessels-add.blade.php'));
        $this->assertStringContainsString('vessel-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('id="pcoded"', $contents);
        $this->assertStringNotContainsString('id="offices-table"', $contents);
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
        $this->assertStringContainsString('name="service"', $contents);
        $this->assertMatchesRegularExpression('/name="service"[^>]*\brequired\b/', $contents);
        $this->assertStringContainsString('name="consignee_port_code"', $contents);
        $this->assertStringContainsString(':required="true"', $contents);
    }

    public function test_shipment_edit_does_not_prompt_pending_transit_on_open(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/edit.blade.php'));

        $this->assertStringNotContainsString('promptPendingTransitOnEditOpen();', $contents);
        $this->assertStringNotContainsString('function promptPendingTransitOnEditOpen()', $contents);
        $this->assertStringContainsString('function promptTransitAfterPreAlertComplete()', $contents);
        $this->assertStringContainsString('promptTransitAfterPreAlertComplete();', $contents);
        $this->assertStringContainsString('createPreAlertMode', $contents);
        $this->assertStringContainsString('Transit required', $contents);
        $this->assertStringContainsString('function syncServiceDetailsTabLock()', $contents);
        $this->assertStringContainsString('function showServiceDetailsUnavailableAlert()', $contents);
        $this->assertStringContainsString('Service details not available. Please create pre alert.', $contents);
    }

    public function test_shipment_edit_service_details_locked_when_in_process(): void
    {
        $form = file_get_contents(resource_path('views/Shipment/partials/edit-shipment-details-form.blade.php'));

        $this->assertStringContainsString('lockServiceDetailsForInProcess', $form);
        $this->assertStringContainsString('workflowEditMode', $form);
        $this->assertStringContainsString('transitMode', $form);
        $this->assertStringContainsString('stock-tab--disabled', $form);
        $this->assertStringContainsString('stock-panel--locked', $form);
    }

    public function test_shipment_edit_status_picker_excludes_system_statuses(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/edit.blade.php'));

        $this->assertStringContainsString("\$manualShipmentStatuses = ['In transit', 'Delivered', 'Completed', 'Cancelled']", $contents);
        $this->assertStringNotContainsString(
            "['In process', 'In transit', 'Delivered', 'Completed', 'Cancelled']",
            $contents
        );
    }

    public function test_shipment_edit_uses_complete_pre_alert_action_instead_of_finalize(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/edit.blade.php'));

        $this->assertStringContainsString('id="complete-prealert-btn"', $contents);
        $this->assertStringContainsString('Complete Pre alert', $contents);
        $this->assertStringContainsString('$canCompletePreAlert', $contents);
        $this->assertMatchesRegularExpression('/@disabled\(\$workflowAwaitingShipment \|\| ! \$canCompletePreAlert\)/', $contents);
        $this->assertStringContainsString('Pre-alert already completed', $contents);
        $this->assertStringContainsString('Generate a pre-alert PDF before completing', $contents);
        $this->assertStringContainsString('title: \'Complete Pre alert?\'', $contents);
        $this->assertStringContainsString('function submitCompletePreAlert', $contents);
        $this->assertStringContainsString('complete-pre-alert', $contents);
        $this->assertStringContainsString('In transit and selected stocks will be marked Completed', $contents);
    }

    public function test_shipment_transit_page_uses_finalize_shipment_header_action(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/edit.blade.php'));

        $this->assertStringContainsString('@if ($transitMode)', $contents);
        $this->assertStringContainsString('id="finalize-shipment-btn"', $contents);
        $this->assertMatchesRegularExpression('/@if\s*\(\s*\$transitMode\s*\).*?>Transit<\/button>/s', $contents);
        $this->assertStringContainsString('$(document).on(\'click\', \'#finalize-shipment-btn\'', $contents);
        $this->assertStringContainsString('function openFinalizeTransitModal()', $contents);
        $this->assertStringContainsString('finalize-shipment-transit-modal', $contents);
    }

    public function test_shipment_transit_page_reuses_workflow_edit_mode(): void
    {
        $contents = file_get_contents(resource_path('views/Shipment/edit.blade.php'));

        $this->assertStringContainsString('$transitMode', $contents);
        $this->assertStringContainsString('$workflowEditMode', $contents);
        $this->assertStringContainsString('$workflowAwaitingShipment', $contents);
        $this->assertStringContainsString('id="workflow-page-body"', $contents);
        $this->assertStringContainsString('workflow-page-body--hidden', $contents);
        $this->assertStringContainsString('header-inline-edit--locked', $contents);
        $this->assertStringContainsString('route(\'transit\')', $contents);
        $this->assertStringContainsString("'transit'", $contents);
        $this->assertStringContainsString('bindWorkflowShipmentSearch', $contents);
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

    public function test_column_picker_supports_clear_all(): void
    {
        $shim = file_get_contents(resource_path('views/partials/multiselect-select2-shim.blade.php'));
        $styles = file_get_contents(resource_path('views/partials/common-assets-styles.blade.php'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('mc-column-picker__clear-all', $shim);
        $this->assertStringContainsString('clearAllColumns', $shim);
        $this->assertStringContainsString('mc-column-picker__tools', $styles);
        $this->assertStringContainsString('saveMcColumnPickerSelection', $shim);
        $this->assertStringContainsString('loadMcColumnPickerSelection', $shim);
        $this->assertStringContainsString('data-mc-user-id', $layout);
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

    public function test_offices_index_blade_renders_list_shell(): void
    {
        $html = $this->renderMigratedView('offices.index', [
            'offices' => collect(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'offices-list-card',
            'data-list-page-header="1"',
            'Offices',
            'offices-search-input',
            'offices-list-footer',
            'pagination-sticky-footer',
            'btn-offices-add',
            'offices-table',
        ]);
    }

    public function test_offices_create_blade_uses_pillar_shell(): void
    {
        $html = $this->renderMigratedView('offices.create', [
            'companies' => collect(),
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'create-office-page',
            'create-office-hero-icon',
            'office-pillars',
            'office-pillar__title',
            'create-office-footer',
            'btn-add-account',
        ]);
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');

        $contents = file_get_contents(resource_path('views/offices/create.blade.php'));
        $this->assertStringContainsString('create-office-page', $contents);
        $this->assertStringContainsString('office-pillar', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
    }

    public function test_offices_edit_blade_uses_pillar_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'office_short_name' => 'MC SIN',
            'status' => 1,
        ]);
        $office->id = 3;
        $office->setRelation('bankAccounts', collect());
        $office->setRelation('contacts', collect());

        $html = $this->renderMigratedView('offices.edit', [
            'office' => $office,
            'companies' => collect(),
            'countries' => MigratedViewStubs::countries(),
        ]);

        $this->assertHtmlContainsAll($html, [
            'edit-office-page',
            'edit-office-hero-icon',
            'office-pillars',
            'office-pillar__title',
            'edit-office-tab',
            'office-details',
            'officeEditForm',
        ]);
        $this->assertCountrySelectPresent($html, 'country_id');
        $this->assertCountrySelectPresent($html, 'office_country_id');

        $contents = file_get_contents(resource_path('views/offices/edit.blade.php'));
        $this->assertStringContainsString('edit-page-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
    }

    public function test_operations_user_create_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $html = $this->renderMigratedView('offices.operations_users.create', [
            'office' => $office,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-pillar',
            'operationUserForm',
            'office-user-footer',
            'Add operation user',
        ]);

        $contents = file_get_contents(resource_path('views/offices/operations_users/create.blade.php'));
        $this->assertStringContainsString('office-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_operations_user_edit_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $contact = new \App\Models\Contact([
            'name' => 'Mirza Mohammed',
            'email' => 'mirza@marinecaddie.com',
            'phone_number' => '+65 9113 5978',
            'category' => 'operations',
        ]);
        $contact->id = 12;

        $html = $this->renderMigratedView('offices.operations_users.edit', [
            'office' => $office,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-tab',
            'user-details',
            'operationUserEditForm',
            'office-user-footer',
            'Mirza Mohammed',
        ]);

        $contents = file_get_contents(resource_path('views/offices/operations_users/edit.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_account_user_create_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $html = $this->renderMigratedView('offices.account_users.create', [
            'office' => $office,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-pillar',
            'accountUserForm',
            'office-user-footer',
            'Add account user',
            'accounting-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/account_users/create.blade.php'));
        $this->assertStringContainsString('office-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_account_user_edit_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $contact = new \App\Models\Contact([
            'name' => 'test',
            'email' => 'test@sdf.com',
            'phone_number' => '1234567890',
            'category' => 'account',
        ]);
        $contact->id = 33;

        $html = $this->renderMigratedView('offices.account_users.edit', [
            'office' => $office,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-tab',
            'user-details',
            'accountUserEditForm',
            'office-user-footer',
            'Accounting user',
            'accounting-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/account_users/edit.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_sales_user_create_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $html = $this->renderMigratedView('offices.sales_users.create', [
            'office' => $office,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-pillar',
            'salesUserForm',
            'office-user-footer',
            'Add sales user',
            'sales-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/sales_users/create.blade.php'));
        $this->assertStringContainsString('office-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_manager_user_create_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $html = $this->renderMigratedView('offices.manager_users.create', [
            'office' => $office,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-pillar',
            'managerUserForm',
            'office-user-footer',
            'Add manager user',
            'manager-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/manager_users/create.blade.php'));
        $this->assertStringContainsString('office-user-form-styles', $contents);
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_manager_user_edit_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $contact = new \App\Models\Contact([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'category' => 'manager',
        ]);
        $contact->id = 35;

        $html = $this->renderMigratedView('offices.manager_users.edit', [
            'office' => $office,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-tab',
            'user-details',
            'managerUserEditForm',
            'office-user-footer',
            'Manager user',
            'manager-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/manager_users/edit.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }

    public function test_sales_user_edit_blade_uses_form_shell(): void
    {
        $office = new \App\Models\Office([
            'office_name' => 'MARINECADDIE SINGAPORE PTE LTD',
            'status' => 1,
        ]);
        $office->id = 3;

        $contact = new \App\Models\Contact([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'category' => 'sales',
        ]);
        $contact->id = 34;

        $html = $this->renderMigratedView('offices.sales_users.edit', [
            'office' => $office,
            'contact' => $contact,
        ]);

        $this->assertHtmlContainsAll($html, [
            'office-user-page',
            'office-user-hero-icon',
            'office-user-tab',
            'user-details',
            'salesUserEditForm',
            'office-user-footer',
            'Sales user',
            'sales-users',
        ]);

        $contents = file_get_contents(resource_path('views/offices/sales_users/edit.blade.php'));
        $this->assertStringNotContainsString('theme-loader', $contents);
        $this->assertStringNotContainsString('offices-table', $contents);
    }
}
