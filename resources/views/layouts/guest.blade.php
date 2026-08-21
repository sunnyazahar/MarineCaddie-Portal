<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    @yield('styles')
    @stack('styles')
</head>

<body class="guest-layout">
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>
