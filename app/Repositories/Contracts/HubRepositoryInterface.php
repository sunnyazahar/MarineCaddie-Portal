<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface HubRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function distinctCountries(): Collection;

    public function countryFlags(): \Illuminate\Support\Collection;

    public function findOrFail(int $id): \App\Models\Hub;

    public function findWithRelations(int $id, array $relations = []): \App\Models\Hub;

    public function create(array $data): \App\Models\Hub;

    public function update(\App\Models\Hub $hub, array $data): bool;

    public function deleteById(int $id): bool;

    public function updateStatus(\App\Models\Hub $hub, bool $isInactive): bool;
}
