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
        Schema::create('investor_payout_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10)->default('fifo');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('paid_company_id')->nullable();
            $table->unsignedBigInteger('investor_id')->nullable();
            $table->decimal('allocated_amount', 15, 2);
            $table->json('payout_ids');
            $table->json('allocations');
            $table->json('distribution_ids')->nullable();
            $table->unsignedTinyInteger('month')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('cleared_date');
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['investor_id', 'company_id'], 'idx_alloc_investor_company');
            $table->index('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_payout_allocations');
    }
};
