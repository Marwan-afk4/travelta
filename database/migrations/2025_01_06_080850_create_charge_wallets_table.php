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
        Schema::create('charge_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->float('amount')->default(0);
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'rejected', 'approve'])->default('pending');
            $table->string('rejected_reason', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_wallets');
    }
};
