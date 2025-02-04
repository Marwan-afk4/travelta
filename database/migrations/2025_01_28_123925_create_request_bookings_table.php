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
        Schema::create('request_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'customer_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'admin_agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'service_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->float('expected_revenue')->nullable(); 
            $table->enum('priority', ['Low', 'Normal', 'High']);
            $table->enum('stages', ['Pending', 'Price quotation', 'Negotiation', 'Won', 'Won Canceled', 'Lost'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_bookings');
    }
};
