<?php

namespace App\Repositories;

use App\Models\CustomerResponsible;
use App\Repositories\Contracts\CustomerResponsibleRepositoryInterface;

class CustomerResponsibleRepository extends BaseRepository implements CustomerResponsibleRepositoryInterface
{
    protected string $modelClass = CustomerResponsible::class;

    public function create(array $data): CustomerResponsible
    {
        return parent::create($data);
    }

    public function updateOrCreate(array $attributes, array $values): CustomerResponsible
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }
}
