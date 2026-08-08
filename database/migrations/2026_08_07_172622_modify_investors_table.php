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
            $table->text('investor_address_arabic')->nullable()->change();
            $table->string('state_arabic')->nullable()->change();
            $table->string('city_arabic')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->text('investor_address_arabic')->nullable(false)->change();
            $table->string('state_arabic')->nullable(false)->change();
            $table->string('city_arabic')->nullable(false)->change();
        });
    }
};
