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
        Schema::create('finantiol_acountings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('details')->nullable();
            $table->float('balance');
            $table->foreignId(column: 'currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finantiol_acountings');
    }
};
