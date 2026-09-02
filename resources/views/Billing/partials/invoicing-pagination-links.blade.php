@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator $paginator */
@endphp
@if ($paginator->hasPages())
    {{ $paginator->links('pagination::simple-bootstrap-5') }}
@endif
