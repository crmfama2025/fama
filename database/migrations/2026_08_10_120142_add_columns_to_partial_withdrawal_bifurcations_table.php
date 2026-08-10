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
        Schema::table('partial_withdrawal_bifurcations', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by')->nullable()->after('added_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->softDeletes();
        });
    }
    /** * Reverse the migrations. */ public function down(): void
    {
        Schema::table('partial_withdrawal_bifurcations', function (Blueprint $table) {
            $table->dropColumn(['updated_by', 'deleted_by']);
            $table->dropSoftDeletes();
        });
    }
};
