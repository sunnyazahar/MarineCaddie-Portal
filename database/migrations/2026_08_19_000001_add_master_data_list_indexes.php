<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes for master-data list pages.
 *
 * Covers: agents, suppliers, vessels, other_companies, customers, hubs
 *
 * Each index targets either:
 *   - An ORDER BY column (prevents filesort)
 *   - A filter column used in WHERE / LIKE prefix queries
 *   - A foreign key used in JOIN / whereHas (country_id, customer_id)
 *   - is_active / hide_in_portal used in hide-inactive filters
 *
 * Note: LIKE '%term%' (contains) cannot use a B-tree index — those columns
 * are intentionally excluded. Only prefix-safe or equality columns are indexed.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── agents ────────────────────────────────────────────────────────────
        // orderBy('agent_name'), filter: city, agent_type, is_active, country_id
        $this->add('agents', 'agents_agent_name_idx',  'agent_name');
        $this->add('agents', 'agents_city_idx',        'city');
        $this->add('agents', 'agents_agent_type_idx',  'agent_type');
        $this->add('agents', 'agents_is_active_idx',   'is_active');
        $this->add('agents', 'agents_country_id_idx',  'country_id');

        // ── suppliers ─────────────────────────────────────────────────────────
        // orderBy('supplier_name'), filter: country_id
        $this->add('suppliers', 'suppliers_supplier_name_idx', 'supplier_name');
        $this->add('suppliers', 'suppliers_country_id_idx',    'country_id');

        // ── customer_vessels (vessels list) ───────────────────────────────────
        // orderBy('vessel'), filter: vessel_imo, vessel_type_alias, customer_id
        $this->add('customer_vessels', 'cv_vessel_type_alias_idx', 'vessel_type_alias');
        $this->add('customer_vessels', 'cv_customer_id_idx',       'customer_id');

        // ── other_companies ───────────────────────────────────────────────────
        // orderBy('company_name'), filter: city, country_id
        $this->add('other_companies', 'oc_company_name_idx', 'company_name');
        $this->add('other_companies', 'oc_city_idx',         'city');
        $this->add('other_companies', 'oc_country_id_idx',   'country_id');

        // ── customers ─────────────────────────────────────────────────────────
        // orderBy('customer_name')
        // (whereHas relations use FK indexes already created by migrations)
        $this->add('customers', 'customers_name_idx', 'customer_name');

        // ── countries ─────────────────────────────────────────────────────────
        // Used in whereHas for country filters across all list pages
        $this->add('countries', 'countries_name_idx',      'name');
        $this->add('countries', 'countries_is_active_idx', 'is_active');
    }

    public function down(): void
    {
        $this->drop('agents', 'agents_agent_name_idx');
        $this->drop('agents', 'agents_city_idx');
        $this->drop('agents', 'agents_agent_type_idx');
        $this->drop('agents', 'agents_is_active_idx');
        $this->drop('agents', 'agents_country_id_idx');

        $this->drop('suppliers', 'suppliers_supplier_name_idx');
        $this->drop('suppliers', 'suppliers_country_id_idx');

        $this->drop('customer_vessels', 'cv_vessel_type_alias_idx');
        $this->drop('customer_vessels', 'cv_customer_id_idx');

        $this->drop('other_companies', 'oc_company_name_idx');
        $this->drop('other_companies', 'oc_city_idx');
        $this->drop('other_companies', 'oc_country_id_idx');

        $this->drop('customers', 'customers_name_idx');

        $this->drop('countries', 'countries_name_idx');
        $this->drop('countries', 'countries_is_active_idx');
    }

    private function add(string $table, string $name, array|string $columns): void
    {
        if ($this->exists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $t) use ($name, $columns) {
            $t->index($columns, $name);
        });
    }

    private function drop(string $table, string $name): void
    {
        if (! $this->exists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $t) use ($name) {
            $t->dropIndex($name);
        });
    }

    private function exists(string $table, string $name): bool
    {
        return DB::select(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?',
            [$name]
        ) !== [];
    }
};
