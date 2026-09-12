@if ($error)
    <div class="tracker-status-card alert-danger tracker-fade-in">
        <span class="tracker-status-kicker">Live lookup update</span>
        <div class="tracker-status-copy">{{ $error }}</div>
    </div>
@endif

@if ($result)
    @php
        $visuals = $result['visuals'] ?? [];
        $speedPct = min(100, max(0, ((float) ($visuals['speed_knots'] ?? 0)) / 20 * 100));
        $distancePct = min(100, max(0, ((float) ($visuals['trip_distance_nm'] ?? 0)) / 5000 * 100));
        $draughtPct = min(100, max(0, ((float) ($visuals['draught_m'] ?? 0)) / 20 * 100));
        $departureMeta = collect([
            $visuals['departure_event'] ?? null,
            $visuals['departure_time'] ?? null,
        ])->filter()->implode(' · ');
        $arrivalMeta = collect([
            $visuals['arrival_event'] ?? null,
            $visuals['arrival_time'] ?? null,
        ])->filter()->implode(' · ');
    @endphp

    @foreach ($result['warnings'] as $warning)
        <div class="tracker-status-card alert-info tracker-fade-in">
            <span class="tracker-status-kicker">Source note</span>
            <div class="tracker-status-copy">{{ $warning }}</div>
        </div>
    @endforeach

    <section class="tracker-result-shell tracker-fade-in" data-tracker-result="loaded">
        <script type="application/json" class="tracker-result-payload">@json($visuals)</script>

        <div class="tracker-summary-hero tracker-surface">
            <div class="tracker-summary-copy">
                <span class="tracker-lookup-chip">{{ $result['header']['lookup'] }}</span>
                <h2 class="tracker-summary-title">{{ $result['header']['title'] }}</h2>
                <p class="tracker-summary-subtitle">{{ $result['header']['subtitle'] }}</p>
                <p class="tracker-result-summary">{{ $result['summary'] }}</p>

                <div class="tracker-keyline">
                    <span class="tracker-key-pill">{{ $visuals['status'] ?? 'Status pending' }}</span>
                    <span class="tracker-key-pill">{{ $visuals['area'] ?? 'Area pending' }}</span>
                    <span class="tracker-key-pill">{{ $visuals['station'] ?? 'AIS station pending' }}</span>
                    <span class="tracker-key-pill">{{ $visuals['reported_at'] ?? 'Latest signal pending' }}</span>
                </div>

                @if (! empty($result['header']['chips']))
                    <div class="tracker-chip-grid">
                        @foreach ($result['header']['chips'] as $chip)
                            <div class="tracker-chip">
                                <span class="tracker-chip-label">{{ $chip['label'] }}</span>
                                <span class="tracker-chip-value">{{ $chip['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tracker-summary-stack">
                <div class="tracker-stat-card">
                    <span class="tracker-stat-label">Current Port</span>
                    <strong class="tracker-stat-value">{{ $visuals['current_port'] ?? '—' }}</strong>
                    <span class="tracker-stat-note">Latest detected port</span>
                </div>

                <div class="tracker-stat-card">
                    <span class="tracker-stat-label">Destination</span>
                    <strong class="tracker-stat-value">{{ $visuals['destination'] ?? '—' }}</strong>
                    <span class="tracker-stat-note">Active route target</span>
                </div>

                <div class="tracker-stat-card">
                    <span class="tracker-stat-label">Coordinates</span>
                    <strong class="tracker-stat-value">{{ $visuals['latitude_label'] ?? '—' }}</strong>
                    <span class="tracker-stat-note">{{ $visuals['longitude_label'] ?? 'Longitude pending' }}</span>
                </div>

            </div>
        </div>

        <div class="tracker-visual-grid">
            <section class="tracker-surface tracker-map-card">
                <div class="tracker-panel-top">
                    <div>
                        <span class="tracker-panel-kicker">Live location</span>
                        <h3 class="tracker-panel-title">Live world map</h3>
                    </div>
                    <span class="tracker-panel-badge">{{ $visuals['area'] ?? 'AIS area pending' }}</span>
                </div>

                <div class="tracker-map-stage">
                    <div class="tracker-vector-map-shell">
                        <div class="tracker-vector-map" data-tracker-leaflet-map aria-label="Vessel location map"></div>
                    </div>
                </div>

                <div class="tracker-map-locations">
                    <article class="tracker-map-location">
                        <span class="tracker-map-location-label">Departure</span>
                        <strong>{{ $visuals['departure_port'] ?? 'Route point pending' }}</strong>
                        <small>{{ $departureMeta ?: 'Departure update is not available yet.' }}</small>
                    </article>

                    <article class="tracker-map-location">
                        <span class="tracker-map-location-label">Arrival</span>
                        <strong>{{ $visuals['arrival_port'] ?? 'Route point pending' }}</strong>
                        <small>{{ $arrivalMeta ?: 'Arrival or destination update is not available yet.' }}</small>
                    </article>
                </div>
            </section>

            <section class="tracker-surface tracker-insight-card">
                <div class="tracker-panel-top">
                    <div>
                        <span class="tracker-panel-kicker">Movement insight</span>
                        <h3 class="tracker-panel-title">Signal and route pulse</h3>
                    </div>
                    <span class="tracker-panel-badge">{{ $visuals['speed_label'] ?? '—' }}</span>
                </div>

                <div class="tracker-compass-stack">
                    <div class="tracker-compass" style="--tracker-course: {{ (($visuals['course'] ?? 0)) . 'deg' }}">
                        <span class="tracker-compass-mark tracker-compass-mark-n">N</span>
                        <span class="tracker-compass-mark tracker-compass-mark-e">E</span>
                        <span class="tracker-compass-mark tracker-compass-mark-s">S</span>
                        <span class="tracker-compass-mark tracker-compass-mark-w">W</span>
                        <span class="tracker-compass-ring"></span>
                        <span class="tracker-compass-arrow"></span>
                        <span class="tracker-compass-center"></span>
                        <div class="tracker-compass-readout">
                            <span>Course</span>
                            <strong>{{ $visuals['course_label'] ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="tracker-meter-stack">
                        <div class="tracker-meter">
                            <div class="tracker-meter-head">
                                <span>Current speed</span>
                                <strong>{{ $visuals['speed_label'] ?? '—' }}</strong>
                            </div>
                            <div class="tracker-meter-rail">
                                <span class="tracker-meter-fill tracker-meter-fill-speed" style="width: {{ number_format($speedPct, 2, '.', '') }}%;"></span>
                            </div>
                        </div>

                        <div class="tracker-meter">
                            <div class="tracker-meter-head">
                                <span>Trip distance</span>
                                <strong>{{ $visuals['trip_distance_label'] ?? '—' }}</strong>
                            </div>
                            <div class="tracker-meter-rail">
                                <span class="tracker-meter-fill tracker-meter-fill-distance" style="width: {{ number_format($distancePct, 2, '.', '') }}%;"></span>
                            </div>
                        </div>

                        <div class="tracker-meter">
                            <div class="tracker-meter-head">
                                <span>Draught</span>
                                <strong>{{ $visuals['draught_label'] ?? '—' }}</strong>
                            </div>
                            <div class="tracker-meter-rail">
                                <span class="tracker-meter-fill tracker-meter-fill-draught" style="width: {{ number_format($draughtPct, 2, '.', '') }}%;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="tracker-sections-grid">
            @foreach ($result['sections'] as $section)
                <section class="tracker-section-card tracker-surface">
                    <div class="tracker-section-head">
                        <span class="tracker-section-icon" aria-hidden="true">
                            <i class="{{ $section['icon'] }}"></i>
                        </span>
                        <h2 class="tracker-section-title">{{ $section['title'] }}</h2>
                    </div>

                    <dl class="tracker-facts">
                        @foreach ($section['items'] as $item)
                            <div class="tracker-fact">
                                <dt>{{ $item['label'] }}</dt>
                                <dd>
                                    @if (filled($item['href'] ?? null))
                                        <a href="{{ $item['href'] }}">{{ $item['value'] }}</a>
                                    @else
                                        {{ $item['value'] }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endforeach
        </div>
    </section>
@elseif (! $error)
    <section class="tracker-empty-state tracker-surface tracker-fade-in" data-tracker-result="empty">
        <div class="tracker-empty-orbit">
            <span class="tracker-empty-orbit-core"></span>
            <span class="tracker-empty-orbit-ring tracker-empty-orbit-ring-a"></span>
            <span class="tracker-empty-orbit-ring tracker-empty-orbit-ring-b"></span>
        </div>

        <span class="tracker-empty-kicker">Realtime lookup ready</span>
        <h2 class="tracker-empty-title">Search and watch the vessel snapshot appear instantly</h2>
        <p class="tracker-empty-copy">
            Type a vessel name, IMO, or MMSI. The result appears here instantly with a pinned world map, movement insight, and detailed vessel facts.
        </p>

        <div class="tracker-empty-examples">
            <button type="button" class="tracker-example-chip" data-tracker-example="CS JOLA">CS JOLA</button>
            <button type="button" class="tracker-example-chip" data-tracker-example="9791896">9791896</button>
            <button type="button" class="tracker-example-chip" data-tracker-example="538007348">538007348</button>
        </div>
    </section>
@endif
