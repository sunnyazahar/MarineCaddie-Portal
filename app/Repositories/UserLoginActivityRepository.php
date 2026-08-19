<?php

namespace App\Repositories;

use App\Models\UserLoginActivity;
use App\Repositories\Contracts\UserLoginActivityRepositoryInterface;

class UserLoginActivityRepository implements UserLoginActivityRepositoryInterface
{
    public function closeOpenSessionsForUser(int $userId): int
    {
        return UserLoginActivity::query()
            ->where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->update(['logged_out_at' => now()]);
    }

    public function create(array $attributes): UserLoginActivity
    {
        return UserLoginActivity::create($attributes);
    }
}
