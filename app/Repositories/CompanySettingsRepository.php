<?php

namespace App\Repositories;

use App\Models\CompanySetting;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompanySettingsRepository extends BaseRepository implements CompanySettingsRepositoryInterface
{
    private const CACHE_KEY = 'company_settings.v1';

    protected string $modelClass = CompanySetting::class;

    private bool $loaded = false;

    private ?CompanySetting $memo = null;

    public function current(): ?CompanySetting
    {
        if ($this->loaded) {
            return $this->memo;
        }

        $this->loaded = true;

        try {
            $attributes = Cache::rememberForever(self::CACHE_KEY, function (): ?array {
                return $this->query()->orderBy('id')->first()?->getAttributes();
            });
        } catch (Throwable $e) {
            // Not installed yet (no DB / table / cache store): callers fall back to defaults.
            Log::debug('Company settings unavailable: ' . $e->getMessage());
            $attributes = null;
        }

        $this->memo = $attributes === null
            ? null
            : (new CompanySetting())->newFromBuilder($attributes);

        return $this->memo;
    }

    public function save(array $attributes): CompanySetting
    {
        $setting = $this->query()->orderBy('id')->first() ?? new CompanySetting();
        $setting->fill($attributes)->save();

        $this->flush();

        return $setting;
    }

    public function flush(): void
    {
        $this->loaded = false;
        $this->memo = null;

        try {
            Cache::forget(self::CACHE_KEY);
        } catch (Throwable) {
            // Cache store not ready (installer runs before the cache table exists).
        }
    }
}
