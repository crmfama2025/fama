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
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->boolean('is_discounted')->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->dateTime('discount_date')->nullable();
            $table->text('discount_reason')->nullable();
            $table->unsignedBigInteger('discounted_by')->nullable();
        });

        Schema::table('contract_payable_clears', function (Blueprint $table) {
            $table->boolean('is_discount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->dropColumn([
                'is_discounted', 'discount_amount', 'discount_date',
                'discount_reason', 'discounted_by',
            ]);
        });

        Schema::table('contract_payable_clears', function (Blueprint $table) {
            $table->dropColumn('is_discount');
        });
    }
};
