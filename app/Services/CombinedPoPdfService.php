<?php

namespace App\Services;

use App\Models\Shipment;
use App\Repositories\Contracts\CrrDocumentRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CombinedPoPdfService
{
    public function __construct(
        private CombinedPoPdfMerger $merger,
        private ShipmentStockSnapshotService $stockSnapshotService,
        private CrrDocumentRepositoryInterface $documents,
    ) {}

    public function documentsForShipment(Shipment $shipment): Collection
    {
        $crrIds = $this->stockSnapshotService->applyResolvedStockCrrs($shipment)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        return $this->documents->pdfDocumentsForShipment($shipment->shipment_number, $crrIds);
    }

    /**
     * @return array<int, string>
     */
    public function absolutePathsForShipment(Shipment $shipment): array
    {
        return $this->documentsForShipment($shipment)
            ->pluck('file_path')
            ->map(fn (string $filePath) => \App\Support\PrivateDisk::path($filePath))
            ->filter(fn (string $path) => is_file($path) && is_readable($path))
            ->values()
            ->all();
    }

    /**
     * @return array{filename: string, content: string, mime: string}|null
     */
    public function buildAttachmentForShipment(Shipment $shipment): ?array
    {
        $paths = $this->absolutePathsForShipment($shipment);

        if (empty($paths)) {
            return null;
        }

        $filename = 'combined-po-documents-' . $shipment->shipment_number . '.pdf';

        try {
            $content = count($paths) === 1
                ? (string) file_get_contents($paths[0])
                : $this->merger->merge($paths);
        } catch (\Throwable $e) {
            Log::warning('Combined PO PDF merge failed for shipment ' . $shipment->shipment_number . ': ' . $e->getMessage());

            return null;
        }

        if ($content === '') {
            return null;
        }

        return [
            'filename' => $filename,
            'content' => $content,
            'mime' => 'application/pdf',
        ];
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    public function individualAttachmentsForShipment(Shipment $shipment): array
    {
        $attachments = [];

        foreach ($this->documentsForShipment($shipment) as $document) {
            $path = \App\Support\PrivateDisk::path($document->file_path);
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }

            $attachments[] = [
                'filename' => $document->file_name ?: basename($document->file_path),
                'content' => (string) file_get_contents($path),
                'mime' => 'application/pdf',
            ];
        }

        return $attachments;
    }

    public function streamMergedPdf(Shipment $shipment, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $documents = $this->documentsForShipment($shipment);

        if ($documents->isEmpty()) {
            abort(404, 'No PO documents found for this shipment.');
        }

        $paths = $this->absolutePathsForShipment($shipment);

        if (empty($paths)) {
            abort(404, 'Document files are missing from storage.');
        }

        if (count($paths) === 1) {
            return response()->file($paths[0], [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        try {
            $mergedPdf = $this->merger->merge($paths);
        } catch (\Throwable $e) {
            Log::error('Combined PO PDF merge failed: ' . $e->getMessage());
            abort(500, 'Failed to generate merged document.');
        }

        return response($mergedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
