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
        Schema::create('investment_profit_record_renewal_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investment_id')
                ->constrained('investments');

            $table->foreignId('investor_id')
                ->constrained('investors');

            $table->date('old_maturity_date');
            $table->date('new_maturity_date');

            $table->date('first_profit_date')->nullable();
            $table->date('last_profit_date')->nullable();

            $table->unsignedInteger('created_profit_records')->default(0);
            $table->unsignedInteger('updated_profit_records')->default(0);

            $table->string('renewal_type', 30);
            $table->timestamp('processed_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_profit_record_renewal_logs');
    }
};
