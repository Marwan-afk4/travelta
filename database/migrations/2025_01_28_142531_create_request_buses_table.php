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
        Schema::create('request_buses', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to')->nullable(); 
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->datetime('departure')->nullable();
            $table->datetime('arrival')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('childreen')->nullable();
            $table->float('adult_price')->nullable();
            $table->float('child_price')->nullable();
            $table->string('bus')->nullable();
            $table->string('bus_number')->nullable();
            $table->string('driver_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_buses');
    }
};
