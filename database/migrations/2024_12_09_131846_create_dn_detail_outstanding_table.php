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
        Schema::create('dn_detail_outstanding', function (Blueprint $table) {
            $table->id('dn_detail_outstanding_no');
            $table->string('no_dn');
            $table->foreign('no_dn')->references('no_dn')->on('dn_header')->cascadeOnDelete();
            $table->unsignedBigInteger('dn_detail_no');
            $table->foreign('dn_detail_no')->references('dn_detail_no')->on('dn_detail')->cascadeOnDelete();
            $table->integer('qty_outstanding')->nullable();
            $table->date('add_outstanding_date')->nullable();
            $table->time('add_outstanding_time')->nullable();
            $table->integer('wave')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dn_detail_outstanding');
    }
};
