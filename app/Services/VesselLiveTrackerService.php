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
    private const DETAIL_SOURCE_HOST = 'https://www.myshiptracking.com';

    private const DETAIL_SOURCE_LABEL = 'MyShipTracking public AIS';

    private const DETAIL_SOURCE_SHORT_LABEL = 'MyShipTracking AIS';

    private const POSITION_SOURCE_HOST = 'https://www.vesselfinder.com';

    private const POSITION_SOURCE_LABEL = 'VesselFinder public AIS';

    private const POSITION_SOURCE_SHORT_LABEL = 'VesselFinder AIS';

    private const STALE_SIGNAL_THRESHOLD_HOURS = 48;

    private const DISPLAY_TIMEZONE = '+02:00';

    private const DISPLAY_TIMEZONE_LABEL = 'UTC+2';

    private const DEFAULT_USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36';

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
            $warnings[] = 'Detailed source page could not be loaded, so a basic public AIS snapshot is being shown.';
        }

        $positionSnapshot = $this->resolvePositionSnapshot($query, $portalMatch, $searchMatch, $detail, $warnings);
        $freshness = $this->buildFreshness($positionSnapshot);

        if ($freshness['warning'] !== null) {
            $warnings[] = $freshness['warning'];
        }

        $header = $this->buildHeader($query, $lookupLabel, $searchMatch, $detail, $positionSnapshot, $freshness);

        return [
            'summary' => $this->buildSummary($header, $detail, $positionSnapshot, $freshness),
            'header' => $header,
            'visuals' => $this->buildVisuals($header, $searchMatch, $detail, $positionSnapshot, $freshness),
            'sections' => array_values(array_filter([
                $this->buildPositionSection($positionSnapshot, $freshness),
                $this->buildVoyageSection($searchMatch, $detail),
                $this->buildParticularsSection($searchMatch, $detail, $positionSnapshot),
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
            'out_of_coverage' => str_contains(Str::lower($html), 'out of coverage'),
        ];
    }

    private function resolvePositionSnapshot(
        string $query,
        ?CustomerVessel $portalMatch,
        array $searchMatch,
        ?array $detail,
        array &$warnings
    ): array {
        $fallback = $this->buildMyShipTrackingPositionSnapshot($searchMatch, $detail);
        $identifier = $this->extractIdentifier($query);
        $imo = $this->firstFilled(
            $this->tableValue($detail['general'] ?? [], 'IMO'),
            $searchMatch['imo'] ?? null,
            $portalMatch !== null && filled($portalMatch->vessel_imo) ? trim((string) $portalMatch->vessel_imo) : null,
            $identifier !== null && $identifier['type'] === 'imo' ? $identifier['value'] : null
        );

        if (! filled($imo)) {
            return $fallback;
        }

        try {
            $position = $this->fetchVesselFinderPosition($imo);
        } catch (RuntimeException) {
            $warnings[] = 'VesselFinder current position could not be loaded right now, so the position card is using MyShipTracking instead.';

            return $fallback;
        }

        if (
            ! filled($position['reported_raw'] ?? null)
            && ! filled($position['area'] ?? null)
            && ! filled($position['lat'] ?? null)
            && ! filled($position['lon'] ?? null)
        ) {
            $warnings[] = 'VesselFinder returned an incomplete position snapshot, so the position card is using MyShipTracking instead.';

            return $fallback;
        }

        $currentPort = $position['current_port'] ?? null;
        $lastPort = $position['last_port'] ?? null;

        return [
            'provider' => 'vesselfinder',
            'source_label' => self::POSITION_SOURCE_LABEL,
            'source_short_label' => self::POSITION_SOURCE_SHORT_LABEL,
            'detail_url' => $this->firstFilled(
                $position['canonical_url'] ?? null,
                $position['detail_url'] ?? null,
                $fallback['detail_url'] ?? null
            ),
            'map_url' => $this->firstFilled($position['map_url'] ?? null, $fallback['map_url'] ?? null),
            'title' => $this->firstFilled($position['name'] ?? null, $fallback['title'] ?? null),
            'type' => $this->firstFilled($position['type'] ?? null, $fallback['type'] ?? null),
            'imo' => $this->firstFilled($position['imo'] ?? null, $fallback['imo'] ?? null),
            'mmsi' => $this->firstFilled($position['mmsi'] ?? null, $fallback['mmsi'] ?? null),
            'flag' => $this->firstFilled($position['flag'] ?? null, $fallback['flag'] ?? null),
            'call_sign' => $this->firstFilled($position['call_sign'] ?? null, $fallback['call_sign'] ?? null),
            'status' => $position['status'] ?? null,
            'area' => $position['area'] ?? null,
            'current_port' => $currentPort,
            'last_port' => $lastPort,
            'last_port_event' => $position['last_port_event'] ?? null,
            'last_port_time_raw' => $position['last_port_time_raw'] ?? null,
            'port_value' => $this->firstFilled($currentPort, $lastPort),
            'port_label' => filled($currentPort) ? 'Current Port' : (filled($lastPort) ? 'Last Port' : 'Port reference'),
            'port_note' => filled($currentPort)
                ? 'Position source port status'
                : (filled($lastPort) ? 'Latest port shown by VesselFinder' : 'Position source did not publish a port'),
            'speed' => $position['speed'] ?? null,
            'course' => $position['course'] ?? null,
            'station' => $position['station'] ?? null,
            'reported_raw' => $position['reported_raw'] ?? null,
            'lat' => $position['lat'] ?? null,
            'lon' => $position['lon'] ?? null,
            'out_of_coverage' => (bool) ($position['out_of_coverage'] ?? false),
        ];
    }

    private function buildMyShipTrackingPositionSnapshot(array $searchMatch, ?array $detail): array
    {
        return [
            'provider' => 'myshiptracking',
            'source_label' => self::DETAIL_SOURCE_LABEL,
            'source_short_label' => self::DETAIL_SOURCE_SHORT_LABEL,
            'detail_url' => $this->firstFilled(
                $detail['canonical_url'] ?? null,
                $searchMatch['detail_url'] ?? null
            ),
            'map_url' => $searchMatch['map_url'] ?? null,
            'title' => $this->firstFilled(
                $detail['name'] ?? null,
                $searchMatch['name'] ?? null
            ),
            'type' => $this->firstFilled(
                $detail['type'] ?? null,
                $searchMatch['type'] ?? null
            ),
            'imo' => $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'IMO'),
                $searchMatch['imo'] ?? null
            ),
            'mmsi' => $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'MMSI'),
                $searchMatch['mmsi'] ?? null
            ),
            'flag' => $this->firstFilled(
                $this->tableValue($detail['general'] ?? [], 'Flag'),
                $detail['information']['flag'] ?? null
            ),
            'call_sign' => $this->tableValue($detail['general'] ?? [], 'Call Sign'),
            'status' => $this->tableValue($detail['position'] ?? [], 'Status'),
            'area' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Area'),
                $detail['information']['area'] ?? null,
                $searchMatch['area'] ?? null
            ),
            'current_port' => $detail['information']['current_port'] ?? null,
            'last_port' => null,
            'last_port_event' => null,
            'last_port_time_raw' => null,
            'port_value' => $detail['information']['current_port'] ?? null,
            'port_label' => 'Current Port',
            'port_note' => 'Latest detected port',
            'speed' => $this->normalizeSpeedLabel($this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Speed'),
                $detail['information']['speed'] ?? null,
                $searchMatch['speed'] ?? null
            )),
            'course' => $this->firstFilled(
                $this->tableValue($detail['position'] ?? [], 'Course'),
                $this->formatDegrees($detail['map']['course'] ?? null)
            ),
            'station' => $this->stationLabel($this->tableValue($detail['position'] ?? [], 'Station')),
            'reported_raw' => $this->firstFilled(
                $this->tableTimestamp($detail['position'] ?? [], 'Position Received'),
                $detail['information']['reported_at'] ?? null,
                $searchMatch['received'] ?? null
            ),
            'lat' => $detail['map']['lat'] ?? null,
            'lon' => $detail['map']['lon'] ?? null,
            'out_of_coverage' => (bool) ($detail['out_of_coverage'] ?? false),
        ];
    }

    private function fetchVesselFinderPosition(string $imo): array
    {
        $detailUrl = self::POSITION_SOURCE_HOST . '/vessels/details/' . rawurlencode($imo);

        try {
            $html = $this->requestHtml($detailUrl);
        } catch (Throwable $exception) {
            throw new RuntimeException('VesselFinder current position page could not be loaded right now.', previous: $exception);
        }

        return $this->parseVesselFinderDetailPage($html, $detailUrl);
    }

    private function parseVesselFinderDetailPage(string $html, string $detailUrl): array
    {
        $xpath = $this->xpath($html);
        $table = $this->tableMapFromCells($xpath, "//table[contains(@class, 'aparams')]//tr");
        $summary = $this->parseVesselFinderSummary(
            $this->nodeText($this->firstNode($xpath->query("//section[contains(@class, 'text-section')]//p[contains(@class, 'text2')]")))
        );
        $payload = $this->parseVesselFinderEmbeddedPayload($xpath);
        $identifiers = $this->parseCombinedIdentifiers($this->tableValue($table, 'IMO / MMSI'));
        $mapLink = $this->firstNode($xpath->query("//a[contains(@href, '/?imo=') or contains(@href, '/?mmsi=')]"));
        $lastPort = $this->parseVesselFinderLastPort($xpath);
        $station = null;

        if (array_key_exists('a2active', $payload)) {
            $station = $payload['a2active'] ? 'Satellite AIS' : 'Terrestrial AIS';
        } elseif (array_key_exists('ship_lat', $payload) || array_key_exists('ship_lon', $payload)) {
            $station = 'Terrestrial AIS';
        }

        return [
            'canonical_url' => $this->firstFilled(
                $this->linkHref($xpath, 'canonical', self::POSITION_SOURCE_HOST),
                $detailUrl
            ),
            'detail_url' => $detailUrl,
            'map_url' => $this->absoluteUrl(
                $mapLink instanceof DOMElement ? $mapLink->getAttribute('href') : null,
                self::POSITION_SOURCE_HOST
            ),
            'name' => $this->nodeText($this->firstNode($xpath->query('//h1[1]'))),
            'type' => $this->firstFilled(
                $this->tableValue($table, 'AIS Type'),
                $summary['type'] ?? null
            ),
            'imo' => $this->firstFilled(
                $identifiers['imo'] ?? null,
                $this->scalarToString($payload['imo'] ?? null)
            ),
            'mmsi' => $this->firstFilled(
                $identifiers['mmsi'] ?? null,
                $this->scalarToString($payload['mmsi'] ?? null)
            ),
            'flag' => $this->firstFilled(
                $this->tableValue($table, 'AIS Flag'),
                $summary['flag'] ?? null
            ),
            'call_sign' => $this->tableValue($table, 'Callsign'),
            'status' => $this->tableValue($table, 'Navigation Status'),
            'area' => $summary['area'] ?? null,
            'current_port' => $summary['current_port'] ?? null,
            'last_port' => $lastPort['port'] ?? null,
            'last_port_event' => $lastPort['event'] ?? null,
            'last_port_time_raw' => $lastPort['time_raw'] ?? null,
            'speed' => $this->firstFilled(
                $summary['speed'] ?? null,
                $this->formatKnots($this->scalarToString($payload['ship_sog'] ?? null))
            ),
            'course' => $this->formatDegrees($this->scalarToString($payload['ship_cog'] ?? null)),
            'station' => $station,
            'reported_raw' => $this->firstFilled(
                $this->tableTimestamp($table, 'Position Received'),
                $summary['reported_at'] ?? null,
                $this->scalarToString($payload['lrpd'] ?? null)
            ),
            'lat' => $this->scalarToString($payload['ship_lat'] ?? null),
            'lon' => $this->scalarToString($payload['ship_lon'] ?? null),
            'out_of_coverage' => str_contains(Str::lower($html), 'out of coverage'),
        ];
    }

    private function parseVesselFinderSummary(?string $summary): array
    {
        if ($summary === null) {
            return [];
        }

        $parsed = [];

        if (preg_match('/current position of .*? is\s+(?:at|in)\s+(.+?)\s+reported\s+(.+?)\s+by AIS/i', $summary, $matches)) {
            $parsed['area'] = $this->normalizePlaceholder($matches[1]);
            $parsed['reported_at'] = $this->normalizePlaceholder($matches[2]);
        }

        if (preg_match('/sailing at a speed of\s+([0-9.]+\s+knots?)/i', $summary, $matches)) {
            $parsed['speed'] = $this->normalizeSpeedLabel($matches[1]);
        }

        if (preg_match('/is a\s+(.+?)\s+built in\s+\d{4}.*?under the flag of\s+(.+?)\.?$/i', $summary, $matches)) {
            $parsed['type'] = $this->normalizePlaceholder($matches[1]);
            $parsed['flag'] = $this->normalizePlaceholder($matches[2]);
        }

        return $parsed;
    }

    private function parseVesselFinderEmbeddedPayload(DOMXPath $xpath): array
    {
        $node = $this->firstNode($xpath->query("//*[@id='djson']"));

        if (! $node instanceof DOMElement || ! $node->hasAttribute('data-json')) {
            return [];
        }

        $decoded = json_decode(html_entity_decode($node->getAttribute('data-json'), ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function parseCombinedIdentifiers(?string $value): array
    {
        if (! filled($value)) {
            return [];
        }

        if (preg_match('/^\s*(\d{7})\s*\/\s*(\d{9})\s*$/', $value, $matches)) {
            return [
                'imo' => $matches[1],
                'mmsi' => $matches[2],
            ];
        }

        $identifiers = [];

        if (preg_match('/(\d{7})/', $value, $imo)) {
            $identifiers['imo'] = $imo[1];
        }

        if (preg_match('/(\d{9})/', $value, $mmsi)) {
            $identifiers['mmsi'] = $mmsi[1];
        }

        return $identifiers;
    }

    private function parseVesselFinderLastPort(DOMXPath $xpath): array
    {
        $container = $this->firstNode($xpath->query("//*[contains(@class, 'vi__stp')][1]"));

        if (! $container instanceof DOMElement) {
            return [];
        }

        $port = $this->nodeText($this->firstNode($xpath->query(".//a[1]", $container)));
        $value = $this->nodeText($this->firstNode($xpath->query(".//*[contains(@class, '_value')][1]", $container)));

        if (! filled($value)) {
            return array_filter([
                'port' => $port,
            ], fn (?string $item) => filled($item));
        }

        if (! preg_match('/^\s*([A-Z]{2,4}\*?)\s*:\s*(.+?)(?:\s+\([^)]*\))?\s*$/', $value, $matches)) {
            return array_filter([
                'port' => $port,
            ], fn (?string $item) => filled($item));
        }

        return array_filter([
            'port' => $port,
            'event' => $this->normalizePlaceholder($matches[1] ?? null),
            'time_raw' => $this->normalizePlaceholder($matches[2] ?? null),
        ], fn (?string $item) => filled($item));
    }

    private function buildHeader(string $query, string $lookupLabel, array $searchMatch, ?array $detail, array $positionSnapshot, array $freshness): array
    {
        $title = $this->firstFilled(
            $positionSnapshot['title'] ?? null,
            $detail['name'] ?? null,
            $searchMatch['name'] ?? null,
            $query
        ) ?? $query;

        $type = $this->firstFilled(
            $positionSnapshot['type'] ?? null,
            $detail['type'] ?? null,
            $searchMatch['type'] ?? null
        );

        $positionSource = $positionSnapshot['source_label'] ?? self::DETAIL_SOURCE_LABEL;
        $subtitle = $type ? $type . ' - latest available public AIS snapshot' : 'Latest available public AIS snapshot';

        if (($positionSnapshot['provider'] ?? null) === 'vesselfinder') {
            $subtitle = $type
                ? $type . ' - position from VesselFinder, voyage details from MyShipTracking'
                : 'Position from VesselFinder, voyage details from MyShipTracking';
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'lookup' => $lookupLabel,
            'external_url' => $this->firstFilled(
                $positionSnapshot['detail_url'] ?? null,
                $detail['canonical_url'] ?? null,
                $searchMatch['detail_url'] ?? null
            ),
            'chips' => array_values(array_filter([
                $this->chip('IMO', $this->firstFilled(
                    $positionSnapshot['imo'] ?? null,
                    $this->tableValue($detail['general'] ?? [], 'IMO'),
                    $searchMatch['imo'] ?? null
                )),
                $this->chip('MMSI', $this->firstFilled(
                    $positionSnapshot['mmsi'] ?? null,
                    $this->tableValue($detail['general'] ?? [], 'MMSI'),
                    $searchMatch['mmsi'] ?? null
                )),
                $this->chip('Flag', $this->firstFilled(
                    $positionSnapshot['flag'] ?? null,
                    $this->tableValue($detail['general'] ?? [], 'Flag'),
                    $detail['information']['flag'] ?? null
                )),
                $this->chip('Call sign', $this->firstFilled(
                    $positionSnapshot['call_sign'] ?? null,
                    $this->tableValue($detail['general'] ?? [], 'Call Sign')
                )),
                $this->chip('Position source', $positionSource),
                ($positionSnapshot['provider'] ?? null) === 'vesselfinder'
                    ? $this->chip('Voyage source', self::DETAIL_SOURCE_LABEL)
                    : null,
                $this->chip('Signal age', $freshness['age_label'] ?? null),
            ])),
        ];
    }

    private function buildSummary(array $header, ?array $detail, array $positionSnapshot, array $freshness): string
    {
        $name = $header['title'] ?? 'This vessel';
        $status = $positionSnapshot['status'] ?? null;
        $area = $positionSnapshot['area'] ?? null;
        $currentPort = $positionSnapshot['current_port'] ?? null;
        $speed = $positionSnapshot['speed'] ?? null;
        $lastSignal = $freshness['reported_at'] ?? null;
        $signalSource = $positionSnapshot['source_label'] ?? self::DETAIL_SOURCE_LABEL;

        $sentences = [];

        if ($freshness['is_stale']) {
            if ($status !== null) {
                $sentences[] = $name . ' was last reported as ' . Str::of($status)->lower()->value();
            }

            if ($area !== null && $currentPort !== null) {
                $sentences[] = 'Last known area was ' . $area . ' near ' . $currentPort;
            } elseif ($area !== null) {
                $sentences[] = 'Last known area was ' . $area;
            } elseif ($currentPort !== null) {
                $sentences[] = 'Last known port was ' . $currentPort;
            }

            if ($speed !== null) {
                $sentences[] = 'Recorded speed was ' . $speed;
            }

            if ($lastSignal !== null) {
                $sentences[] = $signalSource . ' last updated on ' . $lastSignal;
            }
        } else {
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
        }

        return $sentences === []
            ? 'Latest public vessel details are ready.'
            : Str::finish(implode('. ', $sentences), '.');
    }

    private function buildVisuals(array $header, array $searchMatch, ?array $detail, array $positionSnapshot, array $freshness): array
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

        $routeHighlights = $this->buildRouteHighlights($tripStops, $searchMatch, $detail, $positionSnapshot, $freshness);

        $speedText = $positionSnapshot['speed'] ?? null;

        return [
            'title' => $header['title'] ?? null,
            'subtitle' => $header['subtitle'] ?? null,
            'source_label' => $positionSnapshot['source_label'] ?? self::DETAIL_SOURCE_LABEL,
            'voyage_source_label' => self::DETAIL_SOURCE_LABEL,
            'signal_label' => $freshness['signal_label'],
            'signal_age' => $freshness['age_label'],
            'is_stale' => $freshness['is_stale'],
            'position_panel_kicker' => $freshness['is_stale'] ? 'Last known location' : 'Live location',
            'position_panel_title' => $freshness['is_stale'] ? 'Last known world map' : 'Live world map',
            'area' => $positionSnapshot['area'] ?? null,
            'current_port' => $positionSnapshot['current_port'] ?? null,
            'port_value' => $positionSnapshot['port_value'] ?? null,
            'port_label' => $positionSnapshot['port_label'] ?? 'Current Port',
            'port_note' => $positionSnapshot['port_note'] ?? 'Latest detected port',
            'destination' => $searchMatch['destination'] ?? null,
            'reported_at' => $freshness['reported_at'],
            'status' => $positionSnapshot['status'] ?? null,
            'latitude' => $this->toFloat($positionSnapshot['lat'] ?? null),
            'longitude' => $this->toFloat($positionSnapshot['lon'] ?? null),
            'latitude_label' => $this->formatCoordinate($positionSnapshot['lat'] ?? null, 'lat'),
            'longitude_label' => $this->formatCoordinate($positionSnapshot['lon'] ?? null, 'lon'),
            'course' => $this->toFloat($positionSnapshot['course'] ?? null),
            'course_label' => $positionSnapshot['course'] ?? null,
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
            'station' => $positionSnapshot['station'] ?? null,
            'map_url' => $positionSnapshot['map_url'] ?? ($searchMatch['map_url'] ?? null),
            'detail_url' => $this->firstFilled(
                $positionSnapshot['detail_url'] ?? null,
                $detail['canonical_url'] ?? null,
                $searchMatch['detail_url'] ?? null
            ),
            'departure_label' => $routeHighlights['departure_label'],
            'departure_port' => $routeHighlights['departure_port'],
            'departure_event' => $routeHighlights['departure_event'],
            'departure_time' => $routeHighlights['departure_time'],
            'departure_empty_note' => $routeHighlights['departure_empty_note'],
            'arrival_label' => $routeHighlights['arrival_label'],
            'arrival_port' => $routeHighlights['arrival_port'],
            'arrival_event' => $routeHighlights['arrival_event'],
            'arrival_time' => $routeHighlights['arrival_time'],
            'arrival_empty_note' => $routeHighlights['arrival_empty_note'],
            'route_stops' => $tripStops,
        ];
    }

    private function buildPositionSection(array $positionSnapshot, array $freshness): ?array
    {
        return $this->section($freshness['is_stale'] ? 'Last known position' : 'Live position', 'ti-location-pin', [
            $this->fact('Signal freshness', $freshness['signal_label']),
            $this->fact('Last AIS update', $freshness['reported_at']),
            $this->fact('Navigation status', $positionSnapshot['status'] ?? null),
            $this->fact('Current area', $positionSnapshot['area'] ?? null),
            $this->fact('Current port', $positionSnapshot['current_port'] ?? null),
            $this->fact('Last port', $positionSnapshot['last_port'] ?? null),
            $this->fact('Latitude', $this->formatCoordinate($positionSnapshot['lat'] ?? null, 'lat')),
            $this->fact('Longitude', $this->formatCoordinate($positionSnapshot['lon'] ?? null, 'lon')),
            $this->fact('Speed', $positionSnapshot['speed'] ?? null),
            $this->fact('Course', $positionSnapshot['course'] ?? null),
            $this->fact('Station', $positionSnapshot['station'] ?? null),
        ]);
    }

    private function buildFreshness(array $positionSnapshot): array
    {
        $reportedRaw = $positionSnapshot['reported_raw'] ?? null;
        $reportedAt = $this->parseTimestamp($reportedRaw);
        $reportedAtLabel = $this->formatTimestamp($reportedRaw);
        $ageLabel = $reportedAt !== null
            ? $this->humanizeSignalAge($reportedAt)
            : $this->fallbackSignalAgeLabel($reportedRaw);
        $isOutOfCoverage = (bool) ($positionSnapshot['out_of_coverage'] ?? false);
        $sourceLabel = $positionSnapshot['source_label'] ?? 'Public AIS source';
        $sourceShortLabel = $positionSnapshot['source_short_label'] ?? 'public AIS';
        $isStale = $isOutOfCoverage;

        if ($reportedAt !== null) {
            $isStale = $isStale || $reportedAt->lessThan(CarbonImmutable::now('UTC')->subHours(self::STALE_SIGNAL_THRESHOLD_HOURS));
        } elseif ($reportedRaw !== null) {
            $isStale = $isStale || $this->signalLooksStale($reportedRaw);
        }

        $warning = null;

        if ($isOutOfCoverage && $reportedAtLabel !== null) {
            $warning = $sourceLabel . ' shows this vessel as out of coverage. The position below is last known data from '
                . $reportedAtLabel
                . ($ageLabel ? ' (' . $ageLabel . ')' : '')
                . ' and may differ from fresher AIS services.';
        } elseif ($isOutOfCoverage) {
            $warning = $sourceLabel . ' shows this vessel as out of coverage, so the position below is last known data and may differ from fresher AIS services.';
        } elseif ($isStale && $reportedAtLabel !== null) {
            $warning = $sourceLabel . ' last updated this vessel on '
                . $reportedAtLabel
                . ($ageLabel ? ' (' . $ageLabel . ')' : '')
                . '. Treat the map and position below as last known, not current real-time AIS.';
        } elseif ($isStale) {
            $warning = $sourceLabel . ' is older than the real-time window for this vessel. Treat the map and position below as last known, not current real-time AIS.';
        }

        return [
            'is_stale' => $isStale,
            'reported_at' => $reportedAtLabel,
            'age_label' => $ageLabel,
            'signal_label' => $isStale ? 'Last known ' . $sourceShortLabel : 'Current ' . $sourceShortLabel,
            'warning' => $warning,
        ];
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

    private function buildParticularsSection(array $searchMatch, ?array $detail, array $positionSnapshot): ?array
    {
        return $this->section('Vessel particulars', 'ti-anchor', [
            $this->fact('Vessel type', $this->firstFilled(
                $positionSnapshot['type'] ?? null,
                $detail['type'] ?? null,
                $searchMatch['type'] ?? null
            )),
            $this->fact('IMO', $this->firstFilled(
                $positionSnapshot['imo'] ?? null,
                $this->tableValue($detail['general'] ?? [], 'IMO'),
                $searchMatch['imo'] ?? null
            )),
            $this->fact('MMSI', $this->firstFilled(
                $positionSnapshot['mmsi'] ?? null,
                $this->tableValue($detail['general'] ?? [], 'MMSI'),
                $searchMatch['mmsi'] ?? null
            )),
            $this->fact('Flag', $this->firstFilled(
                $positionSnapshot['flag'] ?? null,
                $this->tableValue($detail['general'] ?? [], 'Flag'),
                $detail['information']['flag'] ?? null
            )),
            $this->fact('Call sign', $this->firstFilled(
                $positionSnapshot['call_sign'] ?? null,
                $this->tableValue($detail['general'] ?? [], 'Call Sign')
            )),
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

    private function buildRouteHighlights(
        array $tripStops,
        array $searchMatch,
        ?array $detail,
        array $positionSnapshot,
        array $freshness
    ): array {
        $confirmedDepartureStop = $this->selectConfirmedTripStop($tripStops, 'ATD');
        $confirmedArrivalStop = $this->selectConfirmedTripStop($tripStops, 'ATA', true);
        $lastPortEvent = $positionSnapshot['last_port_event'] ?? null;
        $lastPortTime = $this->formatTimestamp($positionSnapshot['last_port_time_raw'] ?? null);
        $usesLastPortReference = filled($positionSnapshot['last_port'] ?? null);
        $hasConfirmedLeg = ! $freshness['is_stale']
            && $confirmedDepartureStop !== null
            && ($confirmedArrivalStop !== null || filled($searchMatch['destination'] ?? null));

        if ($hasConfirmedLeg) {
            return [
                'departure_label' => 'Departure',
                'departure_port' => $confirmedDepartureStop['port'] ?? null,
                'departure_event' => $confirmedDepartureStop['event'] ?? null,
                'departure_time' => $confirmedDepartureStop['time'] ?? null,
                'departure_empty_note' => 'Departure update is not available yet.',
                'arrival_label' => 'Arrival',
                'arrival_port' => $this->firstFilled(
                    $confirmedArrivalStop['port'] ?? null,
                    $searchMatch['destination'] ?? null,
                    $detail['information']['current_port'] ?? null
                ),
                'arrival_event' => $this->firstFilled(
                    $confirmedArrivalStop['event'] ?? null,
                    filled($searchMatch['destination'] ?? null) ? 'Destination' : null
                ),
                'arrival_time' => $confirmedArrivalStop['time'] ?? null,
                'arrival_empty_note' => 'Arrival or destination update is not available yet.',
            ];
        }

        $fallbackDeparturePort = $this->firstFilled(
            $confirmedDepartureStop['port'] ?? null,
            $searchMatch['destination'] ?? null,
            $tripStops[0]['port'] ?? null
        );
        $fallbackArrivalPort = $this->firstFilled(
            $positionSnapshot['last_port'] ?? null,
            $confirmedArrivalStop['port'] ?? null,
            $detail['information']['current_port'] ?? null,
            $searchMatch['destination'] ?? null,
            ! empty($tripStops) ? $tripStops[array_key_last($tripStops)]['port'] ?? null : null
        );

        return [
            'departure_label' => $confirmedDepartureStop !== null ? 'Departure reference' : 'Route reference',
            'departure_port' => $fallbackDeparturePort,
            'departure_event' => $freshness['is_stale'] ? null : ($confirmedDepartureStop['event'] ?? null),
            'departure_time' => $freshness['is_stale'] ? null : ($confirmedDepartureStop['time'] ?? null),
            'departure_empty_note' => $freshness['is_stale']
                ? 'Historical route reference from MyShipTracking public AIS.'
                : 'Public route sources did not publish a confirmed departure update.',
            'arrival_label' => $usesLastPortReference ? 'Last port' : 'Route arrival',
            'arrival_port' => $fallbackArrivalPort,
            'arrival_event' => $usesLastPortReference ? $lastPortEvent : ($confirmedArrivalStop['event'] ?? null),
            'arrival_time' => $usesLastPortReference ? $lastPortTime : ($confirmedArrivalStop['time'] ?? null),
            'arrival_empty_note' => $usesLastPortReference
                ? 'Latest last-port reference from VesselFinder public AIS.'
                : 'Showing the latest route or port reference from the public sources.',
        ];
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

    private function selectConfirmedTripStop(array $tripStops, string $event, bool $reverse = false): ?array
    {
        $collection = $reverse ? collect($tripStops)->reverse() : collect($tripStops);

        $match = $collection->first(function (array $stop) use ($event) {
            return str_starts_with(strtoupper((string) ($stop['event'] ?? '')), strtoupper($event))
                && filled($stop['time'] ?? null);
        });

        return is_array($match) ? $match : null;
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

    private function tableMapFromCells(DOMXPath $xpath, string $query): array
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

            $labelNode = $this->firstNode($xpath->query('./td[1]', $row));
            $valueNode = $this->firstNode($xpath->query('./td[2]', $row));

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
            ->withUserAgent(self::DEFAULT_USER_AGENT)
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

        if ($this->looksLikeAntiBotChallenge($html)) {
            throw new RuntimeException('Source page presented an anti-bot challenge.');
        }

        return $html;
    }

    private function searchUrl(string $query): string
    {
        return self::DETAIL_SOURCE_HOST . '/vessels?' . http_build_query([
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

    private function linkHref(DOMXPath $xpath, string $rel, string $host = self::DETAIL_SOURCE_HOST): ?string
    {
        $node = $this->firstNode($xpath->query("//link[@rel='{$rel}']"));

        if (! $node instanceof DOMElement) {
            return null;
        }

        return $this->absoluteUrl($node->getAttribute('href'), $host);
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
        if ($node instanceof DOMElement) {
            foreach (['title', 'data-title'] as $attribute) {
                if ($node->hasAttribute($attribute)) {
                    return $this->normalizePlaceholder($node->getAttribute($attribute));
                }
            }
        }

        $titleNode = $this->firstNode($xpath->query('.//*[@title or @data-title]', $node));

        if (! $titleNode instanceof DOMElement) {
            return null;
        }

        return $this->firstFilled(
            $this->normalizePlaceholder($titleNode->getAttribute('title')),
            $this->normalizePlaceholder($titleNode->getAttribute('data-title'))
        );
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

        if ($normalized === '' || $normalized === '---' || $normalized === '-') {
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

    private function scalarToString(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }

        return (string) $value;
    }

    private function formatKnots(?string $value): ?string
    {
        if ($value === null || ! is_numeric($value)) {
            return null;
        }

        return rtrim(rtrim(number_format((float) $value, 1), '0'), '.') . ' Knots';
    }

    private function normalizeSpeedLabel(?string $value): ?string
    {
        $normalized = $this->normalizePlaceholder($value);

        if ($normalized === null) {
            return null;
        }

        if (! preg_match('/-?\d+(?:\.\d+)?/', $normalized, $matches)) {
            return $normalized;
        }

        return $this->formatKnots($matches[0]);
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
        $date = $this->parseTimestamp($normalized);

        if ($date === null) {
            return $normalized;
        }

        return $date->setTimezone(self::DISPLAY_TIMEZONE)->format('d M Y H:i') . ' ' . self::DISPLAY_TIMEZONE_LABEL;
    }

    private function absoluteUrl(?string $value, string $host = self::DETAIL_SOURCE_HOST): ?string
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

        return rtrim($host, '/') . '/' . ltrim($value, '/');
    }

    private function parseTimestamp(?string $value): ?CarbonImmutable
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        $sanitized = trim((string) preg_replace('/\s*\(UTC\)\s*$/i', '', $normalized));
        $sanitized = trim((string) preg_replace('/\s+UTC\s*$/i', '', $sanitized));

        if (preg_match('/^[A-Z][a-z]{2}\s+\d{1,2},\s+\d{1,2}:\d{2}$/', $sanitized)) {
            return $this->parseTimestampWithoutYear($sanitized);
        }

        if (! preg_match('/\d{4}-\d{2}-\d{2}|(?:\d{1,2}\s+[A-Z][a-z]{2}\s+\d{4}|[A-Z][a-z]{2}\s+\d{1,2},\s+\d{4})/', $sanitized)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($sanitized, 'UTC');
        } catch (Throwable) {
            return null;
        }
    }

    private function parseTimestampWithoutYear(string $value): ?CarbonImmutable
    {
        $reference = CarbonImmutable::now('UTC');

        try {
            $parsed = CarbonImmutable::createFromFormat('M j, H:i Y', $value . ' ' . $reference->year, 'UTC');
        } catch (Throwable) {
            return null;
        }

        if ($parsed === false) {
            return null;
        }

        if ($parsed->greaterThan($reference->addDays(2))) {
            return $parsed->subYear();
        }

        return $parsed;
    }

    private function humanizeSignalAge(CarbonImmutable $reportedAt): string
    {
        $seconds = max(0, $reportedAt->diffInSeconds(CarbonImmutable::now('UTC'), true));

        if ($seconds < 90) {
            return 'moments old';
        }

        $minutes = intdiv($seconds, 60);

        if ($minutes < 90) {
            return $minutes . ' ' . Str::plural('minute', $minutes) . ' old';
        }

        $hours = intdiv($seconds, 3600);

        if ($hours < 48) {
            return $hours . ' ' . Str::plural('hour', $hours) . ' old';
        }

        $days = intdiv($seconds, 86400);

        return $days . ' ' . Str::plural('day', $days) . ' old';
    }

    private function fallbackSignalAgeLabel(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->normalizePlaceholder($value);
    }

    private function signalLooksStale(string $value): bool
    {
        $normalized = Str::of($value)->lower()->squish()->value();

        if (preg_match('/(\d+)\s*(day|days|d)\b/', $normalized)) {
            return true;
        }

        if (preg_match('/(\d+)\s*(hour|hours|hr|hrs|h)\b/', $normalized, $matches)) {
            return (int) $matches[1] >= self::STALE_SIGNAL_THRESHOLD_HOURS;
        }

        if (preg_match('/(\d+)\s*(minute|minutes|min|mins|m)\b/', $normalized, $matches)) {
            return (int) $matches[1] >= self::STALE_SIGNAL_THRESHOLD_HOURS * 60;
        }

        return false;
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

    private function looksLikeAntiBotChallenge(string $html): bool
    {
        $normalized = Str::lower($html);

        return str_contains($normalized, '<title>just a moment')
            || str_contains($normalized, '<title>attention required')
            || str_contains($normalized, 'cf-browser-verification')
            || str_contains($normalized, 'checking your browser before accessing')
            || str_contains($normalized, '/cdn-cgi/challenge-platform/')
            || str_contains($normalized, 'window._cf_chl_opt')
            || str_contains($normalized, 'cf_chl_');
    }
}
