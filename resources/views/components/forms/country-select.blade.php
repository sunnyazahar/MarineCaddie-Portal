@props([
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'countries' => [],
    'valueKey' => 'id',
    'labelClass' => 'form-label-custom',
    'wrapperClass' => 'form-group-custom',
    'placeholder' => 'Select Country',
    'allowClear' => null,
    'required' => false,
    'emptyOption' => true,
    'emptyOptionText' => null,
    'dropdownParent' => null,
])

@php
    $fieldId = $id ?? $name;
    $selectedValue = old($name, $value);
    if ($allowClear === null) {
        $allowClear = $valueKey === 'name';
    }
    $emptyLabel = $emptyOptionText ?? ($allowClear ? '' : $placeholder);
@endphp

@if ($wrapperClass)
    <div @class([$wrapperClass])>
@endif

@if ($label)
    <label class="{{ $labelClass }}" for="{{ $fieldId }}">{{ $label }}</label>
@endif

<select
    id="{{ $fieldId }}"
    name="{{ $name }}"
    data-country-select="1"
    data-placeholder="{{ $placeholder }}"
    data-allow-clear="{{ $allowClear ? '1' : '0' }}"
    @if ($dropdownParent) data-dropdown-parent="{{ $dropdownParent }}" @endif
    @if ($required) required @endif
    {{ $attributes->class(['select2-country-select']) }}
    style="width: 100%;"
>
    @if ($emptyOption)
        <option value="">{{ $emptyLabel }}</option>
    @endif
    @foreach ($countries as $country)
        @php
            $optionValue = $valueKey === 'name' ? $country->name : $country->id;
            $isSelected = (string) $selectedValue === (string) $optionValue;
        @endphp
        <option
            value="{{ $optionValue }}"
            data-flag-url="{{ $country->flag_url ?? '' }}"
            data-iso="{{ $country->iso_code ?? '' }}"
            @if ($isSelected) selected @endif
        >{{ $country->name }}</option>
    @endforeach
</select>

@if ($wrapperClass)
    </div>
@endif

@once('forms.country-select-init')
    @push('styles')
        <style>
            .country-select-flag {
                width: 20px;
                height: 15px;
                margin-right: 8px;
                vertical-align: middle;
                border: 1px solid #eee;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            (function () {
                if (window.MarineCaddieInitCountrySelect) {
                    return;
                }

                function resolveCountryFlagUrl($option) {
                    if (!$option || !$option.length) {
                        return null;
                    }

                    var flagUrl = $option.data('flagUrl') || $option.data('flag-url') || $option.attr('data-flag-url') || $option.data('flag');
                    if (flagUrl && String(flagUrl).indexOf('http') === 0) {
                        return flagUrl;
                    }

                    var iso = $option.data('iso') || flagUrl;
                    if (iso && String(iso).length <= 3) {
                        return 'https://flagcdn.com/w20/' + String(iso).toLowerCase() + '.png';
                    }

                    return null;
                }

                function formatCountrySelect(state) {
                    if (!state.id) {
                        return state.text;
                    }

                    var flagUrl = resolveCountryFlagUrl($(state.element));
                    if (!flagUrl) {
                        return state.text;
                    }

                    return $('<span><img src="' + flagUrl + '" class="country-select-flag" alt="" /> ' +
                        $('<div>').text(state.text).html() + '</span>');
                }

                window.MarineCaddieInitCountrySelect = function (scope) {
                    var $scope = scope ? $(scope) : $(document);

                    $scope.find('[data-country-select]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        var options = {
                            placeholder: $select.data('placeholder') || 'Select Country',
                            allowClear: String($select.data('allowClear')) === '1',
                            width: '100%',
                            templateResult: formatCountrySelect,
                            templateSelection: formatCountrySelect
                        };

                        var dropdownParent = $select.data('dropdownParent') || $select.data('dropdown-parent');
                        if (dropdownParent) {
                            options.dropdownParent = $(dropdownParent);
                        }

                        $select.select2(options);

                        $select.off('change.countrySelectValidation').on('change.countrySelectValidation', function () {
                            if ($(this).hasClass('error')) {
                                $(this).next('.select2-container').addClass('error');
                            } else {
                                $(this).next('.select2-container').removeClass('error');
                            }
                        });
                    });
                };

                $(document).ready(function () {
                    window.MarineCaddieInitCountrySelect();
                });
            })();
        </script>
    @endpush
@endonce
