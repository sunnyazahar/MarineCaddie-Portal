<?php

namespace App\Repositories;

use App\Models\CustomerGroup;
use App\Repositories\Contracts\CustomerGroupRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerGroupRepository extends BaseRepository implements CustomerGroupRepositoryInterface
{
    protected string $modelClass = CustomerGroup::class;

    public function all(): Collection
    {
        return $this->query()->get();
    }
}
