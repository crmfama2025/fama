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
        Schema::table('agreement_tenants', function (Blueprint $table) {
            //
            $table->string('tenant_code')->nullable()->after('id');
            $table->integer('tenant_type')->default(1)->comment('1-B2B, 2-B2C');
            $table->string('contact_person_department')->nullable();
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->unsignedBigInteger('payment_frequency_id')->nullable();
            $table->boolean('security_cheque_status')->default(0)->comment('0-No, 1-Yes');
            $table->integer('no_of_owners')->default(1);
        });
        // Modify existing agreement_id to nullable
        Schema::table('agreement_tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('agreement_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_tenants', function (Blueprint $table) {
            //
            $table->dropColumn('tenant_code');
            $table->dropColumn('tenant_type');
            $table->dropColumn('contact_person_department');
            $table->dropColumn('payment_mode_id');
            $table->dropColumn('payment_frequency_id');
            $table->dropColumn('security_cheque_status');
            $table->dropColumn('no_of_owners');
        });

        // Revert agreement_id to non-nullable
        Schema::table('agreement_tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('agreement_id')->nullable(false)->change();
        });
    }
};
