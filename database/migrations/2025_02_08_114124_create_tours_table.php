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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('video_link')->nullable();
            $table->enum('tour_type', ['private', 'group']);
            $table->boolean('status')->default(1);
            $table->integer('days');
            $table->integer('nights');
            $table->foreignId(column: 'tour_type_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('featured', ['yes', 'no']);
            $table->date('featured_from');
            $table->date('featured_to');
            $table->integer('deposit')->default(0);
            $table->enum('deposit_type', ['precentage', 'fixed']);
            $table->integer('tax')->default(0);
            $table->enum('tax_type', ['precentage', 'fixed']);
            $table->foreignId(column: 'pick_up_country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'pick_up_city_id')->nullable()->constrained('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->string('pick_up_map');
            $table->enum('destination_type', ['single', 'multiple']);
            $table->string('tour_email');
            $table->string('tour_website');
            $table->string('tour_phone');
            $table->string('tour_address');
            $table->string('payments_options');
            $table->text('policy');
            $table->boolean('cancelation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
