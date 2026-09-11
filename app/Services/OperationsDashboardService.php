<?php

namespace App\Services;

use App\Models\AdministrationChangeLog;
use App\Models\Agent;
use App\Models\AgentUser;
use App\Models\Crr;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerVessel;
use App\Models\Hub;
use App\Models\HubUser;
use App\Models\Office;
use App\Models\OfficeBankAccount;
use App\Models\OtherCompany;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Contracts\OperationsDashboardRepositoryInterface;
use App\Support\PackageVolumeMetrics;
use App\Support\ListSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OperationsDashboardService
{
    public function __construct(
        private OperationsDashboardRepositoryInterface $dashboardRepository,
    ) {}

    public function build(User $user, int $period = 30): array
    {
        $period = in_array($period, [7, 30, 90], true) ? $period : 30;
        $assistantScope = $this->assistantScope($user);
        $crrs = $this->visibleCrrs($user);
        $shipments = $this->visibleShipments($user);
        $assistantShipmentRelations = $this->assistantShipmentRelations();
        $assistantStockRelations = $this->assistantStockRelations();
        $activeCrrStatuses = [Crr::STATUS_NEW, Crr::STATUS_PENDING, Crr::STATUS_ACTIVE, Crr::STATUS_IN_PROGRESS];
        $activeShipmentStatuses = ['Draft', 'Pending', 'In process', 'In transit', 'Delivered'];

        $stockStatusCounts = (clone $crrs)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $shipmentStatusCounts = (clone $shipments)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stockStatusSeries = collect(Crr::getStatusLabels())->map(fn ($label, $status) => [
            'label' => $label,
            'value' => (int) ($stockStatusCounts[$status] ?? 0),
            'class' => Crr::statusBadgeClass($status),
        ])->values();

        $shipmentStatusSeries = collect(['Draft', 'Pending', 'In process', 'In transit', 'Delivered', 'Completed'])
            ->map(fn ($status) => [
                'label' => $status,
                'value' => (int) ($shipmentStatusCounts[$status] ?? 0),
                'class' => Shipment::statusColorClass($status),
            ]);

        $periodStart = today()->subDays($period - 1);
        $stockDaily = (clone $crrs)
            ->whereDate('created_at', '>=', $periodStart)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $shipmentDaily = (clone $shipments)
            ->whereDate('created_at', '>=', $periodStart)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $trendLabels = collect(range(0, $period - 1))
            ->map(fn ($offset) => $periodStart->copy()->addDays($offset)->format('Y-m-d'));

        $visibleShipmentIds = (clone $shipments)->select('shipments.id');
        $assistantShipmentCreationWindowDays = 90;
        $assistantShipmentCreationDaily = $this->assistantShipmentCreationDaily(
            $shipments,
            $assistantShipmentCreationWindowDays
        );
        $assistantShipmentCreationCounts = [
            'today' => $this->assistantShipmentCreationCountFromDaily($assistantShipmentCreationDaily, 1),
            '7' => $this->assistantShipmentCreationCountFromDaily($assistantShipmentCreationDaily, 7),
            '30' => $this->assistantShipmentCreationCountFromDaily($assistantShipmentCreationDaily, 30),
            '90' => $this->assistantShipmentCreationCountFromDaily(
                $assistantShipmentCreationDaily,
                $assistantShipmentCreationWindowDays
            ),
        ];
        $kpis = [
            'activeStocks' => (clone $crrs)->whereIn('status', $activeCrrStatuses)->count(),
            'unacceptedStocks' => (clone $crrs)
                ->where('accept', false)
                ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
                ->count(),
            'pickupQueue' => (clone $crrs)
                ->whereJsonContains('flags', 'Pick up')
                ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
                ->count(),
            'urgentStocks' => (clone $crrs)
                ->whereIn('priority', ['Urgent', 'Critical', 'Prevent offhire'])
                ->whereIn('status', $activeCrrStatuses)
                ->count(),
            'activeShipments' => (clone $shipments)->whereIn('status', $activeShipmentStatuses)->count(),
            'newShipmentsToday' => (int) ($assistantShipmentCreationCounts['today'] ?? 0),
            'cancelledShipments' => (clone $shipments)
                ->whereIn('status', ['Cancelled', 'Canceled'])
                ->count(),
            'overdueArrivals' => (clone $shipments)
                ->whereIn('status', $activeShipmentStatuses)
                ->whereNotNull('deadline_arrival')
                ->whereDate('deadline_arrival', '<', today())
                ->count(),
            'preAlertsDue' => (clone $shipments)
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->whereNotNull('pre_alert_reminder')
                ->whereDate('pre_alert_reminder', '<=', today())
                ->count(),
            'openIrregularities' => $this->dashboardRepository
                ->openIrregularitiesCountForShipmentQuery(clone $visibleShipmentIds),
            'remindersToday' => $this->dashboardRepository
                ->remindersTodayCountForShipmentQuery($visibleShipmentIds),
        ];
        $serviceSeries = (clone $shipments)
            ->whereIn('status', $activeShipmentStatuses)
            ->whereNotNull('service')
            ->selectRaw('service, COUNT(*) as total')
            ->groupBy('service')
            ->orderByDesc('total')
            ->pluck('total', 'service')
            ->map(fn ($total, $service) => ['label' => $service, 'value' => (int) $total])
            ->values();
        $overdueShipments = (clone $shipments)
            ->with($assistantShipmentRelations)
            ->whereIn('status', $activeShipmentStatuses)
            ->whereNotNull('deadline_arrival')
            ->whereDate('deadline_arrival', '<', today())
            ->orderBy('deadline_arrival')
            ->limit(8)
            ->get();
        $stockFollowUps = (clone $crrs)
            ->with($assistantStockRelations)
            ->where('accept', false)
            ->whereNotIn('status', [Crr::STATUS_COMPLETED, Crr::STATUS_CANCELLED, Crr::STATUS_ARCHIVED])
            ->latest('updated_at')
            ->limit(8)
            ->get();
        $assistantShipments = (clone $shipments)
            ->with($assistantShipmentRelations)
            ->latest('updated_at')
            ->limit(12)
            ->get()
            ->concat($overdueShipments)
            ->unique('id')
            ->values();
        $assistantStocks = (clone $crrs)
            ->with($assistantStockRelations)
            ->latest('updated_at')
            ->limit(12)
            ->get()
            ->concat($stockFollowUps)
            ->unique('id')
            ->values();
        $assistantShipmentSummaries = $this->mapAssistantShipmentCollection($assistantShipments);
        $overdueShipmentSummaries = $this->mapAssistantShipmentCollection($overdueShipments);
        $assistantKpis = $this->assistantScopeKpis($kpis, $assistantScope);

        return [
            'period' => $period,
            'isScoped' => false,
            'hasAssignments' => true,
            'kpis' => $kpis,
            'stockStatusSeries' => $stockStatusSeries,
            'shipmentStatusSeries' => $shipmentStatusSeries,
            'serviceSeries' => $serviceSeries,
            'trend' => [
                'labels' => $trendLabels->map(fn ($day) => Carbon::parse($day)->format('d M')),
                'stocks' => $trendLabels->map(fn ($day) => (int) ($stockDaily[$day] ?? 0)),
                'shipments' => $trendLabels->map(fn ($day) => (int) ($shipmentDaily[$day] ?? 0)),
            ],
            'overdueShipments' => $overdueShipments,
            'stockFollowUps' => $stockFollowUps,
            'assistant' => [
                'scope' => $assistantScope,
                'period' => $period,
                'generatedAt' => now()->format('d M Y H:i'),
                'readOnly' => 'MC Assistant only shows details and summaries here. Create, update, and delete actions are not available.',
                'kpis' => $assistantKpis,
                'shipmentCreationWindowDays' => ($assistantScope['allows']['shipments'] ?? false)
                    ? $assistantShipmentCreationWindowDays
                    : 0,
                'shipmentCreationCounts' => ($assistantScope['allows']['shipments'] ?? false)
                    ? $assistantShipmentCreationCounts
                    : ['today' => 0, '7' => 0, '30' => 0, '90' => 0],
                'shipmentCreationDaily' => ($assistantScope['allows']['shipments'] ?? false)
                    ? $assistantShipmentCreationDaily
                    : [],
                'stockStatuses' => $stockStatusSeries->values()->all(),
                'shipmentStatuses' => $assistantScope['allows']['shipments'] ? $shipmentStatusSeries->values()->all() : [],
                'services' => $assistantScope['allows']['shipments'] ? $serviceSeries->take(6)->values()->all() : [],
                'shipments' => $assistantScope['allows']['shipments'] ? $assistantShipmentSummaries : [],
                'stocks' => $assistantScope['allows']['stocks']
                    ? $assistantStocks->map(fn (Crr $crr) => $this->mapAssistantStock($crr))->all()
                    : [],
                'overdueShipments' => $assistantScope['allows']['shipments'] ? $overdueShipmentSummaries : [],
                'stockFollowUps' => $assistantScope['allows']['stockFollowUps']
                    ? $stockFollowUps->map(fn (Crr $crr) => $this->mapAssistantStock($crr))->all()
                    : [],
            ],
        ];
    }

    public function visibleCrrs(?User $user = null): Builder
    {
        return $this->dashboardRepository->visibleCrrsQuery($user);
    }

    public function visibleShipments(?User $user = null): Builder
    {
        return $this->dashboardRepository->visibleShipmentsQuery($user);
    }

    public function assistantLookup(User $user, string $query): array
    {
        $query = trim($query);
        $scope = $this->assistantScope($user);

        if ($query === '') {
            return $this->assistantLookupMiss($scope);
        }

        $allowedTargets = $scope['allowedLookupTargets'];
        $explicitTargets = $this->assistantExplicitLookupTargets($query);

        if ($explicitTargets !== []) {
            $allowedExplicitTargets = array_values(array_intersect($explicitTargets, $allowedTargets));
            $explicitBlockedTargets = array_values(array_diff($explicitTargets, $allowedTargets));

            if ($allowedExplicitTargets === []) {
                foreach ($explicitBlockedTargets as $target) {
                    if ($this->assistantLookupRecordForTarget($target, $user, $query) !== null) {
                        return $this->assistantLookupScopeBlocked($scope, $target);
                    }
                }

                return $this->assistantLookupScopeBlocked($scope, $explicitTargets[0]);
            }

            foreach ($allowedExplicitTargets as $target) {
                $match = $this->assistantLookupMatch($target, $user, $query, $scope);

                if ($match !== null) {
                    return $match;
                }
            }
        }

        $orderedTargets = $this->assistantLookupTargets($query);
        $remainingAllowedTargets = array_values(array_diff(array_intersect($orderedTargets, $allowedTargets), $explicitTargets));

        foreach (array_values(array_diff($remainingAllowedTargets, $this->assistantAdministrationTargets($remainingAllowedTargets))) as $target) {
            $match = $this->assistantLookupMatch($target, $user, $query, $scope);

            if ($match !== null) {
                return $match;
            }
        }

        $administrationMatch = $this->assistantBestAdministrationLookupMatch($query, $remainingAllowedTargets, $scope);

        if ($administrationMatch !== null) {
            return $administrationMatch;
        }

        foreach (array_values(array_unique(array_merge(
            array_diff($explicitTargets, $allowedTargets),
            array_diff($orderedTargets, $allowedTargets),
        ))) as $target) {
            if ($this->assistantLookupRecordForTarget($target, $user, $query) !== null) {
                return $this->assistantLookupScopeBlocked($scope, $target);
            }
        }

        return $this->assistantLookupMiss($scope);
    }

    private function assistantScope(User $user): array
    {
        $role = trim((string) ($user->role ?: 'User'));
        $stocksOnly = in_array($role, ['Agents', 'Supplier'], true);

        return [
            'role' => $role,
            'mode' => $stocksOnly ? 'stocks-only' : 'dashboard',
            'allowedLookupTargets' => $stocksOnly
                ? ['stock']
                : ['stock', 'shipment', 'office', 'hub', 'agent', 'supplier', 'customer', 'contact', 'vessel', 'user', 'change_log'],
            'allows' => [
                'overview' => ! $stocksOnly,
                'shipments' => ! $stocksOnly,
                'stocks' => true,
                'administration' => ! $stocksOnly,
                'services' => ! $stocksOnly,
                'overdueShipments' => ! $stocksOnly,
                'stockFollowUps' => true,
            ],
        ];
    }

    private function assistantScopeKpis(array $kpis, array $scope): array
    {
        if (($scope['allows']['shipments'] ?? false) === true) {
            return $kpis;
        }

        foreach ([
            'activeShipments',
            'newShipmentsToday',
            'cancelledShipments',
            'overdueArrivals',
            'preAlertsDue',
            'openIrregularities',
            'remindersToday',
        ] as $key) {
            $kpis[$key] = 0;
        }

        return $kpis;
    }

    private function assistantShipmentCreationDaily(Builder $shipments, int $days): array
    {
        $windowDays = max(1, $days);
        $windowStart = today()->subDays($windowDays - 1);
        $dailyCounts = (clone $shipments)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereDate('created_at', '>=', $windowStart)
            ->groupBy('day')
            ->pluck('total', 'day');

        return collect(range(0, $windowDays - 1))
            ->mapWithKeys(function (int $offset) use ($windowStart, $dailyCounts): array {
                $date = $windowStart->copy()->addDays($offset)->format('Y-m-d');

                return [$date => (int) ($dailyCounts[$date] ?? 0)];
            })
            ->all();
    }

    private function assistantShipmentCreationCountFromDaily(array $dailyCounts, int $days): int
    {
        if ($dailyCounts === []) {
            return 0;
        }

        $windowDays = max(1, min($days, count($dailyCounts)));

        return (int) array_sum(array_slice(array_values($dailyCounts), -$windowDays));
    }

    private function assistantLookupMiss(array $scope): array
    {
        return [
            'matched' => false,
            'type' => null,
            'item' => null,
            'scopeBlocked' => false,
            'sensitiveBlocked' => false,
            'blockedReason' => null,
            'scope' => $scope,
        ];
    }

    private function assistantLookupScopeBlocked(array $scope, string $target): array
    {
        return [
            'matched' => false,
            'type' => $target,
            'item' => null,
            'scopeBlocked' => true,
            'sensitiveBlocked' => false,
            'blockedReason' => null,
            'scope' => $scope,
        ];
    }

    private function assistantLookupSensitiveBlocked(array $scope, string $target, string $reason): array
    {
        return [
            'matched' => false,
            'type' => $target,
            'item' => null,
            'scopeBlocked' => false,
            'sensitiveBlocked' => true,
            'blockedReason' => $reason,
            'scope' => $scope,
        ];
    }

    private function assistantLookupRecordForTarget(string $target, User $user, string $query): mixed
    {
        return match ($target) {
            'stock' => $this->findAssistantStockRecord($user, $query),
            'shipment' => $this->findAssistantShipmentRecord($user, $query),
            'office' => $this->findAssistantOfficeRecord($query),
            'hub' => $this->findAssistantHubRecord($query),
            'agent' => $this->findAssistantAgentRecord($query),
            'supplier' => $this->findAssistantSupplierRecord($query),
            'customer' => $this->findAssistantCustomerRecord($query),
            'contact' => $this->findAssistantContactRecord($query),
            'vessel' => $this->findAssistantVesselRecord($query),
            'user' => $this->findAssistantUserRecord($query),
            'change_log' => $this->findAssistantChangeLogRecord($query),
            default => null,
        };
    }

    private function assistantLookupMatch(string $target, User $user, string $query, array $scope): ?array
    {
        $item = $this->assistantLookupRecordForTarget($target, $user, $query);

        if ($item === null) {
            return null;
        }

        return $this->assistantLookupResponse($target, $item, $query, $scope);
    }

    private function assistantLookupResponse(string $target, mixed $item, string $query, array $scope): array
    {
        $mappedItem = $this->mapAssistantLookupItem($target, $item);

        if (($reason = $this->assistantSensitiveLookupReason($target, $query, $mappedItem)) !== null) {
            return $this->assistantLookupSensitiveBlocked($scope, $target, $reason);
        }

        return [
            'matched' => true,
            'type' => $target,
            'item' => $mappedItem,
            'scopeBlocked' => false,
            'sensitiveBlocked' => false,
            'blockedReason' => null,
            'scope' => $scope,
        ];
    }

    /**
     * @param  list<string>  $targets
     * @return list<string>
     */
    private function assistantAdministrationTargets(array $targets): array
    {
        return array_values(array_intersect($targets, ['office', 'hub', 'agent', 'supplier', 'customer', 'contact', 'vessel', 'user', 'change_log']));
    }

    /**
     * @param  list<string>  $targets
     */
    private function assistantBestAdministrationLookupMatch(string $query, array $targets, array $scope): ?array
    {
        $bestCandidate = null;

        foreach ($this->assistantAdministrationTargets($targets) as $target) {
            $candidate = $this->assistantAdministrationCandidateForTarget($target, $query);

            if ($candidate === null) {
                continue;
            }

            if ($bestCandidate === null || $candidate['score'] > $bestCandidate['score']) {
                $bestCandidate = $candidate;
            }
        }

        if ($bestCandidate === null) {
            return null;
        }

        return $this->assistantLookupResponse($bestCandidate['type'], $bestCandidate['record'], $query, $scope);
    }

    private function assistantSensitiveLookupReason(string $target, string $query, array $item): ?string
    {
        if (in_array($target, ['hub', 'agent'], true)) {
            return null;
        }

        $intentText = $this->assistantLookupIntentText($item, $query);
        $sensitiveText = $intentText !== '' ? $intentText : $query;

        return $this->assistantHasSensitiveCredentialIntent($sensitiveText)
            ? 'credentials'
            : null;
    }

    private function assistantLookupIntentText(array $item, string $query): string
    {
        $intentText = ' ' . Str::lower($query) . ' ';
        $identityValues = collect($item['identityValues'] ?? [])
            ->merge([
                $item['name'] ?? null,
                $item['identifier'] ?? null,
                $item['number'] ?? null,
            ])
            ->map(fn ($value) => Str::lower(trim((string) $value)))
            ->filter()
            ->unique()
            ->values();

        foreach ($identityValues as $value) {
            $pattern = '/\b' . str_replace('\ ', '\s+', preg_quote($value, '/')) . '\b/u';
            $intentText = preg_replace($pattern, ' ', $intentText) ?? $intentText;
        }

        return trim(preg_replace('/\s+/u', ' ', $intentText) ?? '');
    }

    private function assistantHasSensitiveCredentialIntent(string $query): bool
    {
        return preg_match('/\b(pass\s*word|passcode|credential(?:s)?|secret\s+code)\b/u', Str::lower($query)) === 1;
    }

    private function assistantAdministrationCandidateForTarget(string $target, string $query): ?array
    {
        $score = null;

        $record = match ($target) {
            'office' => $this->findAssistantOfficeRecord($query, $score),
            'hub' => $this->findAssistantHubRecord($query, $score),
            'agent' => $this->findAssistantAgentRecord($query, $score),
            'supplier' => $this->findAssistantSupplierRecord($query, $score),
            'customer' => $this->findAssistantCustomerRecord($query, $score),
            'contact' => $this->findAssistantContactRecord($query, $score),
            'vessel' => $this->findAssistantVesselRecord($query, $score),
            'user' => $this->findAssistantUserRecord($query, $score),
            'change_log' => $this->findAssistantChangeLogRecord($query, $score),
            default => null,
        };

        if ($record === null || $score === null) {
            return null;
        }

        return [
            'type' => $target,
            'record' => $record,
            'score' => $score,
        ];
    }

    private function mapAssistantShipmentCollection($shipments): array
    {
        $shipments = collect($shipments)->values();

        if ($shipments->isEmpty()) {
            return [];
        }

        $partyNames = Shipment::batchResolvePartyNames($shipments);
        $portCities = Shipment::batchResolvePortCities($shipments);

        return $shipments
            ->map(fn (Shipment $shipment) => $this->mapAssistantShipment($shipment, $partyNames, $portCities))
            ->all();
    }

    private function mapAssistantShipment(Shipment $shipment, array $partyNames = [], array $portCities = []): array
    {
        $shipment->loadMissing([
            'accountManager',
            'creator',
            'changeLogs.user',
            'documents',
            'crrs.customerVessel.customer',
            'crrs.packages',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        if ($partyNames === []) {
            $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        }

        if ($portCities === []) {
            $portCities = Shipment::batchResolvePortCities(collect([$shipment]));
        }

        $linkedStocks = $shipment->crrs
            ->pluck('stock_number')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $flags = collect($shipment->flags ?? [])
            ->map(fn ($flag) => trim((string) $flag))
            ->filter()
            ->values()
            ->all();
        $documents = $shipment->documents
            ->sortBy('id')
            ->values()
            ->map(fn ($document) => [
                'name' => $this->normalizeText($document->file_name) ?: 'Document',
                'type' => $this->normalizeText($document->file_type) ?: 'Document',
                'internal' => (bool) $document->is_internal,
                'addedAt' => $this->formatDate($document->created_at),
            ])
            ->all();
        $stockItems = $shipment->crrs
            ->map(fn (Crr $crr) => $this->mapAssistantShipmentStockItem($crr))
            ->values()
            ->all();
        $totalWeight = (float) $shipment->crrs->sum(
            fn (Crr $crr) => (float) $crr->packages->sum('weight')
        );
        $totalCbm = (float) $shipment->crrs->sum(
            fn (Crr $crr) => (float) $crr->packages->sum('cbm')
        );
        $totalValue = (float) $shipment->crrs->sum(
            fn (Crr $crr) => (float) ($crr->customs_value ?? 0)
        );
        $valueCurrencies = $shipment->crrs
            ->pluck('currency')
            ->map(fn ($currency) => trim((string) $currency))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $customerNames = $shipment->customer_names
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $transportMeta = $this->assistantTransportMeta($shipment->service);
        $transportLegs = $this->mapAssistantTransportLegs($shipment, $portCities);

        return [
            'id' => (int) $shipment->id,
            'number' => (string) ($shipment->shipment_number ?: '—'),
            'status' => (string) ($shipment->status ?: 'Unknown'),
            'creationDate' => $this->formatDate($shipment->created_at),
            'createdBy' => $shipment->creator?->name ?: '—',
            'updatedBy' => $this->shipmentLastModifiedBy($shipment),
            'lastModificationLabel' => $this->shipmentLastModificationTitle($shipment),
            'lastModificationDescription' => $this->shipmentLastModificationDescription($shipment),
            'lastModifiedField' => $this->shipmentLastModifiedField($shipment),
            'accountManager' => $shipment->accountManager?->name ?: '—',
            'flags' => $flags,
            'departure' => $shipment->partyDisplay($shipment->departure, $partyNames),
            'departurePort' => $this->formatPortDisplay($shipment->departure_port_code, $portCities),
            'service' => $shipment->service ?: '—',
            'additionalService' => $shipment->additional_service ?: '—',
            'preferredShipmentDate' => $this->formatDate($shipment->preferred_shipment_date),
            'vesselEta' => $this->formatDate($shipment->vessel_eta),
            'vesselEtd' => $this->formatDate($shipment->vessel_etd),
            'vessel' => $shipment->vessel_display ?: '—',
            'customer' => $shipment->customer_display ?: '—',
            'customerNames' => $customerNames,
            'consignee' => $shipment->partyDisplay($shipment->consignee, $partyNames),
            'consigneeAddress' => $this->normalizeText($shipment->consignee_address),
            'consigneeCity' => $this->normalizeText($shipment->consignee_city),
            'consigneeDistrict' => $this->normalizeText($shipment->consignee_district),
            'consigneeZip' => $this->normalizeText($shipment->consignee_zip),
            'consigneeCountry' => $this->normalizeText($shipment->consignee_country),
            'consigneePort' => $this->formatPortDisplay($shipment->consignee_port_code, $portCities, $shipment->consignee_city),
            'location' => $this->normalizeText($shipment->location),
            'contactPerson' => $this->normalizeText($shipment->consignee_att),
            'consigneeEmail' => $this->normalizeText($shipment->consignee_email),
            'deadlineArrival' => $this->formatDate($shipment->deadline_arrival),
            'preAlertReminder' => $this->formatDate($shipment->pre_alert_reminder),
            'customerReference' => $this->normalizeText($shipment->customer_reference),
            'notApplicableForConsolidation' => (bool) $shipment->not_applicable_for_consolidation,
            'specialConsiderations' => $this->normalizeText($shipment->special_considerations_destination),
            'commentsDepartureHub' => $this->normalizeText($shipment->comments_departure_hub),
            'commentsConsignee' => $this->normalizeText($shipment->comments_consignee),
            'skipInstructionDestination' => (bool) $shipment->skip_instruction_dest,
            'skipInstructionHub' => (bool) $shipment->skip_instruction_hub,
            'skipPrealert' => (bool) $shipment->skip_prealert,
            'projectLogistics' => (bool) $shipment->project_logistics,
            'portAgency' => (bool) $shipment->port_agency,
            'irregularities' => (int) $shipment->irregularities->count(),
            'linkedStocks' => $linkedStocks,
            'stockCount' => (int) $shipment->crrs->count(),
            'stockItems' => $stockItems,
            'totalPackages' => (int) $shipment->total_pcs,
            'totalWeight' => $this->formatDecimal($totalWeight, 2, '0.00'),
            'totalCbm' => $this->formatCbm($totalCbm, '0.00'),
            'totalValue' => $this->formatDecimal($totalValue, 2, '0.00'),
            'totalValueDisplay' => $this->formatShipmentValueDisplay($totalValue, $valueCurrencies),
            'valueCurrencies' => $valueCurrencies,
            'documents' => $documents,
            'documentCount' => count($documents),
            'stockRepackedItems' => $shipment->stock_repacked_items !== null ? (int) $shipment->stock_repacked_items : null,
            'stockRepackedWeight' => $this->formatDecimal($shipment->stock_repacked_weight, 2),
            'serviceRepackedItems' => $shipment->repacked_items !== null ? (int) $shipment->repacked_items : null,
            'serviceRepackedWeight' => $this->formatDecimal($shipment->repacked_weight, 2),
            'transportHeading' => $transportMeta['heading'],
            'transportUnitSingular' => $transportMeta['singular'],
            'transportUnitPlural' => $transportMeta['plural'],
            'transportLegs' => $transportLegs,
            'transportLegCount' => count($transportLegs),
            'updatedAt' => $this->formatDateTime($shipment->updated_at),
        ];
    }

    private function shipmentLastModifiedBy(Shipment $shipment): ?string
    {
        $lastModifier = $this->normalizeText($shipment->changeLogs->first()?->user?->name);

        if ($lastModifier !== null) {
            return $lastModifier;
        }

        $createdAt = $shipment->created_at;
        $updatedAt = $shipment->updated_at;

        if ($createdAt instanceof \DateTimeInterface && $updatedAt instanceof \DateTimeInterface) {
            if ($createdAt->format('Y-m-d H:i:s') === $updatedAt->format('Y-m-d H:i:s')) {
                return $this->normalizeText($shipment->creator?->name);
            }
        }

        return null;
    }

    private function shipmentLastModificationTitle(Shipment $shipment): ?string
    {
        return $this->normalizeText($shipment->changeLogs->first()?->title);
    }

    private function shipmentLastModificationDescription(Shipment $shipment): ?string
    {
        return $this->normalizeText($shipment->changeLogs->first()?->description);
    }

    private function shipmentLastModifiedField(Shipment $shipment): ?string
    {
        $title = $this->shipmentLastModificationTitle($shipment);

        if ($title === null || ! preg_match('/\s+edited$/i', $title)) {
            return null;
        }

        $field = preg_replace('/\s+edited$/i', '', $title);
        $field = is_string($field) ? trim($field) : '';

        return $field !== '' ? $field : null;
    }

    private function mapAssistantStock(Crr $crr): array
    {
        $crr->loadMissing(['customerVessel.customer', 'packages', 'shipments']);

        return [
            'id' => (int) $crr->id,
            'number' => (string) ($crr->stock_number ?: '—'),
            'status' => Crr::getStatusLabels()[$crr->status] ?? 'Unknown',
            'priority' => $crr->priority ?: '—',
            'vessel' => $crr->vessel_name ?: '—',
            'customer' => $crr->customerVessel?->customer?->customer_name ?: '—',
            'supplier' => $crr->supplier ?: '—',
            'hubAgent' => $crr->hub_code ?: ($crr->hub_agent ?: '—'),
            'acceptance' => $crr->accept ? 'Accepted' : 'Awaiting acceptance',
            'currency' => $crr->currency ?: null,
            'customsValue' => $crr->customs_value !== null
                ? number_format((float) $crr->customs_value, 2, '.', '')
                : null,
            'expectedDeliveryDate' => $this->formatDate($crr->expected_delivery_date),
            'linkedShipments' => $crr->shipments
                ->pluck('shipment_number')
                ->filter()
                ->unique()
                ->values()
                ->all(),
            'packageCount' => (int) $crr->packages->count(),
            'updatedAt' => $this->formatDateTime($crr->updated_at),
        ];
    }

    private function mapAssistantShipmentStockItem(Crr $crr): array
    {
        $crr->loadMissing(['packages', 'customerVessel.customer']);

        $packageCount = (int) $crr->packages->count();
        $totalWeight = (float) $crr->packages->sum('weight');
        $totalCbm = (float) $crr->packages->sum('cbm');

        return [
            'hub' => $crr->hub_code ?: ($crr->hub_agent ?: '—'),
            'vessel' => $crr->vessel_name ?: '—',
            'poNumber' => $this->formatJoinedValues($crr->po_numbers),
            'supplier' => $crr->supplier ?: '—',
            'number' => (string) ($crr->stock_number ?: '—'),
            'packages' => $packageCount,
            'weight' => $this->formatDecimal($totalWeight, 2, '0.00'),
            'cbm' => $this->formatCbm($totalCbm, '0.00'),
            'value' => $this->formatValueDisplay($crr->customs_value, $crr->currency),
            'status' => Crr::getStatusLabels()[$crr->status] ?? 'Unknown',
        ];
    }

    private function mapAssistantLookupItem(string $type, mixed $item): array
    {
        return match ($type) {
            'stock' => $this->mapAssistantStock($item),
            'shipment' => $this->mapAssistantShipment($item),
            'office' => $this->mapAssistantOffice($item),
            'hub' => $this->mapAssistantHub($item),
            'agent' => $this->mapAssistantAgent($item),
            'supplier' => $this->mapAssistantSupplier($item),
            'customer' => $this->mapAssistantCustomer($item),
            'contact' => $this->mapAssistantContact($item),
            'vessel' => $this->mapAssistantVessel($item),
            'user' => $this->mapAssistantUser($item),
            'change_log' => is_array($item) ? $item : [],
            default => [],
        };
    }

    private function mapAssistantOffice(Office $office): array
    {
        $office->loadMissing(['country', 'contacts', 'bankAccounts', 'creator', 'updater']);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('office_name', 'Office name', $office->office_name, ['office', 'name']),
                $this->assistantField('office_short_name', 'Office short name', $office->office_short_name, ['short name', 'short code']),
                $this->assistantField('status', 'Status', $this->normalizeText($office->status), ['active status']),
                $this->assistantField('phone_number', 'Phone number', $office->phone_number, ['phone', 'mobile']),
                $this->assistantField('email', 'Email', $office->email, ['mail']),
                $this->assistantField('eori_number', 'EORI number', $office->eori_number, ['eori']),
            ]),
            $this->assistantSection('Address', [
                $this->assistantField('address', 'Address', $office->address, ['office address']),
                $this->assistantField('city', 'City', $office->city),
                $this->assistantField('district_state', 'District / state', $office->district_state, ['district state', 'state']),
                $this->assistantField('zip_code', 'Zip code', $office->zip_code, ['zipcode', 'pin code', 'postal code']),
                $this->assistantField('country', 'Country', $office->country?->name ?: $this->resolveCountryName($office->country_id)),
            ]),
            $this->assistantSection('Postal address', [
                $this->assistantField('postal_address', 'Postal address', $office->postal_address, ['mailing address']),
                $this->assistantField('postal_city', 'Postal city', $office->postal_city),
                $this->assistantField('postal_district_state', 'Postal district / state', $office->postal_district_state, ['postal state']),
                $this->assistantField('postal_zip_code', 'Postal zip code', $office->postal_zip_code, ['postal zipcode', 'postal pin code']),
                $this->assistantField('postal_country', 'Postal country', $this->resolveCountryName($office->office_country_id), ['mailing country']),
            ]),
            $this->assistantSection('Invoice & settings', [
                $this->assistantField('invoicing_currency', 'Invoicing currency', $office->invoicing_currency, ['invoice currency', 'billing currency']),
                $this->assistantField('reporting_currency', 'Reporting currency', $office->reporting_currency),
                $this->assistantField('vat_rates', 'VAT rates', $office->vat_rates, ['vat rate']),
                $this->assistantField('vat_country_specific_name', 'VAT country specific name', $office->vat_country_specific_name, ['vat name']),
                $this->assistantField('vat_number', 'VAT number', $office->vat_number),
                $this->assistantField('invoicing_emails', 'Invoicing emails', $office->invoicing_emails, ['invoice email', 'billing email']),
                $this->assistantField('heading_invoice', 'Invoice heading', $office->heading_invoice, ['invoice title']),
                $this->assistantField('information_invoice', 'Invoice information', $office->information_invoice, ['invoice remarks', 'invoice note']),
                $this->assistantField('use_vat_check', 'Use VAT check', $this->assistantBooleanText($office->use_vat_check), ['vat check']),
                $this->assistantField('show_imo', 'Show IMO', $this->assistantBooleanText($office->show_imo), ['imo visibility']),
                $this->assistantField('enable_reader', 'Enable reader', $this->assistantBooleanText($office->enable_reader), ['reader']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('bank_accounts', 'Bank accounts', $this->formatOfficeBankAccountSummary($office->bankAccounts), ['bank account', 'bank accounts', 'account details', 'iban', 'swift']),
                $this->assistantField('contacts', 'Contacts', $this->formatContactsSummary($office->contacts), ['contact', 'contact list']),
            ]),
            $this->assistantAuditSection($office),
        ];

        return $this->assistantEntityPayload('office', $office->office_name ?: 'Office #' . $office->id, $sections, [
            'id' => $office->id,
            'entityLabel' => 'Office',
            'identifierLabel' => 'Office short name',
            'identifier' => $office->office_short_name,
            'status' => $this->normalizeText($office->status),
            'updatedAt' => $office->updated_at,
            'identityValues' => [$office->office_short_name, $office->email, $office->city],
            'quickFields' => ['Email', 'Phone number', 'Address', 'Country', 'Created by', 'Bank accounts'],
        ]);
    }

    private function mapAssistantHub(Hub $hub): array
    {
        $hub->loadMissing(['documents', 'pricingDocuments', 'contacts', 'creator', 'updater']);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('hub_name', 'Hub name', $hub->hub_name, ['hub', 'name']),
                $this->assistantField('code', 'Code', $hub->code, ['hub code']),
                $this->assistantField('code_description', 'Code description', $hub->code_description),
                $this->assistantField('company_id', 'Company id', $hub->company_id),
                $this->assistantField('customer_number_fm', 'Customer number FM', $hub->customer_number_fm, ['customer number']),
                $this->assistantField('status', 'Status', $hub->hide_in_portal ? 'Hidden in portal' : 'Active', ['active status']),
                $this->assistantField('phone_number', 'Phone number', $hub->phone_number, ['phone', 'mobile']),
                $this->assistantField('contact_person', 'Contact person', $hub->contact_person, ['contact']),
                $this->assistantField('email', 'Email', $hub->email, ['mail']),
                $this->assistantField('portal_email', 'Portal email', $hub->portal_email),
            ]),
            $this->assistantSection('Location', [
                $this->assistantField('hub_address', 'Hub address', $hub->hub_address, ['address']),
                $this->assistantField('city', 'City', $hub->city),
                $this->assistantField('district_state', 'District / state', $hub->district_state, ['state']),
                $this->assistantField('zip_code', 'Zip code', $hub->zip_code, ['zipcode', 'pin code', 'postal code']),
                $this->assistantField('country', 'Country', $hub->country),
                $this->assistantField('port_code', 'Port code', $hub->port_code, ['port']),
                $this->assistantField('office_address', 'Office address', $hub->office_address),
                $this->assistantField('office_city', 'Office city', $hub->office_city),
                $this->assistantField('office_district_state', 'Office district / state', $hub->office_district_state, ['office state']),
                $this->assistantField('office_zip_code', 'Office zip code', $hub->office_zip_code, ['office zipcode']),
                $this->assistantField('office_country', 'Office country', $hub->office_country),
                $this->assistantField('eori_number', 'EORI number', $hub->eori_number, ['eori']),
                $this->assistantField('un_locode', 'UN/LOCODE', $hub->un_locode, ['un locode']),
            ]),
            $this->assistantSection('Billing & agreement', [
                $this->assistantField('invoicing_name', 'Invoicing name', $hub->invoicing_name, ['invoice name']),
                $this->assistantField('invoicing_address', 'Invoicing address', $hub->invoicing_address, ['billing address']),
                $this->assistantField('invoicing_city', 'Invoicing city', $hub->invoicing_city, ['billing city']),
                $this->assistantField('invoicing_district', 'Invoicing district', $hub->invoicing_district, ['billing district']),
                $this->assistantField('invoicing_zip', 'Invoicing zip', $hub->invoicing_zip, ['billing zip']),
                $this->assistantField('billing_country', 'Billing country', $hub->billing_country),
                $this->assistantField('emails_for_invoicing', 'Emails for invoicing', $hub->emails_for_invoicing, ['invoice email', 'billing email']),
                $this->assistantField('emails_for_invoicing_cc', 'Emails for invoicing CC', $hub->emails_for_invoicing_cc, ['invoice cc']),
                $this->assistantField('vat_number', 'VAT number', $hub->vat_number),
                $this->assistantField('invoicing_frequency', 'Invoicing frequency', $hub->invoicing_frequency),
                $this->assistantField('billing_currency_outgoing', 'Billing currency outgoing', $hub->billing_currency_outgoing, ['outgoing currency']),
                $this->assistantField('payment_terms_outgoing', 'Payment terms outgoing', $hub->payment_terms_outgoing, ['outgoing payment terms']),
                $this->assistantField('billing_currency_incoming', 'Billing currency incoming', $hub->billing_currency_incoming, ['incoming currency']),
                $this->assistantField('payment_terms_incoming', 'Payment terms incoming', $hub->payment_terms_incoming, ['incoming payment terms']),
                $this->assistantField('agreement_type', 'Agreement type', $hub->agreement_type),
                $this->assistantField('rebate_percentage', 'Rebate percentage', $this->formatDecimal($hub->rebate_percentage, 2), ['rebate']),
                $this->assistantField('agreement_start_date', 'Agreement start date', $this->formatDate($hub->agreement_start_date), ['start date']),
                $this->assistantField('agreement_expiry_date', 'Agreement expiry date', $this->formatDate($hub->agreement_expiry_date), ['expiry date', 'end date']),
            ]),
            $this->assistantSection('Operations & system', [
                $this->assistantField('is_gts_company', 'GTS company', $this->assistantBooleanText($hub->is_gts_company)),
                $this->assistantField('show_pre_alert', 'Show pre alert', $this->assistantBooleanText($hub->show_pre_alert), ['pre alert']),
                $this->assistantField('hide_in_portal', 'Hide in portal', $this->assistantBooleanText($hub->hide_in_portal), ['portal visibility']),
                $this->assistantField('remarks', 'Remarks', $hub->remarks, ['remark', 'notes']),
                $this->assistantField('special_considerations', 'Special considerations', $hub->special_considerations, ['special note']),
                $this->assistantField('portal_remarks', 'Portal remarks', $hub->portal_remarks),
                $this->assistantField('export_services', 'Export services', $hub->export_services, ['export service']),
                $this->assistantField('import_services', 'Import services', $hub->import_services, ['import service']),
                $this->assistantField('export_emails', 'Export emails', $hub->export_emails, ['export email']),
                $this->assistantField('import_emails', 'Import emails', $hub->import_emails, ['import email']),
                $this->assistantField('stock_item_changed_emails', 'Stock item changed emails', $hub->stock_item_changed_emails, ['stock changed email']),
                $this->assistantField('quote_requests_emails', 'Quote requests emails', $hub->quote_requests_emails, ['quote email']),
                $this->assistantField('coc_signed', 'COC signed', $this->assistantBooleanText($hub->coc_signed)),
                $this->assistantField('sop_implemented', 'SOP implemented', $this->assistantBooleanText($hub->sop_implemented)),
                $this->assistantField('coc_signed_date', 'COC signed date', $this->formatDate($hub->coc_signed_date)),
                $this->assistantField('responsible_manager', 'Responsible manager', $hub->responsible_manager, ['manager']),
                $this->assistantField('scan_gun_login', 'Scan gun login', $hub->scan_gun_login, ['scanner login']),
                $this->assistantField('scan_gun_password', 'Scan gun password', 'Hidden for security', ['scanner password', 'password']),
                $this->assistantField('minimal_cbm', 'Minimal CBM', $this->formatCbm($hub->minimal_cbm), ['minimum cbm']),
                $this->assistantField('minimal_weight', 'Minimal weight', $this->formatDecimal($hub->minimal_weight, 2), ['minimum weight']),
                $this->assistantField('free_storage_days', 'Free storage days', $hub->free_storage_days),
                $this->assistantField('cbm_charge_usd', 'CBM charge USD', $this->formatDecimal($hub->cbm_charge_usd, 2), ['cbm charge']),
                $this->assistantField('scangun_photo_taking', 'Scangun photo taking', $this->assistantBooleanText($hub->scangun_photo_taking), ['photo taking']),
                $this->assistantField('scangun_detailed_shipment_out', 'Scangun detailed shipment out', $this->assistantBooleanText($hub->scangun_detailed_shipment_out), ['detailed shipment out']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('documents', 'SOP documents', $this->formatDocumentSummary($hub->documents, 'file_name', 'file_type', 'document_type', 'document', 'documents'), ['document', 'documents', 'sop document']),
                $this->assistantField('pricing_documents', 'Pricing documents', $this->formatDocumentSummary($hub->pricingDocuments, 'file_name', 'file_type', null, 'pricing document', 'pricing documents'), ['pricing document', 'pricing documents']),
                $this->assistantField('contacts', 'Contacts', $this->formatContactsSummary($hub->contacts), ['contact', 'contact list']),
            ]),
            $this->assistantAuditSection($hub),
        ];

        return $this->assistantEntityPayload('hub', $hub->hub_name ?: 'Hub #' . $hub->id, $sections, [
            'id' => $hub->id,
            'entityLabel' => 'Hub',
            'identifierLabel' => 'Code',
            'identifier' => $hub->code,
            'status' => $hub->hide_in_portal ? 'Hidden in portal' : 'Active',
            'updatedAt' => $hub->updated_at,
            'identityValues' => [$hub->code, $hub->email, $hub->city, $hub->port_code],
            'quickFields' => ['Code', 'Email', 'Contact person', 'Hub address', 'Created by', 'Special considerations'],
        ]);
    }

    private function mapAssistantAgent(Agent $agent): array
    {
        $agent->loadMissing(['country', 'officeCountry', 'billingCountry', 'documents', 'contacts', 'creator', 'updater']);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('agent_name', 'Agent name', $agent->agent_name, ['agent', 'name']),
                $this->assistantField('code', 'Code', $agent->code, ['agent code']),
                $this->assistantField('code_description', 'Code description', $agent->code_description),
                $this->assistantField('agent_type', 'Agent type', $agent->agent_type, ['type']),
                $this->assistantField('status', 'Status', $agent->is_active ? 'Active' : 'Inactive', ['active status']),
                $this->assistantField('phone', 'Phone', $agent->phone, ['phone number', 'mobile']),
                $this->assistantField('contact_person', 'Contact person', $agent->contact_person, ['contact']),
                $this->assistantField('email', 'Email', $agent->email, ['mail']),
            ]),
            $this->assistantSection('Location', [
                $this->assistantField('agent_address', 'Agent address', $agent->agent_address, ['address']),
                $this->assistantField('city', 'City', $agent->city),
                $this->assistantField('district_state', 'District / state', $agent->district_state, ['state']),
                $this->assistantField('zip_code', 'Zip code', $agent->zip_code, ['zipcode', 'pin code', 'postal code']),
                $this->assistantField('country', 'Country', $agent->country?->name ?: $this->resolveCountryName($agent->country_id)),
                $this->assistantField('port_code', 'Port code', $agent->port_code, ['port']),
                $this->assistantField('office_address', 'Office address', $agent->office_address),
                $this->assistantField('office_city', 'Office city', $agent->office_city),
                $this->assistantField('office_district_state', 'Office district / state', $agent->office_district_state, ['office state']),
                $this->assistantField('office_zip_code', 'Office zip code', $agent->office_zip_code, ['office zipcode']),
                $this->assistantField('office_country', 'Office country', $agent->officeCountry?->name ?: $this->resolveCountryName($agent->office_country_id)),
                $this->assistantField('eori_number', 'EORI number', $agent->eori_number, ['eori']),
                $this->assistantField('un_locode', 'UN/LOCODE', $agent->un_locode, ['un locode']),
            ]),
            $this->assistantSection('Billing', [
                $this->assistantField('invoicing_name', 'Invoicing name', $agent->invoicing_name, ['invoice name']),
                $this->assistantField('billing_address', 'Billing address', $agent->billing_address, ['invoice address']),
                $this->assistantField('billing_city', 'Billing city', $agent->billing_city),
                $this->assistantField('billing_district_state', 'Billing district / state', $agent->billing_district_state, ['billing state']),
                $this->assistantField('billing_zip_code', 'Billing zip code', $agent->billing_zip_code, ['billing zipcode']),
                $this->assistantField('billing_country', 'Billing country', $agent->billingCountry?->name ?: $this->resolveCountryName($agent->billing_country_id)),
                $this->assistantField('invoicing_emails', 'Invoicing emails', $agent->invoicing_emails, ['invoice email', 'billing email']),
                $this->assistantField('invoicing_emails_cc', 'Invoicing emails CC', $agent->invoicing_emails_cc, ['invoice cc']),
                $this->assistantField('vat_number', 'VAT number', $agent->vat_number),
                $this->assistantField('invoicing_frequency', 'Invoicing frequency', $agent->invoicing_frequency),
                $this->assistantField('applies_to_rebate', 'Applies to rebate', $this->assistantBooleanText($agent->applies_to_rebate), ['rebate']),
                $this->assistantField('rebate_percentage', 'Rebate percentage', $this->formatDecimal($agent->rebate_percentage, 2)),
                $this->assistantField('outgoing_currency', 'Outgoing currency', $agent->outgoing_currency),
                $this->assistantField('outgoing_payment_terms', 'Outgoing payment terms', $agent->outgoing_payment_terms),
                $this->assistantField('incoming_currency', 'Incoming currency', $agent->incoming_currency),
                $this->assistantField('incoming_payment_terms', 'Incoming payment terms', $agent->incoming_payment_terms),
            ]),
            $this->assistantSection('Operations & system', [
                $this->assistantField('remarks', 'Remarks', $agent->remarks, ['remark', 'notes']),
                $this->assistantField('special_considerations', 'Special considerations', $agent->special_considerations, ['special note']),
                $this->assistantField('show_pre_alert', 'Show pre alert', $this->assistantBooleanText($agent->show_pre_alert), ['pre alert']),
                $this->assistantField('coc_signed', 'COC signed', $this->assistantBooleanText($agent->coc_signed)),
                $this->assistantField('sop_implemented', 'SOP implemented', $this->assistantBooleanText($agent->sop_implemented)),
                $this->assistantField('coc_signed_date', 'COC signed date', $this->formatDate($agent->coc_signed_date)),
                $this->assistantField('responsible_manager', 'Responsible manager', $agent->responsible_manager, ['manager']),
                $this->assistantField('calculate_sell_rates', 'Calculate sell rates', $this->assistantBooleanText($agent->calculate_sell_rates)),
                $this->assistantField('purchase_rate', 'Purchase rate', $this->formatDecimal($agent->purchase_rate, 2)),
                $this->assistantField('sell_rate', 'Sell rate', $this->formatDecimal($agent->sell_rate, 2)),
                $this->assistantField('profit', 'Profit', $this->formatDecimal($agent->profit, 2)),
                $this->assistantField('export_email_services', 'Export email services', $agent->export_email_services, ['export emails']),
                $this->assistantField('import_email_services', 'Import email services', $agent->import_email_services, ['import emails']),
                $this->assistantField('status_changed_emails', 'Status changed emails', $agent->status_changed_emails, ['status email']),
                $this->assistantField('stock_item_changed_emails', 'Stock item changed emails', $agent->stock_item_changed_emails, ['stock changed email']),
                $this->assistantField('quote_requests_emails', 'Quote requests emails', $agent->quote_requests_emails, ['quote email']),
                $this->assistantField('scangun_login', 'Scangun login', $agent->scangun_login, ['scanner login']),
                $this->assistantField('scangun_password', 'Scangun password', 'Hidden for security', ['scanner password', 'password']),
                $this->assistantField('scangun_enable_picture', 'Scangun enable picture', $this->assistantBooleanText($agent->scangun_enable_picture), ['picture']),
                $this->assistantField('scangun_enable_detailed_shipment', 'Scangun enable detailed shipment', $this->assistantBooleanText($agent->scangun_enable_detailed_shipment), ['detailed shipment']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('documents', 'Documents', $this->formatDocumentSummary($agent->documents, 'filename', null, 'section', 'document', 'documents'), ['document', 'documents']),
                $this->assistantField('contacts', 'Contacts', $this->formatContactsSummary($agent->contacts), ['contact', 'contact list']),
            ]),
            $this->assistantAuditSection($agent),
        ];

        return $this->assistantEntityPayload('agent', $agent->agent_name ?: 'Agent #' . $agent->id, $sections, [
            'id' => $agent->id,
            'entityLabel' => 'Agent',
            'identifierLabel' => 'Code',
            'identifier' => $agent->code,
            'status' => $agent->is_active ? 'Active' : 'Inactive',
            'updatedAt' => $agent->updated_at,
            'identityValues' => [$agent->code, $agent->email, $agent->city, $agent->port_code],
            'quickFields' => ['Code', 'Email', 'Contact person', 'Agent address', 'Created by', 'Documents'],
        ]);
    }

    private function mapAssistantSupplier(Supplier $supplier): array
    {
        $supplier->loadMissing(['country', 'officeCountry', 'contacts', 'creator', 'updater']);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('supplier_name', 'Supplier name', $supplier->supplier_name, ['supplier', 'name']),
                $this->assistantField('phone_number', 'Phone number', $supplier->phone_number, ['phone', 'mobile']),
                $this->assistantField('contact_person', 'Contact person', $supplier->contact_person, ['contact']),
                $this->assistantField('email', 'Email', $supplier->email, ['mail']),
                $this->assistantField('currency', 'Currency', $supplier->currency),
                $this->assistantField('remarks', 'Remarks', $supplier->remarks, ['remark', 'notes']),
                $this->assistantField('special_considerations', 'Special considerations', $supplier->special_considerations, ['special note']),
            ]),
            $this->assistantSection('Location', [
                $this->assistantField('supplier_address', 'Supplier address', $supplier->supplier_address, ['address']),
                $this->assistantField('city', 'City', $supplier->city),
                $this->assistantField('district_state', 'District / state', $supplier->district_state, ['state']),
                $this->assistantField('zip_code', 'Zip code', $supplier->zip_code, ['zipcode', 'pin code', 'postal code']),
                $this->assistantField('country', 'Country', $supplier->country?->name ?: $this->resolveCountryName($supplier->country_id)),
                $this->assistantField('port_code', 'Port code', $supplier->port_code, ['port']),
                $this->assistantField('office_address', 'Office address', $supplier->office_address),
                $this->assistantField('office_city', 'Office city', $supplier->office_city),
                $this->assistantField('office_district_state', 'Office district / state', $supplier->office_district_state, ['office state']),
                $this->assistantField('office_zip_code', 'Office zip code', $supplier->office_zip_code, ['office zipcode']),
                $this->assistantField('office_country', 'Office country', $supplier->officeCountry?->name ?: $this->resolveCountryName($supplier->office_country_id)),
                $this->assistantField('vat_number', 'VAT number', $supplier->vat_number),
                $this->assistantField('eori_number', 'EORI number', $supplier->eori_number, ['eori']),
                $this->assistantField('un_locode', 'UN/LOCODE', $supplier->un_locode, ['un locode']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('contacts', 'Contacts', $this->formatContactsSummary($supplier->contacts), ['contact', 'contact list']),
            ]),
            $this->assistantAuditSection($supplier),
        ];

        return $this->assistantEntityPayload('supplier', $supplier->supplier_name ?: 'Supplier #' . $supplier->id, $sections, [
            'id' => $supplier->id,
            'entityLabel' => 'Supplier',
            'identifierLabel' => 'Email',
            'identifier' => $supplier->email,
            'updatedAt' => $supplier->updated_at,
            'identityValues' => [$supplier->email, $supplier->city, $supplier->port_code],
            'quickFields' => ['Email', 'Phone number', 'Contact person', 'Supplier address', 'Created by', 'Currency'],
        ]);
    }

    private function mapAssistantCustomer(Customer $customer): array
    {
        $customer->loadMissing([
            'primaryAddress.country',
            'postalAddress.country',
            'invoiceAddress.country',
            'invoiceDetail',
            'responsible.salesManager.office',
            'responsible.accountManager.office',
            'responsible.accountingUser.office',
            'sop',
            'notificationSetting',
            'documents',
            'vessels',
            'contacts',
            'creator',
            'updater',
        ]);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('customer_name', 'Customer name', $customer->customer_name, ['customer', 'name']),
                $this->assistantField('customer_number', 'Customer number', $customer->customer_number, ['customer code', 'number']),
                $this->assistantField('customer_group_id', 'Customer group', $customer->customer_group_id, ['group']),
                $this->assistantField('phone', 'Phone', $customer->phone, ['phone number', 'mobile']),
                $this->assistantField('contact_person', 'Contact person', $customer->contact_person, ['contact']),
                $this->assistantField('email', 'Email', $customer->email, ['mail']),
                $this->assistantField('internal_shipment', 'Internal shipment', $customer->internal_shipment),
                $this->assistantField('remarks', 'Remarks', $customer->remarks, ['remark', 'notes']),
                $this->assistantField('special_considerations', 'Special considerations', $customer->special_considerations, ['special note']),
                $this->assistantField('un_locode', 'UN/LOCODE', $customer->un_locode, ['un locode']),
                $this->assistantField('show_transport_details', 'Show transport details', $this->assistantBooleanText($customer->show_transport_details), ['transport details']),
                $this->assistantField('esea_store_stock_only', 'ESEA store stock only', $this->assistantBooleanText($customer->esea_store_stock_only), ['stock only']),
            ]),
            $this->assistantSection('Primary address', [
                $this->assistantField('primary_address', 'Primary address', $this->formatCustomerAddress($customer->primaryAddress), ['address']),
                $this->assistantField('primary_city', 'Primary city', $customer->primaryAddress?->city),
                $this->assistantField('primary_state', 'Primary state', $customer->primaryAddress?->state, ['primary district', 'primary district state']),
                $this->assistantField('primary_zip_code', 'Primary zip code', $customer->primaryAddress?->zip_code, ['primary zipcode', 'primary pin code']),
                $this->assistantField('primary_country', 'Primary country', $customer->primaryAddress?->country?->name ?: $this->resolveCountryName($customer->primaryAddress?->country_id), ['country']),
                $this->assistantField('primary_port_code', 'Primary port code', $customer->primaryAddress?->port_code, ['port code', 'port']),
            ]),
            $this->assistantSection('Postal address', [
                $this->assistantField('postal_address', 'Postal address', $this->formatCustomerAddress($customer->postalAddress), ['mailing address']),
                $this->assistantField('postal_city', 'Postal city', $customer->postalAddress?->city),
                $this->assistantField('postal_state', 'Postal state', $customer->postalAddress?->state),
                $this->assistantField('postal_zip_code', 'Postal zip code', $customer->postalAddress?->zip_code, ['postal zipcode']),
                $this->assistantField('postal_country', 'Postal country', $customer->postalAddress?->country?->name ?: $this->resolveCountryName($customer->postalAddress?->country_id)),
            ]),
            $this->assistantSection('Invoice address & billing', [
                $this->assistantField('invoice_address', 'Invoice address', $this->formatCustomerAddress($customer->invoiceAddress), ['billing address']),
                $this->assistantField('invoice_city', 'Invoice city', $customer->invoiceAddress?->city, ['billing city']),
                $this->assistantField('invoice_state', 'Invoice state', $customer->invoiceAddress?->state, ['invoice district']),
                $this->assistantField('invoice_zip_code', 'Invoice zip code', $customer->invoiceAddress?->zip_code, ['invoice zipcode']),
                $this->assistantField('invoice_country', 'Invoice country', $customer->invoiceAddress?->country?->name ?: $this->resolveCountryName($customer->invoiceAddress?->country_id), ['billing country']),
                $this->assistantField('invoice_recipient_name', 'Invoice recipient name', $customer->invoiceDetail?->invoice_recipient_name, ['recipient name']),
                $this->assistantField('invoice_email', 'Invoice email', $customer->invoiceDetail?->invoice_email),
                $this->assistantField('invoice_email_cc', 'Invoice email CC', $customer->invoiceDetail?->invoice_email_cc, ['invoice cc']),
                $this->assistantField('currency_code', 'Currency code', $customer->invoiceDetail?->currency_code, ['currency']),
                $this->assistantField('payment_terms_days', 'Payment terms days', $customer->invoiceDetail?->payment_terms_days, ['payment terms']),
                $this->assistantField('invoice_frequency', 'Invoice frequency', $customer->invoiceDetail?->invoice_frequency),
                $this->assistantField('invoice_remarks', 'Invoice remarks', $customer->invoiceDetail?->invoice_remarks, ['invoice notes']),
                $this->assistantField('vat_number', 'VAT number', $customer->invoiceDetail?->vat_number),
                $this->assistantField('eori_number', 'EORI number', $customer->invoiceDetail?->eori_number, ['eori']),
            ]),
            $this->assistantSection('Responsible team', [
                $this->assistantField('sales_manager', 'Sales manager', $this->formatResponsibleContact($customer->responsible?->salesManager), ['sales']),
                $this->assistantField('account_manager', 'Account manager', $this->formatResponsibleContact($customer->responsible?->accountManager), ['main account manager']),
                $this->assistantField('accounting_user', 'Accounting user', $this->formatResponsibleContact($customer->responsible?->accountingUser), ['accounts user']),
            ]),
            $this->assistantSection('SOP & notifications', [
                $this->assistantField('send_stocklist', 'Send stocklist', $customer->sop?->send_stocklist, ['stocklist']),
                $this->assistantField('onboard_delivery', 'Onboard delivery', $customer->sop?->onboard_delivery, ['on board delivery']),
                $this->assistantField('quotes_prior_to_instructions', 'Quotes prior to instructions', $customer->sop?->quotes_prior_to_instructions, ['quotes']),
                $this->assistantField('agreed_rate', 'Agreed rate', $customer->sop?->agreed_rate),
                $this->assistantField('invoicing_procedure', 'Invoicing procedure', $customer->sop?->invoicing_procedure, ['invoice procedure']),
                $this->assistantField('pending_entry', 'Pending entry', $customer->sop?->pending_entry),
                $this->assistantField('special_pending_routines', 'Special pending routines', $customer->sop?->special_pending_routines, ['pending routines']),
                $this->assistantField('other_procedures_comments', 'Other procedures comments', $customer->sop?->other_procedures_comments, ['other procedures']),
                $this->assistantField('notify_stock_items', 'Notify stock items', $customer->notificationSetting?->notify_stock_items, ['stock notification']),
                $this->assistantField('send_automatic_first_mile_email', 'Send automatic first mile email', $this->assistantBooleanText($customer->notificationSetting?->send_automatic_first_mile_email), ['first mile email']),
                $this->assistantField('notify_first_mile_email_sent', 'Notify first mile email sent', $customer->notificationSetting?->notify_first_mile_email_sent, ['first mile sent']),
                $this->assistantField('shipping_free_storage_days', 'Shipping free storage days', $customer->notificationSetting?->shipping_free_storage_days, ['free storage days']),
                $this->assistantField('shipping_free_storage_weight', 'Shipping free storage weight', $this->formatDecimal($customer->notificationSetting?->shipping_free_storage_weight, 2), ['free storage weight']),
                $this->assistantField('shipping_free_storage_volume', 'Shipping free storage volume', $this->formatDecimal($customer->notificationSetting?->shipping_free_storage_volume, 2), ['free storage volume']),
                $this->assistantField('notify_free_storage_exceeded', 'Notify free storage exceeded', $customer->notificationSetting?->notify_free_storage_exceeded, ['free storage exceeded']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('total_vessels', 'Total vessels', $customer->vessels->count(), ['vessel count', 'total vessel count', 'number of vessels', 'how many vessels', 'kitne vessels', 'kitni vessels']),
                $this->assistantField('vessel_names', 'Vessel names', $customer->vessels->pluck('vessel')->filter()->values()->all(), ['vessel name list', 'vessel names', 'which vessels', 'kaun se vessels', 'kaunse vessels', 'vessels name']),
                $this->assistantField('vessels', 'Vessels', $this->formatVesselSummary($customer->vessels), ['vessel', 'vessels', 'vessel list']),
                $this->assistantField('contacts', 'Contacts', $this->formatContactsSummary($customer->contacts), ['contact', 'contact list']),
                $this->assistantField('documents', 'Documents', $this->formatDocumentSummary($customer->documents, 'file_name', 'file_type', null, 'document', 'documents'), ['document', 'documents']),
            ]),
            $this->assistantAuditSection($customer),
        ];

        return $this->assistantEntityPayload('customer', $customer->customer_name ?: 'Customer #' . $customer->id, $sections, [
            'id' => $customer->id,
            'entityLabel' => 'Customer',
            'identifierLabel' => 'Customer number',
            'identifier' => $customer->customer_number,
            'updatedAt' => $customer->updated_at,
            'identityValues' => [$customer->customer_number, $customer->email, $customer->phone, $customer->contact_person],
            'quickFields' => ['Email', 'Phone', 'Primary address', 'Invoice email', 'Created by', 'Vessels'],
        ]);
    }

    private function mapAssistantContact(Contact $contact): array
    {
        $contact->loadMissing(['office', 'customer', 'hub', 'supplier', 'otherCompany', 'agent', 'creator', 'updater']);

        $linkedVessels = CustomerVessel::query()
            ->with('customer')
            ->where('contact_id', $contact->id)
            ->orderBy('vessel')
            ->get();
        $parentType = $this->contactParentType($contact);
        $parentName = $this->contactParentName($contact);
        $parentLabel = $this->contactParentLabel($contact);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('name', 'Name', $contact->name, ['contact name']),
                $this->assistantField('email', 'Email', $contact->email, ['mail']),
                $this->assistantField('phone_number', 'Phone number', $contact->phone_number, ['phone', 'mobile']),
                $this->assistantField('description', 'Description', $contact->description, ['role', 'designation']),
                $this->assistantField('status', 'Status', $contact->status, ['active status']),
                $this->assistantField('category', 'Category', $contact->category),
            ]),
            $this->assistantSection('Link & settings', [
                $this->assistantField('linked_record_type', 'Linked record type', $parentLabel, ['belongs to type', 'parent type']),
                $this->assistantField('linked_record_name', 'Linked record name', $parentName, ['belongs to', 'parent name', 'linked record']),
                $this->assistantField('is_main_contact', 'Main contact', $this->assistantBooleanText($contact->is_main_contact), ['primary contact']),
                $this->assistantField('reply_to_email', 'Reply to email', $contact->reply_to_email, ['reply email']),
                $this->assistantField('is_cc_enabled', 'CC enabled', $this->assistantBooleanText($contact->is_cc_enabled), ['cc']),
            ]),
            $this->assistantSection('Related details', [
                $this->assistantField('linked_vessels', 'Linked vessels', $this->formatVesselSummary($linkedVessels), ['vessel', 'vessels', 'vessel list']),
            ]),
            $this->assistantAuditSection($contact),
        ];

        return $this->assistantEntityPayload('contact', $contact->name ?: 'Contact #' . $contact->id, $sections, [
            'id' => $contact->id,
            'entityLabel' => 'Contact',
            'identifierLabel' => 'Email',
            'identifier' => $contact->email ?: $contact->phone_number,
            'status' => $this->normalizeText($contact->status),
            'updatedAt' => $contact->updated_at,
            'identityValues' => array_filter([
                $contact->email,
                $contact->phone_number,
                $contact->description,
                $parentType,
                $parentName,
                ...$linkedVessels->pluck('vessel')->all(),
            ]),
            'quickFields' => ['Email', 'Phone number', 'Linked record type', 'Linked record name', 'Created by', 'Linked vessels'],
        ]);
    }

    private function mapAssistantVessel(CustomerVessel $vessel): array
    {
        $vessel->loadMissing(['customer', 'contact', 'creator', 'updater']);

        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('customer', 'Customer', $vessel->customer?->customer_name, ['customer name']),
                $this->assistantField('vessel', 'Vessel name', $vessel->vessel, ['vessel', 'name']),
                $this->assistantField('vessel_name_alias', 'Vessel alias', $vessel->vessel_name_alias, ['alias']),
                $this->assistantField('vessel_imo', 'Vessel IMO', $vessel->vessel_imo, ['imo']),
                $this->assistantField('customer_vessel_code', 'Customer vessel code', $vessel->customer_vessel_code, ['vessel code']),
                $this->assistantField('vessel_type_alias', 'Vessel type alias', $vessel->vessel_type_alias, ['vessel type']),
                $this->assistantField('po_example', 'PO example', $vessel->po_example, ['purchase order example']),
                $this->assistantField('status', 'Status', $vessel->inactive_vessel ? 'Inactive' : 'Active', ['active status']),
            ]),
            $this->assistantSection('Ship profile & ports', [
                $this->assistantField('shipyard', 'Shipyard', $vessel->shipyard),
                $this->assistantField('shipyard_location', 'Shipyard location', $vessel->shipyard_location),
                $this->assistantField('manager', 'Manager', $vessel->manager),
                $this->assistantField('account_manager', 'Account manager', $vessel->account_manager),
                $this->assistantField('receivers_stocklists', 'Receivers stocklists', $vessel->receivers_stocklists, ['stocklists']),
                $this->assistantField('home_consolidation_port', 'Home consolidation port', $vessel->home_consolidation_port, ['consolidation port']),
                $this->assistantField('home_delivery_port', 'Home delivery port', $vessel->home_delivery_port, ['delivery port']),
            ]),
            $this->assistantSection('Flags & preferences', [
                $this->assistantField('not_in_transit', 'Not in transit', $this->assistantBooleanText($vessel->not_in_transit), ['transit']),
                $this->assistantField('inactive_vessel', 'Inactive vessel', $this->assistantBooleanText($vessel->inactive_vessel), ['inactive']),
                $this->assistantField('sanction_blocked', 'Sanction blocked', $this->assistantBooleanText($vessel->sanction_blocked), ['sanction']),
                $this->assistantField('financially_blocked', 'Financially blocked', $this->assistantBooleanText($vessel->financially_blocked), ['financial block']),
                $this->assistantField('pre_payment_only', 'Pre payment only', $this->assistantBooleanText($vessel->pre_payment_only), ['prepayment']),
                $this->assistantField('internal_shipment', 'Internal shipment', $vessel->internal_shipment),
                $this->assistantField('except_from_hubs', 'Except from hubs', $vessel->except_from_hubs, ['excluded hubs']),
                $this->assistantField('invoice_vessel_separately', 'Invoice vessel separately', $this->assistantBooleanText($vessel->invoice_vessel_separately), ['separate invoice']),
                $this->assistantField('yearly_customer_reference', 'Yearly customer reference', $vessel->yearly_customer_reference, ['yearly reference']),
                $this->assistantField('remarks', 'Remarks', $vessel->remarks, ['remark', 'notes']),
            ]),
            $this->assistantSection('Contact & updates', [
                $this->assistantField('main_contact', 'Main contact', $this->formatContactIdentity($vessel->contact), ['contact person', 'contact']),
                $this->assistantField('contact_stocklists', 'Contact stocklists', $this->assistantBooleanText($vessel->contact_stocklists), ['stocklist contact']),
                $this->assistantField('contact_pre_alerts', 'Contact pre alerts', $this->assistantBooleanText($vessel->contact_pre_alerts), ['pre alert contact']),
                $this->assistantField('contact_stock_notifications', 'Contact stock notifications', $this->assistantBooleanText($vessel->contact_stock_notifications), ['stock notifications']),
                $this->assistantField('contact_free_storage_notifications', 'Contact free storage notifications', $this->assistantBooleanText($vessel->contact_free_storage_notifications), ['free storage notifications']),
                $this->assistantField('contact_offers', 'Contact offers', $this->assistantBooleanText($vessel->contact_offers), ['offers']),
            ]),
            $this->assistantAuditSection($vessel),
        ];

        return $this->assistantEntityPayload('vessel', $vessel->vessel ?: 'Vessel #' . $vessel->id, $sections, [
            'id' => $vessel->id,
            'entityLabel' => 'Vessel',
            'identifierLabel' => 'Vessel IMO',
            'identifier' => $vessel->vessel_imo ?: $vessel->customer_vessel_code,
            'status' => $vessel->inactive_vessel ? 'Inactive' : 'Active',
            'updatedAt' => $vessel->updated_at,
            'identityValues' => [$vessel->vessel_name_alias, $vessel->vessel_imo, $vessel->customer_vessel_code, $vessel->customer?->customer_name],
            'quickFields' => ['Vessel IMO', 'Customer', 'Manager', 'Home delivery port', 'Created by', 'Main contact'],
        ]);
    }

    private function mapAssistantUser(User $user): array
    {
        $user->loadMissing(['offices', 'hubs', 'agents', 'suppliers']);

        $username = filled($user->email) ? Str::before((string) $user->email, '@') : null;
        $isOtpBlocked = $user->otp_blocked_until?->isFuture() ?? false;
        $sections = [
            $this->assistantSection('Basic details', [
                $this->assistantField('name', 'Name', $user->name, ['user name', 'full name']),
                $this->assistantField('username', 'Username', $username, ['login', 'login name']),
                $this->assistantField('email', 'Email', $user->email, ['mail']),
                $this->assistantField('phone_number', 'Phone number', $user->phone_number, ['phone', 'mobile']),
                $this->assistantField('role', 'Role', $user->role, ['user role', 'access role', 'user type', 'type']),
                $this->assistantField('status', 'Status', $user->is_active ? 'Active' : 'Inactive', ['active status']),
                $this->assistantField('otp_blocked', 'OTP blocked', $this->assistantBooleanText($isOtpBlocked), ['otp status', 'otp lock', 'blocked']),
                $this->assistantField('otp_blocked_until', 'OTP blocked until', $isOtpBlocked ? $this->formatDateTime($user->otp_blocked_until) : null, ['blocked until', 'otp blocked until', 'otp lock until']),
            ]),
            $this->assistantSection('Assignments', [
                $this->assistantField(
                    'assigned_offices',
                    'Assigned offices',
                    $this->formatCollectionSummary(
                        $user->offices
                            ->sortBy('office_name')
                            ->map(fn (Office $office) => $this->implodeMeaningfulValues([$office->office_name, $office->office_short_name], ' · '))
                            ->all(),
                        'office',
                        'offices',
                        'No assigned offices'
                    ),
                    ['office access', 'assigned office', 'office assignments']
                ),
                $this->assistantField(
                    'assigned_hubs',
                    'Assigned hubs',
                    $this->formatCollectionSummary(
                        $user->hubs
                            ->sortBy('hub_name')
                            ->map(fn (Hub $hub) => $this->implodeMeaningfulValues([$hub->hub_name, $hub->code], ' · '))
                            ->all(),
                        'hub',
                        'hubs',
                        'No assigned hubs'
                    ),
                    ['hub access', 'assigned hub', 'hub assignments']
                ),
                $this->assistantField(
                    'assigned_agents',
                    'Assigned agents',
                    $this->formatCollectionSummary(
                        $user->agents
                            ->sortBy('agent_name')
                            ->map(fn (Agent $agent) => $this->implodeMeaningfulValues([$agent->agent_name, $agent->code], ' · '))
                            ->all(),
                        'agent',
                        'agents',
                        'No assigned agents'
                    ),
                    ['agent access', 'assigned agent', 'agent assignments']
                ),
                $this->assistantField(
                    'assigned_suppliers',
                    'Assigned suppliers',
                    $this->formatCollectionSummary(
                        $user->suppliers
                            ->sortBy('supplier_name')
                            ->map(fn (Supplier $supplier) => $this->normalizeText($supplier->supplier_name))
                            ->all(),
                        'supplier',
                        'suppliers',
                        'No assigned suppliers'
                    ),
                    ['supplier access', 'assigned supplier', 'supplier assignments']
                ),
            ]),
            $this->assistantSection('Record activity', [
                $this->assistantField('created_at', 'Creation date', $this->formatDateTime($user->created_at), ['created date', 'created at', 'added on', 'kab add kiya', 'kab create hua']),
                $this->assistantField('updated_at', 'Last update', $this->formatDateTime($user->updated_at), ['updated at', 'last updated', 'last modified']),
            ]),
        ];

        return $this->assistantEntityPayload('user', $user->name ?: 'User #' . $user->id, $sections, [
            'id' => $user->id,
            'entityLabel' => 'User',
            'identifierLabel' => 'Email',
            'identifier' => $user->email,
            'status' => $user->is_active ? 'Active' : 'Inactive',
            'updatedAt' => $user->updated_at,
            'identityValues' => array_filter([
                $user->email,
                $username,
                $user->phone_number,
                $user->role,
                ...$user->offices->pluck('office_name')->all(),
                ...$user->offices->pluck('office_short_name')->all(),
                ...$user->hubs->pluck('hub_name')->all(),
                ...$user->hubs->pluck('code')->all(),
                ...$user->agents->pluck('agent_name')->all(),
                ...$user->agents->pluck('code')->all(),
                ...$user->suppliers->pluck('supplier_name')->all(),
            ]),
            'quickFields' => ['Role', 'Email', 'Phone number', 'Assigned offices', 'Assigned hubs', 'Last update'],
        ]);
    }

    private function assistantChangeLogQuery(): Builder
    {
        return AdministrationChangeLog::query()
            ->with([
                'user',
                'loggable' => function (MorphTo $morphTo): void {
                    $morphTo->morphWith([
                        Contact::class => ['office', 'customer', 'hub', 'agent', 'supplier', 'otherCompany'],
                        CustomerVessel::class => ['customer'],
                        HubUser::class => ['hub'],
                        AgentUser::class => ['agent'],
                    ]);
                },
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    private function findAssistantChangeLogRecord(string $query, ?int &$score = null): ?array
    {
        if (! $this->assistantHasAdministrationChangeLogIntent($query)) {
            return null;
        }

        $window = $this->assistantChangeLogWindow($query);
        $logQuery = $this->assistantChangeLogQuery();

        if ($window !== null) {
            $logQuery
                ->where('created_at', '>=', $window['from'])
                ->where('created_at', '<=', $window['to']);
        }

        $logs = $logQuery->get();
        $phrases = collect($this->assistantAdministrationLookupPhrases($query))
            ->map(fn ($phrase) => $this->normalizeSearchText($phrase))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $tokens = $this->assistantAdministrationLookupTokens($query);
        $normalizedQuery = $this->normalizeSearchText($query);
        $ranked = $logs->map(function (AdministrationChangeLog $log) use ($phrases, $tokens, $normalizedQuery): array {
            $entry = $this->mapAssistantChangeLogEntry($log);

            return [
                'entry' => $entry,
                'log' => $log,
                'score' => $this->assistantChangeLogScore($entry, $phrases, $tokens, $normalizedQuery),
            ];
        });
        $matched = $ranked
            ->filter(fn (array $candidate) => $candidate['score'] >= 130)
            ->sort(function (array $left, array $right): int {
                if ($left['score'] !== $right['score']) {
                    return $right['score'] <=> $left['score'];
                }

                $leftTimestamp = optional($left['log']->created_at)->timestamp ?? 0;
                $rightTimestamp = optional($right['log']->created_at)->timestamp ?? 0;

                if ($leftTimestamp !== $rightTimestamp) {
                    return $rightTimestamp <=> $leftTimestamp;
                }

                return ((int) ($right['entry']['id'] ?? 0)) <=> ((int) ($left['entry']['id'] ?? 0));
            })
            ->values();

        if ($matched->isNotEmpty()) {
            $score = (int) $matched->first()['score'];
            $matched = $matched
                ->filter(fn (array $candidate) => $candidate['score'] >= max(130, $score - 120))
                ->values();

            return $this->buildAssistantChangeLogSummary(
                $matched->pluck('log')->take(8)->values(),
                $query,
                $window,
                $score,
                $matched->count()
            );
        }

        $fallbackLogs = $logs->take(8)->values();
        $score = $fallbackLogs->isNotEmpty() ? 180 : 120;

        return $this->buildAssistantChangeLogSummary($fallbackLogs, $query, $window, $score, $logs->count());
    }

    private function buildAssistantChangeLogSummary(
        Collection $logs,
        string $query,
        ?array $window = null,
        int $score = 0,
        ?int $totalMatches = null
    ): array
    {
        $entries = $logs
            ->values()
            ->map(fn (AdministrationChangeLog $log) => $this->mapAssistantChangeLogEntry($log))
            ->values();
        $totalMatches ??= $entries->count();
        $latest = $entries->first();
        $earliest = $entries->last();
        $uniqueEntities = $entries->pluck('entityLabel')->filter()->unique()->values()->all();
        $uniqueRecords = $entries->pluck('recordName')->filter()->unique()->values()->all();
        $uniqueUsers = $entries->pluck('userName')->filter()->unique()->values()->all();
        $uniqueFields = $entries->pluck('field')->filter()->unique()->values()->all();
        $subjectName = $this->assistantChangeLogSubjectName($entries, $window);
        $contextLabel = $this->assistantChangeLogContextLabel($entries, $window, $query);
        $dateRange = $latest !== null && $earliest !== null
            ? $earliest['date'] . ' to ' . $latest['date']
            : ($window['human'] ?? null);
        $sections = [
            $this->assistantSection('Summary', [
                $this->assistantField('total_matching_logs', 'Total matching logs', $totalMatches, ['total logs', 'change log count', 'count', 'kitne change logs', 'how many change logs']),
                $this->assistantField('latest_change', 'Latest change', $latest['title'] ?? null, ['last change', 'recent change', 'what changed', 'kya change hua']),
                $this->assistantField('latest_record', 'Latest record', $latest['recordName'] ?? null, ['record', 'record name', 'which record']),
                $this->assistantField('latest_entity', 'Latest entity', $latest['entityLabel'] ?? null, ['entity', 'entity type', 'module']),
                $this->assistantField('latest_changed_field', 'Latest changed field', $latest['field'] ?? null, ['field', 'which field', 'last modification field', 'last changed field', 'kaunsi field', 'kis field']),
                $this->assistantField('latest_changed_by', 'Latest changed by', $latest['userName'] ?? null, ['changed by', 'who changed', 'kisne change kiya', 'kisne modify kiya', 'last modified by']),
                $this->assistantField('latest_change_date', 'Latest change date', $latest['date'] ?? null, ['change date', 'last change date', 'when changed', 'kab change hua']),
                $this->assistantField('latest_change_description', 'Latest change description', $latest['description'] ?? null, ['description', 'change description', 'what changed in detail']),
                $this->assistantField('date_range', 'Date range', $dateRange, ['range', 'period']),
                $this->assistantField('entities', 'Entities', $uniqueEntities, ['entity names', 'entity list']),
                $this->assistantField('records', 'Records', $uniqueRecords, ['record names', 'record list']),
                $this->assistantField('changed_by_users', 'Changed by users', $uniqueUsers, ['users', 'changed by users']),
                $this->assistantField('fields_changed', 'Fields changed', $uniqueFields, ['fields', 'fields changed']),
            ]),
            $this->assistantSection('Recent changes', $entries->take(5)->values()->map(function (array $entry, int $index): array {
                return $this->assistantField(
                    'change_' . ($index + 1),
                    'Change ' . ($index + 1),
                    $this->implodeMeaningfulValues([
                        $entry['date'] ?? null,
                        $entry['recordName'] ?? null,
                        $entry['title'] ?? null,
                        $entry['userName'] ?? null,
                        $entry['description'] ?? null,
                    ], ' · '),
                    ['recent change', 'recent changes', 'change history']
                );
            })->all()),
        ];

        $payload = $this->assistantEntityPayload('change_log', $subjectName, $sections, [
            'id' => (int) ($latest['id'] ?? 0),
            'entityLabel' => 'Administration change logs',
            'identifierLabel' => 'Search context',
            'identifier' => $contextLabel,
            'updatedAt' => $latest['createdAt'] ?? null,
            'identityValues' => array_values(array_filter(array_merge(
                [$subjectName, $contextLabel, $window['label'] ?? null],
                $uniqueEntities,
                $uniqueRecords,
                $uniqueUsers,
                $uniqueFields
            ))),
            'quickFields' => ['Total matching logs', 'Latest change', 'Latest changed field', 'Latest changed by', 'Latest change date', 'Records'],
        ]);
        $payload['matchedCount'] = $totalMatches;
        $payload['logs'] = $entries->all();
        $payload['windowLabel'] = $window['human'] ?? null;
        $payload['score'] = $score;

        return $payload;
    }

    private function mapAssistantChangeLogEntry(AdministrationChangeLog $log): array
    {
        return [
            'id' => (int) $log->id,
            'date' => $this->formatDateTime($log->created_at),
            'createdAt' => $log->created_at,
            'entityLabel' => $this->assistantChangeLogEntityLabel($log),
            'recordName' => $this->assistantChangeLogRecordName($log),
            'title' => $this->assistantChangeLogTitle($log),
            'description' => $this->normalizeText($log->description),
            'userName' => $this->normalizeText($log->user?->name) ?: 'System',
            'field' => $this->assistantChangeLogFieldLabel($log),
        ];
    }

    private function assistantChangeLogScore(array $entry, array $phrases, array $tokens, string $query = ''): int
    {
        $values = collect([
            $entry['entityLabel'] ?? null,
            $entry['recordName'] ?? null,
            $entry['title'] ?? null,
            $entry['description'] ?? null,
            $entry['userName'] ?? null,
            $entry['field'] ?? null,
        ])
            ->flatten()
            ->map(fn ($value) => $this->normalizeSearchText($value))
            ->filter()
            ->unique()
            ->values();

        if ($values->isEmpty()) {
            return 0;
        }

        $recordTokens = $values
            ->flatMap(fn ($value) => $this->searchWordTokens($value))
            ->unique()
            ->values()
            ->all();
        $score = 0;

        foreach ($phrases as $phrase) {
            $phraseTokens = $this->searchWordTokens($phrase);

            foreach ($values as $value) {
                if ($value === $phrase) {
                    $score = max($score, 520 + mb_strlen($phrase));
                    continue;
                }

                if (str_starts_with($value, $phrase)) {
                    $score = max($score, 460 + mb_strlen($phrase));
                    continue;
                }

                if (count($phraseTokens) > 1 && str_contains($value, $phrase)) {
                    $score = max($score, 400 + mb_strlen($phrase));
                    continue;
                }

                if (count($phraseTokens) === 1 && in_array($phraseTokens[0], $this->searchWordTokens($value), true)) {
                    $score = max($score, 390 + mb_strlen($phrase));
                }
            }
        }

        if ($score === 0 && $tokens !== []) {
            $matchedTokens = 0;

            foreach ($tokens as $token) {
                if (in_array($token, $recordTokens, true)) {
                    $matchedTokens += 1;
                    $score += 32 + min(12, mb_strlen($token));
                }
            }

            if ($matchedTokens >= 2) {
                $score += 90;
            }
        }

        $field = $this->normalizeSearchText($entry['field'] ?? null);
        $title = $this->normalizeSearchText($entry['title'] ?? null);
        $queryTokens = $query !== '' ? $this->searchWordTokens($query) : [];

        if ($field !== '' && $query !== '') {
            if ($this->queryHasAssistantKeyword($query, [$field])) {
                $score += 260;
            }

            $fieldTokenMatches = count(array_intersect($this->searchWordTokens($field), $queryTokens));

            if ($fieldTokenMatches > 0) {
                $score += 120 + ($fieldTokenMatches * 20);
            }
        }

        if ($title !== '' && $query !== '' && $this->queryHasAssistantKeyword($query, [$title])) {
            $score += 160;
        }

        $userName = $this->normalizeSearchText($entry['userName'] ?? null);

        if ($userName !== '' && $query !== '') {
            if ($this->queryHasAssistantKeyword($query, [$userName])) {
                $score += 280;
            } else {
                $userTokenMatches = count(array_intersect($this->searchWordTokens($userName), $queryTokens));

                if ($userTokenMatches > 0) {
                    $score += 150 + ($userTokenMatches * 24);
                }
            }
        }

        return $score;
    }

    private function assistantChangeLogSubjectName(Collection $entries, ?array $window = null): string
    {
        $recordNames = $entries->pluck('recordName')->filter()->unique()->values();

        if ($recordNames->count() === 1) {
            return (string) $recordNames->first();
        }

        $entityNames = $entries->pluck('entityLabel')->filter()->unique()->values();

        if ($entityNames->count() === 1) {
            return (string) $entityNames->first();
        }

        if ($window !== null) {
            return $window['label'] . ' administration change logs';
        }

        return 'Administration change logs';
    }

    private function assistantChangeLogContextLabel(Collection $entries, ?array $window, string $query): ?string
    {
        if ($window !== null) {
            return $window['human'];
        }

        $recordNames = $entries->pluck('recordName')->filter()->unique()->values();

        if ($recordNames->count() === 1) {
            return (string) $recordNames->first();
        }

        return $this->normalizeText(Str::limit(trim($query), 120));
    }

    private function assistantHasAdministrationChangeLogIntent(string $query): bool
    {
        $normalized = $this->normalizeSearchText($query);

        if ($normalized === '') {
            return false;
        }

        return $this->queryHasAssistantKeyword($normalized, [
            'change log',
            'change logs',
            'administration change log',
            'administration change logs',
            'change history',
            'edit history',
            'modification history',
            'activity log',
            'audit log',
            'show change log',
            'show change logs',
            'show changes',
            'last changes',
            'latest changes',
            'recent changes',
            'what changes',
            'what were the changes',
            'what was changed',
            'who changed',
            'who modified',
            'who updated',
            'what changed',
            'kya change hua',
            'kya changes hue',
            'changes kya kiye',
            'change kya kiye',
            'kya changes kiye',
            'kya change kiye',
            'kisne change kiya',
            'kisne modify kiya',
            'kisne update kiya',
            'which field changed',
            'last change',
            'latest change',
            'last modification',
            'latest modification',
            'last edited',
            'last edited by',
            'last modified by',
            'modification field',
            'change description',
            'change hua',
            'change kab hua',
            'changed by',
        ]);
    }

    /**
     * @return array{label: string, human: string, from: Carbon, to: Carbon}|null
     */
    private function assistantChangeLogWindow(string $query): ?array
    {
        $normalized = $this->normalizeSearchText($query);

        if ($normalized === '') {
            return null;
        }

        if ($this->queryHasAssistantKeyword($normalized, ['today', 'aaj'])) {
            $from = today()->startOfDay();
            $to = today()->endOfDay();

            return [
                'label' => 'Today',
                'human' => 'Today (' . $from->format('d M Y') . ')',
                'from' => $from,
                'to' => $to,
            ];
        }

        if (preg_match('/(?:last|past|pichle)\s+(\d{1,3})\s+(?:days?|din)\b/u', $normalized, $matches) === 1) {
            $days = max(1, min((int) ($matches[1] ?? 0), 365));
            $from = today()->subDays($days - 1)->startOfDay();
            $to = today()->endOfDay();

            return [
                'label' => 'Last ' . $days . ' days',
                'human' => 'Last ' . $days . ' days (' . $from->format('d M Y') . ' to ' . $to->format('d M Y') . ')',
                'from' => $from,
                'to' => $to,
            ];
        }

        if ($this->queryHasAssistantKeyword($normalized, ['this month', 'is month', 'iss month'])) {
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfMonth()->endOfDay();

            return [
                'label' => 'This month',
                'human' => 'This month (' . $from->format('d M Y') . ' to ' . $to->format('d M Y') . ')',
                'from' => $from,
                'to' => $to,
            ];
        }

        return null;
    }

    private function assistantChangeLogEntityTypes(): array
    {
        return [
            Office::class => 'Office',
            Hub::class => 'Hub',
            Agent::class => 'Agent',
            OtherCompany::class => 'Other company',
            Supplier::class => 'Supplier',
            Customer::class => 'Customer',
            CustomerVessel::class => 'Vessel',
            Contact::class => 'Contact / user',
            HubUser::class => 'Hub user',
            AgentUser::class => 'Agent user',
        ];
    }

    private function assistantChangeLogContextForModel(mixed $model): ?array
    {
        if ($model instanceof Contact) {
            if ($model->office_id) {
                return [
                    'role' => match ((string) $model->category) {
                        'operations' => 'Operations user',
                        'account' => 'Account user',
                        'sales' => 'Sales user',
                        'manager' => 'Manager user',
                        default => 'Office user',
                    },
                    'parent' => $model->office?->office_name ?: ('Office #' . $model->office_id),
                ];
            }

            if ($model->customer_id) {
                return [
                    'role' => 'Customer contact',
                    'parent' => $model->customer?->customer_name ?: ('Customer #' . $model->customer_id),
                ];
            }

            if ($model->hub_id) {
                return [
                    'role' => 'Hub contact',
                    'parent' => $model->hub?->hub_name ?: ('Hub #' . $model->hub_id),
                ];
            }

            if ($model->agent_id) {
                return [
                    'role' => 'Agent contact',
                    'parent' => $model->agent?->agent_name ?: ('Agent #' . $model->agent_id),
                ];
            }

            if ($model->supplier_id) {
                return [
                    'role' => 'Supplier contact',
                    'parent' => $model->supplier?->supplier_name ?: ('Supplier #' . $model->supplier_id),
                ];
            }

            if ($model->other_company_id) {
                return [
                    'role' => 'Other company contact',
                    'parent' => $model->otherCompany?->company_name ?: ('Company #' . $model->other_company_id),
                ];
            }

            return [
                'role' => 'Contact',
                'parent' => null,
            ];
        }

        if ($model instanceof HubUser) {
            return [
                'role' => 'Hub user',
                'parent' => $model->hub?->hub_name ?: ($model->hub_id ? 'Hub #' . $model->hub_id : null),
            ];
        }

        if ($model instanceof AgentUser) {
            return [
                'role' => 'Agent user',
                'parent' => $model->agent?->agent_name ?: ($model->agent_id ? 'Agent #' . $model->agent_id : null),
            ];
        }

        if ($model instanceof CustomerVessel) {
            return [
                'role' => 'Vessel',
                'parent' => $model->customer?->customer_name ?: ($model->customer_id ? 'Customer #' . $model->customer_id : null),
            ];
        }

        return null;
    }

    private function assistantChangeLogEntityLabel(AdministrationChangeLog $log): string
    {
        $model = $log->loggable;

        if (! $model) {
            return $this->assistantChangeLogEntityTypes()[$log->loggable_type] ?? class_basename((string) $log->loggable_type);
        }

        $context = $this->assistantChangeLogContextForModel($model);

        if ($context !== null) {
            return $context['role'];
        }

        return $this->assistantChangeLogEntityTypes()[$log->loggable_type] ?? class_basename($model);
    }

    private function assistantChangeLogTitle(AdministrationChangeLog $log): string
    {
        $title = (string) $log->title;
        $model = $log->loggable;

        if (! $model) {
            return $title;
        }

        $context = $this->assistantChangeLogContextForModel($model);

        if ($context === null) {
            return $title;
        }

        $role = $context['role'];
        $parent = $context['parent'];

        if (str_ends_with($title, ' edited')) {
            $field = substr($title, 0, -strlen(' edited'));

            return $role . ' · ' . $field . ' edited';
        }

        if (preg_match('/^(Contact|Customer Vessel|Hub User|Agent User) created$/i', $title)) {
            return $parent
                ? $role . ' created · ' . $parent
                : $role . ' created';
        }

        return $role . ' · ' . $title;
    }

    private function assistantChangeLogRecordName(AdministrationChangeLog $log): string
    {
        $model = $log->loggable;

        if (! $model) {
            return '#' . $log->loggable_id . ' (deleted)';
        }

        $context = $this->assistantChangeLogContextForModel($model);

        if ($context !== null) {
            $name = match (true) {
                $model instanceof CustomerVessel => (string) ($model->vessel ?: 'Vessel #' . $model->id),
                default => (string) ($model->name ?: 'User #' . $model->id),
            };

            $parts = [$name];

            if ($context['parent']) {
                $parts[] = $context['parent'];
            }

            $label = implode(' · ', $parts);

            if (! in_array($context['role'], ['Vessel'], true)) {
                $label .= ' (' . $context['role'] . ')';
            }

            return $label;
        }

        return match (true) {
            $model instanceof Office => (string) ($model->office_name ?: 'Office #' . $model->id),
            $model instanceof Hub => (string) ($model->hub_name ?: 'Hub #' . $model->id),
            $model instanceof Agent => (string) ($model->agent_name ?: 'Agent #' . $model->id),
            $model instanceof OtherCompany => (string) ($model->company_name ?: 'Company #' . $model->id),
            $model instanceof Supplier => (string) ($model->supplier_name ?: 'Supplier #' . $model->id),
            $model instanceof Customer => (string) ($model->customer_name ?: 'Customer #' . $model->id),
            default => class_basename($model) . ' #' . $model->getKey(),
        };
    }

    private function assistantChangeLogFieldLabel(AdministrationChangeLog $log): ?string
    {
        if (filled($log->field)) {
            return Str::headline(str_replace('_', ' ', (string) $log->field));
        }

        $title = trim((string) $log->title);

        if ($title === '') {
            return null;
        }

        if (str_ends_with($title, ' edited')) {
            return substr($title, 0, -strlen(' edited'));
        }

        if (str_ends_with($title, ' created')) {
            return 'Created';
        }

        return null;
    }

    private function findAssistantOfficeRecord(string $query, ?int &$score = null): ?Office
    {
        return $this->bestAdministrationRecordMatch(
            Office::query()->orderByDesc('id')->get(),
            $query,
            fn (Office $office) => [
                $office->office_name,
                $office->office_short_name,
                $office->email,
                $office->phone_number,
                $office->address,
                $office->city,
                $office->postal_address,
                $office->postal_city,
                $office->invoicing_emails,
                $office->eori_number,
            ],
            $score
        );
    }

    private function findAssistantHubRecord(string $query, ?int &$score = null): ?Hub
    {
        return $this->bestAdministrationRecordMatch(
            Hub::query()->orderByDesc('id')->get(),
            $query,
            fn (Hub $hub) => [
                $hub->hub_name,
                $hub->code,
                $hub->email,
                $hub->phone_number,
                $hub->contact_person,
                $hub->hub_address,
                $hub->city,
                $hub->country,
                $hub->port_code,
                $hub->portal_email,
                $hub->responsible_manager,
            ],
            $score
        );
    }

    private function findAssistantAgentRecord(string $query, ?int &$score = null): ?Agent
    {
        return $this->bestAdministrationRecordMatch(
            Agent::query()->orderByDesc('id')->get(),
            $query,
            fn (Agent $agent) => [
                $agent->agent_name,
                $agent->code,
                $agent->email,
                $agent->phone,
                $agent->contact_person,
                $agent->agent_address,
                $agent->city,
                $agent->agent_type,
                $agent->port_code,
                $agent->responsible_manager,
            ],
            $score
        );
    }

    private function findAssistantSupplierRecord(string $query, ?int &$score = null): ?Supplier
    {
        return $this->bestAdministrationRecordMatch(
            Supplier::query()->orderByDesc('id')->get(),
            $query,
            fn (Supplier $supplier) => [
                $supplier->supplier_name,
                $supplier->email,
                $supplier->phone_number,
                $supplier->contact_person,
                $supplier->supplier_address,
                $supplier->city,
                $supplier->port_code,
                $supplier->currency,
            ],
            $score
        );
    }

    private function findAssistantCustomerRecord(string $query, ?int &$score = null): ?Customer
    {
        return $this->bestAdministrationRecordMatch(
            Customer::query()->orderByDesc('id')->get(),
            $query,
            fn (Customer $customer) => [
                $customer->customer_name,
                $customer->customer_number,
                $customer->email,
                $customer->phone,
                $customer->contact_person,
                $customer->un_locode,
            ],
            $score
        );
    }

    private function findAssistantContactRecord(string $query, ?int &$score = null): ?Contact
    {
        return $this->bestAdministrationRecordMatch(
            Contact::query()
                ->with(['office', 'customer', 'hub', 'supplier', 'otherCompany', 'agent'])
                ->orderByDesc('id')
                ->get(),
            $query,
            fn (Contact $contact) => [
                $contact->name,
                $contact->email,
                $contact->phone_number,
                $contact->description,
                $contact->reply_to_email,
                $contact->status,
                $contact->category,
                $this->contactParentName($contact),
                $this->contactParentLabel($contact),
            ],
            $score
        );
    }

    private function findAssistantVesselRecord(string $query, ?int &$score = null): ?CustomerVessel
    {
        return $this->bestAdministrationRecordMatch(
            CustomerVessel::query()->with('customer')->orderByDesc('id')->get(),
            $query,
            fn (CustomerVessel $vessel) => [
                $vessel->vessel,
                $vessel->vessel_name_alias,
                $vessel->vessel_imo,
                $vessel->customer_vessel_code,
                $vessel->shipyard,
                $vessel->shipyard_location,
                $vessel->manager,
                $vessel->account_manager,
            ],
            $score
        );
    }

    private function findAssistantUserRecord(string $query, ?int &$score = null): ?User
    {
        return $this->bestAdministrationRecordMatch(
            User::query()
                ->with(['offices', 'hubs', 'agents', 'suppliers'])
                ->orderByDesc('id')
                ->get(),
            $query,
            fn (User $user) => array_merge(
                [
                    $user->name,
                    $user->email,
                    filled($user->email) ? Str::before((string) $user->email, '@') : null,
                    $user->phone_number,
                    $user->role,
                    $user->is_active ? 'Active' : 'Inactive',
                ],
                $user->offices->pluck('office_name')->all(),
                $user->offices->pluck('office_short_name')->all(),
                $user->hubs->pluck('hub_name')->all(),
                $user->hubs->pluck('code')->all(),
                $user->agents->pluck('agent_name')->all(),
                $user->agents->pluck('code')->all(),
                $user->suppliers->pluck('supplier_name')->all(),
            ),
            $score
        );
    }

    private function bestAdministrationRecordMatch(Collection $records, string $query, callable $searchableValues, ?int &$score = null): mixed
    {
        $candidate = $this->bestAdministrationRecordCandidate($records, $query, $searchableValues);
        $score = $candidate['score'] ?? null;

        return $candidate['record'] ?? null;
    }

    private function bestAdministrationRecordCandidate(Collection $records, string $query, callable $searchableValues): ?array
    {
        $phrases = collect($this->assistantAdministrationLookupPhrases($query))
            ->map(fn ($phrase) => $this->normalizeSearchText($phrase))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $tokens = $this->assistantAdministrationLookupTokens($query);
        $bestMatch = null;
        $bestScore = 0;
        $exactMatch = null;
        $exactScore = 0;

        foreach ($records as $record) {
            $values = collect($searchableValues($record))
                ->flatten()
                ->map(fn ($value) => $this->normalizeSearchText($value))
                ->filter()
                ->unique()
                ->values();

            if ($values->isEmpty()) {
                continue;
            }

            $recordTokens = $values
                ->flatMap(fn ($value) => $this->searchWordTokens($value))
                ->unique()
                ->values()
                ->all();
            $score = 0;

            foreach ($phrases as $phrase) {
                $phraseTokens = $this->searchWordTokens($phrase);
                $isStrongExactPhrase = count($phraseTokens) >= 2 || preg_match('/[\d@._-]/u', $phrase) === 1;

                foreach ($values as $value) {
                    if ($value === $phrase && $isStrongExactPhrase && mb_strlen($phrase) >= 6) {
                        $matchScore = 5000 + mb_strlen($phrase);

                        if ($matchScore > $exactScore) {
                            $exactScore = $matchScore;
                            $exactMatch = $record;
                        }
                    }

                    if ($value === $phrase) {
                        $score = max($score, 520 + mb_strlen($phrase));
                        continue;
                    }

                    if (str_starts_with($value, $phrase)) {
                        $score = max($score, 460 + mb_strlen($phrase));
                        continue;
                    }

                    if (count($phraseTokens) > 1 && str_contains($value, $phrase)) {
                        $score = max($score, 400 + mb_strlen($phrase));
                        continue;
                    }

                    if (count($phraseTokens) === 1 && in_array($phraseTokens[0], $this->searchWordTokens($value), true)) {
                        $score = max($score, 390 + mb_strlen($phrase));
                        continue;
                    }

                    if (count($phraseTokens) > 1 && array_diff($phraseTokens, $this->searchWordTokens($value)) === []) {
                        $score = max($score, 360 + mb_strlen($phrase));
                    }
                }
            }

            foreach ($values as $value) {
                $valueTokens = $this->searchWordTokens($value);
                $matchedValueTokens = array_values(array_intersect($tokens, $valueTokens));

                if (count($valueTokens) >= 2 && count($matchedValueTokens) >= 2) {
                    $coverageScore = 430 + (count($matchedValueTokens) * 28);

                    if (array_diff($valueTokens, $tokens) === []) {
                        $coverageScore += 140;
                    }

                    $score = max($score, $coverageScore);
                }
            }

            if ($score === 0 && $tokens !== []) {
                $matchedTokens = 0;

                foreach ($tokens as $token) {
                    if (in_array($token, $recordTokens, true)) {
                        $matchedTokens += 1;
                        $score += 32 + min(12, mb_strlen($token));
                    }
                }

                if ($matchedTokens >= 2) {
                    $score += 90;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $record;
            }
        }

        if ($exactMatch !== null) {
            return [
                'record' => $exactMatch,
                'score' => $exactScore,
            ];
        }

        if ($bestScore < 130 || $bestMatch === null) {
            return null;
        }

        return [
            'record' => $bestMatch,
            'score' => $bestScore,
        ];
    }

    /**
     * @return list<string>
     */
    private function assistantAdministrationLookupPhrases(string $query): array
    {
        $phrases = [];

        foreach ($this->assistantLookupTerms($query) as $term) {
            $normalized = $this->normalizeSearchText($term);

            if ($normalized !== '') {
                $phrases[$normalized] = $normalized;
            }
        }

        $meaningfulTokens = $this->assistantAdministrationLookupTokens($query);

        if ($meaningfulTokens !== []) {
            $joined = implode(' ', $meaningfulTokens);
            $phrases[$joined] = $joined;

            foreach ($meaningfulTokens as $token) {
                if (mb_strlen($token) >= 3) {
                    $phrases[$token] = $token;
                }
            }
        }

        $rawTokens = $this->assistantAdministrationPhraseTokens($query);
        $tokenCount = count($rawTokens);

        for ($start = 0; $start < $tokenCount; $start++) {
            for ($length = 2; $length <= min(6, $tokenCount - $start); $length++) {
                $slice = $this->assistantTrimAdministrationPhraseTokens(array_slice($rawTokens, $start, $length));

                if (count($slice) < 2) {
                    continue;
                }

                $phrase = implode(' ', $slice);

                if ($phrase === '' || mb_strlen(str_replace(' ', '', $phrase)) < 4) {
                    continue;
                }

                $phrases[$phrase] = $phrase;
            }
        }

        return array_values($phrases);
    }

    /**
     * @return list<string>
     */
    private function assistantAdministrationPhraseTokens(string $query): array
    {
        $normalized = $this->normalizeSearchText($query);

        if ($normalized === '') {
            return [];
        }

        return collect(preg_split('/\s+/u', $normalized) ?: [])
            ->map(fn ($token) => trim((string) $token))
            ->filter(fn ($token) => $token !== '')
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $tokens
     * @return list<string>
     */
    private function assistantTrimAdministrationPhraseTokens(array $tokens): array
    {
        $stopwords = $this->assistantAdministrationBoundaryStopwords();

        while ($tokens !== [] && isset($stopwords[$tokens[0]])) {
            array_shift($tokens);
        }

        while ($tokens !== [] && isset($stopwords[$tokens[array_key_last($tokens)]])) {
            array_pop($tokens);
        }

        return array_values($tokens);
    }

    /**
     * @return array<string, true>
     */
    private function assistantAdministrationBoundaryStopwords(): array
    {
        $stopwords = $this->assistantAdministrationStopwords();

        unset($stopwords['group']);

        return $stopwords;
    }

    private function assistantLooksLikePersonLookup(string $query): bool
    {
        $normalized = $this->normalizeSearchText($query);

        if ($normalized === '') {
            return false;
        }

        $hasPersonPrompt = $this->queryHasAssistantKeyword($normalized, [
            'who is',
            'whos',
            'kon hai',
            'kon he',
            'kaun hai',
            'kaun he',
        ]);

        if (! $hasPersonPrompt && $this->queryHasAssistantKeyword($normalized, ['contact', 'contacts'])) {
            $hasPersonPrompt = ! $this->assistantUsesContactAsFieldContext($normalized);
        }

        if (! $hasPersonPrompt) {
            return false;
        }

        $tokens = array_values(array_filter(
            $this->assistantAdministrationLookupTokens($query),
            fn ($token) => preg_match('/\p{L}/u', $token) === 1
        ));

        return count($tokens) >= 2;
    }

    /**
     * @return list<string>
     */
    private function assistantAdministrationLookupTokens(string $query): array
    {
        $normalized = $this->normalizeSearchText($query);

        if ($normalized === '') {
            return [];
        }

        $stopwords = $this->assistantAdministrationStopwords();
        $tokens = [];

        foreach (preg_split('/\s+/u', $normalized) ?: [] as $token) {
            $token = trim((string) $token);
            $token = trim($token, " \t\n\r\0\x0B.,:;!?()[]{}\"'");

            if ($token === '' || isset($stopwords[$token])) {
                continue;
            }

            if (mb_strlen($token) < 2 && ! preg_match('/\d/', $token)) {
                continue;
            }

            $tokens[$token] = $token;
        }

        return array_values($tokens);
    }

    /**
     * @return array<string, true>
     */
    private function assistantAdministrationStopwords(): array
    {
        $words = [
            'aap', 'about', 'active', 'admin', 'all', 'and', 'answer', 'are', 'as', 'bata', 'batao', 'bit', 'both',
            'can', 'code', 'complete', 'details', 'detail', 'dikha', 'dikhao', 'do', 'dono', 'email', 'english', 'exact',
            'field', 'fields', 'for', 'full', 'give', 'hain', 'hai', 'help', 'how', 'i', 'id', 'in', 'information',
            'is', 'ka', 'kab', 'kaise', 'kar', 'ke', 'ki', 'kitna', 'kitne', 'kitni', 'kon', 'kaun', 'kya', 'layman',
            'me', 'mujhe', 'my', 'name', 'number', 'of', 'or', 'particular', 'please', 'poora', 'pura', 'push', 'pucho',
            'query', 'record', 'sab', 'sabhi', 'same', 'section', 'show', 'sirf', 'specific', 'status', 'summary',
            'summery', 'tell', 'the', 'their', 'these', 'this', 'to', 'user', 'view', 'what', 'where', 'which', 'who',
            'wise', 'with', 'yahan', 'yes', 'office', 'offices', 'hub', 'hubs', 'agent', 'agents', 'supplier', 'suppliers',
            'customer', 'customers', 'vessel', 'vessels', 'shipment', 'shipments', 'stock', 'stocks', 'crr', 'phone',
            'mobile', 'address', 'city', 'country', 'port', 'contact', 'person', 'manager', 'remarks', 'remark', 'note',
            'notes', 'invoice', 'invoicing', 'billing', 'currency', 'vat', 'eori', 'imo', 'group', 'postal', 'mail',
            'adress', 'addres', 'users', 'username', 'usernames', 'portal', 'login', 'access', 'role', 'roles', 'otp',
            'blocked', 'assigned', 'assignment', 'assignments', 'administration', 'change', 'changes', 'changed', 'log',
            'logs', 'history', 'audit', 'activity', 'edit', 'edited', 'modify', 'modified', 'modification', 'latest',
            'recent', 'recently', 'last', 'day', 'days', 'din',
            'first', 'mile', 'transport', 'delivery', 'services', 'service', 'documents', 'document', 'contacts', 'bank',
            'accounts', 'account', 'pricing', 'list', 'lists', 'everything', 'every', 'available', 'updated', 'update',
            'add', 'added', 'create', 'created', 'creator', 'kiya', 'kiye', 'kye', 'gaya', 'banaya', 'kisne', 'tha', 'thi', 'thein', 'theen', 'ne',
        ];

        return array_fill_keys($words, true);
    }

    /**
     * @return list<string>
     */
    private function searchWordTokens($value): array
    {
        if ($value === null) {
            return [];
        }

        return collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower((string) $value)) ?: [])
            ->map(fn ($token) => trim((string) $token))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeSearchText($value): string
    {
        if ($value === null) {
            return '';
        }

        $normalized = trim(mb_strtolower(preg_replace('/[^\p{L}\p{N}@._-]+/u', ' ', (string) $value) ?? ''));

        return trim(str_replace([' adress ', ' addres '], ' address ', ' ' . $normalized . ' '));
    }

    private function assistantAuditSection($model): array
    {
        return $this->assistantSection('Record activity', [
            $this->assistantField('created_by', 'Created by', $this->formatAuditUser($model->creator ?? null, $model->created_by ?? null), ['creator', 'added by', 'who added', 'kisne add kiya', 'kisne add kiya tha', 'kisne create kiya', 'kisne banaya']),
            $this->assistantField('created_at', 'Creation date', $this->formatDateTime($model->created_at), ['created date', 'created at', 'added on', 'kab add kiya', 'kab add kiya tha', 'kab create hua', 'kab create kiya', 'kab banaya']),
            $this->assistantField('updated_by', 'Updated by', $this->formatAuditUser($model->updater ?? null, $model->updated_by ?? null), ['last updated by', 'who updated', 'kisne update kiya', 'kisne update kiya tha']),
            $this->assistantField('updated_at', 'Last update', $this->formatDateTime($model->updated_at), ['updated at', 'last updated']),
        ]);
    }

    private function formatAuditUser($user, $fallbackId): ?string
    {
        $name = $this->normalizeText($user?->name);

        if ($name !== null) {
            return $name;
        }

        return filled($fallbackId) ? 'User #' . $fallbackId : null;
    }

    private function assistantEntityPayload(string $type, string $name, array $sections, array $options = []): array
    {
        $sections = collect($sections)
            ->map(fn (array $section) => [
                'title' => $section['title'] ?? 'Details',
                'fields' => array_values($section['fields'] ?? []),
            ])
            ->filter(fn (array $section) => $section['fields'] !== [])
            ->values()
            ->all();
        $fields = collect($sections)
            ->flatMap(fn (array $section) => $section['fields'])
            ->values()
            ->all();
        $identifier = $this->normalizeText($options['identifier'] ?? null);
        $status = $this->normalizeText($options['status'] ?? null);

        $quickFields = collect($options['quickFields'] ?? [])
            ->map(fn ($label) => $this->normalizeText($label))
            ->filter()
            ->values()
            ->all();

        if ($quickFields === []) {
            $quickFields = collect($fields)
                ->pluck('label')
                ->take(6)
                ->values()
                ->all();
        }

        return [
            'id' => (int) ($options['id'] ?? 0),
            'type' => $type,
            'entityLabel' => $options['entityLabel'] ?? ucfirst($type),
            'name' => $this->normalizeText($name) ?: '—',
            'identifierLabel' => $options['identifierLabel'] ?? null,
            'identifier' => $identifier,
            'status' => $status,
            'updatedAt' => $this->formatDateTime($options['updatedAt'] ?? null),
            'sections' => $sections,
            'fields' => $fields,
            'quickFields' => $quickFields,
            'identityValues' => collect(array_merge([$name, $identifier], $options['identityValues'] ?? []))
                ->map(fn ($value) => $this->normalizeText($value))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }

    private function assistantSection(string $title, array $fields): array
    {
        return [
            'title' => $title,
            'fields' => array_values(array_filter($fields, fn ($field) => is_array($field) && $field !== [])),
        ];
    }

    private function assistantField(string $key, string $label, $value, array $aliases = []): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'value' => $this->assistantFieldDisplayValue($value),
            'aliases' => collect(array_merge([$label, str_replace(['_', '-'], ' ', $key)], $aliases))
                ->map(fn ($alias) => $this->normalizeText($alias))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }

    private function assistantFieldDisplayValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d M Y H:i');
        }

        if (is_array($value)) {
            $joined = collect($value)
                ->map(fn ($item) => $this->normalizeText($item))
                ->filter()
                ->implode(', ');

            return $joined !== '' ? $joined : '—';
        }

        return $this->normalizeText($value) ?: '—';
    }

    private function assistantBooleanText($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        $normalized = mb_strtolower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'yes', 'on' => 'Yes',
            '0', 'false', 'no', 'off' => 'No',
            default => $this->normalizeText($value),
        };
    }

    private function resolveCountryName($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return $this->normalizeText($value);
        }

        static $countries = null;
        $countries ??= Country::query()
            ->pluck('name', 'id')
            ->map(fn ($name) => trim((string) $name))
            ->all();

        return $countries[(int) $value] ?? trim((string) $value);
    }

    private function implodeMeaningfulValues(array $parts, string $separator = ', '): string
    {
        return collect($parts)
            ->map(fn ($part) => $this->normalizeText($part))
            ->filter()
            ->implode($separator);
    }

    private function formatCollectionSummary(iterable $items, string $singular, string $plural, string $emptyMessage): string
    {
        $values = collect($items)
            ->map(fn ($item) => $this->normalizeText($item))
            ->filter()
            ->values()
            ->all();

        if ($values === []) {
            return $emptyMessage;
        }

        $count = count($values);

        return $count . ' ' . ($count === 1 ? $singular : $plural) . ': ' . implode(', ', $values);
    }

    private function maskSensitiveValue($value, int $visible = 4): ?string
    {
        $value = preg_replace('/\s+/u', '', trim((string) ($value ?? ''))) ?? '';

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) <= $visible) {
            return str_repeat('*', mb_strlen($value));
        }

        return str_repeat('*', max(0, mb_strlen($value) - $visible)) . mb_substr($value, -$visible);
    }

    private function formatContactIdentity(?Contact $contact): string
    {
        if ($contact === null) {
            return '—';
        }

        $summary = $this->implodeMeaningfulValues([
            $contact->name,
            $contact->email,
            $contact->phone_number,
        ], ' · ');

        return $summary !== '' ? $summary : '—';
    }

    private function contactParentType(Contact $contact): ?string
    {
        if ($contact->customer_id) {
            return 'customer';
        }

        if ($contact->office_id) {
            return 'office';
        }

        if ($contact->hub_id) {
            return 'hub';
        }

        if ($contact->agent_id) {
            return 'agent';
        }

        if ($contact->supplier_id) {
            return 'supplier';
        }

        if ($contact->other_company_id) {
            return 'other company';
        }

        return null;
    }

    private function contactParentLabel(Contact $contact): string
    {
        $type = $this->contactParentType($contact);

        return $type !== null ? ucwords($type) : 'Unassigned';
    }

    private function contactParentName(Contact $contact): string
    {
        return $this->normalizeText(
            $contact->customer?->customer_name
            ?? $contact->office?->office_name
            ?? $contact->hub?->hub_name
            ?? $contact->agent?->agent_name
            ?? $contact->supplier?->supplier_name
            ?? $contact->otherCompany?->company_name
        ) ?: '—';
    }

    private function formatResponsibleContact(?Contact $contact): string
    {
        if ($contact === null) {
            return '—';
        }

        $office = $contact->office?->office_short_name ?: $contact->office?->office_name;
        $summary = $this->implodeMeaningfulValues([
            $contact->name,
            $office,
            $contact->email,
        ], ' · ');

        return $summary !== '' ? $summary : '—';
    }

    private function formatContactsSummary(iterable $contacts): string
    {
        return $this->formatCollectionSummary(
            collect($contacts)
                ->sortBy('name')
                ->map(fn (Contact $contact) => $this->formatContactIdentity($contact))
                ->all(),
            'contact',
            'contacts',
            'No saved contacts'
        );
    }

    private function formatOfficeBankAccountSummary(iterable $accounts): string
    {
        return $this->formatCollectionSummary(
            collect($accounts)->map(function (OfficeBankAccount $account) {
                return $this->implodeMeaningfulValues([
                    $account->bank,
                    $account->currency,
                    $account->account_number ? 'A/C ' . $this->maskSensitiveValue($account->account_number) : null,
                    $account->iban ? 'IBAN ' . $this->maskSensitiveValue($account->iban) : null,
                    $account->swift ? 'SWIFT ' . $this->normalizeText($account->swift) : null,
                    $account->is_main_account ? 'Main account' : null,
                ], ' · ');
            })->all(),
            'bank account',
            'bank accounts',
            'No bank account saved'
        );
    }

    private function formatDocumentSummary(
        iterable $documents,
        string $nameField,
        ?string $typeField,
        ?string $sectionField,
        string $singular,
        string $plural
    ): string {
        return $this->formatCollectionSummary(
            collect($documents)->map(function ($document) use ($nameField, $typeField, $sectionField) {
                return $this->implodeMeaningfulValues([
                    data_get($document, $nameField),
                    $typeField ? data_get($document, $typeField) : null,
                    $sectionField ? data_get($document, $sectionField) : null,
                ], ' · ');
            })->all(),
            $singular,
            $plural,
            'No saved documents'
        );
    }

    private function formatCustomerAddress(?CustomerAddress $address): string
    {
        if ($address === null) {
            return '—';
        }

        $summary = $this->implodeMeaningfulValues([
            $address->street,
            $address->city,
            $address->state,
            $address->zip_code,
            $address->country?->name ?: $this->resolveCountryName($address->country_id),
        ]);

        return $summary !== '' ? $summary : '—';
    }

    private function formatVesselSummary(iterable $vessels): string
    {
        return $this->formatCollectionSummary(
            collect($vessels)->map(function (CustomerVessel $vessel) {
                return $this->implodeMeaningfulValues([
                    $vessel->vessel,
                    $vessel->vessel_imo ? 'IMO ' . $vessel->vessel_imo : null,
                ], ' · ');
            })->all(),
            'vessel',
            'vessels',
            'No saved vessels'
        );
    }

    /**
     * @return array{heading: string, singular: string, plural: string}
     */
    private function assistantTransportMeta(?string $service): array
    {
        return match ($service) {
            'Sea freight' => ['heading' => 'Vessel details', 'singular' => 'sea leg', 'plural' => 'sea legs'],
            'Truck' => ['heading' => 'Truck details', 'singular' => 'truck leg', 'plural' => 'truck legs'],
            'Courier' => ['heading' => 'Courier details', 'singular' => 'courier leg', 'plural' => 'courier legs'],
            'Release' => ['heading' => 'Release details', 'singular' => 'release leg', 'plural' => 'release legs'],
            'Hand Carry' => ['heading' => 'Hand carry details', 'singular' => 'hand carry leg', 'plural' => 'hand carry legs'],
            'On-board delivery' => ['heading' => 'On-board delivery details', 'singular' => 'on-board leg', 'plural' => 'on-board legs'],
            default => ['heading' => 'Flight details', 'singular' => 'flight leg', 'plural' => 'flight legs'],
        };
    }

    /**
     * @return list<array{
     *     title: string,
     *     referenceLabel: ?string,
     *     reference: ?string,
     *     carrierLabel: ?string,
     *     carrier: ?string,
     *     departurePort: ?string,
     *     departureDate: ?string,
     *     arrivalDate: ?string,
     *     arrivalTime: ?string,
     *     note: ?string
     * }>
     */
    private function mapAssistantTransportLegs(Shipment $shipment, array $portCities): array
    {
        $departurePort = $this->formatPortDisplay($shipment->departure_port_code, $portCities);

        return match ($shipment->service) {
            'Airfreight' => $shipment->flights
                ->sortBy('sort_order')
                ->values()
                ->map(function ($flight, int $index) use ($departurePort, $portCities) {
                    $isFirstLeg = $index === 0;

                    return [
                        'title' => 'Flight leg ' . ($index + 1),
                        'referenceLabel' => 'AWB',
                        'reference' => $isFirstLeg ? ($this->normalizeText($flight->leg_reference) ?: '—') : '—',
                        'carrierLabel' => 'Flight',
                        'carrier' => $this->normalizeText($flight->flight_number) ?: '—',
                        'departurePort' => $isFirstLeg
                            ? $departurePort
                            : $this->formatPortDisplay($flight->leg_reference, $portCities),
                        'departureDate' => $this->formatDate($flight->departure_date) ?: '—',
                        'arrivalDate' => $this->formatDate($flight->arrival_date) ?: '—',
                        'arrivalTime' => $this->normalizeText($flight->arrival_time) ?: '—',
                        'note' => null,
                    ];
                })
                ->all(),
            'Sea freight' => $shipment->seaLegs
                ->sortBy('sort_order')
                ->values()
                ->map(function ($leg, int $index) use ($departurePort, $portCities) {
                    $isFirstLeg = $index === 0;
                    $note = collect([
                        $this->normalizeText($leg->container_number) ? 'Container ' . $this->normalizeText($leg->container_number) : null,
                        $this->normalizeText($leg->transport_vessel_imo) ? 'IMO ' . $this->normalizeText($leg->transport_vessel_imo) : null,
                    ])->filter()->implode(', ');

                    return [
                        'title' => 'Sea leg ' . ($index + 1),
                        'referenceLabel' => 'Bill of lading',
                        'reference' => $isFirstLeg ? ($this->normalizeText($leg->bill_of_lading) ?: '—') : '—',
                        'carrierLabel' => 'Vessel',
                        'carrier' => $this->normalizeText($leg->transport_vessel_name) ?: '—',
                        'departurePort' => $isFirstLeg
                            ? $departurePort
                            : $this->formatPortDisplay($leg->bill_of_lading, $portCities),
                        'departureDate' => $this->formatDate($leg->etd) ?: '—',
                        'arrivalDate' => $this->formatDate($leg->eta) ?: '—',
                        'arrivalTime' => $this->normalizeText($leg->arrival_time) ?: '—',
                        'note' => $note !== '' ? $note : null,
                    ];
                })
                ->all(),
            'Truck' => $shipment->truckLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg, int $index) => [
                    'title' => 'Truck leg ' . ($index + 1),
                    'referenceLabel' => 'CMR',
                    'reference' => $this->normalizeText($leg->cmr),
                    'carrierLabel' => 'Freight company',
                    'carrier' => $this->normalizeText($leg->freight_company),
                    'departurePort' => $departurePort,
                    'departureDate' => $this->formatDate($leg->departure_date),
                    'arrivalDate' => $this->formatDate($leg->arrival_date),
                    'arrivalTime' => $this->normalizeText($leg->arrival_time),
                    'note' => null,
                ])
                ->all(),
            'Courier' => $shipment->courierLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg, int $index) => [
                    'title' => 'Courier leg ' . ($index + 1),
                    'referenceLabel' => 'Airway bill',
                    'reference' => $this->normalizeText($leg->airway_bill),
                    'carrierLabel' => 'Carrier',
                    'carrier' => $this->normalizeText($leg->carrier),
                    'departurePort' => $departurePort,
                    'departureDate' => $this->formatDate($leg->departure_date),
                    'arrivalDate' => $this->formatDate($leg->arrival_date),
                    'arrivalTime' => $this->normalizeText($leg->arrival_time),
                    'note' => null,
                ])
                ->all(),
            'Release' => $shipment->releaseLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg, int $index) => [
                    'title' => 'Release leg ' . ($index + 1),
                    'referenceLabel' => null,
                    'reference' => null,
                    'carrierLabel' => 'Freight company',
                    'carrier' => $this->normalizeText($leg->freight_company),
                    'departurePort' => $departurePort,
                    'departureDate' => null,
                    'arrivalDate' => $this->formatDate($leg->delivery_date),
                    'arrivalTime' => $this->normalizeText($leg->delivery_time),
                    'note' => null,
                ])
                ->all(),
            'Hand Carry' => $shipment->handCarryLegs
                ->sortBy('sort_order')
                ->values()
                ->map(function ($leg, int $index) use ($departurePort) {
                    $note = collect([
                        $this->normalizeText($leg->contact_phone) ? 'Phone ' . $this->normalizeText($leg->contact_phone) : null,
                        $leg->onboard_hand_carry ? 'Onboard hand carry yes' : null,
                    ])->filter()->implode(', ');

                    return [
                        'title' => 'Hand carry leg ' . ($index + 1),
                        'referenceLabel' => null,
                        'reference' => null,
                        'carrierLabel' => 'Contact',
                        'carrier' => $this->normalizeText($leg->contact_name),
                        'departurePort' => $departurePort,
                        'departureDate' => $this->formatDate($leg->departure_date),
                        'arrivalDate' => $this->formatDate($leg->arrival_date),
                        'arrivalTime' => $this->normalizeText($leg->arrival_time),
                        'note' => $note !== '' ? $note : null,
                    ];
                })
                ->all(),
            'On-board delivery' => $shipment->onBoardLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg, int $index) => [
                    'title' => 'On-board leg ' . ($index + 1),
                    'referenceLabel' => null,
                    'reference' => null,
                    'carrierLabel' => 'Delivery',
                    'carrier' => $this->normalizeText($shipment->consignee_att) ?: $this->normalizeText($shipment->consignee),
                    'departurePort' => $departurePort,
                    'departureDate' => $this->formatDate($leg->departure_date),
                    'arrivalDate' => $this->formatDate($leg->delivery_date),
                    'arrivalTime' => $this->normalizeText($leg->delivery_time),
                    'note' => null,
                ])
                ->all(),
            default => [],
        };
    }

    private function formatDate($value): ?string
    {
        return $this->formatTemporalValue($value, 'd M Y');
    }

    private function formatDateTime($value): ?string
    {
        return $this->formatTemporalValue($value, 'd M Y H:i');
    }

    private function formatDecimal($value, int $decimals = 2, ?string $empty = null): ?string
    {
        if ($value === null || $value === '') {
            return $empty;
        }

        return number_format((float) $value, $decimals, '.', '');
    }

    private function formatCbm($value, ?string $empty = null): ?string
    {
        if ($value === null || $value === '') {
            return $empty;
        }

        return PackageVolumeMetrics::formatCbm((float) $value);
    }

    private function formatValueDisplay($value, $currency = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $formatted = $this->formatDecimal($value, 2, '0.00');
        $currency = trim((string) ($currency ?? ''));

        return $currency !== '' ? ($formatted . ' ' . $currency) : $formatted;
    }

    private function formatShipmentValueDisplay(float $value, array $currencies): string
    {
        $formatted = $this->formatDecimal($value, 2, '0.00') ?? '0.00';

        if (count($currencies) === 1) {
            return $formatted . ' ' . $currencies[0];
        }

        if (count($currencies) > 1) {
            return $formatted . ' (' . implode(', ', $currencies) . ')';
        }

        return $formatted;
    }

    private function formatJoinedValues($value): string
    {
        if (is_array($value)) {
            $joined = collect($value)
                ->map(fn ($item) => $this->normalizeText($item))
                ->filter()
                ->implode(', ');

            return $joined !== '' ? $joined : '—';
        }

        return $this->normalizeText($value) ?: '—';
    }

    private function formatPortDisplay(?string $code, array $portCities, ?string $fallbackCity = null): string
    {
        $code = trim((string) ($code ?? ''));
        $city = '';

        if ($code !== '') {
            $city = trim((string) ($portCities[$code] ?? $portCities[strtoupper($code)] ?? ''));
        }

        if ($city === '' && $fallbackCity !== null) {
            $city = Shipment::normalizePortCityLabel($fallbackCity);
        }

        if ($code !== '' && $city !== '') {
            return $code . ', ' . $city;
        }

        if ($code !== '') {
            return $code;
        }

        return $city !== '' ? $city : '—';
    }

    private function normalizeText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');

        return $text !== '' ? $text : null;
    }

    private function formatTemporalValue($value, string $format): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($format);
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable) {
            return trim((string) $value) ?: null;
        }
    }

    private function queryHasAssistantKeyword(string $query, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/u', $query)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<'stock'|'shipment'|'office'|'hub'|'agent'|'supplier'|'customer'|'contact'|'vessel'|'user'|'change_log'>
     */
    private function assistantExplicitLookupTargets(string $query): array
    {
        $normalized = strtolower($query);
        $targets = [];
        $hasStructuredLookupIdentifier = $this->assistantHasStructuredLookupIdentifier($query);
        $hasShipmentTarget = $this->queryHasAssistantKeyword($normalized, ['shipment', 'shipments', 'awb', 'mawb', 'mbl', 'flight', 'transport']);
        $hasStockTarget = $this->queryHasAssistantKeyword($normalized, ['stock', 'stocks', 'crr']);
        $hasChangeLogTarget = $this->assistantHasAdministrationChangeLogIntent($normalized);
        $hasOfficeTarget = $this->queryHasAssistantKeyword($normalized, ['office', 'offices']);
        $hasHubTarget = $this->queryHasAssistantKeyword($normalized, ['hub', 'hubs']);
        $hasAgentTarget = $this->queryHasAssistantKeyword($normalized, ['agent', 'agents']);
        $hasSupplierTarget = $this->queryHasAssistantKeyword($normalized, ['supplier', 'suppliers']);
        $hasCustomerTarget = $this->queryHasAssistantKeyword($normalized, ['customer', 'customers']);
        $hasVesselTarget = $this->queryHasAssistantKeyword($normalized, ['vessel', 'vessels', 'imo']);
        $hasUserTarget = $this->queryHasAssistantKeyword($normalized, ['user', 'users', 'portal user', 'portal users', 'login user', 'login users', 'username']);
        $officeFieldContextOnly = $this->assistantUsesOfficeAsFieldContext($normalized);
        $contactFieldContextOnly = $this->assistantUsesContactAsFieldContext($normalized);
        $customerFieldContextOnly = $this->assistantUsesCustomerAsFieldContext($normalized);
        $customerVesselRelationshipQuery = $this->assistantLooksLikeCustomerVesselRelationshipQuery($normalized);
        $hasAdministrationEntityTarget = $hasOfficeTarget
            || $hasHubTarget
            || $hasAgentTarget
            || $hasSupplierTarget
            || $hasCustomerTarget
            || $hasVesselTarget
            || $hasUserTarget;
        $hasOtherEntityTarget = $hasShipmentTarget
            || $hasStockTarget
            || $hasChangeLogTarget
            || $hasOfficeTarget
            || $hasHubTarget
            || $hasAgentTarget
            || $hasSupplierTarget
            || $hasCustomerTarget
            || $hasVesselTarget
            || $hasUserTarget;

        $push = static function (string $type) use (&$targets): void {
            if (! in_array($type, $targets, true)) {
                $targets[] = $type;
            }
        };

        if ($hasShipmentTarget) {
            $push('shipment');
        }

        if ($hasStockTarget) {
            $push('stock');
        }

        if (
            $hasChangeLogTarget
            && ! $hasShipmentTarget
            && ! $hasStockTarget
            && (! $hasStructuredLookupIdentifier || $hasAdministrationEntityTarget)
        ) {
            $push('change_log');
        }

        if ($hasOfficeTarget && ! ($officeFieldContextOnly && ($hasHubTarget || $hasAgentTarget || $hasSupplierTarget))) {
            $push('office');
        }

        if ($hasHubTarget) {
            $push('hub');
        }

        if ($hasAgentTarget) {
            $push('agent');
        }

        if ($hasSupplierTarget) {
            $push('supplier');
        }

        if (($hasCustomerTarget && ! ($customerFieldContextOnly && $hasVesselTarget)) || $customerVesselRelationshipQuery) {
            $push('customer');
        }

        if ($hasUserTarget) {
            $push('user');
        }

        if (($this->assistantLooksLikePersonLookup($normalized) || $this->queryHasAssistantKeyword($normalized, ['contact', 'contacts']))
            && ! ($contactFieldContextOnly && $hasOtherEntityTarget)) {
            $push('contact');
        }

        if ($hasVesselTarget) {
            $push('vessel');
        }

        return $targets;
    }

    private function assistantUsesOfficeAsFieldContext(string $query): bool
    {
        foreach ([
            'office address',
            'office city',
            'office district',
            'office district state',
            'office state',
            'office zip',
            'office zip code',
            'office zipcode',
            'office pin code',
            'office country',
            'office eori',
        ] as $phrase) {
            if ($this->queryHasAssistantKeyword($query, [$phrase])) {
                return true;
            }
        }

        return false;
    }

    private function assistantUsesContactAsFieldContext(string $query): bool
    {
        foreach ([
            'contact person',
            'contact list',
            'main contact',
            'primary contact',
            'reply to email',
            'contact pre alert',
            'contact pre alerts',
            'pre alert contact',
            'contact stocklist',
            'contact stocklists',
            'stocklist contact',
            'contact stock notification',
            'contact stock notifications',
            'stock notification contact',
            'contact free storage notification',
            'contact free storage notifications',
            'free storage contact',
            'contact offers',
        ] as $phrase) {
            if ($this->queryHasAssistantKeyword($query, [$phrase])) {
                return true;
            }
        }

        if ($this->queryHasAssistantKeyword($query, ['contacts'])) {
            return true;
        }

        return false;
    }

    private function assistantUsesCustomerAsFieldContext(string $query): bool
    {
        foreach ([
            'vessel ka customer',
            'vessel me customer',
            'vessel ka customer name',
            'vessel me customer name',
            'customer vessel code',
            'yearly customer reference',
        ] as $phrase) {
            if ($this->queryHasAssistantKeyword($query, [$phrase])) {
                return true;
            }
        }

        if ($this->queryHasAssistantKeyword($query, ['for vessel'])
            && $this->queryHasAssistantKeyword($query, ['customer', 'customer name'])) {
            return true;
        }

        return false;
    }

    private function assistantLooksLikeCustomerVesselRelationshipQuery(string $query): bool
    {
        if (! $this->queryHasAssistantKeyword($query, ['vessels'])) {
            return false;
        }

        return $this->queryHasAssistantKeyword($query, [
            'how many vessels',
            'number of vessels',
            'total vessels',
            'total kitne vessels',
            'kitne vessels',
            'kitni vessels',
            'which vessels',
            'kaun se vessels',
            'kaunse vessels',
            'vessel names',
            'vessel name list',
        ]);
    }

    /**
     * @return list<'stock'|'shipment'|'office'|'hub'|'agent'|'supplier'|'customer'|'contact'|'vessel'|'user'|'change_log'>
     */
    private function assistantLookupTargets(string $query): array
    {
        $targets = $this->assistantExplicitLookupTargets($query);
        $fallbackTargets = $this->assistantLooksLikePersonLookup($query)
            ? ['stock', 'shipment', 'contact', 'user', 'customer', 'vessel', 'hub', 'agent', 'supplier', 'office']
            : ['stock', 'shipment', 'customer', 'contact', 'user', 'vessel', 'hub', 'agent', 'supplier', 'office'];

        foreach ($fallbackTargets as $type) {
            if (! in_array($type, $targets, true)) {
                $targets[] = $type;
            }
        }

        return $targets;
    }

    /**
     * @return list<string>
     */
    private function assistantLookupTerms(string $query): array
    {
        $terms = [];

        preg_match_all('/\b[A-Za-z0-9]{2,}(?:-[A-Za-z0-9]+)+\b/', $query, $identifierMatches);
        preg_match_all('/\b\d{5,}\b/', $query, $digitMatches);

        foreach (array_merge($identifierMatches[0] ?? [], $digitMatches[0] ?? [], [$query]) as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate === '' || mb_strlen($candidate) < 3) {
                continue;
            }

            $terms[mb_strtolower($candidate)] = $candidate;
        }

        return array_values($terms);
    }

    private function assistantHasStructuredLookupIdentifier(string $query): bool
    {
        return preg_match('/\b[A-Za-z0-9]{2,}(?:-[A-Za-z0-9]+)+\b/', $query) === 1
            || preg_match('/\b\d{5,}\b/', $query) === 1;
    }

    private function findAssistantStockRecord(User $user, string $query): ?Crr
    {
        foreach ($this->assistantLookupTerms($query) as $term) {
            $baseQuery = $this->visibleCrrs($user)
                ->with(['customerVessel.customer', 'packages', 'shipments'])
                ->orderByDesc('id');

            $exact = (clone $baseQuery)
                ->where('stock_number', $term)
                ->first();

            if ($exact !== null) {
                return $exact;
            }

            if (preg_match('/^\d{5,}$/', $term)) {
                $suffixPattern = '%-' . addcslashes($term, "%_\\") . '%';
                $suffix = (clone $baseQuery)
                    ->where('stock_number', 'like', $suffixPattern)
                    ->first();

                if ($suffix !== null) {
                    return $suffix;
                }
            }

            $prefixPattern = ListSearch::prefix($term);

            if ($prefixPattern !== null) {
                $prefix = (clone $baseQuery)
                    ->where('stock_number', 'like', $prefixPattern)
                    ->first();

                if ($prefix !== null) {
                    return $prefix;
                }
            }
        }

        return null;
    }

    private function findAssistantShipmentRecord(User $user, string $query): ?Shipment
    {
        foreach ($this->assistantLookupTerms($query) as $term) {
            $baseQuery = $this->visibleShipments($user)
                ->with(['accountManager', 'creator', 'documents', 'crrs.customerVessel.customer', 'crrs.packages', 'irregularities'])
                ->orderByDesc('id');

            $exact = (clone $baseQuery)
                ->where('shipment_number', $term)
                ->first();

            if ($exact !== null) {
                return $exact;
            }

            $lookupPattern = $this->assistantShipmentLookupPattern($term);

            if ($lookupPattern === null) {
                continue;
            }

            $match = (clone $baseQuery)
                ->where('shipment_number', 'like', $lookupPattern)
                ->first();

            if ($match !== null) {
                return $match;
            }
        }

        return null;
    }

    private function assistantShipmentLookupPattern(string $term): ?string
    {
        if ($term === '' || preg_match('/^[A-Za-z0-9]{1,3}-$/', $term)) {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9]{2,3}-/', $term)) {
            return ListSearch::prefix($term, 6);
        }

        if (preg_match('/^\d{5,}$/', $term)) {
            return '%-' . addcslashes($term, "%_\\") . '-%';
        }

        return ListSearch::contains($term, 5);
    }

    /**
     * @return list<string>
     */
    private function assistantShipmentRelations(): array
    {
        return [
            'accountManager',
            'creator',
            'changeLogs.user',
            'documents',
            'crrs.customerVessel.customer',
            'crrs.packages',
            'irregularities',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ];
    }

    /**
     * @return list<string>
     */
    private function assistantStockRelations(): array
    {
        return ['customerVessel.customer', 'packages', 'shipments'];
    }

}
