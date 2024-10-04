<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PoResponseSupplier extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     protected $po_header;

    public function __construct($po_header)
    {
        //
        $this->po_header = $po_header;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Po Response Supplier',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.supplier-content-email',
            with: [
                'data' => $this->po_header,
                'url' => 'https://sms.sanohindonesia.co.id'
            ],
        );
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
