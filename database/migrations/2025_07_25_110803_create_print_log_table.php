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
        // Drop table dn_label
        Schema::drop('dn_label');

        Schema::create('print_log', function (Blueprint $table) {
            $table->id();
            $table->string('method_type', 255)->nullable();
            $table->string('method_key', 255)->nullable();
            $table->string('printing_type', 255)->nullable();
            $table->string('created_by', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_log');
        Schema::create('dn_label', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
