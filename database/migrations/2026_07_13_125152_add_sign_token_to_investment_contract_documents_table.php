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
        // Schema::table('investment_contract_documents', function (Blueprint $table) {
        //     $table->renameColumn('unique_key', 'sign_token');
        // });

        Schema::table('investment_contract_documents', function (Blueprint $table) {
            $table->string('sign_token', 64)->nullable()->unique();
            $table->string('investor_sign_channel')->nullable()->after('investor_sign'); // 'whatsapp' | 'email'
            $table->string('company_sign_channel')->nullable()->after('company_sign'); // 'whatsapp' | 'email'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {
            // $table->string('unique_key')->nullable()->change();
            $table->dropColumn(['sign_token', 'investor_sign_channel', 'company_sign_channel']);
        });
    }
};
