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
        Schema::create('investor_guardian_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investor_id')
                ->constrained('investors')
                ->cascadeOnDelete();

            $table->string('guardian_name');
            $table->string('guardian_name_arabic');
            $table->string('guardian_mobile');
            $table->string('guardian_email');
            $table->string('emirates_id_number');
            $table->string('passport_number');

            $table->string('emirates_id_copy');
            $table->string('passport_copy');

            $table->unsignedBigInteger('added_by');
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('investor_guardian_details');
    }
};
