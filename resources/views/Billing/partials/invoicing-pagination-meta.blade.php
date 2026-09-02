@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator $paginator */
@endphp
@if ($paginator->total() > 0)
    <strong>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</strong>
@else
    <strong>No results</strong>
@endif
