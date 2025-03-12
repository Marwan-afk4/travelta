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
        Schema::table('booking_tasks', function (Blueprint $table) {
            $table->foreignId(column: 'engine_tour_id')->nullable()->constrained('book_tourengines')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_tasks', function (Blueprint $table) {
            //
        });
    }
};
