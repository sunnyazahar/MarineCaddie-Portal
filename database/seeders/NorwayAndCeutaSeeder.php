<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class NorwayAndCeutaSeeder extends Seeder
{
    public function run(): void
    {
        $norway = Country::query()->updateOrCreate(
            ['iso_code' => 'NO'],
            [
                'name' => 'Norway',
                'iso3_code' => 'NOR',
                'currency' => 'NOK',
                'currency_value' => '10.5000',
                'phone_code' => '+47',
                'flag_url' => 'https://flagcdn.com/no.svg',
                'flag_emoji' => '🇳🇴',
                'is_active' => true,
            ]
        );

        $ceuta = Country::query()->updateOrCreate(
            ['iso_code' => 'XC'],
            [
                'name' => 'Ceuta',
                'iso3_code' => 'CEU',
                'currency' => 'EUR',
                'currency_value' => '0.8606',
                'phone_code' => '+34',
                'flag_url' => 'https://flagcdn.com/es.svg',
                'flag_emoji' => '🇪🇸',
                'is_active' => true,
            ]
        );

        Port::query()
            ->where('country_code', 'NO')
            ->update([
                'country_id' => $norway->id,
                'country_name' => 'Norway',
                'is_active' => true,
            ]);

        Port::query()
            ->where(function ($query) {
                $query->where('country_name', 'like', '%Svalbard%')
                    ->orWhere('iata_code', 'LYR');
            })
            ->update([
                'country_id' => $norway->id,
                'country_name' => 'Norway',
                'country_code' => 'NO',
                'is_active' => true,
            ]);

        Artisan::call('ports:import', [
            '--path' => database_path('data/norway_seaports.csv'),
        ]);

        if ($this->command) {
            $this->command->getOutput()->write(Artisan::output());
        }

        Port::query()->updateOrCreate(
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'ESCEU',
            ],
            [
                'iata_code' => null,
                'port_name' => 'Ceuta',
                'city' => 'Ceuta',
                'country_name' => 'Ceuta',
                'country_code' => 'XC',
                'country_id' => $ceuta->id,
                'is_active' => true,
            ]
        );

        Port::query()->updateOrCreate(
            [
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => 'JCU',
            ],
            [
                'un_locode' => null,
                'port_name' => 'Ceuta Heliport',
                'city' => 'Ceuta',
                'country_name' => 'Ceuta',
                'country_code' => 'XC',
                'country_id' => $ceuta->id,
                'is_active' => true,
            ]
        );

        $india = Country::query()->where('iso_code', 'IN')->first();

        Port::query()->updateOrCreate(
            [
                'type' => Port::TYPE_SEAPORT,
                'un_locode' => 'INNSA',
            ],
            [
                'iata_code' => null,
                'port_name' => 'Nhava Sheva (Navaseva / JNPT)',
                'city' => 'Nhava Sheva',
                'country_name' => 'India',
                'country_code' => 'IN',
                'country_id' => $india?->id,
                'is_active' => true,
            ]
        );

        Port::query()
            ->where('type', Port::TYPE_SEAPORT)
            ->where('un_locode', 'INNSA1')
            ->update([
                'port_name' => 'JNPT (Nhava Sheva / Navaseva)',
                'city' => 'Nhava Sheva',
                'country_name' => 'India',
                'country_code' => 'IN',
                'country_id' => $india?->id,
                'is_active' => true,
            ]);
    }
}
