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
        Schema::table('po_detail', function (Blueprint $table) {
            $table->bigInteger('price')->nullable()->change();
            $table->bigInteger('amount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_detail', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
            $table->integer('amount')->nullable()->change();
        });
    }
};
