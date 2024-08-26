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
        Schema::connection('mysql')->create('dn_label', function (Blueprint $table) {
            $table->string('dn_label_no', 255)->primary();
            $table->string('dn_detail_no', 25);
            $table->foreign('dn_detail_no')->references('dn_detail_no')->on('dn_detail')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dn_label');
    }
};
