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
            $table->boolean('has_excess_amount')->default(false)->comment('0-No,1-Yes');
            $table->decimal('excess_amount', 12, 2)->default(0);
            $table->decimal('actual_payment_amount', 12, 2)->nullable();
            $table->unsignedBigInteger('excess_added_by')->nullable();
            $table->timestamp('excess_added_at')->nullable();
            $table->timestamp('loss_discount_added_at')->nullable();
            $table->date('excess_date')->nullable();
            $table->text('excess_reason')->nullable();
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
                'has_excess_amount',
                'excess_amount',
                'actual_payment_amount',
                'excess_added_by',
                'excess_added_at',
                'loss_discount_added_at',
                'excess_date',
                'excess_reason'
            ]);
        });
    }
};
