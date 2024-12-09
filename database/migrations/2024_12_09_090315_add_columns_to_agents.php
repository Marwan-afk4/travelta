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
        Schema::table('agents', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('source_id')->nullable()->constrained('customer_sources')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            //
        });
    }
};
