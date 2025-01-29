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
        Schema::create('request_tours', function (Blueprint $table) {
            $table->id();  
            $table->string('tour')->nullable();
            $table->enum('type', ['domestic', 'international'])->nullable();
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->datetime('flight_date')->nullable();
            $table->float('adult_price')->nullable();
            $table->float('child_price')->nullable();
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
        Schema::dropIfExists('request_tours');
    }
};
