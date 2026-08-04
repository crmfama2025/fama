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
            $table->renameColumn('partial_withdrawal_status', 'withdrawal_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_ledgers', function (Blueprint $table) {
            //
            $table->renameColumn('withdrawal_status', 'partial_withdrawal_status');
        });
    }
};
