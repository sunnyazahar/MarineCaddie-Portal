<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OtherCompanyRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function distinctCountries(): Collection;

    public function findOrFail(int $id): \App\Models\OtherCompany;

    public function create(array $data): \App\Models\OtherCompany;

    public function update(\App\Models\OtherCompany $company, array $data): bool;

    public function delete(\App\Models\OtherCompany $company): bool;
}
