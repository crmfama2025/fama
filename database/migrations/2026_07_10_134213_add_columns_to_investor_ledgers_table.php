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
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->date('requested_date')->nullable();
            $table->date('withdrawal_date')->nullable();
            $table->integer('duration_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->dropColumn('requested_date');
            $table->dropColumn('withdrawal_date');
            $table->dropColumn('duration_days');
        });
    }
};
