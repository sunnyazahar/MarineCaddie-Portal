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
    {{ $attributes->class(['mc-input', 'form-control', 'select2-country-select']) }}
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
                display: inline-block !important;
                width: 20px !important;
                height: 15px !important;
                margin: 0 8px 0 0 !important;
                vertical-align: middle;
                border: 1px solid #eee;
                flex-shrink: 0;
            }

            .mc-country-option {
                display: inline-flex !important;
                align-items: center !important;
                gap: 0;
                line-height: 1.2;
                white-space: nowrap;
            }

            .mc-country-option__label {
                display: inline !important;
            }

            .select2-results__option .mc-country-option,
            .select2-selection__rendered .mc-country-option {
                display: inline-flex !important;
                align-items: center !important;
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

                    var $row = $('<span class="mc-country-option"></span>');
                    $row.append($('<img>', {
                        src: flagUrl,
                        class: 'country-select-flag',
                        alt: ''
                    }));
                    $row.append($('<span class="mc-country-option__label"></span>').text(state.text));
                    return $row;
                }

                window.MarineCaddieInitCountrySelect = function (scope) {
                    var $scope = scope ? $(scope) : $(document);

                    $scope.find('[data-country-select]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        var allowClearAttr = $select.attr('data-allow-clear');
                        var options = {
                            placeholder: $select.attr('data-placeholder') || 'Select Country',
                            allowClear: allowClearAttr === '1' || allowClearAttr === 'true',
                            width: '100%',
                            templateResult: formatCountrySelect,
                            templateSelection: formatCountrySelect
                        };

                        // Prefer explicit host (modals / .mc-select2-host), else body
                        var dropdownParent = $select.attr('data-dropdown-parent');
                        var $parent = null;
                        if (dropdownParent) {
                            if (dropdownParent === 'body' || dropdownParent === 'document.body') {
                                $parent = $(document.body);
                            } else {
                                $parent = $(dropdownParent);
                            }
                        }
                        if (!$parent || !$parent.length) {
                            $parent = $select.closest('.mc-select2-host, .modal');
                        }
                        options.dropdownParent = ($parent && $parent.length) ? $parent : $(document.body);

                        // Stock edit page manages #country_of_origin itself
                        if ($select.attr('id') === 'country_of_origin' && $('#country-of-origin-host').length) {
                            return;
                        }

                        $select.select2(options);
                        $select.next('.select2.select2-container').css('width', '100%');

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
