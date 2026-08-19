<?php

namespace App\Repositories\Contracts;

interface CustomerVesselRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerVessel;

    public function findOrFail(int $id, array $with = []): \App\Models\CustomerVessel;

    public function update(\App\Models\CustomerVessel $vessel, array $data): bool;
}
