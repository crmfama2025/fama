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
            $table->tinyInteger('partial_withdrawal_status')->default(0)->comment('0-No ,1-partial withdrawal requested, 2-partail withdrawal done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->dropColumn('partial_withdrawal_status');
        });
    }
};
