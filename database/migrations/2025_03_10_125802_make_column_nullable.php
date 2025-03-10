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
        Schema::table('tour_pricings', function (Blueprint $table) {
            $table->integer('min_age')->nullable()->change();
            $table->integer('max_age')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_pricings', function (Blueprint $table) {
            $table->integer('min_age')->nullable(false)->change();
            $table->integer('max_age')->nullable(false)->change();
        });
    }
};
