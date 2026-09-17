<?php

use App\Support\CountryCache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();
        $countryColumns = Schema::getColumnListing('countries');
        $portColumns = Schema::getColumnListing('ports');

        $countryValues = array_filter([
            'name' => 'Panama',
            'iso3_code' => 'PAN',
            'currency' => 'PAB',
            'phone_code' => in_array('phone_code', $countryColumns, true) ? '+507' : null,
            'flag_url' => in_array('flag_url', $countryColumns, true) ? 'https://flagcdn.com/pa.svg' : null,
            'flag_emoji' => in_array('flag_emoji', $countryColumns, true) ? '🇵🇦' : null,
            'is_active' => 1,
            'updated_at' => in_array('updated_at', $countryColumns, true) ? $now : null,
        ], fn ($value) => $value !== null);

        // Production created_at defaults to 0000-00-00, which strict mode rejects.
        if (
            in_array('created_at', $countryColumns, true)
            && ! DB::table('countries')->where('iso_code', 'PA')->exists()
        ) {
            $countryValues['created_at'] = $now;
        }

        DB::table('countries')->updateOrInsert(['iso_code' => 'PA'], $countryValues);

        $countryId = DB::table('countries')->where('iso_code', 'PA')->value('id');

        $rows = [
            // Airports
            $this->portRow($portColumns, $now, 'airport', 'PTY', null, 'Tocumen International Airport', 'Panama City', $countryId, 'MPTO', 9.0713600, -79.3834500),
            $this->portRow($portColumns, $now, 'airport', 'PAC', null, 'Marcos A. Gelabert International Airport', 'Panama City', $countryId, 'MPMG', 8.9733400, -79.5555800),
            $this->portRow($portColumns, $now, 'airport', 'BLB', null, 'Panama Pacifico International Airport', 'Panama City', $countryId, 'MPPA', 8.9147900, -79.5996300),
            $this->portRow($portColumns, $now, 'airport', 'ONX', null, 'Enrique Adolfo Jimenez Airport', 'Colon', $countryId, 'MPEJ', 9.3566400, -79.8674300),
            $this->portRow($portColumns, $now, 'airport', 'DAV', null, 'Enrique Malek International Airport', 'David', $countryId, 'MPDA', 8.3910000, -82.4070000),
            $this->portRow($portColumns, $now, 'airport', 'RIH', null, 'Scarlett Martinez International Airport', 'Rio Hato', $countryId, 'MPSM', 8.3758800, -80.1278900),
            $this->portRow($portColumns, $now, 'airport', 'BOC', null, 'Bocas del Toro International Airport', 'Bocas del Toro', $countryId, 'MPBO', 9.3408500, -82.2508400),
            $this->portRow($portColumns, $now, 'airport', 'CTD', null, 'Alonso Valderrama Airport', 'Chitre', $countryId, 'MPCE', 7.9878400, -80.4097200),
            $this->portRow($portColumns, $now, 'airport', 'CHX', null, 'Cap Manuel Nino International Airport', 'Changuinola', $countryId, 'MPCH', 9.4586400, -82.5168100),
            $this->portRow($portColumns, $now, 'airport', 'SYP', null, 'Ruben Cantu Airport', 'Santiago', $countryId, 'MPSA', 8.0856000, -80.9453000),
            $this->portRow($portColumns, $now, 'airport', 'JQE', null, 'Jaque Airport', 'Jaque', $countryId, 'MPJE', 7.5177800, -78.1572200),
            $this->portRow($portColumns, $now, 'airport', 'AML', null, 'Puerto Armuelles Airport', 'Puerto Armuelles', $countryId, null, 8.2680600, -82.8647200),
            $this->portRow($portColumns, $now, 'airport', 'PLP', null, 'La Palma Airport', 'La Palma', $countryId, 'MPLP', 8.4066700, -78.1411100),
            $this->portRow($portColumns, $now, 'airport', 'PUE', null, 'Puerto Obaldia Airport', 'Puerto Obaldia', $countryId, 'MPOA', 8.6672200, -77.4172200),
            $this->portRow($portColumns, $now, 'airport', 'ELE', null, 'El Real Airport', 'El Real', $countryId, null, 8.1072200, -77.7288900),

            // Seaports
            $this->portRow($portColumns, $now, 'seaport', null, 'PABLB', 'Port of Balboa', 'Balboa', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAONX', 'Port of Colon', 'Colon', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PACTB', 'Port of Cristobal', 'Cristobal', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAPTY', 'Port of Panama City', 'Panama City', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAPAM', 'Port of Almirante', 'Almirante', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAMNP', 'Port of Bahia Las Minas', 'Las Minas', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAROD', 'Port of Rodman', 'Rodman', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAVAC', 'Port of Vacamonte', 'Vacamonte', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAAGD', 'Port of Aguadulce', 'Aguadulce', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PACHA', 'Port of Charco Azul', 'Charco Azul', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAAML', 'Port of Puerto Armuelles', 'Puerto Armuelles', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PABOC', 'Port of Bocas del Toro', 'Bocas del Toro', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PACHG', 'Port of Chiriqui Grande', 'Chiriqui Grande', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAPBE', 'Port of Portobelo', 'Portobelo', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PAPLP', 'Port of La Palma', 'La Palma', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'PACTD', 'Port of Chitre', 'Chitre', $countryId),
        ];

        foreach ($rows as $row) {
            if ($row === []) {
                continue;
            }

            $lookup = ['type' => $row['type']];

            if ($row['type'] === 'airport') {
                $lookup['iata_code'] = $row['iata_code'];
            } else {
                $lookup['un_locode'] = $row['un_locode'];
            }

            if (
                in_array('created_at', $portColumns, true)
                && ! DB::table('ports')->where($lookup)->exists()
            ) {
                $row['created_at'] = $now;
            }

            DB::table('ports')->updateOrInsert($lookup, $row);
        }

        CountryCache::flush();
    }

    public function down(): void
    {
        DB::table('ports')
            ->where('country_code', 'PA')
            ->where(function ($query) {
                $query->whereIn('iata_code', [
                    'PAC', 'BLB', 'ONX', 'DAV', 'RIH', 'BOC', 'CTD', 'CHX', 'SYP', 'AML', 'PLP', 'PUE', 'ELE',
                ])->orWhereIn('un_locode', [
                    'PABLB', 'PAONX', 'PACTB', 'PAPTY', 'PAPAM', 'PAMNP', 'PAROD', 'PAVAC',
                    'PAAGD', 'PACHA', 'PAAML', 'PABOC', 'PACHG', 'PAPBE', 'PAPLP', 'PACTD',
                ]);
            })
            ->delete();

        DB::table('ports')
            ->where('country_code', 'PA')
            ->whereIn('iata_code', ['PTY', 'JQE'])
            ->update(['country_id' => null]);

        DB::table('countries')->where('iso_code', 'PA')->delete();

        CountryCache::flush();
    }

    private function portRow(
        array $portColumns,
        Carbon $now,
        string $type,
        ?string $iataCode,
        ?string $unLocode,
        string $portName,
        string $city,
        ?int $countryId,
        ?string $icaoCode = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): array {
        $row = [
            'type' => $type,
            'iata_code' => $iataCode,
            'un_locode' => $unLocode,
            'port_name' => $portName,
            'city' => $city,
            'country_name' => 'Panama',
            'country_code' => 'PA',
            'country_id' => $countryId,
            'is_active' => 1,
            'updated_at' => $now,
        ];

        if (in_array('icao_code', $portColumns, true)) {
            $row['icao_code'] = $icaoCode;
        }

        if (in_array('flag', $portColumns, true)) {
            $row['flag'] = '🇵🇦';
        }

        if (in_array('latitude', $portColumns, true)) {
            $row['latitude'] = $latitude;
        }

        if (in_array('longitude', $portColumns, true)) {
            $row['longitude'] = $longitude;
        }

        return array_filter(
            $row,
            fn ($value, $key) => $value !== null || in_array($key, ['iata_code', 'un_locode'], true),
            ARRAY_FILTER_USE_BOTH
        );
    }
};
