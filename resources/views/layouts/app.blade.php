<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=source-sans-3:400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('files/assets/images/favicon.png') }}?v=3">
    <link rel="shortcut icon" type="image/png" href="{{ asset('files/assets/images/favicon.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('files/assets/images/favicon.png') }}?v=3">

    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/feather/css/feather.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.common-assets-styles')

    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>

    @include('partials.stock-status-badge-styles')
    @yield('styles')
    @stack('styles')
    {{-- After page styles so every calendar matches (readable size + brand colors) --}}
    @include('partials.datepicker-styles')
    {{-- After page styles so filter label colors always win --}}
    @include('partials.filter-chrome-styles')
    {{-- After page styles so form control/label font sizes stay readable + consistent --}}
    @include('partials.form-control-typography')
    {{-- After page styles so 100vh list shells cannot clip mobile header/footer --}}
    @include('partials.mobile-chrome-safe')
</head>

<body class="{{ auth()->user()?->isOperations() && request()->routeIs('offices.*', 'hub.*', 'agents.*', 'customers.*', 'contacts.*') ? 'ops-admin-readonly' : '' }}" data-mc-user-id="{{ auth()->id() ?? 'guest' }}">
    @include('layouts.partials.administration-readonly')
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>

    @include('partials.mail-busy-overlay')

    @include('partials.common-assets-scripts')
    @stack('scripts')

    @if(app()->environment('production') && ! config('app.debug'))
        <script>
            document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
            document.addEventListener('keydown', function (e) {
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) ||
                    (e.ctrlKey && e.key.toUpperCase() === 'U') ||
                    (e.metaKey && e.altKey && ['I', 'J', 'C'].includes(e.key.toUpperCase()))
                ) {
                    e.preventDefault();
                    return false;
                }
            });
        </script>
    @endif
</body>

</html>
