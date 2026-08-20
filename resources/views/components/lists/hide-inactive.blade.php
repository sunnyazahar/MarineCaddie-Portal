@props([
    'id' => 'hide-inactive-check',
    'label' => 'Hide inactive',
    'checked' => false,
])

<div {{ $attributes->merge(['class' => 'filter-checkbox-group']) }}>
    <input type="checkbox" id="{{ $id }}" @checked($checked)>
    <label for="{{ $id }}">{{ $label }}</label>
</div>
