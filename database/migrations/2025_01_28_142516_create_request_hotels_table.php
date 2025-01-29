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
        Schema::create('request_hotels', function (Blueprint $table) {
            $table->id();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable(); 
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nights')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('room_type')->nullable();
            $table->integer('room_quantity')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('childreen')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_hotels');
    }
};
