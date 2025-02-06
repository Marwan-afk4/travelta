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
        if (!Schema::hasTable('customer_data')) {
            Schema::create('customer_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId(column: 'customer_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->float('total_booking')->default(0);
                $table->enum('type', ['lead', 'customer'])->default('lead');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_data');
    }
};
