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
        Schema::create('investment_contract_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id');
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('investor_agreement_template_id');
            $table->unsignedBigInteger('investor_agreement_type_id');
            $table->tinyInteger('is_investor_signed')->default(0)->comment('0 = No, 1 = Yes');
            $table->dateTime('investor_signed_at')->nullable();
            $table->tinyInteger('is_company_signed')->default(0)->comment('0 = No, 1 = Yes');
            $table->dateTime('company_signed_at')->nullable();
            $table->integer('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->longText('contract_document_html');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_ledger_documents');
    }
};
