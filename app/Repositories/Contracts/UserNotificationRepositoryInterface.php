<?php

namespace App\Repositories\Contracts;

use App\Models\UserNotification;
use Illuminate\Support\Collection;

interface UserNotificationRepositoryInterface
{
    public function create(array $attributes): UserNotification;

    public function forUser(int $userId, ?string $category, int $limit, int $offset): Collection;

    /**
     * @return array<string, int>
     */
    public function unreadCategoryCountsForUser(int $userId): array;

    public function markAllRead(int $userId): int;

    public function findForUser(int $userId, int $notificationId): ?UserNotification;
}
