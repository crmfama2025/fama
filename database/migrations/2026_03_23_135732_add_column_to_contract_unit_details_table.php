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
        Schema::table('contract_unit_details', function (Blueprint $table) {
            //
            $table->integer('is_sales_agreement_added')->default(0)->after('is_vacant')->comment('0-Not Added,1-Added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_unit_details', function (Blueprint $table) {
            //
            $table->dropColumn('is_sales_agreement_added');
        });
    }
};
