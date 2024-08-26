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
            $table->integer('po_detail_no', 25)->primary();
            $table->string('po_no', 25);
            $table->foreign('po_no')->references('po_no')->on('po_header')->onDelete('cascade');
            $table->integer('po_line');
            $table->integer('po_sequence');
            $table->string('item_code', 25);
            $table->string('code_item_type', 25);
            $table->string('bp_part_no', 255);
            $table->string('bp_part_name', 255);
            $table->string('item_desc_a', 255);
            $table->string('item_desc_b', 255);
            $table->date('planned_receipt_date');
            $table->integer('po_qty');
            $table->integer('receipt_qty');
            $table->integer('invoice_qty');
            $table->string('purchase_unit', 25);
            $table->integer('price');
            $table->integer('amount');
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
