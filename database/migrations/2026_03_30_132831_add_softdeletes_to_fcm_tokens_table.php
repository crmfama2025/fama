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
            $table->softDeletes()->after('updated_at'); // adds deleted_at column
        });
    }

    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
