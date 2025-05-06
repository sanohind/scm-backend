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
        Schema::table('subcont_item', function (Blueprint $table) {
            $table->integer('min_stock_incoming')->nullable();
            $table->integer('min_stock_outgoing')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_item', function (Blueprint $table) {
            $table->dropColumn('min_stock_incoming');
            $table->dropColumn('min_stock_outgoing');
        });
    }
};
