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
        if (!Schema::hasTable('affilate_agents')) {
            Schema::create('affilate_agents', function (Blueprint $table) {
                $table->id();
                $table->string('f_name');
                $table->string('l_name');
                $table->string('email')->unique();
                $table->string('phone');
                $table->string('password');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affilate_agents');
    }
};
