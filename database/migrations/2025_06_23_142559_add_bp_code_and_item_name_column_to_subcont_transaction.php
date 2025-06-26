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
            $table->string('bp_code',50)->nullable()->after('sub_transaction_id');
            $table->string('item_name',50)->nullable()->after('item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_transaction', function (Blueprint $table) {
            $table->dropColumn('bp_code');
            $table->dropColumn('item_name');
        });
    }
};
