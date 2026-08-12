<?php

namespace App\Jobs;

use App\Models\Shipment;
use App\Models\ShipmentPreAlertReminderSend;
use App\Services\MailAttachmentStagingService;
use App\Services\ManifestMailService;
use App\Services\PreAlertMailService;
use App\Services\PreAlertReminderMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendShipmentMailJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array{to?: string, cc?: string, bcc?: string, subject?: string, body?: string}  $overrides
     * @param  array<int, array{path: string, filename: string, mime: string}>  $stagedAttachments
     */
    public function __construct(
        public int $shipmentId,
        public string $mailType,
        public ?string $senderName,
        public ?string $senderEmail,
        public array $overrides,
        public array $documentIds = [],
        public array $excludeAttachments = [],
        public array $stagedAttachments = [],
        public ?int $userId = null,
        public bool $recordReminderSend = false,
    ) {}

    public function handle(
        ManifestMailService $manifestMailService,
        PreAlertMailService $preAlertMailService,
        PreAlertReminderMailService $reminderMailService,
        MailAttachmentStagingService $staging,
    ): void {
        $shipment = $this->loadShipment();

        if (! $shipment) {
            Log::warning('SendShipmentMailJob: shipment not found.', ['shipment_id' => $this->shipmentId]);
            $staging->cleanup($this->stagedAttachments);

            return;
        }

        try {
            $extraAttachments = $staging->loadAsExtraAttachments($this->stagedAttachments);

            match ($this->mailType) {
                'manifest' => $manifestMailService->send(
                    $shipment,
                    $this->senderName,
                    $this->senderEmail,
                    $this->documentIds,
                    $this->excludeAttachments,
                    $this->overrides,
                    $extraAttachments,
                ),
                'pre_alert' => $preAlertMailService->send(
                    $shipment,
                    $this->senderName,
                    $this->senderEmail,
                    $this->documentIds,
                    $this->excludeAttachments,
                    $this->overrides,
                    $extraAttachments,
                ),
                'pre_alert_reminder', 'delivery_status', 'invoice_request' => $reminderMailService->send(
                    $shipment,
                    $this->senderName,
                    $this->senderEmail,
                    $this->reminderServiceMailType(),
                    $this->overrides,
                    $extraAttachments,
                ),
                default => throw new \InvalidArgumentException('Unknown mail type: '.$this->mailType),
            };

            if ($this->recordReminderSend && $this->userId) {
                ShipmentPreAlertReminderSend::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $this->userId,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('SendShipmentMailJob failed: '.$e->getMessage(), [
                'shipment_id' => $this->shipmentId,
                'mail_type' => $this->mailType,
                'exception' => $e,
            ]);
        } finally {
            $staging->cleanup($this->stagedAttachments);
        }
    }

    private function reminderServiceMailType(): string
    {
        return $this->mailType === 'pre_alert_reminder' ? 'pre_alert' : $this->mailType;
    }

    private function loadShipment(): ?Shipment
    {
        $query = Shipment::query();

        if ($this->mailType === 'manifest') {
            return $query->with([
                'crrs.packages',
                'crrs.documents',
                'crrs.customerVessel.customer',
                'accountManager.office',
                'creator',
                'documents',
                'manifests',
            ])->find($this->shipmentId);
        }

        if ($this->mailType === 'pre_alert') {
            return $query->with([
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
            ])->find($this->shipmentId);
        }

        return $query->with([
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
        ])->find($this->shipmentId);
    }
}
