<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserNotificationService
{
    public function notify(
        User|int $user,
        string $message,
        string $category = UserNotification::CATEGORY_OTHER,
        ?string $linkLabel = null,
        ?string $linkUrl = null,
        ?string $icon = null,
        ?Model $related = null,
        ?\DateTimeInterface $occurredAt = null,
    ): UserNotification {
        $userId = $user instanceof User ? $user->id : $user;

        return UserNotification::create([
            'user_id' => $userId,
            'category' => $category,
            'message' => $message,
            'link_label' => $linkLabel,
            'link_url' => $this->normalizeUrl($linkUrl),
            'icon' => $icon ?? $this->iconForCategory($category),
            'is_read' => false,
            'related_type' => $related?->getMorphClass(),
            'related_id' => $related?->getKey(),
            'occurred_at' => $occurredAt ?? now(),
        ]);
    }

    /**
     * @param  iterable<int|User>  $users
     */
    public function notifyMany(
        iterable $users,
        string $message,
        string $category = UserNotification::CATEGORY_OTHER,
        ?string $linkLabel = null,
        ?string $linkUrl = null,
        ?string $icon = null,
        ?Model $related = null,
    ): void {
        foreach ($users as $user) {
            if ($user === null) {
                continue;
            }
            $this->notify($user, $message, $category, $linkLabel, $linkUrl, $icon, $related);
        }
    }

    public function forUser(User $user, ?string $category = null, int $limit = 20, int $offset = 0): Collection
    {
        $query = UserNotification::query()
            ->where('user_id', $user->id)
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
            ->orderByDesc('id');

        return $query->skip($offset)->take($limit)->get();
    }

    public function countsForUser(User $user): array
    {
        $base = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false);

        $unreadTotal = (clone $base)->count();

        $byCategory = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->selectRaw('category, COUNT(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category')
            ->all();

        return [
            'all' => $unreadTotal,
            'comments' => (int) ($byCategory[UserNotification::CATEGORY_COMMENTS] ?? 0),
            'pickups' => (int) ($byCategory[UserNotification::CATEGORY_PICKUPS] ?? 0),
            'costs' => (int) ($byCategory[UserNotification::CATEGORY_COSTS] ?? 0),
            'other' => (int) ($byCategory[UserNotification::CATEGORY_OTHER] ?? 0),
        ];
    }

    public function markAllRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function markRead(User $user, int $notificationId): ?UserNotification
    {
        $notification = UserNotification::query()
            ->where('user_id', $user->id)
            ->find($notificationId);

        if ($notification && ! $notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return $notification;
    }

    public function toArray(UserNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'category' => $notification->category,
            'message' => $notification->message,
            'link_label' => $notification->link_label,
            'link_url' => $this->normalizeUrl($notification->link_url),
            'icon' => $notification->icon,
            'is_read' => (bool) $notification->is_read,
            'time' => $notification->displayTime(),
        ];
    }

    private function normalizeUrl(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '//')) {
            return $url;
        }

        // Ensure app base path (/laravel, /public, etc.) is included.
        return url($url);
    }

    private function iconForCategory(string $category): string
    {
        return match ($category) {
            UserNotification::CATEGORY_PICKUPS => 'pickup',
            UserNotification::CATEGORY_COSTS => 'cost',
            UserNotification::CATEGORY_COMMENTS => 'comment',
            default => 'other',
        };
    }
}
