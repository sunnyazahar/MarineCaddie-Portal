<?php

namespace App\Http\Controllers\Shipments;

use App\Models\ShipmentManifest;
use App\Models\ShipmentPreAlert;
use App\Services\CombinedPoPdfService;
use App\Services\ShipmentChangeLogService;
use App\Services\ShipmentManifestPdfBuilder;
use App\Services\ShipmentManifestService;
use App\Services\ShipmentPdfCompanyFooter;
use App\Services\ShipmentPdfFingerprintService;
use App\Services\ShipmentPreAlertService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentManifestController extends BaseShipmentController
{
    public function generateManifest(Request $request, $id, ShipmentManifestService $manifestService, CombinedPoPdfService $combinedPoPdfService, ShipmentPdfFingerprintService $fingerprintService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['manifests', 'documents', 'crrs']);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->manifestGenerationRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for manifest generation.',
                'errors' => $e->errors(),
            ], 422);
        }

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
            Log::error('Manifest autosave failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before generating manifest.',
            ], 500);
        }

        $shipment = $shipment->fresh(array_merge(
            $fingerprintService->relations(),
            ['documents', 'manifests']
        ));

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item to generate a manifest.',
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $manifestCreated = false;
        $createdManifest = null;

        try {
            $createdManifest = $manifestService->generate($shipment);
            $manifestCreated = $createdManifest !== null;
            if ($createdManifest) {
                $changeLogService->log(
                    $shipment,
                    $createdManifest->version > 1 ? 'Revision created' : 'Manifest generated',
                    $createdManifest->version > 1
                        ? 'Revision ' . $createdManifest->version
                        : $createdManifest->file_name . '.pdf'
                );
            }
        } catch (\Throwable $e) {
            Log::error('Manifest generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not revise manifest PDF. Please try again.',
            ], 500);
        }

        $manifests = $shipment->manifests()->orderBy('version')->get();
        $latestManifest = $manifests->last();
        $manifestRevision = ($latestManifest && $latestManifest->version >= 2)
            ? ($latestManifest->version - 1)
            : null;

        return response()->json([
            'success' => true,
            'created' => $manifestCreated,
            'message' => 'Shipping Instruction revised.',
            'manifests' => $manifests->map(fn (ShipmentManifest $manifest) => $this->manifestToArray($manifest)),
            'manifest_revision' => $manifestRevision,
            'document_count' => $this->shipmentDocumentCount($shipment, $combinedPoPdfService),
            'manifest_mail_pending' => $shipment->fresh('manifests')->needsManifestMailSend(),
        ]);
    }

    public function generatePreAlert(Request $request, $id, ShipmentPreAlertService $preAlertService, CombinedPoPdfService $combinedPoPdfService, ShipmentPdfFingerprintService $fingerprintService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['preAlerts', 'documents', 'crrs', 'manifests']);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->preAlertMailRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for pre-alert generation.',
                'errors' => $e->errors(),
            ], 422);
        }

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
                'message' => 'Could not save service details before generating pre-alert PDF.',
            ], 500);
        }

        $shipment = $shipment->fresh(array_merge(
            $fingerprintService->relations(),
            ['documents', 'manifests', 'preAlerts']
        ));

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item to generate a pre-alert.',
            ], 422);
        }

        if (!\App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Add service details before generating a pre-alert PDF.',
                'code' => 'missing_service_details',
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $preAlertCreated = false;
        $createdPreAlert = null;

        try {
            $createdPreAlert = $preAlertService->generate($shipment);
            $preAlertCreated = $createdPreAlert !== null;
            if ($createdPreAlert) {
                $changeLogService->log(
                    $shipment,
                    $createdPreAlert->version > 1 ? 'Pre-alert revision created' : 'Pre-alert generated',
                    $createdPreAlert->version > 1
                        ? 'Revision ' . $createdPreAlert->version
                        : $createdPreAlert->file_name . '.pdf'
                );
            }
        } catch (\Throwable $e) {
            Log::error('Pre-alert generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not revise pre-alert PDF. Please try again.',
            ], 500);
        }

        $preAlerts = $shipment->preAlerts()->orderBy('version')->get();

        return response()->json([
            'success' => true,
            'created' => $preAlertCreated,
            'message' => 'Pre Arrival Notification revised.',
            'pre_alerts' => $preAlerts->map(fn (ShipmentPreAlert $preAlert) => $this->preAlertToArray($preAlert)),
            'document_count' => $this->shipmentDocumentCount($shipment, $combinedPoPdfService),
            'pre_alert_mail_pending' => $shipment->fresh('preAlerts')->needsPreAlertMailSend(),
            'has_pre_alert_pdf' => $preAlerts->isNotEmpty(),
        ]);
    }

    public function showManifest($shipmentId, $manifestId, ShipmentManifestService $manifestService)
    {
        $manifest = $this->shipmentRepository->findManifestForShipmentOrFail((int) $shipmentId, (int) $manifestId, true);

        try {
            $path = $manifestService->ensureFileExists($manifest);
        } catch (\Throwable $e) {
            Log::error('Manifest file regeneration failed: ' . $e->getMessage());
            abort(404, 'Manifest file not found.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . str_replace(' ', '-', $manifest->displayLabel()) . '-' . $manifest->shipment->shipment_number . '.pdf"',
        ]);
    }

    public function deleteManifest($shipmentId, $manifestId, ShipmentChangeLogService $changeLogService)
    {
        $manifest = $this->shipmentRepository->findManifestForShipmentOrFail((int) $shipmentId, (int) $manifestId);
        $shipment = $manifest->shipment;
        $label = $manifest->displayLabel();

        \App\Support\PrivateDisk::delete($manifest->file_path);
        $manifest->delete();

        $changeLog = null;
        if ($shipment) {
            $changeLog = $changeLogService->log($shipment, 'Manifest removed', $label);
            $changeLog->load('user');
        }

        return response()->json([
            'success' => true,
            'change_log' => $changeLog ? [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ] : null,
            'manifest_mail_pending' => $shipment
                ? $shipment->fresh('manifests')->needsManifestMailSend()
                : false,
        ]);
    }

    public function showPreAlert($shipmentId, $preAlertId, ShipmentPreAlertService $preAlertService)
    {
        $preAlert = $this->shipmentRepository->findPreAlertForShipmentOrFail((int) $shipmentId, (int) $preAlertId, true);

        try {
            $path = $preAlertService->ensureFileExists($preAlert);
        } catch (\Throwable $e) {
            Log::error('Pre-alert file regeneration failed: ' . $e->getMessage());
            abort(404, 'Pre-alert file not found.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . str_replace(' ', '-', $preAlert->displayLabel()) . '-' . $preAlert->shipment->shipment_number . '.pdf"',
        ]);
    }

    public function deletePreAlert($shipmentId, $preAlertId, ShipmentChangeLogService $changeLogService)
    {
        $preAlert = $this->shipmentRepository->findPreAlertForShipmentOrFail((int) $shipmentId, (int) $preAlertId);
        $shipment = $preAlert->shipment;
        $label = $preAlert->displayLabel();

        \App\Support\PrivateDisk::delete($preAlert->file_path);
        $preAlert->delete();

        $changeLog = null;
        if ($shipment) {
            $changeLog = $changeLogService->log($shipment, 'Pre-alert removed', $label);
            $changeLog->load('user');
        }

        return response()->json([
            'success' => true,
            'change_log' => $changeLog ? [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ] : null,
            'pre_alert_mail_pending' => $shipment
                ? $shipment->fresh('preAlerts')->needsPreAlertMailSend()
                : false,
        ]);
    }

    public function combinedPoDocuments($id, CombinedPoPdfService $combinedPoPdfService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        return $combinedPoPdfService->streamMergedPdf(
            $shipment,
            'combined-po-documents-' . $shipment->shipment_number . '.pdf'
        );
    }

    public function combinedManifestDocuments($id, ShipmentManifestPdfBuilder $builder, ShipmentManifestService $manifestService, ShipmentPdfCompanyFooter $companyFooter)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'manifests',
        ]);

        if ($shipment->crrs->isEmpty()) {
            abort(404, 'No stock items linked to this shipment.');
        }

        $latestManifest = $manifestService->latestForShipment($shipment);
        if ($latestManifest) {
            $path = \App\Support\PrivateDisk::path($latestManifest->file_path);
            if (is_file($path)) {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . str_replace(' ', '-', $latestManifest->displayLabel()) . '-' . $shipment->shipment_number . '.pdf"',
                ]);
            }
        }

        $data = $builder->build($shipment);
        $pdfContent = $companyFooter->outputManifest(
            Pdf::loadView('Shipment.pdf.manifest', $data)->setPaper('a4', 'portrait')
        );

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Shipping-Instruction-' . $shipment->shipment_number . '.pdf"',
        ]);
    }

}
