@extends('layouts.guest')

@section('styles')
    @include('partials.auth-guest-page-styles')
@endsection

@section('content')
    <div class="login-page">
        <div class="login-panel">
            <div class="login-panel-inner">
                <div class="login-brand">
                    {!! \App\Support\LogoHelper::imgTag('220px') !!}
                </div>

                <div class="login-card">
                    <div class="login-card-head">
                        <h1 class="login-card-title">Forgot your password?</h1>
                        <p class="login-card-subtitle">
                            Enter your email address and we will send you a secure password reset link so you can regain access to your account.
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="login-alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group-custom">
                            <label for="email" class="field-label">Email address</label>
                            <div class="field-input-wrap">
                                <i class="ti-email field-input-icon" aria-hidden="true"></i>
                                <input id="email" type="email" name="email"
                                    class="field-input @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus
                                    placeholder="Enter your registered email">
                            </div>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-login">Send password reset link</button>

                        <div class="form-footer">
                            <span class="form-footer-hint">Remembered your password?</span>
                            <a href="{{ route('login') }}" class="forgot-link">Back to login</a>
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
                    <i class="ti-key"></i>
                    Account recovery
                </div>
                <h2 class="login-hero-title">
                    Recover access to<br><span>MarineCaddie</span>
                </h2>
                <p class="login-hero-text">
                    Keep your logistics operations moving with secure account recovery designed for speed, clarity, and safe access control.
                </p>
                <ul class="login-hero-features">
                    <li>
                        <i class="ti-email"></i>
                        <span>Reset links sent to your registered email only</span>
                    </li>
                    <li>
                        <i class="ti-timer"></i>
                        <span>Fast account recovery with secure verification</span>
                    </li>
                    <li>
                        <i class="ti-shield"></i>
                        <span>Same trusted access flow for every user</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
