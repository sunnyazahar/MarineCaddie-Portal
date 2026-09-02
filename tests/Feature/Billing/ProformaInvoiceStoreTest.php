<?php

namespace Tests\Feature\Billing;

use App\Models\Crr;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceLineItem;
use App\Models\Shipment;
use App\Services\ProformaNumberGenerator;
use Carbon\Carbon;
use Tests\RegressionTestCase;

class ProformaInvoiceStoreTest extends RegressionTestCase
{
    private const PAYMENT_TYPE = 'full_payment';

    public function test_preview_proforma_number_uses_financial_year_format(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing.preview-proforma-number'))
            ->assertOk()
            ->assertJson([
                'proforma_no' => 'MC-AE26-27-0001',
            ]);
    }

    public function test_preview_proforma_number_uses_back_date_financial_year(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing.preview-proforma-number', [
                'proforma_date' => '15.03.2026',
            ]))
            ->assertOk()
            ->assertJson([
                'proforma_no' => 'MC-AE25-26-0001',
            ]);
    }

    public function test_store_proforma_invoice_uses_back_date_for_numbering(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-BACKDATE-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), [
                'shipment_id' => $shipment->id,
                'proforma_date' => '15.03.2026',
                'currency' => 'USD',
                'payment_type' => self::PAYMENT_TYPE,
                'line_items' => [[
                    'description' => 'Freight charges',
                    'qty' => '1',
                    'rate' => '100.00',
                    'currency' => 'USD',
                    'amount' => '100.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '100.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ]],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'proforma_no' => 'MC-AE25-26-0001',
                'is_update' => false,
            ]);

        $invoice = ProformaInvoice::query()->where('shipment_id', $shipment->id)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('25-26', $invoice->financial_year_label);
        $this->assertSame('2026-03-15', $invoice->proforma_date?->toDateString());
    }

    public function test_update_proforma_invoice_keeps_existing_number(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-UPDATE-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0009',
            'financial_year_label' => '26-27',
            'sequence_no' => 9,
            'client_ref_no' => 'OLD-REF',
            'currency' => 'USD',
            'created_by' => $user->id,
        ]);

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), [
                'shipment_id' => $shipment->id,
                'client_ref_no' => 'NEW-REF',
                'currency' => 'EUR',
                'payment_type' => 'partial_payment',
                'line_items' => [[
                    'description' => 'Updated line',
                    'qty' => '1',
                    'rate' => '200.00',
                    'currency' => 'EUR',
                    'amount' => '200.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '200.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ]],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'proforma_no' => 'MC-AE26-27-0009',
                'is_update' => true,
            ]);

        $invoice = ProformaInvoice::query()->where('shipment_id', $shipment->id)->first();
        $this->assertSame('NEW-REF', $invoice->client_ref_no);
        $this->assertSame('partial_payment', $invoice->payment_type);
        $this->assertSame('EUR', $invoice->currency);
        $this->assertSame('Updated line', $invoice->lineItems()->first()->description);
    }

    public function test_store_proforma_invoice_saves_header_and_multiple_line_items(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-STORE-1',
            'status' => 'In process',
            'service' => 'Airfreight',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
            'customer_reference' => 'REF-STORE-1',
        ]);

        $payload = [
            'shipment_id' => $shipment->id,
            'invoice_type' => '1',
            'shipper' => 'agent:1',
            'consignee' => 'customer:1',
            'billing_party' => 'agent:1',
            'proforma_date' => '01.09.2026',
            'job_no' => 'INV-STORE-1',
            'job_date' => '01.09.2026',
            'client_ref_no' => 'REF-STORE-1',
            'currency' => 'USD',
            'payment_type' => self::PAYMENT_TYPE,
            'paid_amount' => '200.00',
            'line_items' => [
                [
                    'description' => 'Freight charges',
                    'hsn' => '9965',
                    'remarks' => 'Line 1',
                    'qty' => '1',
                    'rate' => '150.00',
                    'currency' => 'USD',
                    'amount' => '150.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '150.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ],
                [
                    'description' => 'Handling charges',
                    'hsn' => '9967',
                    'remarks' => 'Line 2',
                    'qty' => '2',
                    'rate' => '25.00',
                    'currency' => 'USD',
                    'amount' => '50.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '50.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ],
            ],
        ];

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), $payload)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'proforma_no' => 'MC-AE26-27-0001',
            ]);

        $invoice = ProformaInvoice::query()->where('shipment_id', $shipment->id)->first();

        $this->assertNotNull($invoice);
        $this->assertSame('MC-AE26-27-0001', $invoice->proforma_no);
        $this->assertSame('26-27', $invoice->financial_year_label);
        $this->assertSame(1, $invoice->sequence_no);
        $this->assertSame('REF-STORE-1', $invoice->client_ref_no);
        $this->assertSame('USD', $invoice->currency);
        $this->assertSame(self::PAYMENT_TYPE, $invoice->payment_type);
        $this->assertSame('200.00', $invoice->paid_amount);
        $this->assertSame('0.00', $invoice->due_amount);

        $lineItems = ProformaInvoiceLineItem::query()
            ->where('proforma_invoice_id', $invoice->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(2, $lineItems);
        $this->assertSame('Freight charges', $lineItems[0]->description);
        $this->assertSame('Handling charges', $lineItems[1]->description);
        $this->assertSame('50.00', $lineItems[1]->amount);
    }

    public function test_store_proforma_invoice_requires_payment_type(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PAYMENT-REQ-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), [
                'shipment_id' => $shipment->id,
                'currency' => 'USD',
                'line_items' => [[
                    'description' => 'Freight charges',
                    'qty' => '1',
                    'rate' => '100.00',
                    'currency' => 'USD',
                    'amount' => '100.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '100.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ]],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payment_type']);
    }

    public function test_proforma_number_increments_within_financial_year_and_resets_next_year(): void
    {
        $generator = app(ProformaNumberGenerator::class);

        Carbon::setTestNow('2026-09-01 10:00:00');
        $this->assertSame('MC-AE26-27-0001', $generator->previewNext());

        $shipment = Shipment::create([
            'shipment_number' => 'INV-SEQ-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0001',
            'financial_year_label' => '26-27',
            'sequence_no' => 1,
            'created_by' => null,
        ]);

        $this->assertSame('MC-AE26-27-0002', $generator->previewNext());

        Carbon::setTestNow('2027-04-01 10:00:00');
        $this->assertSame('MC-AE27-28-0001', $generator->previewNext());
    }

    public function test_invoicing_list_shows_billed_status_after_save(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-BILLED-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0003',
            'financial_year_label' => '26-27',
            'sequence_no' => 3,
            'payment_type' => 'full_payment',
            'created_by' => $user->id,
        ]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('INV-BILLED-1', $html);
        $this->assertStringContainsString('Billed', $html);
    }

    public function test_store_partial_payment_shows_partially_paid_in_list(): void
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PARTIAL-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), [
                'shipment_id' => $shipment->id,
                'currency' => 'USD',
                'payment_type' => 'partial_payment',
                'paid_amount' => '100.00',
                'line_items' => [[
                    'description' => 'Freight charges',
                    'qty' => '1',
                    'rate' => '300.00',
                    'currency' => 'USD',
                    'amount' => '300.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '300.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ]],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('INV-PARTIAL-1', $html);
        $this->assertStringContainsString('Partially paid', $html);
    }
}
