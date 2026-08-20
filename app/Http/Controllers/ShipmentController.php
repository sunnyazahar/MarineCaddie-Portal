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
                'pagination' => (string) $shipments->links(),
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

    public function preAlertReminders(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $shipments = $this->shipmentRepository->paginatePreAlertReminders($request->all(), $perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.pre-alert-rows', compact('shipments', 'partyNames'))->render(),
                'pagination' => (string) $shipments->links(),
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
                'pagination' => (string) $shipments->links(),
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
            $stockSnapshotService->snapshotShipmentStocks($shipment);

            $shipment->update(['status' => 'Completed']);

            $crrIds = $shipment->crrs()->pluck('crrs.id')->all();
            if (!empty($crrIds)) {
                $this->shipmentRepository->updateCrrStatuses($crrIds, [
                    'status' => Crr::STATUS_COMPLETED,
                ]);
            }
        });

        $shipment = $shipment->fresh(['stockSnapshots']);
        $stockSnapshotService->applyResolvedStockCrrs($shipment);

        return response()->json([
            'success' => true,
            'message' => 'Shipment marked as arrived and completed.',
            'status' => $shipment->status,
        ]);
    }

    public function updateStatus(Request $request, $id, ShipmentChangeLogService $changeLogService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['In process', 'In transit', 'Delivered', 'Completed', 'Cancelled'])],
        ]);

        $previousStatus = $shipment->status;
        DB::transaction(function () use ($shipment, $validated, $previousStatus, $changeLogService) {
            $shipment->update(['status' => $validated['status']]);

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
            'statusOptions'
        ));
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

        if ($validated['action'] === 'transit' && $shipment->status !== 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Shipment must be completed before moving to transit.',
            ], 422);
        }

        if ($validated['action'] === 'complete') {
            DB::transaction(function () use ($shipment, $stockSnapshotService) {
                $stockSnapshotService->snapshotShipmentStocks($shipment);

                $shipment->update(['status' => 'Completed']);

                $crrIds = $shipment->crrs()->pluck('crrs.id')->all();
                if (!empty($crrIds)) {
                    $this->shipmentRepository->updateCrrStatuses($crrIds, [
                        'status' => Crr::STATUS_COMPLETED,
                    ]);
                }
            });

            $shipment = $shipment->fresh(['stockSnapshots']);
            $stockSnapshotService->applyResolvedStockCrrs($shipment);

            return response()->json([
                'success' => true,
                'message' => 'Shipment and selected stocks completed successfully.',
                'status' => $shipment->status,
                'stocks' => $shipment->crrs->map(fn (Crr $crr) => [
                    'id' => $crr->id,
                    'status' => Crr::getStatusLabels()[$crr->status] ?? 'Completed',
                ])->values(),
            ]);
        }

        DB::transaction(function () use ($shipment, $validated, $transitStockDuplicationService) {
            $transitStockDuplicationService->duplicateStocksForTransit(
                $shipment,
                $validated['consignee_code'] ?? null
            );

            $shipment->update([
                'status' => 'In transit',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Shipment moved to transit successfully. Duplicate stocks created.',
            'status' => 'In transit',
            'consignee_code' => $validated['consignee_code'] ?? null,
            'reload' => true,
        ]);
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
        $serviceDetailsFingerprintBefore = $fingerprintService->serviceDetailsFingerprint($shipment);
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

        if (
            \App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($freshShipment)
            && (
                $stocksChanged
                || $fingerprintService->preAlertFingerprint($freshShipment) !== $preAlertFingerprintBefore
                || $fingerprintService->serviceDetailsFingerprint($freshShipment) !== $serviceDetailsFingerprintBefore
            )
        ) {
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
            ]);
        }

        return redirect()
            ->route('shipments.edit', $shipment->id)
            ->with('success', $message);
    }

}
