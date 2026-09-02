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
    'ajax' => false,
    'selectedText' => null,
])

@php
    $fieldId = $id ?? $name;
    $selectedValue = old($name, $value);
    if ($allowClear === null) {
        $allowClear = false;
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
    @if ($ajax) data-country-select-ajax="1" @else data-country-select="1" @endif
    data-placeholder="{{ $placeholder }}"
    data-allow-clear="{{ $allowClear ? '1' : '0' }}"
    @if ($dropdownParent) data-mc-dropdown-parent="{{ $dropdownParent }}" @endif
    @if ($required) required @endif
    {{ $attributes->class(['mc-input', 'form-control', 'select2-country-select']) }}
    style="width: 100%;"
>
    @if ($emptyOption)
        <option value="">{{ $emptyLabel }}</option>
    @endif
    @if ($ajax && filled($selectedValue))
        <option value="{{ $selectedValue }}" selected>{{ $selectedText ?: $selectedValue }}</option>
    @endif
    @unless ($ajax)
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
    @endunless
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

                function resolveCountryFlagFromState(state) {
                    if (state.flag_url && String(state.flag_url).indexOf('http') === 0) {
                        return state.flag_url;
                    }

                    if (state.iso && String(state.iso).length <= 3) {
                        return 'https://flagcdn.com/w20/' + String(state.iso).toLowerCase() + '.png';
                    }

                    return resolveCountryFlagUrl($(state.element));
                }

                function formatCountrySelect(state) {
                    if (!state.id) {
                        return state.text;
                    }

                    var flagUrl = resolveCountryFlagFromState(state);
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

                function resolveCountryDropdownParent($select) {
                    var dropdownParent = $select.attr('data-mc-dropdown-parent') || $select.attr('data-dropdown-parent');
                    $select.removeAttr('data-dropdown-parent');
                    $select.removeData('dropdownParent');

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

                    return ($parent && $parent.length) ? $parent : $(document.body);
                }

                window.MarineCaddieInitCountrySelect = function (scope) {
                    var $scope = scope ? $(scope) : $(document);

                    $scope.find('[data-country-select]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        var options = {
                            placeholder: $select.attr('data-placeholder') || 'Select Country',
                            allowClear: false,
                            width: '100%',
                            templateResult: formatCountrySelect,
                            templateSelection: formatCountrySelect
                        };

                        options.dropdownParent = resolveCountryDropdownParent($select);

                        if ($select.attr('id') === 'country_of_origin' && $('#country-of-origin-host').length) {
                            return;
                        }

                        $select.select2(options);
                        var s2 = $select.data('select2');
                        if (s2 && s2.options && typeof s2.options.set === 'function') {
                            s2.options.set('dropdownParent', options.dropdownParent);
                        }
                        $select.next('.select2.select2-container').css('width', '100%');

                        $select.off('change.countrySelectValidation').on('change.countrySelectValidation', function () {
                            if ($(this).hasClass('error')) {
                                $(this).next('.select2-container').addClass('error');
                            } else {
                                $(this).next('.select2-container').removeClass('error');
                            }
                        });
                    });

                    $scope.find('[data-country-select-ajax]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        var options = {
                            placeholder: $select.attr('data-placeholder') || 'Select Country',
                            allowClear: false,
                            width: '100%',
                            minimumInputLength: 0,
                            templateResult: formatCountrySelect,
                            templateSelection: formatCountrySelect,
                            ajax: {
                                url: @json(route('api.countries')),
                                dataType: 'json',
                                delay: 200,
                                data: function (params) {
                                    return { q: params.term || '' };
                                },
                                processResults: function (data) {
                                    return { results: data.results || [] };
                                },
                                cache: true
                            }
                        };

                        options.dropdownParent = resolveCountryDropdownParent($select);

                        $select.select2(options);
                        var s2Ajax = $select.data('select2');
                        if (s2Ajax && s2Ajax.options && typeof s2Ajax.options.set === 'function') {
                            s2Ajax.options.set('dropdownParent', options.dropdownParent);
                        }
                        $select.next('.select2.select2-container').css('width', '100%');
                    });
                };

                $(document).ready(function () {
                    window.MarineCaddieInitCountrySelect();
                });
            })();
        </script>
    @endpush
@endonce
