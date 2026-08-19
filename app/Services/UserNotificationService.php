<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserNotificationService
{
    public function __construct(
        private UserNotificationRepositoryInterface $notifications,
    ) {}

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

        return $this->notifications->create([
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
        return $this->notifications->forUser($user->id, $category, $limit, $offset);
    }

    public function countsForUser(User $user): array
    {
        $byCategory = $this->notifications->unreadCategoryCountsForUser($user->id);
        $unreadTotal = array_sum($byCategory);

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
        return $this->notifications->markAllRead($user->id);
    }

    public function markRead(User $user, int $notificationId): ?UserNotification
    {
        $notification = $this->notifications->findForUser($user->id, $notificationId);

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
