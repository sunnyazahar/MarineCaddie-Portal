<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function findOrFail(int $id): \App\Models\Supplier;

    public function findWithRelations(int $id, array $relations = []): \App\Models\Supplier;

    public function create(array $data): \App\Models\Supplier;

    public function update(\App\Models\Supplier $supplier, array $data): bool;

    public function delete(int $id): bool;
}
