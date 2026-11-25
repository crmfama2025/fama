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

        Schema::table('agreements', function (Blueprint $table) {
            $table->integer('contract_business_type')->comment('1-b2b, 2-b2c');
        });

        DB::statement("
            UPDATE agreements a
            INNER JOIN contract_units cu ON cu.contract_id = a.contract_id
            SET a.contract_business_type = cu.business_type
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn('contract_business_type');
        });
    }
};
