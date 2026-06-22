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
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->string('investor_name_arabic')->after('investor_name');
            $table->text('investor_address_arabic')->after('investor_address');
            $table->text('address_line2_arabic')->nullable();
            $table->string('city_arabic');
            $table->string('state_arabic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->dropColumn('investor_name_arabic');
            $table->dropColumn('investor_address_arabic');
            $table->dropColumn('address_line2_arabic');
            $table->dropColumn('city_arabic');
            $table->dropColumn('state_arabic');
        });
    }
};
