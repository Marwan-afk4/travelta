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
        Schema::create('room_pricing_data', function (Blueprint $table) {
            $table->id();
            $table->enum('room_type', ['single', 'double', 'triple', 'quadrant']);
            $table->enum('meal_plan', ['bed', 'bed_breakfast', 'half_board', 'full_board', 'all_inclusive']);
            $table->integer('adults');
            $table->integer('children');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_pricing_data');
    }
};
