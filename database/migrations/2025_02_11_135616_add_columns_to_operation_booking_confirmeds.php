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
        Schema::table('operation_booking_confirmeds', function (Blueprint $table) {
            $table->foreignId(column: 'booking_engine_id')->nullable()->constrained('bookingengine_lists')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_booking_confirmeds', function (Blueprint $table) {
            //
        });
    }
};
