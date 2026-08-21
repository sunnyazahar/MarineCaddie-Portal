@extends('layouts.guest')

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

        .login-left {
            flex: 1;
            background: #fff;
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

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 6px;
            padding: 45px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .auth-subtitle {
            font-size: 14px;
            line-height: 1.7;
            color: #64748b;
            margin-bottom: 24px;
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
            height: 42px;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            transition: all 0.2s;
            background: #fff;
        }

        .field-input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
        }

        .btn-login {
            width: 100%;
            height: 42px;
            background: #FF5A5F;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 10px;
        }

        .btn-login:hover {
            opacity: 0.92;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 18px;
            font-size: 12px;
            gap: 12px;
        }

        .forgot-link {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
        }

        .page-footer {
            position: absolute;
            bottom: 40px;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            width: 100%;
        }

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
            inset: 0;
            background: linear-gradient(135deg, rgba(14, 29, 74, 0.9) 0%, rgba(14, 29, 74, 0.7) 100%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            color: #fff;
            max-width: 520px;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .welcome-title b {
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
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 18px;
        }

        .hero-points {
            display: grid;
            gap: 12px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.82);
        }

        .hero-point {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hero-point span {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #38bdf8;
            flex-shrink: 0;
        }

        .status-box {
            margin-bottom: 16px;
            font-size: 13px;
            border-radius: 4px;
            padding: 10px 12px;
        }

        .status-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
        }

        .field-input.is-invalid {
            border-color: #ef4444;
        }

        @media (max-width: 991px) {
            .login-right {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="login-wrapper">
        <div class="login-left">
            <div class="login-logo">
                {!! \App\Support\LogoHelper::imgTag('260px') !!}
            </div>

            <div class="login-card">
                <h1 class="auth-title">Forgot your password?</h1>
                <p class="auth-subtitle">
                    Enter your email address and we will send you a secure password reset link so you can regain access to your account.
                </p>

                @if (session('status'))
                    <div class="status-box status-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group-custom">
                        <label for="email" class="field-label">Email Address</label>
                        <input id="email" type="email" name="email"
                            class="field-input @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="Enter your registered email">

                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">Send Password Reset Link</button>

                    <div class="form-footer">
                        <span style="color:#94a3b8;">Remembered your password?</span>
                        <a href="{{ route('login') }}" class="forgot-link">Back to login</a>
                    </div>
                </form>
            </div>

            <div class="page-footer">
                <span style="font-size: 12px; font-weight: 500; color:#000000">© 2026 MarineCaddie, inc. All rights
                    reserved. | <a href="#">Privacy Policy</a></span>
            </div>
        </div>

        <div class="login-right">
            <div class="overlay"></div>

            <div class="visual-content">
                <h1 class="welcome-title">Recover access to <b>MarineCaddie</b></h1>
                <div class="separator"></div>
                <p class="welcome-text">
                    Keep your logistics operations moving with secure account recovery designed for speed, clarity, and safe access control.
                </p>
                <div class="hero-points">
                    <div class="hero-point"><span></span> Reset links sent to your registered email only</div>
                    <div class="hero-point"><span></span> Fast account recovery with secure verification</div>
                    <div class="hero-point"><span></span> Same trusted access flow for every user</div>
                </div>
            </div>
        </div>
    </div>
@endsection
