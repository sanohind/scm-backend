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
        Schema::connection('mysql')->create('subcont_item', function (Blueprint $table) {
            $table->integer('sub_item_id', true)->primary();
            $table->string('bp_code',25)->nullable();
            $table->foreign('bp_code')->references('bp_code')->on('business_partner')->onDelete('cascade');
            $table->string('item_code', 50)->nullable();
            $table->string('item_name',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcont_item');
    }
};
