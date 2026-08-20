@props([
    'toolbarClass' => '',
])

<div @class(['list-inline-toolbar', $toolbarClass])>
    <div class="list-inline-toolbar-search">
        {{ $search ?? $slot }}
    </div>

    @if (isset($actions))
        <div class="list-inline-toolbar-actions">
            {{ $actions }}
        </div>
    @endif
</div>
