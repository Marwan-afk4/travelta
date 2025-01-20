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
        Schema::table('room_pricings', function (Blueprint $table) {
            $table->foreignId('pricing_data_id')->after('id')->nullable()->constrained('room_pricing_data')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_pricings', function (Blueprint $table) {
            //
        });
    }
};
