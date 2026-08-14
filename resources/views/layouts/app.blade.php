<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" href="{{ asset('files/assets/images/favicon.png') }}?v=3">
    <link rel="shortcut icon" type="image/png" href="{{ asset('files/assets/images/favicon.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('files/assets/images/favicon.png') }}?v=3">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css"
        href="{{asset('files/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/icon/themify-icons/themify-icons.css')}}">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/icon/icofont/css/icofont.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/css/style.css')}}">

    <!-- Required Fremwork -->
    <!-- radial chart.css -->
    <link rel="stylesheet" href="{{asset('files/assets/pages/chart/radial/css/radial.css')}}" type="text/css"
        media="all">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/icon/feather/css/feather.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/css/jquery.mCustomScrollbar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('files/assets/css/responsive-app.css')}}">
    <style>
        body {
            color: rgb(24, 100, 131) !important;
        }

        td a {
            color: rgb(24, 100, 131) !important;
        }

        td a:hover {
            color: rgb(24, 100, 131) !important;
        }

        .heading-route {
            font-size: 16px;
            font-weight: 600;
            margin-left: 10px;
        }

        .app-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-left: 10px;
            font-size: 14px;
            font-weight: 600;
            color: rgb(24, 100, 131);
            line-height: 1.2;
        }

        .app-breadcrumb-sep {
            color: #94a3b8;
            font-weight: 500;
        }

        .app-breadcrumb-link {
            color: rgb(24, 100, 131) !important;
            text-decoration: none;
        }

        .app-breadcrumb-link:hover {
            text-decoration: underline;
            color: #006666 !important;
        }

        .app-breadcrumb-current {
            color: #1f2937;
            font-weight: 700;
        }

        .header-navbar .navbar-wrapper .navbar-logo {
            width: 240px;
            padding-right: 42px;
        }

        .header-navbar .navbar-wrapper .navbar-logo .navbar-logo-link {
            display: inline-flex !important;
            align-items: center;
            max-width: 160px;
        }

        .header-navbar .navbar-wrapper .navbar-logo .navbar-logo-link img,
        .header-navbar .navbar-wrapper .navbar-logo img.marinecaddie-logo {
            display: block !important;
            width: 150px !important;
            max-width: 150px !important;
            height: 36px !important;
            object-fit: contain;
        }
    </style>
    @include('partials.stock-status-badge-styles')
    @yield('styles')
</head>

<body class="{{ auth()->user()?->isOperations() && request()->routeIs('offices.*', 'hub.*', 'agents.*', 'customers.*', 'contacts.*') ? 'ops-admin-readonly' : '' }}">
    @include('layouts.partials.administration-readonly')
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>

    @include('partials.mail-busy-overlay')

    @if(app()->environment('production') && ! config('app.debug'))
        <script>
            // Soft deterrent only — not real security. Skipped when APP_DEBUG=true.
            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
            });

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