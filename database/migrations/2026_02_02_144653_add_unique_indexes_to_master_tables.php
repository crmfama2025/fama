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
        Schema::table('contract_types', function (Blueprint $table) {
            $table->unique('contract_type');
        });

        Schema::table('unit_types', function (Blueprint $table) {
            $table->unique('unit_type');
        });

        Schema::table('unit_statuses', function (Blueprint $table) {
            $table->unique('unit_status');
        });

        Schema::table('unit_size_units', function (Blueprint $table) {
            $table->unique('unit_size_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('contract_types', function (Blueprint $table) {
            $table->dropUnique(['contract_type']);
        });

        Schema::table('unit_types', function (Blueprint $table) {
            $table->dropUnique(['unit_type']);
        });

        Schema::table('unit_statuses', function (Blueprint $table) {
            $table->dropUnique(['unit_status']);
        });

        Schema::table('unit_size_units', function (Blueprint $table) {
            $table->dropUnique(['unit_size_unit']);
        });
    }
};
