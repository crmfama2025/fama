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
        Schema::table('tenant_invoices', function (Blueprint $table) {

            $table->unsignedBigInteger('tenant_id')->after('agreement_payment_detail_id');
            $table->unsignedBigInteger('contract_id')->after('agreement_id');
            $table->unsignedBigInteger('contract_unit_details_id')->after('contract_id');
            $table->unsignedBigInteger('agreement_unit_id')->after('contract_unit_details_id');

            $table->string('invoice_no')->after('agreement_unit_id');
            $table->date('invoice_date')->after('invoice_no');

            $table->string('trn_number')->nullable()->after('invoice_date');

            $table->date('month_start')->after('trn_number');
            $table->date('month_end')->after('month_start');

            $table->decimal('total_amount', 12, 2)->after('month_end');

            $table->tinyInteger('status')->default(0)->comment('0=pending,1=generated,2=approved,3=approval on hold')->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_invoices', function (Blueprint $table) {

            $table->dropColumn([
                'tenant_id',
                'contract_id',
                'contract_unit_details_id',
                'agreement_unit_id',
                'invoice_no',
                'invoice_date',
                'trn_number',
                'month_start',
                'month_end',
                'total_amount',
                'status'
            ]);
        });
    }
};
