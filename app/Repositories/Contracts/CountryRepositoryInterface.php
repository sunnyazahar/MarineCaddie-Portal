<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CountryRepositoryInterface
{
    public function allForPortImport(): Collection;

    public function updateCurrencyValueByCode(string $currencyCode, float $rate): int;

    /**
     * @return array<string, float>
     */
    public function currencyRatesByCode(): array;
}
