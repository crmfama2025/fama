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
        Schema::table('investor_banks', function (Blueprint $table) {
            //
            $table->string('investor_beneficiary_arabic');
            $table->string('investor_bank_name_arabic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_banks', function (Blueprint $table) {
            //
            $table->dropColumn('investor_beneficiary_arabic');
            $table->dropColumn('investor_bank_name_arabic');
        });
    }
};
