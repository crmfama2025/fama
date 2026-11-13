<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;


class LeadApprovedMail extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(public Lead $lead, public $approver = null) {}

    public function build()
    {
        return $this->subject('Lead Approved: ' . ($this->lead->company_name ?: $this->lead->contact_person_name))
            ->view('emails.lead-approved');
    }
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Lead Approved Mail',
    //     );
    // }


    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // public function attachments(): array
    // {
    //     return [];
    // }
}
