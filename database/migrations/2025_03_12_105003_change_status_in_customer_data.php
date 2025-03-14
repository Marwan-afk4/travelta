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
        Schema::table('customer_data', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', ['active', 'inactive', 'suspend'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_data', function (Blueprint $table) {
            //
        });
    }
};
