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
        Schema::table('cleared_receivables', function (Blueprint $table) {
            //
            Schema::table('cleared_receivables', function (Blueprint $table) {
                $table->text('bank_reference')->nullable();
                $table->unsignedBigInteger('receipt_allocation_id')->nullable();
                $table->boolean('have_other_adjustments')->default(0)->comment('0-No, 1-Yes');
                $table->integer('loss_or_discount')->default(0)->comment('0-None, 1-Loss, 2-Discount');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleared_receivables', function (Blueprint $table) {
            //
            Schema::table('cleared_receivables', function (Blueprint $table) {
                $table->dropColumn([
                    'bank_reference',
                    'receipt_allocation_id',
                    'have_other_adjustments',
                    'loss_or_discount',
                ]);
            });
        });
    }
};
