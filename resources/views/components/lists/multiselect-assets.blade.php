@once('lists.multiselect-assets')
    @include('partials.list-mobile-filters-default-hidden')

    @push('styles')
        @include('partials.searchable-filter-multiselect-styles')
    @endpush

    @push('scripts')
        @include('partials.searchable-filter-multiselect-script')
    @endpush
@endonce
