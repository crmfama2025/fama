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
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();

            // Lead being assigned
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            // Salesperson assigned to the lead
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // User who assigned/reassigned the lead
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Assignment remarks/instructions
            $table->text('remarks')->nullable();

            // Date and time of assignment
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignments');
    }
};
