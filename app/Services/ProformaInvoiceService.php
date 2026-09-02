<?php

namespace App\Services;

use App\Models\Shipment;
use App\Repositories\Contracts\ProformaInvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ProformaInvoiceService
{
    public function __construct(
        private ProformaInvoiceRepositoryInterface $proformaInvoiceRepository,
        private ProformaNumberGenerator $proformaNumberGenerator,
    ) {}

    /**
     * @return array{proforma_no: string}
     */
    public function previewNumber(?string $proformaDateInput = null): array
    {
        $date = null;
        $parsed = $this->parseDate($proformaDateInput);

        if ($parsed !== null) {
            $date = Carbon::parse($parsed);
        }

        return [
            'proforma_no' => $this->proformaNumberGenerator->previewNext($date),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, message: string, proforma_no: string}
     */
    public function store(array $payload, int $userId): array
    {
        $shipmentId = (int) ($payload['shipment_id'] ?? 0);
        $shipment = Shipment::query()->find($shipmentId);

        if ($shipment === null) {
            throw ValidationException::withMessages([
                'shipment_id' => 'Shipment not found.',
            ]);
        }
        if ($shipment->status === 'Cancelled') {
            throw ValidationException::withMessages([
                'shipment_id' => 'Cancelled shipments cannot be invoiced.',
            ]);
        }

        $existing = $this->proformaInvoiceRepository->findByShipmentId($shipmentId);
        $isUpdate = $existing !== null;

        if ($isUpdate) {
            $numberData = [
                'financial_year_label' => $existing->financial_year_label,
                'sequence_no' => $existing->sequence_no,
                'proforma_no' => $existing->proforma_no,
            ];
        } else {
            $proformaDate = $this->parseDate($payload['proforma_date'] ?? null);
            $numberData = $this->proformaNumberGenerator->reserveNext(
                $proformaDate !== null ? Carbon::parse($proformaDate) : null
            );
        }

        $invoice = $this->proformaInvoiceRepository->saveForShipment(
            $shipmentId,
            $this->mapInvoiceAttributes($payload, $numberData, $userId, ! $isUpdate),
            $this->mapLineItems($payload['line_items'] ?? []),
        );

        return [
            'success' => true,
            'message' => $isUpdate
                ? 'Proforma invoice updated successfully.'
                : 'Proforma invoice saved successfully.',
            'proforma_no' => $invoice->proforma_no,
            'is_update' => $isUpdate,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{financial_year_label: string, sequence_no: int, proforma_no: string}  $numberData
     * @return array<string, mixed>
     */
    private function mapInvoiceAttributes(array $payload, array $numberData, int $userId, bool $isNew): array
    {
        $paidDue = $this->mapPaidAndDueAmounts($payload);

        $data = [
            'invoice_type' => $this->nullableString($payload['invoice_type'] ?? null),
            'shipper' => $this->nullableString($payload['shipper'] ?? null),
            'consignee' => $this->nullableString($payload['consignee'] ?? null),
            'billing_party' => $this->nullableString($payload['billing_party'] ?? null),
            'bill_to_pos' => filled($payload['bill_to_pos'] ?? null) ? (int) $payload['bill_to_pos'] : null,
            'airport_of_loading' => $this->nullableString($payload['airport_of_loading'] ?? null),
            'airport_of_destination' => $this->nullableString($payload['airport_of_destination'] ?? null),
            'loading_date' => $this->parseDate($payload['loading_date'] ?? null),
            'destination_date' => $this->parseDate($payload['destination_date'] ?? null),
            'due_date' => $this->parseDate($payload['due_date'] ?? null),
            'proforma_date' => $this->parseDate($payload['proforma_date'] ?? null),
            'client_ref_no' => $this->nullableString($payload['client_ref_no'] ?? null),
            'job_no' => $this->nullableString($payload['job_no'] ?? null),
            'job_date' => $this->parseDate($payload['job_date'] ?? null),
            'hawb_no' => $this->nullableString($payload['hawb_no'] ?? null),
            'hawb_date' => $this->parseDate($payload['hawb_date'] ?? null),
            'mawb_no' => $this->nullableString($payload['mawb_no'] ?? null),
            'mawb_date' => $this->parseDate($payload['mawb_date'] ?? null),
            'packages' => $this->nullableString($payload['packages'] ?? null),
            'chargeable_wt' => $this->nullableDecimal($payload['chargeable_wt'] ?? null),
            'gross_wt' => $this->nullableDecimal($payload['gross_wt'] ?? null),
            'commodity' => $this->nullableString($payload['commodity'] ?? null),
            'type_of_supply' => $this->nullableString($payload['type_of_supply'] ?? null),
            'sb_be_no' => $this->nullableString($payload['sb_be_no'] ?? null),
            'sb_be_date' => $this->parseDate($payload['sb_be_date'] ?? null),
            'flight_no' => $this->nullableString($payload['flight_no'] ?? null),
            'flight_date' => $this->parseDate($payload['flight_date'] ?? null),
            'vessel_name' => $this->nullableString($payload['vessel_name'] ?? null),
            'currency' => $this->nullableString($payload['currency'] ?? null),
            'einvoice_status' => $this->nullableString($payload['einvoice_status'] ?? null),
            'payment_type' => $this->nullableString($payload['payment_type'] ?? null),
            'paid_amount' => $paidDue['paid_amount'],
            'due_amount' => $paidDue['due_amount'],
            'created_by' => $userId,
        ];

        if ($isNew) {
            $data['proforma_no'] = $numberData['proforma_no'];
            $data['financial_year_label'] = $numberData['financial_year_label'];
            $data['sequence_no'] = $numberData['sequence_no'];
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{paid_amount: string, due_amount: string}
     */
    private function mapPaidAndDueAmounts(array $payload): array
    {
        $netPayable = $this->calculateNetPayableFromLineItems($payload['line_items'] ?? []);
        $paymentType = $this->nullableString($payload['payment_type'] ?? null);

        if ($paymentType === 'full_payment') {
            $paid = $netPayable;
        } else {
            $paid = (float) ($this->nullableDecimal($payload['paid_amount'] ?? null) ?? '0');
        }

        $due = max(0, $netPayable - $paid);

        return [
            'paid_amount' => number_format($paid, 2, '.', ''),
            'due_amount' => number_format($due, 2, '.', ''),
        ];
    }

    /**
     * @param  mixed  $lineItems
     */
    private function calculateNetPayableFromLineItems(mixed $lineItems): float
    {
        if (! is_array($lineItems)) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($lineItems as $item) {
            if (! is_array($item)) {
                continue;
            }

            $total += (float) ($this->nullableDecimal($item['non_taxable'] ?? null) ?? '0');
            $total += (float) ($this->nullableDecimal($item['taxable'] ?? null) ?? '0');
            $total += (float) ($this->nullableDecimal($item['igst_amt'] ?? null) ?? '0');
        }

        return $total;
    }

    /**
     * @param  mixed  $lineItems
     * @return list<array<string, mixed>>
     */
    private function mapLineItems(mixed $lineItems): array
    {
        if (! is_array($lineItems)) {
            return [];
        }

        return collect($lineItems)
            ->filter(fn ($item) => is_array($item))
            ->values()
            ->map(fn (array $item) => [
                'description' => $this->nullableString($item['description'] ?? null),
                'hsn' => $this->nullableString($item['hsn'] ?? null),
                'remarks' => $this->nullableString($item['remarks'] ?? null),
                'qty' => $this->nullableDecimal($item['qty'] ?? null),
                'qty_type' => $this->nullableString($item['qty_type'] ?? null),
                'rate' => $this->nullableDecimal($item['rate'] ?? null),
                'currency' => $this->nullableString($item['currency'] ?? null),
                'amount' => $this->nullableDecimal($item['amount'] ?? null),
                'exchange_rate' => $this->nullableDecimal($item['exchange_rate'] ?? null),
                'tax_type' => $this->nullableString($item['tax_type'] ?? null),
                'non_taxable' => $this->nullableDecimal($item['non_taxable'] ?? null),
                'taxable' => $this->nullableDecimal($item['taxable'] ?? null),
                'igst_pct' => $this->nullableDecimal($item['igst_pct'] ?? null),
                'igst_amt' => $this->nullableDecimal($item['igst_amt'] ?? null),
                'cgst_pct' => $this->nullableDecimal($item['cgst_pct'] ?? null),
                'cgst_amt' => $this->nullableDecimal($item['cgst_amt'] ?? null),
                'sgst_pct' => $this->nullableDecimal($item['sgst_pct'] ?? null),
                'sgst_amt' => $this->nullableDecimal($item['sgst_amt'] ?? null),
            ])
            ->all();
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function nullableDecimal(mixed $value): ?string
    {
        $value = trim(str_replace(',', '', (string) ($value ?? '')));

        if ($value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }

    private function parseDate(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return null;
        }

        foreach (['d.m.Y', 'd.m.y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->toDateString();
            } catch (\Throwable) {
            }
        }

        return null;
    }
}
