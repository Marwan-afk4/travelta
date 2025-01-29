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
        Schema::create('request_tour_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('destination')->nullable();
            $table->string('hotel_name')->nullable();
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('room_type')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable(); 
            $table->string('nights')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_tour_hotels');
    }
};
