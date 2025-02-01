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
        Schema::create('request_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'request_booking_id')->nullable()->constrained('request_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('stages', ['Pending','Price quotation','Negotiation','Won','Won Canceled','Lost']);
            $table->enum('action', ['call','message','assign_request']);
            $table->enum('priority', ['hot','warm','cold']);
            $table->date('follow_up_date')->nullable();
            $table->text('result')->nullable();
            $table->string('send_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_stages');
    }
};
