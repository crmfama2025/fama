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
            $table->json('applied_investments')->after('investor_agreement_type_id')->nullable();
            $table->integer('reference_mudarabah_id')->after('applied_investments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            $table->dropColumn('applied_investments');
            $table->dropColumn('reference_mudarabah_id');
        });
    }
};
