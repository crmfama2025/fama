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
        Schema::create('investment_company_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investment_id')->constrained('investments')->onDelete('restrict');
            $table->foreignId('company_id')->constrained('companies')->onDelete('restrict');
            $table->decimal('allocated_amount', 14, 2);
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
