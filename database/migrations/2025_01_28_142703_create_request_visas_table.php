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
        Schema::create('request_visas', function (Blueprint $table) {
            $table->id();
            $table->string('country')->nullable();
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->date('travel_date')->nullable();
            $table->date('appointment_date')->nullable();
            $table->integer('number')->nullable();
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
        Schema::dropIfExists('request_visas');
    }
};
