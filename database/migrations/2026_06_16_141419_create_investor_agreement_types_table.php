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
        Schema::create('investor_agreement_types', function (Blueprint $table) {
            $table->id();
            $table->string('investor_agreement_type');
            $table->integer('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_agreement_types');
    }
};
