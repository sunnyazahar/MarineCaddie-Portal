<?php

namespace App\Http\Controllers;

use App\Models\Crr;
use App\Models\CrrCost;
use App\Models\CrrDocument;
use App\Repositories\Contracts\CrrRepositoryInterface;
use App\Services\CrrChangeLogService;
use App\Support\CountryCache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CrrController extends Controller
{
    public function __construct(private CrrRepositoryInterface $crrRepository)
    {
    }

    public function index(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $crrs = $this->crrRepository->paginateIndex($request->all(), $perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Stock.partials.rows', compact('crrs'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $crrs])->render(),
                'total' => $crrs->total(),
            ]);
        }

        $options = $this->crrRepository->indexFilterOptions();

        return view('Stock.stocks', array_merge(['crrs' => $crrs], $options));
    }

    public function store(Request $request, CrrChangeLogService $changeLogService)
    {
        $validated = $request->validate([
            'hub_agent' => ['required', 'string', 'max:50', 'regex:/[A-Za-z0-9]/'],
            'currency' => ['required', 'string', 'max:10'],
            'customs_value' => ['required', 'numeric'],
            'status' => ['nullable', 'integer', \Illuminate\Validation\Rule::in(array_keys(Crr::getStatusLabels()))],
            'actual_delivery_date' => ['nullable', 'required_if:status,' . Crr::STATUS_ACTIVE, 'date'],
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.length' => ['required', 'numeric', 'gt:0'],
            'packages.*.width' => ['required', 'numeric', 'gt:0'],
            'packages.*.height' => ['required', 'numeric', 'gt:0'],
            'packages.*.weight' => ['required', 'numeric', 'gt:0'],
        ], [
            'currency.required' => 'Currency is required.',
            'customs_value.required' => 'Customs value is required.',
            'customs_value.numeric' => 'Customs value must be a number.',
            'actual_delivery_date.required_if' => 'Actual delivery date is required when status is Stock.',
            'packages.required' => 'Please add at least one package before saving.',
            'packages.min' => 'Please add at least one package before saving.',
            'packages.*.length.required' => 'Package length is required.',
            'packages.*.width.required' => 'Package width is required.',
            'packages.*.height.required' => 'Package height is required.',
            'packages.*.weight.required' => 'Package weight is required.',
            'packages.*.length.gt' => 'Package length must be greater than 0.',
            'packages.*.width.gt' => 'Package width must be greater than 0.',
            'packages.*.height.gt' => 'Package height must be greater than 0.',
            'packages.*.weight.gt' => 'Package weight must be greater than 0.',
        ]);

        DB::beginTransaction();
        try {
            // Generate a unique stock number using the selected Hub/Agent code.
            $stockPrefix = strtoupper(trim($validated['hub_agent']));
            $stockPrefix = preg_replace('/[^A-Z0-9]+/', '-', $stockPrefix);
            $stockPrefix = trim((string) $stockPrefix, '-');

            do {
                $randomNumber = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
                $stockNumber = $stockPrefix . '-' . $randomNumber;
            } while ($this->crrRepository->stockNumberExists($stockNumber));

            // --- Build main CRR data explicitly ---
            // Note: po_numbers & delivery_irregularities are cast as 'array' in the model,
            // so we pass them as arrays and Laravel auto-encodes to JSON for storage.
            $poNumbersRaw = $request->input('po_numbers');
            $poNumbersArray = $poNumbersRaw ? preg_split('/[\s,]+/', $poNumbersRaw, -1, PREG_SPLIT_NO_EMPTY) : null;

            $crrData = [
                'stock_number'            => $stockNumber,
                'registered_by'            => $request->user()->id,
                'vessel_name'             => $request->input('vessel_name'),
                'po_numbers'              => $poNumbersArray,
                'po_remarks'              => $request->input('po_remarks'),
                'content'                 => $request->input('content', 'Shipspares'),
                'first_mile_updates'      => $request->input('first_mile_updates'),
                'first_mile_comment'      => $request->input('first_mile_comment'),
                'supplier'                => $request->input('supplier'),
                'is_landed_goods'         => $request->boolean('is_landed_goods'),
                'expected_delivery_date'  => $request->input('expected_delivery_date'),
                'actual_delivery_date'    => $request->input('actual_delivery_date'),
                'supplier_reference'      => $request->input('supplier_reference'),
                'deadline_warehouse'      => $request->input('deadline_warehouse'),
                'internal_shipment'       => $request->input('internal_shipment'),
                'delivery_irregularities' => $request->input('delivery_irregularities') ?: null,
                'incoterm'                => $request->input('incoterm') ?: null,
                'hub_agent'               => $validated['hub_agent'],
                'location'                => $request->input('location'),
                'transit_type'            => $request->input('transit_type'),
                'transit_id'              => $request->input('transit_id'),
                'is_bonded_goods'         => $request->boolean('is_bonded_goods'),
                'customs_doc_type'        => $request->input('customs_doc_type'),
                'bonded_date'             => $request->input('bonded_date'),
                'customs_doc_reference'   => $request->input('customs_doc_reference'),
                'customs_lot_number'      => $request->input('customs_lot_number'),
                'country_of_origin'       => $request->input('country_of_origin'),
                'hs_code'                 => $request->input('hs_code'),
                'currency'                => $validated['currency'],
                'customs_value'           => $validated['customs_value'],
                'priority'                => $request->input('priority'),
                'flags'                   => Crr::defaultFlags(),
                'internal_comments'       => $request->input('internal_comments'),
                'customs_value_usd'       => $request->input('customs_value_usd'),
                'landed_from_vessel'      => $request->input('landed_from_vessel'),
            ];

            $status = (int) $request->input('status', Crr::STATUS_NEW);
            $crrData = array_merge($crrData, Crr::statusUpdateAttributes($status));

            $crr = $this->crrRepository->createCrr($crrData);

            // --- Save Package rows ---
            $packages = [];
            foreach ($request->input('packages', []) as $pkgData) {
                $packages[] = [
                    'crr_id'                   => $crr->id,
                    'length'                   => $pkgData['length']                   ?: null,
                    'width'                    => $pkgData['width']                    ?: null,
                    'height'                   => $pkgData['height']                   ?: null,
                    'weight'                   => $pkgData['weight']                   ?: null,
                    'cbm'                      => $pkgData['cbm']                      ?: null,
                    'warehouse_location'       => $pkgData['warehouse_location']       ?? null,
                    'remarks'                  => $pkgData['remarks']                  ?? null,
                    'is_dgr'                   => isset($pkgData['is_dgr']),
                    'dgr_description'          => $pkgData['dgr_description']          ?? null,
                    'un_number'                => $pkgData['un_number']                ?? null,
                    'dgr_class'                => $pkgData['dgr_class']                ?? null,
                    'is_delivery_irregularity' => isset($pkgData['is_delivery_irregularity']),
                    'delivery_irregularities'  => $pkgData['delivery_irregularities']  ?? null,
                    'is_not_stackable'         => isset($pkgData['is_not_stackable']),
                    'is_medicine'              => isset($pkgData['is_medicine']),
                    'is_xray'                  => isset($pkgData['is_xray']),
                ];
            }
            $this->crrRepository->storePackages($crr->id, $packages);

            // --- Save Cost rows (skip completely blank rows) ---
            $costs = [];
            foreach ($request->input('costs', []) as $costData) {
                $hasData = !empty($costData['type'])      || !empty($costData['carrier'])
                        || !empty($costData['net_value']) || !empty($costData['invoice_no'])
                        || !empty($costData['remarks'])   || !empty($costData['hub_agent']);

                if (!$hasData) {
                    continue;
                }

                $costs[] = [
                    'crr_id'        => $crr->id,
                    'type'          => $costData['type']          ?? null,
                    'carrier'       => $costData['carrier']       ?? null,
                    'net_value'     => $costData['net_value']     ?: null,
                    'currency'      => $costData['currency']      ?? null,
                    'net_value_usd' => $costData['net_value_usd'] ?: null,
                    'invoice_no'    => $costData['invoice_no']    ?: null,
                    'remarks'       => $costData['remarks']       ?: null,
                    'hub_agent'     => $costData['hub_agent']     ?: null,
                    'tag'           => $costData['tag']           ?: null,
                ];
            }
            $this->crrRepository->storeCosts($crr->id, $costs);

            $changeLogService->logCreated($crr);

            DB::commit();

            return redirect()->route('stocks')
                ->with('success', 'CRR created successfully! Stock number: ' . $crr->stock_number);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CRR save failed: ' . $e->getMessage() . ' (line ' . $e->getLine() . ')');
            return back()->withInput()->with('error', 'Failed to save CRR: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $crr = $this->crrRepository->findWithRelationsOrFail((int) $id, [
            'packages',
            'costs',
            'documents',
            'changeLogs.user',
            'registeredBy',
            'customerVessel.customer.responsible.accountManager',
        ]);
        
        extract($this->crrRepository->editReferenceData());
        $countries = CountryCache::active();
        $currencies = CountryCache::currencies();

        return view('Stock.edit', compact('crr', 'vessels', 'countries', 'currencies', 'hubs', 'agents', 'suppliers'));
    }

    public function update(Request $request, $id, CrrChangeLogService $changeLogService)
    {
        $crr = $this->crrRepository->findOrFail((int) $id);
        $crr->load(['packages', 'costs']);
        $changeLogSnapshot = $changeLogService->captureSnapshot($crr);

        $validated = $request->validate([
            'currency' => ['required', 'string', 'max:10'],
            'customs_value' => ['required', 'numeric'],
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.length' => ['required', 'numeric', 'gt:0'],
            'packages.*.width' => ['required', 'numeric', 'gt:0'],
            'packages.*.height' => ['required', 'numeric', 'gt:0'],
            'packages.*.weight' => ['required', 'numeric', 'gt:0'],
        ], [
            'currency.required' => 'Currency is required.',
            'customs_value.required' => 'Customs value is required.',
            'customs_value.numeric' => 'Customs value must be a number.',
            'packages.required' => 'Please add at least one package before saving.',
            'packages.min' => 'Please add at least one package before saving.',
            'packages.*.length.required' => 'Package length is required.',
            'packages.*.width.required' => 'Package width is required.',
            'packages.*.height.required' => 'Package height is required.',
            'packages.*.weight.required' => 'Package weight is required.',
            'packages.*.length.gt' => 'Package length must be greater than 0.',
            'packages.*.width.gt' => 'Package width must be greater than 0.',
            'packages.*.height.gt' => 'Package height must be greater than 0.',
            'packages.*.weight.gt' => 'Package weight must be greater than 0.',
        ]);

        DB::beginTransaction();
        try {
            // --- Update main CRR data ---
            $poNumbersRaw = $request->input('po_numbers');
            $poNumbersArray = $poNumbersRaw ? preg_split('/[\s,]+/', $poNumbersRaw, -1, PREG_SPLIT_NO_EMPTY) : null;

            $crrData = [
                'vessel_name'             => $request->input('vessel_name'),
                'po_numbers'              => $poNumbersArray,
                'po_remarks'              => $request->input('po_remarks'),
                'content'                 => $request->input('content', 'Shipspares'),
                'first_mile_updates'      => $request->input('first_mile_updates'),
                'first_mile_comment'      => $request->input('first_mile_comment'),
                'supplier'                => $request->input('supplier'),
                'is_landed_goods'         => $request->boolean('is_landed_goods'),
                'expected_delivery_date'  => $request->input('expected_delivery_date'),
                'actual_delivery_date'    => $request->input('actual_delivery_date'),
                'supplier_reference'      => $request->input('supplier_reference'),
                'deadline_warehouse'      => $request->input('deadline_warehouse'),
                'internal_shipment'       => $request->input('internal_shipment'),
                'delivery_irregularities' => $request->input('delivery_irregularities') ?: null,
                'incoterm'                => $request->input('incoterm') ?: null,
                'hub_agent'               => $request->input('hub_agent'),
                'location'                => $request->input('location'),
                'transit_type'            => $request->input('transit_type'),
                'transit_id'              => $request->input('transit_id'),
                'is_bonded_goods'         => $request->boolean('is_bonded_goods'),
                'customs_doc_type'        => $request->input('customs_doc_type'),
                'bonded_date'             => $request->input('bonded_date'),
                'customs_doc_reference'   => $request->input('customs_doc_reference'),
                'customs_lot_number'      => $request->input('customs_lot_number'),
                'country_of_origin'       => $request->input('country_of_origin'),
                'hs_code'                 => $request->input('hs_code'),
                'currency'                => $validated['currency'],
                'customs_value'           => $validated['customs_value'],
                'priority'                => $request->input('priority'),
                'internal_comments'       => $request->input('internal_comments'),
                'customs_value_usd'       => $request->input('customs_value_usd'),
                'landed_from_vessel'      => $request->input('landed_from_vessel'),
            ];

            $status = (int) $request->input('status', Crr::STATUS_PENDING);
            $crrData = array_merge($crrData, Crr::statusUpdateAttributes($status));

            $crr->update($crrData);

            // --- Sync Packages (Delete existing and recreate) ---
            $packages = [];
            foreach ($request->input('packages', []) as $pkgData) {
                $packages[] = [
                    'crr_id'                   => $crr->id,
                    'length'                   => $pkgData['length']                   ?: null,
                    'width'                    => $pkgData['width']                    ?: null,
                    'height'                   => $pkgData['height']                   ?: null,
                    'weight'                   => $pkgData['weight']                   ?: null,
                    'cbm'                      => $pkgData['cbm']                      ?: null,
                    'warehouse_location'       => $pkgData['warehouse_location']       ?? null,
                    'remarks'                  => $pkgData['remarks']                  ?? null,
                    'is_dgr'                   => isset($pkgData['is_dgr']),
                    'dgr_description'          => $pkgData['dgr_description']          ?? null,
                    'un_number'                => $pkgData['un_number']                ?? null,
                    'dgr_class'                => $pkgData['dgr_class']                ?? null,
                    'is_delivery_irregularity' => isset($pkgData['is_delivery_irregularity']),
                    'delivery_irregularities'  => $pkgData['delivery_irregularities']  ?? null,
                    'is_not_stackable'         => isset($pkgData['is_not_stackable']),
                    'is_medicine'              => isset($pkgData['is_medicine']),
                    'is_xray'                  => isset($pkgData['is_xray']),
                ];
            }
            $this->crrRepository->replacePackages($crr, $packages);

            // --- Sync Costs (Delete existing and recreate) ---
            $costs = [];
            foreach ($request->input('costs', []) as $costData) {
                $hasData = !empty($costData['type'])      || !empty($costData['carrier'])
                        || !empty($costData['net_value']) || !empty($costData['invoice_no'])
                        || !empty($costData['remarks'])   || !empty($costData['hub_agent']);

                if (!$hasData) {
                    continue;
                }

                $costs[] = [
                    'crr_id'        => $crr->id,
                    'type'          => $costData['type']          ?? null,
                    'carrier'       => $costData['carrier']       ?? null,
                    'net_value'     => $costData['net_value']     ?: null,
                    'currency'      => $costData['currency']      ?? null,
                    'net_value_usd' => $costData['net_value_usd'] ?: null,
                    'invoice_no'    => $costData['invoice_no']    ?: null,
                    'remarks'       => $costData['remarks']       ?: null,
                    'hub_agent'     => $costData['hub_agent']     ?: null,
                    'tag'           => $costData['tag']           ?? null,
                ];
            }
            $this->crrRepository->replaceCosts($crr, $costs);

            DB::commit();

            $crr->load(['packages', 'costs']);
            $changeLogService->logChangesFromSnapshot($crr, $changeLogSnapshot);

            return redirect()->back()
                ->with('success', 'CRR updated successfully! Stock number: ' . $crr->stock_number);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CRR update failed: ' . $e->getMessage() . ' (line ' . $e->getLine() . ')');
            return back()->withInput()->with('error', 'Failed to update CRR: ' . $e->getMessage());
        }
    }

    /**
     * Print stock list for selected CRRs.
     */
    public function printStockList(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $crrs = $this->crrRepository->selectedWithRelations($ids, ['packages', 'documents', 'customerVessel.customer']);

        if ($crrs->isEmpty()) {
            return "<script>alert('No items selected.'); window.close();</script>";
        }

        // Group by vessel
        $grouped = $crrs->groupBy('vessel_name');
        $selectedCustomerNames = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name);
        $uniqueCustomerNames = $selectedCustomerNames->filter()->unique()->values();
        $reportCustomerName = $selectedCustomerNames->contains(fn ($name) => blank($name))
            || $uniqueCustomerNames->count() !== 1
                ? 'all customers'
                : $uniqueCustomerNames->first();

        $pdf = Pdf::loadView('Stock.print', compact('grouped', 'reportCustomerName'))
                  ->setPaper('a4', 'portrait');

        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        // Keep page numbers inside the reserved footer band (A4 height ~842pt, bottom margin 28mm).
        $dompdf->getCanvas()->page_text(
            285,
            805,
            '{PAGE_NUM}/{PAGE_COUNT}',
            $font,
            8,
            [0, 0, 0]
        );

        return $pdf->stream('Stock-List-' . now()->format('YmdHis') . '.pdf');
    }

    /**
     * Print a single CRR detailed report.
     */
    public function showPrintCrr($id)
    {
        $crr = $this->crrRepository->findWithRelationsOrFail((int) $id, ['packages', 'documents', 'customerVessel.customer']);
        $this->applyPrintLocationOverride($crr);

        [$hubAgentCode, $hubAgentName] = $this->crrRepository->resolveHubAgentForPrint($crr);
        
        // MT Manager name from user if available, using placeholder like screenshot
        $mt_manager = "Clarence Ng Yao Wei, SIN"; 
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Stock.print-crr', compact(
            'crr',
            'mt_manager',
            'hubAgentCode',
            'hubAgentName'
        ))
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('CRR-' . $crr->stock_number . '.pdf');
    }

    public function showPrintLabels($id)
    {
        $crr = $this->crrRepository->findWithRelationsOrFail((int) $id, ['packages', 'customerVessel.customer']);
        $this->applyPrintLocationOverride($crr);

        [$hubAgentCode, $hubAgentName] = $this->crrRepository->resolveHubAgentForPrint($crr);
        $consignee = collect([$hubAgentCode, $hubAgentName])->filter()->join(' - ') ?: ($crr->hub_agent ?: '—');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Stock.print-labels', compact('crr', 'consignee'))
                  ->setPaper([0, 0, 425.20, 283.46]); // 150mm x 100mm
        return $pdf->stream('Labels-' . $crr->stock_number . '.pdf');
    }

    /**
     * Prefer the Physical Location currently entered on the edit form (query string),
     * so print works even before the CRR is saved.
     */
    private function applyPrintLocationOverride(Crr $crr): void
    {
        $location = request()->query('location');

        if (is_string($location)) {
            $location = trim($location);
            if ($location !== '') {
                $crr->location = $location;
            }
        }
    }

    /**
     * AJAX: Update the status of a CRR.
     */
    public function updateStatus(Request $request, $id, CrrChangeLogService $changeLogService)
    {
        try {
            $crr = $this->crrRepository->findOrFail((int) $id);

            $validated = $request->validate([
                'status' => ['required', 'integer', \Illuminate\Validation\Rule::in(array_keys(Crr::getStatusLabels()))],
            ]);

            $previousStatus = (int) $crr->status;
            $status = (int) $validated['status'];
            $crr->update(Crr::statusUpdateAttributes($status));

            if ($previousStatus !== (int) $crr->status) {
                $oldLabel = Crr::getStatusLabels()[$previousStatus] ?? (string) $previousStatus;
                $newLabel = Crr::getStatusLabels()[(int) $crr->status] ?? (string) $crr->status;
                $changeLogService->log($crr, 'Status edited', 'From ' . $oldLabel . ' to ' . $newLabel);
            }

            return response()->json([
                'success' => true,
                'status' => $crr->status,
                'status_label' => Crr::getStatusLabels()[$crr->status] ?? 'Unknown',
                'accept' => $crr->accept,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateFlags(Request $request, $id, CrrChangeLogService $changeLogService)
    {
        try {
            $crr = $this->crrRepository->findOrFail((int) $id);

            $flagsInput = $request->input('flags');
            if ($flagsInput !== null && ! is_array($flagsInput)) {
                $request->merge(['flags' => [$flagsInput]]);
            }

            $validated = $request->validate([
                'flags' => 'nullable|array',
                'flags.*' => ['string', \Illuminate\Validation\Rule::in(Crr::availableFlags())],
            ]);

            $previousFlags = $crr->flags ?? [];
            $flags = array_values(array_unique($validated['flags'] ?? []));
            $crr->update(['flags' => $flags]);

            $oldLabel = $previousFlags !== [] ? implode(', ', $previousFlags) : 'empty';
            $newLabel = $flags !== [] ? implode(', ', $flags) : 'empty';

            if ($oldLabel !== $newLabel) {
                $changeLogService->log($crr, 'Flags edited', 'From ' . $oldLabel . ' to ' . $newLabel);
            }

            return response()->json(['success' => true, 'flags' => $crr->flags ?? []]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateAccept(Request $request, $id, CrrChangeLogService $changeLogService)
    {
        try {
            $crr = $this->crrRepository->findOrFail((int) $id);
            $wasAccepted = (bool) $crr->accept;
            $crr->update([
                'accept' => true,
                'status' => Crr::STATUS_ACTIVE,
            ]);

            if (! $wasAccepted) {
                $changeLogService->logAccepted($crr);
            }

            return response()->json([
                'success' => true,
                'accept' => $crr->accept,
                'status' => $crr->status,
                'status_label' => Crr::getStatusLabels()[$crr->status] ?? 'Stock',
                'stock_number' => $crr->stock_number,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX: Upload a single document for a CRR.
     */
    public function uploadDocument(Request $request, $id, CrrChangeLogService $changeLogService)
    {
        $crr = $this->crrRepository->findOrFail((int) $id);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file provided'], 422);
        }

        $file = $request->file('file');
        $path = $file->store('crr_documents', 'private');

        if (! is_string($path) || $path === '' || $path === '0') {
            return response()->json([
                'error' => 'Could not store the file. Check that storage/app/private is writable by the web server.',
            ], 500);
        }

        $doc = $this->crrRepository->createDocument([
            'crr_id'    => $crr->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'Unspecified',
            'is_internal' => false,
        ]);

        $changeLogService->log($crr, 'Document added', $doc->file_name);

        return response()->json([
            'id'        => $doc->id,
            'file_name' => $doc->file_name,
            'file_url'  => $doc->fileUrl(),
            'file_type' => $doc->file_type,
            'is_internal' => (bool) $doc->is_internal,
            'date'      => $doc->created_at->format('d.m.Y'),
            'type_options' => CrrDocument::fileTypeOptionsWithCustom(),
        ]);
    }

    /**
     * Stream a private CRR document (auth required).
     */
    public function showDocument($crrId, $docId)
    {
        $doc = $this->crrRepository->findDocumentForCrrOrFail((int) $crrId, (int) $docId);

        return \App\Support\PrivateDisk::downloadResponse((string) $doc->file_path, (string) $doc->file_name);
    }

    public function updateDocumentType(Request $request, $docId, CrrChangeLogService $changeLogService)
    {
        $doc = $this->crrRepository->findDocumentOrFail((int) $docId);

        $validated = $request->validate([
            'file_type' => ['required', 'string', 'max:100', Rule::in(CrrDocument::fileTypeOptionsWithCustom())],
        ]);

        $fileType = trim($validated['file_type']);
        $previousType = $doc->file_type;
        $doc->update(['file_type' => $fileType]);

        if ($previousType !== $fileType && $doc->crr) {
            $changeLogService->log(
                $doc->crr,
                'Document type edited',
                $doc->file_name . ': From ' . ($previousType ?: 'empty') . ' to ' . $fileType
            );
        }

        return response()->json([
            'success' => true,
            'file_type' => $doc->file_type,
        ]);
    }

    public function updateDocumentInternal(Request $request, $docId, CrrChangeLogService $changeLogService)
    {
        $doc = $this->crrRepository->findDocumentOrFail((int) $docId);

        $validated = $request->validate([
            'is_internal' => ['required', 'boolean'],
        ]);

        $previous = (bool) $doc->is_internal;
        $doc->update(['is_internal' => $validated['is_internal']]);

        if ($previous !== (bool) $doc->is_internal && $doc->crr) {
            $changeLogService->log(
                $doc->crr,
                'Document internal flag edited',
                $doc->file_name . ': From ' . ($previous ? 'Internal' : 'Not internal') . ' to ' . ($doc->is_internal ? 'Internal' : 'Not internal')
            );
        }

        return response()->json([
            'success' => true,
            'is_internal' => (bool) $doc->is_internal,
        ]);
    }

    /**
     * AJAX: Delete a CRR document.
     */
    public function deleteDocument($docId, CrrChangeLogService $changeLogService)
    {
        try {
            $doc = $this->crrRepository->findDocumentOrFail((int) $docId);
            $crr = $doc->crr;
            $fileName = $doc->file_name;
            \App\Support\PrivateDisk::delete($doc->file_path);
            $doc->delete();

            if ($crr) {
                $changeLogService->log($crr, 'Document removed', $fileName);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function stockFollowUp(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $crrs = $this->crrRepository->paginateStockFollowUp($request->all(), $perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Stock.partials.follow-up-rows', compact('crrs'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $crrs])->render(),
                'total' => $crrs->total(),
            ]);
        }

        $options = $this->crrRepository->stockFollowUpFilterOptions();

        return view('Stock.stock-follow-up', array_merge(['crrs' => $crrs], $options));
    }

    public function pickupWorkList(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $handledByMap = $this->crrRepository->hubAgentHandledByMap();
        $crrs = $this->crrRepository->paginatePickupWorkList($request->all(), $handledByMap, $perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Stock.partials.pickup-rows', compact('crrs', 'handledByMap'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $crrs])->render(),
                'total' => $crrs->total(),
            ]);
        }

        $options = $this->crrRepository->pickupWorkListFilterOptions($handledByMap);

        return view('Stock.pickup-work-list', array_merge(
            ['crrs' => $crrs, 'handledByMap' => $handledByMap],
            $options,
        ));
    }

}
