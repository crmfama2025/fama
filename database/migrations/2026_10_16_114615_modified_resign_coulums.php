<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    private string $contractsTable = 'investment_contract_documents';

    public function up(): void
    {
        Schema::table('investor_document_resign_request_logs', function (Blueprint $t) {
            $t->string('signer_role', 20)->default('investor')->after('investment_contract_document_id');
        });


        // Raw SQL because ->renameColumn() needs doctrine/dbal on older Laravel
        DB::statement('ALTER TABLE investor_document_resign_request_logs CHANGE investor_signed_at signed_at TIMESTAMP NULL');

        Schema::table($this->contractsTable, function (Blueprint $t) {
            $t->string('last_resign_role', 20)->nullable();
            $t->integer('resign_requested', 11)->default(0);
            if (! Schema::hasColumn($this->contractsTable, 'company_signed_at')) {
                $t->timestamp('company_signed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
