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
    data-port-select="1"
    data-placeholder="{{ $placeholder }}"
    data-allow-clear="{{ $allowClear ? '1' : '0' }}"
    @if ($required) required @endif
    {{ $attributes->class(['form-control', 'select2-port-code']) }}
    style="width: 100%;"
>
    <option value=""></option>
    @if ($selectedValue)
        <option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>
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

                window.MarineCaddieInitPortSelect = function () {
                    function formatPortResult(port) {
                        if (port.loading || !port.id) {
                            return port.text;
                        }

                        var title = port.code + (port.city ? ', ' + port.city : '');
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

                        return port.code
                            ? (port.code + (port.city ? ', ' + port.city : ''))
                            : port.text;
                    }

                    $('[data-port-select]').each(function () {
                        var $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        $select.select2({
                            placeholder: $select.data('placeholder') || 'Search port code',
                            allowClear: String($select.data('allowClear')) === '1',
                            width: '100%',
                            minimumInputLength: 0,
                            ajax: {
                                url: @json(route('api.ports')),
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

                $(document).ready(window.MarineCaddieInitPortSelect);
            })();
        </script>
    @endpush
@endonce
