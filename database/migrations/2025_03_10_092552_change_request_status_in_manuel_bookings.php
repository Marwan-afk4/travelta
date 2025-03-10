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
            $table->dropColumn('request_status');
            $table->enum('request_status', ['pending', 'reject', 'approve', 'upon_availability'])->default('pending');
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
