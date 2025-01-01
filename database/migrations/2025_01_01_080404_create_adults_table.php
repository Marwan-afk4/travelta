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
        Schema::create('adults', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'manuel_booking_id')->nullable()->constrained('manuel_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adults');
    }
};
