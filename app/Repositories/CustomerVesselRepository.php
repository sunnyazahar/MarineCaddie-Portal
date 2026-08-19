<?php

namespace App\Repositories;

use App\Models\CustomerVessel;
use App\Repositories\Contracts\CustomerVesselRepositoryInterface;

class CustomerVesselRepository extends BaseRepository implements CustomerVesselRepositoryInterface
{
    protected string $modelClass = CustomerVessel::class;

    public function create(array $data): CustomerVessel
    {
        return parent::create($data);
    }

    public function findOrFail(int $id, array $with = []): CustomerVessel
    {
        return parent::findModelOrFail($id, $with);
    }

    public function update(CustomerVessel $vessel, array $data): bool
    {
        return parent::updateModel($vessel, $data);
    }
}
