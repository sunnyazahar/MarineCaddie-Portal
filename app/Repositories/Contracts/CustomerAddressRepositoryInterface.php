<?php

namespace App\Repositories\Contracts;

interface CustomerAddressRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerAddress;

    public function updateOrCreate(array $attributes, array $values): \App\Models\CustomerAddress;
}
