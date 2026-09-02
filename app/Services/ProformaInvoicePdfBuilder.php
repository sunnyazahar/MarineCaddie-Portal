<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Shipment;
use App\Support\CompanyAddress;
use App\Support\ProformaInvoiceBankDetails;

class ProformaInvoicePdfBuilder
{
    public function __construct(
        private InvoicingShipmentRowMapper $invoicingShipmentRowMapper,
    ) {}

    /**
     * @param  array<string, string>  $invoiceTypeOptions
     * @return array<string, mixed>
     */
    public function build(Shipment $shipment, array $invoiceTypeOptions = []): array
    {
        $shipment->loadMissing(['proformaInvoice.lineItems']);
        $invoice = $this->invoicingShipmentRowMapper->mapDetail($shipment);
        $lineItems = is_array($invoice['line_items'] ?? null) ? $invoice['line_items'] : [];

        $invoiceTo = $this->resolveInvoiceToFromBillingParty($shipment, $invoice);

        $totals = $this->calculateTotals($lineItems);
        $proformaNo = trim((string) ($invoice['proforma_no'] ?? ''));

        return [
            'invoice' => $invoice,
            'invoice_type_label' => $invoiceTypeOptions[(string) ($invoice['invoice_type'] ?? '')] ?? '',
            'invoice_to' => $invoiceTo,
            'line_items' => $lineItems,
            'totals' => $totals,
            'document_title' => $proformaNo !== '' ? 'Invoice' : 'Invoice (Draft)',
            'proforma_display_no' => $proformaNo !== '' ? $proformaNo : '—',
            'sender' => [
                'name' => strtoupper(CompanyAddress::NAME),
                'lines' => CompanyAddress::addressLines(),
                'phone' => CompanyAddress::PHONE,
                'email' => CompanyAddress::EMAIL,
            ],
            'bank_details' => ProformaInvoiceBankDetails::toArray(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Shipment>  $shipments
     * @param  array<string, string>  $invoiceTypeOptions
     * @return array<string, mixed>
     */
    public function buildConsolidatedSummary(\Illuminate\Support\Collection $shipments, array $invoiceTypeOptions = []): array
    {
        /** @var Shipment $first */
        $first = $shipments->first();
        $base = $this->build($first, $invoiceTypeOptions);
        $invoice = $base['invoice'];

        $summaryLineItems = [];
        $consolidationRows = [];

        foreach ($shipments as $shipment) {
            $shipment->loadMissing(['proformaInvoice.lineItems']);
            $detail = $this->invoicingShipmentRowMapper->mapDetail($shipment);
            $lineItems = is_array($detail['line_items'] ?? null) ? $detail['line_items'] : [];

            foreach ($lineItems as $item) {
                $summaryLineItems[] = $item;
            }

            $invoiceTotals = $this->calculateTotals($lineItems);
            $totalAmount = $this->resolveConsolidationInvoiceAmount($shipment, $lineItems, $invoiceTotals);

            $consolidationRows[] = [
                'invoice_no' => trim((string) ($detail['proforma_no'] ?? '')),
                'customer_po_no' => trim((string) ($detail['client_ref_no'] ?? '')),
                'shipment_no' => (string) $shipment->shipment_number,
                'mbl_awb_no' => trim((string) ($detail['mawb_no'] ?? $detail['mbl_no'] ?? '')),
                'total_packages' => trim((string) ($detail['packages'] ?? '')),
                'total_gross_weight' => trim((string) ($detail['gross_wt'] ?? '')),
                'total_amount' => $totalAmount,
            ];
        }

        $clientRef = trim((string) ($invoice['client_ref_no'] ?? ''));
        $invoice['proforma_no'] = '';
        $invoice['proforma_date'] = now()->format('d.m.Y');

        $displayNo = $clientRef !== '' ? 'Consolidated / ' . $clientRef : 'Consolidated';
        $totals = $this->calculateTotals($summaryLineItems);
        $consolidatedNet = array_sum(array_map(
            fn (array $row) => $this->toFloat($row['total_amount']),
            $consolidationRows
        ));

        if ($consolidatedNet > 0) {
            $totals['net_payable'] = number_format($consolidatedNet, 2, '.', '');
        }

        return [
            'invoice' => $invoice,
            'invoice_type_label' => $base['invoice_type_label'],
            'invoice_to' => $base['invoice_to'],
            'line_items' => [],
            'consolidation_rows' => $consolidationRows,
            'show_consolidation_summary_table' => true,
            'totals' => $totals,
            'document_title' => 'Consolidated Invoice',
            'proforma_display_no' => $displayNo,
            'hide_shipment_details' => true,
            'sender' => $base['sender'],
            'bank_details' => $base['bank_details'],
        ];
    }

    /**
     * @param  array<string, mixed>  $invoice
     * @return array{name: string, lines: list<string>, phone: string, email: string}
     */
    private function resolveInvoiceToFromBillingParty(Shipment $shipment, array $invoice): array
    {
        $shipment->loadMissing([
            'crrs.customerVessel.customer.invoiceDetail',
            'crrs.customerVessel.customer.invoiceAddress.country',
        ]);

        $customer = $shipment->crrs->first()?->customerVessel?->customer;

        if ($customer === null) {
            return $this->emptyInvoiceTo($invoice);
        }

        return $this->invoiceToFromCustomer($customer, $invoice);
    }

    /**
     * @param  array<string, mixed>  $invoice
     * @return array{name: string, lines: list<string>, phone: string, email: string}
     */
    private function invoiceToFromCustomer(Customer $customer, array $invoice): array
    {
        $detail = $customer->invoiceDetail;
        $address = $customer->invoiceAddress;

        $name = trim((string) ($detail?->invoice_recipient_name ?? ''));
        if ($name === '') {
            $name = trim((string) ($invoice['billing_party_display'] ?? ''));
        }

        return [
            'name' => $name !== '' ? $name : '—',
            'lines' => $this->formatInvoiceAddressLines($address),
            'phone' => trim((string) ($customer->phone ?? '')),
            'email' => trim((string) ($detail?->invoice_email ?? '')),
        ];
    }

    /**
     * @param  array<string, mixed>  $invoice
     * @return array{name: string, lines: list<string>, phone: string, email: string}
     */
    private function emptyInvoiceTo(array $invoice): array
    {
        $name = trim((string) ($invoice['billing_party_display'] ?? ''));

        return [
            'name' => $name !== '' ? $name : '—',
            'lines' => [],
            'phone' => '',
            'email' => '',
        ];
    }

    /**
     * @return list<string>
     */
    private function formatInvoiceAddressLines(?CustomerAddress $address): array
    {
        if ($address === null) {
            return [];
        }

        $rawParts = [];

        if (filled($address->street)) {
            $rawParts[] = (string) $address->street;
        }

        foreach (['city', 'state', 'zip_code'] as $field) {
            $value = $address->{$field};
            if (filled($value)) {
                $rawParts[] = (string) $value;
            }
        }

        if ($address->country) {
            $rawParts[] = (string) $address->country->name;
        }

        $uniqueParts = $this->uniqueCommaSeparatedAddressParts($rawParts);

        if ($uniqueParts === []) {
            return [];
        }

        return [implode(', ', $uniqueParts)];
    }

    /**
     * @param  list<string>  $rawParts
     * @return list<string>
     */
    private function uniqueCommaSeparatedAddressParts(array $rawParts): array
    {
        $seen = [];
        $unique = [];

        foreach ($rawParts as $rawPart) {
            foreach (preg_split('/\r\n|\r|\n|,/', $rawPart) as $segment) {
                $segment = $this->normalizeAddressSegment(trim($segment));
                if ($segment === '') {
                    continue;
                }

                $key = mb_strtolower($segment);
                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $unique[] = $segment;
            }
        }

        return $unique;
    }

    private function normalizeAddressSegment(string $segment): string
    {
        $normalized = $segment;

        while (preg_match('/\b(\S+)\s+\1\b/ui', $normalized)) {
            $normalized = preg_replace('/\b(\S+)\s+\1\b/ui', '$1', $normalized);
        }

        return trim(preg_replace('/\s{2,}/u', ' ', $normalized) ?? $normalized);
    }

    /**
     * @param  list<array<string, string>>  $lineItems
     * @param  array<string, string>  $invoiceTotals
     */
    private function resolveConsolidationInvoiceAmount(
        Shipment $shipment,
        array $lineItems,
        array $invoiceTotals,
    ): string {
        $fromLineItems = $this->toFloat($invoiceTotals['net_payable'] ?? 0);

        if ($fromLineItems > 0) {
            return $invoiceTotals['net_payable'];
        }

        $listRow = $this->invoicingShipmentRowMapper->map($shipment);

        return number_format($this->toFloat($listRow['net_invoice_amount'] ?? 0), 2, '.', '');
    }

    /**
     * @param  list<array<string, string>>  $lineItems
     * @return array<string, string>
     */
    private function calculateTotals(array $lineItems): array
    {
        $amount = 0.0;
        $nonTaxable = 0.0;
        $taxable = 0.0;
        $vat = 0.0;

        foreach ($lineItems as $item) {
            $amount += $this->toFloat($item['amount'] ?? 0);
            $nonTaxable += $this->toFloat($item['non_taxable'] ?? 0);
            $taxable += $this->toFloat($item['taxable'] ?? 0);
            $vat += $this->toFloat($item['igst_amt'] ?? 0);
        }

        return [
            'amount' => number_format($amount, 2, '.', ''),
            'non_taxable' => number_format($nonTaxable, 2, '.', ''),
            'taxable' => number_format($taxable, 2, '.', ''),
            'vat' => number_format($vat, 2, '.', ''),
            'net_payable' => number_format($nonTaxable + $taxable + $vat, 2, '.', ''),
        ];
    }

    private function toFloat(mixed $value): float
    {
        $value = str_replace(',', '', trim((string) $value));

        return is_numeric($value) ? (float) $value : 0.0;
    }
}
