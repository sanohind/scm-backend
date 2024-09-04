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
        Schema::connection('mysql')->create('business_partner', function (Blueprint $table) {
            $table->string('bp_code',25)->primary();
            $table->string('bp_name',255)->nullable();
            $table->string('bp_status_desc',25)->nullable();
            $table->string('bp_currency',25)->nullable();
            $table->string('country',25)->nullable();
            $table->string('adr_line_1',255)->nullable();
            $table->string('adr_line_2',255)->nullable();
            $table->string('adr_line_3',255)->nullable();
            $table->string('adr_line_4',255)->nullable();
            $table->string('bp_phone',255)->nullable();
            $table->string('bp_fax',25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_partner');
    }
};
