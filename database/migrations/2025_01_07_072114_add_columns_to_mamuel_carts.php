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
        Schema::table('manuel_carts', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'approve', 'rejected']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mamuel_carts', function (Blueprint $table) {
            //
        });
    }
};
