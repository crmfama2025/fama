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
            $table->string('signed_pdf_path')->nullable();
            $table->dateTime('both_notified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            $table->dropColumn('signed_pdf_path');
            $table->dropColumn('both_notified_at');
        });
    }
};
