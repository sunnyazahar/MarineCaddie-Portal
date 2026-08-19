<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface OfficeRepositoryInterface
{
    public function all(): Collection;

    public function findWithRelations(int $id, array $relations = []): \App\Models\Office;

    public function create(array $data): \App\Models\Office;

    public function update(\App\Models\Office $office, array $data): bool;

    public function findOrFail(int $id): \App\Models\Office;
}
