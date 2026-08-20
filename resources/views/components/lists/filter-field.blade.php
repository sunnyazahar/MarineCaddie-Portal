@props([
    'label',
    'width' => null,
])

<div @class(['filter-item']) @if($width) style="width: {{ $width }};" @endif>
    <div class="filter-group">
        <span class="filter-label">{{ $label }}</span>
        {{ $slot }}
    </div>
</div>
