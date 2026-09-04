<?php

namespace Tests\Feature\Billing;

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerInvoiceDetail;
use App\Models\CustomerVessel;
use App\Models\ProformaInvoice;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Services\ProformaInvoicePdfBuilder;
use DOMDocument;
use DOMXPath;
use Tests\RegressionTestCase;

class InvoicingTableColumnsTest extends RegressionTestCase
{
    public function test_invoicing_table_lists_shipments_and_hides_cancelled(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'INV-STK-1',
            'vessel_name' => 'Test Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 1250.5,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        CrrPackage::create(['crr_id' => $crr->id, 'weight' => 12.5]);

        $visible = Shipment::create([
            'shipment_number' => 'INV-VISIBLE-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
            'customer_reference' => 'REF-123',
        ]);
        $visible->crrs()->attach($crr->id);

        Shipment::create([
            'shipment_number' => 'INV-CANCELLED-1',
            'status' => 'Cancelled',
            'service' => 'Airfreight',
        ]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertOk()
            ->getContent();

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $thCount = $xpath->query('//*[@id="invoicing-table"]/thead/tr/th')->length;
        $tdCount = $xpath->query('//*[@id="invoicing-table"]/tbody/tr[1]/td')->length;
        $colCount = $xpath->query('//*[@id="invoicing-table"]/colgroup/col')->length;

        $this->assertSame(19, $thCount, 'Expected 19 header columns');
        $this->assertSame(19, $tdCount, 'Expected 19 body columns on first row');
        $this->assertSame(19, $colCount, 'Expected 19 colgroup columns');
        $this->assertStringContainsString('INV-VISIBLE-1', $html);
        $this->assertStringContainsString(route('shipments.edit', $visible->id), $html);
        $this->assertStringNotContainsString('INV-CANCELLED-1', $html);
        $this->assertStringContainsString('Airfreight', $html);
        $this->assertStringContainsString('Ready for billing', $html);
        $this->assertStringContainsString('1,250.50', $html);
    }

    public function test_edit_page_billing_party_shows_invoice_recipient_name(): void
    {
        $user = $this->createAdminUser();

        $customer = Customer::create(['customer_name' => 'Invoicing Billing Customer']);
        CustomerInvoiceDetail::create([
            'customer_id' => $customer->id,
            'invoice_recipient_name' => 'Marine Invoice Recipient Ltd',
        ]);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Billing Vessel',
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-STK-BILL-1',
            'vessel_name' => 'Billing Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 900,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-BILLING-PARTY-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($crr->id);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk()
            ->assertSee('Marine Invoice Recipient Ltd')
            ->assertDontSee('Invoicing Billing Customer')
            ->assertSee('id="billing-party"', false)
            ->assertSee('name="billing_party"', false);
    }

    public function test_edit_page_bill_to_pos_shows_invoice_address_country(): void
    {
        $user = $this->createAdminUser();

        $country = Country::create([
            'name' => 'United Arab Emirates',
            'currency' => 'AED',
            'is_active' => true,
        ]);

        $customer = Customer::create(['customer_name' => 'Invoicing Bill To Customer']);
        CustomerInvoiceDetail::create([
            'customer_id' => $customer->id,
            'invoice_recipient_name' => 'Invoice Recipient UAE',
        ]);
        CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => 'invoice',
            'street' => 'Dubai Marina',
            'country_id' => $country->id,
        ]);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Bill To Vessel',
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-STK-BILLTO-1',
            'vessel_name' => 'Bill To Vessel',
            'content' => 'Shipspares',
            'currency' => 'AED',
            'customs_value' => 1200,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-BILL-TO-POS-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($crr->id);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk()
            ->assertSee('United Arab Emirates')
            ->assertSee('id="bill_to_pos"', false)
            ->assertSee('data-country-select-ajax="1"', false)
            ->assertSee('value="'.$country->id.'"', false);
    }

    public function test_invoicing_list_filters_by_billing_status_via_ajax(): void
    {
        $user = $this->createAdminUser();

        $readyShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-READY-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $billedShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-BILLED-1',
            'status' => 'Completed',
            'service' => 'Courier',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $billedShipment->id,
            'proforma_no' => 'MC-AE26-27-0099',
            'financial_year_label' => '26-27',
            'sequence_no' => 99,
            'payment_type' => 'full_payment',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'status' => ['Billed'],
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $html = $response->json('html');
        $this->assertStringContainsString('MC-AE26-27-0099', $html);
        $this->assertStringNotContainsString('INV-FILTER-READY-1', $html);
        $this->assertSame(1, $response->json('total'));
    }

    public function test_invoicing_list_filters_by_partially_paid_status_via_ajax(): void
    {
        $user = $this->createAdminUser();

        $partialShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-PARTIAL-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $billedShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-PARTIAL-BILLED-1',
            'status' => 'Completed',
            'service' => 'Courier',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $partialShipment->id,
            'proforma_no' => 'MC-AE26-27-0101',
            'financial_year_label' => '26-27',
            'sequence_no' => 101,
            'payment_type' => 'partial_payment',
            'created_by' => $user->id,
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $billedShipment->id,
            'proforma_no' => 'MC-AE26-27-0102',
            'financial_year_label' => '26-27',
            'sequence_no' => 102,
            'payment_type' => 'full_payment',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'status' => ['Partially paid'],
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $html = $response->json('html');
        $this->assertStringContainsString('MC-AE26-27-0101', $html);
        $this->assertStringNotContainsString('MC-AE26-27-0102', $html);
        $this->assertSame(1, $response->json('total'));
    }

    public function test_invoicing_list_filters_by_invoice_no_and_shipment_no_via_ajax(): void
    {
        $user = $this->createAdminUser();

        $targetShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-SHIP-001',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $otherShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-SHIP-002',
            'status' => 'Completed',
            'service' => 'Courier',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $targetShipment->id,
            'proforma_no' => 'MC-AE26-27-INV-FILTER',
            'financial_year_label' => '26-27',
            'sequence_no' => 201,
            'payment_type' => 'full_payment',
            'created_by' => $user->id,
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $otherShipment->id,
            'proforma_no' => 'MC-AE26-27-OTHER-99',
            'financial_year_label' => '26-27',
            'sequence_no' => 202,
            'payment_type' => 'full_payment',
            'created_by' => $user->id,
        ]);

        $byInvoiceNo = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'invoice_no' => 'MC-AE26-27-INV',
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $byInvoiceNo->assertOk();
        $this->assertStringContainsString('MC-AE26-27-INV-FILTER', $byInvoiceNo->json('html'));
        $this->assertStringNotContainsString('MC-AE26-27-OTHER-99', $byInvoiceNo->json('html'));

        $byInvoiceNoPartial = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'invoice_no' => 'MC-AE26-27-INV-FILTER',
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $byInvoiceNoPartial->assertOk();
        $this->assertStringContainsString('MC-AE26-27-INV-FILTER', $byInvoiceNoPartial->json('html'));
        $this->assertStringNotContainsString('MC-AE26-27-OTHER-99', $byInvoiceNoPartial->json('html'));

        $byShipmentNo = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'job_no' => 'INV-FILTER-SHIP-002',
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $byShipmentNo->assertOk();
        $this->assertStringContainsString('INV-FILTER-SHIP-002', $byShipmentNo->json('html'));
        $this->assertStringNotContainsString('INV-FILTER-SHIP-001', $byShipmentNo->json('html'));
    }

    public function test_invoicing_list_filters_by_mawb_mbl_via_ajax(): void
    {
        $user = $this->createAdminUser();

        $withMawb = Shipment::create([
            'shipment_number' => 'INV-FILTER-MAWB-1',
            'status' => 'Completed',
            'service' => 'Courier',
        ]);

        $withoutMawb = Shipment::create([
            'shipment_number' => 'INV-FILTER-MAWB-2',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        \App\Models\ShipmentCourierLeg::query()->create([
            'shipment_id' => $withMawb->id,
            'airway_bill' => 'MAWB-INV-FILTER-999',
        ]);

        $response = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'mawb_mbl' => 'MAWB-INV-FILTER',
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $html = $response->json('html');
        $this->assertStringContainsString('INV-FILTER-MAWB-1', $html);
        $this->assertStringNotContainsString('INV-FILTER-MAWB-2', $html);
        $this->assertSame(1, $response->json('total'));
    }

    public function test_invoicing_list_filters_by_po_number_on_client_ref_via_ajax(): void
    {
        $user = $this->createAdminUser();

        $matchingShipment = Shipment::create([
            'shipment_number' => 'INV-FILTER-PO-MATCH',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => 'PO-CLIENT-REF-123',
        ]);

        Shipment::create([
            'shipment_number' => 'INV-FILTER-PO-OTHER',
            'status' => 'Completed',
            'service' => 'Courier',
            'customer_reference' => 'OTHER-REF-999',
        ]);

        $response = $this->actingAsVerified($user)
            ->getJson(route('billing.invoicing', [
                'po_number' => 'CLIENT-REF',
            ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $html = $response->json('html');
        $this->assertStringContainsString('INV-FILTER-PO-MATCH', $html);
        $this->assertStringNotContainsString('INV-FILTER-PO-OTHER', $html);
        $this->assertSame(1, $response->json('total'));
    }

    public function test_consolidated_print_merges_selected_invoices_with_same_po_and_party(): void
    {
        $user = $this->createAdminUser();

        $customer = Customer::create(['customer_name' => 'Consolidate Customer']);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Consolidate Vessel',
        ]);

        $shipmentA = Shipment::create([
            'shipment_number' => 'INV-CONSOL-A',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => 'PO-CONSOL-123',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
        ]);

        $shipmentB = Shipment::create([
            'shipment_number' => 'INV-CONSOL-B',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => 'PO-CONSOL-123',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
        ]);

        $crrA = Crr::create([
            'stock_number' => 'INV-STK-CONSOL-A',
            'vessel_name' => 'Consolidate Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 500,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crrB = Crr::create([
            'stock_number' => 'INV-STK-CONSOL-B',
            'vessel_name' => 'Consolidate Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 700,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipmentA->crrs()->attach($crrA->id);
        $shipmentB->crrs()->attach($crrB->id);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipmentA->id,
            'proforma_no' => 'MC-AE26-27-0201',
            'financial_year_label' => '26-27',
            'sequence_no' => 201,
            'payment_type' => 'full_payment',
            'client_ref_no' => 'PO-CONSOL-123',
        ]);
        ProformaInvoice::query()->create([
            'shipment_id' => $shipmentB->id,
            'proforma_no' => 'MC-AE26-27-0202',
            'financial_year_label' => '26-27',
            'sequence_no' => 202,
            'payment_type' => 'full_payment',
            'client_ref_no' => 'PO-CONSOL-123',
        ]);

        $response = $this->actingAsVerified($user)
            ->get(route('billing.invoicing.consolidated-print', [
                'job_no' => ['INV-CONSOL-A', 'INV-CONSOL-B'],
            ]));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));

        $mergedPath = tempnam(sys_get_temp_dir(), 'mc_consol_pdf_');
        $this->assertNotFalse($mergedPath);
        file_put_contents($mergedPath, $response->getContent());
        $fpdi = new \setasign\Fpdi\Fpdi('P', 'pt');
        $this->assertSame(3, $fpdi->setSourceFile($mergedPath));
        @unlink($mergedPath);

        $summaryData = app(ProformaInvoicePdfBuilder::class)->buildConsolidatedSummary(
            app(ShipmentRepositoryInterface::class)->findManyForInvoicingByNumbers(['INV-CONSOL-A', 'INV-CONSOL-B']),
            ['1' => 'AIR Export']
        );
        $this->assertSame('Consolidated Invoice', $summaryData['document_title']);
        $this->assertTrue($summaryData['hide_shipment_details']);
        $this->assertTrue($summaryData['show_consolidation_summary_table']);
        $this->assertCount(0, $summaryData['line_items']);
        $this->assertCount(2, $summaryData['consolidation_rows']);
        $this->assertSame('INV-CONSOL-A', $summaryData['consolidation_rows'][0]['shipment_no']);
        $this->assertSame('INV-CONSOL-B', $summaryData['consolidation_rows'][1]['shipment_no']);
        $this->assertSame('PO-CONSOL-123', $summaryData['consolidation_rows'][0]['customer_po_no']);
        $this->assertSame('500.00', $summaryData['consolidation_rows'][0]['total_amount']);
        $this->assertSame('700.00', $summaryData['consolidation_rows'][1]['total_amount']);
        $this->assertSame('1200.00', $summaryData['totals']['net_payable']);
    }

    public function test_consolidated_print_allows_empty_po_when_party_matches(): void
    {
        $user = $this->createAdminUser();

        $customer = Customer::create(['customer_name' => 'Empty PO Customer']);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Empty PO Vessel',
        ]);

        $shipmentA = Shipment::create([
            'shipment_number' => 'INV-CONSOL-EMPTY-PO-A',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => null,
        ]);
        $shipmentB = Shipment::create([
            'shipment_number' => 'INV-CONSOL-EMPTY-PO-B',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => '',
        ]);

        $crrA = Crr::create([
            'stock_number' => 'INV-STK-EMPTY-PO-A',
            'vessel_name' => 'Empty PO Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 100,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crrB = Crr::create([
            'stock_number' => 'INV-STK-EMPTY-PO-B',
            'vessel_name' => 'Empty PO Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 200,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipmentA->crrs()->attach($crrA->id);
        $shipmentB->crrs()->attach($crrB->id);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipmentA->id,
            'proforma_no' => 'MC-AE26-27-0203',
            'financial_year_label' => '26-27',
            'sequence_no' => 203,
            'payment_type' => 'full_payment',
        ]);
        ProformaInvoice::query()->create([
            'shipment_id' => $shipmentB->id,
            'proforma_no' => 'MC-AE26-27-0204',
            'financial_year_label' => '26-27',
            'sequence_no' => 204,
            'payment_type' => 'full_payment',
        ]);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.consolidated-print', [
                'job_no' => ['INV-CONSOL-EMPTY-PO-A', 'INV-CONSOL-EMPTY-PO-B'],
            ]))
            ->assertOk();
    }

    public function test_consolidated_print_requires_generated_invoices(): void
    {
        $user = $this->createAdminUser();

        $customer = Customer::create(['customer_name' => 'Pending Invoice Customer']);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'Pending Invoice Vessel',
        ]);

        $shipmentA = Shipment::create([
            'shipment_number' => 'INV-CONSOL-PENDING-A',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => 'PO-PENDING-1',
        ]);
        $shipmentB = Shipment::create([
            'shipment_number' => 'INV-CONSOL-PENDING-B',
            'status' => 'Completed',
            'service' => 'Airfreight',
            'customer_reference' => 'PO-PENDING-1',
        ]);

        $crrA = Crr::create([
            'stock_number' => 'INV-STK-PENDING-A',
            'vessel_name' => 'Pending Invoice Vessel',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);
        $crrB = Crr::create([
            'stock_number' => 'INV-STK-PENDING-B',
            'vessel_name' => 'Pending Invoice Vessel',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipmentA->crrs()->attach($crrA->id);
        $shipmentB->crrs()->attach($crrB->id);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.consolidated-print', [
                'job_no' => ['INV-CONSOL-PENDING-A', 'INV-CONSOL-PENDING-B'],
            ]))
            ->assertStatus(422);
    }

    public function test_edit_icon_opens_proforma_edit_page_for_shipment(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'INV-STK-EDIT-1',
            'vessel_name' => 'Edit Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 500,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-EDIT-OPEN-1',
            'status' => 'In process',
            'service' => 'Airfreight',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
        ]);
        $shipment->crrs()->attach($crr->id);

        $response = $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk()
            ->assertSee('INV-EDIT-OPEN-1')
            ->assertSee('Edit Vessel')
            ->assertSee('Generate Invoice')
            ->assertSee('Credit Note')
            ->assertSee(now()->format('d.m.Y'))
            ->assertSee('value="Airfreight" selected', false);

        $html = $response->getContent();
        $this->assertStringContainsString('class="form-control form-control-sm line-rate text-right" value=""', $html);
        $this->assertMatchesRegularExpression(
            '/id="proforma_no"[^>]*value=""[^>]*readonly/',
            $html,
            'Proforma No should be blank and readonly'
        );
    }

    public function test_edit_page_shows_update_button_for_saved_proforma(): void
    {
        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-EDIT-SAVED-1',
            'status' => 'In process',
            'service' => 'Airfreight',
        ]);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0010',
            'financial_year_label' => '26-27',
            'sequence_no' => 10,
            'created_by' => $user->id,
        ]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk()
            ->assertSee('Update')
            ->assertSee('MC-AE26-27-0010')
            ->getContent();

        $this->assertStringContainsString('id="proforma-generate-invoice"', $html);
        $this->assertStringContainsString('disabled', $html);
        $this->assertStringContainsString('id="proforma-update-invoice"', $html);
    }

    public function test_edit_page_returns_404_for_cancelled_shipment(): void
    {
        $user = $this->createAdminUser();

        Shipment::create([
            'shipment_number' => 'INV-EDIT-CANCELLED-1',
            'status' => 'Cancelled',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => 'INV-EDIT-CANCELLED-1']))
            ->assertNotFound();
    }

    public function test_print_pdf_invoice_to_shows_billing_party_details(): void
    {
        $user = $this->createAdminUser();

        $country = Country::create([
            'name' => 'United Arab Emirates',
            'currency' => 'AED',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'customer_name' => 'PDF Billing Customer',
            'phone' => '+971 4 123 4567',
        ]);
        CustomerInvoiceDetail::create([
            'customer_id' => $customer->id,
            'invoice_recipient_name' => 'Marine Invoice Recipient Ltd',
            'invoice_email' => 'billing@marine-invoice.test',
        ]);
        CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => 'invoice',
            'street' => 'Dubai Marina Tower',
            'country_id' => $country->id,
        ]);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'PDF Billing Vessel',
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-STK-PDF-BILL-1',
            'vessel_name' => 'PDF Billing Vessel',
            'content' => 'Shipspares',
            'currency' => 'AED',
            'customs_value' => 800,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PDF-BILL-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($crr->id);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0101',
            'financial_year_label' => '26-27',
            'sequence_no' => 101,
            'payment_type' => 'full_payment',
        ]);

        $data = app(ProformaInvoicePdfBuilder::class)->build($shipment);
        $html = view('Billing.pdf.proforma-invoice', $data)->render();

        $this->assertSame('Marine Invoice Recipient Ltd', $data['invoice_to']['name']);
        $this->assertStringContainsString('Dubai Marina Tower, United Arab Emirates', $html);
        $this->assertStringNotContainsString('Dubai Marina Tower, Dubai Marina Tower', $html);
        $this->assertStringContainsString('billing@marine-invoice.test', $html);
        $this->assertStringContainsString('+971 4 123 4567', $html);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.print', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk();
    }

    public function test_print_pdf_invoice_to_deduplicates_repeated_address_parts(): void
    {
        $country = Country::create([
            'name' => 'Cyprus',
            'currency' => 'EUR',
            'is_active' => true,
        ]);

        $customer = Customer::create(['customer_name' => 'MSC Customer']);
        CustomerInvoiceDetail::create([
            'customer_id' => $customer->id,
            'invoice_recipient_name' => 'MSC SHIPMANAGEMENT LTD',
        ]);
        CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => 'invoice',
            'street' => 'MSC House, 51, Ilia Kannaourou Street, Kourion Municipality, Ypsonas, 4187, Limassol',
            'city' => 'Limassol',
            'zip_code' => '4187',
            'country_id' => $country->id,
        ]);
        CustomerVessel::create([
            'customer_id' => $customer->id,
            'vessel' => 'MSC Vessel',
        ]);

        $crr = Crr::create([
            'stock_number' => 'INV-STK-PDF-DEDUP-1',
            'vessel_name' => 'MSC Vessel',
            'content' => 'Shipspares',
            'currency' => 'EUR',
            'customs_value' => 500,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PDF-DEDUP-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);
        $shipment->crrs()->attach($crr->id);

        $data = app(ProformaInvoicePdfBuilder::class)->build($shipment);

        $this->assertSame(
            'MSC House, 51, Ilia Kannaourou Street, Kourion Municipality, Ypsonas, 4187, Limassol, Cyprus',
            $data['invoice_to']['lines'][0]
        );
    }

    public function test_print_proforma_invoice_opens_pdf_for_shipment(): void
    {
        $user = $this->createAdminUser();

        $crr = Crr::create([
            'stock_number' => 'INV-STK-PRINT-1',
            'vessel_name' => 'Print Vessel',
            'content' => 'Shipspares',
            'currency' => 'USD',
            'customs_value' => 250,
            'status' => Crr::STATUS_IN_PROGRESS,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PRINT-1',
            'status' => 'In process',
            'service' => 'Airfreight',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DXB',
        ]);
        $shipment->crrs()->attach($crr->id);

        ProformaInvoice::query()->create([
            'shipment_id' => $shipment->id,
            'proforma_no' => 'MC-AE26-27-0102',
            'financial_year_label' => '26-27',
            'sequence_no' => 102,
            'payment_type' => 'full_payment',
        ]);

        $response = $this->actingAsVerified($user)
            ->get(route('billing.invoicing.print', ['proformaNo' => $shipment->shipment_number]));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_print_requires_generated_invoice(): void
    {
        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-PRINT-BLOCK-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing.print', ['proformaNo' => $shipment->shipment_number]))
            ->assertStatus(422);
    }

    public function test_invoicing_list_print_icon_marks_ungenerated_invoice(): void
    {
        $user = $this->createAdminUser();

        Shipment::create([
            'shipment_number' => 'INV-PRINT-UI-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('data-invoice-generated="0"', $html);
        $this->assertStringContainsString('Please generate the invoice first before printing.', $html);
        $this->assertStringContainsString('Please generate the invoice first for every selected shipment before consolidating.', $html);
    }

    public function test_unsaved_edit_page_first_line_item_row_is_blank_by_default(): void
    {
        $user = $this->createAdminUser();

        $shipment = Shipment::create([
            'shipment_number' => 'INV-TYPE-BLANK-EDIT',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $html = $this->actingAsVerified($user)
            ->get(route('billing.invoicing.edit', ['proformaNo' => $shipment->shipment_number]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('class="form-control form-control-sm line-qty-type"', $html);
        $this->assertStringContainsString('data-qty-type-blank="1"', $html);
        $this->assertStringContainsString('value="" selected', $html);
        $this->assertStringNotContainsString('value="KG" selected', $html);
        $this->assertStringContainsString('var proformaIsSaved = false', $html);
        $this->assertStringNotContainsString('class="line-description" value="Freight charges"', $html);
        $this->assertStringContainsString('class="form-control form-control-sm line-description" value=""', $html);
        $this->assertStringContainsString('class="form-control form-control-sm line-qty text-right" value=""', $html);
        $this->assertStringContainsString('class="form-control form-control-sm line-rate text-right" value=""', $html);
    }
}
