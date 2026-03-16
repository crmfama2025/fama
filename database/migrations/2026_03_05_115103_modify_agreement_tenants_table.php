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
        Schema::table('agreement_tenants', function (Blueprint $table) {
            // $table->unsignedBigInteger('sales_tenant_agreement_id')->nullable();
            $table->tinyInteger('tenant_source')->default(1)->comment('1=System, 2=Sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('agreement_tenants', function (Blueprint $table) {
            // $table->dropColumn('sales_tenant_agreement_id');
            $table->dropColumn('tenant_source');
        });
    }
};
