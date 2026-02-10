<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('agreement_status_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('agreement_status_logs', function (Blueprint $table) {
            $table->dropColumn('deleted_by');
        });
    }
};
