<?php

namespace App\Repositories\Contracts;

interface HubUserRepositoryInterface
{
    public function findOrFail(int $id): \App\Models\HubUser;

    public function update(\App\Models\HubUser $hubUser, array $data): bool;
}
