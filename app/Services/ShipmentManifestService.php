<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentManifest;
use App\Repositories\Contracts\ShipmentManifestRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShipmentManifestService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $pdfBuilder,
        private ShipmentPdfCompanyFooter $companyFooter,
        private ShipmentManifestRepositoryInterface $manifests,
    ) {}

    public function generate(Shipment $shipment): ?ShipmentManifest
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
        ]);

        if ($shipment->crrs->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($shipment) {
            $this->manifests->lockShipment($shipment->id);
            $version = $this->manifests->nextVersionForShipment($shipment->id);

            $label = ShipmentManifest::labelForVersion($version);
            $fileName = $label . '-' . $shipment->shipment_number . '.pdf';
            $relativePath = 'shipment_manifests/' . $shipment->id . '/' . str_replace(' ', '-', $fileName);
            $pdfContent = $this->buildPdfContent($shipment, $version);

            $this->storePdf($relativePath, $pdfContent);

            return $this->manifests->create([
                'shipment_id' => $shipment->id,
                'version' => $version,
                'file_name' => $label,
                'file_path' => $relativePath,
                'form_hash' => null,
            ]);
        });
    }

    public function ensureFileExists(ShipmentManifest $manifest): string
    {
        $manifest->loadMissing(
            'shipment.crrs.packages',
            'shipment.crrs.customerVessel.customer',
            'shipment.accountManager.office',
            'shipment.creator'
        );

        $path = \App\Support\PrivateDisk::path($manifest->file_path);

        if (is_file($path) && filesize($path) > 100) {
            return $path;
        }

        $pdfContent = $this->buildPdfContent($manifest->shipment, (int) $manifest->version);
        $this->storePdf($manifest->file_path, $pdfContent);

        if (!is_file($path) || filesize($path) < 100) {
            throw new RuntimeException('Manifest PDF could not be regenerated.');
        }

        return $path;
    }

    public function latestForShipment(Shipment $shipment): ?ShipmentManifest
    {
        return $shipment->manifests()->latest('version')->first();
    }

    private function buildPdfContent(Shipment $shipment, ?int $manifestVersion = null): string
    {
        $data = $this->pdfBuilder->build($shipment, $manifestVersion);

        $pdf = Pdf::loadView('Shipment.pdf.manifest', $data)
            ->setPaper('a4', 'portrait');

        return $this->companyFooter->output($pdf, (string) ($data['createdAt'] ?? ''));
    }

    private function storePdf(string $relativePath, string $pdfContent): void
    {
        $disk = \App\Support\PrivateDisk::disk();
        $directory = dirname($relativePath);
        $disk->makeDirectory($directory);
        $this->ensureDirectoryWritable($disk->path($directory));

        if ($disk->put($relativePath, $pdfContent) && $disk->exists($relativePath)) {
            $path = $disk->path($relativePath);
            if (is_file($path) && filesize($path) > 100) {
                @chmod($path, 0666);

                return;
            }
        }

        $absolutePath = $disk->path($relativePath);

        if (!is_dir(dirname($absolutePath))) {
            throw new RuntimeException('Manifest PDF storage directory could not be created.');
        }

        $bytesWritten = @file_put_contents($absolutePath, $pdfContent);

        if ($bytesWritten === false || !is_file($absolutePath) || filesize($absolutePath) < 100) {
            throw new RuntimeException('Manifest PDF could not be stored.');
        }

        @chmod($absolutePath, 0666);
    }

    private function ensureDirectoryWritable(string $directory): void
    {
        $storageRoot = \App\Support\PrivateDisk::path('');
        $current = $directory;

        while ($current !== dirname($current) && str_starts_with($current, $storageRoot)) {
            if (!is_dir($current) && !mkdir($current, 0777, true) && !is_dir($current)) {
                throw new RuntimeException('Manifest PDF storage directory could not be created.');
            }

            if (!is_writable($current)) {
                @chmod($current, 0777);
            }

            $current = dirname($current);
        }
    }
}
