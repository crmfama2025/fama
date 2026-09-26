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
        Schema::table('investment_additional_documents', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('investment_contract_document_id')->after('investment_document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_additional_documents', function (Blueprint $table) {
            //
            $table->dropColumn('investment_contract_document_id');
        });
    }
};
