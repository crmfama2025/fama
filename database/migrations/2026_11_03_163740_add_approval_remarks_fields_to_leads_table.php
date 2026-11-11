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
        Schema::table('leads', function (Blueprint $t) {
            $t->text('approval_remarks')->nullable()->after('rejection_reason');
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('approval_remarks');
        });
    }
};
