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
        Schema::table('investment_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('investment_contract_document_id')->nullable()->after('id');
            $table->string('version_number')->nullable()->after('investment_contract_document_id');
            $table->unsignedBigInteger('investment_agreement_type_id')->nullable()->after('version_number');
            $table->unsignedBigInteger('company_id')->nullable()->after('investment_agreement_type_id');
            $table->date('document_date')->nullable()->after('company_id');
            $table->unsignedBigInteger('investment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_documents', function (Blueprint $table) {
            $table->dropColumn([
                'investment_contract_document_id',
                'version_number',
                'investment_agreement_type_id',
                'company_id',
                'document_date',
            ]);
            $table->unsignedBigInteger('investment_id')->nullable(false)->change();
        });
    }
};
