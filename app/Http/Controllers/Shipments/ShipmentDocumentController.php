<?php

namespace App\Http\Controllers\Shipments;

use App\Models\ShipmentDocument;
use App\Services\ShipmentChangeLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShipmentDocumentController extends BaseShipmentController
{
    public function uploadDocument(Request $request, $id, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('shipment_documents', 'private');

        $document = $this->shipmentRepository->createDocument([
            'shipment_id' => $shipment->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'Unspecified',
            'is_internal' => true,
        ]);

        $changeLogService->log($shipment, 'Document added', $document->file_name);

        return response()->json([
            'id' => $document->id,
            'file_name' => $document->file_name,
            'file_url' => $document->fileUrl(),
            'file_type' => $document->file_type,
            'is_internal' => (bool) $document->is_internal,
            'date' => $document->created_at->format('d.m.Y'),
            'type_options' => ShipmentDocument::fileTypeOptionsWithCustom(),
        ]);
    }

    public function deleteDocument($docId, ShipmentChangeLogService $changeLogService)
    {
        try {
            $document = $this->shipmentRepository->findDocumentOrFail((int) $docId);
            $shipment = $document->shipment;
            $fileName = $document->file_name;
            \App\Support\PrivateDisk::delete($document->file_path);
            $document->delete();

            if ($shipment) {
                $changeLogService->log($shipment, 'Document removed', $fileName);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Shipment document delete failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function showDocument($shipmentId, $docId)
    {
        $document = $this->shipmentRepository->findDocumentForShipmentOrFail((int) $shipmentId, (int) $docId);

        return \App\Support\PrivateDisk::downloadResponse((string) $document->file_path, (string) $document->file_name);
    }

    public function updateDocumentType(Request $request, $docId, ShipmentChangeLogService $changeLogService)
    {
        $document = $this->shipmentRepository->findDocumentOrFail((int) $docId);

        $validated = $request->validate([
            'file_type' => ['required', 'string', 'max:100'],
        ]);

        $fileType = trim($validated['file_type']);
        if ($fileType === '') {
            return response()->json([
                'success' => false,
                'error' => 'Document type is required.',
            ], 422);
        }

        $previousType = $document->file_type;
        $document->update(['file_type' => $fileType]);

        if ($previousType !== $fileType) {
            $changeLogService->log(
                $document->shipment,
                'Document type edited',
                $document->file_name . ': From ' . ($previousType ?: 'empty') . ' to ' . $fileType
            );
        }

        return response()->json([
            'success' => true,
            'file_type' => $document->file_type,
        ]);
    }

    public function updateDocumentInternal(Request $request, $docId, ShipmentChangeLogService $changeLogService)
    {
        $document = $this->shipmentRepository->findDocumentOrFail((int) $docId);

        $validated = $request->validate([
            'is_internal' => ['required', 'boolean'],
        ]);

        $previous = (bool) $document->is_internal;
        $document->update(['is_internal' => $validated['is_internal']]);

        if ($previous !== (bool) $document->is_internal && $document->shipment) {
            $changeLogService->log(
                $document->shipment,
                'Document internal flag edited',
                $document->file_name . ': From ' . ($previous ? 'Internal' : 'Not internal') . ' to ' . ($document->is_internal ? 'Internal' : 'Not internal')
            );
        }

        return response()->json([
            'success' => true,
            'is_internal' => (bool) $document->is_internal,
        ]);
    }

}
