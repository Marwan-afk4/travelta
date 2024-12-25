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
        Schema::create('manuel_tour_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name');
            $table->foreignId(column: 'manuel_tour_id')->nullable()->constrained('manuel_tours')->onUpdate('cascade')->onDelete('cascade');
            $table->string('room_type');
            $table->date('check_in');
            $table->date('check_out'); 
            $table->string('nights');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_tour_hotels');
    }
};
