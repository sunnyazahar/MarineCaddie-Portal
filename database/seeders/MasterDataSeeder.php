<?php

namespace Database\Seeders;

use App\Support\CountryCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Lookup data every install needs (countries + currencies, airport and seaport codes).
 * Safe to re-run: existing countries are left untouched and ports are upserted.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCountries();
        $this->seedPorts();

        CountryCache::flush();
    }

    private function seedCountries(): void
    {
        $now = now();
        $rows = array_map(
            fn (array $country) => $country + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            require database_path('data/countries.php')
        );

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('countries')->insertOrIgnore($chunk);
        }
    }

    private function seedPorts(): void
    {
        Artisan::call('ports:import', ['--path' => database_path('data/port_codes.csv')]);
        $this->command?->getOutput()->write(Artisan::output());

        $this->call(NorwayAndCeutaSeeder::class);
    }
}
