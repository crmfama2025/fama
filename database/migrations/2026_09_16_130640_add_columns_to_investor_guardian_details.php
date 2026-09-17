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
            $table->text('guardian_address')->after('guardian_mobile');
            $table->text('guardian_address_ar')->after('guardian_mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_guardian_details', function (Blueprint $table) {
            //
            $table->dropColumn('guardian_address');
            $table->dropColumn('guardian_address_ar');
        });
    }
};
