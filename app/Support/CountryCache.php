<?php

namespace App\Support;

use App\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Cached country lookups.
 *
 * Countries change very rarely — cache for 1 hour.
 * Call CountryCache::flush() after any country update to invalidate.
 */
class CountryCache
{
    private const TTL = 3600; // 1 hour

    /** All active countries ordered by name (Eloquent collection). */
    public static function active(): Collection
    {
        return Cache::remember('countries_active', self::TTL, fn () =>
            Country::where('is_active', true)->orderBy('name')->get()
        );
    }

    /** Distinct non-null currencies from active countries, sorted. */
    public static function currencies(): Collection
    {
        return Cache::remember('countries_currencies', self::TTL, fn () =>
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
        return Cache::remember('countries_active_raw', self::TTL, fn () =>
            DB::table('countries')->where('is_active', 1)->orderBy('name')->get()
        );
    }

    /** Flush all country caches (call after create/update/delete a country). */
    public static function flush(): void
    {
        Cache::forget('countries_active');
        Cache::forget('countries_currencies');
        Cache::forget('countries_active_raw');
    }
}
