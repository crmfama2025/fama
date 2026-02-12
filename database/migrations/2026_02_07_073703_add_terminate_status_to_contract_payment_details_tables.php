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
        Schema::table('contracts', function (Blueprint $table) {
            $table->date('terminated_date')->nullable();
            $table->text('terminated_reason')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->decimal('balance_amount', 8, 2)->nullable();
            $table->integer('balance_received')->default(0);

            $table->integer('contract_status')
                ->comment('0-Pending, 1-Processing, 2-Approved, 3-Rejected, 4-Send for Approval, 5-Approval on Hold, 6-Sign Pending, 7- Signed, 8-Expired, 9-Terminated')
                ->change();
        });

        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->integer('terminate_status')->default(0)->comment('0-Active, 1-Terminated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('terminated_date');
            $table->dropColumn('terminated_reason');
            $table->dropColumn('terminated_by');
            $table->dropColumn('balance_amount');
            $table->dropColumn('balance_received');
            $table->integer('contract_status')->comment('0-Pending, 1-Processing, 2-Approved, 3-Rejected, 4-Send for Approval, 5-Approval on Hold, 6-Sign Pending, 7- Signed, 8-Expired')->change();
        });

        Schema::table('contract_payment_details', function (Blueprint $table) {
            $table->dropColumn('terminate_status');
        });
    }
};
