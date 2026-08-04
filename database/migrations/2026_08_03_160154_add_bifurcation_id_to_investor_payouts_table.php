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
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->unsignedBigInteger('bifurcation_id')
                ->nullable()
                ->after('payout_reference_id');
        });
    }

    public function down(): void
    {
        Schema::table('investor_payouts', function (Blueprint $table) {
            $table->dropColumn('bifurcation_id');
        });
    }
};
