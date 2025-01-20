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
        Schema::create('room_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->date('from');
            $table->date('to');
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_pricings');
    }
};
