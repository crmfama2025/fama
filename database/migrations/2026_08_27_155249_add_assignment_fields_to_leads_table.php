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
        Schema::table('leads', function (Blueprint $table) {

            $table->unsignedBigInteger('assigned_to')
                ->nullable()
                ->after('status');

            $table->unsignedBigInteger('assigned_by')
                ->nullable()
                ->after('assigned_to');

            $table->timestamp('assigned_at')
                ->nullable()
                ->after('assigned_by');

            // Current assigned salesperson
            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // User who assigned/reassigned the lead
            $table->foreign('assigned_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {

            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_by']);

            $table->dropColumn([
                'assigned_to',
                'assigned_by',
                'assigned_at',
            ]);
        });
    }
};
