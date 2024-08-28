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
        Schema::connection('mysql')->create('po_header', function (Blueprint $table) {
            $table->string('po_no', 25)->primary();
            $table->string('supplier_code',255);
            $table->foreign('supplier_code')->references('bp_code')->on('business_partner')->onDelete('cascade');
            $table->string('supplier_name', 255)->nullable();
            $table->string('po_type_desc', 25)->nullable();
            $table->date('po_date')->nullable();
            $table->integer('po_year')->nullable();
            $table->integer('po_period')->nullable();
            $table->string('po_status', 25)->nullable();
            $table->string('references_1', 255)->nullable();
            $table->string('references_2', 255)->nullable();
            $table->string('attn_name', 25)->nullable();
            $table->string('po_currency', 25)->nullable();
            $table->string('pr_no')->nullable();
            $table->date('planned_receipt_date')->nullable();
            $table->string('payment_term', 25)->nullable();
            $table->string('po_origin', 25)->nullable();
            $table->integer('po_revision_no')->nullable();
            $table->date('po_revision_date')->nullable();
            $table->string('response', 25)->nullable();
            $table->dateTime('accept_at')->nullable();
            $table->dateTime('decline_at')->nullable();
            $table->dateTime('po_printed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_header');
    }
};
