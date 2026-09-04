@props([
    'name' => 'port_code',
    'id' => null,
    'value' => null,
    'label' => null,
    'labelClass' => 'form-label-custom',
    'wrapperClass' => 'form-group-custom',
    'placeholder' => 'Search port code',
    'allowClear' => false,
    'required' => false,
])

@php
    $fieldId = $id ?? $name;
    $selectedValue = old($name, $value);
    $selectedLabel = $selectedValue
        ? (\App\Models\Port::selectLabelForCode($selectedValue) ?: $selectedValue)
        : null;
@endphp

@if ($wrapperClass)
    <div @class([$wrapperClass])>
@endif

@if ($label)
    <label class="{{ $labelClass }}" for="{{ $fieldId }}">
        {{ $label }}@if ($required) <span class="text-danger">*</span>@endif
    </label>
@endif

<select
    id="{{ $fieldId }}"
    name="{{ $name }}"
    data-port-select="1"
    data-placeholder="{{ $placeholder }}"
    data-allow-clear="{{ $allowClear ? '1' : '0' }}"
    @if ($required) required @endif
    {{ $attributes->class(['mc-input', 'form-control', 'select2-port-code']) }}
    style="width: 100%;"
>
    <option value=""></option>
    @if ($selectedValue)
        <option value="{{ $selectedValue }}" selected>{{ $selectedLabel }}</option>
    @endif
</select>

@if ($wrapperClass)
    </div>
@endif

@once('forms.port-select-init')
    @push('scripts')
        <script>
            (function () {
                if (window.MarineCaddieInitPortSelect) {
                    return;
                }

                window.MarineCaddiePortsApiUrl = @json(route('api.ports'));

                window.MarineCaddieNormalizePortCity = function (city) {
                    city = String(city || '').trim();
                    if (!city) {
                        return '';
                    }

                    var paren = city.match(/\(([^)]+)\)/);
                    if (paren && String(paren[1] || '').trim() !== '') {
                        return String(paren[1]).trim();
                    }

                    if (city.indexOf(',') !== -1) {
                        city = city.split(',', 2)[0].trim();
                    }

                    if (city.indexOf(' - ') !== -1) {
                        city = city.split(' - ', 2)[0].trim();
                    }

                    return city;
                };

                window.MarineCaddieFormatPortLabel = function (code, city) {
                    code = String(code || '').trim();
                    if (!code) {
                        return '';
                    }

                    city = window.MarineCaddieNormalizePortCity(city);
                    return city ? (code + ', ' + city) : code;
                };

                window.MarineCaddieSetPortCodeSelect = function ($select, code) {
                    code = $.trim((code || '').toString());
                    if (!$select || !$select.length) {
                        return;
                    }

                    if (!code) {
                        $select.val(null).trigger('change');
                        return;
                    }

                    function applyOption(label) {
                        label = String(label || code).trim() || code;
                        var $existing = $select.find('option').filter(function () {
                            return $(this).val() === code;
                        });

                        if ($existing.length === 0) {
                            $select.append(new Option(label, code, true, true));
                        } else {
                            $existing.text(label).prop('selected', true);
                        }

                        $select.val(code).trigger('change');
                    }

                    var $match = $select.find('option').filter(function () {
                        return $(this).val() === code;
                    }).first();
                    if ($match.length && String($match.text() || '').indexOf(',') !== -1) {
                        applyOption($match.text());
                        return;
                    }

                    $.getJSON(window.MarineCaddiePortsApiUrl, { q: code })
                        .done(function (data) {
                            var hit = (data.results || []).find(function (row) {
                                return row.id === code || row.code === code;
                            });

                            if (hit) {
                                applyOption(
                                    hit.text || window.MarineCaddieFormatPortLabel(hit.code || code, hit.city)
                                );
                                return;
                            }

                            applyOption(code);
                        })
                        .fail(function () {
                            applyOption(code);
                        });
                };

                window.MarineCaddieInitPortSelect = function () {
                    function formatPortResult(port) {
                        if (port.loading || !port.id) {
                            return port.text;
                        }

                        var title = window.MarineCaddieFormatPortLabel(port.code || port.id, port.city);
                        if (!title) {
                            title = port.text;
                        }

                        var $option = $(
                            '<div class="port-option">' +
                                '<div style="font-weight: 600; font-size: 13px; color: #111827;"></div>' +
                                '<div style="font-size: 12px; color: #6b7280;"></div>' +
                            '</div>'
                        );
                        $option.find('div').eq(0).text(title);
                        $option.find('div').eq(1).text(port.country || '');

                        return $option;
                    }

                    function formatPortSelection(port) {
                        if (!port.id) {
                            return port.text;
                        }

                        if (port.code || port.city) {
                            return window.MarineCaddieFormatPortLabel(port.code || port.id, port.city);
                        }

                        return port.text;
                    }

                    $('[data-port-select]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            $select.select2('destroy');
                        }

                        $select.select2({
                            placeholder: $select.data('placeholder') || 'Search port code',
                            allowClear: String($select.data('allowClear')) === '1',
                            width: '100%',
                            minimumInputLength: 0,
                            ajax: {
                                url: window.MarineCaddiePortsApiUrl,
                                dataType: 'json',
                                delay: 200,
                                data: function (params) {
                                    return { q: params.term || '' };
                                },
                                processResults: function (data) {
                                    return { results: data.results || [] };
                                },
                                cache: true
                            },
                            templateResult: formatPortResult,
                            templateSelection: formatPortSelection
                        });
                    });
                };

                $(document).ready(function () {
                    setTimeout(window.MarineCaddieInitPortSelect, 0);
                });
            })();
        </script>
    @endpush
@endonce
