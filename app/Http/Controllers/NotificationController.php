<?php

namespace App\Http\Controllers;

use App\Services\UserNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, UserNotificationService $notifications): JsonResponse
    {
        $user = $request->user();
        $category = $request->query('category', 'all');
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 15)));

        $items = $notifications->forUser($user, $category, $limit, $offset)
            ->map(fn ($n) => $notifications->toArray($n))
            ->values();

        return response()->json([
            'success' => true,
            'notifications' => $items,
            'counts' => $notifications->countsForUser($user),
            'has_more' => $items->count() === $limit,
        ]);
    }

    public function markAllRead(Request $request, UserNotificationService $notifications): JsonResponse
    {
        $notifications->markAllRead($request->user());

        return response()->json([
            'success' => true,
            'counts' => $notifications->countsForUser($request->user()),
        ]);
    }

    public function markRead(Request $request, int $id, UserNotificationService $notifications): JsonResponse
    {
        $item = $notifications->markRead($request->user(), $id);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'notification' => $notifications->toArray($item),
            'counts' => $notifications->countsForUser($request->user()),
        ]);
    }
}
