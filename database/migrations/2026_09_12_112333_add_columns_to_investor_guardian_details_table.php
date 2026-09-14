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
        Schema::table('investor_guardian_details', function (Blueprint $table) {
            //
            $table->date('eid_expiry_date')->nullable()->after('passport_copy');
            $table->date('passport_expiry_date')->nullable()->after('passport_copy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_guardian_details', function (Blueprint $table) {
            //
            $table->dropColumn('eid_expiry_date');
            $table->dropColumn('passport_expiry_date');
        });
    }
};
