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
        Schema::dropIfExists('tour_prices'); // Replace with your table name
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
