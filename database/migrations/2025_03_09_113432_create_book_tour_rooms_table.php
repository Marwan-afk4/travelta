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
        Schema::create('book_tour_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_tour_id')->constrained('book_tourengines')->onDelete('cascade');
            $table->string('single_room_count')->nullable();
            $table->string('double_room_count')->nullable();
            $table->string('triple_room_count')->nullable();
            $table->string('quad_room_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_tour_rooms');
    }
};
