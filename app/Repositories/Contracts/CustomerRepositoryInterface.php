<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function filterOffices(): Collection;

    public function filterAccountManagers(): Collection;

    public function filterSalesManagers(): Collection;

    public function filterCountries(): Collection;
}
