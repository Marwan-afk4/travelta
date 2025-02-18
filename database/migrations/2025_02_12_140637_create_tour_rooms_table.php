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
        Schema::create('tour_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'tour_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('adult_single');
            $table->integer('adult_double');
            $table->integer('adult_triple');
            $table->integer('adult_quadruple');
            $table->integer('children_single');
            $table->integer('children_double');
            $table->integer('children_triple');
            $table->integer('children_quadruple');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_rooms');
    }
};
