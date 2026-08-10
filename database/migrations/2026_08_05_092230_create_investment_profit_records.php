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
        Schema::create('investment_profit_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('investment_id');
            $table->date('profit_release_month');
            $table->decimal('profit_amount', 15, 2);
            $table->integer('has_profit_amount')->default(1)->comment('0 = dont_have_pofit, 1 = have_profit');
            $table->enum('release_status', ['pending', 'released', 'partially_released', 'hold', 'cancelled'])
                ->default('pending');
            $table->decimal('released_total_amount', 15, 2);
            $table->date('last_released_at')->nullable();
            $table->date('last_released_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_profit_records');
    }
};
