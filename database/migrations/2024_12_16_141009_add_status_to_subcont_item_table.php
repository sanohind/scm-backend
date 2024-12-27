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
            $table->string('status',25)->nullable()->default('1')->after('item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_item', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
