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
        Schema::table('properties', function (Blueprint $t) {
            $t->unsignedBigInteger('ref_property_id')->nullable()->after('makani_verified_by');
            $t->index('ref_property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $t) {
            $t->dropIndex(['ref_property_id']);
            $t->dropColumn('ref_property_id');
        });
    }
};
