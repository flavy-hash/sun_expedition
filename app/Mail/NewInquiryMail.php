<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent to the office for every booking request; replying goes straight to the traveller. */
class NewInquiryMail extends Mailable
{
    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        $trip = $this->inquiry->tour?->title ?? 'a custom trip';

        return new Envelope(
            subject: "New booking request — {$this->inquiry->name} · {$trip}",
            replyTo: [$this->inquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.new-inquiry');
    }
}
