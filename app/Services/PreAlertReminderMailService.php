<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Shipment;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PreAlertReminderMailService
{
    public function __construct(
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private EmlMessageBuilder $emlMessageBuilder,
    ) {}

    public function buildEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail);

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            []
        );
    }

    public function buildPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail);

        return $this->previewFromMail($mail);
    }

    public function buildDeliveryStatusEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, 'delivery_status');

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            []
        );
    }

    public function buildDeliveryStatusPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        return $this->previewFromMail(
            $this->prepareMail($shipment, $senderName, $senderEmail, 'delivery_status')
        );
    }

    public function buildInvoiceRequestEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, 'invoice_request');

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            []
        );
    }

    public function buildInvoiceRequestPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        return $this->previewFromMail(
            $this->prepareMail($shipment, $senderName, $senderEmail, 'invoice_request')
        );
    }

    /**
     * Send a reminder email (any mail type) via SMTP, with optional field overrides.
     *
     * @param  array{to?: string, cc?: string, bcc?: string, subject?: string, body?: string}  $overrides
     * @return array{to: array<int,string>, cc: array<int,string>, bcc: array<int,string>, subject: string}
     */
    public function send(
        Shipment $shipment,
        ?string $senderName = null,
        ?string $senderEmail = null,
        string $mailType = 'delivery_status',
        array $overrides = [],
        array $extraAttachments = []
    ): array {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, $mailType);

        $to = $this->parseAddressList($overrides['to'] ?? null);
        if ($to === []) {
            $to = collect($mail['to'])->pluck('email')->filter()->values()->all();
        }
        $cc = array_key_exists('cc', $overrides)
            ? $this->parseAddressList($overrides['cc'])
            : collect($mail['cc'])->pluck('email')->filter()->values()->all();
        $bcc = $this->parseAddressList($overrides['bcc'] ?? null);
        $subject = trim((string) ($overrides['subject'] ?? $mail['subject']));
        $body = array_key_exists('body', $overrides)
            ? (string) $overrides['body']
            : (string) $mail['body'];

        if ($to === []) {
            throw new RuntimeException('No recipient email address provided.');
        }

        if ($subject === '') {
            throw new RuntimeException('Email subject is required.');
        }

        $normalizedBody = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? '';
        $htmlBody = nl2br(e($normalizedBody), false);
        $fromEmail = $mail['senderEmail'] ?: config('mail.from.address');
        $fromName = $mail['senderName'] ?: config('mail.from.name');

        Mail::html($htmlBody, function ($message) use ($to, $cc, $bcc, $subject, $fromEmail, $fromName, $extraAttachments) {
            $message->to($to)->subject($subject);

            \App\Support\MailEnvelopeHelper::applySenderEnvelope($message, (string) $fromEmail, (string) $fromName);

            if ($cc !== []) {
                $message->cc($cc);
            }

            if ($bcc !== []) {
                $message->bcc($bcc);
            }

            foreach ($extraAttachments as $attachment) {
                $message->attachData(
                    $attachment['content'],
                    $attachment['filename'],
                    ['mime' => $attachment['mime'] ?? 'application/octet-stream']
                );
            }
        });

        return [
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function parseAddressList(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $parts = is_array($value) ? $value : (preg_split('/[;,]+/', (string) $value) ?: []);

        return array_values(array_unique(array_filter(array_map(
            static fn ($email) => strtolower(trim((string) $email)),
            $parts
        ), static fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))));
    }

    private function previewFromMail(array $mail): array
    {
        return [
            'to' => collect($mail['to'])->pluck('email')->filter()->implode(','),
            'cc' => collect($mail['cc'])->pluck('email')->filter()->implode(','),
            'subject' => $mail['subject'],
            'body' => preg_replace("/\r\n|\r|\n/", "\n", $mail['body']),
            'from' => \App\Support\MailEnvelopeHelper::smtpMailboxAddress() ?: $mail['senderEmail'],
            'from_name' => $mail['senderName'],
        ];
    }

    private function prepareMail(
        Shipment $shipment,
        ?string $senderName,
        ?string $senderEmail,
        string $mailType = 'pre_alert'
    ): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        if ($shipment->crrs->isEmpty()) {
            throw new RuntimeException('No stock items linked to this shipment.');
        }

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureParty = $this->resolveDepartureContact($shipment, $partyNames);

        if (empty($departureParty['email'])) {
            throw new RuntimeException('No email address found for the departure party.');
        }

        $sender = \App\Support\MailEnvelopeHelper::resolveShipmentSender(
            $senderName,
            $senderEmail,
            $shipment->accountManager?->name ?? $shipment->creator?->name ?? 'Marinetrans',
            $shipment->accountManager?->email ?? $shipment->creator?->email ?? config('mail.from.address', 'esea@marinetrans.net'),
        );

        return [
            'senderName' => $sender['name'],
            'senderEmail' => $sender['email'],
            'subject' => match ($mailType) {
                'delivery_status' => $this->buildDeliveryStatusSubject($shipment),
                'invoice_request' => $this->buildInvoiceRequestSubject($shipment),
                default => $this->buildSubject($shipment),
            },
            'body' => match ($mailType) {
                'delivery_status' => $this->buildDeliveryStatusBody($shipment, $departureParty, $sender['name'], $sender['email']),
                'invoice_request' => $this->buildInvoiceRequestBody($shipment, $departureParty, $sender['name'], $sender['email']),
                default => $this->buildBody($shipment, $departureParty, $sender['name'], $sender['email']),
            },
            'to' => $this->buildToAddresses($departureParty),
            'cc' => $this->buildCcAddresses($shipment, $sender['email'], $departureParty['email']),
        ];
    }

    private function buildSubject(Shipment $shipment): string
    {
        $service = $shipment->service ?? '—';
        $departure = $this->manifestPdfBuilder->formatPortCity($shipment->departure_port_code);
        $destination = $this->manifestPdfBuilder->formatPortCity(
            $shipment->consignee_port_code,
            $shipment->consignee_city
        );

        return sprintf(
            'Reminder:Outgoing shipment details / %s / MC REF: %s / %s to %s',
            $service,
            $shipment->shipment_number,
            $departure,
            $destination
        );
    }

    private function buildBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $departureName = $departureParty['name'] ?: 'Sir/Madam';
        $service = $shipment->service ?? 'shipment';

        $lines = [
            'To ' . $departureName,
            '',
            'Please provide the details of ' . $service . ' to ' . $this->buildDestinationLabel($shipment),
            '',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildDeliveryStatusSubject(Shipment $shipment): string
    {
        $service = $shipment->service ?? '—';
        $departure = $this->manifestPdfBuilder->formatPortCity($shipment->departure_port_code);
        $destination = $this->manifestPdfBuilder->formatPortCity(
            $shipment->consignee_port_code,
            $shipment->consignee_city
        );

        return sprintf(
            'Delivery status request / %s/ MC REF: %s / %s to %s',
            $service,
            $shipment->shipment_number,
            $departure,
            $destination
        );
    }

    private function buildDeliveryStatusBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $service = $shipment->service ?? 'shipment';
        $lines = [
            'To ' . ($departureParty['name'] ?: 'Sir/Madam'),
            '',
            'Please provide the delivery status of ' . $service . ' to ' . $this->buildDeliveryDestinationLabel($shipment),
            '',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildInvoiceRequestSubject(Shipment $shipment): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $departurePort = $shipment->departure_port_code ?: '—';
        $destinationPort = $shipment->consignee_port_code ?: '—';

        return sprintf(
            'Invoice Request - %s/%s/%s / %s',
            $shipment->shipment_number,
            $vessel,
            $departurePort,
            $destinationPort
        );
    }

    private function buildInvoiceRequestBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $lines = [
            'Attn: ' . ($departureParty['name'] ?: 'Sir/Madam'),
            '',
            sprintf(
                'Please provide your Debit Note (Billing Invoice) for shipment Ref. %s / %s.',
                $shipment->shipment_number,
                $vessel
            ),
            '',
            'Shipment Details:',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildDestinationLabel(Shipment $shipment): string
    {
        $portAndCity = collect([
            $shipment->consignee_port_code,
            $shipment->consignee_city,
        ])->filter()->implode(' - ');

        return collect([
            $portAndCity,
            $shipment->location,
        ])->filter()->implode(' / ') ?: '—';
    }

    private function buildDeliveryDestinationLabel(Shipment $shipment): string
    {
        $portCityDistrict = collect([
            $shipment->consignee_port_code,
            $shipment->consignee_city,
            $shipment->consignee_district,
        ])->filter()->implode(' - ');

        return collect([
            $portCityDistrict,
            $shipment->location,
        ])->filter()->implode(' / ') ?: '—';
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildToAddresses(array $departureParty): array
    {
        if (empty($departureParty['email'])) {
            return [];
        }

        return [[
            'name' => $departureParty['name'] ?? '',
            'email' => $departureParty['email'],
        ]];
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildCcAddresses(Shipment $shipment, string $senderEmail, string $departureEmail): array
    {
        $addresses = [];

        if ($shipment->accountManager?->email
            && $shipment->accountManager->email !== $senderEmail
            && $shipment->accountManager->email !== $departureEmail) {
            $addresses[] = [
                'name' => $shipment->accountManager->name,
                'email' => $shipment->accountManager->email,
            ];
        }

        return $addresses;
    }

    private function resolveDepartureContact(Shipment $shipment, array $partyNames): array
    {
        $composite = $shipment->departure;
        $result = [
            'name' => '',
            'email' => '',
        ];

        if (!$composite) {
            return $result;
        }

        if (!str_contains($composite, ':')) {
            $result['name'] = $partyNames[$composite] ?? $composite;

            return $result;
        }

        [$type, $id] = explode(':', $composite, 2);
        $id = (int) $id;
        $result['name'] = $partyNames[$composite] ?? $composite;

        switch ($type) {
            case 'agent':
                $agent = Agent::find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['email'] = $agent->email ?? '';
                }
                break;
            case 'hub':
                $hub = Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['email'] = $hub->email ?? '';
                }
                break;
            case 'office':
                $office = Office::find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['email'] = $office->email ?? '';
                }
                break;
        }

        return $result;
    }
}
