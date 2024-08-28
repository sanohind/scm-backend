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
        Schema::connection('mysql')->create('dn_header', function (Blueprint $table) {
            $table->string('dn_no', 25)->primary(); // fixed no_dn
            $table->string('po_no', 25)->nullable();
            $table->foreign('po_no')->references('po_no')->on('po_header')->onDelete('cascade');
            $table->string('supplier_code',255)->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->date('dn_created_date')->nullable();
            $table->integer('dn_year')->nullable();
            $table->integer('dn_period')->nullable();
            $table->date('plan_delivery_date')->nullable();
            $table->time('plan_delivery_time')->nullable();
            $table->string('status_desc', 25)->nullable();
            $table->dateTime('confirm_update_at')->nullable();
            $table->dateTime('dn_printed_at')->nullable();
            $table->dateTime('dn_label_printed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dn_header');
    }
};
