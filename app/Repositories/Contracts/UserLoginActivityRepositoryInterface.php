<?php

namespace App\Repositories\Contracts;

use App\Models\UserLoginActivity;

interface UserLoginActivityRepositoryInterface
{
    public function closeOpenSessionsForUser(int $userId): int;

    public function create(array $attributes): UserLoginActivity;
}
