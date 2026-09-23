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
        Schema::create('investment_renewal_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->json('before_values');
            $table->json('after_values');
            $table->json('changes');
            $table->json('schedule_changes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_renewal_edit_logs');
    }
};
