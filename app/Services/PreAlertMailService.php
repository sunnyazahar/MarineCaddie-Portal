<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentFlight;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentOnBoardLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentTruckLeg;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PreAlertMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
        private CombinedPoPdfService $combinedPoPdfService,
        private EmlMessageBuilder $emlMessageBuilder,
    ) {}

    public function buildEml(
        Shipment $shipment,
        ?string $senderName = null,
        ?string $senderEmail = null,
        array $documentIds = [],
        array $excludeAttachments = []
    ): string {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, $documentIds, $excludeAttachments);

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            $mail['attachments']
        );
    }

    public function buildPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail);

        return [
            'to' => collect($mail['to'])->pluck('email')->filter()->implode(','),
            'cc' => collect($mail['cc'])->pluck('email')->filter()->implode(','),
            'bcc' => '',
            'from' => \App\Support\MailEnvelopeHelper::smtpMailboxAddress() ?: $mail['senderEmail'],
            'from_name' => $mail['senderName'],
            'subject' => $mail['subject'],
            'body' => preg_replace("/\r\n|\r|\n/", "\n", $mail['body']),
        ];
    }

    /**
     * @param  array{to?: string, cc?: string, bcc?: string, subject?: string, body?: string}  $overrides
     * @param  array<int, array{filename: string, content: string, mime?: string}>  $extraAttachments
     * @return array{to: array<int, string>, cc: array<int, string>, bcc: array<int, string>, subject: string}
     */
    public function send(
        Shipment $shipment,
        ?string $senderName = null,
        ?string $senderEmail = null,
        array $documentIds = [],
        array $excludeAttachments = [],
        array $overrides = [],
        array $extraAttachments = []
    ): array {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, $documentIds, $excludeAttachments);

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

        $attachments = array_merge($mail['attachments'], $extraAttachments);
        $normalizedBody = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? '';
        $htmlBody = nl2br(e($normalizedBody), false);
        $fromEmail = $mail['senderEmail'] ?: config('mail.from.address');
        $fromName = $mail['senderName'] ?: config('mail.from.name');

        Mail::html($htmlBody, function ($message) use ($to, $cc, $bcc, $subject, $attachments, $fromEmail, $fromName) {
            $message->to($to)->subject($subject);

            \App\Support\MailEnvelopeHelper::applySenderEnvelope($message, (string) $fromEmail, (string) $fromName);

            if ($cc !== []) {
                $message->cc($cc);
            }

            if ($bcc !== []) {
                $message->bcc($bcc);
            }

            foreach ($attachments as $attachment) {
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

    private function prepareMail(
        Shipment $shipment,
        ?string $senderName,
        ?string $senderEmail,
        array $documentIds = [],
        array $excludeAttachments = []
    ): array {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'preAlerts',
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

        $manifestData = $this->manifestPdfBuilder->build($shipment);
        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $consigneeParty = $this->resolveConsigneeContact($shipment, $partyNames);

        $sender = \App\Support\MailEnvelopeHelper::resolveShipmentSender(
            $senderName,
            $senderEmail,
            $shipment->creator?->name ?? 'Marinetrans',
            $shipment->creator?->email ?? config('mail.from.address', 'esea@marinetrans.net'),
        );

        return [
            'senderName' => $sender['name'],
            'senderEmail' => $sender['email'],
            'subject' => $this->buildSubject($shipment, $manifestData),
            'body' => $this->buildBody($shipment, $manifestData, $consigneeParty, $sender['name'], $sender['email']),
            'to' => $this->buildToAddresses($consigneeParty),
            'cc' => $this->buildCcAddresses($sender['email']),
            'attachments' => $this->buildAttachments($shipment, $manifestData, $documentIds, $excludeAttachments),
        ];
    }

    private function buildSubject(Shipment $shipment, array $manifestData): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $service = $shipment->service ?? '—';
        $departure = $manifestData['departurePort'] ?? '—';
        $destination = $this->buildDestinationLabel($shipment);

        return sprintf(
            'Pre-alert for Ref. %s / %s / %s / From %s to %s',
            $shipment->shipment_number,
            $vessel,
            $service,
            $departure,
            $destination
        );
    }

    private function buildBody(
        Shipment $shipment,
        array $manifestData,
        array $consigneeParty,
        string $senderName,
        string $senderEmail
    ): string {
        $consigneeName = $consigneeParty['name'] ?: ($shipment->consignee_att ?: 'Sir/Madam');
        $destination = $this->buildDestinationLabel($shipment);
        $service = $shipment->service ?? 'shipment';

        $lines = [
            'To ' . $consigneeName,
            '',
            'Please find attached pre-alert for ' . $service . ' to ' . $destination,
            '',
            '',
            'Shipment Details:',
            'Shipment Ref. ' . $shipment->shipment_number,
            'From: ' . ($manifestData['departurePort'] ?? '—'),
            'To: ' . $destination,
            'Vessel: ' . ($manifestData['vesselLine'] ?? '—'),
            'Total packages: ' . ($manifestData['totals']['packages'] ?? '—'),
            'Total weight: ' . ($manifestData['totals']['weight'] ?? '—') . ' kg',
            'Total CBM: ' . ($manifestData['totals']['cbm'] ?? '—'),
            '',
        ];

        $lines[] = 'Service Details:';
        $lines[] = '';
        $serviceDetailLines = $this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment);
        array_push($lines, ...array_slice($serviceDetailLines, 2));
        $lines[] = '';

        $lines[] = 'Please do the needful.';
        $lines[] = '';
        $lines[] = 'With kind regards,';
        $lines[] = '';
        $lines[] = $senderName;
        $lines[] = $senderEmail;
        $lines[] = 'Marincaddie';

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

    private function buildServiceDetailsSection(Shipment $shipment): string
    {
        $service = $shipment->service;
        if (!$service) {
            return '';
        }

        $lines = ['Service Details:', 'Service: ' . $service, ''];

        switch ($service) {
            case 'Airfreight':
                foreach ($shipment->flights as $index => $flight) {
                    $lines = array_merge($lines, $this->formatFlightLeg($flight, $index + 1));
                }
                break;
            case 'Sea freight':
                foreach ($shipment->seaLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatSeaLeg($leg, $index + 1));
                }
                break;
            case 'Truck':
                foreach ($shipment->truckLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatTruckLeg($leg, $index + 1));
                }
                break;
            case 'Courier':
                foreach ($shipment->courierLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatCourierLeg($leg, $index + 1));
                }
                break;
            case 'Release':
                foreach ($shipment->releaseLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatReleaseLeg($leg, $index + 1));
                }
                break;
            case 'Hand Carry':
                foreach ($shipment->handCarryLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatHandCarryLeg($leg, $index + 1));
                }
                break;
            case 'On-board delivery':
                foreach ($shipment->onBoardLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatOnBoardLeg($leg, $index + 1));
                }
                break;
        }

        return count($lines) > 3 ? implode("\r\n", $lines) : '';
    }

    /**
     * @return array<int, string>
     */
    private function formatFlightLeg(ShipmentFlight $flight, int $number): array
    {
        return $this->formatLegBlock('Flight ' . $number, [
            'Leg reference' => $flight->leg_reference,
            'Flight number' => $flight->flight_number,
            'Departure date' => $this->formatDate($flight->departure_date),
            'Arrival date' => $this->formatDate($flight->arrival_date),
            'Arrival time' => $flight->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatSeaLeg(ShipmentSeaLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Sea leg ' . $number, [
            'Bill of lading' => $leg->bill_of_lading,
            'Container number' => $leg->container_number,
            'Transport vessel IMO' => $leg->transport_vessel_imo,
            'Transport vessel name' => $leg->transport_vessel_name,
            'ETD' => $this->formatDate($leg->etd),
            'ETA' => $this->formatDate($leg->eta),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatTruckLeg(ShipmentTruckLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Truck ' . $number, [
            'CMR' => $leg->cmr,
            'Freight company' => $leg->freight_company,
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatCourierLeg(ShipmentCourierLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Courier ' . $number, [
            'Airway bill' => $leg->airway_bill,
            'Carrier' => $leg->carrier,
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatReleaseLeg(ShipmentReleaseLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Release ' . $number, [
            'Freight company' => $leg->freight_company,
            'Delivery date' => $this->formatDate($leg->delivery_date),
            'Delivery time' => $leg->delivery_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatHandCarryLeg(ShipmentHandCarryLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Hand carry ' . $number, [
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
            'Contact name' => $leg->contact_name,
            'Contact phone' => $leg->contact_phone,
            'Onboard hand carry' => $leg->onboard_hand_carry ? 'Yes' : 'No',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatOnBoardLeg(ShipmentOnBoardLeg $leg, int $number): array
    {
        return $this->formatLegBlock('On-board delivery ' . $number, [
            'Departure date' => $this->formatDate($leg->departure_date),
            'Delivery date' => $this->formatDate($leg->delivery_date),
            'Delivery time' => $leg->delivery_time,
        ]);
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<int, string>
     */
    private function formatLegBlock(string $title, array $fields): array
    {
        $lines = [$title . ':'];
        $hasValue = false;

        foreach ($fields as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $hasValue = true;
            $lines[] = '  ' . $label . ': ' . $value;
        }

        if (!$hasValue) {
            return [];
        }

        $lines[] = '';

        return $lines;
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format('d.m.Y');
        }

        return (string) $date;
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildToAddresses(array $consigneeParty): array
    {
        $addresses = [];

        if (!empty($consigneeParty['email'])) {
            $addresses[] = [
                'name' => $consigneeParty['name'] ?? '',
                'email' => $consigneeParty['email'],
            ];
        }

        return $addresses;
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildCcAddresses(string $senderEmail): array
    {
        $email = trim($senderEmail);
        if ($email === '') {
            return [];
        }

        return [[
            'email' => $email,
        ]];
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function buildAttachments(
        Shipment $shipment,
        array $manifestData,
        array $documentIds = [],
        array $excludeAttachments = []
    ): array {
        $attachments = [];
        $exclude = array_flip(array_map('strval', $excludeAttachments));

        if (! isset($exclude['pre_alert'])) {
            $latestPreAlert = $shipment->preAlerts->sortByDesc('version')->first();
            if ($latestPreAlert && is_file(\App\Support\PrivateDisk::path($latestPreAlert->file_path))) {
                $attachments[] = [
                    'filename' => 'pre-alert-' . $shipment->shipment_number . '-' . $latestPreAlert->version . '.pdf',
                    'content' => (string) file_get_contents(\App\Support\PrivateDisk::path($latestPreAlert->file_path)),
                    'mime' => 'application/pdf',
                ];
            }
        }

        if (! isset($exclude['combined_po'])) {
            $combinedPoAttachment = $this->combinedPoPdfService->buildAttachmentForShipment($shipment);
            if ($combinedPoAttachment !== null) {
                $attachments[] = $combinedPoAttachment;
            } else {
                foreach ($this->combinedPoPdfService->individualAttachmentsForShipment($shipment) as $poAttachment) {
                    $attachments[] = $poAttachment;
                }
            }
        }

        return array_merge($attachments, $this->buildUploadedDocumentAttachments($shipment, $documentIds));
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function buildUploadedDocumentAttachments(Shipment $shipment, array $documentIds): array
    {
        if ($documentIds === []) {
            return [];
        }

        $attachments = [];

        foreach ($shipment->documents as $document) {
            if (!in_array($document->id, $documentIds, true)) {
                continue;
            }

            $path = \App\Support\PrivateDisk::path($document->file_path);
            if (!is_file($path)) {
                continue;
            }

            $attachments[] = [
                'filename' => $document->file_name,
                'content' => (string) file_get_contents($path),
                'mime' => 'application/pdf',
            ];
        }

        return $attachments;
    }

    private function resolveConsigneeContact(Shipment $shipment, array $partyNames): array
    {
        $composite = $shipment->consignee;
        $result = [
            'name' => '',
            'email' => $shipment->consignee_email ?? '',
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
                $agent = \App\Models\Agent::find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['email'] = $agent->email ?: $result['email'];
                }
                break;
            case 'hub':
                $hub = \App\Models\Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['email'] = $hub->email ?: $result['email'];
                }
                break;
            case 'office':
                $office = \App\Models\Office::find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['email'] = $office->email ?: $result['email'];
                }
                break;
        }

        return $result;
    }
}
