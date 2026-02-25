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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('trade_license_number')->nullable()->after('contact_person');
            $table->string('trade_license')->nullable()->after('trade_license_number');
            $table->string('trade_license_expiry')->nullable()->after('trade_license');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('trade_license_number');
            $table->dropColumn('trade_license');
            $table->dropColumn('trade_license_expiry');
        });
    }
};
