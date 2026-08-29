<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shipments\BaseShipmentController;

use App\Support\CountryCache;
use App\Support\ListSearch;
use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentDocument;
use App\Services\CombinedPoPdfService;
use App\Services\ManifestMailService;
use App\Services\PreAlertMailService;
use App\Services\ShipmentChangeLogService;
use App\Services\ShipmentManifestService;
use App\Services\ShipmentPdfFingerprintService;
use App\Services\ShipmentPreAlertService;
use App\Services\ShipmentStockSnapshotService;
use App\Services\ShipmentTransitStockDuplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ShipmentController extends BaseShipmentController
{
    public function index(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $shipments = $this->shipmentRepository->paginateIndex($request->all(), $perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);
        $vesselCustomerMap = Shipment::batchResolveVesselCustomerNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.rows', compact('shipments', 'partyNames', 'vesselCustomerMap'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $shipments])->render(),
                'total' => $shipments->total(),
            ]);
        }

        $options = $this->shipmentRepository->indexFilterOptions();

        return view('Shipment.shipments', array_merge([
            'shipments' => $shipments,
            'partyNames' => $partyNames,
            'vesselCustomerMap' => $vesselCustomerMap,
        ], $options));
    }

    public function createPreAlert(
        Request $request,
        ManifestMailService $manifestMailService,
        PreAlertMailService $preAlertMailService,
        CombinedPoPdfService $combinedPoPdfService,
        ShipmentManifestService $manifestService,
        ShipmentPreAlertService $preAlertService,
        ShipmentStockSnapshotService $stockSnapshotService,
        ShipmentTransitStockDuplicationService $transitStockDuplicationService
    ) {
        view()->share('createPreAlertMode', true);

        $id = (int) $request->query('shipment', 0);
        if ($id > 0 && Shipment::query()->whereKey($id)->exists()) {
            return $this->edit(
                $id,
                $manifestMailService,
                $preAlertMailService,
                $combinedPoPdfService,
                $manifestService,
                $preAlertService,
                $stockSnapshotService,
                $transitStockDuplicationService
            );
        }

        $lookup = trim((string) $request->query('q', ''));
        if ($lookup !== '') {
            $matched = $this->shipmentRepository->findByShipmentNumberLookup($lookup);
            if ($matched) {
                return redirect()->route('create-pre-alert', ['shipment' => $matched->id]);
            }
        }

        $shipment = $this->makeBlankPreAlertShipment();
        if ($lookup !== '') {
            $shipment->shipment_number = $lookup;
        }
        $countries = CountryCache::active();
        extract($this->shipmentRepository->shipmentEditReferenceData());
        extract($this->irregularityFormOptions());

        return view('Shipment.edit', [
            'shipment' => $shipment,
            'partyNames' => [],
            'departureDisplay' => '',
            'consigneeDisplay' => '',
            'consigneeCode' => '',
            'consigneePartyCodes' => $consigneePartyCodes,
            'countries' => $countries,
            'crrs' => $crrs,
            'hubs' => $hubs,
            'agents' => $agents,
            'combinedPoDocuments' => collect(),
            'shipmentDocumentTypeOptions' => ShipmentDocument::fileTypeOptionsWithCustom(),
            'manifestMailPreview' => null,
            'preAlertMailPreview' => null,
            'irregularityTypeOptions' => $irregularityTypeOptions,
            'partyResponsibleOptions' => $partyResponsibleOptions,
            'consequenceOptions' => $consequenceOptions,
            'statusOptions' => $statusOptions,
            'transitDestinationStocksReady' => false,
            'createPreAlertMode' => true,
        ]);
    }

    public function transit(
        Request $request,
        ManifestMailService $manifestMailService,
        PreAlertMailService $preAlertMailService,
        CombinedPoPdfService $combinedPoPdfService,
        ShipmentManifestService $manifestService,
        ShipmentPreAlertService $preAlertService,
        ShipmentStockSnapshotService $stockSnapshotService,
        ShipmentTransitStockDuplicationService $transitStockDuplicationService
    ) {
        view()->share('transitMode', true);

        $id = (int) $request->query('shipment', 0);
        if ($id > 0 && Shipment::query()->whereKey($id)->exists()) {
            return $this->edit(
                $id,
                $manifestMailService,
                $preAlertMailService,
                $combinedPoPdfService,
                $manifestService,
                $preAlertService,
                $stockSnapshotService,
                $transitStockDuplicationService
            );
        }

        $lookup = trim((string) $request->query('q', ''));
        if ($lookup !== '') {
            $matched = $this->shipmentRepository->findByShipmentNumberLookup($lookup);
            if ($matched) {
                return redirect()->route('transit', ['shipment' => $matched->id]);
            }
        }

        $shipment = $this->makeBlankPreAlertShipment();
        if ($lookup !== '') {
            $shipment->shipment_number = $lookup;
        }
        $countries = CountryCache::active();
        extract($this->shipmentRepository->shipmentEditReferenceData());
        extract($this->irregularityFormOptions());

        return view('Shipment.edit', [
            'shipment' => $shipment,
            'partyNames' => [],
            'departureDisplay' => '',
            'consigneeDisplay' => '',
            'consigneeCode' => '',
            'consigneePartyCodes' => $consigneePartyCodes,
            'countries' => $countries,
            'crrs' => $crrs,
            'hubs' => $hubs,
            'agents' => $agents,
            'combinedPoDocuments' => collect(),
            'shipmentDocumentTypeOptions' => ShipmentDocument::fileTypeOptionsWithCustom(),
            'manifestMailPreview' => null,
            'preAlertMailPreview' => null,
            'irregularityTypeOptions' => $irregularityTypeOptions,
            'partyResponsibleOptions' => $partyResponsibleOptions,
            'consequenceOptions' => $consequenceOptions,
            'statusOptions' => $statusOptions,
            'transitDestinationStocksReady' => false,
            'transitMode' => true,
        ]);
    }

    private function makeBlankPreAlertShipment(): Shipment
    {
        $shipment = new Shipment([
            'shipment_number' => $this->shipmentNumberUserPrefix() . '-',
            'status' => '',
            'flags' => [],
        ]);

        foreach ([
            'crrs', 'flights', 'seaLegs', 'truckLegs', 'courierLegs',
            'releaseLegs', 'handCarryLegs', 'onBoardLegs', 'manifests',
            'preAlerts', 'documents', 'irregularities', 'changeLogs',
            'stockSnapshots',
        ] as $relation) {
            $shipment->setRelation($relation, collect());
        }

        $shipment->setRelation('creator', null);
        $shipment->setRelation('accountManager', null);

        return $shipment;
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $shipments = $this->shipmentRepository->searchByShipmentNumber($q);

        return response()->json([
            'results' => $shipments->map(function (Shipment $shipment) {
                return [
                    'id' => $shipment->id,
                    'text' => $shipment->shipment_number,
                ];
            })->values(),
        ]);
    }

    public function preAlertReminders(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $shipments = $this->shipmentRepository->paginatePreAlertReminders($request->all(), $perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.pre-alert-rows', compact('shipments', 'partyNames'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $shipments])->render(),
                'total' => $shipments->total(),
            ]);
        }

        $options = $this->shipmentRepository->followUpFilterOptions('not_completed');

        return view('Shipment.pre-alert-reminders', array_merge($options, [
            'shipments' => $shipments,
            'partyNames' => $partyNames,
        ]));
    }

    public function shipmentFollowUp(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $shipments = $this->shipmentRepository->paginateShipmentFollowUp($request->all(), $perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.follow-up-rows', compact('shipments', 'partyNames'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $shipments])->render(),
                'total' => $shipments->total(),
            ]);
        }

        $options = $this->shipmentRepository->followUpFilterOptions('follow_up');

        return view('Shipment.shipment-follow-up', array_merge($options, [
            'shipments' => $shipments,
            'partyNames' => $partyNames,
        ]));
    }

    public function costFollowUp()
    {
        $options = $this->shipmentRepository->followUpFilterOptions('not_cancelled');

        return view('Shipment.cost-follow-up', $options);
    }

    public function costFollowUpSearch(Request $request)
    {
        $accountManagers = array_values(array_filter((array) $request->input('account_manager', [])));
        $customers = array_values(array_filter((array) $request->input('customer', [])));
        $vessels = array_values(array_filter((array) $request->input('vessel', [])));
        $creators = array_values(array_filter((array) $request->input('created_by', [])));
        $statuses = array_values(array_filter((array) $request->input('status', [])));
        $shipmentNo = trim((string) $request->input('shipment_no', ''));
        $portDestination = trim((string) $request->input('port_destination', ''));

        $hasFilter = $accountManagers || $customers || $vessels || $creators || $statuses
            || ListSearch::prefix($shipmentNo) || ListSearch::prefix($portDestination);

        if (! $hasFilter) {
            return response()->json(['data' => []]);
        }

        $shipments = $this->shipmentRepository
            ->buildCostFollowUpSearchQuery($request->all())
            ->limit(250)
            ->get();
        $partyNames = Shipment::batchResolvePartyNames($shipments);
        $vesselCustomerMap = Shipment::batchResolveVesselCustomerNames($shipments);

        $data = $shipments->map(function (Shipment $shipment) use ($partyNames, $vesselCustomerMap) {
            $customerNames = $shipment->customerNamesFromVessels($vesselCustomerMap);
            $departureDisplay = $shipment->departure_port_code ?: $shipment->partyDisplay($shipment->departure, $partyNames);
            $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
            $etd = $shipment->service_etd;
            $eta = $shipment->service_eta;
            $delDate = $shipment->deadline_arrival;
            $delOverdue = $delDate && $delDate->copy()->startOfDay()->lte(now()->startOfDay());

            return [
                'id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'edit_url' => route('shipments.edit', $shipment->id),
                'has_open_irregularities' => $shipment->hasOpenIrregularities(),
                'customer' => $shipment->formatNamesDisplay($customerNames),
                'vessel' => $shipment->vessel_display,
                'service' => $shipment->service ?? '—',
                'service_reference' => $shipment->service_reference_display,
                'consignee' => $consigneeDisplay,
                'consignee_type' => explode(':', (string) $shipment->consignee, 2)[0],
                'departure' => $departureDisplay ?: '—',
                'destination' => $shipment->destination_display,
                'etd' => $etd?->format('d.m.Y') ?? '—',
                'eta' => $eta?->format('d.m.Y') ?? '—',
                'del_date' => $delDate?->format('d.m.Y') ?? '—',
                'del_overdue' => $delOverdue,
                'status' => $shipment->status ?? '—',
                'status_badge_class' => $shipment->statusBadgeClass(),
                'reminder_sent_count' => (int) ($shipment->reminder_sent_count ?? 0),
                'preview_url' => route('shipments.invoice-request-mail.preview', $shipment->id),
                'send_url' => route('shipments.invoice-request-mail.send', $shipment->id),
                'record_url' => route('shipments.pre-alert-reminder-mail.send', $shipment->id),
                'eml_url' => route('shipments.invoice-request-mail', $shipment->id),
                'eml_filename' => 'invoice-request-' . $shipment->shipment_number . '.eml',
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    public function markAsArrived($id, ShipmentStockSnapshotService $stockSnapshotService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ]);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'This shipment is already completed.',
            ], 422);
        }

        DB::transaction(function () use ($shipment, $stockSnapshotService) {
            $this->completeShipmentWithDestinationStocks(
                $shipment,
                $stockSnapshotService,
            );
        });

        $shipment = $shipment->fresh(['stockSnapshots']);
        $stockSnapshotService->applyResolvedStockCrrs($shipment);

        return response()->json([
            'success' => true,
            'message' => 'Shipment marked as arrived and completed.',
            'status' => $shipment->status,
        ]);
    }

    public function updateStatus(
        Request $request,
        $id,
        ShipmentChangeLogService $changeLogService,
        ShipmentStockSnapshotService $stockSnapshotService,
        ShipmentTransitStockDuplicationService $transitStockDuplicationService
    ) {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        $validated = $request->validate([
            // Create default is In process; In process is not available as a manual status pick.
            'status' => ['required', Rule::in(['In transit', 'Delivered', 'Completed', 'Cancelled'])],
        ]);

        $previousStatus = $shipment->status;
        DB::transaction(function () use (
            $shipment,
            $validated,
            $previousStatus,
            $changeLogService,
            $stockSnapshotService,
        ) {
            if ($validated['status'] === 'Completed' && $previousStatus !== 'Completed') {
                $shipment->loadMissing([
                    'crrs.packages',
                    'crrs.costs',
                    'crrs.documents',
                    'flights',
                    'seaLegs',
                    'truckLegs',
                    'courierLegs',
                ]);

                $this->completeShipmentWithDestinationStocks(
                    $shipment,
                    $stockSnapshotService,
                );
            } elseif ($validated['status'] === 'In transit' && $previousStatus !== 'In transit') {
                $shipment->loadMissing([
                    'crrs.packages',
                    'crrs.costs',
                    'crrs.documents',
                    'flights',
                    'seaLegs',
                    'truckLegs',
                    'courierLegs',
                ]);

                // Create destination copies but keep shipment linked to the completed originals.
                $transitStockDuplicationService->duplicateStocksForTransit($shipment, null, false);
                $shipment->update(['status' => 'In transit']);
            } else {
                $shipment->update(['status' => $validated['status']]);
            }

            if ($shipment->status === 'Cancelled') {
                $crrIds = $shipment->crrs()->pluck('crrs.id');

                if ($crrIds->isNotEmpty()) {
                    $this->shipmentRepository->updateCrrStatuses($crrIds->all(), [
                        'status' => Crr::STATUS_ACTIVE,
                        'internal_shipment' => null,
                    ]);
                }
            }

            if ($previousStatus !== $shipment->status) {
                $changeLogService->log(
                    $shipment,
                    'Status edited',
                    'From ' . ($previousStatus ?: 'empty') . ' to ' . $shipment->status
                );
            }
        });

        if ($previousStatus !== $shipment->status) {
            $actor = auth()->user()?->name ?? 'System';
            $when = now()->timezone(config('app.timezone', 'UTC'))->format('d.m.Y H:i');
            $message = 'Shipment [' . $shipment->shipment_number . '] status changed from '
                . ($previousStatus ?: 'empty') . ' to ' . $shipment->status
                . ' at ' . $when . ' by ' . $actor . '.';
            $linkUrl = route('shipments.edit', $shipment->id);

            $recipientIds = collect([$shipment->created_by])
                ->merge(
                    $this->shipmentRepository->adminUserIds()
                )
                ->filter()
                ->unique()
                ->reject(fn ($id) => (int) $id === (int) auth()->id())
                ->values();

            $notifier = app(\App\Services\UserNotificationService::class);
            $category = str_contains(strtolower($shipment->status), 'pickup')
                ? \App\Models\UserNotification::CATEGORY_PICKUPS
                : \App\Models\UserNotification::CATEGORY_OTHER;

            foreach ($recipientIds as $userId) {
                $notifier->notify(
                    (int) $userId,
                    $message,
                    $category,
                    $shipment->shipment_number,
                    $linkUrl,
                    null,
                    $shipment
                );
            }
        }

        return response()->json([
            'success' => true,
            'status' => $shipment->status,
        ]);
    }

    public function updateFlags(Request $request, $id, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        $flagsInput = $request->input('flags');
        if ($flagsInput !== null && ! is_array($flagsInput)) {
            $request->merge(['flags' => [$flagsInput]]);
        }

        $validated = $request->validate([
            'flags' => 'nullable|array',
            'flags.*' => ['string', Rule::in(Shipment::availableFlags())],
        ]);

        $previousFlags = $shipment->flags ?? [];
        $flags = array_values(array_unique($validated['flags'] ?? []));
        $flags = array_slice($flags, 0, 1);
        $shipment->update(['flags' => $flags]);

        $oldLabel = $previousFlags !== [] ? implode(', ', $previousFlags) : 'empty';
        $newLabel = $flags !== [] ? implode(', ', $flags) : 'empty';

        if ($oldLabel !== $newLabel) {
            $changeLogService->log($shipment, 'Flags edited', 'From ' . $oldLabel . ' to ' . $newLabel);
        }

        return response()->json([
            'success' => true,
            'flags' => $shipment->flags ?? [],
        ]);
    }

    public function store(Request $request, ShipmentChangeLogService $changeLogService)
    {
        $validated = $this->validateShipmentRequest($request);

        DB::beginTransaction();

        try {
            do {
                $shipmentNumber = $this->generateShipmentNumber();
            } while ($this->shipmentRepository->shipmentNumberExists($shipmentNumber));

            $shipment = $this->shipmentRepository->createShipment(array_merge(
                [
                    'shipment_number' => $shipmentNumber,
                    'created_by' => auth()->id(),
                    'flags' => Shipment::defaultFlags(),
                ],
                $this->buildShipmentAttributes($request, $validated)
            ));

            $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
            if (!empty($crrIds)) {
                $shipment->crrs()->attach($crrIds);
            }

            $this->syncCrrInternalShipments($shipment, $crrIds);

            $this->syncIrregularities($shipment, $validated['irregularities'] ?? []);

            $this->syncFlights($shipment, $validated['flights'] ?? [], $validated['service'] ?? null);

            $this->syncSeaLegs($shipment, $validated['sea_legs'] ?? [], $validated['service'] ?? null);

            $this->syncTruckLegs($shipment, $validated['truck_legs'] ?? [], $validated['service'] ?? null);

            $this->syncCourierLegs($shipment, $validated['courier_legs'] ?? [], $validated['service'] ?? null);

            $this->syncReleaseLegs($shipment, $validated['release_legs'] ?? [], $validated['service'] ?? null);

            $this->syncHandCarryLegs($shipment, $request->input('hand_carry_legs', []), $validated['service'] ?? null);

            $this->syncOnBoardLegs($shipment, $validated['on_board_legs'] ?? [], $validated['service'] ?? null);

            $changeLogService->logCreated($shipment);

            DB::commit();

            return redirect()
                ->route('shipments.edit', $shipment->id)
                ->with('success', 'Shipment ' . $shipment->shipment_number . ' created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Shipment store failed: ' . $e->getMessage() . ' (line ' . $e->getLine() . ')');

            return back()
                ->withInput()
                ->with('error', 'Failed to create shipment: ' . $e->getMessage());
        }
    }

    public function edit($id, ManifestMailService $manifestMailService, PreAlertMailService $preAlertMailService, CombinedPoPdfService $combinedPoPdfService, ShipmentManifestService $manifestService, ShipmentPreAlertService $preAlertService, ShipmentStockSnapshotService $stockSnapshotService, ShipmentTransitStockDuplicationService $transitStockDuplicationService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.customerVessel.customer.primaryAddress.country',
            'crrs.customerVessel.customer.responsible.accountManager.office',
            'crrs.customerVessel.customer.responsible.salesManager',
            'crrs.customerVessel.customer.group',
            'stockSnapshots',
            'accountManager.office',
            'creator',
            'irregularities',
            'documents',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
            'manifests',
            'preAlerts',
            'changeLogs.user',
        ]);

        $stockSnapshotService->applyResolvedStockCrrs($shipment);

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureDisplay = $shipment->partyDisplay($shipment->departure, $partyNames);
        $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);

        $countries = CountryCache::active();
        extract($this->shipmentRepository->shipmentEditReferenceData());

        extract($this->irregularityFormOptions());

        $combinedPoDocuments = $combinedPoPdfService->documentsForShipment($shipment);
        $shipmentDocumentTypeOptions = ShipmentDocument::fileTypeOptionsWithCustom();

        if ($shipment->manifests->isEmpty() && $shipment->crrs->isNotEmpty()) {
            $manifestService->generate($shipment);
            $shipment->load('manifests');
        }

        if ($shipment->preAlerts->isEmpty() && $shipment->crrs->isNotEmpty() && \App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            $preAlertService->generate($shipment);
            $shipment->load('preAlerts');
        }

        $manifestMailPreview = null;
        try {
            $manifestMailPreview = $manifestMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable) {
            $manifestMailPreview = null;
        }

        $preAlertMailPreview = null;
        try {
            $preAlertMailPreview = $preAlertMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable) {
            $preAlertMailPreview = null;
        }

        $consigneeCode = $transitStockDuplicationService->resolveConsigneePartyCode($shipment->consignee) ?? '';
        $transitDestinationStocksReady = $transitStockDuplicationService->hasDestinationStocksForShipment($shipment);

        return view('Shipment.edit', compact(
            'shipment',
            'partyNames',
            'departureDisplay',
            'consigneeDisplay',
            'consigneeCode',
            'consigneePartyCodes',
            'countries',
            'crrs',
            'hubs',
            'agents',
            'combinedPoDocuments',
            'shipmentDocumentTypeOptions',
            'manifestMailPreview',
            'preAlertMailPreview',
            'irregularityTypeOptions',
            'partyResponsibleOptions',
            'consequenceOptions',
            'statusOptions',
            'transitDestinationStocksReady',
        ));
    }

    public function completePreAlert(Request $request, $id, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, ['crrs', 'preAlerts']);

        $validated = $request->validate([
            'shipment_number' => 'required|string|max:255',
        ]);

        if ($validated['shipment_number'] !== $shipment->shipment_number) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment number does not match.',
            ], 422);
        }

        if (! $shipment->preAlerts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Generate a pre-alert PDF before completing.',
            ], 422);
        }

        if ($shipment->status !== 'In process') {
            return response()->json([
                'success' => false,
                'message' => 'Pre-alert has already been completed for this shipment.',
            ], 422);
        }

        $previousStatus = $shipment->status;

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status' => 'In transit']);

            $crrIds = $shipment->crrs()->pluck('crrs.id')->all();
            if ($crrIds !== []) {
                $this->shipmentRepository->updateCrrStatuses($crrIds, [
                    'status' => Crr::STATUS_COMPLETED,
                ]);
            }
        });

        $shipment = $shipment->fresh('crrs');

        if ($previousStatus !== 'In transit') {
            $changeLogService->log(
                $shipment,
                'Pre-alert completed',
                'Status changed from ' . ($previousStatus ?: 'empty') . ' to In transit'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Pre-alert completed. Shipment status is now In transit.',
            'status' => $shipment->status,
            'stocks' => $shipment->crrs->map(fn (Crr $crr) => [
                'id' => $crr->id,
                'status' => Crr::getStatusLabels()[$crr->status] ?? 'Completed',
            ])->values(),
        ]);
    }

    public function finalize(Request $request, $id, ShipmentStockSnapshotService $stockSnapshotService, ShipmentTransitStockDuplicationService $transitStockDuplicationService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ]);

        $validated = $request->validate([
            'shipment_number' => 'required|string|max:255',
            'consignee_code' => 'nullable|string|max:255',
            'action' => 'required|in:complete,transit',
        ]);

        if ($validated['shipment_number'] !== $shipment->shipment_number) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment number does not match.',
            ], 422);
        }

        if ($validated['action'] === 'complete') {
            DB::transaction(function () use ($shipment, $stockSnapshotService) {
                $this->completeShipmentWithDestinationStocks(
                    $shipment,
                    $stockSnapshotService,
                );
            });

            $shipment = $shipment->fresh(['stockSnapshots']);
            $stockSnapshotService->applyResolvedStockCrrs($shipment);

            return response()->json([
                'success' => true,
                'message' => 'Shipment and selected stocks completed successfully. Use Transit to generate destination stocks when needed.',
                'status' => $shipment->status,
                'stocks' => $shipment->crrs->map(fn (Crr $crr) => [
                    'id' => $crr->id,
                    'status' => Crr::getStatusLabels()[$crr->status] ?? 'Completed',
                ])->values(),
            ]);
        }

        DB::transaction(function () use ($shipment, $validated, $transitStockDuplicationService) {
            // Create destination stock copies, but keep shipment_crr on the completed originals
            // so the shipment edit page continues to show the old completed stocks.
            $transitStockDuplicationService->duplicateStocksForTransit(
                $shipment,
                $validated['consignee_code'] ?? null,
                false
            );

            // Finalize → Transit creates destination stocks and keeps shipment Completed
            // (not "In transit"). Complete is still required first.
            $shipment->update([
                'status' => 'Completed',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Destination stocks created. Shipment status is Completed.',
            'status' => 'Completed',
            'consignee_code' => $validated['consignee_code'] ?? null,
            'reload' => true,
        ]);
    }

    /**
     * Shared Complete path: snapshot → Completed + source CRRs (no destination stock copies).
     *
     * Destination stock duplicates are created only on Finalize → Transit (or manual In transit status).
     */
    protected function completeShipmentWithDestinationStocks(
        Shipment $shipment,
        ShipmentStockSnapshotService $stockSnapshotService,
    ): void {
        $stockSnapshotService->snapshotShipmentStocks($shipment);

        $shipment->update(['status' => 'Completed']);

        $crrIds = $shipment->crrs()->pluck('crrs.id')->all();
        if (!empty($crrIds)) {
            $this->shipmentRepository->updateCrrStatuses($crrIds, [
                'status' => Crr::STATUS_COMPLETED,
            ]);
        }
    }

    public function update(Request $request, $id, ShipmentPdfFingerprintService $fingerprintService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);
        $validated = $this->validateShipmentRequest($request, $shipment);

        $shipment->load([
            'crrs',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
            'accountManager',
        ]);
        $partyNamesBefore = Shipment::batchResolvePartyNames(collect([$shipment]));
        $changeLogSnapshot = $changeLogService->captureSnapshot($shipment);

        $fingerprintService->prepareForFingerprint($shipment);
        $manifestFingerprintBefore = $fingerprintService->manifestFingerprint($shipment);
        $preAlertFingerprintBefore = $fingerprintService->preAlertFingerprint($shipment);
        $previousCrrIds = $shipment->crrs->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        DB::beginTransaction();

        try {
            $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

            if (! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                $shipment->crrs()->sync($crrIds);

                $this->syncCrrInternalShipments($shipment, $crrIds, $previousCrrIds);
            }

            $this->syncIrregularities($shipment, $validated['irregularities'] ?? []);

            $this->syncFlights($shipment, $validated['flights'] ?? [], $validated['service'] ?? null);

            $this->syncSeaLegs($shipment, $validated['sea_legs'] ?? [], $validated['service'] ?? null);

            $this->syncTruckLegs($shipment, $validated['truck_legs'] ?? [], $validated['service'] ?? null);

            $this->syncCourierLegs($shipment, $validated['courier_legs'] ?? [], $validated['service'] ?? null);

            $this->syncReleaseLegs($shipment, $validated['release_legs'] ?? [], $validated['service'] ?? null);

            $this->syncHandCarryLegs($shipment, $request->input('hand_carry_legs', []), $validated['service'] ?? null);

            $this->syncOnBoardLegs($shipment, $validated['on_board_legs'] ?? [], $validated['service'] ?? null);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Shipment update failed: ' . $e->getMessage() . ' (line ' . $e->getLine() . ')');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update shipment: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update shipment: ' . $e->getMessage());
        }

        $freshShipment = $shipment->fresh($fingerprintService->relations());
        $fingerprintService->prepareForFingerprint($freshShipment);

        $freshShipment->load([
            'crrs',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
            'accountManager',
        ]);
        $changeLogService->logChangesFromSnapshot($freshShipment, $changeLogSnapshot, $partyNamesBefore);

        $currentCrrIds = $freshShipment->crrs->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $previousSorted = $previousCrrIds;
        $currentSorted = $currentCrrIds;
        sort($previousSorted);
        sort($currentSorted);
        $stocksChanged = $previousSorted !== $currentSorted;

        if (
            $stocksChanged
            || $fingerprintService->manifestFingerprint($freshShipment) !== $manifestFingerprintBefore
        ) {
            try {
                $manifest = app(ShipmentManifestService::class)->generate($freshShipment);
                if ($manifest) {
                    $changeLogService->log(
                        $freshShipment,
                        $manifest->version > 1 ? 'Revision created' : 'Manifest generated',
                        $manifest->version > 1 ? 'Revision ' . $manifest->version : $manifest->file_name . '.pdf'
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Manifest generation after shipment save failed: ' . $e->getMessage());
            }
        }

        $shouldGeneratePreAlert = \App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($freshShipment)
            && $fingerprintService->preAlertFingerprint($freshShipment) !== $preAlertFingerprintBefore;

        if ($shouldGeneratePreAlert) {
            try {
                $preAlert = app(ShipmentPreAlertService::class)->generate($freshShipment);
                if ($preAlert) {
                    $changeLogService->log(
                        $freshShipment,
                        $preAlert->version > 1 ? 'Pre-alert revision created' : 'Pre-alert generated',
                        $preAlert->version > 1 ? 'Revision ' . $preAlert->version : $preAlert->file_name . '.pdf'
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Pre-alert generation after shipment save failed: ' . $e->getMessage());
            }
        }

        $message = 'Shipment ' . $shipment->shipment_number . ' updated successfully.';

        if ($request->expectsJson()) {
            $freshShipment->loadMissing(['manifests', 'preAlerts']);

            return response()->json([
                'success' => true,
                'message' => $message,
                'manifest_mail_pending' => $freshShipment->needsManifestMailSend(),
                'pre_alert_mail_pending' => $freshShipment->needsPreAlertMailSend(),
                'has_pre_alert_pdf' => $freshShipment->preAlerts()->exists(),
            ]);
        }

        if ($request->input('return_to') === 'create-pre-alert') {
            return redirect()
                ->route('create-pre-alert', ['shipment' => $shipment->id])
                ->with('success', $message);
        }

        if ($request->input('return_to') === 'transit') {
            return redirect()
                ->route('transit', ['shipment' => $shipment->id])
                ->with('success', $message);
        }

        return redirect()
            ->route('shipments.edit', $shipment->id)
            ->with('success', $message);
    }

}
