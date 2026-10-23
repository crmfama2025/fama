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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('staff_per_room')->nullable()->after('total_staff');
            $table->decimal('budget', 12, 2)->nullable()->after('required_location');
            $table->date('moving_date')->nullable()->after('budget');
            $table->enum('lead_category', ['C1', 'C2', 'C3'])->nullable()->after('moving_date');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['staff_per_room', 'budget', 'moving_date', 'lead_category']);
        });
    }
};
