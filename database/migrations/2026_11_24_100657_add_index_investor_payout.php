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
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->index(
                ['investor_id', 'is_processed', 'original_profit_date'],
                'idx_payouts_investor_fifo'
            );
            $table->index('investment_id', 'idx_payouts_investment');
        });

        Schema::table('investments', function (Blueprint $table) {
            $table->index('company_id', 'idx_investments_company');
            $table->index('payout_batch_id', 'idx_investments_batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->dropIndex('idx_payouts_investor_fifo');
            $table->dropIndex('idx_payouts_investment');
        });

        Schema::table('investments', function (Blueprint $table) {
            $table->dropIndex('idx_investments_company');
            $table->dropIndex('idx_investments_batch');
        });
    }
};
