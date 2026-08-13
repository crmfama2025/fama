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
        //
        Schema::table('agreement_signature_events', function (Blueprint $table) {
            $table->unsignedBigInteger('send_by')->nullable();
            // $table->unsignedBigInteger('sendto_management_by')->nullable();
            // $table->dateTime('sendto_investor_date')->nullable();
            // $table->dateTime('sendto_management_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_signature_events', function (Blueprint $table) {
            $table->dropColumn([
                'send_by',
                // 'sendto_management_by',
                // 'sendto_investor_date',
                // 'sendto_management_date',
            ]);
        });
    }
};
