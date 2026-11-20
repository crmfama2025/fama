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
        Schema::table('properties', function (Blueprint $t) {
            $t->unsignedTinyInteger('source')->default(1);      // 1 = Property Master, 2 = Makani Check
            $t->string('makani_status')->nullable();
            $t->decimal('makani_latitude', 10, 7)->nullable();
            $t->decimal('makani_longitude', 10, 7)->nullable();
            $t->unsignedInteger('makani_distance_m')->nullable();
            $t->timestamp('makani_verified_at')->nullable();
            $t->unsignedBigInteger('makani_verified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $t) {
            $t->dropColumn([
                'source',
                'makani_status',
                'makani_latitude',
                'makani_longitude',
                'makani_distance_m',
                'makani_verified_at',
                'makani_verified_by'
            ]);
        });
    }
};
