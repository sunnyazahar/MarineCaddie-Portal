<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AgentRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function distinctCountries(): Collection;

    public function distinctTypes(): Collection;

    public function findOrFail(int $id): \App\Models\Agent;

    public function findWithRelations(int $id, array $relations = []): \App\Models\Agent;

    public function create(array $data): \App\Models\Agent;

    public function update(\App\Models\Agent $agent, array $data): bool;

    public function delete(int $id): bool;

    public function updateStatus(\App\Models\Agent $agent, bool $isActive): bool;
}
