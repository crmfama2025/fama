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
        Schema::table('agreement_documents', function (Blueprint $table) {
            //
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_documents', function (Blueprint $table) {
            //
            $table->dropColumn('issued_date');
            $table->dropColumn('expiry_date');
        });
    }
};
