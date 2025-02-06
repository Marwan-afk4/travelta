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
        if (!Schema::hasTable('manuel_bookings')) {
            Schema::create('manuel_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId(column: 'to_supplier_id')->nullable()->constrained('supplier_agents')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId(column: 'to_customer_id')->nullable()->constrained('customers')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId(column: 'from_supplier_id')->nullable()->constrained('supplier_agents')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId(column: 'from_service_id')->nullable()->constrained('services')->onUpdate('cascade')->onDelete('set null');
                $table->float('cost');
                $table->enum('tax_type', ['include', 'exclude']);
                $table->float('total_price');
                $table->foreignId(column: 'currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId(column: 'country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId(column: 'city_id')->nullable()->constrained('cities')->onUpdate('cascade')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuel_bookings');
    }
};
