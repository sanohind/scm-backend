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
        Schema::table('subcont_transaction', function (Blueprint $table) {
            $table->dropForeign('subcont_transaction_sub_item_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_transaction', function (Blueprint $table) {
            $table->foreign('sub_item_id')->references('sub_item_id')->on('subcont_item')->onDelete('cascade');
        });
    }
};
