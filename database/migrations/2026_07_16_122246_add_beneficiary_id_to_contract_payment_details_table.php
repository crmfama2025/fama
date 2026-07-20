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
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->nullable()->after('bank_id');
        });

        // Backfill existing rows from the parent contract's vendor_id
        DB::statement('
            UPDATE contract_payment_details cpd
            INNER JOIN contracts c ON c.id = cpd.contract_id
            SET cpd.beneficiary_id = c.vendor_id
            WHERE cpd.beneficiary_id IS NULL
        ');

        // Now that every row has a value, enforce NOT NULL
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->nullable(false)->change();
            $table->foreign('beneficiary_id')->references('id')->on('vendors');
        });
    }

    public function down(): void
    {
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->dropForeign(['beneficiary_id']);
            $table->dropColumn('beneficiary_id');
        });
    }
};
