<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            if (! Schema::hasColumn('hubs', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('phone_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            if (Schema::hasColumn('hubs', 'contact_person')) {
                $table->dropColumn('contact_person');
            }
        });
    }
};
