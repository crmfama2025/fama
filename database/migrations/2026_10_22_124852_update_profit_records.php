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
        Schema::table('investment_profit_records', function (Blueprint $table) {
            $table->dropColumn('last_released_by');
        });

        Schema::table('investment_profit_records', function (Blueprint $table) {
            $table->unsignedBigInteger('last_released_by')->nullable()->after('last_released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_profit_records', function (Blueprint $table) {
            $table->dropColumn('last_released_by');
        });

        Schema::table('investment_profit_records', function (Blueprint $table) {
            $table->date('last_released_by')->nullable()->after('last_released_at');
        });
    }
};
