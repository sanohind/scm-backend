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
        Schema::create('listing_report', function (Blueprint $table) {
            $table->string('po_listing_no', 25)->primary();
            $table->string('bp_code', 25);
            $table->foreign('bp_code')->references('bp_code')->on('business_partner')->onDelete('cascade');
            $table->datetime('date');
            $table->string('file', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_report');
    }
};
