<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelledShipmentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name?: string, email: string}>  $ccAddresses
     */
    public function __construct(
        public string $mailSubject,
        public string $body,
        public ?Contact $accountManager,
        public string $toEmail,
        public string $toName = '',
        public array $ccAddresses = [],
    ) {}

    public function envelope(): Envelope
    {
        $fromAddress = \App\Support\MailEnvelopeHelper::smtpMailboxAddress()
            ?: (string) config('mail.from.address');
        $fromName = trim((string) ($this->accountManager?->name ?: config('mail.from.name')));
        $replyToEmail = trim((string) ($this->accountManager?->email ?: $fromAddress));

        $cc = [];
        foreach ($this->ccAddresses as $address) {
            $cc[] = new Address(
                $address['email'],
                trim((string) ($address['name'] ?? '')),
            );
        }

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            to: [new Address($this->toEmail, $this->toName)],
            cc: $cc,
            replyTo: [new Address($replyToEmail, $fromName)],
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.shipment.cancelled',
            text: 'emails.shipment.cancelled-text',
            with: [
                'body' => $this->body,
            ],
        );
    }
}
