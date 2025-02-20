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
            $table->foreignId('currency_id')->after('amount')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
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
