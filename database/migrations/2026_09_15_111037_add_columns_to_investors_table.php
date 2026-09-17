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
            $table->tinyInteger('investor_category')->default(0)->after('investor_type')->comment('0-individual,1-company');
            $table->unsignedInteger('place_of_incorporation_id')->nullable()->after('investor_category');
            $table->unsignedInteger('legal_type_id')->nullable()->after('place_of_incorporation_id');
            $table->tinyInteger('is_trade_license_uploaded')->default(0)->after('is_ref_com_cont_uploaded');
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn([
                'investor_category',
                'place_of_incorporation_id',
                'legal_type_id',
                'is_trade_license_uploaded'
            ]);
        });
    }
};
