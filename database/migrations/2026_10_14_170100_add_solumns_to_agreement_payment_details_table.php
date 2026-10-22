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
        Schema::table('agreement_payment_details', function (Blueprint $table) {
            //
            Schema::table('agreement_payment_details', function (Blueprint $table) {
                $table->integer('loss_or_discount')->default(0)->comment('0-None, 1-Loss, 2-Discount');
                $table->decimal('loss_discount_amount', 12, 2)->nullable();
                $table->date('loss_discount_date')->nullable();
                $table->text('loss_discount_remarks')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_payment_details', function (Blueprint $table) {
            //
            $table->dropColumn([
                'loss_or_discount',
                'loss_discount_amount',
                'loss_discount_date',
                'loss_discount_remarks',
            ]);
        });
    }
};
