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
            $table->float('mark_up')->after('cost');
            $table->enum('mark_up_type', ['value', 'precentage'])->after('mark_up');
            $table->float('price')->after('mark_up_type');
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
