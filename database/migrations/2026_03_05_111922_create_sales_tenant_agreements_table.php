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
        Schema::create('sales_tenant_agreements', function (Blueprint $table) {
            $table->id();

            $table->string('sales_agreement_code');

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('tenant_id');
            $table->integer('business_type')->default(1)->comment('1-B2B, 2-B2C');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('is_approved')->default(0)->comment('0-Pending, 1-Approved, 2-Rejected');
            $table->string('rejection_reason')->nullable();
            $table->integer('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('approved_comments')->nullable();

            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_tenant_agreements');
    }
};
