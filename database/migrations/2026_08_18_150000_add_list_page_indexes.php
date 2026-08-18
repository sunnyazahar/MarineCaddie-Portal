<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('shipments', 'shipments_status_id_idx', ['status', 'id']);
        $this->addIndexIfMissing('shipments', 'shipments_created_by_id_idx', ['created_by', 'id']);
        $this->addIndexIfMissing('shipments', 'shipments_manager_status_id_idx', ['account_manager_id', 'status', 'id']);
        $this->addIndexIfMissing('shipments', 'shipments_departure_port_code_idx', 'departure_port_code');
        $this->addIndexIfMissing('shipments', 'shipments_consignee_port_code_idx', 'consignee_port_code');

        $this->addIndexIfMissing('crrs', 'crrs_status_id_idx', ['status', 'id']);
        $this->addIndexIfMissing('crrs', 'crrs_hub_agent_idx', 'hub_agent');
        $this->addIndexIfMissing('crrs', 'crrs_vessel_name_idx', 'vessel_name');
        $this->addIndexIfMissing('crrs', 'crrs_transit_id_idx', 'transit_id');
        $this->addIndexIfMissing('crrs', 'crrs_internal_shipment_idx', 'internal_shipment');
        $this->addIndexIfMissing('crrs', 'crrs_supplier_reference_idx', 'supplier_reference');
        $this->addIndexIfMissing('crrs', 'crrs_supplier_idx', 'supplier');

        $this->addIndexIfMissing('customers', 'customers_customer_name_idx', 'customer_name');
        $this->addIndexIfMissing('customer_vessels', 'customer_vessels_vessel_idx', 'vessel');
        $this->addIndexIfMissing('customer_vessels', 'customer_vessels_vessel_alias_idx', 'vessel_name_alias');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('customer_vessels', 'customer_vessels_vessel_idx');
        $this->dropIndexIfExists('customer_vessels', 'customer_vessels_vessel_alias_idx');
        $this->dropIndexIfExists('customers', 'customers_customer_name_idx');
        $this->dropIndexIfExists('crrs', 'crrs_status_id_idx');
        $this->dropIndexIfExists('crrs', 'crrs_hub_agent_idx');
        $this->dropIndexIfExists('crrs', 'crrs_vessel_name_idx');
        $this->dropIndexIfExists('crrs', 'crrs_transit_id_idx');
        $this->dropIndexIfExists('crrs', 'crrs_internal_shipment_idx');
        $this->dropIndexIfExists('crrs', 'crrs_supplier_reference_idx');
        $this->dropIndexIfExists('crrs', 'crrs_supplier_idx');
        $this->dropIndexIfExists('shipments', 'shipments_status_id_idx');
        $this->dropIndexIfExists('shipments', 'shipments_created_by_id_idx');
        $this->dropIndexIfExists('shipments', 'shipments_manager_status_id_idx');
        $this->dropIndexIfExists('shipments', 'shipments_departure_port_code_idx');
        $this->dropIndexIfExists('shipments', 'shipments_consignee_port_code_idx');
    }

    private function addIndexIfMissing(string $table, string $name, array|string $columns): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($name, $columns) {
            $table->index($columns, $name);
        });
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($name) {
            $table->dropIndex($name);
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        $indexes = DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$name]);

        return $indexes !== [];
    }
};
