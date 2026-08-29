<?php

namespace App\Http\Controllers\Shipments;

use App\Services\CombinedPoPdfService;
use App\Services\ManifestMailService;
use App\Services\PreAlertMailService;
use App\Services\ShipmentChangeLogService;
use App\Services\ShipmentMailDispatchService;
use App\Services\ShipmentManifestService;
use App\Services\ShipmentPdfFingerprintService;
use App\Services\ShipmentPreAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentMailController extends BaseShipmentController
{
    public function manifestMailLauncher($id, ManifestMailService $manifestMailService, CombinedPoPdfService $combinedPoPdfService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['documents']);

        try {
            $manifestMailPreview = $manifestMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            abort(400, $e->getMessage());
        }

        $attachmentSources = [
            [
                'url' => route('shipments.combined-manifest-documents', $shipment->id),
                'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
            ],
        ];

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $attachmentSources[] = [
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return view('Shipment.manifest-mail-launcher', [
            'shipment' => $shipment,
            'manifestMailPreview' => $manifestMailPreview,
            'attachmentSources' => $attachmentSources,
            'emlUrl' => route('shipments.manifest-mail', $shipment->id),
            'emlFilename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function manifestMail(Request $request, $id, ManifestMailService $manifestMailService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.customerVessel.customer',
            'crrs.documents',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ]);

        try {
            $eml = $manifestMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email,
                $this->parseMailDocumentIds($request->query('document_ids')),
                $this->parseMailExcludeAttachments($request->query('exclude_attachments'))
            );
        } catch (\Throwable $e) {
            Log::error('Manifest mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'manifest-mail-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function prepareManifestMail(Request $request, $id, ManifestMailService $manifestMailService, CombinedPoPdfService $combinedPoPdfService, ShipmentManifestService $manifestService, ShipmentPdfFingerprintService $fingerprintService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['manifests', 'documents', 'crrs']);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->manifestGenerationRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for manifest email.',
                'errors' => $e->errors(),
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $manifestFingerprintBefore = $fingerprintService->manifestFingerprint($shipment);

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Manifest autosave before mail prepare failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before preparing manifest email.',
            ], 500);
        }

        $shipment = $shipment->fresh(array_merge(
            $fingerprintService->relations(),
            ['documents', 'manifests']
        ));

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item before sending a manifest.',
            ], 422);
        }

        try {
            $fingerprintService->prepareForFingerprint($shipment);
            if (
                $shipment->manifests->isEmpty()
                || $fingerprintService->manifestFingerprint($shipment) !== $manifestFingerprintBefore
            ) {
                $manifestService->generate($shipment);
                $shipment->load('manifests');
            }
        } catch (\Throwable $e) {
            Log::warning('Manifest generation before mail prepare failed: ' . $e->getMessage());
        }

        try {
            $preview = $manifestMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Manifest mail preview failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'attachments' => $this->manifestMailAttachmentSources($shipment, $combinedPoPdfService),
            'eml_url' => route('shipments.manifest-mail', $shipment->id),
            'eml_filename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
            'open_url' => route('shipments.manifest-mail.open', $shipment->id),
            'send_url' => route('shipments.manifest-mail.send', $shipment->id),
        ]);
    }

    public function sendManifestMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ]);

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'document_ids' => ['nullable'],
            'exclude_attachments' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'manifest',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            $this->parseMailDocumentIds($request->input('document_ids')),
            $this->parseMailExcludeAttachments($request->input('exclude_attachments')),
        );

        $latestManifest = $shipment->latestManifest();
        if ($latestManifest) {
            $latestManifest->markMailSent();
        }

        $description = $latestManifest
            ? $latestManifest->displayLabel() . ' · To ' . $validated['to']
            : 'To ' . $validated['to'];
        $changeLog = $changeLogService->log($shipment, 'Manifest email sent', $description);
        $changeLog->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Manifest email sent successfully.',
            'queued' => true,
            'manifest_mail_pending' => false,
            'change_log' => [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function manifestMailOpen(Request $request, $id)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);
        $documentIds = $this->parseMailDocumentIds($request->query('document_ids'));
        $excludeAttachments = $this->parseMailExcludeAttachments($request->query('exclude_attachments'));
        $emlUrl = route('shipments.manifest-mail', $shipment->id);
        $query = [];

        if ($documentIds !== []) {
            $query['document_ids'] = implode(',', $documentIds);
        }
        if ($excludeAttachments !== []) {
            $query['exclude_attachments'] = implode(',', $excludeAttachments);
        }
        if ($query !== []) {
            $emlUrl .= '?' . http_build_query($query);
        }

        return view('Shipment.manifest-mail-open', [
            'emlUrl' => $emlUrl,
            'filename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function preparePreAlertMail(Request $request, $id, PreAlertMailService $preAlertMailService, CombinedPoPdfService $combinedPoPdfService, ShipmentPreAlertService $preAlertService, \App\Services\ShipmentPdfFingerprintService $fingerprintService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['manifests', 'documents', 'crrs', 'preAlerts']);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->preAlertMailRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for pre-alert email.',
                'errors' => $e->errors(),
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $preAlertFingerprintBefore = $fingerprintService->preAlertFingerprint($shipment);

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }

                $service = $validated['service'] ?? $shipment->service;
                $this->syncFlights($shipment, $validated['flights'] ?? [], $service);
                $this->syncSeaLegs($shipment, $validated['sea_legs'] ?? [], $service);
                $this->syncTruckLegs($shipment, $validated['truck_legs'] ?? [], $service);
                $this->syncCourierLegs($shipment, $validated['courier_legs'] ?? [], $service);
                $this->syncReleaseLegs($shipment, $validated['release_legs'] ?? [], $service);
                $this->syncHandCarryLegs($shipment, $request->input('hand_carry_legs', []), $service);
                $this->syncOnBoardLegs($shipment, $validated['on_board_legs'] ?? [], $service);
            });
        } catch (\Throwable $e) {
            Log::error('Pre-alert autosave failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before preparing pre-alert email.',
            ], 500);
        }

        $shipment = $shipment->fresh([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
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
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item before sending a pre-alert.',
            ], 422);
        }

        if (!\App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Add service details before generating a pre-alert PDF.',
                'code' => 'missing_service_details',
            ], 422);
        }

        try {
            $fingerprintService->prepareForFingerprint($shipment);
            if (
                ! $shipment->preAlerts()->exists()
                || $fingerprintService->preAlertFingerprint($shipment) !== $preAlertFingerprintBefore
            ) {
                $preAlertService->generate($shipment);
                $shipment->load('preAlerts');
            }
        } catch (\Throwable $e) {
            Log::warning('Pre-alert generation before mail prepare failed: ' . $e->getMessage());
        }

        if (! $shipment->latestPreAlert()) {
            return response()->json([
                'success' => false,
                'message' => 'Could not prepare the latest pre-alert PDF for email.',
            ], 500);
        }

        try {
            $preview = $preAlertMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert mail preview failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'attachments' => $this->preAlertMailAttachmentSources($shipment, $combinedPoPdfService),
            'eml_url' => route('shipments.pre-alert-mail', $shipment->id),
            'eml_filename' => 'pre-alert-mail-' . $shipment->shipment_number . '.eml',
            'open_url' => route('shipments.pre-alert-mail.open', $shipment->id),
            'send_url' => route('shipments.pre-alert-mail.send', $shipment->id),
        ]);
    }

    public function sendPreAlertMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
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

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'document_ids' => ['nullable'],
            'exclude_attachments' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'pre_alert',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            $this->parseMailDocumentIds($request->input('document_ids')),
            $this->parseMailExcludeAttachments($request->input('exclude_attachments')),
        );

        $latestPreAlert = $shipment->latestPreAlert();
        if ($latestPreAlert) {
            $latestPreAlert->markMailSent();
        }

        $description = $latestPreAlert
            ? $latestPreAlert->displayLabel() . ' · To ' . $validated['to']
            : 'To ' . $validated['to'];
        $changeLog = $changeLogService->log($shipment, 'Pre-alert email sent', $description);
        $changeLog->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Pre-alert email sent successfully.',
            'queued' => true,
            'pre_alert_mail_pending' => false,
            'change_log' => [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function preAlertMailOpen(Request $request, $id)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);
        $documentIds = $this->parseMailDocumentIds($request->query('document_ids'));
        $excludeAttachments = $this->parseMailExcludeAttachments($request->query('exclude_attachments'));
        $emlUrl = route('shipments.pre-alert-mail', $shipment->id);
        $query = [];

        if ($documentIds !== []) {
            $query['document_ids'] = implode(',', $documentIds);
        }
        if ($excludeAttachments !== []) {
            $query['exclude_attachments'] = implode(',', $excludeAttachments);
        }
        if ($query !== []) {
            $emlUrl .= '?' . http_build_query($query);
        }

        return view('Shipment.manifest-mail-open', [
            'emlUrl' => $emlUrl,
            'filename' => 'pre-alert-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function preAlertMail(Request $request, $id, PreAlertMailService $preAlertMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        try {
            $eml = $preAlertMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email,
                $this->parseMailDocumentIds($request->query('document_ids')),
                $this->parseMailExcludeAttachments($request->query('exclude_attachments'))
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'pre-alert-mail-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

}
