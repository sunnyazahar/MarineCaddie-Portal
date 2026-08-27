<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\CrrCost;
use App\Models\CrrDocument;
use App\Models\CrrPackage;
use App\Models\CustomerVessel;
use App\Models\Hub;
use App\Repositories\Contracts\CrrRepositoryInterface;
use App\Models\Supplier;
use App\Support\ListSearch;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrrRepository extends BaseRepository implements CrrRepositoryInterface
{
    protected string $modelClass = Crr::class;

    public function buildIndexQuery(array $filters): Builder
    {
        $query = $this->query()->with([
            'packages',
            'documents',
            'customerVessel.customer.responsible.accountManager.office',
        ]);

        $this->applyStockIndexFilters($query, $filters);

        return $query->orderByDesc('id');
    }

    public function paginateIndex(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildIndexQuery($filters)->paginate($perPage);
    }

    public function indexFilterOptions(): array
    {
        $customers = DB::table('customers')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->orderBy('customer_name')
            ->pluck('customer_name');

        $vessels = $this->query()
            ->whereNotNull('vessel_name')
            ->where('vessel_name', '!=', '')
            ->distinct()
            ->orderBy('vessel_name')
            ->pluck('vessel_name');

        $accountManagers = DB::table('contacts')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $offices = DB::table('offices')
            ->whereNotNull('office_name')
            ->where('office_name', '!=', '')
            ->distinct()
            ->orderBy('office_name')
            ->pluck('office_name');

        $hubAgentOptions = $this->query()
            ->whereNotNull('hub_agent')
            ->where('hub_agent', '!=', '')
            ->distinct()
            ->orderBy('hub_agent')
            ->pluck('hub_agent')
            ->values();

        return compact('customers', 'vessels', 'accountManagers', 'offices', 'hubAgentOptions');
    }

    public function buildStockFollowUpQuery(array $filters): Builder
    {
        $query = $this->query()
            ->stockFollowUp()
            ->with([
                'packages',
                'documents',
                'customerVessel.customer.responsible.accountManager',
                'shipments',
                'registeredBy',
            ]);

        $this->applyStockFollowUpFilters($query, $filters);

        return $query->latest();
    }

    public function paginateStockFollowUp(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->buildStockFollowUpQuery($filters)->paginate($perPage);
    }

    public function stockFollowUpFilterOptions(): array
    {
        $customers = DB::table('customers')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->orderBy('customer_name')
            ->pluck('customer_name');

        $accountManagers = DB::table('contacts')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        return compact('customers', 'accountManagers');
    }

    public function buildPickupWorkListQuery(array $filters, Collection $handledByMap): Builder
    {
        $query = $this->query()
            ->pickupWorkList()
            ->with([
                'packages',
                'documents',
                'customerVessel.customer.responsible.accountManager',
            ]);

        $this->applyPickupWorkListFilters($query, $filters, $handledByMap);

        return $query->latest();
    }

    public function paginatePickupWorkList(array $filters, Collection $handledByMap, int $perPage): LengthAwarePaginator
    {
        return $this->buildPickupWorkListQuery($filters, $handledByMap)->paginate($perPage);
    }

    public function pickupWorkListFilterOptions(Collection $handledByMap): array
    {
        $accountManagers = DB::table('contacts')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $vessels = $this->query()
            ->pickupWorkList()
            ->whereNotNull('vessel_name')
            ->where('vessel_name', '!=', '')
            ->distinct()
            ->orderBy('vessel_name')
            ->pluck('vessel_name');

        $hubAgents = $this->query()
            ->pickupWorkList()
            ->whereNotNull('hub_agent')
            ->where('hub_agent', '!=', '')
            ->distinct()
            ->orderBy('hub_agent')
            ->pluck('hub_agent');

        $handledByOptions = $handledByMap->values()->unique()->sort()->values();

        return compact('accountManagers', 'vessels', 'handledByOptions', 'hubAgents');
    }

    public function hubAgentHandledByMap(): Collection
    {
        $hubAgentValues = $this->query()
            ->pickupWorkList()
            ->whereNotNull('hub_agent')
            ->where('hub_agent', '!=', '')
            ->distinct()
            ->pluck('hub_agent');

        $handledByMap = collect();

        Hub::query()
            ->where(function ($query) use ($hubAgentValues) {
                $query->whereIn('code', $hubAgentValues)
                    ->orWhereIn('hub_name', $hubAgentValues);
            })
            ->get(['code', 'hub_name', 'responsible_manager'])
            ->each(function (Hub $hub) use ($handledByMap) {
                if ($hub->responsible_manager) {
                    $handledByMap->put($hub->code, $hub->responsible_manager);
                    $handledByMap->put($hub->hub_name, $hub->responsible_manager);
                }
            });

        Agent::query()
            ->where(function ($query) use ($hubAgentValues) {
                $query->whereIn('code', $hubAgentValues)
                    ->orWhereIn('agent_name', $hubAgentValues);
            })
            ->get(['code', 'agent_name', 'responsible_manager'])
            ->each(function (Agent $agent) use ($handledByMap) {
                if ($agent->responsible_manager) {
                    $handledByMap->put($agent->code, $agent->responsible_manager);
                    $handledByMap->put($agent->agent_name, $agent->responsible_manager);
                }
            });

        return $handledByMap;
    }

    public function resolveHubAgentForPrint(Crr $crr): array
    {
        $hubAgent = Hub::query()
            ->where('code', $crr->hub_agent)
            ->orWhere('hub_name', $crr->hub_agent)
            ->first();

        if (! $hubAgent) {
            $hubAgent = Agent::query()
                ->where('code', $crr->hub_agent)
                ->orWhere('agent_name', $crr->hub_agent)
                ->first();
        }

        $code = $hubAgent?->code ?: $crr->hub_code;
        $name = $hubAgent instanceof Hub
            ? $hubAgent->hub_name
            : ($hubAgent instanceof Agent ? $hubAgent->agent_name : null);

        return [$code, $name];
    }

    public function stockNumberExists(string $stockNumber): bool
    {
        return $this->query()->where('stock_number', $stockNumber)->exists();
    }

    public function createCrr(array $attributes): Crr
    {
        return $this->modelClass::create($attributes);
    }

    public function storePackages(int $crrId, array $packages): void
    {
        foreach ($packages as $package) {
            CrrPackage::create(array_merge($package, [
                'crr_id' => $crrId,
            ]));
        }
    }

    public function replacePackages(Crr $crr, array $packages): void
    {
        $crr->packages()->delete();
        $this->storePackages($crr->id, $packages);
    }

    public function storeCosts(int $crrId, array $costs): void
    {
        foreach ($costs as $cost) {
            CrrCost::create(array_merge($cost, [
                'crr_id' => $crrId,
            ]));
        }
    }

    public function replaceCosts(Crr $crr, array $costs): void
    {
        $crr->costs()->delete();
        $this->storeCosts($crr->id, $costs);
    }

    public function editReferenceData(): array
    {
        $vessels = CustomerVessel::with('customer.responsible.accountManager')
            ->select('vessel', 'customer_id')
            ->groupBy('vessel', 'customer_id')
            ->get();
        $hubs = Hub::orderBy('hub_name')->get();
        $agents = Agent::with('country')->orderBy('agent_name')->get();
        $suppliers = Supplier::with('country')->orderBy('supplier_name')->get();

        return compact('vessels', 'hubs', 'agents', 'suppliers');
    }

    public function findWithRelationsOrFail(int $id, array $relations = []): Crr
    {
        return $this->query()->with($relations)->findOrFail($id);
    }

    public function findOrFail(int $id): Crr
    {
        return $this->query()->findOrFail($id);
    }

    public function selectedWithRelations(array $ids, array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->with($relations)
            ->whereIn('id', $ids)
            ->orderByDesc('id')
            ->get();
    }

    public function createDocument(array $attributes): CrrDocument
    {
        return CrrDocument::create($attributes);
    }

    public function findDocumentForCrrOrFail(int $crrId, int $docId): CrrDocument
    {
        return CrrDocument::query()->where('crr_id', $crrId)->findOrFail($docId);
    }

    public function findDocumentOrFail(int $docId): CrrDocument
    {
        return CrrDocument::query()->findOrFail($docId);
    }

    private function applyStockIndexFilters(Builder $query, array $filters): void
    {
        $hubAgents = array_values(array_filter((array) ($filters['hub_agent'] ?? [])));
        $customers = array_values(array_filter((array) ($filters['customer'] ?? [])));
        $vessels = array_values(array_filter((array) ($filters['vessel'] ?? [])));
        $statuses = array_values(array_filter((array) ($filters['status'] ?? [])));
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));
        $offices = array_values(array_filter((array) ($filters['office'] ?? [])));
        $stockNumber = trim((string) ($filters['stock_number'] ?? ''));
        $poNumber = trim((string) ($filters['po_number'] ?? ''));
        $supplier = trim((string) ($filters['supplier'] ?? ''));
        $serviceReference = trim((string) ($filters['supplier_reference'] ?? ($filters['service_reference'] ?? '')));
        $shipment = trim((string) ($filters['shipment'] ?? ''));
        $transitId = trim((string) ($filters['transit_id'] ?? ''));

        $stockNumberLike = ListSearch::prefix($stockNumber);
        $supplierLike = ListSearch::prefix($supplier);
        $serviceReferenceLike = ListSearch::prefix($serviceReference);
        $shipmentLike = ListSearch::prefix($shipment);
        $transitIdLike = ListSearch::prefix($transitId);
        $poExact = mb_strlen($poNumber) >= 3 ? $poNumber : '';

        $hasNonStatus = $hubAgents || $customers || $vessels || $accountManagers || $offices
            || $stockNumberLike || $poExact !== '' || $supplierLike || $serviceReferenceLike
            || $shipmentLike || $transitIdLike;

        $statusValues = [];
        $labelLookup = collect(Crr::getStatusLabels())->mapWithKeys(fn ($label, $value) => [strtolower($label) => $value]);
        foreach ($statuses as $status) {
            $statusValues[] = $labelLookup[strtolower((string) $status)] ?? $status;
        }

        $query
            ->when($hubAgents, function ($q) use ($hubAgents) {
                $related = Hub::query()
                    ->where(function ($sub) use ($hubAgents) {
                        $sub->whereIn('code', $hubAgents)->orWhereIn('hub_name', $hubAgents);
                    })
                    ->get(['code', 'hub_name']);
                $agentRelated = Agent::query()
                    ->where(function ($sub) use ($hubAgents) {
                        $sub->whereIn('code', $hubAgents)->orWhereIn('agent_name', $hubAgents);
                    })
                    ->get(['code', 'agent_name']);

                $values = collect($hubAgents)
                    ->merge($related->pluck('code'))
                    ->merge($related->pluck('hub_name'))
                    ->merge($agentRelated->pluck('code'))
                    ->merge($agentRelated->pluck('agent_name'))
                    ->unique()
                    ->values();

                $q->whereIn('hub_agent', $values);
            })
            ->when($customers, fn ($q) => $q->whereHas('customerVessel.customer', fn ($sub) => $sub->whereIn('customer_name', $customers)))
            ->when($vessels, fn ($q) => $q->whereIn('vessel_name', $vessels))
            ->when($accountManagers, function ($q) use ($accountManagers) {
                $q->where(function ($sub) use ($accountManagers) {
                    $sub->whereHas('customerVessel', fn ($cv) => $cv->whereIn('account_manager', $accountManagers))
                        ->orWhereHas('customerVessel.customer.responsible.accountManager', fn ($am) => $am->whereIn('name', $accountManagers));
                });
            })
            ->when($offices, fn ($q) => $q->whereHas('customerVessel.customer.responsible.accountManager.office', fn ($sub) => $sub->whereIn('office_name', $offices)))
            ->when($stockNumberLike, fn ($q, $pattern) => $q->where('stock_number', 'like', $pattern))
            ->when($poExact !== '', fn ($q) => $q->whereJsonContains('po_numbers', $poExact))
            ->when($supplierLike, fn ($q, $pattern) => $q->where('supplier', 'like', $pattern))
            ->when($serviceReferenceLike, fn ($q, $pattern) => $q->where('supplier_reference', 'like', $pattern))
            ->when($shipmentLike, fn ($q, $pattern) => $q->where('internal_shipment', 'like', $pattern))
            ->when($transitIdLike, fn ($q, $pattern) => $q->where('transit_id', 'like', $pattern))
            ->when($statusValues, fn ($q) => $q->whereIn('status', $statusValues))
            ->when(! $statusValues && ! $hasNonStatus, fn ($q) => $q->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED]));
    }

    private function applyStockFollowUpFilters(Builder $query, array $filters): void
    {
        $customers = array_values(array_filter((array) ($filters['customer'] ?? [])));
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));

        $query
            ->when($customers, fn ($q) => $q->whereHas('customerVessel.customer', fn ($sub) => $sub->whereIn('customer_name', $customers)))
            ->when($accountManagers, function ($q) use ($accountManagers) {
                $q->where(function ($sub) use ($accountManagers) {
                    $sub->whereHas('customerVessel', fn ($cv) => $cv->whereIn('account_manager', $accountManagers))
                        ->orWhereHas('customerVessel.customer.responsible.accountManager', fn ($am) => $am->whereIn('name', $accountManagers));
                });
            });
    }

    private function applyPickupWorkListFilters(Builder $query, array $filters, Collection $handledByMap): void
    {
        $accountManagers = array_values(array_filter((array) ($filters['account_manager'] ?? [])));
        $handledBy = array_values(array_filter((array) ($filters['handled_by'] ?? [])));
        $vessels = array_values(array_filter((array) ($filters['vessel'] ?? [])));
        $hubAgents = array_values(array_filter((array) ($filters['hub_agent'] ?? [])));
        $stockNumber = trim((string) ($filters['stock_number'] ?? ''));
        $supplierRef = trim((string) ($filters['supplier_reference'] ?? ''));

        $query
            ->when($accountManagers, function ($q) use ($accountManagers) {
                $q->where(function ($sub) use ($accountManagers) {
                    $sub->whereHas('customerVessel', fn ($cv) => $cv->whereIn('account_manager', $accountManagers))
                        ->orWhereHas('customerVessel.customer.responsible.accountManager', fn ($am) => $am->whereIn('name', $accountManagers));
                });
            })
            ->when($handledBy, function ($q) use ($handledBy, $handledByMap) {
                $keys = $handledByMap->filter(fn ($name) => in_array($name, $handledBy, true))->keys();
                $q->whereIn('hub_agent', $keys);
            })
            ->when($vessels, fn ($q) => $q->whereIn('vessel_name', $vessels))
            ->when($hubAgents, fn ($q) => $q->whereIn('hub_agent', $hubAgents))
            ->when(ListSearch::prefix($stockNumber), fn ($q, $pattern) => $q->where('stock_number', 'like', $pattern))
            ->when(ListSearch::prefix($supplierRef), fn ($q, $pattern) => $q->where('supplier_reference', 'like', $pattern));

        $this->applyDateRangeFilter($query, $filters['expected_delivery'] ?? null, 'expected_delivery_date');
        $this->applyDateRangeFilter($query, $filters['deadline_warehouse'] ?? null, 'deadline_warehouse');
        $this->applyDateRangeFilter($query, $filters['pickup_date'] ?? null, 'actual_delivery_date');
    }

    private function applyDateRangeFilter(Builder $query, mixed $value, string $column): void
    {
        $value = trim((string) $value);
        if ($value === '' || ! str_contains($value, ' - ')) {
            return;
        }

        [$from, $to] = array_map('trim', explode(' - ', $value, 2));

        try {
            $start = Carbon::createFromFormat('d.m.Y', $from)->startOfDay();
            $end = Carbon::createFromFormat('d.m.Y', $to)->endOfDay();
            $query->whereBetween($column, [$start->toDateString(), $end->toDateString()]);
        } catch (\Exception $e) {
            // Ignore unparseable date ranges.
        }
    }
}
