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
            $table->date('renewed_at')->nullable();
            $table->date('investor_novation_applied_at')->nullable();
            $table->unsignedBigInteger('investor_novation_applied_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn('renewed_at');
            $table->dropColumn('investor_novation_applied_at');
            $table->dropColumn('investor_novation_applied_by');
        });
    }
};
