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
        Schema::create('business_partner_email', function (Blueprint $table) {
            $table->string('partner_id', 25);
            $table->foreign('partner_id')->references('bp_code')->on('business_partner')->onDelete('cascade');
            $table->unsignedBigInteger('email_id');
            $table->foreign('email_id')->references('email_id')->on('email')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_partner_email');
    }
};
