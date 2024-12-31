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
        Schema::table('affilate_agents', function (Blueprint $table) {
            $table->float('total_booking')->default(0);
            $table->float('total_commission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affilate_agents', function (Blueprint $table) {
            //
        });
    }
};