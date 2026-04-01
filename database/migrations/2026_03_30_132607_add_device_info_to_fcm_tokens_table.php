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
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->string('device_id')->nullable()->after('device_name');
            $table->string('user_agent')->nullable()->after('device_id');
        });
    }

    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropColumn('device_id');
            $table->dropColumn('user_agent');
        });
    }
};
