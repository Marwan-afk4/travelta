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
        Schema::create('manuel_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'tax_id')->nullable()->constrained('taxes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'manuel_id')->nullable()->constrained('manuel_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_taxes');
    }
};
