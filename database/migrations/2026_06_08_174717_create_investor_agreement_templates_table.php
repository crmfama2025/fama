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
        Schema::create('investor_agreement_templates', function (Blueprint $table) {
            $table->id();
            $table->string('version_no');
            $table->integer('investor_agreement_type_id');
            $table->longText('template');
            $table->date('effective_from');
            $table->integer('is_active')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_agreement_templates');
    }
};
