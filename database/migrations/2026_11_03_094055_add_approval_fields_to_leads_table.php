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
            $t->tinyInteger('approval_status')->default(0)->after('status');
            $t->foreignId('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->foreignId('verifier_id')->nullable();         
            // $t->tinyInteger('verification_status')->default(0);
            $t->foreignId('verification_completed_by')->nullable();
            $t->timestamp('verification_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'rejection_reason',
                'verifier_id',
                // 'verification_status',
                'verification_completed_by',
                'verification_completed_at',
            ]);
        });
    }
};
