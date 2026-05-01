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
        Schema::table('contract_units', function (Blueprint $table) {
            //
            $table->decimal('occupied_rent_per_month', 12, 2)->default(0);
            $table->decimal('total_payment_pending', 12, 2)->default(0);
            $table->decimal('total_payment_received', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_units', function (Blueprint $table) {
            //
            $table->dropColumn('occupied_rent_per_month');
            $table->dropColumn('total_payment_pending');
            $table->dropColumn('total_payment_received');
        });
    }
};
