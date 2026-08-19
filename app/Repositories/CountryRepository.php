<?php

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Support\Collection;

class CountryRepository implements CountryRepositoryInterface
{
    public function allForPortImport(): Collection
    {
        return Country::query()->get(['id', 'name', 'iso_code', 'flag_emoji']);
    }

    public function updateCurrencyValueByCode(string $currencyCode, float $rate): int
    {
        return Country::query()
            ->where('currency', $currencyCode)
            ->update(['currency_value' => $rate]);
    }

    public function currencyRatesByCode(): array
    {
        return Country::query()
            ->whereNotNull('currency')
            ->where('currency', '!=', '')
            ->whereNotNull('currency_value')
            ->get(['currency', 'currency_value'])
            ->groupBy(fn (Country $country) => strtoupper(trim((string) $country->currency)))
            ->map(fn ($rows) => (float) $rows->first()->currency_value)
            ->all();
    }
}
