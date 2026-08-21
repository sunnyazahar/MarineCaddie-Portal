@props([
    'tableId',
    'wrapperId' => null,
    'tableClass' => '',
    'paginationId' => null,
    'paginator' => null,
    'minWidth' => null,
    'headTemplateId' => null,
    'paginationClass' => 'mt-3 px-3 pb-2 list-ajax-pagination',
])

@php
    $wrapperId = $wrapperId ?? ($tableId . '-wrapper');
@endphp

@if ($headTemplateId && isset($headTemplate))
    <template id="{{ $headTemplateId }}">
        <thead>
            {{ $headTemplate }}
        </thead>
    </template>
@endif

<div id="{{ $wrapperId }}" {{ $attributes->class(['table-responsive', 'list-ajax-table-wrapper'])->merge(['style' => 'padding: 0;']) }}>
    <table
        id="{{ $tableId }}"
        @class([$tableClass])
        @if($minWidth) style="min-width: {{ $minWidth }};" @endif
    >
        @isset($head)
            <thead>
                {{ $head }}
            </thead>
        @endisset
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

@if ($paginationId)
    <div id="{{ $paginationId }}" @class([$paginationClass, 'list-ajax-pagination'])>
        {!! $paginator ?? '' !!}
    </div>
@endif
