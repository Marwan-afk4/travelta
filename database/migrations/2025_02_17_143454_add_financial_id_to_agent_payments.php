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
        Schema::table('agent_payments', function (Blueprint $table) {
            $table->foreignId('financial_id')->nullable()->constrained('finantiol_acountings')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_payments', function (Blueprint $table) {
            //
        });
    }
};
