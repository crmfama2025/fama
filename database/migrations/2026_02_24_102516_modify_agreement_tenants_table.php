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
        Schema::table('agreement_tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('no_of_owners')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('agreement_tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('no_of_owners')->default(1)->change();
        });
    }
};
