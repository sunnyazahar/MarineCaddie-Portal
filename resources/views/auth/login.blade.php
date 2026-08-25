@extends('layouts.app')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif !important;
            background: #f1f5f9;
            overflow: hidden;
            color: #334155 !important;
        }

        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        /* Left Pane - Form */
        .login-left {
            flex: 1;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .login-logo {
            margin-bottom: 50px;
            text-align: center;
        }

        .login-logo img {
            height: 100px;
            width: auto;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 6px;
            padding: 45px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
        }

        .form-group-custom {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .field-input {
            width: 100%;
            height: 38px;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            transition: all 0.2s;
            background: #fff;
        }

        .btn-login {
            width: 100%;
            height: 42px;
            background: #FF5A5F;
            color: #FFFFFF;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 15px;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        .btn-login:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 12px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #94a3b8;
            cursor: pointer;
        }

        .forgot-link {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 35px;
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
        }

        .page-footer {
            position: absolute;
            bottom: 40px;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            width: 100%;
        }

        /* Right Pane - Visual */
        .login-right {
            flex: 1.5;
            background-image: url('{{ asset("files/assets/images/login_bg.png") }}');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 100px;
            overflow: hidden;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(14, 29, 74, 0.9) 0%, rgba(14, 29, 74, 0.7) 100%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            color: #FFFFFF;
            max-width: 500px;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .welcome-title b,
        .welcome-title strong {
            font-weight: 700;
        }

        .separator {
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 20px 0;
        }

        .welcome-text {
            font-size: 16px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
        }

        .btn-readmore {
            display: inline-block;
            padding: 10px 25px;
            border: 1px solid #FFFFFF;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-readmore:hover {
            background: #FFFFFF;
            color: #0E1D4A;
        }

        /* Decorative squares */
        .decor-square {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(56, 189, 248, 0.5);
            border-radius: 8px;
            z-index: 1;
        }

        .decor-top-right {
            top: 40px;
            right: 40px;
            border-color: rgba(56, 189, 248, 0.4);
        }

        .decor-bottom-left {
            bottom: -20px;
            left: 20%;
            width: 80px;
            height: 80px;
            border-color: rgba(255, 77, 77, 0.5);
            transform: rotate(45deg);
            z-index: 1;
        }

        .decor-top-left {
            top: -20px;
            left: 10%;
            width: 60px;
            height: 60px;
            border-color: rgba(34, 197, 94, 0.5);
            transform: rotate(-15deg);
        }

        @media (max-width: 991px) {
            .login-right {
                display: none;
            }

            .login-left {
                flex: 1;
            }
        }

        /* Error handling styles */
        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #FF4D4D;
            margin-top: 5px;
        }

        .field-input.is-invalid {
            border-color: #FF4D4D;
        }
    </style>
@endsection

@section('content')
    <div class="login-wrapper">
        <!-- Left Side: Login Form -->
        <div class="login-left">
            <div class="login-logo">
                {!! \App\Support\LogoHelper::imgTag('260px') !!}
            </div>

            <div class="login-card">
                @if (session('status'))
                    <div class="invalid-feedback" style="margin-bottom: 14px; color: #0f766e;">{{ session('status') }}</div>
                @endif
                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" id="screen-resolution" name="screen_resolution">
                    <input type="hidden" id="browser-language" name="browser_language">
                    <input type="hidden" id="browser-timezone" name="browser_timezone">

                    <div class="form-group-custom">
                        <label class="field-label">User Name</label>
                        <input type="text" name="email" class="field-input @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group-custom">
                        <label class="field-label">Password</label>
                        <input type="password" name="password" class="field-input @error('password') is-invalid @enderror"
                            required placeholder="Enter your password">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" id="login-button" class="btn-login">Log In</button>

                    <div class="form-footer">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Remember Me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        @endif
                    </div>

                </form>
            </div>

            <div class="page-footer">
                <span style="font-size: 12px; font-weight: 500; color:#000000">© 2026 MarineCaddie, inc. All rights
                    reserved. | <a href="#" class="mc-privacy-link" data-mc-privacy-open>Privacy Policy</a></span>
            </div>
        </div>

        <!-- Right Side: Branding -->
        <div class="login-right">
            <div class="overlay"></div>

            <div class="visual-content">
                <h1 class="welcome-title">Welcome to <b>MarineCaddie</b></h1>
                <div class="separator"></div>
                <p class="welcome-text">
                    MarineCaddie transforms logistics with smart, technology-driven solutions ensuring real-time visibility,
                    efficient handling, and reliable delivery of your cargo worldwide.
                </p>
                <a href="https://www.marinecaddie.com/" class="btn-readmore" target="_blank" rel="noopener noreferrer">Read more ..</a>
            </div>
        </div>
    </div>

    @include('partials.privacy-policy-modal')

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