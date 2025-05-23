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
        Schema::table('payments_carts', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
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
