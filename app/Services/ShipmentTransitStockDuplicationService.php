<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\CrrDocument;
use App\Models\Shipment;
use App\Repositories\Contracts\CrrDocumentRepositoryInterface;
use App\Repositories\Contracts\PartyLookupRepositoryInterface;
use App\Repositories\Contracts\ShipmentTransitStockRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShipmentTransitStockDuplicationService
{
    public function __construct(
        private ShipmentTransitStockRepositoryInterface $transitStocks,
        private PartyLookupRepositoryInterface $partyLookupRepository,
        private CrrDocumentRepositoryInterface $documents,
    ) {}

    public function resolveConsigneePartyCode(?string $party): ?string
    {
        return $this->resolveHubAgentFromShipmentParty($party);
    }

    /**
     * True when this shipment already has destination copies created FROM its
     * currently linked stocks.
     *
     * Multi-leg safe: a linked stock that is itself a prior-leg duplicate
     * (duplicated_from_crr_id set) does NOT count as "already transited".
     */
    public function hasDestinationStocksForShipment(Shipment $shipment): bool
    {
        $linkedIds = $shipment->crrs()
            ->pluck('crrs.id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values()
            ->all();

        if ($linkedIds === []) {
            return false;
        }

        return $this->transitStocks
            ->existingDuplicates($linkedIds)
            ->isNotEmpty();
    }

    /**
     * Duplicate linked stocks for destination (Active copies with transit refs / hub_agent).
     *
     * @param  bool  $syncShipmentLinks  When true, replace shipment_crr with the duplicates.
     *                                   When false (Complete / Transit finalize), keep completed
     *                                   originals linked so the shipment still shows those stocks.
     * @return array<int, int> Map of original CRR id => duplicate CRR id
     */
    public function duplicateStocksForTransit(
        Shipment $shipment,
        ?string $consigneeCode = null,
        bool $syncShipmentLinks = true,
    ): array {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $originalIds = $shipment->crrs->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($originalIds === []) {
            return [];
        }

        $existingDuplicates = $this->transitStocks->existingDuplicates($originalIds);

        $mapping = [];

        foreach ($shipment->crrs as $original) {
            if ($existingDuplicates->has($original->id)) {
                $mapping[$original->id] = $existingDuplicates[$original->id]->id;

                continue;
            }

            $duplicate = $this->duplicateCrr($original, $shipment, $consigneeCode);
            $mapping[$original->id] = $duplicate->id;
        }

        if ($syncShipmentLinks && $mapping !== []) {
            $shipment->crrs()->sync(array_values($mapping));
        }

        return $mapping;
    }

    private function duplicateCrr(Crr $original, Shipment $shipment, ?string $consigneeCode = null): Crr
    {
        $attributes = $this->duplicateableAttributes($original);
        // Destination copies stay free of related shipment until the user attaches them later.
        $attributes['internal_shipment'] = null;
        $attributes['duplicated_from_crr_id'] = $original->id;
        $attributes['status'] = Crr::STATUS_ACTIVE;

        $transit = $this->resolveTransitFields($shipment);
        if ($transit['transit_type'] !== null) {
            $attributes['transit_type'] = $transit['transit_type'];
        }
        if ($transit['transit_id'] !== null) {
            $attributes['transit_id'] = $transit['transit_id'];
        }

        $hubAgent = $this->resolveHubAgentFromConsigneeCode($consigneeCode);
        if ($hubAgent === null || $hubAgent === '') {
            $hubAgent = (string) ($this->resolveHubAgentFromShipmentParty($shipment->consignee) ?? '');
        }
        if ($hubAgent !== '') {
            $attributes['hub_agent'] = $hubAgent;
        }

        $deliveryDate = $this->deliveryDateFromShipment($shipment);
        if ($deliveryDate !== null) {
            $attributes['expected_delivery_date'] = $deliveryDate;
            $attributes['actual_delivery_date'] = $deliveryDate;
        }

        $duplicate = $this->transitStocks->createCrr($attributes);

        foreach ($original->packages as $package) {
            $packageAttributes = $this->duplicateableAttributes($package);
            $packageAttributes['crr_id'] = $duplicate->id;
            $packageAttributes['warehouse_location'] = $shipment->shipment_number;

            $this->transitStocks->createCrrPackage($packageAttributes);
        }

        foreach ($original->costs as $cost) {
            $costAttributes = $this->duplicateableAttributes($cost);
            $costAttributes['crr_id'] = $duplicate->id;

            $this->transitStocks->createCrrCost($costAttributes);
        }

        foreach ($original->documents as $document) {
            $this->duplicateDocument($document, $duplicate->id);
        }

        return $duplicate;
    }

    /**
     * Copy only fillable attributes that exist on the model's table.
     *
     * @return array<string, mixed>
     */
    private function duplicateableAttributes(object $model): array
    {
        if (! method_exists($model, 'getFillable') || ! method_exists($model, 'getTable') || ! method_exists($model, 'getAttribute')) {
            return [];
        }

        $columns = array_flip(\Illuminate\Support\Facades\Schema::getColumnListing($model->getTable()));
        $attributes = [];

        foreach ($model->getFillable() as $field) {
            if ($field === 'id' || ! isset($columns[$field])) {
                continue;
            }

            $attributes[$field] = $model->getAttribute($field);
        }

        return $attributes;
    }

    /**
     * @return array{transit_type: ?string, transit_id: ?string}
     */
    private function resolveTransitFields(Shipment $shipment): array
    {
        return match ($shipment->service) {
            'Courier' => [
                'transit_type' => 'CMR',
                'transit_id' => $this->firstNonEmptyValue($shipment->courierLegs->pluck('airway_bill')),
            ],
            'Airfreight' => [
                'transit_type' => 'AWB',
                'transit_id' => $this->firstNonEmptyValue($shipment->flights->pluck('leg_reference')),
            ],
            'Sea freight' => [
                'transit_type' => 'B/L',
                'transit_id' => $this->firstNonEmptyValue($shipment->seaLegs->pluck('bill_of_lading')),
            ],
            'Truck' => [
                'transit_type' => 'CMR',
                'transit_id' => $this->firstNonEmptyValue($shipment->truckLegs->pluck('cmr')),
            ],
            default => [
                'transit_type' => null,
                'transit_id' => null,
            ],
        };
    }

    private function firstNonEmptyValue($values): ?string
    {
        foreach ($values as $value) {
            $normalized = trim((string) ($value ?? ''));
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return null;
    }

    private function resolveHubAgentFromConsigneeCode(?string $consigneeCode): ?string
    {
        $consigneeCode = trim((string) ($consigneeCode ?? ''));
        if ($consigneeCode === '') {
            return null;
        }

        $hub = $this->partyLookupRepository->findHubByCodePortOrLocode($consigneeCode);
        if ($hub) {
            return $this->hubAgentValue($hub);
        }

        $agent = $this->partyLookupRepository->findAgentByCodePortOrLocode($consigneeCode);
        if ($agent) {
            return $this->hubAgentValue($agent);
        }

        $customer = $this->partyLookupRepository->findCustomerByNumberLocodeOrAddressPort($consigneeCode);
        if ($customer) {
            $customerNumber = trim((string) ($customer->customer_number ?? ''));
            if ($customerNumber !== '') {
                return $customerNumber;
            }

            $customerName = trim((string) ($customer->customer_name ?? ''));

            return $customerName !== '' ? $customerName : null;
        }

        return $consigneeCode;
    }

    private function deliveryDateFromShipment(Shipment $shipment): ?string
    {
        // Prefer service-details arrival (last leg ETA / arrival / delivery date).
        $arrival = $shipment->service_eta ?? $shipment->deadline_arrival;

        if ($arrival === null) {
            return null;
        }

        if ($arrival instanceof \DateTimeInterface) {
            return Carbon::instance($arrival)->format('Y-m-d');
        }

        $normalized = trim((string) $arrival);
        if ($normalized === '') {
            return null;
        }

        try {
            return Carbon::parse($normalized)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveHubAgentFromShipmentParty(?string $party): ?string
    {
        if (!$party || !str_contains($party, ':')) {
            return null;
        }

        [$type, $id] = explode(':', $party, 2);
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        return match ($type) {
            'hub' => $this->hubAgentValue($this->partyLookupRepository->findHubById($id)),
            'agent' => $this->hubAgentValue($this->partyLookupRepository->findAgentById($id)),
            'office' => $this->hubAgentValue($this->partyLookupRepository->findOfficeById($id)),
            default => null,
        };
    }

    private function hubAgentValue(?object $party): ?string
    {
        if (!$party) {
            return null;
        }

        $code = trim((string) ($party->code ?? ''));
        if ($code !== '') {
            return $code;
        }

        $name = trim((string) ($party->hub_name ?? $party->agent_name ?? $party->office_name ?? ''));

        return $name !== '' ? $name : null;
    }

    private function duplicateDocument(CrrDocument $document, int $duplicateCrrId): void
    {
        $disk = \App\Support\PrivateDisk::disk();
        $newPath = $document->file_path;

        if ($document->file_path && $disk->exists($document->file_path)) {
            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
            $basename = pathinfo($document->file_path, PATHINFO_FILENAME);
            $newPath = 'crr_documents/' . $basename . '-dup-' . Str::uuid() . ($extension ? '.' . $extension : '');

            try {
                $disk->copy($document->file_path, $newPath);
            } catch (\Throwable $e) {
                Log::warning('Could not copy CRR document during transit duplication: ' . $e->getMessage());
                $newPath = $document->file_path;
            }
        }

        $this->documents->create([
            'crr_id' => $duplicateCrrId,
            'file_name' => $document->file_name,
            'file_path' => $newPath,
            'file_type' => $document->file_type,
        ]);
    }
}
