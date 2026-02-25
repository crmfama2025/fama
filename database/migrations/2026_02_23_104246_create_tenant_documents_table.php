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
        Schema::create('tenant_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->integer('document_type')->comment('1-Passport, 2-Emirates ID, 3-Trade License');
            $table->string('document_number');
            $table->string('original_document_path');
            $table->string('original_document_name');
            $table->integer('added_by');
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('agreement_tenants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_documents');
    }
};
