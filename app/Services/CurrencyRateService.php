<?php

namespace App\Services;

use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CurrencyRateService
{
    public function __construct(
        private CountryRepositoryInterface $countries,
    ) {}

    /**
     * @return array{updated: int, last_update: ?string}
     */
    public function updateFromUsd(): array
    {
        $response = Http::timeout(20)->acceptJson()->get('https://open.er-api.com/v6/latest/USD');

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch exchange rates from the API.');
        }

        $data = $response->json();

        if (($data['result'] ?? '') !== 'success' || ! is_array($data['rates'] ?? null)) {
            throw new RuntimeException('API returned an unsuccessful result.');
        }

        $updatedCount = 0;

        foreach ($data['rates'] as $currencyCode => $rate) {
            $updated = $this->countries->updateCurrencyValueByCode((string) $currencyCode, (float) $rate);

            if ($updated) {
                $updatedCount++;
            }
        }

        $lastUpdate = $data['time_last_update_utc'] ?? null;

        Log::info('Currency rates updated.', [
            'updated' => $updatedCount,
            'last_update' => $lastUpdate,
        ]);

        return [
            'updated' => $updatedCount,
            'last_update' => $lastUpdate,
        ];
    }
}
