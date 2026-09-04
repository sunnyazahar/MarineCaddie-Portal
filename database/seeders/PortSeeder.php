<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $countriesByIso = Country::query()
            ->get(['id', 'name', 'iso_code', 'flag_emoji'])
            ->keyBy(fn (Country $country) => strtoupper($country->iso_code));

        $rows = array_merge($this->sampleAirports(), $this->sampleSeaports());

        foreach ($rows as $row) {
            $country = $countriesByIso->get(strtoupper($row['country_code']));

            $attributes = [
                'icao_code' => $row['icao_code'] ?? null,
                'port_name' => $row['port_name'],
                'city' => $row['city'] ?? null,
                'country_name' => $row['country_name'],
                'country_code' => strtoupper($row['country_code']),
                'flag' => $row['flag'] ?? $country?->flag_emoji,
                'country_id' => $country?->id,
                'latitude' => $row['latitude'] ?? null,
                'longitude' => $row['longitude'] ?? null,
                'is_active' => true,
            ];

            if ($row['type'] === Port::TYPE_AIRPORT) {
                Port::updateOrCreate(
                    ['type' => Port::TYPE_AIRPORT, 'iata_code' => $row['iata_code']],
                    $attributes
                );
                continue;
            }

            Port::updateOrCreate(
                ['type' => Port::TYPE_SEAPORT, 'un_locode' => $row['un_locode']],
                $attributes
            );
        }
    }

    private function sampleAirports(): array
    {
        return [
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'DEL',
                'icao_code' => 'VIDP',
                'port_name' => 'Indira Gandhi International Airport',
                'city' => 'Delhi',
                'country_name' => 'India',
                'country_code' => 'IN',
                'flag' => '🇮🇳',
                'latitude' => 28.5562000,
                'longitude' => 77.1000000,
            ],
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'BOM',
                'icao_code' => 'VABB',
                'port_name' => 'Chhatrapati Shivaji Maharaj International Airport',
                'city' => 'Mumbai',
                'country_name' => 'India',
                'country_code' => 'IN',
                'flag' => '🇮🇳',
                'latitude' => 19.0887000,
                'longitude' => 72.8679000,
            ],
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'LHR',
                'icao_code' => 'EGLL',
                'port_name' => 'Heathrow Airport',
                'city' => 'London',
                'country_name' => 'United Kingdom',
                'country_code' => 'GB',
                'flag' => '🇬🇧',
                'latitude' => 51.4706000,
                'longitude' => -0.4619410,
            ],
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'JFK',
                'icao_code' => 'KJFK',
                'port_name' => 'John F. Kennedy International Airport',
                'city' => 'New York',
                'country_name' => 'United States',
                'country_code' => 'US',
                'flag' => '🇺🇸',
                'latitude' => 40.6413000,
                'longitude' => -73.7781000,
            ],
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'DXB',
                'icao_code' => 'OMDB',
                'port_name' => 'Dubai International Airport',
                'city' => 'Dubai',
                'country_name' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'latitude' => 25.2532000,
                'longitude' => 55.3657000,
            ],
        ];
    }

    private function sampleSeaports(): array
    {
        return [
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'INNSA',
                'port_name' => 'Nhava Sheva (Navaseva / JNPT)',
                'city' => 'Nhava Sheva',
                'country_name' => 'India',
                'country_code' => 'IN',
                'flag' => '🇮🇳',
            ],
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'SGSIN',
                'port_name' => 'Port of Singapore',
                'city' => 'Singapore',
                'country_name' => 'Singapore',
                'country_code' => 'SG',
                'flag' => '🇸🇬',
            ],
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'NLRTM',
                'port_name' => 'Port of Rotterdam',
                'city' => 'Rotterdam',
                'country_name' => 'Netherlands',
                'country_code' => 'NL',
                'flag' => '🇳🇱',
            ],
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'GBFXT',
                'port_name' => 'Port of Felixstowe',
                'city' => 'Felixstowe',
                'country_name' => 'United Kingdom',
                'country_code' => 'GB',
                'flag' => '🇬🇧',
            ],
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'USNYC',
                'port_name' => 'Port of New York and New Jersey',
                'city' => 'New York',
                'country_name' => 'United States',
                'country_code' => 'US',
                'flag' => '🇺🇸',
            ],
        ];
    }
}
