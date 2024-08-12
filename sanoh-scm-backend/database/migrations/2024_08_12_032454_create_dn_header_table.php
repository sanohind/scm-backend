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
        Schema::create('dn_header', function (Blueprint $table) {
            $table->string('no_dn', 25)->primary();
            $table->string('po_no', 25);
            $table->foreign('po_no')->references('po_no')->on('po_header')->onDelete('cascade');
            $table->date('dn_created_date');
            $table->integer('dn_year');
            $table->integer('dn_period');
            $table->date('plan_delivery_date');
            $table->time('plan_delivery_time');
            $table->string('status_desc', 25);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dn_header');
    }
};
