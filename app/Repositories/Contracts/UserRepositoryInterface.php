<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function paginateForAdmin(array $filters, int $perPage): LengthAwarePaginator;

    public function assignmentOptions(): array;

    public function createUser(array $attributes): User;

    public function usersForChangeLog(): Collection;

    public function notificationRecipientsForAgent(int $agentId, ?int $excludeUserId = null): Collection;
}
