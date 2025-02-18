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
        Schema::create('agent_payables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('manuel_booking_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
            $table->float('paid')->default(0);
            $table->float('payable');
            $table->date('due_date');
            $table->date('manuel_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_payables');
    }
};
