<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BreadcrumbBuilder
{
    /**
     * Build breadcrumb items for the current request.
     *
     * @return array<int, array{label: string, url: ?string}>
     */
    public static function items(): array
    {
        $routeName = request()->route()?->getName()
            ?? Route::currentRouteName();
        $path = trim(request()->path(), '/');

        if ($routeName === 'dashboard' || $routeName === 'dashboard.home' || $path === '' || $path === 'dashboard') {
            return [
                ['label' => 'Dashboard', 'url' => null],
            ];
        }

        $items = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
        ];

        $mapped = self::mapFromRouteName((string) ($routeName ?? ''));

        if ($mapped === []) {
            $mapped = self::mapFromPath($path);
        }

        foreach ($mapped as $item) {
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    protected static function mapFromRouteName(string $routeName): array
    {
        if ($routeName === '') {
            return [];
        }

        $definitions = self::definitions();

        if (isset($definitions[$routeName])) {
            return $definitions[$routeName];
        }

        foreach ($definitions as $pattern => $crumbs) {
            if (Str::is($pattern, $routeName)) {
                return $crumbs;
            }
        }

        return self::fallbackFromRouteName($routeName);
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    protected static function mapFromPath(string $path): array
    {
        if ($path === '') {
            return [['label' => 'Dashboard', 'url' => null]];
        }

        $segments = explode('/', $path);
        $crumbs = [];
        $accumulated = '';

        foreach ($segments as $index => $segment) {
            if ($segment === '' || is_numeric($segment)) {
                continue;
            }

            $accumulated .= ($accumulated === '' ? '' : '/') . $segment;
            $label = self::humanize($segment);
            $isLast = $index === array_key_last($segments)
                || ($index === count($segments) - 2 && is_numeric(end($segments)));

            $crumbs[] = [
                'label' => $label,
                'url' => $isLast ? null : url('/' . $accumulated),
            ];
        }

        if ($crumbs === []) {
            return [['label' => self::humanize($path), 'url' => null]];
        }

        $crumbs[count($crumbs) - 1]['url'] = null;

        return $crumbs;
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    protected static function fallbackFromRouteName(string $routeName): array
    {
        $parts = explode('.', $routeName);
        $crumbs = [];

        $section = $parts[0] ?? '';
        $sectionLabel = self::sectionLabel($section);

        if ($sectionLabel) {
            $crumbs[] = [
                'label' => $sectionLabel,
                'url' => self::sectionUrl($section),
            ];
        }

        $action = end($parts) ?: $routeName;
        $actionLabel = self::actionLabel($action, $section);

        if ($actionLabel && $actionLabel !== $sectionLabel) {
            $crumbs[] = ['label' => $actionLabel, 'url' => null];
        } elseif ($crumbs === []) {
            $crumbs[] = ['label' => self::humanize($routeName), 'url' => null];
        } else {
            $crumbs[count($crumbs) - 1]['url'] = null;
        }

        return $crumbs;
    }

    /**
     * @return array<string, array<int, array{label: string, url: ?string}>>
     */
    protected static function definitions(): array
    {
        return [
            // Stocks
            'stocks' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Stock list', 'url' => null],
            ],
            'stocks.edit' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Stock list', 'url' => route('stocks')],
                ['label' => 'Edit stock', 'url' => null],
            ],
            'stocks.*' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Stock details', 'url' => null],
            ],
            'stock-follow-up' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Stock follow-up', 'url' => null],
            ],
            'pickup-work-list' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Pick up work list', 'url' => null],
            ],
            'create-crr' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'Create CRR', 'url' => null],
            ],
            'etl-stock-items' => [
                ['label' => 'Stocks', 'url' => route('stocks')],
                ['label' => 'ETL stock items', 'url' => null],
            ],

            // Shipments
            'shipments' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'All shipments', 'url' => null],
            ],
            'shipments.edit' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'All shipments', 'url' => route('shipments')],
                ['label' => 'Edit shipment', 'url' => null],
            ],
            'shipments.*' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Shipment details', 'url' => null],
            ],
            'create-shipment' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Create shipment', 'url' => null],
            ],
            'create-pre-alert' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Create pre alert', 'url' => null],
            ],
            'transit' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Transit', 'url' => null],
            ],
            'pre-alert-reminders' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Shipment follow up', 'url' => null],
            ],
            'shipment-follow-up' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Delivery follow-up', 'url' => null],
            ],
            'cost-follow-up' => [
                ['label' => 'Shipments', 'url' => route('shipments')],
                ['label' => 'Cost follow-up', 'url' => null],
            ],

            // Billing
            'billable-shipments' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Billable shipments', 'url' => null],
            ],
            'all-invoices' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'All invoices', 'url' => null],
            ],
            'all-incoming-invoices' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'All incoming invoices', 'url' => null],
            ],
            'all-costs' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'All costs', 'url' => null],
            ],
            'accounting' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Accounting', 'url' => null],
            ],
            'billing.invoicing' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Invoicing', 'url' => null],
            ],
            'billing.invoicing.edit' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Invoicing', 'url' => route('billing.invoicing')],
                ['label' => 'Edit proforma invoice', 'url' => null],
            ],
            'billing.debit-notes' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Debit note', 'url' => null],
            ],
            'billing.credit-notes' => [
                ['label' => 'Billing', 'url' => null],
                ['label' => 'Credit note', 'url' => null],
            ],

            // Administration - Offices
            'administration.change-logs' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Change logs', 'url' => null],
            ],
            'offices.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Offices', 'url' => null],
            ],
            'offices.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Offices', 'url' => route('offices.index')],
                ['label' => 'Create office', 'url' => null],
            ],
            'offices.edit' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Offices', 'url' => route('offices.index')],
                ['label' => 'Edit office', 'url' => null],
            ],
            'offices.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Offices', 'url' => route('offices.index')],
                ['label' => 'Office details', 'url' => null],
            ],

            // Hubs
            'hub.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Hubs', 'url' => null],
            ],
            'hub.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Hubs', 'url' => route('hub.index')],
                ['label' => 'Create hub', 'url' => null],
            ],
            'hub.show' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Hubs', 'url' => route('hub.index')],
                ['label' => 'Hub details', 'url' => null],
            ],
            'hub.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Hubs', 'url' => route('hub.index')],
                ['label' => 'Hub details', 'url' => null],
            ],

            // Agents
            'agents.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Agents', 'url' => null],
            ],
            'agents.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Agents', 'url' => route('agents.index')],
                ['label' => 'Create agent', 'url' => null],
            ],
            'agents.edit' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Agents', 'url' => route('agents.index')],
                ['label' => 'Edit agent', 'url' => null],
            ],
            'agents.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Agents', 'url' => route('agents.index')],
                ['label' => 'Agent details', 'url' => null],
            ],

            // Other companies
            'other-companies.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Other companies', 'url' => null],
            ],
            'other-companies.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Other companies', 'url' => route('other-companies.index')],
                ['label' => 'Create company', 'url' => null],
            ],
            'other-companies.edit' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Other companies', 'url' => route('other-companies.index')],
                ['label' => 'Edit company', 'url' => null],
            ],
            'other-companies.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Other companies', 'url' => route('other-companies.index')],
                ['label' => 'Company details', 'url' => null],
            ],

            // Suppliers
            'suppliers.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Suppliers', 'url' => null],
            ],
            'suppliers.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                ['label' => 'Create supplier', 'url' => null],
            ],
            'suppliers.edit' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                ['label' => 'Edit supplier', 'url' => null],
            ],
            'suppliers.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                ['label' => 'Supplier details', 'url' => null],
            ],

            // Customers
            'customers.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => null],
            ],
            'customers.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => route('customers.index')],
                ['label' => 'Create customer', 'url' => null],
            ],
            'customers.edit' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => route('customers.index')],
                ['label' => 'Edit customer', 'url' => null],
            ],
            'customers.vessels.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => route('customers.index')],
                ['label' => 'Vessels', 'url' => null],
            ],
            'customers.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => route('customers.index')],
                ['label' => 'Customer details', 'url' => null],
            ],
            'contacts.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Customers', 'url' => route('customers.index')],
                ['label' => 'Contacts', 'url' => null],
            ],

            // Vessels
            'vessels.index' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Vessels', 'url' => null],
            ],
            'vessels.create' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Vessels', 'url' => route('vessels.index')],
                ['label' => 'Create vessel', 'url' => null],
            ],
            'vessels.*' => [
                ['label' => 'Administration', 'url' => null],
                ['label' => 'Vessels', 'url' => route('vessels.index')],
                ['label' => 'Vessel details', 'url' => null],
            ],
        ];
    }

    protected static function sectionLabel(string $section): ?string
    {
        return match ($section) {
            'stocks', 'stock-follow-up', 'pickup-work-list', 'create-crr', 'etl-stock-items' => 'Stocks',
            'shipments', 'create-shipment', 'create-pre-alert', 'transit', 'pre-alert-reminders', 'shipment-follow-up', 'cost-follow-up' => 'Shipments',
            'billable-shipments', 'all-invoices', 'all-incoming-invoices', 'all-costs', 'accounting', 'billing.invoicing', 'billing.debit-notes', 'billing.credit-notes' => 'Billing',
            'offices', 'hub', 'agents', 'other-companies', 'suppliers', 'customers', 'vessels', 'contacts', 'administration' => 'Administration',
            default => $section !== '' ? self::humanize($section) : null,
        };
    }

    protected static function sectionUrl(string $section): ?string
    {
        return match ($section) {
            'stocks' => route('stocks'),
            'shipments' => route('shipments'),
            'offices' => route('offices.index'),
            'hub' => route('hub.index'),
            'agents' => route('agents.index'),
            'other-companies' => route('other-companies.index'),
            'suppliers' => route('suppliers.index'),
            'customers', 'contacts' => route('customers.index'),
            'vessels' => route('vessels.index'),
            default => null,
        };
    }

    protected static function actionLabel(string $action, string $section = ''): string
    {
        return match ($action) {
            'index' => self::humanize($section),
            'create' => 'Create',
            'edit', 'show', 'update' => 'Edit',
            'store' => 'Create',
            default => self::humanize($action),
        };
    }

    protected static function humanize(string $value): string
    {
        $value = str_replace(['-', '_', '.'], ' ', $value);

        return Str::title(trim($value));
    }
}
