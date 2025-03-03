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
        Schema::create('book_tourengines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('from_supplier_id')->nullable()->constrained('agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('to_hotel_id')->nullable()->constrained('tour_hotels')->onUpdate('cascade')->onDelete('cascade');
            $table->string('to_name')->notnullable();
            $table->string('to_email')->notnullable();
            $table->string('to_phone')->notnullable();
            $table->string('to_role')->notnullable();
            $table->integer('no_of_people')->notnullable();
            $table->string('code')->notnullable();
            $table->foreignId('currency_id')->nullable()->constrained('currency_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('total_price')->notnullable();
            $table->enum('status', ['pending', 'confirmed', 'vouchered','canceled'])->default('confirmed');
            $table->enum('payment_status', ['full','partial','half','later'])->default('full');
            $table->text('special_request')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_tourengines');
    }
};
