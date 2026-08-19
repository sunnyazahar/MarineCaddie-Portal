<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentPreAlert;
use App\Repositories\Contracts\ShipmentPreAlertRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShipmentPreAlertService
{
    public function __construct(
        private ShipmentPreAlertPdfBuilder $pdfBuilder,
        private ShipmentPdfCompanyFooter $companyFooter,
        private ShipmentPreAlertRepositoryInterface $preAlerts,
    ) {}

    public function generate(Shipment $shipment): ?ShipmentPreAlert
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office.country',
            'creator',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        if ($shipment->crrs->isEmpty() || !ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            return null;
        }

        return DB::transaction(function () use ($shipment) {
            $this->preAlerts->lockShipment($shipment->id);
            $version = $this->preAlerts->nextVersionForShipment($shipment->id);

            $label = ShipmentPreAlert::labelForVersion($version);
            $fileName = str_replace(' ', '-', $label) . '-' . $shipment->shipment_number . '-' . $version . '.pdf';
            $relativePath = 'shipment_pre_alerts/' . $shipment->id . '/' . $fileName;
            $pdfContent = $this->buildPdfContent($shipment);

            $this->storePdf($relativePath, $pdfContent);

            return $this->preAlerts->create([
                'shipment_id' => $shipment->id,
                'version' => $version,
                'file_name' => $label,
                'file_path' => $relativePath,
                'form_hash' => null,
            ]);
        });
    }

    public function ensureFileExists(ShipmentPreAlert $preAlert): string
    {
        $preAlert->loadMissing(
            'shipment.crrs.packages',
            'shipment.crrs.customerVessel.customer',
            'shipment.accountManager.office.country',
            'shipment.creator',
            'shipment.flights',
            'shipment.seaLegs',
            'shipment.truckLegs',
            'shipment.courierLegs',
            'shipment.releaseLegs',
            'shipment.handCarryLegs',
            'shipment.onBoardLegs',
        );

        $path = \App\Support\PrivateDisk::path($preAlert->file_path);

        if (is_file($path) && filesize($path) > 100) {
            return $path;
        }

        $pdfContent = $this->buildPdfContent($preAlert->shipment);
        $this->storePdf($preAlert->file_path, $pdfContent);

        if (!is_file($path) || filesize($path) < 100) {
            throw new RuntimeException('Pre-alert PDF could not be regenerated.');
        }

        return $path;
    }

    private function buildPdfContent(Shipment $shipment): string
    {
        $data = $this->pdfBuilder->build($shipment);
        $pdf = Pdf::loadView('Shipment.pdf.pre-alert', $data)
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
            throw new RuntimeException('Pre-alert PDF storage directory could not be created.');
        }

        $bytesWritten = @file_put_contents($absolutePath, $pdfContent);

        if ($bytesWritten === false || !is_file($absolutePath) || filesize($absolutePath) < 100) {
            throw new RuntimeException('Pre-alert PDF could not be stored.');
        }

        @chmod($absolutePath, 0666);
    }

    private function ensureDirectoryWritable(string $directory): void
    {
        $storageRoot = \App\Support\PrivateDisk::path('');
        $current = $directory;

        while ($current !== dirname($current) && str_starts_with($current, $storageRoot)) {
            if (!is_dir($current) && !mkdir($current, 0777, true) && !is_dir($current)) {
                throw new RuntimeException('Pre-alert PDF storage directory could not be created.');
            }

            if (!is_writable($current)) {
                @chmod($current, 0777);
            }

            $current = dirname($current);
        }
    }
}
