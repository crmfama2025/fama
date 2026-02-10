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
        Schema::table('contract_payable_clears', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->integer('returned_status')->default(0)->comment('0-Normal Clear, 1-Returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_payable_clears', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('returned_status');
        });
    }
};
