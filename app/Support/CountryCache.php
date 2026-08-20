<?php

namespace App\Support;

use App\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Cached country lookups.
 *
 * Cache keys include the active-country count so new countries appear on every
 * server without a manual cache clear. Call CountryCache::flush() after updates
 * that do not change the active count (e.g. rename).
 */
class CountryCache
{
    private const TTL = 3600; // 1 hour

    /** All active countries ordered by name (Eloquent collection). */
    public static function active(): Collection
    {
        $count = self::activeCount();

        return Cache::remember("countries_active_{$count}", self::TTL, fn () =>
            Country::where('is_active', true)->orderBy('name')->get()
        );
    }

    /** Distinct non-null currencies from active countries, sorted. */
    public static function currencies(): Collection
    {
        $count = self::activeCount();

        return Cache::remember("countries_currencies_{$count}", self::TTL, fn () =>
            Country::where('is_active', true)
                ->whereNotNull('currency')
                ->distinct()
                ->orderBy('currency')
                ->pluck('currency')
                ->values()
        );
    }

    /** Active countries as plain DB rows (for controllers using DB::table). */
    public static function activeRaw(): Collection
    {
        $count = self::activeCount();

        return Cache::remember("countries_active_raw_{$count}", self::TTL, fn () =>
            DB::table('countries')->where('is_active', 1)->orderBy('name')->get()
        );
    }

    /** Flush all country caches (call after create/update/delete a country). */
    public static function flush(): void
    {
        foreach (['countries_active', 'countries_currencies', 'countries_active_raw'] as $legacyKey) {
            Cache::forget($legacyKey);
        }

        $count = self::activeCount();
        Cache::forget("countries_active_{$count}");
        Cache::forget("countries_currencies_{$count}");
        Cache::forget("countries_active_raw_{$count}");
    }

    private static function activeCount(): int
    {
        return (int) DB::table('countries')->where('is_active', 1)->count();
    }
}
