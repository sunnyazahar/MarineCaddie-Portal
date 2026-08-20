@props([
    'toggleId',
    'bodyClass' => 'list-filters-open',
    'toolbarClass' => 'list-filters-toolbar',
])

<div @class([$toolbarClass, 'list-filters-toolbar'])>
    <button
        type="button"
        id="{{ $toggleId }}"
        class="btn btn-outline-teal btn-sm list-filters-toggle"
        data-body-class="{{ $bodyClass }}"
    >
        <i class="ti-filter"></i>
        <span class="list-filters-toggle-label">Show filters</span>
    </button>

    @if (isset($actions))
        <div @class([$toolbarClass . '-actions', 'list-filters-toolbar-actions'])>
            {{ $actions }}
        </div>
    @endif
</div>

@once('lists.filter-toggle-script')
    @push('scripts')
        <script>
            (function () {
                if (window.MarineCaddieInitListFilterToggle) {
                    return;
                }

                window.MarineCaddieInitListFilterToggle = function () {
                    $(document).on('click', '.list-filters-toggle', function () {
                        var bodyClass = $(this).data('bodyClass') || 'list-filters-open';
                        $('body').toggleClass(bodyClass);
                        var isOpen = $('body').hasClass(bodyClass);
                        $(this).toggleClass('is-open', isOpen);
                        $(this).find('.list-filters-toggle-label').text(isOpen ? 'Hide filters' : 'Show filters');
                    });
                };

                $(document).ready(window.MarineCaddieInitListFilterToggle);
            })();
        </script>
    @endpush
@endonce
