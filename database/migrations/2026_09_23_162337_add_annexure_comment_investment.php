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
        Schema::table('investments', function (Blueprint $table) {
            $table->text('annexure_comment')->nullable();
            $table->text('annexure_comment_ar')->nullable();
            $table->text('common_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn('annexure_comment');
            $table->dropColumn('annexure_comment_ar');
            $table->dropColumn('common_comment');
        });
    }
};
