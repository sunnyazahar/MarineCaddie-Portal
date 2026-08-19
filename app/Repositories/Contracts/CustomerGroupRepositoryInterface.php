<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CustomerGroupRepositoryInterface
{
    public function all(): Collection;
}
