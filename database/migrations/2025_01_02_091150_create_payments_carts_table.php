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
        Schema::create('payments_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'manuel_cart_id')->nullable()->constrained('manuel_carts')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
            $table->date('date');
            $table->boolean('payment')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_carts');
    }
};
