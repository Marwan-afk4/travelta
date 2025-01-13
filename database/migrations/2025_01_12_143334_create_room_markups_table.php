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
        Schema::create('room_markups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('agent_code');
            $table->float('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_markups');
    }
};
