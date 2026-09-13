<?php

namespace App\Services;

use App\Models\CustomerVessel;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class VesselLiveTrackerService
{
    private const SOURCE_HOST = 'https://www.myshiptracking.com';

    private const DISPLAY_TIMEZONE = 'Asia/Kolkata';

    private const DISPLAY_TIMEZONE_LABEL = 'IST';

    private const STATION_LABELS = [
        'T-AIS' => 'Terrestrial AIS',
        'S-AIS' => 'Satellite AIS',
        'SAT-AIS' => 'Satellite AIS',
        'TER-AIS' => 'Terrestrial AIS',
    ];

    public function lookup(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            throw new RuntimeException('Enter a vessel name or IMO to continue.');
        }

        $portalMatch = $this->findPortalMatch($query);
        $warnings = [];

        [$searchMatch, $lookupLabel] = $this->resolvePublicMatch($query, $portalMatch, $warnings);

        $detail = null;

        try {
            $detail = $this->fetchDetailPage($searchMatch['detail_url'] ?? null);
        } catch (RuntimeException) {
            $warnings[] = 'Detailed live page could not be loaded, so a basic live snapshot is being shown.';
        }

        $header = $this->buildHeader($query, $lookupLabel, $searchMatch, $detail);

        return [
            'summary' => $this->buildSummary($header, $searchMatch, $detail),
            'header' => $header,
            'visuals' => $this->buildVisuals($header, $searchMatch, $detail),
            'sections' => array_values(array_filter([
                $this->buildPositionSection($searchMatch, $detail),
                $this->buildVoyageSection($searchMatch, $detail),
                $this->buildParticularsSection($searchMatch, $detail),
                $this->buildPortalSection($portalMatch),
            ])),
            'warnings' => array_values(array_unique(array_filter($warnings))),
        ];
    }

    public function suggestions(string $query, int $limit = 8): array
    {
        $query = trim($query);

        if ($query === '' || Str::length($query) < 2) {
            return [];
        }

        $normalizedQuery = $this->normalizeText($query);

        return CustomerVessel::query()
            ->with('customer:id,customer_name')
            ->select('id', 'customer_id', 'vessel', 'vessel_name_alias', 'vessel_imo', 'inactive_vessel')
            ->where(function ($builder) use ($query) {
                $builder->where('vessel', 'like', '%' . $query . '%')
                    ->orWhere('vessel_name_alias', 'like', '%' . $query . '%')
                    ->orWhere('vessel_imo', 'like', '%' . $query . '%');
            })
            ->get()
            ->sortByDesc(function (CustomerVessel $vessel) use ($query, $normalizedQuery) {
                return $this->suggestionScore($vessel, $query, $normalizedQuery);
            })
            ->unique(function (CustomerVessel $vessel) {
                return implode('|', [
                    $this->normalizeText((string) $vessel->vessel),
                    $this->normalizeText((string) $vessel->vessel_name_alias),
                    trim((string) $vessel->vessel_imo),
                ]);
            })
            ->take($limit)
            ->map(function (CustomerVessel $vessel) {
                $label = $this->firstFilled(
                    trim((string) $vessel->vessel),
                    trim((string) $vessel->vessel_name_alias),
                    trim((string) $vessel->vessel_imo)
                );
                $searchValue = $this->firstFilled(
                    trim((string) $vessel->vessel_imo),
                    trim((string) $vessel->vessel),
                    trim((string) $vessel->vessel_name_alias)
                );

                if ($label === null || $searchValue === null) {
                    return null;
                }

                return [
                    'value' => $searchValue,
                    'label' => $label,
                    'meta' => collect([
                        filled($vessel->vessel_imo) ? 'IMO ' . trim((string) $vessel->vessel_imo) : null,
                        filled($vessel->vessel_name_alias) ? 'Alias ' . trim((string) $vessel->vessel_name_alias) : null,
                        $vessel->customer?->customer_name,
                        $vessel->inactive_vessel ? 'Inactive vessel' : null,
                    ])->filter()->implode(' • '),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolvePublicMatch(string $query, ?CustomerVessel $portalMatch, array &$warnings): array
    {
        $lastException = null;

        foreach ($this->searchCandidates($query, $portalMatch) as $candidate) {
            try {
                $match = $this->searchPublicListing($candidate['search']);
            } catch (RuntimeException $exception) {
                $lastException = $exception;

                continue;
            }

            $lookupLabel = match ($candidate['source']) {
                'identifier' => 'Matched by exact ' . strtoupper((string) $candidate['identifier_type']) . ' ' . $candidate['search'],
                'portal_imo' => 'Resolved from saved portal vessel ' . trim((string) $portalMatch?->vessel),
                'portal_name' => 'Matched by saved portal vessel name ' . trim((string) $candidate['label']),
                default => 'Matched by public vessel search: ' . ($match['name'] ?? $query),
            };

            if (
                in_array($candidate['source'], ['query', 'portal_name'], true)
                && $this->normalizeText($query) !== ''
                && $this->normalizeText((string) ($match['name'] ?? '')) !== ''
                && $this->normalizeText($query) !== $this->normalizeText((string) ($match['name'] ?? ''))
            ) {
                $warnings[] = 'Showing the closest public vessel match found for your search.';
            }

            return [$match, $lookupLabel];
        }

        throw $lastException ?? new RuntimeException('No public vessel matched that search right now.');
    }

    private function searchCandidates(string $query, ?CustomerVessel $portalMatch): array
    {
        $seen = [];
        $candidates = [];
        $identifier = $this->extractIdentifier($query);

        $push = function (string $search, string $source, array $extra = []) use (&$seen, &$candidates): void {
            $search = trim($search);

            if ($search === '') {
                return;
            }

            $key = $this->normalizeText($search);

            if ($key === '' || isset($seen[$key])) {
                return;
            }

            $seen[$key] = true;
            $candidates[] = array_merge([
                'search' => $search,
                'source' => $source,
            ], $extra);
        };

        if ($identifier !== null) {
            $push($identifier['value'], 'identifier', ['identifier_type' => $identifier['type']]);
        }

        if ($portalMatch !== null) {
            if (filled($portalMatch->vessel_imo)) {
                $push((string) $portalMatch->vessel_imo, 'portal_imo');
            }

            if (filled($portalMatch->vessel)) {
                $push((string) $portalMatch->vessel, 'portal_name', ['label' => trim((string) $portalMatch->vessel)]);
            }

            if (filled($portalMatch->vessel_name_alias)) {
                $push((string) $portalMatch->vessel_name_alias, 'portal_name', ['label' => trim((string) $portalMatch->vessel_name_alias)]);
            }
        }

        $push($query, 'query');

        return $candidates;
    }

    private function searchPublicListing(string $search): array
    {
        try {
            $html = $this->requestHtml($this->searchUrl($search));
        } catch (Throwable $exception) {
            throw new RuntimeException('Live vessel search could not be loaded from the public source right now.', previous: $exception);
        }

        $rows = $this->parseSearchRows($html);

        if ($rows === []) {
            throw new RuntimeException('No public vessel matched that search right now.');
        }

        $normalizedSearch = $this->normalizeText($search);
        $identifier = $this->extractIdentifier($search);

        usort($rows, function (array $left, array $right) use ($normalizedSearch, $identifier) {
            return $this->searchScore($right, $normalizedSearch, $identifier) <=> $this->searchScore($left, $normalizedSearch, $identifier);
        });

        $match = $rows[0] ?? null;

        if (! is_array($match) || ! filled($match['detail_url'] ?? null)) {
            throw new RuntimeException('A live vessel match was found, but the details page link is missing.');
        }

        return $match;
    }

    private function searchScore(array $row, string $normalizedSearch, ?array $identifier): int
    {
        $score = 0;
        $name = $this->normalizeText((string) ($row['name'] ?? ''));
        $imo = (string) ($row['imo'] ?? '');
        $mmsi = (string) ($row['mmsi'] ?? '');

        if ($identifier !== null) {
            if ($identifier['type'] === 'imo' && $imo === $identifier['value']) {
                $score += 150;
            }

            if ($identifier['type'] === 'mmsi' && $mmsi === $identifier['value']) {
                $score += 150;
            }
        }

        if ($name !== '' && $name === $normalizedSearch) {
            $score += 100;
        } elseif ($name !== '' && str_starts_with($name, $normalizedSearch)) {
            $score += 60;
        } elseif ($name !== '' && str_contains($name, $normalizedSearch)) {
            $score += 30;
        }

        if (filled($row['area'] ?? null)) {
            $score += 5;
        }

        if (filled($row['speed'] ?? null)) {
            $score += 3;
        }

        return $score;
    }

    private function parseSearchRows(string $html): array
    {
        $xpath = $this->xpath($html);
        $rows = $xpath->query("//table[@id='table-filter']//tbody[contains(@class, 'table-body')]/tr");

        if ($rows === false) {
            return [];
        }

        $results = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $cells = $xpath->query('./td', $row);

            if ($cells === false || $cells->length < 7) {
                continue;
            }

            $primaryLink = $this->firstNode($xpath->query(".//a[contains(@href, '/vessels/')]", $cells->item(0)));
            $menuDetailLink = $this->firstNode($xpath->query(".//a[contains(@href, '/vessels/')]", $row));
            $mapLink = $this->firstNode($xpath->query(".//a[contains(@href, '/?mmsi=')]", $row));

            $detailPath = $this->firstFilled(
                $primaryLink?->getAttribute('href'),
                $menuDetailLink?->getAttribute('href')
            );

            $results[] = [
                'name' => $this->firstFilled(
                    $this->nodeText($primaryLink),
                    $this->nodeText($cells->item(0))
                ),
                'detail_path' => $detailPath,
                'detail_url' => $this->absoluteUrl($detailPath),
                'map_url' => $this->absoluteUrl($mapLink?->getAttribute('href')),
                'imo' => $this->extractPathIdentifier($detailPath, 'imo'),
                'mmsi' => $this->firstFilled(
                    $this->extractDigits($this->nodeText($cells->item(1)), 9),
                    $this->extractPathIdentifier($detailPath, 'mmsi')
                ),
                'type' => $this->nodeText($cells->item(2)),
                'area' => $this->nodeText($cells->item(3)),
                'speed' => $this->nodeText($cells->item(4)),
                'destination' => $this->nodeText($cells->item(5)),
                'received' => $this->nodeTitleOrText($xpath, $cells->item(6)),
            ];
        }

        return array_values(array_filter($results, fn (array $row) => filled($row['name'] ?? null)));
    }

    private function fetchDetailPage(?string $detailUrl): array
    {
        if (! filled($detailUrl)) {
            throw new RuntimeException('A live vessel match was found, but the details page link is missing.');
        }

        try {
            $html = $this->requestHtml($detailUrl);
        } catch (Throwable $exception) {
            throw new RuntimeException('Detailed live vessel page could not be loaded right now.', previous: $exception);
        }

        return $this->parseDetailPage($html, $detailUrl);
    }

    private function parseDetailPage(string $html, string $detailUrl): array
    {
        $xpath = $this->xpath($html);
        $general = $this->tableMap($xpath, "//*[@id='vsl-info-card']//table//tr");
        $trip = $this->tableMap($xpath, "//*[@id='ft-trip']//table[contains(@class, 'table-borderless')]//tr");
        $position = $this->tableMap($xpath, "//*[@id='ft-position']//table[contains(@class, 'table-borderless')]//tr");
        $information = $this->parseInformationSection($xpath, $html);
        $tripStops = $this->parseTripStops($xpath);
        $map = $this->parseMapSnapshot($html);

        return [
            'canonical_url' => $this->firstFilled($this->linkHref($xpath, 'canonical'), $detailUrl),
            'name' => $this->firstFilled(
                $this->nodeText($this->firstNode($xpath->query("//div[contains(@class, 'sticky-header')]//h1"))),
                $this->metaContent($xpath, 'og:title')
            ),
            'type' => $this->firstFilled(
                $this->nodeText($this->firstNode($xpath->query("//div[contains(@class, 'sticky-header')]//h2"))),
                $this->tableValue($general, 'Type'),
                $information['type'] ?? null
            ),
            'general' => $general,
            'trip' => $trip,
            'position' => $position,
            'trip_stops' => $tripStops,
            'information' => $information,
            'map' => $map,
        ];
    }

    private function buildHeader(string $query, string $lookupLabel, array $searchMatch, ?array $detail): array
    {
        $title = $this->firstFilled(
            $detail['name'] ?? null,
            $searchMatch['name'] ?? null,
            $query
        ) ?? $query;

        $type = $this->firstFilled(
            $detail['type'] ?? null,
            $searchMatch['type'] ?? null
        );

        return [
            'title' => $title,
            'subtitle' => $type ? 'Live public vessel snapshot for ' . $type : 'Live public vessel snapshot',
            'lookup' => $lookupLabel,
            'external_url' => $this->firstFilled(
                $detail['canonical_url'] ?? null,
                $searchMatch['detail_url'] ?? null
            ),
            'chips' => array_values(array_filter([
                $this->chip('IMO', $this->firstFilled(
                    $this->tableValue($detail['general'] ?? [], 'IMO'),
                    $searchMatch['imo'] ?? null
                )),
                $this->chip('MMSI', $this->firstFilled(
                    $this->tableValue($detail['general'] ?? [], 'MMSI'),
                    $searchMatch['mmsi'] ?? null
                )),
                $this->chip('Flag', $this->firstFilled(
                    $this->tableValue($detail['general'] ?? [], 'Flag'),
                    $detail['information']['flag'] ?? null
                )),
                $this->chip('Call sign', $this->tableValue($detail['general'] ?? [], 'Call Sign')),
                $this->chip('Live source', 'Public web scrape'),
            ])),
        ];
    }

    private function buildSummary(array $header, array $searchMatch, ?array $detail): string
    {
        $name = $header['title'] ?? 'This vessel';
        $status = $this->firstFilled(
            $this->tableValue($detail['position'] ?? [], 'Status'),
            $searchMatch['status'] ?? null
        );
        $area = $this->firstFilled(
            $this->tableValue($detail['position'] ?? [], 'Area'),
            $detail['information']['area'] ?? null,
            $searchMatch['area'] ?? null
        );
        $currentPort = $detail['information']['current_port'] ?? null;
        $speed = $this->firstFilled(
            $this->tableValue($detail['position'] ?? [], 'Speed'),
            $detail['information']['speed'] ?? null,
            $searchMatch['speed'] ?? null
        );
        $lastSignal = $this->formatTimestamp($this->firstFilled(
            $this->tableTimestamp($detail['position'] ?? [], 'Position Received'),
            $detail['information']['reported_at'] ?? null,
            $searchMatch['received'] ?? null
        ));

        $sentences = [];

        if ($status !== null) {
            $sentences[] = $name . ' is currently ' . Str::of($status)->lower()->value();
        }

        if ($area !== null && $currentPort !== null) {
            $sentences[] = 'Current area is ' . $area . ' near ' . $currentPort;
        } elseif ($area !== null) {
            $sentences[] = 'Current area is ' . $area;
        } elseif ($currentPort !== null) {
            $sentences[] = 'Current port is ' . $currentPort;
        }

        if ($speed !== null) {
            $sentences[] = 'Speed showing as ' . $speed;
        }

        if ($lastSignal !== null) {
            $sentences[] = 'Last AIS update was ' . $lastSignal;
        }

        return $sentences === []
            ? 'Latest live vessel details are ready.'
            : Str::finish(implode('. ', $sentences), '.');
    }

    private function buildVisuals(array $header, array $searchMatch, ?array $detail): array
    {
        $tripStops = collect($detail['trip_stops'] ?? [])
            ->map(function (array $stop) {
                return [
                    'port' => $stop['port'] ?? null,
                    'event' => $stop['event'] ?? null,
                    'time' => $this->formatTimestamp($stop['time'] ?? null),
                ];
            })
            ->filter(fn (array $stop) => filled($stop['port'] ?? null))
            ->values()
            ->all();

        $departureStop = collect($tripStops)->first(function (array $stop) {
            return str_starts_with(strtoupper((string) ($stop['event'] ?? '')), 'ATD');
        }) ?? ($tripStops[0] ?? null);

        $arrivalStop = collect($tripStops)->reverse()->first(function (array $stop) {
            return str_starts_with(strtoupper((string) ($stop['event'] ?? '')), 'ATA');
        }) ?? (! empty($tripStops) ? $tripStops[array_key_last($tripStops)] : null);

        $speedText = $this->firstFilled(
            $this->tableValue($detail['position'] ?? [], 'Speed'),
            $detail['information']['speed'] ?? null,
            $searchMatch['speed'] ?? null
        );

        return [
            'title' => $header['title'] ?? null,
            'subtitle' => $header['subtitle'] ?? null,
            'area' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Area'),
                $detail['information']['area'] ?? null,
                $searchMatch['area'] ?? null
            ),
            'current_port' => $detail['information']['current_port'] ?? null,
            'destination' => $searchMatch['destination'] ?? null,
            'reported_at' => $this->formatTimestamp($this->firstFilled(
                $this->tableTimestamp($detail['position'] ?? [], 'Position Received'),
                $detail['information']['reported_at'] ?? null,
                $searchMatch['received'] ?? null
            )),
            'status' => $this->tableValue($detail['position'] ?? [], 'Status'),
            'latitude' => $this->toFloat($detail['map']['lat'] ?? null),
            'longitude' => $this->toFloat($detail['map']['lon'] ?? null),
            'latitude_label' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Latitude'),
                $this->formatCoordinate($detail['map']['lat'] ?? null, 'lat')
            ),
            'longitude_label' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Longitude'),
                $this->formatCoordinate($detail['map']['lon'] ?? null, 'lon')
            ),
            'course' => $this->toFloat($this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Course'),
                $this->formatDegrees($detail['map']['course'] ?? null)
            )),
            'course_label' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Course'),
                $this->formatDegrees($detail['map']['course'] ?? null)
            ),
            'speed_knots' => $this->toFloat($speedText),
            'speed_label' => $speedText,
            'trip_distance_nm' => $this->toFloat($this->tableValue($detail['trip'] ?? [], 'Trip Distance')),
            'trip_distance_label' => $this->tableValue($detail['trip'] ?? [], 'Trip Distance'),
            'avg_speed_label' => $this->tableValue($detail['trip'] ?? [], 'AVG Speed'),
            'max_speed_label' => $this->tableValue($detail['trip'] ?? [], 'MAX Speed'),
            'draught_m' => $this->toFloat($this->firstFilled(
                $this->tableValue($detail['trip'] ?? [], 'Draught'),
                $detail['information']['draught'] ?? null
            )),
            'draught_label' => $this->firstFilled(
                $this->tableValue($detail['trip'] ?? [], 'Draught'),
                $detail['information']['draught'] ?? null
            ),
            'trip_time_label' => $this->tableValue($detail['trip'] ?? [], 'Trip Time'),
            'station' => $this->stationLabel($this->tableValue($detail['position'] ?? [], 'Station')),
            'map_url' => $searchMatch['map_url'] ?? null,
            'detail_url' => $this->firstFilled(
                $detail['canonical_url'] ?? null,
                $searchMatch['detail_url'] ?? null
            ),
            'departure_port' => $departureStop['port'] ?? null,
            'departure_event' => $departureStop['event'] ?? null,
            'departure_time' => $departureStop['time'] ?? null,
            'arrival_port' => $this->firstFilled(
                $arrivalStop['port'] ?? null,
                $searchMatch['destination'] ?? null,
                $detail['information']['current_port'] ?? null
            ),
            'arrival_event' => $this->firstFilled(
                $arrivalStop['event'] ?? null,
                filled($searchMatch['destination'] ?? null) ? 'Destination' : null
            ),
            'arrival_time' => $arrivalStop['time'] ?? null,
            'route_stops' => $tripStops,
        ];
    }

    private function buildPositionSection(array $searchMatch, ?array $detail): ?array
    {
        $map = $detail['map'] ?? [];

        return $this->section('Live position', 'ti-location-pin', [
            $this->fact('Last AIS update', $this->formatTimestamp($this->firstFilled(
                $this->tableTimestamp($detail['position'] ?? [], 'Position Received'),
                $detail['information']['reported_at'] ?? null,
                $searchMatch['received'] ?? null
            ))),
            $this->fact('Navigation status', $this->tableValue($detail['position'] ?? [], 'Status')),
            $this->fact('Current area', $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Area'),
                $detail['information']['area'] ?? null,
                $searchMatch['area'] ?? null
            )),
            $this->fact('Current port', $detail['information']['current_port'] ?? null),
            $this->fact('Latitude', $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Latitude'),
                $this->formatCoordinate($map['lat'] ?? null, 'lat')
            )),
            $this->fact('Longitude', $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Longitude'),
                $this->formatCoordinate($map['lon'] ?? null, 'lon')
            )),
            $this->fact('Speed', $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Speed'),
                $detail['information']['speed'] ?? null,
                $searchMatch['speed'] ?? null
            )),
            $this->fact('Course', $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Course'),
                $this->formatDegrees($map['course'] ?? null)
            )),
            $this->fact('Station', $this->stationLabel($this->tableValue($detail['position'] ?? [], 'Station'))),
        ]);
    }

    private function buildVoyageSection(array $searchMatch, ?array $detail): ?array
    {
        $stops = $detail['trip_stops'] ?? [];
        $departurePort = $stops[0]['port'] ?? null;
        $latestPort = $stops[count($stops) - 1]['port'] ?? null;

        return $this->section('Voyage snapshot', 'ti-direction-alt', [
            $this->fact('Destination', $searchMatch['destination'] ?? null),
            $this->fact('Departure port', $departurePort),
            $this->fact('Latest port', $this->firstFilled($detail['information']['current_port'] ?? null, $latestPort)),
            $this->fact('Trip time', $this->tableValue($detail['trip'] ?? [], 'Trip Time')),
            $this->fact('Trip distance', $this->tableValue($detail['trip'] ?? [], 'Trip Distance')),
            $this->fact('Average speed', $this->tableValue($detail['trip'] ?? [], 'AVG Speed')),
            $this->fact('Maximum speed', $this->tableValue($detail['trip'] ?? [], 'MAX Speed')),
            $this->fact('Reported draught', $this->firstFilled(
                $this->tableValue($detail['trip'] ?? [], 'Draught'),
                $detail['information']['draught'] ?? null
            )),
        ]);
    }

    private function buildParticularsSection(array $searchMatch, ?array $detail): ?array
    {
        return $this->section('Vessel particulars', 'ti-anchor', [
            $this->fact('Vessel type', $this->firstFilled(
                $detail['type'] ?? null,
                $searchMatch['type'] ?? null
            )),
            $this->fact('IMO', $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'IMO'),
                $searchMatch['imo'] ?? null
            )),
            $this->fact('MMSI', $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'MMSI'),
                $searchMatch['mmsi'] ?? null
            )),
            $this->fact('Flag', $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'Flag'),
                $detail['information']['flag'] ?? null
            )),
            $this->fact('Call sign', $this->tableValue($detail['general'] ?? [], 'Call Sign')),
            $this->fact('Size', $this->tableValue($detail['general'] ?? [], 'Size')),
            $this->fact('GT', $this->tableValue($detail['general'] ?? [], 'GT')),
            $this->fact('DWT', $this->tableValue($detail['general'] ?? [], 'DWT')),
            $this->fact('Build', $this->tableValue($detail['general'] ?? [], 'Build')),
        ]);
    }

    private function buildPortalSection(?CustomerVessel $portalMatch): ?array
    {
        if ($portalMatch === null) {
            return null;
        }

        return [
            'title' => 'Portal match',
            'icon' => 'ti-layout-grid2-alt',
            'items' => [
                $this->fact('Saved vessel', $portalMatch->vessel),
                $this->fact('Vessel alias', $portalMatch->vessel_name_alias),
                $this->fact('Customer', $portalMatch->customer?->customer_name),
                $this->fact('Saved IMO', $portalMatch->vessel_imo),
                $this->fact('Saved type', $portalMatch->vessel_type_alias),
                $this->fact('Manager', $portalMatch->manager),
                $this->fact('Account manager', $portalMatch->account_manager),
            ],
        ];
    }

    private function parseInformationSection(DOMXPath $xpath, string $html): array
    {
        $paragraphs = [];
        $nodes = $xpath->query("//div[contains(@class, 'card')][.//h3[normalize-space()='Information']]//div[contains(@class, 'card-body')][1]/p");

        if ($nodes !== false) {
            foreach ($nodes as $node) {
                $text = $this->nodeText($node);

                if ($text !== null) {
                    $paragraphs[] = $text;
                }
            }
        }

        $summary = implode(' ', $paragraphs);
        $parsed = [
            'paragraphs' => $paragraphs,
        ];

        if (preg_match('/is in\s+(.+?)\s+with coordinates\s+([\-0-9.]+\s*°\s*\/\s*[\-0-9.]+\s*°)\s+as reported on\s+([0-9:\- ]+)/i', $summary, $matches)) {
            $parsed['area'] = $this->normalizePlaceholder($matches[1]);
            $parsed['coordinates'] = $this->normalizePlaceholder($matches[2]);
            $parsed['reported_at'] = $this->normalizePlaceholder($matches[3]);
        }

        if (preg_match('/current speed is\s+(.+?)\s+and is currently inside the port of\s+(.+?)\./i', $summary, $matches)) {
            $parsed['speed'] = $this->normalizePlaceholder($matches[1]);
            $parsed['current_port'] = $this->normalizePlaceholder($matches[2]);
        }

        if (preg_match("/\\) is a\\s+(.+?)\\s+It'?s sailing under the flag of\\s+\\[(.+?)\\]\\s+(.+?)\\./i", $summary, $matches)) {
            $parsed['type'] = $this->normalizePlaceholder($matches[1]);
            $parsed['flag'] = $this->normalizePlaceholder($matches[3]);
        }

        if (preg_match('/current draught .*? is\s+([0-9.]+\s+meters)/i', $summary, $matches)) {
            $parsed['draught'] = $this->normalizePlaceholder($matches[1]);
        }

        if (! isset($parsed['coordinates'])) {
            $map = $this->parseMapSnapshot($html);

            if (isset($map['lat'], $map['lon'])) {
                $parsed['coordinates'] = rtrim(rtrim(number_format((float) $map['lat'], 5), '0'), '.')
                    . '° / '
                    . rtrim(rtrim(number_format((float) $map['lon'], 5), '0'), '.')
                    . '°';
            }
        }

        return $parsed;
    }

    private function parseTripStops(DOMXPath $xpath): array
    {
        $nodes = $xpath->query("//*[@id='vpage-current-trip']//div[contains(@class, 'myst-arrival-cont')]");

        if ($nodes === false) {
            return [];
        }

        $stops = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $port = $this->firstFilled(
                $this->nodeText($this->firstNode($xpath->query(".//h3//a", $node))),
                $this->nodeText($this->firstNode($xpath->query(".//h3", $node)))
            );

            if ($port === null) {
                continue;
            }

            $stops[] = [
                'port' => $port,
                'event' => $this->nodeText($this->firstNode($xpath->query(".//small[contains(@class, 'text-muted')][1]", $node))),
                'time' => $this->nodeText($this->firstNode($xpath->query(".//div[contains(@class, 'px-1')][1]", $node))),
            ];
        }

        return $stops;
    }

    private function parseMapSnapshot(string $html): array
    {
        if (! preg_match('/canvas_map_generate\("map_locator",\s*\d+,\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/', $html, $matches)) {
            return [];
        }

        return [
            'lat' => $matches[1],
            'lon' => $matches[2],
            'course' => $matches[3],
            'speed' => $matches[4],
        ];
    }

    private function tableMap(DOMXPath $xpath, string $query): array
    {
        $rows = $xpath->query($query);

        if ($rows === false) {
            return [];
        }

        $map = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $labelNode = $this->firstNode($xpath->query('./th[1]', $row));
            $valueNode = $this->firstNode($xpath->query('./td[1]', $row));

            if (! $labelNode instanceof DOMNode || ! $valueNode instanceof DOMNode) {
                continue;
            }

            $label = $this->normalizePlaceholder($this->nodeText($labelNode));

            if ($label === null) {
                continue;
            }

            $map[$this->normalizeLabel($label)] = [
                'label' => $label,
                'value' => $this->normalizePlaceholder($this->nodeText($valueNode)),
                'title' => $this->normalizePlaceholder($this->nestedTitle($xpath, $valueNode)),
            ];
        }

        return $map;
    }

    private function tableValue(array $table, string $label): ?string
    {
        $entry = $table[$this->normalizeLabel($label)] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        return $entry['value'] ?? null;
    }

    private function tableTimestamp(array $table, string $label): ?string
    {
        $entry = $table[$this->normalizeLabel($label)] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        return $this->firstFilled($entry['title'] ?? null, $entry['value'] ?? null);
    }

    private function requestHtml(string $url): string
    {
        $response = Http::timeout(20)
            ->withUserAgent('Mozilla/5.0 (compatible; MarineCaddieBot/1.0; +https://portal.marinecaddie.com)')
            ->withHeaders([
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Source page returned HTTP ' . $response->status() . '.');
        }

        $html = $response->body();

        if (trim($html) === '') {
            throw new RuntimeException('Source page returned an empty response.');
        }

        return $html;
    }

    private function searchUrl(string $query): string
    {
        return self::SOURCE_HOST . '/vessels?' . http_build_query([
            'name' => $query,
        ]);
    }

    private function findPortalMatch(string $query): ?CustomerVessel
    {
        $normalizedQuery = $this->normalizeText($query);
        $identifier = $this->extractIdentifier($query);

        return CustomerVessel::query()
            ->with('customer')
            ->when($identifier !== null, function ($builder) use ($identifier) {
                if ($identifier['type'] === 'imo') {
                    $builder->where('vessel_imo', $identifier['value']);
                } else {
                    $builder->whereRaw('1 = 0');
                }
            }, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('vessel', 'like', '%' . $query . '%')
                        ->orWhere('vessel_name_alias', 'like', '%' . $query . '%');
                });
            })
            ->get()
            ->sortByDesc(function (CustomerVessel $vessel) use ($normalizedQuery, $identifier) {
                if ($identifier !== null && $identifier['type'] === 'imo' && trim((string) $vessel->vessel_imo) === $identifier['value']) {
                    return 100;
                }

                $score = 0;
                $name = $this->normalizeText((string) $vessel->vessel);
                $alias = $this->normalizeText((string) $vessel->vessel_name_alias);

                if ($name !== '' && $name === $normalizedQuery) {
                    $score += 80;
                }

                if ($alias !== '' && $alias === $normalizedQuery) {
                    $score += 60;
                }

                if ($name !== '' && str_contains($name, $normalizedQuery)) {
                    $score += 20;
                }

                if ($alias !== '' && str_contains($alias, $normalizedQuery)) {
                    $score += 10;
                }

                if (filled($vessel->vessel_imo)) {
                    $score += 5;
                }

                return $score;
            })
            ->first();
    }

    private function extractIdentifier(string $query): ?array
    {
        if (! preg_match('/(\d{7}|\d{9})/', $query, $matches)) {
            return null;
        }

        $value = $matches[1];

        return [
            'type' => strlen($value) === 9 ? 'mmsi' : 'imo',
            'value' => $value,
        ];
    }

    private function suggestionScore(CustomerVessel $vessel, string $query, string $normalizedQuery): int
    {
        $score = 0;
        $vesselName = $this->normalizeText((string) $vessel->vessel);
        $alias = $this->normalizeText((string) $vessel->vessel_name_alias);
        $imo = trim((string) $vessel->vessel_imo);

        if ($imo !== '' && $imo === $query) {
            $score += 180;
        } elseif ($imo !== '' && str_starts_with($imo, $query)) {
            $score += 120;
        } elseif ($imo !== '' && str_contains($imo, $query)) {
            $score += 75;
        }

        if ($vesselName !== '' && $vesselName === $normalizedQuery) {
            $score += 170;
        } elseif ($vesselName !== '' && str_starts_with($vesselName, $normalizedQuery)) {
            $score += 130;
        } elseif ($vesselName !== '' && str_contains($vesselName, $normalizedQuery)) {
            $score += 85;
        }

        if ($alias !== '' && $alias === $normalizedQuery) {
            $score += 160;
        } elseif ($alias !== '' && str_starts_with($alias, $normalizedQuery)) {
            $score += 115;
        } elseif ($alias !== '' && str_contains($alias, $normalizedQuery)) {
            $score += 70;
        }

        if (! $vessel->inactive_vessel) {
            $score += 10;
        }

        return $score;
    }

    private function xpath(string $html): DOMXPath
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function firstNode($nodes): ?DOMNode
    {
        if ($nodes === false || $nodes === null || $nodes->length === 0) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMNode ? $node : null;
    }

    private function linkHref(DOMXPath $xpath, string $rel): ?string
    {
        $node = $this->firstNode($xpath->query("//link[@rel='{$rel}']"));

        if (! $node instanceof DOMElement) {
            return null;
        }

        return $this->absoluteUrl($node->getAttribute('href'));
    }

    private function metaContent(DOMXPath $xpath, string $property): ?string
    {
        $node = $this->firstNode($xpath->query("//meta[@property='{$property}']"));

        if (! $node instanceof DOMElement) {
            return null;
        }

        return $this->normalizePlaceholder($node->getAttribute('content'));
    }

    private function nestedTitle(DOMXPath $xpath, DOMNode $node): ?string
    {
        if ($node instanceof DOMElement && $node->hasAttribute('title')) {
            return $node->getAttribute('title');
        }

        $titleNode = $this->firstNode($xpath->query('.//*[@title]', $node));

        if (! $titleNode instanceof DOMElement) {
            return null;
        }

        return $titleNode->getAttribute('title');
    }

    private function nodeTitleOrText(DOMXPath $xpath, ?DOMNode $node): ?string
    {
        if ($node === null) {
            return null;
        }

        return $this->firstFilled(
            $this->normalizePlaceholder($this->nestedTitle($xpath, $node)),
            $this->normalizePlaceholder($this->nodeText($node))
        );
    }

    private function nodeText(?DOMNode $node): ?string
    {
        if ($node === null) {
            return null;
        }

        return $this->normalizePlaceholder(trim(preg_replace('/\s+/u', ' ', html_entity_decode($node->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? ''));
    }

    private function normalizeLabel(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();
    }

    private function normalizeText(string $value): string
    {
        return Str::of($value)
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', ' ')
            ->squish()
            ->value();
    }

    private function normalizePlaceholder(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(str_replace("\xc2\xa0", ' ', $value));

        if ($normalized === '' || $normalized === '---') {
            return null;
        }

        return $normalized;
    }

    private function extractPathIdentifier(?string $path, string $type): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $pattern = $type === 'imo' ? '/-imo-(\d{7})\b/' : '/-mmsi-(\d{9})\b/';

        if (! preg_match($pattern, (string) $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function extractDigits(?string $value, int $length): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! preg_match('/\d{' . $length . '}/', $value, $matches)) {
            return null;
        }

        return $matches[0];
    }

    private function stationLabel(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::STATION_LABELS[strtoupper($value)] ?? $value;
    }

    private function section(string $title, string $icon, array $items): ?array
    {
        $hasData = collect($items)->contains(function (array $item) {
            return ($item['value'] ?? '—') !== '—';
        });

        if (! $hasData) {
            return null;
        }

        return [
            'title' => $title,
            'icon' => $icon,
            'items' => $items,
        ];
    }

    private function fact(string $label, ?string $value, ?string $href = null): array
    {
        return [
            'label' => $label,
            'value' => $value ?: '—',
            'href' => $href,
        ];
    }

    private function chip(string $label, ?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        return [
            'label' => $label,
            'value' => $value,
        ];
    }

    private function firstFilled(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value !== null && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function formatCoordinate(?string $value, string $axis): ?string
    {
        if (! is_numeric($value)) {
            return null;
        }

        $numeric = (float) $value;
        $suffix = $axis === 'lat'
            ? ($numeric >= 0 ? 'N' : 'S')
            : ($numeric >= 0 ? 'E' : 'W');

        return number_format(abs($numeric), 5) . '° ' . $suffix;
    }

    private function formatDegrees(?string $value): ?string
    {
        if (! is_numeric($value)) {
            return null;
        }

        return rtrim(rtrim(number_format((float) $value, 1), '0'), '.') . '°';
    }

    private function formatTimestamp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        $sanitized = trim((string) preg_replace('/\s*\(UTC\)\s*$/i', '', $normalized));
        $sanitized = trim((string) preg_replace('/\s+UTC\s*$/i', '', $sanitized));

        try {
            $date = CarbonImmutable::parse($sanitized, 'UTC')->setTimezone(self::DISPLAY_TIMEZONE);
        } catch (Throwable) {
            return $normalized;
        }

        return $date->format('d M Y H:i') . ' ' . self::DISPLAY_TIMEZONE_LABEL;
    }

    private function absoluteUrl(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, 'http://')) {
            return 'https://' . substr($value, 7);
        }

        if (str_starts_with($value, '//')) {
            return 'https:' . $value;
        }

        return rtrim(self::SOURCE_HOST, '/') . '/' . ltrim($value, '/');
    }

    private function toFloat(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (! preg_match('/-?\d+(?:\.\d+)?/', $value, $matches)) {
            return null;
        }

        return (float) $matches[0];
    }
}
