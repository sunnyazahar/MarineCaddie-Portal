<?php

namespace App\Repositories\Contracts;

use App\Models\Port;

interface PortRepositoryInterface
{
    public function deleteByType(string $type): int;

    public function firstOrNewAirportByIata(string $iataCode): Port;

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function upsertSeaports(array $rows): void;

    public function countAirports(): int;

    public function countSeaports(): int;

    public function findByCodeWithCountry(string $code): ?Port;

    public function findByUnLocodePrefixWithCountry(string $prefix): ?Port;
}
