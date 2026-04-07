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
        Schema::table('tenant_documents', function (Blueprint $table) {
            $table->string('original_document_name')->nullable()->change();
            $table->string('original_document_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tenant_documents', function (Blueprint $table) {
            $table->string('original_document_name')->nullable(false)->change();
            $table->string('original_document_path')->nullable(false)->change();
        });
    }
};
