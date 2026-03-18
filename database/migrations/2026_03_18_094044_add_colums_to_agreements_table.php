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
        Schema::table('agreements', function (Blueprint $table) {
            //
            $table->tinyInteger('renewal_status')->default(0)->comment('0-new 1-renewal');
            // 0 = not renewal, 1 = renewed, 2 = renewal pending (you can define)

            $table->unsignedBigInteger('parent_agreement_id')->nullable()->after('renewal_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            //
            $table->dropColumn(['renewal_status', 'parent_agreement_id']);
        });
    }
};
