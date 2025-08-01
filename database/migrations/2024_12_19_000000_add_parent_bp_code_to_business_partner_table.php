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
            $table->string('parent_bp_code', 25)->nullable()->after('bp_code');
            $table->index('parent_bp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('business_partner', function (Blueprint $table) {
            $table->dropIndex(['parent_bp_code']);
            $table->dropColumn('parent_bp_code');
        });
    }
}; 