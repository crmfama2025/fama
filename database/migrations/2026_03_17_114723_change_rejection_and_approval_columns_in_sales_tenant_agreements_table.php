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
        Schema::table('sales_tenant_agreements', function (Blueprint $table) {
            // Change rejection_reason and approved_comments to TEXT
            $table->text('rejection_reason')->nullable()->change();
            $table->text('approved_comments')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_tenant_agreements', function (Blueprint $table) {
            // Revert back to VARCHAR(255) if needed
            $table->string('rejection_reason')->nullable()->change();
            $table->string('approved_comments')->nullable()->change();
        });
    }
};
