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
        Schema::create('agreement_subunit_rent_bifurcation', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('agreement_id');
            $table->unsignedBigInteger('agreement_unit_id');
            $table->unsignedBigInteger('contract_unit_details_id');
            $table->unsignedBigInteger('contract_subunit_details_id');

            $table->decimal('rent_per_month', 12, 2)->default(0);
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->foreign('agreement_id')
                ->references('id')->on('agreements')
                ->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_subunit_rent_bifurcation');
    }
};
