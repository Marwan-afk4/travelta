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
        // Schema::create('hotels', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('hotel_name')->notnull();
        //     $table->text('description')->nullable();
        //     $table->string('email')->unique()->nullable();
        //     $table->string('phone_number')->notnull()->unique();
        //     $table->string('hotel_logo')->nullable();
        //     $table->foreignId('country_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('city_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('zone_id')->constrained()->onDelete('cascade');
        //     $table->integer('stars')->notNull();
        //     $table->string('hotel_video_link')->nullable();
        //     $table->string('hotel_website')->nullable();
        //     $table->dateTime('check_in')->nullable();
        //     $table->dateTime('check_out')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
