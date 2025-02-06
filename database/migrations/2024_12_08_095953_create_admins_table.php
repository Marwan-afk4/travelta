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
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->foreignId(column: 'admin_position_id')->constrained()->onDelete('cascade');
                $table->foreignId('zone_id')->constrained()->onDelete('cascade');
                $table->string('name')->notnull();
                $table->string('email')->unique()->nullable();
                $table->string('password')->nullable();
                $table->string('phone_number')->notnull()->unique();
                $table->longText('legal_paper')->notnull();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
