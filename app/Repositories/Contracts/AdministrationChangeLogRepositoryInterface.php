<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdministrationChangeLogRepositoryInterface
{
    public function search(array $filters, int $perPage = 50): LengthAwarePaginator;
}
