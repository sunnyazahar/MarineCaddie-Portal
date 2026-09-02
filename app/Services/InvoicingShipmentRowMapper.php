<?php

namespace App\Services;

use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceLineItem;
use App\Models\Shipment;
use Illuminate\Support\Collection;

class InvoicingShipmentRowMapper
{
    /**
     * @param  Collection<int, Shipment>  $shipments
     * @return Collection<int, array<string, string>>
     */
    public function mapCollection(Collection $shipments): Collection
    {
        $partyNames = Shipment::batchResolvePartyNames($shipments);

        return $shipments->map(fn (Shipment $shipment) => $this->map($shipment, $partyNames));
    }

    /**
     * @param  array<string, string>  $partyNames
     * @return array<string, string>
     */
    public function map(Shipment $shipment, array $partyNames = []): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
            'proformaInvoice',
        ]);

        $packages = $shipment->crrs->flatMap(fn ($crr) => $crr->packages);
        $stockItemsTotalWeight = round((float) $packages->sum('weight'), 2);
        $grossWeight = $this->resolveGrossWeightKg($shipment, $stockItemsTotalWeight);
        $chargeableWeight = round($grossWeight * 1.08, 2);
        // Same total as shipment edit stock chips: sum of linked CRR customs_value.
        $netAmount = round((float) $shipment->crrs->sum('customs_value'), 2);
        $currency = (string) ($shipment->crrs->pluck('currency')->filter()->first() ?: '');
        $vessel = (string) ($shipment->crrs->pluck('vessel_name')->filter()->first() ?: '');
        $shipper = $shipment->partyDisplay($shipment->departure, $partyNames);
        $consignee = $shipment->partyDisplay($shipment->consignee, $partyNames);
        $partyName = collect([$shipper, $vessel])->filter()->implode(' / ');

        $createdAt = $shipment->created_at;
        $invoiceDateLabel = '-';
        $jobDateLabel = $createdAt?->format('d.m.Y') ?? '';
        $shipmentNumber = (string) $shipment->shipment_number;
        $invoiceNoLabel = '-';

        if ($shipment->proformaInvoice) {
            $saved = $shipment->proformaInvoice;
            $invoiceNoLabel = (string) $saved->proforma_no;
            $invoiceDateLabel = $saved->created_at?->format('d.m.Y') ?? '-';
            $jobDateLabel = $this->formatDate($saved->job_date) ?: $jobDateLabel;
        }

        $clientRefNo = (string) ($shipment->customer_reference ?: '');
        if ($shipment->proformaInvoice) {
            $clientRefNo = (string) ($shipment->proformaInvoice->client_ref_no ?: $clientRefNo);
        }

        return [
            'shipment_id' => (string) $shipment->id,
            'service_type' => (string) ($shipment->service ?: ''),
            'proforma_no' => $invoiceNoLabel,
            'job_no' => $shipmentNumber,
            'proforma_date' => $invoiceDateLabel,
            'job_date' => $jobDateLabel,
            'shipper_name' => $shipper,
            'consignee_name' => $consignee,
            'party_name' => $partyName,
            'port_of_loading' => (string) ($shipment->departure_port_code ?: ''),
            'port_of_discharge' => (string) ($shipment->consignee_port_code ?: ''),
            'client_ref_no' => $clientRefNo,
            'mbl_no' => $this->resolveTransportReference($shipment),
            'gross_wt' => $this->formatNumber($grossWeight),
            'chargeable_wt' => $this->formatNumber($chargeableWeight),
            'currency' => $currency,
            'gst_amount' => '',
            'net_invoice_amount' => number_format($netAmount, 2),
            'status' => $this->resolveInvoicingStatus($shipment),
        ];
    }

    /**
     * @param  array<string, string>  $partyNames
     * @return array<string, mixed>
     */
    public function mapDetail(Shipment $shipment, array $partyNames = []): array
    {
        if ($partyNames === []) {
            $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        }

        $row = $this->map($shipment, $partyNames);
        $shipment->loadMissing(['crrs.packages', 'crrs.customerVessel.customer.invoiceDetail', 'crrs.customerVessel.customer.invoiceAddress.country', 'flights', 'proformaInvoice.lineItems']);

        if ($shipment->proformaInvoice) {
            return $this->mapDetailFromSaved($shipment, $row, $partyNames);
        }

        $billingPartyRecipient = $this->resolveBillingPartyInvoiceRecipient($shipment);
        $billToPos = $this->resolveBillToPosFromInvoiceCountry($shipment);
        $packagesCount = (int) $shipment->crrs->sum(fn ($crr) => $crr->packages->count());
        if (filled($shipment->repacked_items)) {
            $packagesCount = (int) $shipment->repacked_items;
        }

        $flight = $shipment->flights->first();
        $vessel = (string) ($shipment->crrs->pluck('vessel_name')->filter()->first() ?: '');
        $commodity = (string) ($shipment->crrs->pluck('content')->filter()->first() ?: 'Shipspares');
        $loadingDate = $shipment->preferred_shipment_date?->format('d.m.Y') ?: $row['proforma_date'];
        $destinationDate = $shipment->deadline_arrival?->format('d.m.Y') ?: '';
        $flightDate = $flight?->departure_date
            ? \Illuminate\Support\Carbon::parse($flight->departure_date)->format('d.m.Y')
            : '';

        return array_merge($row, [
            'proforma_no' => '',
            'proforma_date' => now()->format('d.m.Y'),
            'job_no' => $row['job_no'],
            'einvoice_status' => '',
            'payment_type' => '',
            'paid_amount' => '',
            'due_amount' => '',
            'shipper_departure' => (string) ($shipment->departure ?: ''),
            'shipper_departure_display' => $row['shipper_name'],
            'consignee' => (string) ($shipment->consignee ?: ''),
            'consignee_display' => $row['consignee_name'],
            'billing_party' => $billingPartyRecipient,
            'billing_party_display' => $billingPartyRecipient,
            'bill_to_pos' => $billToPos['id'],
            'bill_to_pos_display' => $billToPos['display'],
            'invoice_type' => $this->guessInvoiceType($shipment->service),
            'airport_of_loading' => $row['port_of_loading'],
            'airport_of_destination' => $row['port_of_discharge'],
            'loading_date' => $loadingDate,
            'destination_date' => $destinationDate,
            'due_date' => $destinationDate,
            'hawb_no' => '',
            'hawb_date' => '',
            'mawb_no' => $row['mbl_no'],
            'mawb_date' => $row['job_date'],
            'packages' => (string) $packagesCount,
            'commodity' => $commodity,
            'type_of_supply' => (string) ($shipment->service ?: ''),
            'sb_be_no' => '',
            'sb_be_date' => '',
            'flight_no' => (string) ($flight?->flight_number ?: ''),
            'flight_date' => $flightDate,
            'vessel_name' => $vessel,
            'line_items' => [$this->blankDefaultLineItem()],
            'is_saved' => false,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function blankDefaultLineItem(): array
    {
        return [
            'description' => '',
            'hsn' => '',
            'remarks' => '',
            'qty' => '',
            'qty_type' => '',
            'rate' => '',
            'currency' => '',
            'amount' => '0.00',
            'exchange_rate' => '1',
            'tax_type' => 'T',
            'non_taxable' => '0.00',
            'taxable' => '0.00',
            'igst_pct' => '0',
            'igst_amt' => '0.00',
            'cgst_pct' => '0',
            'cgst_amt' => '0.00',
            'sgst_pct' => '0',
            'sgst_amt' => '0.00',
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<string, string>  $partyNames
     * @return array<string, mixed>
     */
    private function mapDetailFromSaved(Shipment $shipment, array $row, array $partyNames): array
    {
        /** @var ProformaInvoice $saved */
        $saved = $shipment->proformaInvoice;
        $billingPartyRecipient = $this->resolveBillingPartyInvoiceRecipient($shipment);
        $billToPos = $this->resolveBillToPosFromInvoiceCountry($shipment);

        return [
            'shipment_id' => (string) $shipment->id,
            'service_type' => $row['service_type'],
            'proforma_no' => $saved->proforma_no,
            'proforma_date' => $this->formatDate($saved->proforma_date) ?: now()->format('d.m.Y'),
            'job_no' => $saved->job_no ?: $shipment->shipment_number,
            'job_date' => $this->formatDate($saved->job_date),
            'shipper_name' => $row['shipper_name'],
            'consignee_name' => $row['consignee_name'],
            'party_name' => $row['party_name'],
            'port_of_loading' => $row['port_of_loading'],
            'port_of_discharge' => $row['port_of_discharge'],
            'client_ref_no' => $saved->client_ref_no ?: $row['client_ref_no'],
            'mbl_no' => $row['mbl_no'],
            'gross_wt' => $this->formatNumber((float) ($saved->gross_wt ?? 0)) ?: $row['gross_wt'],
            'chargeable_wt' => $this->formatNumber((float) ($saved->chargeable_wt ?? 0)) ?: $row['chargeable_wt'],
            'currency' => $saved->currency ?: $row['currency'],
            'gst_amount' => '',
            'net_invoice_amount' => $row['net_invoice_amount'],
            'status' => $this->resolveInvoicingStatus($shipment),
            'einvoice_status' => (string) ($saved->einvoice_status ?: ''),
            'payment_type' => (string) ($saved->payment_type ?: ''),
            'paid_amount' => $this->formatDecimal($saved->paid_amount),
            'due_amount' => $this->formatDecimal($saved->due_amount),
            'shipper_departure' => (string) ($saved->shipper ?: $shipment->departure ?: ''),
            'shipper_departure_display' => $shipment->partyDisplay($saved->shipper ?: $shipment->departure, $partyNames),
            'consignee' => (string) ($saved->consignee ?: $shipment->consignee ?: ''),
            'consignee_display' => $shipment->partyDisplay($saved->consignee ?: $shipment->consignee, $partyNames),
            'billing_party' => $billingPartyRecipient !== '' ? $billingPartyRecipient : (string) ($saved->billing_party ?: ''),
            'billing_party_display' => $billingPartyRecipient !== '' ? $billingPartyRecipient : (string) ($saved->billing_party ?: ''),
            'bill_to_pos' => $billToPos['id'] !== '' ? $billToPos['id'] : ($saved->bill_to_pos ? (string) $saved->bill_to_pos : ''),
            'bill_to_pos_display' => $billToPos['display'] !== '' ? $billToPos['display'] : $this->resolveBillToPosDisplay((string) ($saved->bill_to_pos ?: '')),
            'invoice_type' => (string) ($saved->invoice_type ?: $this->guessInvoiceType($shipment->service)),
            'airport_of_loading' => (string) ($saved->airport_of_loading ?: $row['port_of_loading']),
            'airport_of_destination' => (string) ($saved->airport_of_destination ?: $row['port_of_discharge']),
            'loading_date' => $this->formatDate($saved->loading_date),
            'destination_date' => $this->formatDate($saved->destination_date),
            'due_date' => $this->formatDate($saved->due_date),
            'hawb_no' => (string) ($saved->hawb_no ?: ''),
            'hawb_date' => $this->formatDate($saved->hawb_date),
            'mawb_no' => (string) ($saved->mawb_no ?: ''),
            'mawb_date' => $this->formatDate($saved->mawb_date),
            'packages' => (string) ($saved->packages ?: ''),
            'commodity' => (string) ($saved->commodity ?: ''),
            'type_of_supply' => (string) ($saved->type_of_supply ?: $shipment->service ?: ''),
            'sb_be_no' => (string) ($saved->sb_be_no ?: ''),
            'sb_be_date' => $this->formatDate($saved->sb_be_date),
            'flight_no' => (string) ($saved->flight_no ?: ''),
            'flight_date' => $this->formatDate($saved->flight_date),
            'vessel_name' => (string) ($saved->vessel_name ?: ''),
            'line_items' => $saved->lineItems
                ->map(fn (ProformaInvoiceLineItem $item) => $this->mapSavedLineItem($item))
                ->values()
                ->all(),
            'is_saved' => true,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function mapSavedLineItem(ProformaInvoiceLineItem $item): array
    {
        return [
            'description' => (string) ($item->description ?: ''),
            'hsn' => (string) ($item->hsn ?: ''),
            'remarks' => (string) ($item->remarks ?: ''),
            'qty' => $this->formatDecimal($item->qty, 0),
            'qty_type' => (string) ($item->qty_type ?: ''),
            'rate' => $this->formatDecimal($item->rate),
            'currency' => (string) ($item->currency ?: 'USD'),
            'amount' => $this->formatDecimal($item->amount),
            'exchange_rate' => $this->formatDecimal($item->exchange_rate, 0),
            'tax_type' => (string) ($item->tax_type ?: 'T'),
            'non_taxable' => $this->formatDecimal($item->non_taxable),
            'taxable' => $this->formatDecimal($item->taxable),
            'igst_pct' => $this->formatDecimal($item->igst_pct, 0),
            'igst_amt' => $this->formatDecimal($item->igst_amt),
            'cgst_pct' => $this->formatDecimal($item->cgst_pct, 0),
            'cgst_amt' => $this->formatDecimal($item->cgst_amt),
            'sgst_pct' => $this->formatDecimal($item->sgst_pct, 0),
            'sgst_amt' => $this->formatDecimal($item->sgst_amt),
        ];
    }

    private function resolveInvoicingStatus(Shipment $shipment): string
    {
        $invoice = $shipment->proformaInvoice;

        if ($invoice === null) {
            return 'Ready for billing';
        }

        if ($invoice->payment_type === 'partial_payment') {
            return 'Partially paid';
        }

        return 'Billed';
    }

    /**
     * Before invoice save: service-details repacked weight, else linked stock package total.
     */
    private function resolveGrossWeightKg(Shipment $shipment, float $stockItemsTotalWeight): float
    {
        $repackedWeight = $shipment->repacked_weight;

        if ($repackedWeight !== null && (float) $repackedWeight > 0) {
            return round((float) $repackedWeight, 2);
        }

        return round($stockItemsTotalWeight, 2);
    }

    /**
     * Linked stocks on a shipment share one customer (see PROJECT_STANDARDS §12d).
     */
    private function resolveStockCustomer(Shipment $shipment): ?\App\Models\Customer
    {
        $shipment->loadMissing([
            'crrs.customerVessel.customer.invoiceDetail',
            'crrs.customerVessel.customer.invoiceAddress.country',
        ]);

        return $shipment->crrs->first()?->customerVessel?->customer;
    }

    /**
     * @return array{id: string, display: string}
     */
    private function resolveBillToPosFromInvoiceCountry(Shipment $shipment): array
    {
        $country = $this->resolveStockCustomer($shipment)?->invoiceAddress?->country;

        if ($country === null) {
            return ['id' => '', 'display' => ''];
        }

        return [
            'id' => (string) $country->id,
            'display' => (string) $country->name,
        ];
    }

    private function resolveBillToPosDisplay(string $countryId): string
    {
        if ($countryId === '') {
            return '';
        }

        return (string) (\App\Models\Country::query()->find((int) $countryId)?->name ?? '');
    }

    private function resolveBillingPartyInvoiceRecipient(Shipment $shipment): string
    {
        return (string) ($this->resolveStockCustomer($shipment)?->invoiceDetail?->invoice_recipient_name ?? '');
    }

    private function formatDate(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return \Illuminate\Support\Carbon::parse($value)->format('d.m.Y');
    }

    private function formatDecimal(mixed $value, int $decimals = 2): string
    {
        if ($value === null || $value === '') {
            return $decimals === 0 ? '0' : '0.00';
        }

        return number_format((float) $value, $decimals, '.', '');
    }

    private function guessInvoiceType(?string $service): string
    {
        return match ($service) {
            'Airfreight' => '1',
            'Sea freight' => '3',
            'Courier' => '5',
            'On-board delivery' => '6',
            default => '7',
        };
    }

    private function resolveTransportReference(Shipment $shipment): string
    {
        $candidates = collect([
            $shipment->flights->first()?->leg_reference,
            $shipment->seaLegs->first()?->bill_of_lading,
            $shipment->truckLegs->first()?->cmr,
            $shipment->courierLegs->first()?->airway_bill,
            $shipment->releaseLegs->first()?->leg_reference,
            $shipment->handCarryLegs->first()?->leg_reference,
            $shipment->onBoardLegs->first()?->leg_reference,
        ]);

        return (string) ($candidates->first(fn ($value) => filled($value)) ?: '');
    }

    private function formatNumber(float $value): string
    {
        if ($value <= 0) {
            return '';
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') ?: '0';
    }
}
