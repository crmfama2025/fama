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
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('sendto_investor_by')->nullable();
            $table->unsignedBigInteger('sendto_management_by')->nullable();
            $table->dateTime('sendto_investor_date')->nullable();
            $table->dateTime('sendto_management_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            $table->dropColumn([
                'sendto_investor_by',
                'sendto_management_by',
                'sendto_investor_date',
                'sendto_management_date'
            ]);
        });
    }
};
