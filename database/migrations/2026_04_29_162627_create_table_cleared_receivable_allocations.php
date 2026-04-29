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
        Schema::create('cleared_receivable_allocations', function (Blueprint $table) {
            $table->id();
            $table->decimal('allocated_amount', 12, 2);

            $table->json('cleared_receivable_ids');

            $table->date('cleared_date')->nullable();

            $table->unsignedBigInteger('added_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleared_receivable_allocations');
    }
};
