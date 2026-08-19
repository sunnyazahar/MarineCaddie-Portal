<?php

namespace App\Repositories\Contracts;

use App\Models\Crr;
use App\Models\CrrDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface CrrRepositoryInterface
{
    public function buildIndexQuery(array $filters): Builder;

    public function paginateIndex(array $filters, int $perPage): LengthAwarePaginator;

    public function indexFilterOptions(): array;

    public function buildStockFollowUpQuery(array $filters): Builder;

    public function paginateStockFollowUp(array $filters, int $perPage): LengthAwarePaginator;

    public function stockFollowUpFilterOptions(): array;

    public function buildPickupWorkListQuery(array $filters, Collection $handledByMap): Builder;

    public function paginatePickupWorkList(array $filters, Collection $handledByMap, int $perPage): LengthAwarePaginator;

    public function pickupWorkListFilterOptions(Collection $handledByMap): array;

    public function hubAgentHandledByMap(): Collection;

    public function resolveHubAgentForPrint(Crr $crr): array;

    public function stockNumberExists(string $stockNumber): bool;

    public function createCrr(array $attributes): Crr;

    public function storePackages(int $crrId, array $packages): void;

    public function replacePackages(Crr $crr, array $packages): void;

    public function storeCosts(int $crrId, array $costs): void;

    public function replaceCosts(Crr $crr, array $costs): void;

    public function editReferenceData(): array;

    public function findWithRelationsOrFail(int $id, array $relations = []): Crr;

    public function findOrFail(int $id): Crr;

    public function selectedWithRelations(array $ids, array $relations = []): EloquentCollection;

    public function createDocument(array $attributes): CrrDocument;

    public function findDocumentForCrrOrFail(int $crrId, int $docId): CrrDocument;

    public function findDocumentOrFail(int $docId): CrrDocument;
}
