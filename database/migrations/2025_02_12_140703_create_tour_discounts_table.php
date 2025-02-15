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
        Schema::create('tour_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'tour_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('from');
            $table->integer('to');
            $table->float('discount');
            $table->enum('type', ['precentage', 'fixed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_discounts');
    }
};
