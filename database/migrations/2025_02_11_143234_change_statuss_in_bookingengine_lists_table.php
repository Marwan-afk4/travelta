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
        Schema::table('bookingengine_lists', function (Blueprint $table) {
            $table->dropColumn('status'); // Drop the existing column
        });
        
        Schema::table('bookingengine_lists', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'vouchered', 'canceled'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookingengine_lists', function (Blueprint $table) {
            //
        });
    }
};
