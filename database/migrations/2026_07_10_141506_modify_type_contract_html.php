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
            $table->longText('contract_document_html')->nullable()->change();
            $table->string('investor_sign')->nullable();
            $table->string('company_sign')->nullable();
            // $table->string('unique_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            $table->longText('contract_document_html')->nullable()->change();
            $table->dropColumn('investor_sign');
            $table->dropColumn('company_sign');
            // $table->dropColumn('unique_key');
        });
    }
};
