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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'plan_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('password');
            $table->float('total_booking')->default(0);
            $table->float('total_commission')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('price_cycle')->nullable();
            $table->enum('role', ['agent', 'supplier']);
            $table->foreignId(column: 'country_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId(column: 'city_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId(column: 'source_id')->nullable()->constrained('customer_sources')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
