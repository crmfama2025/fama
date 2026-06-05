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
            $table->string('invoice_path')->nullable()->default(null)->change();
            $table->string('invoice_file_name')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tenant_invoices', function (Blueprint $table) {
            $table->string('invoice_path')->nullable(false)->change();
            $table->string('invoice_file_name')->nullable(false)->change();
        });
    }
};
