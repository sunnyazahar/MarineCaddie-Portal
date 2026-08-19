<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface VesselRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;

    public function distinctTypes(): Collection;
}
