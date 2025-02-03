<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DnDetailAndOutstandingNotificationInternal extends Mailable
{
    use Queueable, SerializesModels;

    protected $dn_detail;

    /**
     * Create a new message instance.
     */
    public function __construct($dn_detail)
    {
        $this->dn_detail = $dn_detail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A NEW NOTIFICATION from SMS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.dn-detail-and-outstanding-notification-internal',
            with: [
                'data' => $this->dn_detail,
                'url' => 'https://sms.sanohindonesia.co.id',
            ]
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
