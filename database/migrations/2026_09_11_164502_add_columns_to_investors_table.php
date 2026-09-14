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
            $table->enum('investor_prefix', ['Mr', 'Ms'])->nullable()->after('id');
            $table->string('investor_prefix_arabic')->nullable()->after('investor_prefix');
            $table->tinyInteger('investor_type')->default(0)->after('investor_prefix_arabic')->comment('o-major,1-minor'); // 0 = major, 1 = minor
            $table->tinyInteger('gender')->nullable()->after('investor_type')->comment('0-male,1-female'); // 1 = male, 2 = female
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn(['investor_prefix', 'investor_prefix_arabic', 'investor_type', 'gender']);
        });
    }
};
