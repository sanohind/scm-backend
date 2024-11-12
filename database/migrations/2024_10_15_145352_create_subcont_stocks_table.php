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
        Schema::connection('mysql')->create('subcont_stock', function (Blueprint $table) {
            $table->integer('sub_stock_id', true)->primary();
            $table->integer('sub_item_id');
            $table->foreign('sub_item_id')->references('sub_item_id')->on('subcont_item')->onDelete('cascade');
            $table->string('item_code',length: 50);
            $table->integer('incoming_fresh_stock')->default(0);
            $table->integer('incoming_replating_stock')->default(0);
            $table->integer('process_fresh_stock')->default(0);
            $table->integer('process_replating_stock')->default(0);
            $table->integer('ng_fresh_stock')->default(0);
            $table->integer('ng_replating_stock')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcont_stock');
    }
};
