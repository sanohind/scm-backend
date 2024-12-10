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
        // Step 1: Make the column nullable with a default value
        Schema::table('dn_detail', function (Blueprint $table) {
            $table->integer('qty_confirm')->nullable()->default(0)->change();
        });

        // Step 2: Update existing NULL values to 0
        DB::statement('UPDATE dn_detail SET qty_confirm = 0 WHERE qty_confirm IS NULL');

        // Step 3: Make the column non-nullable
        Schema::table('dn_detail', function (Blueprint $table) {
            $table->integer('qty_confirm')->nullable(false)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column to nullable without a default value
        Schema::table('dn_detail', function (Blueprint $table) {
            $table->integer('qty_confirm')->nullable()->default(null)->change();
        });
    }
};
