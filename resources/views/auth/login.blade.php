@extends('layouts.guest')

@section('styles')
    @include('partials.auth-guest-page-styles')
    <style>
        .login-panel--signin {
            background:
                radial-gradient(ellipse 70% 50% at 0% 0%, rgba(0, 128, 128, 0.11), transparent 55%),
                radial-gradient(ellipse 55% 45% at 100% 15%, rgba(14, 29, 74, 0.07), transparent 50%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(0, 174, 239, 0.08), transparent 45%),
                linear-gradient(180deg, #f4f8fb 0%, #eef4f8 48%, #f8fafc 100%);
            overflow: hidden;
        }

        .login-panel--signin::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.4;
            background-image:
                linear-gradient(rgba(14, 29, 74, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(14, 29, 74, 0.035) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 75% 70% at 50% 40%, #000 20%, transparent 75%);
        }

        .login-panel--signin .login-panel-inner,
        .login-panel--signin .login-footer {
            position: relative;
            z-index: 1;
        }

        .login-panel--signin .login-brand {
            margin-bottom: 32px;
            animation: loginFadeUp 0.55s ease both;
        }

        .login-panel--signin .login-brand .marinecaddie-logo {
            max-height: 78px !important;
            max-width: 240px !important;
            filter: drop-shadow(0 8px 18px rgba(14, 29, 74, 0.1));
        }

        .login-panel--signin .login-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 18px;
            padding: 34px 30px 30px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            box-shadow:
                0 1px 0 rgba(255, 255, 255, 0.9) inset,
                0 10px 28px rgba(14, 29, 74, 0.07),
                0 28px 56px rgba(14, 29, 74, 0.06);
            animation: loginFadeUp 0.65s ease 0.06s both;
        }

        .login-panel--signin .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FF5A5F 0%, #38bdf8 55%, #008080 100%);
        }

        .login-panel--signin .login-card-head {
            margin-bottom: 26px;
        }

        .login-panel--signin .login-card-title {
            font-size: 26px;
            letter-spacing: -0.03em;
        }

        .login-panel--signin .login-card-subtitle {
            font-size: 14.5px;
            line-height: 1.5;
        }

        .login-panel--signin .field-input {
            height: 48px;
            border-radius: 12px;
            background: #fbfdff;
        }

        .login-panel--signin .field-input:focus {
            background: #fff;
            border-color: #008080;
            box-shadow: 0 0 0 4px rgba(0, 128, 128, 0.12);
        }

        .login-panel--signin .field-input-wrap:focus-within .field-input-icon {
            color: #008080;
        }

        .login-panel--signin .btn-login {
            height: 48px;
            margin-top: 20px;
            border-radius: 12px;
            background: linear-gradient(135deg, #FF5A5F 0%, #ff7a7e 55%, #ff8f70 100%);
            box-shadow: 0 12px 24px rgba(255, 90, 95, 0.28);
            letter-spacing: 0.03em;
        }

        .login-panel--signin .btn-login:hover:not(:disabled) {
            box-shadow: 0 14px 28px rgba(255, 90, 95, 0.34);
        }

        .login-panel--signin .form-footer {
            margin-top: 20px;
            padding-top: 4px;
        }

        .login-secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin: 18px 0 0;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            animation: loginFadeUp 0.7s ease 0.12s both;
        }

        .login-secure-note i {
            font-size: 13px;
            color: #008080;
        }

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

        .login-hero-title {
            font-weight: 300;
        }

        .login-hero-title b,
        .login-hero-title strong {
            font-weight: 700;
            color: #fff;
            background: none;
            -webkit-background-clip: unset;
            background-clip: unset;
        }

        .login-hero-separator {
            width: 100%;
            max-width: 420px;
            height: 1px;
            margin: 0 0 20px;
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-readmore {
            display: inline-block;
            padding: 10px 25px;
            border: 1px solid #FFFFFF;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-readmore:hover {
            background: #FFFFFF;
            color: #0E1D4A;
            text-decoration: none;
        }

        @keyframes loginFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-panel--signin .login-brand,
            .login-panel--signin .login-card,
            .login-secure-note {
                animation: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="login-page">
        <div class="login-panel login-panel--signin">
            <div class="login-panel-inner">
                <div class="login-brand">
                    {!! \App\Support\LogoHelper::imgTag('240px') !!}
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

                        <button type="submit" id="login-button" class="btn-login">Log in</button>

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

                <p class="login-secure-note">
                    <i class="ti-lock" aria-hidden="true"></i>
                    Secured login · OTP verification enabled
                </p>
            </div>

            <div class="login-footer">
                © {{ date('Y') }} MarineCaddie, Inc. All rights reserved.
                <span aria-hidden="true"> · </span>
                <a href="#">Privacy policy</a>
            </div>
        </div>

        <aside class="login-hero">
            <div class="login-hero-overlay"></div>

            <div class="login-hero-content">
                <h2 class="login-hero-title">Welcome to <b>MarineCaddie</b></h2>
                <div class="login-hero-separator" aria-hidden="true"></div>
                <p class="login-hero-text">
                    MarineCaddie transforms logistics with smart, technology-driven solutions ensuring real-time visibility,
                    efficient handling, and reliable delivery of your cargo worldwide.
                </p>
                <a href="https://www.marinecaddie.com/" class="btn-readmore" target="_blank" rel="noopener noreferrer">Read more ..</a>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginButton = document.getElementById('login-button');

            document.getElementById('screen-resolution').value =
                window.screen.width + 'x' + window.screen.height;
            document.getElementById('browser-language').value =
                navigator.language || '';
            document.getElementById('browser-timezone').value =
                Intl.DateTimeFormat().resolvedOptions().timeZone || '';

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
