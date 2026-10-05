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
            $table->unsignedBigInteger('nationality_id')->nullable()->change();
            $table->string('id_number')->nullable()->change();
            $table->unsignedBigInteger('country_of_residence')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->unsignedBigInteger('nationality_id')->nullable(false)->change();
            $table->string('id_number')->nullable(false)->change();
            $table->unsignedBigInteger('country_of_residence')->nullable(false)->change();
        });
    }
};
