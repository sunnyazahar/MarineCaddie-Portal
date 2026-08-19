<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function findOrFail(int $id, array $with = []): \App\Models\Customer;

    public function find(int $id): ?\App\Models\Customer;

    public function create(array $data): \App\Models\Customer;

    public function update(\App\Models\Customer $customer, array $data): bool;

    public function filterOffices(): Collection;

    public function filterAccountManagers(): Collection;

    public function filterSalesManagers(): Collection;

    public function filterCountries(): Collection;
}
