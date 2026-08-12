<?php

namespace App\Services;

use App\Jobs\SendShipmentMailJob;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentMailDispatchService
{
    public function __construct(
        private MailAttachmentStagingService $attachmentStaging,
    ) {}

    /**
     * @param  array{to: string, cc?: string, bcc?: string, subject: string, body?: string}  $overrides
     */
    public function dispatchAfterResponse(
        Request $request,
        Shipment $shipment,
        string $mailType,
        array $overrides,
        array $documentIds = [],
        array $excludeAttachments = [],
        bool $recordReminderSend = false,
    ): void {
        $stagedAttachments = $this->attachmentStaging->stageFromRequest($request);

        SendShipmentMailJob::dispatch(
            shipmentId: $shipment->id,
            mailType: $mailType,
            senderName: auth()->user()?->name,
            senderEmail: auth()->user()?->email,
            overrides: $overrides,
            documentIds: $documentIds,
            excludeAttachments: $excludeAttachments,
            stagedAttachments: $stagedAttachments,
            userId: auth()->id(),
            recordReminderSend: $recordReminderSend,
        )->afterResponse();
    }
}
