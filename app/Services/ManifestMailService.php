<?php

namespace App\Services;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ManifestMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
        private CombinedPoPdfService $combinedPoPdfService,
        private EmlMessageBuilder $emlMessageBuilder,
        private ShipmentPdfCompanyFooter $companyFooter,
        private ShipmentManifestService $manifestService,
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
        $htmlBody = \App\Support\MailBodyHtml::fromComposeBody($body);
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
            'crrs.documents',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ]);

        if ($shipment->crrs->isEmpty()) {
            throw new RuntimeException('No stock items linked to this shipment.');
        }

        $manifestData = $this->manifestPdfBuilder->build($shipment);
        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureParty = $this->manifestPdfBuilder->resolvePartyContact($shipment->departure, $partyNames);

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
            'body' => $this->buildBody($shipment, $departureParty, $sender['name'], $sender['email']),
            'to' => $this->buildToAddresses($departureParty),
            'cc' => $this->buildCcAddresses($sender['email']),
            'attachments' => $this->buildAttachments($shipment, $manifestData, $documentIds, $excludeAttachments),
        ];
    }

    private function buildSubject(Shipment $shipment, array $manifestData): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $service = $shipment->service ?? '—';
        $departure = $this->manifestPdfBuilder->formatPortCity($shipment->departure_port_code);
        $destination = $this->manifestPdfBuilder->formatPortCity(
            $shipment->consignee_port_code,
            $shipment->consignee_city
        );

        $subject = sprintf(
            'Manifest for Shipment Ref. %s / %s / %s / From %s to %s',
            $shipment->shipment_number,
            $vessel,
            $service,
            $departure,
            $destination
        );

        if (! empty($manifestData['manifestRevisionLabel'])) {
            $subject = $manifestData['manifestRevisionLabel'] . ' / ' . $subject;
        }

        return $subject;
    }

    private function buildBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $departureName = $departureParty['name'] ?: 'Sir/Madam';
        $destination = $this->buildDestinationLabel($shipment);
        $service = $shipment->service ?? 'shipment';
        $deadline = $shipment->deadline_arrival?->format('d.m.Y') ?? '—';

        $lines = [
            'To ' . $departureName,
            '',
            'Please prepare ' . $service . ' to ' . $destination . ' and provide service details.',
            '',
            '',
            '**Service Details:**',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '**Deadline ' . $deadline.'**',
            '',
            '',
            '',
            'With kind regards,',
            $senderName,
            $senderEmail,
            'Marinecaddie',
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
        ])->filter()->implode(' / ') ?: 'destination';
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildToAddresses(array $party): array
    {
        $addresses = [];

        if (! empty($party['email'])) {
            $addresses[] = [
                'name' => $party['name'] ?? '',
                'email' => $party['email'],
            ];
        }

        return $addresses;
    }

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

        if (! isset($exclude['manifest'])) {
            $latestManifest = $shipment->latestManifest();
            if ($latestManifest) {
                try {
                    $path = $this->manifestService->ensureFileExists($latestManifest);
                    $attachments[] = [
                        'filename' => str_replace(' ', '-', $latestManifest->displayLabel())
                            . '-' . $shipment->shipment_number . '.pdf',
                        'content' => (string) file_get_contents($path),
                        'mime' => 'application/pdf',
                    ];
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Latest manifest attachment fallback to generated PDF: '.$e->getMessage(), [
                        'shipment_id' => $shipment->id,
                        'manifest_id' => $latestManifest->id,
                        'version' => $latestManifest->version,
                    ]);

                    $manifestPdf = $this->companyFooter->output(
                        Pdf::loadView('Shipment.pdf.manifest', $manifestData)->setPaper('a4', 'portrait'),
                        (string) ($manifestData['createdAt'] ?? '')
                    );

                    $attachments[] = [
                        'filename' => str_replace(' ', '-', $latestManifest->displayLabel())
                            . '-' . $shipment->shipment_number . '.pdf',
                        'content' => $manifestPdf,
                        'mime' => 'application/pdf',
                    ];
                }
            } else {
                $manifestPdf = $this->companyFooter->output(
                    Pdf::loadView('Shipment.pdf.manifest', $manifestData)->setPaper('a4', 'portrait'),
                    (string) ($manifestData['createdAt'] ?? '')
                );

                $attachments[] = [
                    'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
                    'content' => $manifestPdf,
                    'mime' => 'application/pdf',
                ];
            }
        }

        $attachments = array_merge($attachments, $this->buildUploadedDocumentAttachments($shipment, $documentIds));

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

        return $attachments;
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
}
