{{-- Shared Pcoded shell open. Optional: $pageWrapperClass (e.g. "p-0") --}}
@php
    $pageWrapperClass = trim((string) ($pageWrapperClass ?? ''));
@endphp
<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">
        @include('layouts.top-menu')
        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                @include('layouts.left-menu')
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <div class="main-body">
                            <div class="page-wrapper{{ $pageWrapperClass !== '' ? ' ' . $pageWrapperClass : '' }}">
                                <div class="page-body">
