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
        Schema::connection('mysql')->create('po_detail', function (Blueprint $table) {
            $table->id('po_detail_no', 25);
            $table->string('po_no', 25);
            $table->foreign('po_no')->references('po_no')->on('po_header')->onDelete('cascade');
            $table->integer('po_line')->nullable();
            $table->integer('po_sequence')->nullable();
            $table->string('item_code', 25)->nullable();
            $table->string('code_item_type', 25)->nullable();
            $table->string('bp_part_no', 255)->nullable();
            $table->string('bp_part_name', 255)->nullable();
            $table->string('item_desc_a', 255)->nullable();
            $table->string('item_desc_b', 255)->nullable();
            $table->date('planned_receipt_date')->nullable();
            $table->integer('po_qty')->nullable();
            $table->integer('receipt_qty')->nullable();
            $table->integer('invoice_qty')->nullable();
            $table->string('purchase_unit', 25)->nullable();
            $table->integer('price')->nullable();
            $table->integer('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_detail');
    }
};
