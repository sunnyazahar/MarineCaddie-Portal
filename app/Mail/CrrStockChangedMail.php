<?php

namespace App\Mail;

use App\Models\Crr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrrStockChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{title: string, description: ?string}>  $changes
     * @param  array<int, string>  $shipmentNumbers
     */
    public function __construct(
        public Crr $crr,
        public array $changes,
        public string $accountManagerName,
        public string $changedByName,
        public array $shipmentNumbers = [],
    ) {}

    public function envelope(): Envelope
    {
        $fromAddress = \App\Support\MailEnvelopeHelper::smtpMailboxAddress()
            ?: (string) config('mail.from.address');
        $fromName = (string) config('mail.from.name');

        $stockNumber = $this->crr->stock_number ?: ('#' . $this->crr->id);
        $vessel = trim((string) ($this->crr->vessel_name ?: ''));
        $shipmentPart = $this->shipmentNumbers !== []
            ? ' — Shipment ' . implode(', ', $this->shipmentNumbers)
            : '';

        $subject = 'Stock ' . $stockNumber . ' updated'
            . $shipmentPart
            . ($vessel !== '' ? ' — ' . $vessel : '');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: [new Address($fromAddress, $fromName)],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.stock.changed',
            text: 'emails.stock.changed-text',
            with: [
                'crr' => $this->crr,
                'changes' => $this->changes,
                'accountManagerName' => $this->accountManagerName,
                'changedByName' => $this->changedByName,
                'shipmentNumbers' => $this->shipmentNumbers,
                'shipmentNumbersLabel' => $this->shipmentNumbers !== []
                    ? implode(', ', $this->shipmentNumbers)
                    : null,
                'stockNumber' => $this->crr->stock_number ?: ('#' . $this->crr->id),
                'vesselName' => $this->crr->vessel_name ?: '—',
                'logoPath' => public_path('files/assets/images/marinecaddie-logo.png'),
                'stockUrl' => url('/stocks/edit/' . $this->crr->id),
            ],
        );
    }
}
