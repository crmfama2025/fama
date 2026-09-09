<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('lead_code');
            $table->string('company_name')->nullable();
            $table->string('contact_person_name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('lead_source');

            $table->unsignedInteger('total_staff')->nullable();

            $table->string('required_location')->nullable();
            $table->text('requirement');

            $table->tinyInteger('status')
                ->default(0)
                ->comment('
                0 = Pending,
                1 = Processing,
                2 = Interested,
                3 = Call Back,
                4 = No Answer,
                5 = Not Interested,
                6 = Meeting Scheduled,
                7 = Proposal Sent,
                8 = Negotiation,
                9 = Converted,
                10 = Lost,
                11 = Others
            ');


            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('created_by');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
