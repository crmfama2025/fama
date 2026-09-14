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
        Schema::table('investor_guardian_details', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropColumn('investor_id');
            $table->string('investor_guardian_code')->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('investor_guardian_details', function (Blueprint $table) {
            $table->dropUnique(['investor_guardian_code']);
            $table->dropColumn('investor_guardian_code');
            $table->unsignedBigInteger('investor_id')->nullable();

            $table->foreignId('investor_id')
                ->constrained('investors')
                ->cascadeOnDelete();
        });
    }
};
