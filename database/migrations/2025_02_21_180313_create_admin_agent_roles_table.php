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
        Schema::create('admin_agent_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->nullable()->constrained('admin_agent_positions')->onUpdate('cascade')->onDelete('cascade');
            $table->string('module');
            $table->string('action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_agent_roles');
    }
};
