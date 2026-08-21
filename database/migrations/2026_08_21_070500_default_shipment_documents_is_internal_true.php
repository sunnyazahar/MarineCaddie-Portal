<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shipment_documents') || ! Schema::hasColumn('shipment_documents', 'is_internal')) {
            return;
        }

        // Manual uploads attach to mail when Internal is ticked — enable by default.
        DB::table('shipment_documents')->where('is_internal', false)->update(['is_internal' => true]);
    }

    public function down(): void
    {
        // Intentionally left blank — do not force existing docs back to unticked.
    }
};
