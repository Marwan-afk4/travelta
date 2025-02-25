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
        Schema::table('manuel_bookings', function (Blueprint $table) {
            $table->foreignId(column: 'agent_sales_id')->after('id')->nullable()->constrained('hrm_employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuel_bookings', function (Blueprint $table) {
            //
        });
    }
};
