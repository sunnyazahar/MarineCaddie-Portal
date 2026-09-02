<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerResponsible;
use App\Models\Crr;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Shipment;
use App\Models\ShipmentDocument;
use App\Models\ShipmentFlight;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentIrregularity;
use App\Models\ShipmentManifest;
use App\Models\ShipmentOnBoardLeg;
use App\Models\ShipmentPreAlertReminderSend;
use App\Models\ShipmentPreAlert;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentTruckLeg;
use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Support\ListSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class ShipmentRepository extends BaseRepository implements ShipmentRepositoryInterface
{
    protected string $modelClass = Shipment::class;

    public function buildIndexQuery(array $filters): Builder
    {
        $query = $this->query()
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

        $this->applyShipmentIndexFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function paginateIndex(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildIndexQuery($filters)->paginate($perPage);
    }

    public function paginateForInvoicing(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildInvoicingQuery($filters)->paginate($perPage);
    }

    public function buildInvoicingQuery(array $filters): Builder
    {
        $query = $this->query()
            ->with([
                'crrs.packages',
                'flights',
                'seaLegs',
                'truckLegs',
                'courierLegs',
                'releaseLegs',
                'handCarryLegs',
                'onBoardLegs',
                'proformaInvoice',
            ])
            ->where('status', '!=', 'Cancelled');

        $this->applyInvoicingFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function findForInvoicingByNumber(string $shipmentNumber): ?Shipment
    {
        $shipmentNumber = trim($shipmentNumber);
        if ($shipmentNumber === '') {
            return null;
        }

        return $this->query()
            ->with([
                'crrs.packages',
                'flights',
                'seaLegs',
                'truckLegs',
                'courierLegs',
                'releaseLegs',
                'handCarryLegs',
                'onBoardLegs',
                'proformaInvoice',
            ])
            ->where('shipment_number', $shipmentNumber)
            ->where('status', '!=', 'Cancelled')
            ->first();
    }

  /**
     * @param  list<string>  $shipmentNumbers
     * @return EloquentCollection<int, Shipment>
     */
    public function findManyForInvoicingByNumbers(array $shipmentNumbers): EloquentCollection
    {
        $shipmentNumbers = array_values(array_unique(array_filter(array_map(
            static fn (string $number) => trim($number),
            $shipmentNumbers
        ))));

        if ($shipmentNumbers === []) {
            return new EloquentCollection();
        }

        return $this->query()
            ->with([
                'crrs.packages',
                'flights',
                'seaLegs',
                'truckLegs',
                'courierLegs',
                'releaseLegs',
                'handCarryLegs',
                'onBoardLegs',
                'proformaInvoice.lineItems',
            ])
            ->whereIn('shipment_number', $shipmentNumbers)
            ->where('status', '!=', 'Cancelled')
            ->get();
    }

    public function indexFilterOptions(): array
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

        $services = $this->query()->whereNotNull('service')->distinct()->orderBy('service')->pluck('service');
        $statuses = $this->query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
        $departureOptions = $this->query()->whereNotNull('departure_port_code')->distinct()->orderBy('departure_port_code')->pluck('departure_port_code');

        $accountManagers = $this->customerAccountManagers();

        $creators = User::query()
            ->whereIn('id', $this->query()->whereNotNull('created_by')->distinct()->pluck('created_by'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $offices = Office::query()
            ->whereIn('id', $accountManagers->pluck('office_id')->filter()->unique())
            ->orderBy('office_name')
            ->get(['id', 'office_name']);

        return compact(
            'customers',
            'vessels',
            'services',
            'statuses',
            'departureOptions',
            'accountManagers',
            'creators',
            'offices',
        );
    }

    public function buildPreAlertRemindersQuery(array $filters): Builder
    {
        $query = $this->query()
            ->with([
                'crrs.packages',
                'crrs.customerVessel.customer',
                'accountManager.office',
                'creator',
                'irregularities',
            ])
            ->withCount('preAlertReminderSends as reminder_sent_count')
            ->where('status', '!=', 'Completed');

        $this->applyShipmentFollowUpFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function paginatePreAlertReminders(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildPreAlertRemindersQuery($filters)->paginate($perPage);
    }

    public function buildShipmentFollowUpQuery(array $filters): Builder
    {
        $query = $this->query()
            ->with([
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

        $this->applyShipmentFollowUpFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function paginateShipmentFollowUp(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildShipmentFollowUpQuery($filters)->paginate($perPage);
    }

    public function buildCostFollowUpSearchQuery(array $filters): Builder
    {
        $query = $this->query()
            ->with([
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

        $this->applyShipmentFollowUpFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function followUpFilterOptions(string $scope): array
    {
        $baseQuery = $this->query();
        if ($scope === 'not_completed') {
            $baseQuery->where('status', '!=', 'Completed');
        } elseif ($scope === 'follow_up') {
            $baseQuery->whereNotIn('status', ['Completed', 'Draft']);
        } else {
            $baseQuery->where('status', '!=', 'Cancelled');
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

        $statuses = (clone $baseQuery)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        $accountManagers = $this->customerAccountManagers();

        $creators = User::query()
            ->whereIn('id', (clone $baseQuery)->whereNotNull('created_by')->distinct()->pluck('created_by'))
            ->orderBy('name')
            ->get();

        return compact('customers', 'vessels', 'statuses', 'accountManagers', 'creators');
    }

    public function findOrFail(int $id): Shipment
    {
        return $this->query()->findOrFail($id);
    }

    public function findWithRelationsOrFail(int $id, array $relations = []): Shipment
    {
        return $this->query()->with($relations)->findOrFail($id);
    }

    public function shipmentNumberExists(string $shipmentNumber): bool
    {
        return $this->query()->where('shipment_number', $shipmentNumber)->exists();
    }

    public function searchByShipmentNumber(string $q, int $limit = 40): EloquentCollection
    {
        $pattern = $this->shipmentNumberLookupPattern(trim($q));
        if ($pattern === null) {
            return new EloquentCollection();
        }

        return $this->query()
            ->where('shipment_number', 'like', $pattern)
            ->orderByDesc('id')
            ->limit(max(1, $limit))
            ->get(['id', 'shipment_number']);
    }

    public function findByShipmentNumberLookup(string $q): ?Shipment
    {
        $q = trim($q);
        if ($q === '') {
            return null;
        }

        $exact = $this->query()
            ->where('shipment_number', $q)
            ->orderByDesc('id')
            ->first();

        if ($exact) {
            return $exact;
        }

        return $this->searchByShipmentNumber($q, 1)->first();
    }

    private function shipmentNumberLookupPattern(string $q): ?string
    {
        if ($q === '' || preg_match('/^[A-Za-z0-9]{1,3}-$/', $q)) {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9]{2,3}-/', $q)) {
            return ListSearch::prefix($q, 6);
        }

        if (preg_match('/^\d{5,}$/', $q)) {
            return '%-' . addcslashes($q, "%_\\") . '-%';
        }

        return ListSearch::contains($q, 5);
    }

    public function createShipment(array $attributes): Shipment
    {
        return Shipment::create($attributes);
    }

    public function createPreAlertReminderSend(int $shipmentId, ?int $userId): void
    {
        ShipmentPreAlertReminderSend::create([
            'shipment_id' => $shipmentId,
            'user_id' => $userId,
        ]);
    }

    public function selectableCrrsForShipment(): \Illuminate\Database\Eloquent\Collection
    {
        return Crr::with(['packages', 'documents', 'customerVessel.customer'])
            ->selectableForShipment()
            ->latest()
            ->get();
    }

    public function shipmentEditReferenceData(): array
    {
        $hubs = Hub::orderBy('hub_name')->get();
        $agents = Agent::with('country')->orderBy('agent_name')->get();
        $crrs = $this->selectableCrrsForShipment();
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

        return compact('crrs', 'hubs', 'agents', 'consigneePartyCodes');
    }

    public function replaceIrregularities(Shipment $shipment, array $irregularities): void
    {
        $shipment->irregularities()->delete();

        foreach ($irregularities as $irregularity) {
            ShipmentIrregularity::create(array_merge($irregularity, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceFlights(Shipment $shipment, array $flights): void
    {
        $shipment->flights()->delete();

        foreach ($flights as $flight) {
            ShipmentFlight::create(array_merge($flight, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceSeaLegs(Shipment $shipment, array $seaLegs): void
    {
        $shipment->seaLegs()->delete();

        foreach ($seaLegs as $seaLeg) {
            ShipmentSeaLeg::create(array_merge($seaLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceTruckLegs(Shipment $shipment, array $truckLegs): void
    {
        $shipment->truckLegs()->delete();

        foreach ($truckLegs as $truckLeg) {
            ShipmentTruckLeg::create(array_merge($truckLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceCourierLegs(Shipment $shipment, array $courierLegs): void
    {
        $shipment->courierLegs()->delete();

        foreach ($courierLegs as $courierLeg) {
            ShipmentCourierLeg::create(array_merge($courierLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceReleaseLegs(Shipment $shipment, array $releaseLegs): void
    {
        $shipment->releaseLegs()->delete();

        foreach ($releaseLegs as $releaseLeg) {
            ShipmentReleaseLeg::create(array_merge($releaseLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceOnBoardLegs(Shipment $shipment, array $onBoardLegs): void
    {
        $shipment->onBoardLegs()->delete();

        foreach ($onBoardLegs as $onBoardLeg) {
            ShipmentOnBoardLeg::create(array_merge($onBoardLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function replaceHandCarryLegs(Shipment $shipment, array $handCarryLegs): void
    {
        $shipment->handCarryLegs()->delete();

        foreach ($handCarryLegs as $handCarryLeg) {
            ShipmentHandCarryLeg::create(array_merge($handCarryLeg, [
                'shipment_id' => $shipment->id,
            ]));
        }
    }

    public function invalidSelectableCrrCount(array $crrIds): int
    {
        return Crr::query()
            ->whereIn('id', $crrIds)
            ->whereIn('status', [Crr::STATUS_IN_PROGRESS, Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED])
            ->count();
    }

    public function selectedHubValues(array $crrIds): \Illuminate\Support\Collection
    {
        return Crr::query()
            ->whereIn('id', $crrIds)
            ->get()
            ->map(function (Crr $crr) {
                $hubValue = trim((string) ($crr->hub_code ?: $crr->hub_agent));

                return $hubValue !== '' ? mb_strtolower($hubValue) : null;
            })
            ->filter()
            ->unique()
            ->values();
    }

    public function updateCrrStatuses(array $crrIds, array $attributes): int
    {
        return \App\Models\Crr::query()->whereIn('id', $crrIds)->update($attributes);
    }

    public function updateCrrStatusesForShipmentNumber(array $crrIds, string $shipmentNumber, array $attributes): int
    {
        return \App\Models\Crr::query()
            ->whereIn('id', $crrIds)
            ->where('internal_shipment', $shipmentNumber)
            ->update($attributes);
    }

    public function adminUserIds(): \Illuminate\Support\Collection
    {
        return User::query()->where('role', 'Admin')->pluck('id');
    }

    public function findManifestForShipmentOrFail(int $shipmentId, int $manifestId, bool $withShipment = false): ShipmentManifest
    {
        $query = ShipmentManifest::query()->where('shipment_id', $shipmentId);
        if ($withShipment) {
            $query->with('shipment');
        }

        return $query->findOrFail($manifestId);
    }

    public function findPreAlertForShipmentOrFail(int $shipmentId, int $preAlertId, bool $withShipment = false): ShipmentPreAlert
    {
        $query = ShipmentPreAlert::query()->where('shipment_id', $shipmentId);
        if ($withShipment) {
            $query->with('shipment');
        }

        return $query->findOrFail($preAlertId);
    }

    public function createDocument(array $attributes): ShipmentDocument
    {
        return ShipmentDocument::create($attributes);
    }

    public function findDocumentOrFail(int $docId): ShipmentDocument
    {
        return ShipmentDocument::query()->findOrFail($docId);
    }

    public function findDocumentForShipmentOrFail(int $shipmentId, int $docId): ShipmentDocument
    {
        return ShipmentDocument::query()->where('shipment_id', $shipmentId)->findOrFail($docId);
    }

    private function applyInvoicingFilters(Builder $query, array $filters): void
    {
        $billingStatuses = array_values(array_filter((array) ($filters['status'] ?? [])));
        if ($billingStatuses) {
            $wantsBilled = in_array('Billed', $billingStatuses, true);
            $wantsReady = in_array('Ready for billing', $billingStatuses, true);
            $wantsPartiallyPaid = in_array('Partially paid', $billingStatuses, true);

            $query->where(function (Builder $statusQuery) use ($wantsBilled, $wantsReady, $wantsPartiallyPaid) {
                if ($wantsReady) {
                    $statusQuery->orWhereDoesntHave('proformaInvoice');
                }

                if ($wantsBilled) {
                    $statusQuery->orWhereHas('proformaInvoice', function (Builder $invoiceQuery) {
                        $invoiceQuery->where(function (Builder $paymentQuery) {
                            $paymentQuery
                                ->where('payment_type', 'full_payment')
                                ->orWhereNull('payment_type');
                        });
                    });
                }

                if ($wantsPartiallyPaid) {
                    $statusQuery->orWhereHas('proformaInvoice', function (Builder $invoiceQuery) {
                        $invoiceQuery->where('payment_type', 'partial_payment');
                    });
                }
            });
        }

        $this->applyInvoicingShipmentFilters($query, $filters);

        if ($this->hasInvoicingCrrFilterParams($filters)) {
            $query->whereHas('crrs', fn (Builder $crrQuery) => $this->applyInvoicingCrrFilters($crrQuery, $filters));
        }
    }

    private function applyInvoicingShipmentFilters(Builder $query, array $filters): void
    {
        $invoiceNo = trim((string) ($filters['invoice_no'] ?? ($filters['proforma_no'] ?? '')));
        $shipmentNo = trim((string) ($filters['job_no'] ?? ($filters['shipment'] ?? ($filters['shipment_no'] ?? ($filters['shipment_number'] ?? '')))));
        $mawbMbl = trim((string) ($filters['mawb_mbl'] ?? ($filters['mawb'] ?? ($filters['mbl'] ?? ''))));
        $poNumber = trim((string) ($filters['po_number'] ?? ($filters['client_ref_no'] ?? '')));
        $dateFrom = trim((string) ($filters['date_from'] ?? ''));
        $dateTo = trim((string) ($filters['date_to'] ?? ''));

        $invoiceNoLike = ListSearch::contains($invoiceNo);
        $shipmentNoLike = ListSearch::prefix($shipmentNo);
        $mawbMblLike = ListSearch::prefix($mawbMbl);
        $poNumberLike = ListSearch::contains($poNumber);

        $query
            ->when($invoiceNoLike, fn ($q, $pattern) => $q->whereHas(
                'proformaInvoice',
                fn ($invoiceQuery) => $invoiceQuery->where('proforma_no', 'like', $pattern)
            ))
            ->when($shipmentNoLike, fn ($q, $pattern) => $q->where('shipment_number', 'like', $pattern))
            ->when($poNumberLike, function ($q, $pattern) {
                $q->where(function ($sub) use ($pattern) {
                    $sub->where('customer_reference', 'like', $pattern)
                        ->orWhereHas('proformaInvoice', fn ($invoiceQuery) => $invoiceQuery->where('client_ref_no', 'like', $pattern));
                });
            })
            ->when($mawbMblLike, function ($q, $pattern) {
                $q->where(function ($sub) use ($pattern) {
                    $sub->whereHas('flights', fn ($leg) => $leg->where('leg_reference', 'like', $pattern))
                        ->orWhereHas('courierLegs', fn ($leg) => $leg->where('airway_bill', 'like', $pattern))
                        ->orWhereHas('seaLegs', fn ($leg) => $leg->where('bill_of_lading', 'like', $pattern));
                });
            })
            ->when($dateFrom !== '' || $dateTo !== '', function ($q) use ($dateFrom, $dateTo) {
                $q->whereHas('proformaInvoice', function ($invoiceQuery) use ($dateFrom, $dateTo) {
                    if ($dateFrom !== '') {
                        $invoiceQuery->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo !== '') {
                        $invoiceQuery->whereDate('created_at', '<=', $dateTo);
                    }
                });
            });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function hasInvoicingCrrFilterParams(array $filters): bool
    {
        $customers = array_values(array_filter((array) ($filters['customer'] ?? [])));
        $vessels = array_values(array_filter((array) ($filters['vessel'] ?? [])));
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));

        return $customers || $vessels || $accountManagers;
    }

    private function applyInvoicingCrrFilters(Builder $query, array $filters): void
    {
        $customers = array_values(array_filter((array) ($filters['customer'] ?? [])));
        $vessels = array_values(array_filter((array) ($filters['vessel'] ?? [])));
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));

        $query
            ->when($customers, fn ($q) => $q->whereHas('customerVessel.customer', fn ($sub) => $sub->whereIn('customer_name', $customers)))
            ->when($vessels, fn ($q) => $q->whereIn('vessel_name', $vessels))
            ->when($accountManagers, function ($q) use ($accountManagers) {
                $q->where(function ($sub) use ($accountManagers) {
                    $sub->whereHas('customerVessel', fn ($cv) => $cv->whereIn('account_manager', $accountManagers))
                        ->orWhereHas('customerVessel.customer.responsible.accountManager', fn ($am) => $am->whereIn('name', $accountManagers));
                });
            });
    }

    private function applyShipmentFollowUpFilters(Builder $query, array $filters): void
    {
        $shipmentNo = trim((string) ($filters['shipment_no'] ?? ($filters['shipment_number'] ?? '')));
        $destination = trim((string) ($filters['port_destination'] ?? ($filters['destination'] ?? '')));
        $filters['shipment_number'] = $shipmentNo;
        $filters['destination'] = $destination;

        $this->applyShipmentIndexFilters($query, $filters);

        if (! empty($filters['show_etl'])) {
            $query->whereHas('crrs', fn ($q) => $q->whereRaw('UPPER(internal_shipment) = ?', ['ETL']));
        }
    }

    private function applyShipmentIndexFilters(Builder $query, array $filters): void
    {
        $customers = array_values(array_filter((array) ($filters['customer'] ?? [])));
        $vessels = array_values(array_filter((array) ($filters['vessel'] ?? [])));
        $departurePorts = array_values(array_filter((array) ($filters['departure_port_code'] ?? [])));
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));
        $creators = array_values(array_filter((array) ($filters['created_by'] ?? [])));
        $offices = array_values(array_filter((array) ($filters['office'] ?? [])));
        $services = array_values(array_filter((array) ($filters['service'] ?? [])));
        $statuses = array_values(array_filter((array) ($filters['status'] ?? [])));
        $shipmentNumber = trim((string) ($filters['shipment_number'] ?? ''));
        $serviceReference = trim((string) ($filters['service_reference'] ?? ''));
        $poNumber = trim((string) ($filters['po_number'] ?? ''));
        $consignee = trim((string) ($filters['consignee'] ?? ''));
        $destination = trim((string) ($filters['destination'] ?? ''));
        $creationDate = trim((string) ($filters['creation_date'] ?? ''));

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

    private function customerAccountManagers(): EloquentCollection
    {
        return Contact::query()
            ->whereIn(
                'id',
                CustomerResponsible::query()
                    ->whereNotNull('account_manager_id')
                    ->distinct()
                    ->pluck('account_manager_id')
            )
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'office_id']);
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
