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
        Schema::create('nationality_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('nationality_id')->nullable()->constrained('nationalities')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nationality_pricings');
    }
};
