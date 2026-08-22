@extends('layouts.guest')

@section('styles')
    @include('partials.auth-guest-page-styles')
    <style>
        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--mc-muted);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            accent-color: var(--mc-teal);
            cursor: pointer;
        }

        .location-card {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
        }

        .location-status {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #b45309;
            line-height: 1.45;
        }

        .location-status i {
            flex-shrink: 0;
            margin-top: 2px;
            font-size: 16px;
        }

        .location-status.is-ready {
            color: #15803d;
        }

        .btn-location {
            margin-top: 8px;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--mc-teal);
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .btn-location:hover {
            color: var(--mc-teal-dark);
        }
    </style>
@endsection

@section('content')
    <div class="login-page">
        <div class="login-panel">
            <div class="login-panel-inner">
                <div class="login-brand">
                    {!! \App\Support\LogoHelper::imgTag('220px') !!}
                    <p class="login-brand-tagline">Maritime logistics portal</p>
                </div>

                <div class="login-card">
                    <div class="login-card-head">
                        <h1 class="login-card-title">Welcome back</h1>
                        <p class="login-card-subtitle">Sign in to manage shipments, stock, and operations.</p>
                    </div>

                    @if (session('status'))
                        <div class="login-alert-success">{{ session('status') }}</div>
                    @endif

                    <form id="login-form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" id="browser-latitude" name="browser_latitude">
                        <input type="hidden" id="browser-longitude" name="browser_longitude">
                        <input type="hidden" id="browser-location-accuracy" name="browser_location_accuracy">
                        <input type="hidden" id="screen-resolution" name="screen_resolution">
                        <input type="hidden" id="browser-language" name="browser_language">
                        <input type="hidden" id="browser-timezone" name="browser_timezone">

                        <div class="form-group-custom">
                            <label class="field-label" for="login-email">User name</label>
                            <div class="field-input-wrap">
                                <i class="ti-email field-input-icon" aria-hidden="true"></i>
                                <input type="text" id="login-email" name="email"
                                    class="field-input @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                            </div>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-custom">
                            <label class="field-label" for="login-password">Password</label>
                            <div class="field-input-wrap">
                                <i class="ti-lock field-input-icon" aria-hidden="true"></i>
                                <input type="password" id="login-password" name="password"
                                    class="field-input @error('password') is-invalid @enderror"
                                    required placeholder="Enter your password">
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="location-card">
                            <div id="location-status" class="location-status">
                                <i class="ti-location-pin" aria-hidden="true"></i>
                                <span>Location permission is required to log in.</span>
                            </div>
                            <button type="button" id="request-location-button" class="btn-location">
                                Enable location
                            </button>
                            @error('browser_latitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" id="login-button" class="btn-login" disabled>Log in</button>

                        <div class="form-footer">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                Remember me
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="login-footer">
                © {{ date('Y') }} MarineCaddie, Inc. All rights reserved.
                <span aria-hidden="true"> · </span>
                <a href="#">Privacy policy</a>
            </div>
        </div>

        <aside class="login-hero" aria-hidden="true">
            <div class="login-hero-overlay"></div>

            <div class="login-hero-content">
                <div class="login-hero-badge">
                    <i class="ti-ship"></i>
                    Global logistics
                </div>
                <h2 class="login-hero-title">
                    Smart shipping for<br><span>modern maritime ops</span>
                </h2>
                <p class="login-hero-text">
                    MarineCaddie gives your team real-time visibility, efficient cargo handling,
                    and reliable delivery workflows — from pre-alert to final mile.
                </p>
                <ul class="login-hero-features">
                    <li>
                        <i class="ti-eye"></i>
                        <span>Track shipments and stock in one place</span>
                    </li>
                    <li>
                        <i class="ti-package"></i>
                        <span>Coordinate hubs, agents, and warehouse deadlines</span>
                    </li>
                    <li>
                        <i class="ti-world"></i>
                        <span>Operate worldwide with confidence and control</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginButton = document.getElementById('login-button');
            var locationButton = document.getElementById('request-location-button');
            var locationStatus = document.getElementById('location-status');
            var locationStatusText = locationStatus.querySelector('span');

            document.getElementById('screen-resolution').value =
                window.screen.width + 'x' + window.screen.height;
            document.getElementById('browser-language').value =
                navigator.language || '';
            document.getElementById('browser-timezone').value =
                Intl.DateTimeFormat().resolvedOptions().timeZone || '';

            function setLocationMessage(message, ready) {
                locationStatus.classList.toggle('is-ready', !!ready);
                if (locationStatusText) {
                    locationStatusText.textContent = message;
                } else {
                    locationStatus.textContent = message;
                }
            }

            function requestLocation() {
                setLocationMessage('Requesting your location...', false);

                if (!navigator.geolocation) {
                    setLocationMessage('This browser does not support location access. Login is unavailable.', false);
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('browser-latitude').value = position.coords.latitude;
                    document.getElementById('browser-longitude').value = position.coords.longitude;
                    document.getElementById('browser-location-accuracy').value = position.coords.accuracy;
                    setLocationMessage('Location permission granted.', true);
                    locationButton.style.display = 'none';
                    loginButton.disabled = false;
                }, function(error) {
                    loginButton.disabled = true;
                    locationButton.style.display = 'inline-block';
                    setLocationMessage(
                        error.code === error.PERMISSION_DENIED
                            ? 'Location permission was denied. Please enable it to log in.'
                            : 'Your location could not be detected. Please try again.',
                        false
                    );
                }, {
                    enableHighAccuracy: false,
                    timeout: 10000,
                    maximumAge: 300000
                });
            }

            locationButton.addEventListener('click', requestLocation);
            requestLocation();

            var loginForm = document.getElementById('login-form');
            var csrfRefreshing = false;
            loginForm.addEventListener('submit', function(e) {
                if (csrfRefreshing) {
                    return;
                }
                e.preventDefault();
                csrfRefreshing = true;
                loginButton.disabled = true;
                fetch(@json(url('/login/csrf')), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).then(function(response) {
                    return response.ok ? response.json() : null;
                }).then(function(data) {
                    if (data && data.token) {
                        var tokenInput = loginForm.querySelector('input[name="_token"]');
                        if (tokenInput) {
                            tokenInput.value = data.token;
                        }
                    }
                }).catch(function() {
                    // Continue with the existing token if refresh fails.
                }).finally(function() {
                    loginForm.submit();
                });
            });
        });
    </script>
@endsection
