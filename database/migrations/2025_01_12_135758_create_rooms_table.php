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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('status')->default(1);
            $table->enum('price_type', ['fixed', 'variable']);
            $table->float('price')->nullable();
            $table->integer('quantity');
            $table->integer('max_adults');
            $table->integer('max_children');
            $table->integer('max_capacity');
            $table->integer('min_stay');
            $table->foreignId(column: 'room_type_id')->nullable()->constrained('room_types')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId(column: 'hotel_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId(column: 'hotel_meal_id')->nullable()->constrained('hotel_meals')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId(column: 'currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('set null');
            $table->float('b2c_markup');
            $table->float('b2e_markup');
            $table->float('b2b_markup');
            $table->enum('tax_type', ['include', 'exclude', 'include_except']);
            $table->string('check_in');
            $table->string('check_out');
            $table->text('policy');
            $table->text('children_policy');
            $table->enum('cancelation', ['free', 'non_refunable']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
