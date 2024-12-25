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
        Schema::create('manuel_flights', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['domestic', 'international'])->nullable();
            $table->enum('direction', ['one_way', 'round_trip', 'multi_city'])->nullable();
            $table->string('from_to', 1000)->nullable();
            $table->datetime('departure')->nullable();
            $table->datetime('arrival')->nullable();
            $table->string('class')->nullable();
            $table->string('adults')->nullable();
            $table->string('childreen')->nullable();
            $table->string('infants')->nullable();
            $table->string('airline')->nullable();
            $table->string('ticket_number')->nullable();
            $table->float('adult_price')->nullable();
            $table->float('child_price')->nullable();
            $table->string('ref_pnr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_flights');
    }
};
