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
        Schema::table('investments', function (Blueprint $table) {
            //
            $table->text('company_bank_iban')->nullable()->after('invested_company_id');
            $table->text('company_bank_account_number')->nullable()->after('company_bank_iban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            //
            $table->dropColumn('company_bank_iban');
            $table->dropColumn('company_bank_account_number');
        });
    }
};
