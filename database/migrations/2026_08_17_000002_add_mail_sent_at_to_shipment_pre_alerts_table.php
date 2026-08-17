<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_pre_alerts', function (Blueprint $table) {
            $table->timestamp('mail_sent_at')->nullable()->after('form_hash');
        });

        // Existing pre-alerts were already used historically — don't force a resend highlight.
        DB::table('shipment_pre_alerts')
            ->whereNull('mail_sent_at')
            ->update(['mail_sent_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('shipment_pre_alerts', function (Blueprint $table) {
            $table->dropColumn('mail_sent_at');
        });
    }
};
