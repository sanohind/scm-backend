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
        Schema::table('listing_report', function (Blueprint $table) {
            Schema::rename('listing_report', 'performance_report');

            Schema::table('performance_report', function (Blueprint $table)
            {
                $table->renameColumn('po_listing_no', 'performance_no');
                $table->date('date')->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_report', function (Blueprint $table) {
            Schema::rename('performance_report', 'listing_report');

            Schema::table('listing_report', function (Blueprint $table) {
                $table->renameColumn('performance_no', 'po_listing_no');
                $table->dateTime('date')->change();
            });
        });
    }
};
