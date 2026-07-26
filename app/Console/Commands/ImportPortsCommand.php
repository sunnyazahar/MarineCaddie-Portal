<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportPortsCommand extends Command
{
    protected $signature = 'ports:import
                            {--path= : CSV path (default: database/data/port_codes.csv)}
                            {--fresh : Truncate ports table before import}';

    protected $description = 'Import airport IATA codes from port_codes CSV into ports, mapped to countries';

    public function handle(): int
    {
        $path = $this->option('path')
            ?: database_path('data/port_codes.csv');

        if (! is_readable($path)) {
            $this->error("CSV not readable: {$path}");

            return self::FAILURE;
        }

        $countries = Country::query()
            ->get(['id', 'name', 'iso_code', 'flag_emoji']);

        $byIso = $countries->keyBy(fn (Country $c) => strtoupper((string) $c->iso_code));
        $byName = $countries->keyBy(fn (Country $c) => $this->normalizeName($c->name));

        $aliases = $this->countryAliases();

        if ($this->option('fresh')) {
            DB::table('ports')->delete();
            $this->warn('Cleared existing ports rows.');
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error("Could not open: {$path}");

            return self::FAILURE;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->error('Empty CSV.');

            return self::FAILURE;
        }

        $header = array_map(fn ($h) => Str::of((string) $h)->trim()->lower()->toString(), $header);
        $index = array_flip($header);

        foreach (['iata_code', 'port_name', 'country_name'] as $required) {
            if (! isset($index[$required])) {
                fclose($handle);
                $this->error("CSV missing column: {$required}");

                return self::FAILURE;
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $unmappedCountries = [];
        $withoutCountryId = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $iata = strtoupper(trim((string) ($row[$index['iata_code']] ?? '')));
            $portName = trim((string) ($row[$index['port_name']] ?? ''));
            $excelCountry = trim((string) ($row[$index['country_name']] ?? ''));

            if (! preg_match('/^[A-Z]{3}$/', $iata) || $portName === '' || $excelCountry === '') {
                $skipped++;
                continue;
            }

            [$country, $countryCode, $countryName] = $this->resolveCountry(
                $excelCountry,
                $aliases,
                $byIso,
                $byName
            );

            if ($countryCode === null) {
                $unmappedCountries[$excelCountry] = ($unmappedCountries[$excelCountry] ?? 0) + 1;
                $skipped++;
                continue;
            }

            if ($country === null) {
                $withoutCountryId++;
            }

            $attributes = [
                'icao_code' => null,
                'un_locode' => null,
                'port_name' => $portName,
                'city' => $this->extractCity($portName),
                'country_name' => $country?->name ?? $countryName,
                'country_code' => $countryCode,
                'flag' => $country?->flag_emoji,
                'country_id' => $country?->id,
                'is_active' => true,
            ];

            $port = Port::query()->firstOrNew([
                'type' => Port::TYPE_AIRPORT,
                'iata_code' => $iata,
            ]);

            $wasExisting = $port->exists;
            $port->fill($attributes)->save();

            if ($wasExisting) {
                $updated++;
            } else {
                $created++;
            }
        }

        fclose($handle);

        $this->info("Import finished.");
        $this->line("Created: {$created}");
        $this->line("Updated: {$updated}");
        $this->line("Skipped: {$skipped}");
        $this->line("Rows without countries.country_id (ISO resolved, no DB match): {$withoutCountryId}");
        $this->line('Total airports in DB: '.Port::airports()->count());

        if ($unmappedCountries !== []) {
            arsort($unmappedCountries);
            $this->warn('Unmapped Excel country names (skipped):');
            foreach ($unmappedCountries as $name => $count) {
                $this->line("  {$name}: {$count}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Country>  $byIso
     * @param  \Illuminate\Support\Collection<string, Country>  $byName
     * @return array{0: ?Country, 1: ?string, 2: string}
     */
    private function resolveCountry(
        string $excelCountry,
        array $aliases,
        $byIso,
        $byName
    ): array {
        $normalized = $this->normalizeName($excelCountry);

        if (isset($aliases[$normalized])) {
            $iso = strtoupper($aliases[$normalized]);
            $country = $byIso->get($iso);

            return [$country, $iso, $country?->name ?? $excelCountry];
        }

        if ($byName->has($normalized)) {
            $country = $byName->get($normalized);

            return [$country, strtoupper((string) $country->iso_code), $country->name];
        }

        return [null, null, $excelCountry];
    }

    private function normalizeName(string $name): string
    {
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = str_replace(["\xc2\xa0", '’', '`'], [' ', "'", "'"], $name);
        $name = preg_replace('/\s+/u', ' ', trim($name)) ?? trim($name);

        return Str::lower($name);
    }

    private function extractCity(string $portName): ?string
    {
        if (str_contains($portName, ' - ')) {
            return trim(explode(' - ', $portName, 2)[0]) ?: null;
        }

        if (preg_match('/^(.+?)\s*\([^)]+\)\s*$/u', $portName, $m)) {
            return trim($m[1]) ?: null;
        }

        return $portName !== '' ? $portName : null;
    }

    /**
     * Excel country label => ISO 3166-1 alpha-2.
     * Used when the label does not exactly match countries.name.
     *
     * @return array<string, string>
     */
    private function countryAliases(): array
    {
        return [
            'usa' => 'US',
            'usa (la)' => 'US',
            'united states' => 'US',
            'us minor outlying islands' => 'UM',
            'virgin islands (u.s.)' => 'VI',
            'puerto rico' => 'PR',
            'guam' => 'GU',
            'american samoa' => 'AS',
            'northern mariana islands' => 'MP',

            'united kingdom' => 'GB',
            'scotland, uk' => 'GB',
            'channel islands' => 'GB',

            'korea south' => 'KR',
            'south korea' => 'KR',
            'north korea' => 'KP',

            'viet nam' => 'VN',
            'vietnam' => 'VN',

            "cote d'ivoire" => 'CI',
            "côte d'ivoire" => 'CI',

            'russia' => 'RU',
            'iran' => 'IR',
            'syria' => 'SY',
            'laos' => 'LA',
            'lao pdr' => 'LA',
            'brunei' => 'BN',
            'bolivia' => 'BO',
            'venezuela' => 'VE',
            'tanzania' => 'TZ',
            'moldova' => 'MD',
            'macedonia' => 'MK',
            'taiwan' => 'TW',
            'hong kong' => 'HK',
            'macau, china sar' => 'MO',
            'palestinian territory' => 'PS',
            'reunion' => 'RE',
            'mayotte' => 'YT',
            'greenland' => 'GL',
            'faroe islands' => 'FO',
            'gibraltar' => 'GI',
            'bermuda' => 'BM',
            'cayman islands' => 'KY',
            'british virgin islands' => 'VG',
            'virgin islands (british)' => 'VG',
            'anguilla' => 'AI',
            'montserrat' => 'MS',
            'turks and caicos islands' => 'TC',
            'falkland islands' => 'FK',
            'french guiana' => 'GF',
            'guadeloupe' => 'GP',
            'martinique' => 'MQ',
            'st. martin (guadeloupe)' => 'MF',
            'new caledonia' => 'NC',
            'french polynesia' => 'PF',
            'french polynesia (tahiti)' => 'PF',
            'wallis and futuna islands' => 'WF',
            'aruba' => 'AW',
            'curacao' => 'CW',
            'netherlands antilles' => 'AN',
            'cook island' => 'CK',
            'cook islands' => 'CK',
            'samoa' => 'WS',
            'american samoa' => 'AS',
            'fiji' => 'FJ',
            'fiji/suva' => 'FJ',
            'papua new guinea' => 'PG',
            'solomon islands' => 'SB',
            'vanuatu' => 'VU',
            'tonga' => 'TO',
            'kiribati' => 'KI',
            'marshall islands' => 'MH',
            'micronesia' => 'FM',
            'palau' => 'PW',
            'timor leste (east timor)' => 'TL',
            'east timor' => 'TL',
            'myanmar' => 'MM',
            'cambodia' => 'KH',
            'mongolia' => 'MN',
            'kazakhstan' => 'KZ',
            'uzbekistan' => 'UZ',
            'turkmenistan' => 'TM',
            'tajikistan' => 'TJ',
            'kyrgyzstan' => 'KG',
            'georgia' => 'GE',
            'armenia' => 'AM',
            'azerbaijan' => 'AZ',
            'belarus' => 'BY',
            'ukraine' => 'UA',
            'moldova' => 'MD',
            'serbia' => 'RS',
            'montenegro' => 'ME',
            'croatia (hrvatska)' => 'HR',
            'croatia' => 'HR',
            'slovenia' => 'SI',
            'slovakia' => 'SK',
            'czech republic' => 'CZ',
            'bosnia and herzegovina' => 'BA',
            'albania' => 'AL',
            'north macedonia' => 'MK',
            'kosovo' => 'XK',
            'malta' => 'MT',
            'luxembourg' => 'LU',
            'liechtenstein' => 'LI',
            'monaco' => 'MC',
            'andorra' => 'AD',
            'san marino' => 'SM',
            'vatican' => 'VA',
            'iceland' => 'IS',
            'norway' => 'NO',
            'sweden' => 'SE',
            'finland' => 'FI',
            'denmark' => 'DK',
            'svalbard/norway' => 'SJ',
            'lithuania' => 'LT',
            'latvia' => 'LV',
            'estonia' => 'EE',
            'ireland' => 'IE',
            'portugal' => 'PT',
            'spain' => 'ES',
            'ibiza/spain' => 'ES',
            'teneriffa/spain' => 'ES',
            'switzerland' => 'CH',
            'switzerland/france' => 'CH',
            'austria' => 'AT',
            'germany' => 'DE',
            'france' => 'FR',
            'belgium' => 'BE',
            'netherlands' => 'NL',
            'italy' => 'IT',
            'greece' => 'GR',
            'cyprus' => 'CY',
            'turkey' => 'TR',
            'israel' => 'IL',
            'jordan' => 'JO',
            'lebanon' => 'LB',
            'iraq' => 'IQ',
            'kuwait' => 'KW',
            'bahrain' => 'BH',
            'qatar' => 'QA',
            'oman' => 'OM',
            'yemen' => 'YE',
            'saudi arabia' => 'SA',
            'saudi arabien' => 'SA',
            'united arab emirates' => 'AE',
            'egypt' => 'EG',
            'libya' => 'LY',
            'tunisia' => 'TN',
            'algeria' => 'DZ',
            'morocco' => 'MA',
            'sudan' => 'SD',
            'south sudan' => 'SS',
            'ethiopia' => 'ET',
            'eritrea' => 'ER',
            'djibouti' => 'DJ',
            'somalia' => 'SO',
            'kenya' => 'KE',
            'uganda' => 'UG',
            'rwanda' => 'RW',
            'burundi' => 'BI',
            'tanzania' => 'TZ',
            'mozambique' => 'MZ',
            'malawi' => 'MW',
            'zambia' => 'ZM',
            'zimbabwe' => 'ZW',
            'botswana' => 'BW',
            'namibia' => 'NA',
            'south africa' => 'ZA',
            'lesotho' => 'LS',
            'swaziland' => 'SZ',
            'eswatini' => 'SZ',
            'madagascar' => 'MG',
            'mauritius' => 'MU',
            'seychelles' => 'SC',
            'comoros (comores)' => 'KM',
            'comoros' => 'KM',
            'cape verde' => 'CV',
            'sao tome & principe' => 'ST',
            'guinea-bissau' => 'GW',
            'guinea' => 'GN',
            'sierra leone' => 'SL',
            'liberia' => 'LR',
            'ivory coast' => 'CI',
            'ghana' => 'GH',
            'togo' => 'TG',
            'benin' => 'BJ',
            'nigeria' => 'NG',
            'niger' => 'NE',
            'chad' => 'TD',
            'cameroon' => 'CM',
            'central african republic' => 'CF',
            'equatorial guinea' => 'GQ',
            'gabon' => 'GA',
            'gabon/loyautte' => 'GA',
            'congo (roc)' => 'CG',
            'congo (drc)' => 'CD',
            'congo' => 'CG',
            'angola' => 'AO',
            'mali' => 'ML',
            'mauritania' => 'MR',
            'senegal' => 'SN',
            'gambia' => 'GM',
            'burkina faso' => 'BF',
            'canada' => 'CA',
            'mexico' => 'MX',
            'guatemala' => 'GT',
            'belize' => 'BZ',
            'honduras' => 'HN',
            'el salvador' => 'SV',
            'nicaragua' => 'NI',
            'costa rica' => 'CR',
            'panama' => 'PA',
            'cuba' => 'CU',
            'jamaica' => 'JM',
            'haiti' => 'HT',
            'dominican republic' => 'DO',
            'bahamas' => 'BS',
            'the bahamas' => 'BS',
            'barbados' => 'BB',
            'trinidad and tobago' => 'TT',
            'grenada' => 'GD',
            'saint lucia' => 'LC',
            'saint vincent & the grenadines' => 'VC',
            'saint vincent and the grenadines' => 'VC',
            'saint kitts and nevis' => 'KN',
            'st. kitts and nevis' => 'KN',
            'antigua and barbuda' => 'AG',
            'dominica' => 'DM',
            'guyana' => 'GY',
            'suriname' => 'SR',
            'brazil' => 'BR',
            'argentina' => 'AR',
            'chile' => 'CL',
            'uruguay' => 'UY',
            'paraguay' => 'PY',
            'bolivia' => 'BO',
            'peru' => 'PE',
            'ecuador' => 'EC',
            'colombia' => 'CO',
            'venezuela' => 'VE',
            'australia' => 'AU',
            'king island (australia)' => 'AU',
            'new zealand' => 'NZ',
            'hokkaido, japan' => 'JP',
            'japan' => 'JP',
            'india' => 'IN',
            'india, maharashtra' => 'IN',
            'pakistan' => 'PK',
            'bangladesh' => 'BD',
            'sri lanka' => 'LK',
            'nepal' => 'NP',
            'bhutan' => 'BT',
            'maldives' => 'MV',
            'maledives' => 'MV',
            'afghanistan' => 'AF',
            'china' => 'CN',
            'pr china' => 'CN',
            'fujian, pr china' => 'CN',
            'guangdong, pr china' => 'CN',
            'guangxi, pr china' => 'CN',
            'heilongjiang, pr china' => 'CN',
            'hubei, pr china' => 'CN',
            'jiangxi, china' => 'CN',
            'jilin, pr china' => 'CN',
            'liaoning, pr china' => 'CN',
            'shaanxi, pr china' => 'CN',
            'shandong, pr china' => 'CN',
            'shanxi, pr china' => 'CN',
            'sichuan, pr china' => 'CN',
            'xinjiang, pr china' => 'CN',
            'yunnan, pr china' => 'CN',
            'zhejiang, pr china' => 'CN',
            'indonesia' => 'ID',
            'malaysia' => 'MY',
            'singapore' => 'SG',
            'thailand' => 'TH',
            'philippines' => 'PH',
            'cambodia' => 'KH',
            'laos' => 'LA',
            'myanmar' => 'MM',
            'mongolia' => 'MN',
            'poland' => 'PL',
            'hungary' => 'HU',
            'romania' => 'RO',
            'bulgaria' => 'BG',
            'loyautte, pazifik' => 'NC',
            'loyaute, pazifik' => 'NC',
        ];
    }
}
