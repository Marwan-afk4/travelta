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
        Schema::create('bookingengine_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_supplier_id')->nullable()->constrained('agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('to_agent_id')->nullable()->constrained('agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('to_customer_id')->nullable()->constrained('customers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->onUpdate('cascade')->onDelete('cascade');
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->string('room_type')->nullable();
            $table->integer('no_of_adults')->nullable();
            $table->integer('no_of_children')->nullable();
            $table->integer('no_of_nights')->nullable();
            $table->enum('payment_status', ['full', 'partial', 'half' , 'later']);
            $table->string('code')->nullable();
            $table->enum('status', ['done', 'inprogress', 'faild'])->default('inprogress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingengine_lists');
    }
};
