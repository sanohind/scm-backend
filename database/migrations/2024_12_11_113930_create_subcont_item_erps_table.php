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
        Schema::create('subcont_item_erp', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item', 255);
            $table->string('description', 255);
            $table->string('item_group', 255);
            $table->string('group_desc', 255);
            $table->string('material', 255);
            $table->string('old_item', 255);
            $table->string('unit', 255);
            $table->string('div_code', 255);
            $table->string('divisi', 255);
            $table->string('model', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcont_item_erp');
    }
};
