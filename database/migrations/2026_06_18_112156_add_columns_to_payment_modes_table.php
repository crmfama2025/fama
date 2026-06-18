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
        Schema::table('payment_modes', function (Blueprint $table) {
            //
            $table->string('payment_mode_arabic_name')->nullable()->after('payment_mode_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_modes', function (Blueprint $table) {
            //
            $table->dropColumn('payment_mode_arabic_name');
        });
    }
};
