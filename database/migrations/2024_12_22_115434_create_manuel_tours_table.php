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
        Schema::create('manuel_tours', function (Blueprint $table) {
            $table->id();  
            $table->string('tour');
            $table->enum('type', ['domestic', 'international']);
            $table->foreignId(column: 'manuel_booking_id')->nullable()->constrained('manuel_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('destination');
            $table->string('hotel_name');
            $table->string('room_type');
            $table->date('check_in');
            $table->date('check_out'); 
            $table->string('nights');
            $table->integer('adults');
            $table->integer('childreen'); 
            $table->float('adult_price');
            $table->float('child_price');
            $table->enum('transportation', ['Flight', 'Bus']);
            $table->integer('seats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_tours');
    }
};
