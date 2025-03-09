<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('group_pricings')) {
            Schema::create('group_pricings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pricing_id')->constrained('room_pricings')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('group_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_pricings');
    }
};
