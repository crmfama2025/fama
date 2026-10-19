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
        Schema::table('cleared_receivable_allocations', function (Blueprint $table) {
            //
            Schema::table('cleared_receivable_allocations', function (Blueprint $table) {
                $table->text('bank_reference')->nullable();
                $table->unsignedBigInteger('receipt_allocation_id')->nullable();
                $table->boolean('have_other_adjustments')->default(0)->comment('0-No, 1-Yes');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleared_receivable_allocations', function (Blueprint $table) {
            //
            $table->dropColumn([
                'bank_reference',
                'receipt_allocation_id',
                'have_other_adjustments',
            ]);
        });
    }
};
