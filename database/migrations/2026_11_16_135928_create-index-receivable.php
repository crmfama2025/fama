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
        // 1. Equality on terminate_status/deleted_at, range on payment_date,
        //    and ORDER BY payment_date, id served from the index (no filesort).
        $this->replaceIndex(
            'agreement_payment_details',
            'idx_apd_term_deleted_date',
            '`terminate_status`, `deleted_at`, `payment_date`'
        );

        // 2. Join lookup for the EXISTS on agreement_payments.
        $this->replaceIndex(
            'agreement_payment_details',
            'idx_apd_payment_id',
            '`agreement_payment_id`'
        );

        // 3. Covering index for the SUM(paid_amount) subquery and the unpaid filter.
        $this->replaceIndex(
            'cleared_receivables',
            'idx_cr_agreement_detail_deleted',
            '`agreement_payment_details_id`, `deleted_at`, `paid_amount`'
        );

        // 4. Indexes created by hand earlier under other names are now redundant.
        $this->dropIndex('agreement_payment_details', 'idx_apd_agreement_payment_id');
        $this->dropIndex('cleared_receivables', 'idx_cr_detail_deleted');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndex('agreement_payment_details', 'idx_apd_term_deleted_date');
        $this->dropIndex('agreement_payment_details', 'idx_apd_payment_id');
        $this->dropIndex('cleared_receivables', 'idx_cr_agreement_detail_deleted');
    }

    private function indexExists(string $table, string $index): bool
    {
        return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index])) > 0;
    }

    /** Create the index, or drop and re-add it in one statement if it already exists. */
    private function replaceIndex(string $table, string $index, string $columns): void
    {
        $drop = $this->indexExists($table, $index) ? "DROP INDEX `{$index}`, " : '';

        DB::statement(
            "ALTER TABLE `{$table}` {$drop}ADD INDEX `{$index}` ({$columns}), ALGORITHM=INPLACE, LOCK=NONE"
        );
    }

    private function dropIndex(string $table, string $index): void
    {
        if (! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement(
            "ALTER TABLE `{$table}` DROP INDEX `{$index}`, ALGORITHM=INPLACE, LOCK=NONE"
        );
    }
};
