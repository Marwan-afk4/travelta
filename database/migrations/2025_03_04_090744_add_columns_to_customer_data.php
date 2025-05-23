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
        Schema::table('customer_data', function (Blueprint $table) {
            $table->string('watts')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('customer_sources')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('agent_sales_id')->nullable()->constrained('hrm_employees')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('service_id')->nullable()->constrained('services')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('nationality_id')->nullable()->constrained('nationalities')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onUpdate('cascade')->onDelete('set null');
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            //
        });
    }
};
