<?php

namespace App\Services;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ConsolidatedProformaInvoicePdfService
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private InvoicingShipmentRowMapper $invoicingShipmentRowMapper,
        private ProformaInvoicePdfBuilder $pdfBuilder,
        private CombinedPoPdfMerger $pdfMerger,
    ) {}

    /**
     * @param  list<string>  $jobNumbers
     */
    public function streamMergedPdf(array $jobNumbers, array $invoiceTypeOptions): \Symfony\Component\HttpFoundation\Response
    {
        $shipments = $this->resolveConsolidationShipments($jobNumbers);
        $this->assertConsolidationGroup($shipments);

        $summaryData = $this->pdfBuilder->buildConsolidatedSummary($shipments, $invoiceTypeOptions);
        $pdfContents = [
            $this->renderProformaPdfWithoutPageNumbers($summaryData),
        ];

        foreach ($shipments as $shipment) {
            $data = $this->pdfBuilder->build($shipment, $invoiceTypeOptions);
            $pdfContents[] = $this->renderProformaPdfWithoutPageNumbers($data);
        }

        $merged = $this->pdfMerger->mergeContents($pdfContents, stampContinuousPageNumbers: true);

        $row = $this->invoicingShipmentRowMapper->mapCollection($shipments)->first();
        $poSlug = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($row['client_ref_no'] ?? 'consolidated'));
        $filename = 'consolidated-invoice-' . trim($poSlug, '-') . '.pdf';

        return response($merged, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * @param  list<string>  $jobNumbers
     * @return Collection<int, Shipment>
     */
    private function resolveConsolidationShipments(array $jobNumbers): Collection
    {
        $jobNumbers = array_values(array_unique(array_filter(array_map(
            static fn (string $value) => trim($value),
            $jobNumbers
        ))));

        if (count($jobNumbers) < 2) {
            throw ValidationException::withMessages([
                'job_no' => 'Select at least two invoices to consolidate.',
            ]);
        }

        $shipments = $this->shipmentRepository->findManyForInvoicingByNumbers($jobNumbers);

        if ($shipments->count() !== count($jobNumbers)) {
            throw ValidationException::withMessages([
                'job_no' => 'One or more selected shipments could not be found.',
            ]);
        }

        return $shipments->sortBy(function (Shipment $shipment) use ($jobNumbers) {
            return array_search($shipment->shipment_number, $jobNumbers, true);
        })->values();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function renderProformaPdfWithoutPageNumbers(array $data): string
    {
        $pdf = Pdf::loadView('Billing.pdf.proforma-invoice', $data)->setPaper('a4', 'portrait');
        $pdf->render();

        $output = $pdf->output();

        if (! is_string($output) || strlen($output) < 100) {
            throw new \RuntimeException('PDF could not be generated for consolidated invoice.');
        }

        return $output;
    }

    /**
     * @param  Collection<int, Shipment>  $shipments
     */
    private function assertConsolidationGroup(Collection $shipments): void
    {
        $rows = $this->invoicingShipmentRowMapper->mapCollection($shipments);

        $ungenerated = $rows->first(static fn (array $row) => empty($row['invoice_generated']));
        if ($ungenerated !== null) {
            abort(422, 'Please generate the invoice first for every selected shipment before consolidating.');
        }

        $poNumbers = $rows
            ->pluck('client_ref_no')
            ->map(static fn (string $value) => trim($value))
            ->unique()
            ->values();

        $partyNames = $rows
            ->pluck('party_name')
            ->map(static fn (string $value) => trim($value))
            ->unique()
            ->values();

        if ($poNumbers->count() !== 1 || $partyNames->count() !== 1) {
            throw ValidationException::withMessages([
                'job_no' => 'Consolidated invoice requires the same PO No. and Party Name on every selected row.',
            ]);
        }

        if ($partyNames->first() === '') {
            throw ValidationException::withMessages([
                'job_no' => 'Consolidated invoice requires a Party Name on every selected row.',
            ]);
        }
    }
}
