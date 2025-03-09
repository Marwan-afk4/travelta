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
        Schema::table('book_tour_rooms', function (Blueprint $table) {
            $table->foreignId('to_hotel_id')->after('book_tour_id')->constrained('tour_hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_tour_rooms', function (Blueprint $table) {
            //
        });
    }
};
