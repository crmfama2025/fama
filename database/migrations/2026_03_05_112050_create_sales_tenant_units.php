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
        Schema::create('sales_tenant_units', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_tenant_agreement_id');
            $table->integer('floor_number');

            $table->unsignedBigInteger('unit_type_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('contract_unit_details_id');
            $table->unsignedBigInteger('contract_subunit_details_id')->nullable();
            $table->text('subunit_ids')->nullable();

            $table->decimal('annual_rent', 12, 2);
            $table->decimal('monthly_rent', 12, 2);

            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('unit_type_id')
            //       ->references('id')
            //       ->on('unit_types')
            //       ->onDelete('cascade');
            $table->foreign('sales_tenant_agreement_id')->references('id')->on('sales_tenant_agreements')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_tenant_units');
    }
};
