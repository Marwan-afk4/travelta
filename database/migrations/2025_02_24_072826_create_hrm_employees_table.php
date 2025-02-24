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
        Schema::create('hrm_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'affilate_id')->nullable()->constrained('affilate_agents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'agent_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->foreignId(column: 'department_id')->nullable()->constrained('hrm_departments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId(column: 'role_id')->nullable()->constrained('admin_agent_positions')->onUpdate('cascade')->onDelete('set null');
            $table->string('image')->nullable();
            $table->string('user_name')->nullable();
            $table->string('password')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_employees');
    }
};
