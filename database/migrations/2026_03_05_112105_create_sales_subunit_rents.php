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
        Schema::create('sales_tenant_subunit_rents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_tenant_agreement_id')->nullable();
            $table->unsignedBigInteger('sales_tenant_unit_id');
            $table->unsignedBigInteger('contract_subunit_details_id');

            $table->decimal('rent_per_month', 12, 2)->nullable();

            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Optional Foreign Keys
            $table->foreign('sales_tenant_agreement_id')
                ->references('id')->on('sales_tenant_agreements')
                ->onDelete('cascade');

            // $table->foreign('sales_tenant_unit_id')
            //       ->references('id')->on('sales_tenant_units')
            //       ->onDelete('cascade');

            // $table->foreign('contract_subunit_details_id')
            //       ->references('id')->on('contract_subunit_details')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_tenant_subunit_rents');
    }
};
