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
        Schema::table('affilate_agents', function (Blueprint $table) {
            $table->foreignId(column: 'plan_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('price_cycle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affilate_agents', function (Blueprint $table) {
            //
        });
    }
};
