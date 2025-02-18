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
        Schema::create('agent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
            $table->enum('type', ['collect', 'payment']);
            $table->date('date')->nullable();
            $table->string('code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_payments');
    }
};
