<?php

namespace App\Repositories;

use App\Models\CustomerAddress;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;

class CustomerAddressRepository extends BaseRepository implements CustomerAddressRepositoryInterface
{
    protected string $modelClass = CustomerAddress::class;

    public function create(array $data): CustomerAddress
    {
        return parent::create($data);
    }

    public function updateOrCreate(array $attributes, array $values): CustomerAddress
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }
}
