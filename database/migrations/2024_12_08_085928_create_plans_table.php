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
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name')->notnull();
                $table->text('description')->nullable();
                $table->integer('user_limit')->notnull();
                $table->integer('branch_limit')->notnull();
                $table->integer('period_in_days')->notnull();
                $table->string('module_type')->notnull();
                $table->float('price')->notnull();
                $table->string('discount_type')->notnull();
                $table->float('price_after_discount')->nullable();
                $table->float('admin_cost')->nullable();
                $table->float('branch_cost')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
