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
        if (!Schema::hasTable('legal_papers')) {
            Schema::create('legal_papers', function (Blueprint $table) {
                $table->id();
                $table->longText('image');
                $table->foreignId('user_id')->nullable()->constrained('customers')->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_papers');
    }
};
