{{-- Inner HTML for fixed list pagination footers (meta always visible; links when multi-page). --}}
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator $paginator */
@endphp
<div class="list-pagination-meta">
    @if ($paginator->total() > 0)
        <strong>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</strong>
    @else
        <strong>No results</strong>
    @endif
</div>
<div class="list-pagination-links">
    @if ($paginator->hasPages())
        {{ $paginator->links('pagination::simple-bootstrap-5') }}
    @endif
</div>
