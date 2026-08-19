<?php

namespace App\Repositories;

use App\Models\Port;
use App\Repositories\Contracts\PortRepositoryInterface;

class PortRepository implements PortRepositoryInterface
{
    public function deleteByType(string $type): int
    {
        return Port::query()->where('type', $type)->delete();
    }

    public function firstOrNewAirportByIata(string $iataCode): Port
    {
        return Port::query()->firstOrNew([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => $iataCode,
        ]);
    }

    public function upsertSeaports(array $rows): void
    {
        Port::query()->upsert(
            $rows,
            ['type', 'un_locode'],
            ['port_name', 'city', 'country_name', 'country_code', 'country_id', 'is_active', 'updated_at']
        );
    }

    public function countAirports(): int
    {
        return Port::airports()->count();
    }

    public function countSeaports(): int
    {
        return Port::seaports()->count();
    }

    public function findByCodeWithCountry(string $code): ?Port
    {
        return Port::query()
            ->with('country')
            ->where(function ($query) use ($code) {
                $query->where('iata_code', $code)
                    ->orWhere('un_locode', $code)
                    ->orWhere('port_name', $code);
            })
            ->first();
    }

    public function findByUnLocodePrefixWithCountry(string $prefix): ?Port
    {
        return Port::query()
            ->with('country')
            ->where('un_locode', 'like', $prefix . '%')
            ->first();
    }
}
