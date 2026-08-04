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
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->tinyInteger('profit_payout_status')->default(0)->comment('0-Not Paid ,1-partially paid, 2-Fully Paid');
            $table->decimal('withdrawal_month_profit', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->dropColumn([
                'withdrawal_month_profit',
                'profit_payout_status',
            ]);
        });
    }
};
