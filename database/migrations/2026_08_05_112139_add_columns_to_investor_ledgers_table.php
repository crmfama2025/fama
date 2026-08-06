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
            $table->timestamp('approved_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('approval_remarks')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('investor_ledgers', function (Blueprint $table) {
            $table->dropColumn(['approved_date', 'approved_by', 'approval_remarks']);
        });
    }
};
