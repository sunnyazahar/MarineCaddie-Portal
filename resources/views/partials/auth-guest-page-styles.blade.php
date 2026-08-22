<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --mc-navy: #0b1f4a;
        --mc-teal: #008080;
        --mc-teal-dark: #006666;
        --mc-text: #1e293b;
        --mc-muted: #64748b;
        --mc-border: #e2e8f0;
        --mc-surface: #ffffff;
    }

    html,
    body.guest-layout {
        margin: 0;
        padding: 0;
        min-height: 100%;
        height: 100%;
        font-family: 'Source Sans 3', 'Open Sans', sans-serif !important;
        color: var(--mc-text);
        background: #f8fafc;
    }

    body.guest-layout #app,
    body.guest-layout main {
        min-height: 100%;
        height: 100%;
    }

    .login-page {
        display: grid;
        grid-template-columns: minmax(420px, 1fr) minmax(480px, 1.15fr);
        min-height: 100vh;
        width: 100%;
    }

    .login-panel {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh;
        min-height: 100dvh;
        padding: 48px 32px 24px;
        background:
            radial-gradient(circle at 10% 0%, rgba(0, 128, 128, 0.06), transparent 42%),
            radial-gradient(circle at 90% 100%, rgba(0, 174, 239, 0.05), transparent 40%),
            #f8fafc;
    }

    .login-panel-inner {
        width: 100%;
        max-width: 420px;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 28px;
    }

    .login-brand .marinecaddie-logo {
        max-height: 72px !important;
        width: auto !important;
        max-width: 220px !important;
        height: auto !important;
        object-fit: contain;
    }

    .login-brand-tagline {
        margin: 10px 0 0;
        font-size: 13px;
        font-weight: 600;
        color: var(--mc-muted);
        letter-spacing: 0.02em;
    }

    .login-card {
        background: var(--mc-surface);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 16px;
        padding: 32px 28px 28px;
        box-shadow:
            0 4px 6px rgba(15, 23, 42, 0.02),
            0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .login-card-head {
        margin-bottom: 24px;
    }

    .login-card-title {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 800;
        color: var(--mc-navy);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .login-card-subtitle {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
        color: var(--mc-muted);
        line-height: 1.45;
    }

    .form-group-custom {
        margin-bottom: 16px;
    }

    .field-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .field-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .field-input-icon {
        position: absolute;
        left: 14px;
        z-index: 2;
        color: #94a3b8;
        font-size: 15px;
        line-height: 1;
        pointer-events: none;
    }

    .field-input {
        width: 100%;
        height: 46px;
        padding: 0 14px 0 42px;
        font-size: 15px;
        font-weight: 500;
        color: var(--mc-text);
        border: 1px solid var(--mc-border);
        border-radius: 10px;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .field-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .field-input:focus {
        outline: none;
        border-color: var(--mc-teal);
        box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.12);
    }

    .field-input.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .btn-login {
        width: 100%;
        height: 46px;
        margin-top: 18px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--mc-teal) 0%, #009999 100%);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.02em;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0, 128, 128, 0.28);
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
    }

    .btn-login:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(0, 128, 128, 0.32);
    }

    .btn-login:active:not(:disabled) {
        transform: translateY(0);
    }

    .btn-login:disabled {
        cursor: not-allowed;
        opacity: 0.55;
        box-shadow: none;
    }

    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
        font-size: 13px;
    }

    .form-footer-hint {
        color: var(--mc-muted);
        font-weight: 500;
    }

    .forgot-link {
        color: var(--mc-teal);
        text-decoration: none;
        font-weight: 700;
    }

    .forgot-link:hover {
        color: var(--mc-teal-dark);
        text-decoration: underline;
    }

    .login-alert-success {
        margin-bottom: 16px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 13px;
        font-weight: 600;
    }

    .invalid-feedback {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #dc2626;
        margin-top: 6px;
    }

    .login-footer {
        position: static;
        flex-shrink: 0;
        width: 100%;
        max-width: 420px;
        margin-top: auto;
        padding: 24px 16px 8px;
        text-align: center;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.55;
        color: #94a3b8;
    }

    .login-footer a {
        color: var(--mc-teal);
        text-decoration: none;
        font-weight: 600;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .login-hero {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 64px 72px;
        overflow: hidden;
        color: #fff;
        background: #0b1f4a;
    }

    .login-hero::before {
        content: '';
        position: absolute;
        inset: -24px;
        z-index: 0;
        background-image: url('{{ asset("files/assets/images/login_bg.png") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        filter: blur(10px);
        transform: scale(1.06);
        pointer-events: none;
    }

    .login-hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(135deg, rgba(14, 29, 74, 0.88) 0%, rgba(14, 29, 74, 0.72) 100%);
    }

    .login-hero-content {
        position: relative;
        z-index: 3;
        max-width: 520px;
    }

    .login-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .login-hero-badge i {
        font-size: 14px;
        color: #7dd3fc;
    }

    .login-hero-title {
        margin: 0 0 14px;
        font-size: clamp(28px, 3.2vw, 40px);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .login-hero-title span {
        background: linear-gradient(90deg, #fff 0%, #a5f3fc 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .login-hero-text {
        margin: 0 0 28px;
        font-size: 16px;
        line-height: 1.65;
        color: rgba(255, 255, 255, 0.82);
        font-weight: 500;
    }

    .login-hero-features {
        display: grid;
        gap: 14px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .login-hero-features li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.92);
    }

    .login-hero-features i {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 15px;
        color: #99f6e4;
    }

    @media (max-width: 991.98px) {
        html,
        body.guest-layout {
            height: auto;
            min-height: 100%;
            min-height: 100dvh;
        }

        body.guest-layout #app,
        body.guest-layout main {
            height: auto;
            min-height: 100dvh;
        }

        .login-page {
            grid-template-columns: 1fr;
            min-height: 100dvh;
        }

        .login-hero {
            display: none;
        }

        .login-panel {
            min-height: 100dvh;
            padding: max(24px, env(safe-area-inset-top)) 16px max(16px, env(safe-area-inset-bottom));
        }

        .login-panel-inner {
            justify-content: flex-start;
            padding-top: 4px;
        }

        .login-card {
            padding: 24px 20px 22px;
        }

        .login-card-title {
            font-size: 22px;
        }

        .login-footer {
            margin-top: 28px;
            padding: 0 4px max(12px, env(safe-area-inset-bottom));
        }
    }

    @media (max-width: 420px) {
        .form-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
