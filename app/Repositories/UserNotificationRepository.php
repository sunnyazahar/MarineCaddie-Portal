<?php

namespace App\Repositories;

use App\Models\UserNotification;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class UserNotificationRepository implements UserNotificationRepositoryInterface
{
    public function create(array $attributes): UserNotification
    {
        return UserNotification::create($attributes);
    }

    public function forUser(int $userId, ?string $category, int $limit, int $offset): Collection
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->when($category && $category !== 'all', function ($q) use ($category) {
                if ($category === UserNotification::CATEGORY_OTHER) {
                    $q->whereIn('category', [
                        UserNotification::CATEGORY_OTHER,
                        UserNotification::CATEGORY_COSTS,
                    ]);
                } else {
                    $q->where('category', $category);
                }
            })
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function unreadCategoryCountsForUser(int $userId): array
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->selectRaw('category, COUNT(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    public function markAllRead(int $userId): int
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function findForUser(int $userId, int $notificationId): ?UserNotification
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->find($notificationId);
    }
}
