<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Support\CountryCache;
use App\Support\ListSearch;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Crr;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Supplier;
use App\Models\Shipment;
use App\Models\ShipmentDocument;
use App\Models\ShipmentManifest;
use App\Models\ShipmentPreAlert;
use App\Models\ShipmentPreAlertReminderSend;
use App\Models\ShipmentFlight;
use App\Models\ShipmentIrregularity;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentOnBoardLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentTruckLeg;
use App\Models\User;
use App\Services\CombinedPoPdfService;
use App\Services\ManifestMailService;
use App\Services\PreAlertMailService;
use App\Services\PreAlertReminderMailService;
use App\Services\ShipmentManifestPdfBuilder;
use App\Services\ShipmentChangeLogService;
use App\Services\ShipmentMailDispatchService;
use App\Services\ShipmentManifestService;
use App\Services\ShipmentPdfFingerprintService;
use App\Services\ShipmentPreAlertService;
use App\Services\ShipmentStockSnapshotService;
use App\Services\ShipmentTransitStockDuplicationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));

        $query = Shipment::query()
            ->with([
                'crrs.customerVessel.customer',
                'accountManager.office',
                'creator',
                'irregularities',
                'flights',
                'seaLegs',
                'truckLegs',
                'courierLegs',
                'releaseLegs',
            ]);

        $this->applyShipmentIndexFilters($query, $request);

        $shipments = $query->orderByDesc('id')->paginate($perPage);
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

        $customers = DB::table('customers')
            ->select('customer_name')
            ->whereNotNull('customer_name')
            ->distinct()
            ->orderBy('customer_name')
            ->pluck('customer_name');

        $vessels = DB::table('customer_vessels')
            ->select('vessel')
            ->whereNotNull('vessel')
            ->where('vessel', '!=', '')
            ->distinct()
            ->orderBy('vessel')
            ->pluck('vessel');

        $services = Shipment::query()->whereNotNull('service')->distinct()->orderBy('service')->pluck('service');
        $statuses = Shipment::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
        $departureOptions = Shipment::query()->whereNotNull('departure_port_code')->distinct()->orderBy('departure_port_code')->pluck('departure_port_code');

        $accountManagers = Contact::query()
            ->whereIn('id', Shipment::query()->whereNotNull('account_manager_id')->distinct()->pluck('account_manager_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'office_id']);

        $creators = User::query()
            ->whereIn('id', Shipment::query()->whereNotNull('created_by')->distinct()->pluck('created_by'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $offices = Office::query()
            ->whereIn('id', $accountManagers->pluck('office_id')->filter()->unique())
            ->orderBy('office_name')
            ->get(['id', 'office_name']);

        return view('Shipment.shipments', compact(
            'shipments',
            'partyNames',
            'vesselCustomerMap',
            'customers',
            'vessels',
            'services',
            'statuses',
            'departureOptions',
            'accountManagers',
            'creators',
            'offices'
        ));
    }

    public function preAlertReminders(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));
        $baseQuery = Shipment::query()->where('status', '!=', 'Completed');

        $query = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'irregularities',
        ])
            ->withCount('preAlertReminderSends as reminder_sent_count')
            ->where('status', '!=', 'Completed');

        $this->applyShipmentFollowUpFilters($query, $request);

        $shipments = $query->orderByDesc('id')->paginate($perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.pre-alert-rows', compact('shipments', 'partyNames'))->render(),
                'pagination' => (string) $shipments->links(),
                'total' => $shipments->total(),
            ]);
        }

        $options = $this->shipmentFollowUpFilterOptions($baseQuery);

        return view('Shipment.pre-alert-reminders', array_merge($options, [
            'shipments' => $shipments,
            'partyNames' => $partyNames,
        ]));
    }

    public function shipmentFollowUp(Request $request)
    {
        $perPage = max(25, min(100, (int) $request->query('per_page', 50)));
        $baseQuery = Shipment::query()->whereNotIn('status', ['Completed', 'Draft']);

        $query = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ])
            ->withMax('preAlertReminderSends as last_reminder_sent_at', 'created_at')
            ->whereNotIn('status', ['Completed', 'Draft']);

        $this->applyShipmentFollowUpFilters($query, $request);

        $shipments = $query->orderByDesc('id')->paginate($perPage);
        $shipmentRows = $shipments->getCollection();
        $partyNames = Shipment::batchResolvePartyNames($shipmentRows);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Shipment.partials.follow-up-rows', compact('shipments', 'partyNames'))->render(),
                'pagination' => (string) $shipments->links(),
                'total' => $shipments->total(),
            ]);
        }

        $options = $this->shipmentFollowUpFilterOptions($baseQuery);

        return view('Shipment.shipment-follow-up', array_merge($options, [
            'shipments' => $shipments,
            'partyNames' => $partyNames,
        ]));
    }

    public function costFollowUp()
    {
        $baseQuery = Shipment::query()->where('status', '!=', 'Cancelled');
        $options = $this->shipmentFollowUpFilterOptions($baseQuery);

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

        $query = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ])
            ->withCount('preAlertReminderSends as reminder_sent_count')
            ->where('status', '!=', 'Cancelled');

        $this->applyShipmentFollowUpFilters($query, $request);

        $shipments = $query->orderByDesc('id')->limit(250)->get();
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

    public function preAlertReminderMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
        ])->findOrFail($id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        try {
            $preview = $reminderMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.pre-alert-reminder-mail', $shipment->id),
            'eml_filename' => 'pre-alert-reminder-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendPreAlertReminderMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'pre_alert_reminder',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminder email sent successfully.',
            'queued' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count() + 1,
        ]);
    }

    public function preAlertReminderMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $eml = $reminderMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert reminder mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'pre-alert-reminder-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function deliveryStatusReminderMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive delivery status reminders.',
            ], 422);
        }

        try {
            $preview = $reminderMailService->buildDeliveryStatusPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.delivery-status-reminder-mail', $shipment->id),
            'eml_filename' => 'delivery-status-request-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendDeliveryStatusReminderMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive delivery status reminders.',
            ], 422);
        }

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'delivery_status',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminder email sent successfully.',
            'queued' => true,
        ]);
    }

    public function deliveryStatusReminderMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $eml = $reminderMailService->buildDeliveryStatusEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Delivery status reminder mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'delivery-status-request-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function invoiceRequestMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $preview = $reminderMailService->buildInvoiceRequestPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.invoice-request-mail', $shipment->id),
            'eml_filename' => 'invoice-request-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendInvoiceRequestMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = Shipment::findOrFail($id);
        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'invoice_request',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoice request email sent successfully.',
            'queued' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count() + 1,
        ]);
    }

    public function invoiceRequestMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $eml = $reminderMailService->buildInvoiceRequestEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Invoice request mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'invoice-request-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function recordPreAlertReminderSend($id)
    {
        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        ShipmentPreAlertReminderSend::create([
            'shipment_id' => $shipment->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count(),
        ]);
    }

    public function markAsArrived($id, ShipmentStockSnapshotService $stockSnapshotService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ])->findOrFail($id);

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
                Crr::whereIn('id', $crrIds)->update([
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
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['In process', 'In transit', 'Delivered', 'Completed', 'Cancelled'])],
        ]);

        $previousStatus = $shipment->status;
        DB::transaction(function () use ($shipment, $validated, $previousStatus, $changeLogService) {
            $shipment->update(['status' => $validated['status']]);

            if ($shipment->status === 'Cancelled') {
                $crrIds = $shipment->crrs()->pluck('crrs.id');

                if ($crrIds->isNotEmpty()) {
                    Crr::query()->whereIn('id', $crrIds)->update([
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
                    \App\Models\User::query()->where('role', 'Admin')->pluck('id')
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
        $shipment = Shipment::findOrFail($id);

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
            } while (Shipment::where('shipment_number', $shipmentNumber)->exists());

            $shipment = Shipment::create(array_merge(
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
        $shipment = Shipment::with([
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
        ])->findOrFail($id);

        $stockSnapshotService->applyResolvedStockCrrs($shipment);

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureDisplay = $shipment->partyDisplay($shipment->departure, $partyNames);
        $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);

        $countries = CountryCache::active();
        $crrs = Crr::with(['packages', 'documents', 'customerVessel.customer'])
            ->selectableForShipment()
            ->latest()
            ->get();
        $hubs = Hub::orderBy('hub_name')->get();
        $agents = Agent::with('country')->orderBy('agent_name')->get();

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
        $consigneePartyCodes = [];
        foreach ($hubs as $hub) {
            $code = trim((string) ($hub->code ?? ''));
            if ($code !== '') {
                $consigneePartyCodes['hub:' . $hub->id] = $code;
            }
        }
        foreach ($agents as $agent) {
            $code = trim((string) ($agent->code ?? ''));
            if ($code !== '') {
                $consigneePartyCodes['agent:' . $agent->id] = $code;
            }
        }

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

    public function generateManifest(Request $request, $id, ShipmentManifestService $manifestService, CombinedPoPdfService $combinedPoPdfService, ShipmentPdfFingerprintService $fingerprintService)
    {
        $shipment = Shipment::with(['manifests', 'documents', 'crrs'])->findOrFail($id);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->manifestGenerationRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for manifest generation.',
                'errors' => $e->errors(),
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $manifestFingerprintBefore = $fingerprintService->manifestFingerprint($shipment);

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Manifest autosave failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before generating manifest.',
            ], 500);
        }

        $shipment = $shipment->fresh(array_merge(
            $fingerprintService->relations(),
            ['documents', 'manifests']
        ));

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item to generate a manifest.',
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $manifestCreated = false;

        if ($fingerprintService->manifestFingerprint($shipment) !== $manifestFingerprintBefore) {
            try {
                $manifest = $manifestService->generate($shipment);
                $manifestCreated = $manifest !== null;
            } catch (\Throwable $e) {
                Log::error('Manifest generation failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Could not generate manifest PDF. Please try again.',
                ], 500);
            }
        }

        $manifests = $shipment->manifests()->orderBy('version')->get();

        return response()->json([
            'success' => true,
            'created' => $manifestCreated,
            'manifests' => $manifests->map(fn (ShipmentManifest $manifest) => $this->manifestToArray($manifest)),
            'document_count' => $this->shipmentDocumentCount($shipment, $combinedPoPdfService),
            'manifest_mail_pending' => $shipment->fresh('manifests')->needsManifestMailSend(),
        ]);
    }

    public function generatePreAlert(Request $request, $id, ShipmentPreAlertService $preAlertService, CombinedPoPdfService $combinedPoPdfService, ShipmentPdfFingerprintService $fingerprintService)
    {
        $shipment = Shipment::with(['preAlerts', 'documents', 'crrs', 'manifests'])->findOrFail($id);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->preAlertMailRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for pre-alert generation.',
                'errors' => $e->errors(),
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $preAlertFingerprintBefore = $fingerprintService->preAlertFingerprint($shipment);

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }

                $service = $validated['service'] ?? $shipment->service;
                $this->syncFlights($shipment, $validated['flights'] ?? [], $service);
                $this->syncSeaLegs($shipment, $validated['sea_legs'] ?? [], $service);
                $this->syncTruckLegs($shipment, $validated['truck_legs'] ?? [], $service);
                $this->syncCourierLegs($shipment, $validated['courier_legs'] ?? [], $service);
                $this->syncReleaseLegs($shipment, $validated['release_legs'] ?? [], $service);
                $this->syncHandCarryLegs($shipment, $request->input('hand_carry_legs', []), $service);
                $this->syncOnBoardLegs($shipment, $validated['on_board_legs'] ?? [], $service);
            });
        } catch (\Throwable $e) {
            Log::error('Pre-alert autosave failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save service details before generating pre-alert PDF.',
            ], 500);
        }

        $shipment = $shipment->fresh(array_merge(
            $fingerprintService->relations(),
            ['documents', 'manifests', 'preAlerts']
        ));

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item to generate a pre-alert.',
            ], 422);
        }

        if (!\App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Add service details before generating a pre-alert PDF.',
                'code' => 'missing_service_details',
            ], 422);
        }

        $fingerprintService->prepareForFingerprint($shipment);
        $preAlertCreated = false;

        if (
            $fingerprintService->preAlertFingerprint($shipment) !== $preAlertFingerprintBefore
            || ! $shipment->preAlerts()->exists()
        ) {
            try {
                $preAlert = $preAlertService->generate($shipment);
                $preAlertCreated = $preAlert !== null;
            } catch (\Throwable $e) {
                Log::error('Pre-alert generation failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Could not generate pre-alert PDF. Please try again.',
                ], 500);
            }
        }

        $preAlerts = $shipment->preAlerts()->orderBy('version')->get();

        return response()->json([
            'success' => true,
            'created' => $preAlertCreated,
            'pre_alerts' => $preAlerts->map(fn (ShipmentPreAlert $preAlert) => $this->preAlertToArray($preAlert)),
            'document_count' => $this->shipmentDocumentCount($shipment, $combinedPoPdfService),
            'pre_alert_mail_pending' => $shipment->fresh('preAlerts')->needsPreAlertMailSend(),
        ]);
    }

    public function showManifest($shipmentId, $manifestId, ShipmentManifestService $manifestService)
    {
        $manifest = ShipmentManifest::with('shipment')->where('shipment_id', $shipmentId)->findOrFail($manifestId);

        try {
            $path = $manifestService->ensureFileExists($manifest);
        } catch (\Throwable $e) {
            Log::error('Manifest file regeneration failed: ' . $e->getMessage());
            abort(404, 'Manifest file not found.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $manifest->file_name . '-' . $manifest->shipment->shipment_number . '.pdf"',
        ]);
    }

    public function deleteManifest($shipmentId, $manifestId, ShipmentChangeLogService $changeLogService)
    {
        $manifest = ShipmentManifest::where('shipment_id', $shipmentId)->findOrFail($manifestId);
        $shipment = $manifest->shipment;
        $label = $manifest->displayLabel();

        \App\Support\PrivateDisk::delete($manifest->file_path);
        $manifest->delete();

        $changeLog = null;
        if ($shipment) {
            $changeLog = $changeLogService->log($shipment, 'Manifest removed', $label);
            $changeLog->load('user');
        }

        return response()->json([
            'success' => true,
            'change_log' => $changeLog ? [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ] : null,
            'manifest_mail_pending' => $shipment
                ? $shipment->fresh('manifests')->needsManifestMailSend()
                : false,
        ]);
    }

    public function showPreAlert($shipmentId, $preAlertId, ShipmentPreAlertService $preAlertService)
    {
        $preAlert = ShipmentPreAlert::with('shipment')->where('shipment_id', $shipmentId)->findOrFail($preAlertId);

        try {
            $path = $preAlertService->ensureFileExists($preAlert);
        } catch (\Throwable $e) {
            Log::error('Pre-alert file regeneration failed: ' . $e->getMessage());
            abort(404, 'Pre-alert file not found.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="pre-alert-' . $preAlert->shipment->shipment_number . '-' . $preAlert->version . '.pdf"',
        ]);
    }

    public function deletePreAlert($shipmentId, $preAlertId, ShipmentChangeLogService $changeLogService)
    {
        $preAlert = ShipmentPreAlert::where('shipment_id', $shipmentId)->findOrFail($preAlertId);
        $shipment = $preAlert->shipment;
        $label = $preAlert->displayLabel();

        \App\Support\PrivateDisk::delete($preAlert->file_path);
        $preAlert->delete();

        $changeLog = null;
        if ($shipment) {
            $changeLog = $changeLogService->log($shipment, 'Pre-alert removed', $label);
            $changeLog->load('user');
        }

        return response()->json([
            'success' => true,
            'change_log' => $changeLog ? [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ] : null,
            'pre_alert_mail_pending' => $shipment
                ? $shipment->fresh('preAlerts')->needsPreAlertMailSend()
                : false,
        ]);
    }

    public function combinedPoDocuments($id, CombinedPoPdfService $combinedPoPdfService)
    {
        $shipment = Shipment::findOrFail($id);

        return $combinedPoPdfService->streamMergedPdf(
            $shipment,
            'combined-po-documents-' . $shipment->shipment_number . '.pdf'
        );
    }

    public function combinedManifestDocuments($id, ShipmentManifestPdfBuilder $builder, ShipmentManifestService $manifestService, ShipmentPdfCompanyFooter $companyFooter)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'manifests',
        ])->findOrFail($id);

        if ($shipment->crrs->isEmpty()) {
            abort(404, 'No stock items linked to this shipment.');
        }

        $latestManifest = $manifestService->latestForShipment($shipment);
        if ($latestManifest) {
            $path = \App\Support\PrivateDisk::path($latestManifest->file_path);
            if (is_file($path)) {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $latestManifest->file_name . '-' . $shipment->shipment_number . '.pdf"',
                ]);
            }
        }

        $data = $builder->build($shipment);
        $pdfContent = $companyFooter->output(
            Pdf::loadView('Shipment.pdf.manifest', $data)->setPaper('a4', 'portrait'),
            (string) ($data['createdAt'] ?? '')
        );

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="manifest-' . $shipment->shipment_number . '.pdf"',
        ]);
    }

    public function manifestMailLauncher($id, ManifestMailService $manifestMailService, CombinedPoPdfService $combinedPoPdfService)
    {
        $shipment = Shipment::with('documents')->findOrFail($id);

        try {
            $manifestMailPreview = $manifestMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            abort(400, $e->getMessage());
        }

        $attachmentSources = [
            [
                'url' => route('shipments.combined-manifest-documents', $shipment->id),
                'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
            ],
        ];

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $attachmentSources[] = [
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return view('Shipment.manifest-mail-launcher', [
            'shipment' => $shipment,
            'manifestMailPreview' => $manifestMailPreview,
            'attachmentSources' => $attachmentSources,
            'emlUrl' => route('shipments.manifest-mail', $shipment->id),
            'emlFilename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function manifestMail(Request $request, $id, ManifestMailService $manifestMailService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'crrs.documents',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ])->findOrFail($id);

        try {
            $eml = $manifestMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email,
                $this->parseMailDocumentIds($request->query('document_ids')),
                $this->parseMailExcludeAttachments($request->query('exclude_attachments'))
            );
        } catch (\Throwable $e) {
            Log::error('Manifest mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'manifest-mail-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function prepareManifestMail(Request $request, $id, ManifestMailService $manifestMailService, CombinedPoPdfService $combinedPoPdfService, ShipmentManifestService $manifestService)
    {
        $shipment = Shipment::with(['manifests', 'documents', 'crrs'])->findOrFail($id);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->manifestGenerationRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for manifest email.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Manifest autosave before mail prepare failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before preparing manifest email.',
            ], 500);
        }

        $shipment = $shipment->fresh([
            'crrs.packages',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ]);

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item before sending a manifest.',
            ], 422);
        }

        try {
            if ($shipment->manifests->isEmpty()) {
                $manifestService->generate($shipment);
                $shipment->load('manifests');
            }
        } catch (\Throwable $e) {
            Log::warning('Manifest generation before mail prepare failed: ' . $e->getMessage());
        }

        try {
            $preview = $manifestMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Manifest mail preview failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'attachments' => $this->manifestMailAttachmentSources($shipment, $combinedPoPdfService),
            'eml_url' => route('shipments.manifest-mail', $shipment->id),
            'eml_filename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
            'open_url' => route('shipments.manifest-mail.open', $shipment->id),
            'send_url' => route('shipments.manifest-mail.send', $shipment->id),
        ]);
    }

    public function sendManifestMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
        ])->findOrFail($id);

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'document_ids' => ['nullable'],
            'exclude_attachments' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'manifest',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            $this->parseMailDocumentIds($request->input('document_ids')),
            $this->parseMailExcludeAttachments($request->input('exclude_attachments')),
        );

        $latestManifest = $shipment->latestManifest();
        if ($latestManifest) {
            $latestManifest->markMailSent();
        }

        $description = $latestManifest
            ? $latestManifest->displayLabel() . ' · To ' . $validated['to']
            : 'To ' . $validated['to'];
        $changeLog = $changeLogService->log($shipment, 'Manifest email sent', $description);
        $changeLog->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Manifest email sent successfully.',
            'queued' => true,
            'manifest_mail_pending' => false,
            'change_log' => [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function manifestMailOpen(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        $documentIds = $this->parseMailDocumentIds($request->query('document_ids'));
        $excludeAttachments = $this->parseMailExcludeAttachments($request->query('exclude_attachments'));
        $emlUrl = route('shipments.manifest-mail', $shipment->id);
        $query = [];

        if ($documentIds !== []) {
            $query['document_ids'] = implode(',', $documentIds);
        }
        if ($excludeAttachments !== []) {
            $query['exclude_attachments'] = implode(',', $excludeAttachments);
        }
        if ($query !== []) {
            $emlUrl .= '?' . http_build_query($query);
        }

        return view('Shipment.manifest-mail-open', [
            'emlUrl' => $emlUrl,
            'filename' => 'manifest-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function preparePreAlertMail(Request $request, $id, PreAlertMailService $preAlertMailService, CombinedPoPdfService $combinedPoPdfService)
    {
        $shipment = Shipment::with(['manifests', 'documents', 'crrs'])->findOrFail($id);
        $this->normalizeManifestGenerationRequest($request);

        try {
            $validated = $request->validate($this->preAlertMailRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate shipment data for pre-alert email.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            DB::transaction(function () use ($shipment, $request, $validated) {
                $shipment->update($this->buildShipmentAttributes($request, $validated, onlyPresent: true));

                if ($request->has('crr_ids') && ! in_array($shipment->status, ['Completed', 'Cancelled'], true)) {
                    $crrIds = array_values(array_unique($validated['crr_ids'] ?? []));
                    $shipment->crrs()->sync($crrIds);
                    $this->syncCrrInternalShipments($shipment, $crrIds);
                }

                $service = $validated['service'] ?? $shipment->service;
                $this->syncFlights($shipment, $validated['flights'] ?? [], $service);
                $this->syncSeaLegs($shipment, $validated['sea_legs'] ?? [], $service);
                $this->syncTruckLegs($shipment, $validated['truck_legs'] ?? [], $service);
                $this->syncCourierLegs($shipment, $validated['courier_legs'] ?? [], $service);
                $this->syncReleaseLegs($shipment, $validated['release_legs'] ?? [], $service);
                $this->syncHandCarryLegs($shipment, $request->input('hand_carry_legs', []), $service);
                $this->syncOnBoardLegs($shipment, $validated['on_board_legs'] ?? [], $service);
            });
        } catch (\Throwable $e) {
            Log::error('Pre-alert autosave failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not save shipment changes before preparing pre-alert email.',
            ], 500);
        }

        $shipment = $shipment->fresh([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
            'preAlerts',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        if ($shipment->crrs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Add at least one stock item before sending a pre-alert.',
            ], 422);
        }

        if (!\App\Services\ShipmentPreAlertPdfBuilder::shipmentHasServiceDetails($shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Add service details before generating a pre-alert PDF.',
                'code' => 'missing_service_details',
            ], 422);
        }

        try {
            if (! $shipment->preAlerts()->exists()) {
                app(ShipmentPreAlertService::class)->generate($shipment);
                $shipment->load('preAlerts');
            }
        } catch (\Throwable $e) {
            Log::warning('Pre-alert generation before mail prepare failed: ' . $e->getMessage());
        }

        try {
            $preview = $preAlertMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert mail preview failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'attachments' => $this->preAlertMailAttachmentSources($shipment, $combinedPoPdfService),
            'eml_url' => route('shipments.pre-alert-mail', $shipment->id),
            'eml_filename' => 'pre-alert-mail-' . $shipment->shipment_number . '.eml',
            'open_url' => route('shipments.pre-alert-mail.open', $shipment->id),
            'send_url' => route('shipments.pre-alert-mail.send', $shipment->id),
        ]);
    }

    public function sendPreAlertMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService, ShipmentChangeLogService $changeLogService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'preAlerts',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ])->findOrFail($id);

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'document_ids' => ['nullable'],
            'exclude_attachments' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'pre_alert',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            $this->parseMailDocumentIds($request->input('document_ids')),
            $this->parseMailExcludeAttachments($request->input('exclude_attachments')),
        );

        $latestPreAlert = $shipment->latestPreAlert();
        if ($latestPreAlert) {
            $latestPreAlert->markMailSent();
        }

        $description = $latestPreAlert
            ? $latestPreAlert->displayLabel() . ' · To ' . $validated['to']
            : 'To ' . $validated['to'];
        $changeLog = $changeLogService->log($shipment, 'Pre-alert email sent', $description);
        $changeLog->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Pre-alert email sent successfully.',
            'queued' => true,
            'pre_alert_mail_pending' => false,
            'change_log' => [
                'title' => $changeLog->title,
                'description' => $changeLog->description,
                'user' => $changeLog->user?->name ?? 'System',
                'timestamp' => $changeLog->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function preAlertMailOpen(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        $documentIds = $this->parseMailDocumentIds($request->query('document_ids'));
        $excludeAttachments = $this->parseMailExcludeAttachments($request->query('exclude_attachments'));
        $emlUrl = route('shipments.pre-alert-mail', $shipment->id);
        $query = [];

        if ($documentIds !== []) {
            $query['document_ids'] = implode(',', $documentIds);
        }
        if ($excludeAttachments !== []) {
            $query['exclude_attachments'] = implode(',', $excludeAttachments);
        }
        if ($query !== []) {
            $emlUrl .= '?' . http_build_query($query);
        }

        return view('Shipment.manifest-mail-open', [
            'emlUrl' => $emlUrl,
            'filename' => 'pre-alert-mail-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function preAlertMail(Request $request, $id, PreAlertMailService $preAlertMailService)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $eml = $preAlertMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email,
                $this->parseMailDocumentIds($request->query('document_ids')),
                $this->parseMailExcludeAttachments($request->query('exclude_attachments'))
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'pre-alert-mail-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function uploadDocument(Request $request, $id, ShipmentChangeLogService $changeLogService)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('shipment_documents', 'private');

        $document = ShipmentDocument::create([
            'shipment_id' => $shipment->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'Unspecified',
            'is_internal' => false,
        ]);

        $changeLogService->log($shipment, 'Document added', $document->file_name);

        return response()->json([
            'id' => $document->id,
            'file_name' => $document->file_name,
            'file_url' => $document->fileUrl(),
            'file_type' => $document->file_type,
            'is_internal' => (bool) $document->is_internal,
            'date' => $document->created_at->format('d.m.Y'),
            'type_options' => ShipmentDocument::fileTypeOptionsWithCustom(),
        ]);
    }

    public function deleteDocument($docId, ShipmentChangeLogService $changeLogService)
    {
        try {
            $document = ShipmentDocument::findOrFail($docId);
            $shipment = $document->shipment;
            $fileName = $document->file_name;
            \App\Support\PrivateDisk::delete($document->file_path);
            $document->delete();

            if ($shipment) {
                $changeLogService->log($shipment, 'Document removed', $fileName);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Shipment document delete failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function showDocument($shipmentId, $docId)
    {
        $document = ShipmentDocument::where('shipment_id', $shipmentId)->findOrFail($docId);

        return \App\Support\PrivateDisk::downloadResponse((string) $document->file_path, (string) $document->file_name);
    }

    public function updateDocumentType(Request $request, $docId, ShipmentChangeLogService $changeLogService)
    {
        $document = ShipmentDocument::findOrFail($docId);

        $validated = $request->validate([
            'file_type' => ['required', 'string', 'max:100'],
        ]);

        $fileType = trim($validated['file_type']);
        if ($fileType === '') {
            return response()->json([
                'success' => false,
                'error' => 'Document type is required.',
            ], 422);
        }

        $previousType = $document->file_type;
        $document->update(['file_type' => $fileType]);

        if ($previousType !== $fileType) {
            $changeLogService->log(
                $document->shipment,
                'Document type edited',
                $document->file_name . ': From ' . ($previousType ?: 'empty') . ' to ' . $fileType
            );
        }

        return response()->json([
            'success' => true,
            'file_type' => $document->file_type,
        ]);
    }

    public function updateDocumentInternal(Request $request, $docId, ShipmentChangeLogService $changeLogService)
    {
        $document = ShipmentDocument::findOrFail($docId);

        $validated = $request->validate([
            'is_internal' => ['required', 'boolean'],
        ]);

        $previous = (bool) $document->is_internal;
        $document->update(['is_internal' => $validated['is_internal']]);

        if ($previous !== (bool) $document->is_internal && $document->shipment) {
            $changeLogService->log(
                $document->shipment,
                'Document internal flag edited',
                $document->file_name . ': From ' . ($previous ? 'Internal' : 'Not internal') . ' to ' . ($document->is_internal ? 'Internal' : 'Not internal')
            );
        }

        return response()->json([
            'success' => true,
            'is_internal' => (bool) $document->is_internal,
        ]);
    }

    public function finalize(Request $request, $id, ShipmentStockSnapshotService $stockSnapshotService, ShipmentTransitStockDuplicationService $transitStockDuplicationService)
    {
        $shipment = Shipment::with([
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'crrs.customerVessel.customer',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ])->findOrFail($id);

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
                    Crr::whereIn('id', $crrIds)->update([
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
        $shipment = Shipment::findOrFail($id);
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

    private function validateShipmentRequest(Request $request, ?Shipment $shipment = null): array
    {
        $rules = [
            'departure' => 'nullable|string|max:255',
            'departure_port_code' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'additional_service' => 'nullable|string|max:255',
            'preferred_shipment_date' => 'nullable|string',
            'deadline_arrival' => 'nullable|string',
            'vessel_eta' => 'nullable|string',
            'vessel_etd' => 'nullable|string',
            'pre_alert_reminder' => 'nullable|string',
            'customer_reference' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
            'consignee_address' => 'nullable|string',
            'consignee_city' => 'nullable|string|max:255',
            'consignee_district' => 'nullable|string|max:255',
            'consignee_zip' => 'nullable|string|max:255',
            'consignee_country' => 'nullable|string|max:255',
            'consignee_port_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'consignee_att' => 'required|string|max:255',
            'consignee_email' => 'nullable|email|max:255',
            'account_manager' => 'nullable|integer|exists:contacts,id',
            'status' => 'nullable|string|max:255',
            'repacked_items' => 'nullable|integer|min:0',
            'repacked_weight' => 'nullable|numeric|min:0',
            'special_considerations_destination' => 'nullable|string',
            'comments_departure_hub' => 'nullable|string',
            'comments_consignee' => 'nullable|string',
            'crr_ids' => 'nullable|array',
            'crr_ids.*' => 'integer|exists:crrs,id',
            'irregularities' => 'nullable|array',
            'irregularities.*.irregularity_date' => 'nullable|string',
            'irregularities.*.irregularity_type' => 'nullable|string|max:255',
            'irregularities.*.party_responsible' => 'nullable|string|max:255',
            'irregularities.*.consequence' => 'nullable|string|max:255',
            'irregularities.*.extra_cost_mt_usd' => 'nullable|numeric',
            'irregularities.*.status' => 'nullable|string|max:255',
            'irregularities.*.cause_of_irregularity' => 'nullable|string',
            'irregularities.*.action_taken' => 'nullable|string',
            'irregularities.*.customer_response' => 'nullable|string',
            'irregularities.*.hub_agent_comments' => 'nullable|string',
            'irregularities.*.handled_by' => 'nullable|string|max:255',
            'flights' => 'nullable|array',
            'flights.*.leg_reference' => 'nullable|string|max:255',
            'flights.*.flight_number' => 'nullable|string|max:255',
            'flights.*.departure_date' => 'nullable|string',
            'flights.*.arrival_date' => 'nullable|string',
            'flights.*.arrival_time' => 'nullable|string|max:5',
            'sea_legs' => 'nullable|array',
            'sea_legs.*.bill_of_lading' => 'nullable|string|max:255',
            'sea_legs.*.container_number' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_imo' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_name' => 'nullable|string|max:255',
            'sea_legs.*.etd' => 'nullable|string',
            'sea_legs.*.eta' => 'nullable|string',
            'sea_legs.*.arrival_time' => 'nullable|string|max:5',
            'truck_legs' => 'nullable|array',
            'truck_legs.*.cmr' => 'nullable|string|max:255',
            'truck_legs.*.freight_company' => 'nullable|string|max:255',
            'truck_legs.*.departure_date' => 'nullable|string',
            'truck_legs.*.arrival_date' => 'nullable|string',
            'truck_legs.*.arrival_time' => 'nullable|string|max:5',
            'courier_legs' => 'nullable|array',
            'courier_legs.*.airway_bill' => 'nullable|string|max:255',
            'courier_legs.*.carrier' => 'nullable|string|max:255',
            'courier_legs.*.departure_date' => 'nullable|string',
            'courier_legs.*.arrival_date' => 'nullable|string',
            'courier_legs.*.arrival_time' => 'nullable|string|max:5',
            'release_legs' => 'nullable|array',
            'release_legs.*.freight_company' => 'nullable|string|max:255',
            'release_legs.*.delivery_date' => 'nullable|string',
            'release_legs.*.delivery_time' => 'nullable|string|max:5',
            'hand_carry_legs' => 'nullable|array',
            'hand_carry_legs.*.departure_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_time' => 'nullable|string|max:5',
            'hand_carry_legs.*.contact_name' => 'nullable|string|max:255',
            'hand_carry_legs.*.contact_phone' => 'nullable|string|max:255',
            'hand_carry_legs.*.onboard_hand_carry' => 'nullable|boolean',
            'on_board_legs' => 'nullable|array',
            'on_board_legs.*.departure_date' => 'nullable|string',
            'on_board_legs.*.delivery_date' => 'nullable|string',
            'on_board_legs.*.delivery_time' => 'nullable|string|max:5',
        ];

        $validator = validator($request->all(), $rules, [], [
            'consignee_att' => 'contact person',
        ]);

        $validator->after(function ($validator) use ($request, $shipment) {
            $crrIds = array_values(array_unique($request->input('crr_ids', [])));

            $this->validateSelectableCrrsForShipment($crrIds, $validator, $shipment);
            $this->validateSingleHubForSelectedCrrs($crrIds, $validator);
        });

        return $validator->validate();
    }

    private function validateSelectableCrrsForShipment(array $crrIds, \Illuminate\Contracts\Validation\Validator $validator, ?Shipment $shipment = null): void
    {
        if (empty($crrIds)) {
            return;
        }

        $attachedIds = $shipment
            ? $shipment->crrs()->pluck('crrs.id')->map(fn ($id) => (int) $id)->all()
            : [];
        $newCrrIds = array_values(array_diff(array_map('intval', $crrIds), $attachedIds));

        if ($newCrrIds === []) {
            return;
        }

        $invalidCount = Crr::query()
            ->whereIn('id', $newCrrIds)
            ->whereIn('status', [Crr::STATUS_IN_PROGRESS, Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED])
            ->count();

        if ($invalidCount > 0) {
            $validator->errors()->add('crr_ids', 'In Progress, completed and cancelled stock items cannot be added to a shipment.');
        }
    }

    private function validateSingleHubForSelectedCrrs(array $crrIds, \Illuminate\Contracts\Validation\Validator $validator): void
    {
        if (count($crrIds) <= 1) {
            return;
        }

        $hubValues = Crr::query()
            ->whereIn('id', $crrIds)
            ->get()
            ->map(function (Crr $crr) {
                $hubValue = trim((string) ($crr->hub_code ?: $crr->hub_agent));

                return $hubValue !== '' ? mb_strtolower($hubValue) : null;
            })
            ->filter()
            ->unique()
            ->values();

        if ($hubValues->count() <= 1) {
            return;
        }

        $validator->errors()->add('crr_ids', 'All selected stock items must belong to the same hub.');
    }

    private function normalizeManifestGenerationRequest(Request $request): void
    {
        // Only normalize an explicitly submitted empty value. Merging null when the
        // field is absent makes Request::has('account_manager') true in Laravel 12
        // and wipes account_manager_id on partial updates (service details / pre-alert).
        if ($request->exists('account_manager') && $request->input('account_manager') === '') {
            $request->merge(['account_manager' => null]);
        }
    }

    private function manifestGenerationRules(): array
    {
        return [
            'departure' => 'nullable|string|max:255',
            'departure_port_code' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'additional_service' => 'nullable|string|max:255',
            'preferred_shipment_date' => 'nullable|string',
            'deadline_arrival' => 'nullable|string',
            'vessel_eta' => 'nullable|string',
            'vessel_etd' => 'nullable|string',
            'pre_alert_reminder' => 'nullable|string',
            'customer_reference' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
            'consignee_address' => 'nullable|string',
            'consignee_city' => 'nullable|string|max:255',
            'consignee_district' => 'nullable|string|max:255',
            'consignee_zip' => 'nullable|string|max:255',
            'consignee_country' => 'nullable|string|max:255',
            'consignee_port_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'consignee_att' => 'nullable|string|max:255',
            'consignee_email' => 'nullable|string|max:255',
            'account_manager' => 'nullable|integer|exists:contacts,id',
            'status' => 'nullable|string|max:255',
            'repacked_items' => 'nullable|integer|min:0',
            'repacked_weight' => 'nullable|numeric|min:0',
            'special_considerations_destination' => 'nullable|string',
            'comments_departure_hub' => 'nullable|string',
            'comments_consignee' => 'nullable|string',
            'not_applicable_for_consolidation' => 'nullable|boolean',
            'skip_instruction_dest' => 'nullable|boolean',
            'skip_instruction_hub' => 'nullable|boolean',
            'skip_prealert' => 'nullable|boolean',
            'project_logistics' => 'nullable|boolean',
            'port_agency' => 'nullable|boolean',
            'crr_ids' => 'nullable|array',
            'crr_ids.*' => 'integer|exists:crrs,id',
        ];
    }

    private function preAlertMailRules(): array
    {
        return array_merge($this->manifestGenerationRules(), [
            'flights' => 'nullable|array',
            'flights.*.leg_reference' => 'nullable|string|max:255',
            'flights.*.flight_number' => 'nullable|string|max:255',
            'flights.*.departure_date' => 'nullable|string',
            'flights.*.arrival_date' => 'nullable|string',
            'flights.*.arrival_time' => 'nullable|string|max:5',
            'sea_legs' => 'nullable|array',
            'sea_legs.*.bill_of_lading' => 'nullable|string|max:255',
            'sea_legs.*.container_number' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_imo' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_name' => 'nullable|string|max:255',
            'sea_legs.*.etd' => 'nullable|string',
            'sea_legs.*.eta' => 'nullable|string',
            'sea_legs.*.arrival_time' => 'nullable|string|max:5',
            'truck_legs' => 'nullable|array',
            'truck_legs.*.cmr' => 'nullable|string|max:255',
            'truck_legs.*.freight_company' => 'nullable|string|max:255',
            'truck_legs.*.departure_date' => 'nullable|string',
            'truck_legs.*.arrival_date' => 'nullable|string',
            'truck_legs.*.arrival_time' => 'nullable|string|max:5',
            'courier_legs' => 'nullable|array',
            'courier_legs.*.airway_bill' => 'nullable|string|max:255',
            'courier_legs.*.carrier' => 'nullable|string|max:255',
            'courier_legs.*.departure_date' => 'nullable|string',
            'courier_legs.*.arrival_date' => 'nullable|string',
            'courier_legs.*.arrival_time' => 'nullable|string|max:5',
            'release_legs' => 'nullable|array',
            'release_legs.*.freight_company' => 'nullable|string|max:255',
            'release_legs.*.delivery_date' => 'nullable|string',
            'release_legs.*.delivery_time' => 'nullable|string|max:5',
            'hand_carry_legs' => 'nullable|array',
            'hand_carry_legs.*.departure_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_time' => 'nullable|string|max:5',
            'hand_carry_legs.*.contact_name' => 'nullable|string|max:255',
            'hand_carry_legs.*.contact_phone' => 'nullable|string|max:255',
            'hand_carry_legs.*.onboard_hand_carry' => 'nullable|boolean',
            'on_board_legs' => 'nullable|array',
            'on_board_legs.*.departure_date' => 'nullable|string',
            'on_board_legs.*.delivery_date' => 'nullable|string',
            'on_board_legs.*.delivery_time' => 'nullable|string|max:5',
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function parseMailDocumentIds(mixed $documentIds): array
    {
        if ($documentIds === null || $documentIds === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            'intval',
            is_array($documentIds) ? $documentIds : explode(',', (string) $documentIds)
        ))));
    }

    /**
     * @return array<int, string>
     */
    private function parseMailExcludeAttachments(mixed $excludeAttachments): array
    {
        if ($excludeAttachments === null || $excludeAttachments === '') {
            return [];
        }

        $keys = is_array($excludeAttachments)
            ? $excludeAttachments
            : explode(',', (string) $excludeAttachments);

        return array_values(array_unique(array_filter(array_map(
            static fn ($key) => trim((string) $key),
            $keys
        ), static fn ($key) => $key !== '')));
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function collectUploadedMailAttachments(Request $request): array
    {
        $attachments = [];

        foreach ($request->file('files', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower((string) $file->getClientOriginalExtension());
            if (! in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $attachments[] = [
                'filename' => $file->getClientOriginalName(),
                'content' => (string) file_get_contents($file->getRealPath()),
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
            ];
        }

        return $attachments;
    }

    /**
     * @return array<int, array{url: string, filename: string}>
     */
    private function manifestMailAttachmentSources(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): array
    {
        $sources = [];
        $latestManifest = $shipment->manifests->sortByDesc('version')->first();

        if ($latestManifest) {
            $sources[] = [
                'key' => 'manifest',
                'url' => route('shipments.manifests.show', [$shipment->id, $latestManifest->id]),
                'filename' => $latestManifest->displayLabel() . '-' . $shipment->shipment_number . '.pdf',
            ];
        } else {
            $sources[] = [
                'key' => 'manifest',
                'url' => route('shipments.combined-manifest-documents', $shipment->id),
                'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
            ];
        }

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $sources[] = [
                'key' => 'combined_po',
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return array_merge($sources, $this->checkedShipmentDocumentAttachmentSources($shipment));
    }

    /**
     * @return array<int, array{url: string, filename: string}>
     */
    private function preAlertMailAttachmentSources(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): array
    {
        $sources = [];
        $latestPreAlert = $shipment->preAlerts->sortByDesc('version')->first();

        if ($latestPreAlert) {
            $sources[] = [
                'key' => 'pre_alert',
                'url' => route('shipments.pre-alerts.show', [$shipment->id, $latestPreAlert->id]),
                'filename' => 'pre-alert-' . $shipment->shipment_number . '-' . $latestPreAlert->version . '.pdf',
            ];
        }

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $sources[] = [
                'key' => 'combined_po',
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return array_merge($sources, $this->checkedShipmentDocumentAttachmentSources($shipment));
    }

    /**
     * @return array<int, array{key: string, url: string, filename: string, document_id: int}>
     */
    private function checkedShipmentDocumentAttachmentSources(Shipment $shipment): array
    {
        $sources = [];

        foreach ($shipment->documents as $document) {
            if (! $document->is_internal) {
                continue;
            }

            $sources[] = [
                'key' => 'document-' . $document->id,
                'url' => $document->fileUrl(),
                'filename' => $document->file_name,
                'document_id' => $document->id,
            ];
        }

        return $sources;
    }

    private function manifestToArray(ShipmentManifest $manifest): array
    {
        return [
            'id' => $manifest->id,
            'version' => $manifest->version,
            'file_name' => $manifest->file_name,
            'display_label' => $manifest->displayLabel(),
            'file_url' => route('shipments.manifests.show', [$manifest->shipment_id, $manifest->id]),
            'date' => $manifest->created_at->format('d.m.Y'),
            'time' => $manifest->created_at->format('H:i'),
        ];
    }

    private function preAlertToArray(ShipmentPreAlert $preAlert): array
    {
        return [
            'id' => $preAlert->id,
            'version' => $preAlert->version,
            'file_name' => $preAlert->file_name,
            'display_label' => $preAlert->displayLabel(),
            'file_url' => route('shipments.pre-alerts.show', [$preAlert->shipment_id, $preAlert->id]),
            'date' => $preAlert->created_at->format('d.m.Y'),
            'time' => $preAlert->created_at->format('H:i'),
        ];
    }

    private function shipmentDocumentCount(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): int
    {
        $combinedPoCount = $combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty() ? 1 : 0;

        return $combinedPoCount
            + $shipment->manifests()->count()
            + $shipment->preAlerts()->count()
            + $shipment->documents()->count();
    }

    private function buildShipmentAttributes(Request $request, array $validated, bool $onlyPresent = false): array
    {
        $attributes = [
            'departure' => $validated['departure'] ?? null,
            'departure_port_code' => $validated['departure_port_code'] ?? null,
            'service' => $validated['service'] ?? null,
            'additional_service' => $validated['additional_service'] ?? null,
            'preferred_shipment_date' => $this->parseDate($validated['preferred_shipment_date'] ?? null),
            'deadline_arrival' => $this->parseDate($validated['deadline_arrival'] ?? null),
            'vessel_eta' => $this->parseDate($validated['vessel_eta'] ?? null),
            'vessel_etd' => $this->parseDate($validated['vessel_etd'] ?? null),
            'pre_alert_reminder' => $this->parseDate($validated['pre_alert_reminder'] ?? null),
            'customer_reference' => $validated['customer_reference'] ?? null,
            'not_applicable_for_consolidation' => $request->boolean('not_applicable_for_consolidation'),
            'consignee' => $validated['consignee'] ?? null,
            'consignee_address' => $validated['consignee_address'] ?? null,
            'consignee_city' => $validated['consignee_city'] ?? null,
            'consignee_district' => $validated['consignee_district'] ?? null,
            'consignee_zip' => $validated['consignee_zip'] ?? null,
            'consignee_country' => $validated['consignee_country'] ?? null,
            'consignee_port_code' => $validated['consignee_port_code'] ?? null,
            'location' => $validated['location'] ?? null,
            'consignee_att' => $validated['consignee_att'] ?? null,
            'consignee_email' => $validated['consignee_email'] ?? null,
            'account_manager_id' => !empty($validated['account_manager']) ? (int) $validated['account_manager'] : null,
            'special_considerations_destination' => $validated['special_considerations_destination'] ?? null,
            'skip_instruction_dest' => $request->boolean('skip_instruction_dest'),
            'comments_departure_hub' => $validated['comments_departure_hub'] ?? null,
            'skip_instruction_hub' => $request->boolean('skip_instruction_hub'),
            'comments_consignee' => $validated['comments_consignee'] ?? null,
            'skip_prealert' => $request->boolean('skip_prealert'),
            'project_logistics' => $request->boolean('project_logistics'),
            'port_agency' => $request->boolean('port_agency'),
            'status' => $validated['status'] ?? $request->input('status', 'In process'),
            'repacked_items' => array_key_exists('repacked_items', $validated)
                ? ($validated['repacked_items'] !== null ? (int) $validated['repacked_items'] : null)
                : null,
            'repacked_weight' => array_key_exists('repacked_weight', $validated)
                ? ($validated['repacked_weight'] !== null ? (float) $validated['repacked_weight'] : null)
                : null,
        ];

        if (!$onlyPresent) {
            return $attributes;
        }

        $booleanFields = [
            'not_applicable_for_consolidation',
            'skip_instruction_dest',
            'skip_instruction_hub',
            'skip_prealert',
            'project_logistics',
            'port_agency',
        ];

        $requestKeyMap = [
            'account_manager_id' => 'account_manager',
        ];

        return collect($attributes)->filter(function ($value, $key) use ($request, $booleanFields, $requestKeyMap) {
            if (in_array($key, $booleanFields, true)) {
                return true;
            }

            $requestKey = $requestKeyMap[$key] ?? $key;

            // Preserve existing account manager unless a real ID was posted.
            if ($key === 'account_manager_id') {
                return $request->filled('account_manager');
            }

            return $request->has($requestKey);
        })->all();
    }

    private function syncCrrInternalShipments(Shipment $shipment, array $crrIds, ?array $previousCrrIds = null): void
    {
        if (!empty($crrIds)) {
            Crr::whereIn('id', $crrIds)->update([
                'internal_shipment' => $shipment->shipment_number,
                'status' => Crr::STATUS_IN_PROGRESS,
            ]);
        }

        if ($previousCrrIds === null) {
            return;
        }

        $removedCrrIds = array_diff($previousCrrIds, $crrIds);
        if (empty($removedCrrIds)) {
            return;
        }

        Crr::whereIn('id', $removedCrrIds)
            ->where('internal_shipment', $shipment->shipment_number)
            ->update([
                'internal_shipment' => null,
                'status' => Crr::STATUS_ACTIVE,
            ]);
    }

    private function syncIrregularities(Shipment $shipment, array $irregularities): void
    {
        $shipment->irregularities()->delete();

        foreach ($irregularities as $irregularity) {
            if (!$this->irregularityHasData($irregularity)) {
                continue;
            }

            ShipmentIrregularity::create([
                'shipment_id' => $shipment->id,
                'irregularity_date' => $this->parseDate($irregularity['irregularity_date'] ?? null),
                'irregularity_type' => $irregularity['irregularity_type'] ?? null,
                'party_responsible' => $irregularity['party_responsible'] ?? null,
                'consequence' => $irregularity['consequence'] ?? null,
                'extra_cost_mt_usd' => $irregularity['extra_cost_mt_usd'] ?? null,
                'status' => $irregularity['status'] ?? null,
                'cause_of_irregularity' => $irregularity['cause_of_irregularity'] ?? null,
                'action_taken' => $irregularity['action_taken'] ?? null,
                'customer_response' => $irregularity['customer_response'] ?? null,
                'hub_agent_comments' => $irregularity['hub_agent_comments'] ?? null,
                'handled_by' => $irregularity['handled_by'] ?? null,
            ]);
        }
    }

    private function syncFlights(Shipment $shipment, array $flights, ?string $service): void
    {
        $shipment->flights()->delete();

        if ($service !== 'Airfreight') {
            return;
        }

        foreach ($flights as $index => $flight) {
            if (!$this->flightHasData($flight)) {
                continue;
            }

            ShipmentFlight::create([
                'shipment_id' => $shipment->id,
                'leg_reference' => $flight['leg_reference'] ?? null,
                'flight_number' => $flight['flight_number'] ?? null,
                'departure_date' => $this->parseDate($flight['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($flight['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($flight['arrival_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function syncSeaLegs(Shipment $shipment, array $seaLegs, ?string $service): void
    {
        $shipment->seaLegs()->delete();

        if ($service !== 'Sea freight') {
            return;
        }

        foreach ($seaLegs as $index => $leg) {
            if (!$this->seaLegHasData($leg)) {
                continue;
            }

            ShipmentSeaLeg::create([
                'shipment_id' => $shipment->id,
                'bill_of_lading' => $leg['bill_of_lading'] ?? null,
                'container_number' => $leg['container_number'] ?? null,
                'transport_vessel_imo' => $leg['transport_vessel_imo'] ?? null,
                'transport_vessel_name' => $leg['transport_vessel_name'] ?? null,
                'etd' => $this->parseDate($leg['etd'] ?? null),
                'eta' => $this->parseDate($leg['eta'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function seaLegHasData(array $leg): bool
    {
        foreach (['bill_of_lading', 'container_number', 'transport_vessel_imo', 'transport_vessel_name', 'etd', 'eta', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function syncTruckLegs(Shipment $shipment, array $truckLegs, ?string $service): void
    {
        $shipment->truckLegs()->delete();

        if ($service !== 'Truck') {
            return;
        }

        foreach ($truckLegs as $index => $leg) {
            if (!$this->truckLegHasData($leg)) {
                continue;
            }

            ShipmentTruckLeg::create([
                'shipment_id' => $shipment->id,
                'cmr' => $leg['cmr'] ?? null,
                'freight_company' => $leg['freight_company'] ?? null,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function truckLegHasData(array $leg): bool
    {
        foreach (['cmr', 'freight_company', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function syncCourierLegs(Shipment $shipment, array $courierLegs, ?string $service): void
    {
        $shipment->courierLegs()->delete();

        if ($service !== 'Courier') {
            return;
        }

        foreach ($courierLegs as $index => $leg) {
            if (!$this->courierLegHasData($leg)) {
                continue;
            }

            ShipmentCourierLeg::create([
                'shipment_id' => $shipment->id,
                'airway_bill' => $leg['airway_bill'] ?? null,
                'carrier' => $leg['carrier'] ?? null,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function courierLegHasData(array $leg): bool
    {
        foreach (['airway_bill', 'carrier', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function syncReleaseLegs(Shipment $shipment, array $releaseLegs, ?string $service): void
    {
        $shipment->releaseLegs()->delete();

        if ($service !== 'Release') {
            return;
        }

        foreach ($releaseLegs as $index => $leg) {
            if (!$this->releaseLegHasData($leg)) {
                continue;
            }

            ShipmentReleaseLeg::create([
                'shipment_id' => $shipment->id,
                'freight_company' => $leg['freight_company'] ?? null,
                'delivery_date' => $this->parseDate($leg['delivery_date'] ?? null),
                'delivery_time' => $this->parseArrivalTime($leg['delivery_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function releaseLegHasData(array $leg): bool
    {
        foreach (['freight_company', 'delivery_date', 'delivery_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function syncOnBoardLegs(Shipment $shipment, array $onBoardLegs, ?string $service): void
    {
        $shipment->onBoardLegs()->delete();

        if ($service !== 'On-board delivery') {
            return;
        }

        foreach ($onBoardLegs as $index => $leg) {
            if (!$this->onBoardLegHasData($leg)) {
                continue;
            }

            ShipmentOnBoardLeg::create([
                'shipment_id' => $shipment->id,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'delivery_date' => $this->parseDate($leg['delivery_date'] ?? null),
                'delivery_time' => $this->parseArrivalTime($leg['delivery_time'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function onBoardLegHasData(array $leg): bool
    {
        foreach (['departure_date', 'delivery_date', 'delivery_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function syncHandCarryLegs(Shipment $shipment, array $handCarryLegs, ?string $service): void
    {
        $shipment->handCarryLegs()->delete();

        if ($service !== 'Hand Carry') {
            return;
        }

        foreach ($handCarryLegs as $index => $leg) {
            if (!$this->handCarryLegHasData($leg)) {
                continue;
            }

            ShipmentHandCarryLeg::create([
                'shipment_id' => $shipment->id,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'contact_name' => $leg['contact_name'] ?? null,
                'contact_phone' => $leg['contact_phone'] ?? null,
                'onboard_hand_carry' => !empty($leg['onboard_hand_carry']),
                'sort_order' => $index,
            ]);
        }
    }

    private function handCarryLegHasData(array $leg): bool
    {
        if (!empty($leg['onboard_hand_carry'])) {
            return true;
        }

        foreach (['departure_date', 'arrival_date', 'arrival_time', 'contact_name', 'contact_phone'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    private function flightHasData(array $flight): bool
    {
        foreach (['leg_reference', 'flight_number', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($flight[$field])) {
                return true;
            }
        }

        return false;
    }

    private function parseArrivalTime(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            [$hours, $minutes] = explode(':', $value);

            return sprintf('%02d:%02d', (int) $hours, (int) $minutes);
        }

        return $value;
    }

    private function irregularityFormOptions(): array
    {
        return [
            'irregularityTypeOptions' => [
                'Customer complaint',
                'Shipment missing (and found)',
                'Delayed shipment',
                'Damage to shipment',
                'Incorrect or missing shipping documentation',
                'Cross label',
                'Shipment short shipped',
                'Shipment misrouted',
                'Slow or unclear communication by agent',
                'Quotation unclear or incomplete',
                'Other',
                'No cost provided by agent',
                'Send pre-alert in wrong format',
                'Billing discrepancy',
            ],
            'partyResponsibleOptions' => [
                'Marinetrans',
                'Departing Hub',
                'Receiving Agent',
                'Customer',
                'Carrier',
            ],
            'consequenceOptions' => [
                'Deadline and delivery met',
                'Original deadline missed, but vessel/destination reached',
                'Deadline and vessel missed',
                'Official customer claim',
            ],
            'statusOptions' => [
                'Not started',
                'In process',
                'Closed',
            ],
        ];
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    private function generateShipmentNumber(): string
    {
        $userName = (string) (auth()->user()?->name ?? '');
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $userName) ?? '', 0, 3));

        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $random = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $monthYear = now()->format('my');

        return $prefix . '-' . $random . '-' . $monthYear;
    }

    private function irregularityHasData(array $irregularity): bool
    {
        foreach ($irregularity as $value) {
            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function applyShipmentFollowUpFilters($query, Request $request): void
    {
        $shipmentNo = trim((string) $request->input('shipment_no', $request->input('shipment_number', '')));
        $destination = trim((string) $request->input('port_destination', $request->input('destination', '')));

        $request->merge([
            'shipment_number' => $shipmentNo,
            'destination' => $destination,
        ]);

        $this->applyShipmentIndexFilters($query, $request);

        if ($request->boolean('show_etl')) {
            $query->whereHas('crrs', fn ($q) => $q->whereRaw('UPPER(internal_shipment) = ?', ['ETL']));
        }
    }

    private function shipmentFollowUpFilterOptions($baseQuery): array
    {
        $customers = DB::table('customers')
            ->select('customer_name')
            ->whereNotNull('customer_name')
            ->distinct()
            ->orderBy('customer_name')
            ->pluck('customer_name');

        $vessels = DB::table('customer_vessels')
            ->select('vessel')
            ->whereNotNull('vessel')
            ->where('vessel', '!=', '')
            ->distinct()
            ->orderBy('vessel')
            ->pluck('vessel');

        $statuses = (clone $baseQuery)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        $accountManagers = Contact::query()
            ->whereIn('id', (clone $baseQuery)->whereNotNull('account_manager_id')->distinct()->pluck('account_manager_id'))
            ->orderBy('name')
            ->get();

        $creators = User::query()
            ->whereIn('id', (clone $baseQuery)->whereNotNull('created_by')->distinct()->pluck('created_by'))
            ->orderBy('name')
            ->get();

        return compact('customers', 'vessels', 'statuses', 'accountManagers', 'creators');
    }

    private function applyShipmentIndexFilters($query, Request $request): void
    {
        $customers = array_values(array_filter((array) $request->input('customer', [])));
        $vessels = array_values(array_filter((array) $request->input('vessel', [])));
        $departurePorts = array_values(array_filter((array) $request->input('departure_port_code', [])));
        $accountManagers = array_values(array_filter((array) $request->input('account_manager', [])));
        $creators = array_values(array_filter((array) $request->input('created_by', [])));
        $offices = array_values(array_filter((array) $request->input('office', [])));
        $services = array_values(array_filter((array) $request->input('service', [])));
        $statuses = array_values(array_filter((array) $request->input('status', [])));
        $shipmentNumber = trim((string) $request->input('shipment_number', ''));
        $serviceReference = trim((string) $request->input('service_reference', ''));
        $poNumber = trim((string) $request->input('po_number', ''));
        $consignee = trim((string) $request->input('consignee', ''));
        $destination = trim((string) $request->input('destination', ''));
        $creationDate = trim((string) $request->input('creation_date', ''));

        $shipmentNumberLike = ListSearch::prefix($shipmentNumber);
        $destinationLike = ListSearch::prefix($destination);
        $serviceReferenceLike = ListSearch::prefix($serviceReference);
        $consigneeLike = ListSearch::prefix($consignee);
        $poExact = mb_strlen($poNumber) >= 3 ? $poNumber : '';

        $query
            ->when($shipmentNumberLike, fn ($q, $pattern) => $q->where('shipment_number', 'like', $pattern))
            ->when($customers, fn ($q) => $q->whereHas('crrs.customerVessel.customer', fn ($sub) => $sub->whereIn('customer_name', $customers)))
            ->when($vessels, function ($q) use ($vessels) {
                $q->whereHas('crrs', function ($sub) use ($vessels) {
                    $sub->whereIn('vessel_name', $vessels)
                        ->orWhereHas('customerVessel', function ($cv) use ($vessels) {
                            $cv->whereIn('vessel', $vessels)
                                ->orWhereIn('vessel_name_alias', $vessels);
                        });
                });
            })
            ->when($departurePorts, fn ($q) => $q->whereIn('departure_port_code', $departurePorts))
            ->when($accountManagers, fn ($q) => $q->whereHas('accountManager', fn ($sub) => $sub->whereIn('name', $accountManagers)))
            ->when($creators, fn ($q) => $q->whereHas('creator', fn ($sub) => $sub->whereIn('name', $creators)))
            ->when($offices, fn ($q) => $q->whereHas('accountManager.office', fn ($sub) => $sub->whereIn('office_name', $offices)))
            ->when($services, fn ($q) => $q->whereIn('service', $services))
            ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))
            ->when($creationDate !== '', fn ($q) => $q->whereDate('created_at', $creationDate))
            ->when($poExact !== '', fn ($q) => $q->whereHas('crrs', fn ($sub) => $sub->whereJsonContains('po_numbers', $poExact)))
            ->when($destinationLike, function ($q, $pattern) use ($destination) {
                if (preg_match('/^[A-Za-z0-9]{2,8}$/', $destination)) {
                    $q->where('consignee_port_code', 'like', $pattern);

                    return;
                }

                $q->where(function ($sub) use ($pattern) {
                    $sub->where('consignee_port_code', 'like', $pattern)
                        ->orWhere('consignee_city', 'like', $pattern)
                        ->orWhere('consignee_country', 'like', $pattern);
                });
            })
            ->when($serviceReferenceLike, function ($q, $pattern) {
                $q->where(function ($sub) use ($pattern) {
                    $sub->whereHas('flights', fn ($leg) => $leg->where('leg_reference', 'like', $pattern))
                        ->orWhereHas('courierLegs', fn ($leg) => $leg->where('airway_bill', 'like', $pattern))
                        ->orWhereHas('seaLegs', fn ($leg) => $leg->where('bill_of_lading', 'like', $pattern))
                        ->orWhereHas('truckLegs', function ($leg) use ($pattern) {
                            $leg->where('cmr', 'like', $pattern)->orWhere('freight_company', 'like', $pattern);
                        })
                        ->orWhereHas('releaseLegs', fn ($leg) => $leg->where('freight_company', 'like', $pattern));
                });
            })
            ->when($consigneeLike, function ($q, $pattern) use ($consignee) {
                $keys = $this->consigneeKeysMatching($consignee);
                $q->where(function ($sub) use ($pattern, $keys) {
                    $sub->where('consignee', 'like', $pattern)
                        ->orWhere('consignee_city', 'like', $pattern)
                        ->orWhere('consignee_country', 'like', $pattern)
                        ->orWhere('consignee_port_code', 'like', $pattern)
                        ->orWhere('consignee_address', 'like', $pattern)
                        ->orWhere('consignee_att', 'like', $pattern);

                    if ($keys) {
                        $sub->orWhereIn('consignee', $keys);
                    }
                });
            });
    }

    private function consigneeKeysMatching(string $term): array
    {
        $like = ListSearch::prefix($term);
        if ($like === null) {
            return [];
        }

        $keys = [];

        foreach (Hub::query()->where('hub_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'hub:' . $id;
        }
        foreach (Agent::query()->where('agent_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'agent:' . $id;
        }
        foreach (Customer::query()->where('customer_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'customer:' . $id;
        }
        foreach (Office::query()->where('office_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'office:' . $id;
        }
        foreach (Supplier::query()->where('supplier_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'supplier:' . $id;
        }
        foreach (OtherCompany::query()->where('company_name', 'like', $like)->pluck('id') as $id) {
            $keys[] = 'other_company:' . $id;
        }

        return $keys;
    }
}
