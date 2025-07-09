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
        Schema::table('payments_carts', function (Blueprint $table) {
            $table->morphs('booking_engine');
            $table->foreignId('to_customer_id')->nullable()->constrained('customers')->onUpdate('cascade')->onDelete('cascade');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments_carts', function (Blueprint $table) {
            //
        });
    }
};
