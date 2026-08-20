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

        DB::table('countries')->updateOrInsert(
            ['iso_code' => 'MZ'],
            array_filter([
                'name' => 'Mozambique',
                'iso3_code' => 'MOZ',
                'currency' => 'MZN',
                'phone_code' => in_array('phone_code', $countryColumns, true) ? '+258' : null,
                'flag_url' => in_array('flag_url', $countryColumns, true) ? 'https://flagcdn.com/mz.svg' : null,
                'flag_emoji' => in_array('flag_emoji', $countryColumns, true) ? '🇲🇿' : null,
                'is_active' => 1,
                'updated_at' => in_array('updated_at', $countryColumns, true) ? $now : null,
                'created_at' => in_array('created_at', $countryColumns, true) ? DB::raw('COALESCE(created_at, NOW())') : null,
            ], fn ($value) => $value !== null)
        );

        $countryId = DB::table('countries')->where('iso_code', 'MZ')->value('id');

        $rows = [
            $this->portRow($portColumns, $now, 'airport', 'MPM', null, 'Maputo International Airport', 'Maputo', $countryId, 'FQMA', -25.9208300, 32.5725000),
            $this->portRow($portColumns, $now, 'airport', 'BEW', null, 'Beira Airport', 'Beira', $countryId, 'FQBR', -19.7963900, 34.9075000),
            $this->portRow($portColumns, $now, 'airport', 'MNC', null, 'Nacala Airport', 'Nacala', $countryId, 'FQNC', -14.4880600, 40.7122200),
            $this->portRow($portColumns, $now, 'airport', 'POL', null, 'Pemba Airport', 'Pemba', $countryId, 'FQPB', -12.9866700, 40.5222200),
            $this->portRow($portColumns, $now, 'airport', 'UEL', null, 'Quelimane Airport', 'Quelimane', $countryId, 'FQQL', -17.8552800, 36.8688900),
            $this->portRow($portColumns, $now, 'airport', 'TET', null, 'Tete Chingozi Airport', 'Tete', $countryId, 'FQTT', -16.1047200, 33.6400000),
            $this->portRow($portColumns, $now, 'airport', 'VNX', null, 'Vilankulo Airport', 'Vilankulo', $countryId, 'FQVL', -22.0183300, 35.3130600),
            $this->portRow($portColumns, $now, 'airport', 'VPY', null, 'Chimoio Airport', 'Chimoio', $countryId, 'FQCH', -19.1511100, 33.4288900),
            $this->portRow($portColumns, $now, 'airport', 'APL', null, 'Nampula Airport', 'Nampula', $countryId, 'FQNP', -15.1056100, 39.2818000),
            $this->portRow($portColumns, $now, 'airport', 'INH', null, 'Inhambane Airport', 'Inhambane', $countryId, 'FQIN', -23.8764300, 35.4085400),
            $this->portRow($portColumns, $now, 'airport', 'VXC', null, 'Lichinga Airport', 'Lichinga', $countryId, 'FQLC', -13.2739900, 35.2662600),
            $this->portRow($portColumns, $now, 'airport', 'VJB', null, 'Xai-Xai Airport', 'Xai-Xai', $countryId, 'FQXA', -25.0380600, 33.6263900),
            $this->portRow($portColumns, $now, 'seaport', null, 'MZMPM', 'Port of Maputo', 'Maputo', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'MZBEW', 'Port of Beira', 'Beira', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'MZMNC', 'Port of Nacala', 'Nacala', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'MZPOL', 'Port of Pemba', 'Pemba', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'MZUEL', 'Port of Quelimane', 'Quelimane', $countryId),
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

            DB::table('ports')->updateOrInsert($lookup, $row);
        }

        CountryCache::flush();
    }

    public function down(): void
    {
        DB::table('ports')
            ->where('country_code', 'MZ')
            ->where(function ($query) {
                $query->whereIn('iata_code', ['MPM', 'BEW', 'MNC', 'POL', 'UEL', 'TET', 'VNX', 'VPY', 'APL', 'INH', 'VXC', 'VJB'])
                    ->orWhereIn('un_locode', ['MZMPM', 'MZBEW', 'MZMNC', 'MZPOL', 'MZUEL']);
            })
            ->delete();

        DB::table('countries')->where('iso_code', 'MZ')->delete();

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
            'country_name' => 'Mozambique',
            'country_code' => 'MZ',
            'country_id' => $countryId,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (in_array('icao_code', $portColumns, true)) {
            $row['icao_code'] = $icaoCode;
        }

        if (in_array('flag', $portColumns, true)) {
            $row['flag'] = '🇲🇿';
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
