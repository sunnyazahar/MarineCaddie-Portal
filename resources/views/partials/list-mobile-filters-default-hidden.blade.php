{{-- Mobile: all list filter panels hidden until user taps Show filters (body.*-filters-open). --}}
@once('list-mobile-filters-default-hidden')
    @push('styles')
        <style>
            @media (max-width: 991.98px) {
                [class*="-filters-fields"] {
                    display: none !important;
                }

                body[class*="-filters-open"] [class*="-filters-fields"] {
                    display: flex !important;
                    flex-direction: column;
                    max-height: 42vh;
                    overflow-x: hidden;
                    overflow-y: auto;
                    -webkit-overflow-scrolling: touch;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function () {
                if (window.MarineCaddieResetMobileListFilters) {
                    return;
                }

                window.MarineCaddieResetMobileListFilters = function () {
                    if (!window.matchMedia || !window.matchMedia('(max-width: 991.98px)').matches) {
                        return;
                    }

                    document.body.classList.forEach(function (className) {
                        if (className.indexOf('-filters-open') !== -1) {
                            document.body.classList.remove(className);
                        }
                        if (className.indexOf('-filters-collapsed') !== -1) {
                            document.body.classList.remove(className);
                        }
                    });

                    $('[class*="-filters-fields"]').removeClass('is-collapsed');

                    $('.list-filters-toggle, [id$="-filters-toggle"]').each(function () {
                        $(this)
                            .removeClass('is-open is-collapsed')
                            .find('[class*="-filters-toggle-label"], .list-filters-toggle-label')
                            .text('Show filters');
                    });
                };

                $(document).ready(window.MarineCaddieResetMobileListFilters);
                $(window).on('resize.marineCaddieMobileFilters', function () {
                    if (window.matchMedia('(max-width: 991.98px)').matches) {
                        window.MarineCaddieResetMobileListFilters();
                    }
                });
            })();
        </script>
    @endpush
@endonce
