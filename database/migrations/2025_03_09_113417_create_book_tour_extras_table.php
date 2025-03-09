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
        Schema::create('book_tour_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_tour_id')->constrained('book_tourengines')->onDelete('cascade');
            $table->foreignId('extra_id')->constrained('tour_extras')->onDelete('cascade');
            $table->integer('extra_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_tour_extras');
    }
};
