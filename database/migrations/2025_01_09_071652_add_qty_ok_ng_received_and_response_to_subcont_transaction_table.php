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
        Schema::connection('mysql')->table('subcont_transaction', function (Blueprint $table) {
            $table->integer('actual_qty_ok_receive')->nullable();
            $table->integer('actual_qty_ng_receive')->nullable();
            $table->string('response', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_transaction', function (Blueprint $table) {
            $table->dropColumn('actual_qty_ok_receive');
            $table->dropColumn('actual_qty_ng_receive');
            $table->dropColumn('response');
        });
    }
};
