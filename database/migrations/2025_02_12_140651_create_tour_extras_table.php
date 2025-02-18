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
        Schema::create('tour_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'tour_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->float('price');
            $table->enum('type', ['one_time', 'person', 'night']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_extras');
    }
};
