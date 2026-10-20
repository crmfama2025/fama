<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Change this to the real table behind InvestmentContractDocuments
    private string $contractsTable = 'investment_contract_documents';

    public function up(): void
    {
        Schema::table($this->contractsTable, function (Blueprint $t) {
            if (! Schema::hasColumn($this->contractsTable, 'resign_count')) {
                $t->unsignedTinyInteger('resign_count')->default(0);
            }
            if (! Schema::hasColumn($this->contractsTable, 'last_resign_reason')) {
                $t->text('last_resign_reason')->nullable();
            }
            if (! Schema::hasColumn($this->contractsTable, 'last_resign_at')) {
                $t->timestamp('last_resign_at')->nullable();
            }
        });

        Schema::create('investor_document_resign_request_logs', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('investment_contract_document_id');

            $t->unsignedTinyInteger('attempt_no');
            $t->text('reason');
            $t->string('channel', 20);                       // whatsapp | email
            $t->unsignedBigInteger('requested_by');

            $t->longText('rejected_signed_html')->nullable();
            $t->longText('rejected_signature_image')->nullable();
            $t->timestamp('investor_signed_at')->nullable(); // when the rejected signature was made
            $t->timestamp('resigned_at')->nullable();        // set when the investor signs again

            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_document_resign_request_logs');

        Schema::table($this->contractsTable, function (Blueprint $t) {
            $cols = array_filter(
                ['resign_count', 'last_resign_reason', 'last_resign_at'],
                fn($c) => Schema::hasColumn($this->contractsTable, $c)
            );
            if ($cols) {
                $t->dropColumn($cols);
            }
        });
    }
};
