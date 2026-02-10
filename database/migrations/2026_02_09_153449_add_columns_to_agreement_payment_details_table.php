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
            //
            $table->integer('transaction_type')->default(0)->comment('1 = Receive, 2 = Pay Back');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_payment_details', function (Blueprint $table) {
            //
            $table->dropColumn('transaction_type');
        });
    }
};
