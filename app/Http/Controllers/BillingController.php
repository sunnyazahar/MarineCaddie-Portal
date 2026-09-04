<?php

namespace App\Http\Controllers;

use App\Http\Requests\Billing\StoreProformaInvoiceRequest;
use App\Models\Shipment;
use App\Repositories\Contracts\CrrRepositoryInterface;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Services\ConsolidatedProformaInvoicePdfService;
use App\Services\InvoicingShipmentRowMapper;
use App\Services\ProformaInvoicePdfBuilder;
use App\Services\ProformaInvoiceService;
use App\Services\ShipmentPdfCompanyFooter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private CrrRepositoryInterface $crrRepository,
        private InvoicingShipmentRowMapper $invoicingShipmentRowMapper,
        private ProformaInvoiceService $proformaInvoiceService,
    ) {}

    private const MOCK_INVOICE_TOTAL = 200;

    /** @var array<string, string> */
    private const INVOICE_TYPE_OPTIONS = [
        '1' => 'AIR Export',
        '2' => 'AIR Import',
        '3' => 'SEA Export',
        '4' => 'SEA Import',
        '5' => 'Courier',
        '6' => 'On Board Delivery',
        '7' => 'Miscellaneous',
    ];

    /** @var list<string> */
    private const SERVICE_OPTIONS = [
        'Courier',
        'Airfreight',
        'Sea freight',
        'Truck',
        'Release',
        'Hand Carry',
        'On-board delivery',
    ];

    public function invoicing(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 25)));
        $filters = $request->all();
        $invoices = $this->shipmentRepository->paginateForInvoicing($filters, $perPage);
        $invoices->setCollection(
            $this->invoicingShipmentRowMapper->mapCollection($invoices->getCollection())
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Billing.partials.invoicing-rows', compact('invoices'))->render(),
                'pagination' => view('Billing.partials.invoicing-pagination-links', ['paginator' => $invoices])->render(),
                'pagination_meta' => view('Billing.partials.invoicing-pagination-meta', ['paginator' => $invoices])->render(),
                'total' => $invoices->total(),
            ]);
        }

        $filterOptions = $this->crrRepository->indexFilterOptions();

        return view('Billing.all-invoices', array_merge(compact('invoices'), $filterOptions));
    }

    public function debitNotes()
    {
        return view('Billing.debit-notes');
    }

    public function creditNotes()
    {
        return view('Billing.credit-notes');
    }

    public function editInvoice(string $proformaNo)
    {
        $shipment = $this->shipmentRepository->findForInvoicingByNumber($proformaNo);
        if ($shipment === null) {
            abort(404);
        }

        $invoice = $this->invoicingShipmentRowMapper->mapDetail($shipment);

        return view('Billing.edit-proforma-invoice', [
            'invoice' => $invoice,
            'invoiceTypeOptions' => self::INVOICE_TYPE_OPTIONS,
            'serviceOptions' => self::SERVICE_OPTIONS,
            'shipmentId' => $shipment->id,
        ]);
    }

    public function previewProformaNumber(Request $request)
    {
        return response()->json(
            $this->proformaInvoiceService->previewNumber($request->query('proforma_date'))
        );
    }

    public function storeProformaInvoice(StoreProformaInvoiceRequest $request)
    {
        $result = $this->proformaInvoiceService->store(
            $request->validated(),
            (int) $request->user()->id,
        );

        return response()->json($result);
    }

    public function printProformaInvoice(string $proformaNo, ProformaInvoicePdfBuilder $pdfBuilder, ShipmentPdfCompanyFooter $companyFooter)
    {
        $shipment = $this->shipmentRepository->findForInvoicingByNumber($proformaNo);
        if ($shipment === null) {
            abort(404);
        }

        $shipment->loadMissing('proformaInvoice');
        if ($shipment->proformaInvoice === null) {
            abort(422, 'Please generate the invoice first before printing.');
        }

        $data = $pdfBuilder->build($shipment, self::INVOICE_TYPE_OPTIONS);
        $numberSlug = $data['proforma_display_no'] !== '—'
            ? $data['proforma_display_no']
            : $shipment->shipment_number;
        $filename = 'proforma-invoice-' . preg_replace('/[^A-Za-z0-9._-]+/', '-', $numberSlug) . '.pdf';

        $pdf = Pdf::loadView('Billing.pdf.proforma-invoice', $data)->setPaper('a4', 'portrait');
        $output = $companyFooter->outputPageNumbers($pdf);

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function printConsolidatedProformaInvoices(Request $request, ConsolidatedProformaInvoicePdfService $consolidatedPdfService)
    {
        $jobNumbers = array_values(array_filter((array) $request->query('job_no', []), static fn ($value) => trim((string) $value) !== ''));

        return $consolidatedPdfService->streamMergedPdf($jobNumbers, self::INVOICE_TYPE_OPTIONS);
    }

    private function resolveMockInvoiceIndex(string $proformaNo): ?int
    {
        if (! preg_match('/^PF-SIN-(\d+)$/', $proformaNo, $matches)) {
            return null;
        }

        $index = (int) $matches[1] - 117000;

        if ($index < 1 || $index > self::MOCK_INVOICE_TOTAL) {
            return null;
        }

        return $index;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMockInvoiceDetail(int $i): array
    {
        $row = $this->buildMockInvoiceRow($i);
        $supplyTypes = ['Export', 'Import', 'Domestic'];
        $day = max(1, ($i % 28) + 1);
        $month = str_pad((string) (($i % 12) + 1), 2, '0', STR_PAD_LEFT);
        $dueDay = min(28, $day + 7);
        $hawbDay = max(1, $day - 2);
        $shipperDeparture = $this->resolveShipperDeparture($i);
        $consigneeData = $this->resolveConsignee($i);

        return array_merge($row, $shipperDeparture, $consigneeData, [
            'shipper_name' => $shipperDeparture['shipper_departure_display'] ?: $row['shipper_name'],
            'consignee_name' => $consigneeData['consignee_display'] ?: $row['consignee_name'],
            'invoice_type' => (string) (($i - 1) % count(self::INVOICE_TYPE_OPTIONS) + 1),
            'billing_party' => $shipperDeparture['shipper_departure'],
            'billing_party_display' => $shipperDeparture['shipper_departure_display'] ?: $row['shipper_name'],
            'bill_to_pos' => '',
            'airport_of_loading' => $row['port_of_loading'],
            'airport_of_destination' => $row['port_of_discharge'],
            'loading_date' => sprintf('%02d.%s.2026', $day, $month),
            'destination_date' => sprintf('%02d.%s.2026', min(28, $day + 3), $month),
            'due_date' => sprintf('%02d.%s.2026', $dueDay, $month),
            'hawb_no' => 'HAWB'.(880000 + $i),
            'hawb_date' => sprintf('%02d.%s.2026', $hawbDay, $month),
            'mawb_no' => $row['mbl_no'] !== '' ? $row['mbl_no'] : 'MAWB'.(770000 + $i),
            'mawb_date' => $row['job_date'],
            'packages' => (string) (1 + ($i % 12)),
            'commodity' => 'General Cargo',
            'type_of_supply' => $supplyTypes[$i % count($supplyTypes)],
            'sb_be_date' => $row['sb_be_no'] !== '' ? $row['job_date'] : '',
            'flight_no' => $i % 3 === 0 ? 'SQ'.(100 + ($i % 900)) : '',
            'flight_date' => $i % 3 === 0 ? $row['proforma_date'] : '',
            'vessel_name' => $row['vessel_name'] ?? 'Green Sea',
            'line_items' => $this->buildMockLineItems($i),
        ]);
    }

    /**
     * @return array{shipper_departure: string, shipper_departure_display: string}
     */
    private function resolveShipperDeparture(int $i): array
    {
        $shipment = Shipment::query()->find(11);

        if (! $shipment || ! $shipment->departure) {
            $shipment = Shipment::query()
                ->whereNotNull('departure')
                ->orderBy('id')
                ->skip(max(0, ($i - 1) % 20))
                ->first();
        }

        if (! $shipment || ! $shipment->departure) {
            return [
                'shipper_departure' => '',
                'shipper_departure_display' => '',
            ];
        }

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));

        return [
            'shipper_departure' => $shipment->departure,
            'shipper_departure_display' => $shipment->partyDisplay($shipment->departure, $partyNames),
        ];
    }

    /**
     * @return array{consignee: string, consignee_display: string}
     */
    private function resolveConsignee(int $i): array
    {
        $shipment = Shipment::query()->find(11);

        if (! $shipment || ! $shipment->consignee) {
            $shipment = Shipment::query()
                ->whereNotNull('consignee')
                ->orderBy('id')
                ->skip(max(0, ($i - 1) % 20))
                ->first();
        }

        if (! $shipment || ! $shipment->consignee) {
            return [
                'consignee' => '',
                'consignee_display' => '',
            ];
        }

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));

        return [
            'consignee' => $shipment->consignee,
            'consignee_display' => $shipment->partyDisplay($shipment->consignee, $partyNames),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function buildMockLineItems(int $i): array
    {
        $currency = ['USD', 'EUR', 'SGD'][$i % 3];
        $secondRate = number_format(600 + ($i * 13.7) % 500, 2, '.', '');

        return [
            [
                'description' => 'Air Import Clearance Charges',
                'hsn' => '9967',
                'remarks' => 'Clearance Charges',
                'qty' => '1',
                'qty_type' => 'KG',
                'rate' => '7',
                'currency' => $currency,
                'amount' => '7.00',
                'exchange_rate' => '1',
                'tax_type' => 'T',
                'non_taxable' => '0.00',
                'taxable' => '7.00',
                'igst_pct' => '0',
                'igst_amt' => '0.00',
                'cgst_pct' => '0',
                'cgst_amt' => '0.00',
                'sgst_pct' => '0',
                'sgst_amt' => '0.00',
            ],
            [
                'description' => 'Ocean Freight',
                'hsn' => '9965',
                'remarks' => 'Freight Charges',
                'qty' => '1',
                'qty_type' => 'Job',
                'rate' => $secondRate,
                'currency' => $currency,
                'amount' => $secondRate,
                'exchange_rate' => '1',
                'tax_type' => 'T',
                'non_taxable' => '0.00',
                'taxable' => $secondRate,
                'igst_pct' => '0',
                'igst_amt' => '0.00',
                'cgst_pct' => '0',
                'cgst_amt' => '0.00',
                'sgst_pct' => '0',
                'sgst_amt' => '0.00',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function buildMockInvoiceRow(int $i): array
    {
        $serviceTypes = ['Airfreight', 'Sea freight', 'Courier', 'Truck'];
        $einvoiceStatuses = ['Pending', 'Generated', 'Sent', 'Failed'];
        $currencies = ['USD', 'EUR', 'SGD'];
        $ports = ['SIN', 'RTM', 'HKG', 'DXB', 'MOH', 'PRI', 'SUR', 'JEB', 'BOM', 'KUL'];
        $shippers = [
            'MarineCaddie Shipping LLC',
            'Allseas Engineering BV',
            'Xt Management Ltd.',
            'Fleet Management Ltd.',
            'TATA NYK SHIPPING PTE LTD',
            'Seaspan Ship Management Ltd',
            'Integrated Maritime Management',
        ];
        $consignees = [
            'Allseas Engineering BV',
            'Fleet Management Ltd.',
            'Green Sea Vessel Ops',
            'BERNHARD SCHULTE SHIP MANAGEMENT',
            'Seaspan Ship Management Ltd',
            'CANG HAI Maritime',
            'Benromach Vessel Services',
            'FLORENCE Ship Management',
        ];
        $vessels = ['Green Sea', 'YM THRONE', 'ODETTE', 'Adam Schulte', 'Benromach', 'FLORENCE', 'Mari Innovator', 'CANG HAI'];

        $shipper = $shippers[$i % count($shippers)];
        $consignee = $consignees[$i % count($consignees)];
        $vessel = $vessels[$i % count($vessels)];
        $pol = $ports[$i % count($ports)];
        $pod = $ports[($i + 3) % count($ports)];
        $currency = $currencies[$i % count($currencies)];
        $grossWt = number_format(50 + ($i * 17.35) % 8900, 2, '.', ' ');
        $chargeableWt = number_format((float) str_replace(' ', '', $grossWt) * 1.08, 2, '.', ' ');
        $netAmount = number_format(100 + ($i * 127.47) % 14000, 2, '.', ' ');
        $gstAmount = number_format((float) str_replace(' ', '', $netAmount) * 0.09, 2, '.', ' ');
        $day = max(1, ($i % 28) + 1);
        $month = str_pad((string) (($i % 12) + 1), 2, '0', STR_PAD_LEFT);
        $proformaDate = sprintf('%02d.%s.2026', $day, $month);
        $jobDay = max(1, $day - 1);
        $jobDate = sprintf('%02d.%s.2026', $jobDay, $month);

        return [
            'service_type' => $serviceTypes[$i % count($serviceTypes)],
            'einvoice_status' => $einvoiceStatuses[$i % count($einvoiceStatuses)],
            'proforma_no' => 'PF-SIN-'.(117000 + $i),
            'proforma_date' => $proformaDate,
            'job_no' => 'JOB'.(371000 + $i),
            'job_date' => $jobDate,
            'shipper_name' => $shipper,
            'consignee_name' => $consignee,
            'party_name' => $shipper,
            'port_of_loading' => $pol,
            'port_of_discharge' => $pod,
            'client_ref_no' => strtoupper(substr($pol, 0, 3)).(6374000 + $i).'-'.($i % 100),
            'sb_be_no' => $i % 4 !== 0 ? (($i % 2 === 0 ? 'SB-' : 'BE-').(1000 + $i)) : '',
            'mbl_no' => $i % 5 !== 0 ? 'MBL'.(550000 + $i) : '',
            'gross_wt' => $grossWt,
            'chargeable_wt' => $chargeableWt,
            'currency' => $currency,
            'gst_amount' => $gstAmount,
            'net_invoice_amount' => $netAmount,
            'status' => $i % 2 === 0 ? 'Billed' : 'Ready for billing',
            'vessel_name' => $vessel,
        ];
    }
}
