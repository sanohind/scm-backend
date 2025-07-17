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
        Schema::table('dn_detail', function (Blueprint $table) {
            $table->string('item_customer', 25)->nullable()->after('item_desc_b');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dn_detail', function (Blueprint $table) {
            $table->dropColumn('item_customer');
        });
    }
};
