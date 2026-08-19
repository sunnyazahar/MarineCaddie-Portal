<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface OfficeRepositoryInterface
{
    public function all(): Collection;

    public function findOrFail(int $id): \App\Models\Office;
}
