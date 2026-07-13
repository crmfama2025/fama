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
        Schema::create('agreement_signature_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('investment_contract_documents')->cascadeOnDelete();
            $table->enum('signer_role', ['investor', 'company']);
            $table->enum('event_type', ['sent', 'signed']);
            $table->string('channel')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_signature_events');
    }
};
