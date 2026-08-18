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
            margin-bottom: 36px;
            text-align: center;
            animation: fadeUp 0.55s ease both;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.7);
            border-radius: 12px;
            padding: 40px 34px 34px;
            box-shadow: 0 18px 40px rgba(14, 29, 74, 0.08);
            animation: fadeUp 0.65s ease 0.08s both;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FF5A5F 0%, #38bdf8 55%, #349DDA 100%);
        }

        .otp-badge {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(56, 189, 248, 0.16), rgba(255, 90, 95, 0.12));
            border: 1px solid rgba(56, 189, 248, 0.25);
            color: #0E1D4A;
            font-size: 28px;
            animation: pulseSoft 2.4s ease-in-out infinite;
        }

        .otp-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #0E1D4A;
            margin-bottom: 8px;
        }

        .otp-subtitle {
            text-align: center;
            font-size: 13px;
            line-height: 1.55;
            color: #64748b;
            margin-bottom: 26px;
        }

        .otp-subtitle strong {
            color: #0E1D4A;
            font-weight: 600;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .otp-digit {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            color: #0E1D4A;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            outline: none;
        }

        .otp-digit:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.18);
            transform: translateY(-1px);
        }

        .otp-digit.is-invalid {
            border-color: #FF4D4D;
            box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.12);
        }

        .otp-digit.filled {
            border-color: #349DDA;
            background: #f8fbff;
        }

        .btn-login {
            width: 100%;
            height: 44px;
            background: linear-gradient(135deg, #FF5A5F 0%, #ff7a7e 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
            margin-top: 18px;
            box-shadow: 0 10px 20px rgba(255, 90, 95, 0.25);
        }

        .btn-login:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(255, 90, 95, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 12px;
            gap: 12px;
        }

        .resend-text {
            color: #94a3b8;
        }

        .forgot-link {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .forgot-link:disabled {
            color: #94a3b8;
            cursor: not-allowed;
        }

        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 18px;
            font-size: 11px;
            color: #94a3b8;
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(14, 29, 74, 0.92) 0%, rgba(14, 29, 74, 0.72) 100%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            color: #FFFFFF;
            max-width: 520px;
            animation: fadeUp 0.7s ease 0.12s both;
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
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 28px;
        }

        .hero-points {
            display: grid;
            gap: 12px;
            margin-bottom: 28px;
        }

        .hero-point {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-point span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.18);
            flex-shrink: 0;
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

        .alert-otp {
            border-radius: 8px;
            font-size: 12px;
            padding: 10px 12px;
            margin-bottom: 16px;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #FF4D4D;
            margin-top: 8px;
            text-align: center;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseSoft {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.2);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 0 0 10px rgba(56, 189, 248, 0);
            }
        }

        @media (max-width: 991px) {
            .login-right {
                display: none;
            }

            .login-left {
                flex: 1;
            }

            .otp-digit {
                width: 42px;
                height: 50px;
                font-size: 18px;
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
                @if (($blockedSecondsLeft ?? 0) > 0)
                    {{-- BLOCKED STATE --}}
                    <div class="otp-badge" style="background:linear-gradient(145deg,rgba(239,68,68,.18),rgba(220,38,38,.12));border-color:rgba(239,68,68,.35);">
                        <i class="icofont icofont-ban" style="color:#dc2626;"></i>
                    </div>
                    <h1 class="otp-title" style="color:#dc2626;">Account temporarily locked</h1>
                    <p class="otp-subtitle">
                        Too many incorrect OTP attempts.<br>
                        Locked for <strong id="block-countdown">{{ ceil($blockedSecondsLeft / 60) }} minute(s)</strong>.
                    </p>
                    <div style="background:#fef2f2;border-left:4px solid #dc2626;color:#991b1b;border-radius:8px;padding:14px 16px;font-size:13px;line-height:1.6;margin-bottom:16px;">
                        Your account has been locked for <strong>{{ ceil($blockedSecondsLeft / 60) }} minute(s)</strong>
                        after <strong>5 consecutive failed OTP attempts</strong>.<br><br>
                        If this was not you, please contact your administrator immediately.
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top:6px;">
                        @csrf
                        <button
                            type="submit"
                            style="display:block;width:100%;text-align:center;font-size:13px;color:#64748b;text-decoration:none;background:none;border:none;padding:0;cursor:pointer;">
                            &larr; Back to login
                        </button>
                    </form>
                    <div class="secure-note" style="margin-top:18px;">
                        <i class="icofont icofont-lock"></i>
                        Account will unlock automatically when the timer expires.
                    </div>
                    <script>
                    (function() {
                        var secs = {{ (int) $blockedSecondsLeft }};
                        var el   = document.getElementById('block-countdown');
                        if (!el) return;
                        var t = setInterval(function() {
                            secs--;
                            if (secs <= 0) { clearInterval(t); location.reload(); return; }
                            var m = Math.ceil(secs / 60);
                            el.textContent = m + ' minute(s) (' + secs + 's)';
                        }, 1000);
                    })();
                    </script>
                @else
                    {{-- NORMAL OTP STATE --}}
                    <div class="otp-badge">
                        <i class="icofont icofont-ui-password"></i>
                    </div>
                    <h1 class="otp-title">Verify your identity</h1>
                    <p class="otp-subtitle">
                        Enter the 6-digit code we sent to<br>
                        <strong>{{ $maskedEmail }}</strong>
                    </p>

                    @if (! empty($localOtp))
                        <div class="alert {{ ! empty($otpMailFailed) ? 'alert-warning' : 'alert-info' }} alert-otp mb-0">
                            @if (! empty($otpMailFailed))
                                Email cannot be delivered from this local machine. Use the code below to continue.
                            @else
                                Local development mode.
                            @endif
                            Your code is <strong style="letter-spacing: 2px; font-size: 15px;">{{ $localOtp }}</strong>
                        </div>
                    @elseif (! empty($otpMailFailed))
                        <div class="alert alert-danger alert-otp mb-0">
                            {{ ! empty($otpMailError) ? $otpMailError : 'We could not send the verification email. Please try Resend code, or ask an administrator to check the server mail settings.' }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success alert-otp mb-0">{{ session('status') }}</div>
                    @endif

                    @if (($attemptsLeft ?? 5) < 5)
                        <div style="background:#fff7ed;border-left:4px solid #f59e0b;color:#92400e;border-radius:8px;padding:10px 14px;font-size:12.5px;margin-bottom:8px;">
                            <i class="feather icon-alert-triangle" style="margin-right:5px;"></i>
                            Warning: <strong>{{ $attemptsLeft }} attempt(s) remaining</strong> before your account is locked.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
                        @csrf
                        <input type="hidden" name="otp" id="otp-value" value="{{ old('otp') }}">

                        <div class="otp-inputs" id="otp-inputs">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="1"
                                    class="otp-digit @error('otp') is-invalid @enderror"
                                    data-index="{{ $i }}"
                                    autocomplete="one-time-code"
                                    aria-label="Digit {{ $i + 1 }}"
                                >
                            @endfor
                        </div>

                        @error('otp')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror

                        <button type="submit" class="btn-login" id="verify-btn">Verify OTP</button>
                    </form>

                    <div class="form-footer">
                        <span class="resend-text" id="resend-label">
                            @if ($resendAvailableIn > 0)
                                Resend code in <span id="countdown">{{ $resendAvailableIn }}</span>s
                            @else
                                Didn’t receive the code?
                            @endif
                        </span>

                        <form method="POST" action="{{ route('otp.resend') }}" id="resend-form" style="display:inline;">
                            @csrf
                            <button
                                type="submit"
                                class="forgot-link"
                                id="resend-btn"
                                @if ($resendAvailableIn > 0) disabled @endif
                            >Resend OTP</button>
                        </form>
                    </div>

                    <div class="secure-note">
                        <i class="icofont icofont-lock"></i>
                        Secured login · Code expires in 10 minutes
                    </div>
                @endif
            </div>

            <div class="page-footer">
                <span style="font-size: 12px; font-weight: 500; color:#000000">© 2026 MarineCaddie, inc. All rights
                    reserved. | <a href="#">Privacy Policy</a></span>
            </div>
        </div>

        <div class="login-right">
            <div class="overlay"></div>

            <div class="visual-content">
                <h1 class="welcome-title">Secure access to <b>MarineCaddie</b></h1>
                <div class="separator"></div>
                <p class="welcome-text">
                    We’ve added an extra verification step to protect your shipments, stock, and billing data.
                    Confirm the one-time code to continue into your workspace.
                </p>
                <div class="hero-points">
                    <div class="hero-point"><span></span> One-time code for every login session</div>
                    <div class="hero-point"><span></span> Protects sensitive logistics operations</div>
                    <div class="hero-point"><span></span> Fast verification, safer access</div>
                </div>
                <a href="#" class="btn-readmore">Learn more ..</a>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var digits = Array.prototype.slice.call(document.querySelectorAll('.otp-digit'));
        var hidden = document.getElementById('otp-value');
        var form = document.getElementById('otp-form');
        var verifyBtn = document.getElementById('verify-btn');
        var resendBtn = document.getElementById('resend-btn');
        var resendLabel = document.getElementById('resend-label');
        var countdown = {{ (int) $resendAvailableIn }};

        function syncHidden() {
            var value = digits.map(function(input) { return input.value.replace(/\D/g, ''); }).join('');
            hidden.value = value;
            digits.forEach(function(input) {
                input.classList.toggle('filled', input.value !== '');
            });
            return value;
        }

        function focusIndex(index) {
            if (digits[index]) {
                digits[index].focus();
                digits[index].select();
            }
        }

        digits.forEach(function(input, index) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 1);
                syncHidden();
                if (this.value && index < digits.length - 1) {
                    focusIndex(index + 1);
                }
                if (syncHidden().length === 6) {
                    verifyBtn.focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    focusIndex(index - 1);
                }
                if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    focusIndex(index - 1);
                }
                if (e.key === 'ArrowRight' && index < digits.length - 1) {
                    e.preventDefault();
                    focusIndex(index + 1);
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach(function(char, i) {
                    if (digits[i]) {
                        digits[i].value = char;
                    }
                });
                syncHidden();
                focusIndex(Math.min(pasted.length, digits.length - 1));
            });
        });

        if (hidden.value) {
            hidden.value.split('').forEach(function(char, i) {
                if (digits[i]) {
                    digits[i].value = char;
                }
            });
            syncHidden();
        }

        if (digits[0]) {
            digits[0].focus();
        }

        form.addEventListener('submit', function(e) {
            var value = syncHidden();
            if (value.length !== 6) {
                e.preventDefault();
                digits.forEach(function(input) { input.classList.add('is-invalid'); });
                focusIndex(0);
            }
        });

        function updateResendUi() {
            if (!resendBtn || !resendLabel) {
                return;
            }

            if (countdown > 0) {
                resendBtn.disabled = true;
                resendLabel.innerHTML = 'Resend code in <span id="countdown">' + countdown + '</span>s';
            } else {
                resendBtn.disabled = false;
                resendLabel.textContent = 'Didn’t receive the code?';
            }
        }

        if (countdown > 0) {
            updateResendUi();
            var timer = setInterval(function() {
                countdown -= 1;
                if (countdown <= 0) {
                    clearInterval(timer);
                    countdown = 0;
                }
                updateResendUi();
            }, 1000);
        }
    })();
    </script>
@endsection
