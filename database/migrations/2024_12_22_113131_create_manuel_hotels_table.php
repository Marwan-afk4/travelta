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
        Schema::create('manuel_hotels', function (Blueprint $table) {
            $table->id();
            $table->date('check_in');
            $table->date('check_out'); 
            $table->foreignId(column: 'manuel_booking_id')->nullable()->constrained('manuel_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nights');
            $table->string('hotel_name');
            $table->string('room_type');
            $table->integer('room_quantity');
            $table->integer('adults');
            $table->integer('childreen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_hotels');
    }
};
