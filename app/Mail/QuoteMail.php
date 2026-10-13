<?php

namespace App\Mail;

use App\Models\Quote;
use App\Support\QuotePdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class QuoteMail extends Mailable
{
    public function __construct(public Quote $quote, public bool $forOffice = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->forOffice
                ? "New instant quote {$this->quote->reference} — {$this->quote->customer_name}"
                : "Your Suni Expedition quote {$this->quote->reference}",
            replyTo: $this->forOffice ? [$this->quote->customer_email] : [config('quotes.office_email')],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.quote');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => QuotePdf::output($this->quote), $this->quote->pdfFilename())->withMime('application/pdf'),
        ];
    }
}
