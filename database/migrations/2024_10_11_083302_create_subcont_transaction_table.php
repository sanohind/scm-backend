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
        Schema::connection('mysql')->create('subcont_transaction', function (Blueprint $table) {
            $table->integer('sub_transaction_id')->primary()->autoIncrement();
            $table->string('delivery_note', 255);
            $table->integer('sub_item_id')->nullable();
            $table->foreign('sub_item_id')->references('sub_item_id')->on('subcont_item')->onDelete('cascade');
            $table->string('item_code', 50)->nullable();
            $table->string('transaction_type', 25)->nullable();
            $table->date('transaction_date')->nullable();
            $table->time('transaction_time')->nullable();
            $table->integer('qty_ok')->nullable();
            $table->integer('qty_ng')->nullable();
            $table->string('status', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcont_transaction');
    }
};
