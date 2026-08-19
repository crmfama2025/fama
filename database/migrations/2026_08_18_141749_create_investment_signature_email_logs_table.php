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
        Schema::create('investment_signature_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_contract_document_id');
            $table->string('recipient_type'); // 'investor' | 'company'
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->longtext('template')->nullable(); // blade view used
            $table->string('status')->default('pending'); // pending | success | failed
            $table->text('response')->nullable(); // raw Brevo response or error message
            $table->unsignedTinyInteger('attempt_count')->default(1);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_signature_email_logs');
    }
};
