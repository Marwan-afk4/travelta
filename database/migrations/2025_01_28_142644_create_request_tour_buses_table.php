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
        Schema::create('request_tour_buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'request_tour_id')->nullable()->constrained('request_tours')->onUpdate('cascade')->onDelete('cascade');
            $table->string('transportation')->nullable();
            $table->integer('seats')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_tour_buses');
    }
};
