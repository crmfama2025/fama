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
        Schema::table('investments', function (Blueprint $table) {
            //
            $table->decimal('total_invested_amount', 12, 2)->default(0);
            $table->decimal('total_withdrawn_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            //
            $table->dropColumn('total_invested_amount');
            $table->dropColumn('total_withdrawn_amount');
        });
    }
};
