<style>
    .dash-page {
        padding-bottom: 120px;
    }

    .mc-assistant-shell {
        --mc-assistant-inline-start: auto;
        --mc-assistant-inline-end: 24px;
        --mc-assistant-bottom: max(0px, env(safe-area-inset-bottom, 0px));
        --mc-assistant-visual-offset: 0px;
        --mc-assistant-screen-bottom: calc(var(--mc-assistant-bottom) + var(--mc-assistant-visual-offset));
        --mc-assistant-width: min(390px, calc(100vw - 24px));
        --mc-assistant-panel-gap: 12px;
        --mc-assistant-launcher-height: 72px;
        z-index: 1080;
    }

    .mc-assistant-panel {
        position: fixed;
        right: var(--mc-assistant-inline-end);
        left: var(--mc-assistant-inline-start);
        bottom: var(--mc-assistant-screen-bottom);
        width: var(--mc-assistant-width);
        max-height: min(640px, calc(100vh - 112px));
        padding: 18px;
        border: 1px solid #d6e4ef;
        border-radius: 20px;
        background:
            radial-gradient(circle at top right, rgba(0, 174, 239, 0.18), transparent 34%),
            linear-gradient(160deg, rgba(14, 29, 74, 0.04) 0%, rgba(255, 255, 255, 0.99) 50%),
            #ffffff;
        box-shadow: 0 22px 52px rgba(14, 29, 74, 0.18);
        display: flex;
        flex-direction: column;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(18px) scale(0.98);
        transform-origin: bottom right;
        transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        overflow: hidden;
        box-sizing: border-box;
        z-index: 1081;
    }

    .mc-assistant-shell.is-open .mc-assistant-panel {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .mc-assistant-panel::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, #00aeef 0%, #0e1d4a 100%);
    }

    .mc-assistant-launcher {
        position: fixed;
        right: var(--mc-assistant-inline-end);
        left: var(--mc-assistant-inline-start);
        bottom: var(--mc-assistant-screen-bottom);
        width: var(--mc-assistant-width);
        border: 1px solid rgba(14, 29, 74, 0.08);
        border-radius: 20px;
        background: linear-gradient(135deg, #0e1d4a 0%, #16397a 42%, #00aeef 140%);
        color: #ffffff;
        padding: 15px 16px;
        display: flex !important;
        align-items: center;
        gap: 12px;
        box-shadow: 0 18px 36px rgba(14, 29, 74, 0.24);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.18s ease, visibility 0.18s ease;
        text-align: left;
        touch-action: manipulation;
        box-sizing: border-box;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        z-index: 1082;
    }

    .mc-assistant-shell.is-open .mc-assistant-launcher {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(10px);
    }

    .mc-assistant-launcher:hover,
    .mc-assistant-launcher:focus {
        outline: none;
        transform: translateY(-1px);
        box-shadow: 0 20px 40px rgba(14, 29, 74, 0.28);
    }

    .mc-assistant-launcher__pulse {
        position: relative;
        flex: 0 0 12px;
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #ffb020;
        box-shadow: 0 0 0 6px rgba(255, 176, 32, 0.2);
    }

    .mc-assistant-launcher__copy {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1 1 auto;
    }

    .mc-assistant-launcher__title {
        display: block;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.01em;
    }

    .mc-assistant-launcher__status {
        display: block;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.82);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mc-assistant-launcher__toggle {
        flex: 0 0 auto;
        min-width: 64px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.02em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .mc-assistant-shell.is-open .mc-assistant-launcher__toggle {
        background: rgba(255, 255, 255, 0.2);
    }

    .mc-assistant-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
    }

    .mc-assistant-header-copy {
        min-width: 0;
    }

    .mc-assistant-kicker {
        margin: 0 0 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0088c7;
    }

    .mc-assistant-heading-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .mc-assistant-title {
        margin: 0;
        font-size: 1.18rem;
        font-weight: 800;
        color: #0e1d4a;
        letter-spacing: -0.02em;
    }

    .mc-assistant-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #0e1d4a;
        background: rgba(0, 174, 239, 0.12);
        border: 1px solid rgba(0, 174, 239, 0.22);
    }

    .mc-assistant-copy {
        margin: 6px 0 0;
        font-size: 13px;
        line-height: 1.5;
        color: #5f6c83;
    }

    .mc-assistant-close {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        border: 1px solid #d6e4ef;
        border-radius: 10px;
        background: #ffffff;
        color: #4f6785;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(14, 29, 74, 0.06);
        transition: border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        touch-action: manipulation;
    }

    .mc-assistant-close:hover,
    .mc-assistant-close:focus {
        outline: none;
        border-color: #00aeef;
        color: #0e1d4a;
        transform: translateY(-1px);
    }

    .mc-assistant-thread {
        flex: 1 1 auto;
        min-height: 240px;
        min-width: 0;
        max-height: none;
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid #e4edf4;
        background: linear-gradient(180deg, rgba(248, 251, 254, 0.96) 0%, rgba(255, 255, 255, 0.98) 100%);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .mc-assistant-message {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-width: 90%;
    }

    .mc-assistant-message--bot {
        align-self: flex-start;
    }

    .mc-assistant-message--user {
        align-self: flex-end;
        text-align: right;
    }

    .mc-assistant-message__label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8a99ad;
    }

    .mc-assistant-message__bubble {
        padding: 11px 13px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.5;
        box-shadow: 0 6px 18px rgba(14, 29, 74, 0.06);
        overflow-wrap: anywhere;
    }

    .mc-assistant-message--bot .mc-assistant-message__bubble {
        background: #ffffff;
        border: 1px solid #dbe7f1;
        color: #1f314f;
        border-bottom-left-radius: 6px;
    }

    .mc-assistant-message--user .mc-assistant-message__bubble {
        background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
        color: #ffffff;
        border-bottom-right-radius: 6px;
    }

    .mc-assistant-message__bubble p:last-child {
        margin-bottom: 0;
    }

    .mc-assistant-message__bubble ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    .mc-assistant-message__bubble li + li {
        margin-top: 6px;
    }

    .mc-assistant-response-stack__item + .mc-assistant-response-stack__item {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #dbe7f1;
    }

    .mc-assistant-composer {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 14px;
    }

    .mc-assistant-input-wrap {
        flex: 1 1 auto;
        min-width: 0;
        display: block;
    }

    .mc-assistant-input {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #d6e4ef;
        min-height: 42px;
        font-size: 16px;
    }

    .mc-assistant-input:focus {
        border-color: #00aeef;
        box-shadow: 0 0 0 0.12rem rgba(0, 174, 239, 0.14);
    }

    .mc-assistant-send {
        flex: 0 0 auto;
        min-height: 42px;
        border: none;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #ff5a5f 0%, #e87722 100%);
        box-shadow: 0 8px 18px rgba(232, 119, 34, 0.24);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        touch-action: manipulation;
    }

    .mc-assistant-send:hover,
    .mc-assistant-send:focus {
        outline: none;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(232, 119, 34, 0.28);
    }

    .mc-assistant-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 10px;
    }

    .mc-assistant-help {
        margin: 0;
        flex: 1 1 auto;
        font-size: 12px;
        color: #64748b;
    }

    .mc-assistant-clear {
        flex: 0 0 auto;
        min-height: 40px;
        padding: 8px 12px;
        border: 1px solid #d6e4ef;
        border-radius: 10px;
        background: #ffffff;
        color: #0e1d4a;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        box-shadow: 0 4px 12px rgba(14, 29, 74, 0.05);
        cursor: pointer;
        transition: transform 0.15s ease, border-color 0.15s ease, background 0.15s ease, color 0.15s ease;
        touch-action: manipulation;
    }

    .mc-assistant-clear:hover,
    .mc-assistant-clear:focus {
        outline: none;
        transform: translateY(-1px);
        border-color: #00aeef;
        background: #ecf8fd;
        color: #0b2957;
    }

    .mc-assistant-clear:disabled {
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
    }

    .mc-assistant-help code,
    .mc-assistant-message__bubble code {
        display: inline-block;
        padding: 1px 5px;
        border-radius: 6px;
        background: rgba(14, 29, 74, 0.06);
        color: #0e1d4a;
        font-size: 11px;
    }

    @media (max-width: 767.98px) {
        .dash-page {
            padding-bottom: 204px;
        }

        .mc-assistant-shell {
            --mc-assistant-inline-start: max(12px, env(safe-area-inset-left, 0px));
            --mc-assistant-inline-end: max(12px, env(safe-area-inset-right, 0px));
            --mc-assistant-bottom: env(safe-area-inset-bottom, 0px);
            --mc-assistant-width: auto;
            --mc-assistant-panel-gap: 0px;
            --mc-assistant-launcher-height: 74px;
        }

        .mc-assistant-panel {
            max-height: min(82dvh, calc(100dvh - 28px));
            padding: 16px 15px calc(16px + env(safe-area-inset-bottom, 0px));
            border-radius: 22px;
        }

        .mc-assistant-thread {
            min-height: 180px;
            padding: 13px;
        }

        .mc-assistant-header {
            gap: 12px;
            margin-bottom: 12px;
        }

        .mc-assistant-copy {
            font-size: 12px;
        }

        .mc-assistant-footer {
            align-items: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .mc-assistant-shell {
            --mc-assistant-bottom: env(safe-area-inset-bottom, 0px);
            --mc-assistant-launcher-height: 70px;
        }

        .mc-assistant-launcher {
            padding: 13px 14px;
            border-radius: 18px;
            gap: 10px;
        }

        .mc-assistant-launcher__title {
            font-size: 12px;
        }

        .mc-assistant-launcher__status {
            font-size: 10px;
        }

        .mc-assistant-launcher__toggle {
            min-width: 58px;
            padding: 6px 10px;
        }

        .mc-assistant-panel {
            max-height: min(84dvh, calc(100dvh - 20px));
            padding: 15px 14px calc(14px + env(safe-area-inset-bottom, 0px));
            border-radius: 20px;
        }

        .mc-assistant-title {
            font-size: 1.05rem;
        }

        .mc-assistant-badge {
            font-size: 9px;
        }

        .mc-assistant-close {
            width: 32px;
            height: 32px;
            border-radius: 9px;
        }

        .mc-assistant-composer {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            margin-top: 12px;
        }

        .mc-assistant-input-wrap,
        .mc-assistant-send {
            width: 100%;
        }

        .mc-assistant-send {
            width: 100%;
            min-height: 46px;
        }

        .mc-assistant-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            margin-top: 12px;
        }

        .mc-assistant-clear {
            width: 100%;
            min-height: 44px;
        }

        .mc-assistant-message {
            max-width: 100%;
        }

        .mc-assistant-message__bubble {
            padding: 10px 12px;
        }

        .mc-assistant-help {
            font-size: 11px;
            line-height: 1.45;
        }
    }

    @media (max-width: 399.98px), (max-height: 720px) {
        .mc-assistant-panel {
            max-height: min(88dvh, calc(100dvh - 16px));
        }

        .mc-assistant-thread {
            min-height: 160px;
        }
    }
</style>
