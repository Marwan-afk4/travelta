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
        Schema::create('manuel_buses', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to'); 
            $table->foreignId(column: 'manuel_booking_id')->nullable()->constrained('manuel_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->datetime('departure');
            $table->datetime('arrival');
            $table->integer('adults');
            $table->integer('childreen');
            $table->float('adult_price');
            $table->float('child_price');
            $table->string('bus')->nullable();
            $table->string('bus_number')->nullable();
            $table->string('driver_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_buses');
    }
};
