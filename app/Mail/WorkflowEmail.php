<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkflowEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailBody;

    public $emailSubject;

    public $fromEmail;

    public $fromName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject = 'Workflow Email',
        string $body = '',
        ?string $from = null,
        ?string $fromName = null
    ) {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->fromEmail = $from;
        $this->fromName = $fromName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            subject: $this->emailSubject,
        );

        if ($this->fromEmail) {
            $envelope->from(new Address($this->fromEmail, $this->fromName ?? ''));
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.workflow',
            with: [
                'body' => $this->emailBody,
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
