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
        Schema::table('nationalities', function (Blueprint $table) {
            //
            $table->string('nationality_arabic_name')->nullable()->after('nationality_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nationalities', function (Blueprint $table) {
            //
            $table->dropColumn('nationality_arabic_name');
        });
    }
};
