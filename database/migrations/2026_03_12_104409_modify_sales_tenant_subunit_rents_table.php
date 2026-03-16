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
        //
        Schema::table('sales_tenant_subunit_rents', function (Blueprint $table) {

            $table->foreign('sales_tenant_unit_id')
                ->references('id')
                ->on('sales_tenant_units')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('sales_tenant_subunit_rents', function (Blueprint $table) {

            $table->dropForeign(['sales_tenant_unit_id']);
        });
    }
};
