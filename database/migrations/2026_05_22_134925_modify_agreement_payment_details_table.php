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
        Schema::table('agreement_payment_details', function (Blueprint $table) {
            $table->tinyInteger('is_invoice_added')->default(0)->comment('0=pending,1=added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('agreement_payment_details', function (Blueprint $table) {
            $table->dropColumn('is_invoice_added');
        });
    }
};
