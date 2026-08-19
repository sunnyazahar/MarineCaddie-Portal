<?php

namespace App\Repositories;

use App\Models\AgentUser;
use App\Repositories\Contracts\AgentUserRepositoryInterface;

class AgentUserRepository extends BaseRepository implements AgentUserRepositoryInterface
{
    protected string $modelClass = AgentUser::class;

    public function findOrFail(int $id, array $with = []): AgentUser
    {
        return parent::findModelOrFail($id, $with);
    }

    public function create(array $data): AgentUser
    {
        return parent::create($data);
    }

    public function update(AgentUser $user, array $data): bool
    {
        return parent::updateModel($user, $data);
    }

    public function deleteById(int $id): bool
    {
        return parent::deleteById($id);
    }
}
