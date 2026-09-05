@php
    $composeThemeBases = [
        '#ffffff', '#000000', '#e7e6e6', '#44546a', '#5b9bd5',
        '#ed7d31', '#a5a5a5', '#4472c4', '#7030a0', '#00b050',
    ];
    $composeThemeShades = [
        ['#f2f2f2', '#7f7f7f', '#d0cece', '#d6dce4', '#deebf6', '#fce4d6', '#ededed', '#d9e2f3', '#e2d5f1', '#e2efd9'],
        ['#d8d8d8', '#595959', '#aeabab', '#adb9ca', '#bdd7ee', '#f8cbad', '#dbdbdb', '#b4c6e7', '#c5aadb', '#c5e0b3'],
        ['#bfbfbf', '#3f3f3f', '#757070', '#8496b0', '#9cc2e5', '#f4b183', '#c9c9c9', '#8eaadb', '#a780c7', '#a8d08d'],
        ['#a5a5a5', '#262626', '#3a3838', '#323f4f', '#2e75b5', '#c55a11', '#7b7b7b', '#2f5496', '#5b2c6f', '#548235'],
        ['#7f7f7f', '#0c0c0c', '#171616', '#222a35', '#1e4e79', '#833c0b', '#525252', '#1f3864', '#3b1642', '#375623'],
    ];
    $composeStandardColors = [
        '#c00000', '#ff0000', '#ffc000', '#ffff00', '#92d050',
        '#00b050', '#00b0f0', '#0070c0', '#002060', '#7030a0',
    ];
@endphp

<div class="compose-color-picker" data-color-mode="fore" data-default-color="#111827">
    <button type="button" class="compose-color-tool-btn" title="Font color" aria-label="Font color" aria-haspopup="true" aria-expanded="false">
        <span class="compose-color-letter" aria-hidden="true">A</span>
    </button>
    <div class="compose-color-palette" role="dialog" aria-label="Font color" hidden>
        <button type="button" class="compose-color-auto" data-color="#111827">Automatic</button>
        <div class="compose-color-section-label">Theme colours</div>
        <div class="compose-color-theme-bases">
            @foreach ($composeThemeBases as $color)
                <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
            @endforeach
        </div>
        <div class="compose-color-theme-shades">
            @foreach ($composeThemeShades as $row)
                <div class="compose-color-theme-row">
                    @foreach ($row as $color)
                        <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="compose-color-section-label">Standard colours</div>
        <div class="compose-color-standard">
            @foreach ($composeStandardColors as $color)
                <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
            @endforeach
        </div>
        <button type="button" class="compose-color-more">
            <i class="ti-palette" aria-hidden="true"></i> More colours...
        </button>
        <input type="color" class="compose-color-native" value="#111827" tabindex="-1" aria-hidden="true">
    </div>
</div>

<div class="compose-color-picker" data-color-mode="highlight" data-default-color="#fff59d">
    <button type="button" class="compose-color-tool-btn compose-color-tool-btn-highlight" title="Highlight" aria-label="Highlight" aria-haspopup="true" aria-expanded="false">
        <span class="compose-color-letter compose-color-letter-highlight" aria-hidden="true">A</span>
    </button>
    <div class="compose-color-palette" role="dialog" aria-label="Highlight color" hidden>
        <button type="button" class="compose-color-auto" data-color="transparent">No highlight</button>
        <div class="compose-color-section-label">Theme colours</div>
        <div class="compose-color-theme-bases">
            @foreach ($composeThemeBases as $color)
                <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
            @endforeach
        </div>
        <div class="compose-color-theme-shades">
            @foreach ($composeThemeShades as $row)
                <div class="compose-color-theme-row">
                    @foreach ($row as $color)
                        <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="compose-color-section-label">Standard colours</div>
        <div class="compose-color-standard">
            @foreach ($composeStandardColors as $color)
                <button type="button" class="compose-color-swatch" data-color="{{ $color }}" style="background:{{ $color }}" title="{{ strtoupper($color) }}"></button>
            @endforeach
        </div>
        <button type="button" class="compose-color-more">
            <i class="ti-palette" aria-hidden="true"></i> More colours...
        </button>
        <input type="color" class="compose-color-native" value="#fff59d" tabindex="-1" aria-hidden="true">
    </div>
</div>
