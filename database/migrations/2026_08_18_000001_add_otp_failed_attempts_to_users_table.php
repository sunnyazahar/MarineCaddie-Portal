<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('otp_failed_attempts')->default(0)->after('login_otp_sent_at');
            $table->timestamp('otp_blocked_until')->nullable()->after('otp_failed_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_failed_attempts', 'otp_blocked_until']);
        });
    }
};
