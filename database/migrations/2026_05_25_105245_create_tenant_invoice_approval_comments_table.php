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
        Schema::create('tenant_invoice_approval_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_invoice_id');
            $table->unsignedBigInteger('added_by');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('tenant_invoice_id')->references('id')->on('tenant_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_invoice_approval_comments');
    }
};
