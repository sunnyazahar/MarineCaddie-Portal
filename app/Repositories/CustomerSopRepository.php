<?php

namespace App\Repositories;

use App\Models\CustomerSop;
use App\Repositories\Contracts\CustomerSopRepositoryInterface;

class CustomerSopRepository extends BaseRepository implements CustomerSopRepositoryInterface
{
    protected string $modelClass = CustomerSop::class;

    public function create(array $data): CustomerSop
    {
        return parent::create($data);
    }

    public function updateOrCreate(array $attributes, array $values): CustomerSop
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }
}
