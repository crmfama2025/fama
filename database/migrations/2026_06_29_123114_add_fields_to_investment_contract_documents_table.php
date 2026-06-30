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
        Schema::table('investment_contract_documents', function (Blueprint $table) {

            $table->string('contract_file_path')->nullable();
            $table->string('additional_file_path')->nullable();
            $table->dateTime('generated_date')->nullable();
            $table->boolean('has_additional_doc')->default(false)->comment('0 = No, 1 = Yes');
            $table->tinyInteger('action_type')->default(0)->comment('0 = Upload, 1 = Generate');
            $table->integer('generated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('investment_contract_documents', function (Blueprint $table) {

            $table->dropColumn([
                'contract_file_path',
                'additional_file_path',
                'generated_date',
                'has_additional_doc',
                'action_type',
                'generated_by'
            ]);
        });
    }
};
