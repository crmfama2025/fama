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
        Schema::create('investor_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id');
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('investor_transaction_type_id');
            $table->decimal('transaction_amount', 12, 2)->default(0);
            $table->tinyInteger('is_credit')->default(0)->comment('0 = Debit, 1 = Credit');
            $table->dateTime('transaction_date')->nullable();
            $table->integer('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_ledger');
    }
};
