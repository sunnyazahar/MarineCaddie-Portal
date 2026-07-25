<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 32)->default('other'); // comments, pickups, costs, other
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('link_label')->nullable();
            $table->string('link_url')->nullable();
            $table->string('icon')->default('comment'); // comment, pickup, cost, other
            $table->boolean('is_read')->default(false);
            $table->nullableMorphs('related');
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['user_id', 'category', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
