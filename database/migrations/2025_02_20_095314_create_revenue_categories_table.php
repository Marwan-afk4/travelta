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
        Schema::create('revenue_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'category_id')->nullable()->constrained('revenue_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_categories');
    }
};
