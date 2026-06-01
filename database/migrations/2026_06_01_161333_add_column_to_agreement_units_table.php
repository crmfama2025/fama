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
        Schema::table('agreement_units', function (Blueprint $table) {
            //
            $table->integer('is_rent_bifurcation_added')->default(0)->after('unit_revenue')->comment('0 - no, 1 - yes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_units', function (Blueprint $table) {
            //
            $table->dropColumn('is_rent_bifurcation_added');
        });
    }
};
