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
                        <h1 class="login-card-title">Set a new password</h1>
                        <p class="login-card-subtitle">
                            Create a fresh password for your MarineCaddie account. Use a strong password that is easy for you to remember and hard for others to guess.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group-custom">
                            <label for="email" class="field-label">Email address</label>
                            <div class="field-input-wrap">
                                <i class="ti-email field-input-icon" aria-hidden="true"></i>
                                <input id="email" type="email" name="email"
                                    class="field-input @error('email') is-invalid @enderror"
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                                    placeholder="Enter your registered email">
                            </div>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-custom">
                            <label for="password" class="field-label">New password</label>
                            <div class="field-input-wrap">
                                <i class="ti-lock field-input-icon" aria-hidden="true"></i>
                                <input id="password" type="password" name="password"
                                    class="field-input @error('password') is-invalid @enderror"
                                    required autocomplete="new-password"
                                    placeholder="Enter your new password">
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-custom">
                            <label for="password-confirm" class="field-label">Confirm password</label>
                            <div class="field-input-wrap">
                                <i class="ti-lock field-input-icon" aria-hidden="true"></i>
                                <input id="password-confirm" type="password" name="password_confirmation"
                                    class="field-input" required autocomplete="new-password"
                                    placeholder="Re-enter your new password">
                            </div>
                        </div>

                        <button type="submit" class="btn-login">Reset password</button>

                        <div class="form-footer">
                            <span class="form-footer-hint">Need to sign in instead?</span>
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
                    <i class="ti-lock"></i>
                    Password reset
                </div>
                <h2 class="login-hero-title">
                    Securely reset your<br><span>MarineCaddie</span> password
                </h2>
                <p class="login-hero-text">
                    A secure password reset experience helps protect shipment, stock, billing, and user access across your daily operations.
                </p>
                <ul class="login-hero-features">
                    <li>
                        <i class="ti-shield"></i>
                        <span>Protected reset flow with secure token validation</span>
                    </li>
                    <li>
                        <i class="ti-check-box"></i>
                        <span>Clean and simple password recovery experience</span>
                    </li>
                    <li>
                        <i class="ti-user"></i>
                        <span>Designed for safe access across all user roles</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
