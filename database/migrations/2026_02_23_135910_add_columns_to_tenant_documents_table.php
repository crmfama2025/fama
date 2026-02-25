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
        Schema::table('tenant_documents', function (Blueprint $table) {
            //
            $table->unsignedInteger('owner_index')->nullable()->after('tenant_id')->comment('Index of owner for multiple owners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_documents', function (Blueprint $table) {
            //
            $table->dropColumn('owner_index');
        });
    }
};
