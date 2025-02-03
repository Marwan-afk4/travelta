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
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId(column: 'country_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
                $table->enum('type', ['precentage', 'value'])->default('precentage');
                $table->float('amount');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
