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
        Schema::connection('mysql')->table('business_partner', function (Blueprint $table) {
            $table->string('bp_role', 25)->nullable();
            $table->string('bp_role_desc', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('business_partner', function (Blueprint $table) {
            $table->dropColumn('bp_role');
            $table->dropColumn('bp_role_desc');
        });
    }
};
