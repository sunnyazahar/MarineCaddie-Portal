<?php

namespace App\Http\Controllers;

use App\Models\OtherCompany;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BillingController extends Controller
{
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

    public function invoicing(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 25)));
        $page = max(1, (int) $request->query('page', 1));
        $offset = ($page - 1) * $perPage;
        $start = $offset + 1;
        $end = min($offset + $perPage, self::MOCK_INVOICE_TOTAL);

        $pageItems = collect();
        for ($i = $start; $i <= $end; $i++) {
            $pageItems->push($this->buildMockInvoiceRow($i));
        }

        $invoices = new LengthAwarePaginator(
            $pageItems,
            self::MOCK_INVOICE_TOTAL,
            $perPage,
            $page,
            [
                'path' => route('billing.invoicing'),
                'query' => $request->query(),
            ]
        );

        return view('Billing.all-invoices', compact('invoices'));
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
        $index = $this->resolveMockInvoiceIndex($proformaNo);
        if ($index === null) {
            abort(404);
        }

        $invoice = $this->buildMockInvoiceDetail($index);
        $billToPosOptions = OtherCompany::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'code']);

        if ($billToPosOptions->isNotEmpty()) {
            $invoice['bill_to_pos'] = (string) $billToPosOptions[($index - 1) % $billToPosOptions->count()]->id;
        } else {
            $invoice['bill_to_pos'] = '';
        }

        return view('Billing.edit-proforma-invoice', [
            'invoice' => $invoice,
            'invoiceTypeOptions' => self::INVOICE_TYPE_OPTIONS,
            'billToPosOptions' => $billToPosOptions,
        ]);
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
            'vessel_name' => explode(' / ', $row['party_name'])[1] ?? 'Green Sea',
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
            'party_name' => $shipper.' / '.$vessel,
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
        ];
    }
}
