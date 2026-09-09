<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_follow_ups', function (Blueprint $table) {

            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            $table->foreignId('followed_up_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->tinyInteger('follow_up_type')
                ->nullable()
                ->comment('1=Phone Call, 2=WhatsApp, 3=Email, 4=Meeting, 5=SMS, 6=Other');

            $table->tinyInteger('follow_up_status')
                ->nullable()
                ->comment('2=Interested, 3=Call Back, 4=No Answer, 5=Not Interested, 6=Meeting Scheduled, 7=Proposal Sent, 8=Negotiation, 9=Converted, 10=Lost, 11=Others');

            $table->dateTime('follow_up_date')
                ->nullable();

            $table->dateTime('next_follow_up_date')
                ->nullable();

            $table->date('meeting_date')
                ->nullable();

            $table->time('meeting_time')
                ->nullable();

            $table->string('meeting_location')
                ->nullable();

            $table->text('not_interested_reason')
                ->nullable();

            $table->text('notes')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('lead_id');
            $table->index('followed_up_by');
            $table->index('follow_up_status');
            $table->index('follow_up_date');
            $table->index('next_follow_up_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};
