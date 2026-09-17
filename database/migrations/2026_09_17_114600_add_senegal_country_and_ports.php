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
            ['iso_code' => 'SN'],
            array_filter([
                'name' => 'Senegal',
                'iso3_code' => 'SEN',
                'currency' => 'XOF',
                'phone_code' => in_array('phone_code', $countryColumns, true) ? '+221' : null,
                'flag_url' => in_array('flag_url', $countryColumns, true) ? 'https://flagcdn.com/sn.svg' : null,
                'flag_emoji' => in_array('flag_emoji', $countryColumns, true) ? '🇸🇳' : null,
                'is_active' => 1,
                'updated_at' => in_array('updated_at', $countryColumns, true) ? $now : null,
                'created_at' => in_array('created_at', $countryColumns, true) ? DB::raw('COALESCE(created_at, NOW())') : null,
            ], fn ($value) => $value !== null)
        );

        $countryId = DB::table('countries')->where('iso_code', 'SN')->value('id');

        $rows = [
            $this->portRow($portColumns, $now, 'airport', 'DSS', null, 'Blaise Diagne International Airport', 'Diass', $countryId, 'GOBD', 14.6700330, -17.0738890),
            $this->portRow($portColumns, $now, 'airport', 'DKR', null, 'Dakar - Léopold Sédar Senghor International Airport', 'Dakar', $countryId, 'GOOY', 14.7397000, -17.4902250),
            $this->portRow($portColumns, $now, 'airport', 'ZIG', null, 'Ziguinchor Airport', 'Ziguinchor', $countryId, 'GOGG', 12.5556170, -16.2817830),
            $this->portRow($portColumns, $now, 'airport', 'CSK', null, 'Cap Skirring Airport', 'Cap Skirring', $countryId, 'GOGS', 12.3953300, -16.7480900),
            $this->portRow($portColumns, $now, 'airport', 'KLC', null, 'Kaolack Airport', 'Kaolack', $countryId, 'GOOK', 14.1468810, -16.0513000),
            $this->portRow($portColumns, $now, 'airport', 'KDA', null, 'Kolda North Airport', 'Kolda', $countryId, 'GODK', 12.8985300, -14.9681100),
            $this->portRow($portColumns, $now, 'airport', 'KGG', null, 'Kedougou Airport', 'Kedougou', $countryId, 'GOTK', 12.5722920, -12.2202780),
            $this->portRow($portColumns, $now, 'airport', 'MAX', null, 'Ouro Sogui Airport', 'Matam', $countryId, 'GOSM', 15.5936100, -13.3227800),
            $this->portRow($portColumns, $now, 'airport', 'POD', null, 'Podor Airport', 'Podor', $countryId, 'GOSP', 16.6780600, -14.9666700),
            $this->portRow($portColumns, $now, 'airport', 'RDT', null, 'Richard Toll Airport', 'Richard Toll', $countryId, 'GOSR', 16.4377800, -15.6558300),
            $this->portRow($portColumns, $now, 'airport', 'XLS', null, 'Saint-Louis Airport', 'Saint-Louis', $countryId, 'GOSS', 16.0507600, -16.4631700),
            $this->portRow($portColumns, $now, 'airport', 'TUD', null, 'Tambacounda Airport', 'Tambacounda', $countryId, 'GOTT', 13.7368100, -13.6531200),
            $this->portRow($portColumns, $now, 'airport', 'BXE', null, 'Bakel Airport', 'Bakel', $countryId, 'GOTB', 14.8472500, -12.4682600),
            $this->portRow($portColumns, $now, 'airport', 'SMY', null, 'Simenti Airport', 'Simenti', $countryId, 'GOTS', 13.0465300, -13.2956100),
            $this->portRow($portColumns, $now, 'airport', 'NIK', null, 'Niokolo-Koba Airport', 'Niokolo-Koba', $countryId, 'GOTN', 13.0500000, -12.7272000),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNDKR', 'Port of Dakar', 'Dakar', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNKLC', 'Port of Kaolack', 'Kaolack', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNZIG', 'Port of Ziguinchor', 'Ziguinchor', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNXLS', 'Port of Saint-Louis', 'Saint-Louis', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNFOU', 'Port of Foundiougne', 'Foundiougne', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNLYN', 'Port of Lyndiane', 'Lyndiane', $countryId),
            $this->portRow($portColumns, $now, 'seaport', null, 'SNMBA', "M'bao Terminal", 'Mbao', $countryId),
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
            ->where('country_code', 'SN')
            ->where(function ($query) {
                $query->whereIn('iata_code', ['DSS', 'ZIG', 'CSK', 'KLC', 'KDA', 'KGG', 'MAX', 'POD', 'RDT', 'XLS', 'TUD', 'BXE', 'SMY', 'NIK'])
                    ->orWhereIn('un_locode', ['SNDKR', 'SNKLC', 'SNZIG', 'SNXLS', 'SNFOU', 'SNLYN', 'SNMBA']);
            })
            ->delete();

        DB::table('ports')->where('type', 'airport')->where('iata_code', 'DKR')->update([
            'country_id' => null,
        ]);

        DB::table('countries')->where('iso_code', 'SN')->delete();

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
            'country_name' => 'Senegal',
            'country_code' => 'SN',
            'country_id' => $countryId,
            'is_active' => 1,
            'updated_at' => $now,
        ];

        if (in_array('icao_code', $portColumns, true)) {
            $row['icao_code'] = $icaoCode;
        }

        if (in_array('flag', $portColumns, true)) {
            $row['flag'] = '🇸🇳';
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
