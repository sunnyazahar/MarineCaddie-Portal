<?php

namespace App\Repositories\Contracts;

interface AgentUserRepositoryInterface
{
    public function findOrFail(int $id, array $with = []): \App\Models\AgentUser;

    public function create(array $data): \App\Models\AgentUser;

    public function update(\App\Models\AgentUser $user, array $data): bool;

    public function deleteById(int $id): bool;
}
