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
            //
            $table->unsignedBigInteger('indirect_company_id')->default(0);
            $table->unsignedBigInteger('indirect_contract_id')->default(0);
            $table->boolean('indirect_status')->default(0)->comment('0-direct 1-indirect');
            $table->integer('contract_status')
                ->comment('0-Pending, 1-Processing, 2-Approved, 3-Rejected, 4-Send for Approval, 5-Approval on Hold, 6-Sign Pending, 7- Signed, 8-Expired, 9-Terminated, 10-Dropped')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
            $table->dropColumn('indirect_company_id');
            $table->dropColumn('indirect_contract_id');
            $table->dropColumn('indirect_status');
            $table->integer('contract_status')
                ->comment('0-Pending, 1-Processing, 2-Approved, 3-Rejected, 4-Send for Approval, 5-Approval on Hold, 6-Sign Pending, 7- Signed, 8-Expired, 9-Terminated')
                ->change();
        });
    }
};
