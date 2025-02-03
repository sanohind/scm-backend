<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PoResponseInternal extends Mailable
{
    use Queueable, SerializesModels;

    protected $po_header;

    /**
     * Create a new message instance.
     */
    public function __construct($po_header)
    {
        // Set initial value
        $this->po_header = $po_header;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'A NEW NOTIFICATION from SMS');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(markdown: 'mail.internal-content-email',
            with: [
                'data' => $this->po_header,
                'url' => 'https://sms.sanohindonesia.co.id',
            ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
