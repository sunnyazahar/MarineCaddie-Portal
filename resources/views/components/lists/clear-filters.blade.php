@props([
    'id' => 'clear-filters',
    'label' => 'Clear filters',
])

<a href="#" id="{{ $id }}" {{ $attributes->merge(['class' => 'btn-clear-filters']) }}>{{ $label }}</a>
