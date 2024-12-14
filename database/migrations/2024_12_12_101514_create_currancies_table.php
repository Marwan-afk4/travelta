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
        Schema::create('currancies', function (Blueprint $table) {
            $table->id();
            $table->string('currancy_name')->notnull();
            $table->string('currancy_symbol')->notnull();
            $table->string('currancy_code')->notnull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currancies');
    }
};
