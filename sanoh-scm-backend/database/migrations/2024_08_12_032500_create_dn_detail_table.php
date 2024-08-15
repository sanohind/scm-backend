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
        Schema::create('dn_detail', function (Blueprint $table) {
            $table->string('dn_detail_no', 25)->primary();
            $table->string('no_dn', 25);
            $table->foreign('no_dn')->references('no_dn')->on('dn_header')->onDelete('cascade');
            $table->integer('dn_line');
            $table->integer('order_origin');
            $table->date('plan_delivery_date');
            $table->time('plan_delivery_time');
            $table->date('actual_receipt_date');
            $table->time('actual_receipt_time');
            $table->string('no_order', 25);
            $table->integer('order_set');
            $table->integer('order_line');
            $table->integer('order_seq');
            $table->string('part_no', 25);
            $table->string('supplier_item_no', 25);
            $table->string('item_desc_a', 255);
            $table->string('item_desc_b', 255);
            $table->integer('lot_number');
            $table->integer('dn_qty');
            $table->integer('receipt_qty');
            $table->string('dn_unit');
            $table->string('dn_snp');
            $table->string('reference',255);
            $table->integer('status_desc');
            $table->integer('qty_confirm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dn_detail');
    }
};
