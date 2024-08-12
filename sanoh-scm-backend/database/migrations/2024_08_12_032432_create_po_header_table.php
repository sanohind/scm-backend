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
        Schema::create('po_header', function (Blueprint $table) {
            $table->string('po_no', 25)->primary();
            $table->string('bp_code', 25);
            $table->foreign('bp_code')->references('bp_code')->on('business_partner')->onDelete('cascade');
            $table->string('po_type_desc', 25);
            $table->date('po_date');
            $table->integer('po_year');
            $table->integer('po_period');
            $table->string('po_status', 25);
            $table->string('references_1', 255);
            $table->string('references_2', 255);
            $table->string('attn_name', 25);
            $table->string('po_currency', 25);
            $table->integer('pr_no');
            $table->date('planned_receipt_date');
            $table->string('po_origin', 25);
            $table->integer('po_revision_no');
            $table->date('po_revision_date');
            $table->string('response', 25);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_header');
    }
};
