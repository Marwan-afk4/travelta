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
        Schema::create('adult_engines', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->morphs('booking_engine');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adult_engines');
    }
};
