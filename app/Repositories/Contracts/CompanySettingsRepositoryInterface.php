<?php

namespace App\Repositories\Contracts;

use App\Models\CompanySetting;

interface CompanySettingsRepositoryInterface
{
    /** The single settings row, or null before install / when the table is unavailable. */
    public function current(): ?CompanySetting;

    /** Create or update the single settings row and refresh the cache. */
    public function save(array $attributes): CompanySetting;

    public function flush(): void;
}
