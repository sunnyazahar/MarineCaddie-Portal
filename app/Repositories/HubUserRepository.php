<?php

namespace App\Repositories;

use App\Models\HubUser;
use App\Repositories\Contracts\HubUserRepositoryInterface;

class HubUserRepository extends BaseRepository implements HubUserRepositoryInterface
{
    protected string $modelClass = HubUser::class;

    public function findOrFail(int $id): HubUser
    {
        return parent::findModelOrFail($id);
    }

    public function update(HubUser $hubUser, array $data): bool
    {
        return parent::updateModel($hubUser, $data);
    }
}
