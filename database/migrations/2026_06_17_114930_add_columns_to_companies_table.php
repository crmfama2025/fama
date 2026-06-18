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
        Schema::table('companies', function (Blueprint $table) {
            //
            $table->string('company_arabic_name')->nullable()->after('company_name');
            $table->string('trade_license_number')->nullable()->after('company_arabic_name');
            $table->string('registration_no')->nullable()->after('trade_license_number');
            $table->string('letter_head_path')->nullable()->after('registration_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
            $table->dropColumn('company_arabic_name');
            $table->dropColumn('trade_license_number');
            $table->dropColumn('registration_no');
            $table->dropColumn('letter_head_path');
        });
    }
};
