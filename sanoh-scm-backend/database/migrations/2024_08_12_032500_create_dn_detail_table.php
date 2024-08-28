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
        Schema::connection('mysql')->create('dn_detail', function (Blueprint $table) {
            $table->id('dn_detail_no', 25);
            $table->string('dn_no', 25)->nullable(); //fixed no_dn
            $table->foreign('dn_no')->references('dn_no')->on('dn_header')->onDelete('cascade');
            $table->integer('dn_line')->nullable();
            $table->integer('order_origin')->nullable();
            $table->date('plan_delivery_date')->nullable();
            $table->time('plan_delivery_time')->nullable();
            $table->date('actual_receipt_date')->nullable();
            $table->time('actual_receipt_time')->nullable();
            $table->string('no_order', 25)->nullable();
            $table->integer('order_set')->nullable();
            $table->integer('order_line')->nullable();
            $table->integer('order_seq')->nullable();
            $table->string('part_no', 25)->nullable();
            $table->string('supplier_item_no', 25)->nullable();
            $table->string('item_desc_a', 255)->nullable();
            $table->string('item_desc_b', 255)->nullable();
            $table->string('lot_number', 255)->nullable();
            $table->integer('dn_qty')->nullable();
            $table->integer('receipt_qty')->nullable();
            $table->string('dn_unit')->nullable();
            $table->integer('dn_snp')->nullable();
            $table->string('reference',255)->nullable();
            $table->integer('status_desc')->nullable();
            $table->integer('qty_confirm')->nullable();
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
