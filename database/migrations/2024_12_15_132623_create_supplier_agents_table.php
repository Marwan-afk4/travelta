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
        if (!Schema::hasTable('supplier_agents')) {
            Schema::create('supplier_agents', function (Blueprint $table) {
                $table->id();
                $table->string('agent');
                $table->string('admin_name');
                $table->string('admin_phone');
                $table->string('admin_email');
                $table->string('emails', 500);
                $table->string('phones');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_agents');
    }
};
