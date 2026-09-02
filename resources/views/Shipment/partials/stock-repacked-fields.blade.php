@props([
    'itemsName',
    'weightName',
    'itemsId',
    'weightId',
    'itemsValue' => null,
    'weightValue' => null,
    'disabled' => false,
])

<div class="stock-repacked-section">
    <div class="stock-repacked-heading">Repacked as:</div>
    <div class="stock-repacked-fields">
        <div class="stock-repacked-field">
            <label for="{{ $itemsId }}" class="stock-repacked-field-label">Repacked item(s)</label>
            <input
                type="text"
                name="{{ $itemsName }}"
                id="{{ $itemsId }}"
                class="form-control stock-repacked-input"
                inputmode="numeric"
                autocomplete="off"
                value="{{ old($itemsName, $itemsValue) }}"
                @if($disabled) disabled @endif
            >
        </div>
        <div class="stock-repacked-field">
            <label for="{{ $weightId }}" class="stock-repacked-field-label">Repacked weight (kg)</label>
            <input
                type="text"
                name="{{ $weightName }}"
                id="{{ $weightId }}"
                class="form-control stock-repacked-input"
                inputmode="decimal"
                autocomplete="off"
                value="{{ old($weightName, $weightValue) }}"
                @if($disabled) disabled @endif
            >
        </div>
    </div>
</div>
