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
            $table->dropColumn('trn_number');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_invoices', function (Blueprint $table) {
            $table->string('trn_number')->nullable()->after('invoice_date');
        });
    }
};
