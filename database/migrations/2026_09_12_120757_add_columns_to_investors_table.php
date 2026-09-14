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
        Schema::table('investors', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_guardian_id')
                ->nullable()
                ->after('investor_type');

            $table->foreign('investor_guardian_id')
                ->references('id')
                ->on('investor_guardian_details')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropForeign(['investor_guardian_id']);
            $table->dropColumn('investor_guardian_id');
        });
    }
};
