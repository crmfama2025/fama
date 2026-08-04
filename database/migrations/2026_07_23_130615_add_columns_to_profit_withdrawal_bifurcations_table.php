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
        Schema::table('partial_withdrawal_bifurcations', function (Blueprint $table) {
            //
            $table->decimal('withdrawal_month_profit', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('balance_to_pay', 12, 2)->default(0);
            $table->tinyInteger('payout_status')->default(0)->comment('0-Not Paid ,1-partially paid, 2-Fully Paid');
            $table->tinyInteger('profit_payout_status')->default(0)->comment('0-Not Paid ,1-partially paid, 2-Fully Paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partial_withdrawal_bifurcations', function (Blueprint $table) {
            //
            $table->dropColumn([
                'withdrawal_month_profit',
                'total_paid',
                'balance_to_pay',
                'payout_status',
                'profit_payout_status'
            ]);
        });
    }
};
