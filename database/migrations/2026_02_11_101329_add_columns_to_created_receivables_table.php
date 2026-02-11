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
        Schema::table('cleared_receivables', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('paid_company_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crleared_receivables', function (Blueprint $table) {
            //
            $table->dropColumn('paid_company_id');
        });
    }
};
