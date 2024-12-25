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
            $table->float('adult_price');
            $table->float('child_price');
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
        Schema::dropIfExists('manuel_tours');
    }
};
