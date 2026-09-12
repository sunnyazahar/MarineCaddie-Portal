@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body.vessel-live-tracker-page {
            background:
                radial-gradient(circle at top left, rgba(0, 174, 239, 0.14), transparent 30%),
                radial-gradient(circle at top right, rgba(14, 29, 74, 0.12), transparent 26%),
                linear-gradient(180deg, #edf7fb 0%, #f7fbfe 34%, #ffffff 100%);
        }

        .vessel-live-tracker-shell {
            padding: 16px 16px 26px;
        }

        .vessel-live-tracker-layout {
            display: grid;
            gap: 18px;
        }

        .tracker-surface,
        .tracker-status-card,
        .tracker-hero-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(183, 216, 233, 0.95);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 18px 45px rgba(14, 29, 74, 0.08);
            backdrop-filter: blur(10px);
        }

        .tracker-hero-card::before,
        .tracker-surface::before,
        .tracker-status-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.38), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .tracker-hero-card {
            overflow: visible;
            z-index: 8;
        }

        .tracker-hero-top {
            position: relative;
            padding: 22px 24px 16px;
            background:
                radial-gradient(circle at 20% 18%, rgba(0, 174, 239, 0.32), transparent 22%),
                radial-gradient(circle at 82% 10%, rgba(255, 255, 255, 0.12), transparent 20%),
                linear-gradient(135deg, rgba(11, 28, 70, 0.98) 0%, rgba(0, 126, 184, 0.98) 100%);
            color: #fff;
        }

        .tracker-hero-top::after {
            content: "";
            position: absolute;
            inset: auto -8% -54% auto;
            width: 240px;
            height: 240px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .tracker-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.78);
        }

        .tracker-eyebrow::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #ffd166;
            box-shadow: 0 0 0 8px rgba(255, 209, 102, 0.16);
            animation: trackerPulse 2.4s ease-in-out infinite;
        }

        .tracker-hero-title-row {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(220px, 0.7fr);
            gap: 18px;
            align-items: end;
            margin-top: 14px;
        }

        .tracker-hero-title {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.02;
            color: #fff;
        }

        .tracker-hero-subtitle {
            margin: 8px 0 0;
            max-width: 760px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.86);
        }

        .tracker-hero-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .tracker-hero-mini-card {
            padding: 14px 14px 12px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.10);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .tracker-hero-mini-card span {
            display: block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.62);
        }

        .tracker-hero-mini-card strong {
            display: block;
            margin-top: 5px;
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .tracker-hero-body {
            position: relative;
            z-index: 2;
            padding: 20px 24px 24px;
        }

        .tracker-help-text {
            margin: 0 0 18px;
            max-width: 900px;
            color: #506178;
            font-size: 15px;
            line-height: 1.75;
        }

        .tracker-search-form {
            display: grid;
            gap: 10px;
        }

        .tracker-search-main {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: end;
        }

        .tracker-field-label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #0e1d4a;
        }

        .tracker-search-stack {
            display: grid;
            gap: 8px;
        }

        .tracker-search-field {
            position: relative;
            z-index: 12;
        }

        .tracker-input-frame {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 58px;
            padding: 0 18px;
            border: 1px solid #b9dced;
            border-radius: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(245, 250, 255, 0.98));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                0 10px 24px rgba(14, 29, 74, 0.04);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .tracker-input-frame:focus-within {
            border-color: #00aeef;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                0 0 0 4px rgba(0, 174, 239, 0.12),
                0 16px 30px rgba(0, 126, 184, 0.08);
            transform: translateY(-1px);
        }

        .tracker-input-frame::before {
            content: "";
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #00aeef 0%, #0e1d4a 100%);
            box-shadow: 0 0 0 7px rgba(0, 174, 239, 0.10);
            flex-shrink: 0;
        }

        .tracker-search-input {
            width: 100%;
            min-height: 54px;
            padding: 0;
            border: 0;
            outline: none;
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
            background: transparent;
        }

        .tracker-search-input::placeholder {
            color: #90a1ba;
            font-weight: 600;
        }

        .tracker-search-note {
            margin: 0;
            color: #667892;
            font-size: 13px;
            line-height: 1.55;
        }

        .tracker-suggestion-panel[hidden] {
            display: none !important;
        }

        .tracker-suggestion-panel {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 30;
            display: grid;
            gap: 8px;
            max-height: min(320px, 46vh);
            padding: 10px;
            border: 1px solid #d7e7f1;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(243, 250, 255, 0.98));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.7),
                0 16px 32px rgba(14, 29, 74, 0.08);
            overflow-y: auto;
            overscroll-behavior: contain;
            animation: trackerFadeUp 0.22s ease both;
        }

        .tracker-suggestion-list {
            display: grid;
            gap: 8px;
        }

        .tracker-suggestion-item {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d7e7f1;
            border-radius: 15px;
            background: linear-gradient(180deg, #ffffff 0%, #f4fbff 100%);
            text-align: left;
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .tracker-suggestion-item:hover,
        .tracker-suggestion-item.is-active {
            border-color: #00aeef;
            background: linear-gradient(180deg, #effaff 0%, #e6f7ff 100%);
            box-shadow: 0 10px 24px rgba(0, 126, 184, 0.10);
            transform: translateY(-1px);
        }

        .tracker-suggestion-title {
            display: block;
            color: #0f1f3d;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.35;
        }

        .tracker-suggestion-meta {
            display: block;
            margin-top: 4px;
            color: #688099;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        .tracker-submit-btn,
        .tracker-back-btn,
        .tracker-inline-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 56px;
            padding: 0 22px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .tracker-submit-btn:hover,
        .tracker-back-btn:hover,
        .tracker-inline-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .tracker-submit-btn {
            position: relative;
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #ff5a5f 0%, #f58b18 100%);
            box-shadow: 0 18px 30px rgba(245, 138, 24, 0.26);
        }

        .tracker-submit-btn[disabled] {
            opacity: 0.8;
            cursor: wait;
        }

        .tracker-submit-btn .tracker-submit-loader {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.36);
            border-top-color: #fff;
            border-radius: 999px;
            animation: trackerSpin 0.9s linear infinite;
        }

        .tracker-submit-btn.is-loading .tracker-submit-loader {
            display: inline-flex;
        }

        .tracker-submit-btn.is-loading .tracker-submit-label {
            opacity: 0.9;
        }

        .tracker-back-btn,
        .tracker-inline-btn {
            border: 1px solid #b8d8e8;
            color: #0e1d4a;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 24px rgba(14, 29, 74, 0.05);
        }

        .tracker-inline-btn {
            min-height: 46px;
            padding: 0 16px;
            border-radius: 16px;
        }

        .tracker-inline-btn-secondary {
            background: linear-gradient(180deg, #f8fcff, #edf7fd);
        }

        .tracker-results-mount {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 14px;
        }

        .tracker-results-mount.is-loading {
            pointer-events: none;
        }

        .tracker-status-card {
            padding: 16px 18px;
        }

        .tracker-status-card.alert-danger {
            border-color: #f7c7cb;
            background: linear-gradient(180deg, #fff5f5 0%, #fffefe 100%);
            color: #9f1239;
        }

        .tracker-status-card.alert-info {
            border-color: #c8e8f3;
            background: linear-gradient(180deg, #f2fbff 0%, #ffffff 100%);
            color: #0d4f73;
        }

        .tracker-status-kicker {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            opacity: 0.74;
        }

        .tracker-status-copy {
            font-size: 15px;
            line-height: 1.6;
            font-weight: 700;
        }

        .tracker-empty-state {
            padding: 34px 28px 32px;
            text-align: center;
        }

        .tracker-empty-orbit {
            position: relative;
            width: 116px;
            height: 116px;
            margin: 0 auto 18px;
        }

        .tracker-empty-orbit-core,
        .tracker-empty-orbit-ring {
            position: absolute;
            inset: 0;
            border-radius: 999px;
        }

        .tracker-empty-orbit-core {
            inset: 28px;
            background: linear-gradient(135deg, #00aeef 0%, #0e1d4a 100%);
            box-shadow: 0 16px 34px rgba(0, 126, 184, 0.28);
        }

        .tracker-empty-orbit-ring {
            border: 1px solid rgba(0, 126, 184, 0.18);
        }

        .tracker-empty-orbit-ring-a {
            animation: trackerOrbit 7s linear infinite;
        }

        .tracker-empty-orbit-ring-b {
            inset: 12px;
            border-style: dashed;
            animation: trackerOrbitReverse 9s linear infinite;
        }

        .tracker-empty-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #0f7eaf;
        }

        .tracker-empty-title {
            margin: 12px auto 10px;
            max-width: 700px;
            color: #0e1d4a;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
        }

        .tracker-empty-copy {
            margin: 0 auto;
            max-width: 700px;
            color: #54677f;
            font-size: 16px;
            line-height: 1.8;
        }

        .tracker-empty-examples {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 22px;
        }

        .tracker-example-chip,
        .tracker-key-pill,
        .tracker-panel-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
        }

        .tracker-example-chip {
            border: 1px solid #c7e5f3;
            background: linear-gradient(180deg, #f9fdff, #eef9ff);
            color: #0f4f72;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(14, 29, 74, 0.05);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .tracker-example-chip:hover {
            transform: translateY(-1px);
            border-color: #8ed4ef;
            box-shadow: 0 14px 28px rgba(0, 126, 184, 0.10);
        }

        .tracker-fade-in {
            animation: trackerFadeUp 0.4s ease both;
        }

        .tracker-result-shell {
            display: grid;
            gap: 16px;
        }

        .tracker-summary-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(290px, 0.75fr);
            gap: 18px;
            padding: 22px;
        }

        .tracker-summary-title {
            margin: 14px 0 0;
            color: #0e1d4a;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.08;
        }

        .tracker-summary-subtitle {
            margin: 6px 0 0;
            color: #67809d;
            font-size: 14px;
            line-height: 1.5;
        }

        .tracker-lookup-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: linear-gradient(180deg, #e7f7fc, #dff4fb);
            color: #007db1;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .tracker-result-summary {
            margin: 16px 0 0;
            color: #21324a;
            font-size: 16px;
            line-height: 1.8;
            font-weight: 700;
        }

        .tracker-keyline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .tracker-key-pill,
        .tracker-panel-badge {
            border: 1px solid rgba(0, 126, 184, 0.10);
            background: rgba(236, 248, 253, 0.9);
            color: #0c5b81;
        }

        .tracker-chip-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .tracker-chip {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 18px;
            border: 1px solid #deedf5;
            background: linear-gradient(180deg, #fdfeff 0%, #f5fbff 100%);
        }

        .tracker-chip-label {
            display: block;
            margin-bottom: 5px;
            color: #8090a7;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .tracker-chip-value {
            display: block;
            color: #10203f;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.45;
        }

        .tracker-summary-stack {
            display: grid;
            gap: 12px;
            align-content: start;
        }

        .tracker-stat-card {
            padding: 16px;
            border-radius: 20px;
            border: 1px solid #e1eef5;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(240, 248, 253, 0.98));
            box-shadow: 0 12px 26px rgba(14, 29, 74, 0.04);
        }

        .tracker-stat-label,
        .tracker-panel-kicker,
        .tracker-map-location-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #7c8ea6;
        }

        .tracker-stat-value {
            display: block;
            margin-top: 6px;
            color: #0f1f3d;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.25;
        }

        .tracker-stat-note {
            display: block;
            margin-top: 4px;
            color: #688099;
            font-size: 13px;
            line-height: 1.5;
        }

        .tracker-summary-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 2px;
        }

        .tracker-visual-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
            gap: 16px;
        }

        .tracker-map-card,
        .tracker-insight-card,
        .tracker-section-card {
            padding: 20px;
        }

        .tracker-panel-top,
        .tracker-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .tracker-panel-title,
        .tracker-section-title {
            margin: 6px 0 0;
            color: #0e1d4a;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.2;
        }

        .tracker-section-head {
            justify-content: flex-start;
            margin-bottom: 14px;
        }

        .tracker-section-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(0, 174, 239, 0.16), rgba(14, 29, 74, 0.10));
            color: #0087bf;
            font-size: 18px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .tracker-map-stage {
            position: relative;
            min-height: 410px;
            margin-top: 16px;
            border-radius: 24px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.76), rgba(255, 255, 255, 0) 30%),
                linear-gradient(180deg, #eef3f7 0%, #e5ebf2 100%);
            border: 1px solid #d8e2ec;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.76),
                0 24px 44px rgba(14, 29, 74, 0.10);
        }

        .tracker-map-stage::before,
        .tracker-map-stage::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        .tracker-map-stage::before {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0) 18%);
        }

        .tracker-map-stage::after {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(204, 214, 226, 0.10));
        }

        .tracker-vector-map-shell,
        .tracker-vector-map {
            position: absolute;
            inset: 0;
        }

        .tracker-vector-map.leaflet-container {
            width: 100%;
            height: 100%;
            background: #eff3f7;
            font-family: inherit;
        }

        .tracker-vector-map.leaflet-container .leaflet-pane,
        .tracker-vector-map.leaflet-container .leaflet-top,
        .tracker-vector-map.leaflet-container .leaflet-bottom {
            z-index: 2;
        }

        .tracker-vector-map.leaflet-container .leaflet-tile-pane {
            filter: saturate(0.94) contrast(1.02);
        }

        .tracker-vector-map.leaflet-container .leaflet-control-zoom {
            margin-top: 92px;
            margin-right: 16px;
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 30px rgba(24, 39, 75, 0.12);
        }

        .tracker-vector-map.leaflet-container .leaflet-control-zoom a {
            width: 56px;
            height: 56px;
            line-height: 54px;
            border: 0;
            background: rgba(255, 255, 255, 0.96);
            color: #29384d;
            font-size: 24px;
            font-weight: 900;
            transition: background-color 160ms ease, color 160ms ease, transform 160ms ease;
        }

        .tracker-vector-map.leaflet-container .leaflet-control-zoom a:hover,
        .tracker-vector-map.leaflet-container .leaflet-control-zoom a:focus {
            background: #ffffff;
            color: #0e1d4a;
            transform: scale(1.02);
        }

        .tracker-vector-map.leaflet-container .leaflet-control-zoom a:first-child {
            border-bottom: 1px solid #d7e0e8;
        }

        .tracker-vector-map.leaflet-container .leaflet-control-zoom a:last-child {
            border-top: 0;
        }

        .tracker-vector-map.leaflet-container .leaflet-control-attribution {
            margin: 0 14px 12px 0;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.90);
            color: #5f6f85;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.03em;
            box-shadow: 0 10px 22px rgba(24, 39, 75, 0.08);
        }

        .tracker-vector-map.leaflet-container .leaflet-control-attribution a {
            color: #1b7db5;
        }

        .tracker-vector-map.leaflet-container .leaflet-marker-icon {
            background: transparent;
            border: 0;
        }

        .tracker-leaflet-marker {
            position: relative;
            width: 28px;
            height: 28px;
        }

        .tracker-leaflet-marker-pulse,
        .tracker-leaflet-marker-core {
            position: absolute;
            border-radius: 999px;
        }

        .tracker-leaflet-marker-pulse {
            inset: 0;
            background: rgba(110, 217, 92, 0.32);
            animation: trackerPing 2.2s ease-out infinite;
        }

        .tracker-leaflet-marker-core {
            inset: 4px;
            background: radial-gradient(circle at 35% 35%, #a1f594 0%, #6fd767 48%, #3c9b37 100%);
            border: 3px solid #ffffff;
            box-shadow: 0 10px 20px rgba(60, 155, 55, 0.32);
        }

        .tracker-map-empty {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            text-align: center;
            color: #4d627b;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.6;
            z-index: 3;
        }

        .tracker-map-locations {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .tracker-map-location {
            position: relative;
            min-height: 110px;
            padding: 16px 18px;
            border-radius: 20px;
            border: 1px solid #dcebf4;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(241, 248, 252, 0.98));
            box-shadow: 0 16px 32px rgba(14, 29, 74, 0.06);
            overflow: hidden;
        }

        .tracker-map-location::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #00aeef 0%, #67d160 100%);
        }

        .tracker-map-location strong {
            display: block;
            margin-top: 8px;
            color: #0f1f3d;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .tracker-map-location small {
            display: block;
            margin-top: 7px;
            color: #688099;
            font-size: 13px;
            line-height: 1.5;
        }

        .tracker-compass-stack {
            display: grid;
            gap: 18px;
            margin-top: 16px;
        }

        .tracker-compass {
            position: relative;
            width: 220px;
            height: 220px;
            margin: 0 auto;
            border-radius: 999px;
            background:
                radial-gradient(circle at center, rgba(255, 255, 255, 0.98) 0 24%, rgba(230, 243, 250, 0.94) 24% 54%, rgba(14, 29, 74, 0.08) 54% 55%, rgba(248, 251, 255, 0.98) 55% 100%);
            box-shadow:
                inset 0 0 0 1px rgba(178, 214, 230, 0.7),
                0 24px 42px rgba(14, 29, 74, 0.08);
        }

        .tracker-compass-ring {
            position: absolute;
            inset: 18px;
            border-radius: 999px;
            border: 1px dashed rgba(14, 29, 74, 0.18);
        }

        .tracker-compass-mark {
            position: absolute;
            color: #8090a7;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.1em;
        }

        .tracker-compass-mark-n { top: 16px; left: 50%; transform: translateX(-50%); }
        .tracker-compass-mark-e { top: 50%; right: 18px; transform: translateY(-50%); }
        .tracker-compass-mark-s { bottom: 16px; left: 50%; transform: translateX(-50%); }
        .tracker-compass-mark-w { top: 50%; left: 18px; transform: translateY(-50%); }

        .tracker-compass-arrow {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 4px;
            height: 86px;
            background: linear-gradient(180deg, #ff5a5f 0%, rgba(255, 90, 95, 0.12) 100%);
            border-radius: 999px;
            transform-origin: 50% calc(100% - 10px);
            transform: translate(-50%, -78px) rotate(var(--tracker-course));
            box-shadow: 0 0 18px rgba(255, 90, 95, 0.25);
        }

        .tracker-compass-arrow::before {
            content: "";
            position: absolute;
            top: -8px;
            left: 50%;
            width: 18px;
            height: 18px;
            transform: translateX(-50%) rotate(45deg);
            border-radius: 3px;
            background: #ff5a5f;
        }

        .tracker-compass-center {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: linear-gradient(135deg, #00aeef 0%, #0e1d4a 100%);
            transform: translate(-50%, -50%);
            box-shadow: 0 0 0 8px rgba(0, 174, 239, 0.12);
        }

        .tracker-compass-readout {
            position: absolute;
            left: 50%;
            bottom: 34px;
            width: 126px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.88);
            text-align: center;
            transform: translateX(-50%);
            box-shadow: 0 14px 26px rgba(14, 29, 74, 0.08);
        }

        .tracker-compass-readout span {
            display: block;
            color: #8494aa;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .tracker-compass-readout strong {
            display: block;
            margin-top: 5px;
            color: #0f1f3d;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .tracker-meter-stack {
            display: grid;
            gap: 12px;
        }

        .tracker-meter {
            padding: 14px;
            border-radius: 18px;
            border: 1px solid #e2eef5;
            background: linear-gradient(180deg, #ffffff, #f6fbff);
        }

        .tracker-meter-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #6d7f96;
            font-size: 13px;
            font-weight: 700;
        }

        .tracker-meter-head strong {
            color: #0f1f3d;
            font-weight: 800;
        }

        .tracker-meter-rail {
            position: relative;
            height: 12px;
            margin-top: 12px;
            border-radius: 999px;
            background: #e7f0f5;
            overflow: hidden;
        }

        .tracker-meter-fill {
            position: absolute;
            inset: 0 auto 0 0;
            border-radius: inherit;
            animation: trackerGrowBar 0.8s ease both;
        }

        .tracker-meter-fill-speed {
            background: linear-gradient(90deg, #00aeef 0%, #0088c7 100%);
        }

        .tracker-meter-fill-distance {
            background: linear-gradient(90deg, #0e1d4a 0%, #1876c8 100%);
        }

        .tracker-meter-fill-draught {
            background: linear-gradient(90deg, #ffb248 0%, #ff5a5f 100%);
        }

        .tracker-sections-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            align-items: start;
        }

        .tracker-facts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 0;
        }

        .tracker-fact {
            padding: 14px 15px;
            border-radius: 16px;
            border: 1px solid #e4eef5;
            background: linear-gradient(180deg, #fcfeff 0%, #f7fbff 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .tracker-fact dt {
            margin: 0 0 6px;
            color: #7d8ea5;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .tracker-fact dd {
            margin: 0;
            color: #12213f;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.55;
            word-break: break-word;
        }

        .tracker-fact dd a {
            color: #0079ab;
            text-decoration: none;
        }

        .tracker-fact dd a:hover {
            text-decoration: underline;
        }

        .tracker-loading-shell {
            display: grid;
            gap: 16px;
        }

        .tracker-loading-card {
            padding: 22px;
        }

        .tracker-loading-top {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(220px, 0.8fr);
            gap: 16px;
        }

        .tracker-loading-visual {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(260px, 0.9fr);
            gap: 16px;
        }

        .tracker-skeleton-line,
        .tracker-skeleton-pill,
        .tracker-skeleton-box,
        .tracker-skeleton-grid span {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            background: linear-gradient(90deg, #edf4f8 0%, #f7fbfe 48%, #edf4f8 100%);
        }

        .tracker-skeleton-line::after,
        .tracker-skeleton-pill::after,
        .tracker-skeleton-box::after,
        .tracker-skeleton-grid span::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9), transparent);
            animation: trackerShimmer 1.4s linear infinite;
        }

        .tracker-skeleton-pill {
            width: 220px;
            height: 38px;
        }

        .tracker-skeleton-line {
            height: 16px;
            margin-top: 12px;
        }

        .tracker-skeleton-line-lg {
            height: 22px;
            width: 72%;
        }

        .tracker-skeleton-line-md {
            width: 88%;
        }

        .tracker-skeleton-line-sm {
            width: 60%;
        }

        .tracker-skeleton-box {
            min-height: 120px;
        }

        .tracker-skeleton-box-map {
            min-height: 320px;
        }

        .tracker-skeleton-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .tracker-skeleton-grid span {
            min-height: 88px;
        }

        @keyframes trackerPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.82; }
        }

        @keyframes trackerSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes trackerFadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes trackerOrbit {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes trackerOrbitReverse {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        @keyframes trackerPing {
            0% {
                transform: scale(0.6);
                opacity: 0.8;
            }
            100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }

        @keyframes trackerGrowBar {
            from { width: 0 !important; }
        }

        @keyframes trackerShimmer {
            to { transform: translateX(100%); }
        }

        @media (max-width: 1199.98px) {
            .tracker-summary-hero,
            .tracker-visual-grid,
            .tracker-loading-top,
            .tracker-loading-visual {
                grid-template-columns: minmax(0, 1fr);
            }

            .tracker-hero-title-row {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 991.98px) {
            .tracker-search-main,
            .tracker-sections-grid,
            .tracker-facts,
            .tracker-map-locations {
                grid-template-columns: minmax(0, 1fr);
            }

            .tracker-summary-stack {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .vessel-live-tracker-shell {
                padding: 12px 12px 22px;
            }

            .tracker-hero-top,
            .tracker-hero-body,
            .tracker-map-card,
            .tracker-insight-card,
            .tracker-section-card,
            .tracker-summary-hero,
            .tracker-status-card,
            .tracker-empty-state,
            .tracker-loading-card {
                padding-left: 16px;
                padding-right: 16px;
            }

            .tracker-hero-title {
                font-size: 25px;
            }

            .tracker-hero-mini-grid,
            .tracker-summary-stack,
            .tracker-skeleton-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .tracker-map-stage {
                min-height: 300px;
            }

            .tracker-vector-map.leaflet-container .leaflet-control-zoom {
                margin-top: 12px;
                margin-right: 12px;
            }

            .tracker-vector-map.leaflet-container .leaflet-control-zoom a {
                width: 48px;
                height: 48px;
                line-height: 46px;
            }

            .tracker-vector-map.leaflet-container .leaflet-control-attribution {
                margin-right: 10px;
                margin-bottom: 10px;
                max-width: calc(100% - 20px);
            }

            .tracker-compass {
                width: 198px;
                height: 198px;
            }

            .tracker-empty-title,
            .tracker-summary-title {
                font-size: 24px;
            }
        }

        @media (max-width: 575.98px) {
            .tracker-search-main {
                gap: 12px;
            }

            .tracker-submit-btn,
            .tracker-back-btn {
                width: 100%;
            }

            .tracker-help-text,
            .tracker-result-summary,
            .tracker-empty-copy {
                font-size: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <script>document.body.classList.add('vessel-live-tracker-page');</script>

    @include('layouts.partials.pcoded-shell-start')

    <div class="vessel-live-tracker-shell">
        <div class="vessel-live-tracker-layout">
            <x-lists.page-header
                title="Live Vessel Tracker"
                subtitle="Search by vessel name or IMO and pull a live vessel snapshot without leaving the portal"
                icon="ti-anchor"
            >
                <x-slot:actions>
                    <a href="{{ route('vessels.index') }}" class="tracker-back-btn">Back to vessels</a>
                </x-slot:actions>
            </x-lists.page-header>

            <section class="tracker-hero-card">
                <div class="tracker-hero-top">
                    <span class="tracker-eyebrow">MarineCaddie live lookup</span>

                    <div class="tracker-hero-title-row">
                        <div>
                            <h1 class="tracker-hero-title">Search a vessel in real time</h1>
                            <p class="tracker-hero-subtitle">
                                Search by vessel name, saved vessel alias, 7-digit IMO, or 9-digit MMSI. Results appear instantly below without reloading the page with a pinned world map view.
                            </p>
                        </div>

                        <div class="tracker-hero-mini-grid">
                            <div class="tracker-hero-mini-card">
                                <span>Live Result</span>
                                <strong>Instant update</strong>
                            </div>
                            <div class="tracker-hero-mini-card">
                                <span>Map view</span>
                                <strong>World pin</strong>
                            </div>
                            <div class="tracker-hero-mini-card">
                                <span>Search inputs</span>
                                <strong>Name / IMO / MMSI</strong>
                            </div>
                            <div class="tracker-hero-mini-card">
                                <span>Route focus</span>
                                <strong>Departure to arrival</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tracker-hero-body">
                    <p class="tracker-help-text">
                        Submit a vessel query to render a live world map pin, movement insight, and detailed facts in one smart dashboard panel. If the live source returns partial data, the page still shows the best available snapshot gracefully.
                    </p>

                    <form
                        id="tracker-search-form"
                        method="POST"
                        action="{{ route('vessels.live-tracker.search') }}"
                        class="tracker-search-form"
                        data-live-tracker-ajax="true"
                        data-suggestions-url="{{ route('vessels.live-tracker.suggestions') }}"
                    >
                        @csrf
                        <div class="tracker-search-main">
                            <div class="tracker-search-stack">
                                <label for="tracker-query" class="tracker-field-label">Vessel name or IMO</label>
                                <div class="tracker-search-field">
                                    <div class="tracker-input-frame">
                                        <input
                                            id="tracker-query"
                                            type="text"
                                            name="query"
                                            value=""
                                            class="tracker-search-input"
                                            autocomplete="off"
                                            aria-autocomplete="list"
                                            aria-expanded="false"
                                            aria-controls="tracker-suggestion-list"
                                            placeholder="Example: CS JOLA or 9791896"
                                        >
                                    </div>

                                    <div id="tracker-suggestions" class="tracker-suggestion-panel" hidden>
                                        <div id="tracker-suggestion-list" class="tracker-suggestion-list" role="listbox" aria-label="Saved vessel suggestions"></div>
                                    </div>
                                </div>
                            </div>

                            <button id="tracker-submit" type="submit" class="tracker-submit-btn">
                                <span class="tracker-submit-loader" aria-hidden="true"></span>
                                <span class="tracker-submit-label">Fetch live details</span>
                            </button>
                        </div>

                        <p class="tracker-search-note">
                            Search examples: <strong>CS JOLA</strong>, <strong>9791896</strong>, <strong>538007348</strong>
                        </p>
                    </form>
                </div>
            </section>

            <div id="tracker-results-mount" class="tracker-results-mount" aria-live="polite">
                @include('Vessels.partials.live-tracker-result', ['result' => $result, 'error' => $error])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('tracker-search-form');
            var mount = document.getElementById('tracker-results-mount');
            var input = document.getElementById('tracker-query');
            var submit = document.getElementById('tracker-submit');
            var suggestionPanel = document.getElementById('tracker-suggestions');
            var suggestionList = document.getElementById('tracker-suggestion-list');

            if (!form || !mount || !input || !submit || !suggestionPanel || !suggestionList || !window.jQuery) {
                return;
            }

            var $ = window.jQuery;
            var pendingRequest = null;
            var pendingSuggestionRequest = null;
            var activeRequestId = 0;
            var activeSuggestionRequestId = 0;
            var suggestionDebounceTimer = null;
            var activeSuggestionIndex = -1;
            var suggestionItems = [];
            var trackerMapStates = new WeakMap();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function loadingMarkup(query) {
                var safeQuery = escapeHtml(query);

                return '' +
                    '<section class="tracker-loading-shell">' +
                        '<div class="tracker-surface tracker-loading-card">' +
                            '<div class="tracker-loading-top">' +
                                '<div>' +
                                    '<div class="tracker-skeleton-pill"></div>' +
                                    '<div class="tracker-skeleton-line tracker-skeleton-line-lg"></div>' +
                                    '<div class="tracker-skeleton-line tracker-skeleton-line-md"></div>' +
                                    '<div class="tracker-skeleton-line tracker-skeleton-line-sm"></div>' +
                                    '<div class="tracker-skeleton-grid">' +
                                        '<span></span><span></span><span></span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="tracker-skeleton-box"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="tracker-loading-visual">' +
                            '<div class="tracker-surface tracker-loading-card tracker-skeleton-box tracker-skeleton-box-map"></div>' +
                            '<div class="tracker-surface tracker-loading-card tracker-skeleton-box"></div>' +
                        '</div>' +
                        '<div class="tracker-status-card alert-info tracker-fade-in">' +
                            '<span class="tracker-status-kicker">Loading live snapshot</span>' +
                            '<div class="tracker-status-copy">Fetching latest public vessel details for ' + safeQuery + '.</div>' +
                        '</div>' +
                    '</section>';
            }

            function setLoadingState(isLoading, query) {
                submit.disabled = isLoading;
                submit.classList.toggle('is-loading', isLoading);
                mount.classList.toggle('is-loading', isLoading);
                mount.setAttribute('aria-busy', isLoading ? 'true' : 'false');

                if (isLoading) {
                    teardownVisuals();
                    mount.innerHTML = loadingMarkup(query);
                }
            }

            function hideSuggestions() {
                suggestionItems = [];
                activeSuggestionIndex = -1;
                suggestionList.innerHTML = '';
                suggestionPanel.hidden = true;
                input.setAttribute('aria-expanded', 'false');
                input.removeAttribute('aria-activedescendant');
            }

            function renderSuggestions(items) {
                suggestionItems = Array.isArray(items) ? items : [];
                activeSuggestionIndex = -1;

                if (!suggestionItems.length) {
                    hideSuggestions();
                    return;
                }

                suggestionList.innerHTML = suggestionItems.map(function (item, index) {
                    return '' +
                        '<button type="button" class="tracker-suggestion-item" id="tracker-suggestion-' + index + '" data-tracker-suggestion-index="' + index + '" role="option" aria-selected="false">' +
                            '<span class="tracker-suggestion-title">' + escapeHtml(item.label || item.value || '') + '</span>' +
                            (item.meta ? '<span class="tracker-suggestion-meta">' + escapeHtml(item.meta) + '</span>' : '') +
                        '</button>';
                }).join('');

                suggestionPanel.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            }

            function setActiveSuggestion(index) {
                var buttons = suggestionList.querySelectorAll('[data-tracker-suggestion-index]');

                if (!buttons.length) {
                    activeSuggestionIndex = -1;
                    input.removeAttribute('aria-activedescendant');
                    return;
                }

                if (index < 0) {
                    index = buttons.length - 1;
                } else if (index >= buttons.length) {
                    index = 0;
                }

                activeSuggestionIndex = index;

                buttons.forEach(function (button, buttonIndex) {
                    var isActive = buttonIndex === activeSuggestionIndex;

                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');

                    if (isActive) {
                        input.setAttribute('aria-activedescendant', button.id);
                        button.scrollIntoView({ block: 'nearest' });
                    }
                });
            }

            function fetchSuggestions(query) {
                if (!query || query.length < 2) {
                    hideSuggestions();
                    return;
                }

                if (pendingSuggestionRequest && typeof pendingSuggestionRequest.abort === 'function') {
                    pendingSuggestionRequest.abort();
                }

                activeSuggestionRequestId += 1;
                var requestId = activeSuggestionRequestId;

                pendingSuggestionRequest = $.ajax({
                    url: form.getAttribute('data-suggestions-url'),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        query: query,
                        _token: csrfToken
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                pendingSuggestionRequest.done(function (response) {
                    if (requestId !== activeSuggestionRequestId) {
                        return;
                    }

                    if ((input.value || '').trim() !== query) {
                        return;
                    }

                    renderSuggestions(response && Array.isArray(response.suggestions) ? response.suggestions : []);
                });

                pendingSuggestionRequest.fail(function (xhr, status) {
                    if (status !== 'abort' && requestId === activeSuggestionRequestId) {
                        hideSuggestions();
                    }
                });

                pendingSuggestionRequest.always(function () {
                    if (requestId === activeSuggestionRequestId) {
                        pendingSuggestionRequest = null;
                    }
                });
            }

            function queueSuggestions() {
                clearTimeout(suggestionDebounceTimer);

                suggestionDebounceTimer = setTimeout(function () {
                    fetchSuggestions((input.value || '').trim());
                }, 150);
            }

            function applySuggestion(index) {
                var item = suggestionItems[index];

                if (!item) {
                    return false;
                }

                input.value = item.value || item.label || '';
                hideSuggestions();
                search((input.value || '').trim());

                return true;
            }

            function parsePayload(scope) {
                var payloadEl = scope.querySelector('.tracker-result-payload');

                if (!payloadEl) {
                    return null;
                }

                try {
                    return JSON.parse(payloadEl.textContent || '{}');
                } catch (error) {
                    return null;
                }
            }

            function normalizeCoordinate(value) {
                return typeof value === 'number' && isFinite(value) ? value : null;
            }

            function getDefaultMapZoom() {
                return window.innerWidth < 768 ? 2 : 3;
            }

            function getTrackerMapState(scope) {
                var state = trackerMapStates.get(scope);

                if (!state) {
                    state = { map: null, marker: null, mapLayer: null, latitude: null, longitude: null };
                    trackerMapStates.set(scope, state);
                }

                return state;
            }

            function destroyTrackerMap(scope) {
                var state = trackerMapStates.get(scope);

                if (!state) {
                    return;
                }

                if (state.map && typeof state.map.remove === 'function') {
                    state.map.remove();
                }

                trackerMapStates.delete(scope);
            }

            function teardownVisuals() {
                mount.querySelectorAll('[data-tracker-result="loaded"]').forEach(function (scope) {
                    destroyTrackerMap(scope);
                });
            }

            function appendMapFallback(mapLayer, message) {
                mapLayer.innerHTML = '';

                var empty = document.createElement('div');
                empty.className = 'tracker-map-empty';
                empty.textContent = message;
                mapLayer.appendChild(empty);
            }

            function createTrackerMarkerIcon() {
                if (!window.L || typeof window.L.divIcon !== 'function') {
                    return null;
                }

                return window.L.divIcon({
                    className: 'tracker-leaflet-marker',
                    html: '<span class="tracker-leaflet-marker-pulse"></span><span class="tracker-leaflet-marker-core"></span>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
            }

            function buildTrackerMarkerLabel(scope, payload) {
                var title = scope.querySelector('.tracker-summary-title');
                var parts = [];

                if (title && title.textContent) {
                    parts.push(title.textContent.trim());
                }

                if (payload.area) {
                    parts.push(payload.area);
                }

                if (payload.current_port) {
                    parts.push(payload.current_port);
                }

                return parts.filter(Boolean).join(' • ') || 'Vessel live position';
            }

            function renderTrackerMap(scope) {
                var mapLayer = scope.querySelector('[data-tracker-leaflet-map]');
                var payload = parsePayload(scope);
                var latitude;
                var longitude;
                var state;
                var zoomLevel;
                var markerLabel;

                if (!mapLayer || !payload) {
                    destroyTrackerMap(scope);
                    return;
                }

                latitude = normalizeCoordinate(payload.latitude);
                longitude = normalizeCoordinate(payload.longitude);
                state = getTrackerMapState(scope);

                if (!window.L || typeof window.L.map !== 'function') {
                    destroyTrackerMap(scope);
                    appendMapFallback(mapLayer, 'Interactive map could not load right now, but the live vessel details are still shown below.');
                    return;
                }

                if (latitude === null || longitude === null || longitude < -180 || longitude > 180 || latitude < -90 || latitude > 90) {
                    destroyTrackerMap(scope);
                    appendMapFallback(mapLayer, 'Live coordinates are not available right now, but the other vessel details are still shown below.');
                    return;
                }

                if (!state.map || state.mapLayer !== mapLayer) {
                    destroyTrackerMap(scope);
                    state = getTrackerMapState(scope);
                    state.mapLayer = mapLayer;
                    mapLayer.innerHTML = '';

                    state.map = window.L.map(mapLayer, {
                        zoomControl: false,
                        scrollWheelZoom: false,
                        doubleClickZoom: true,
                        touchZoom: true,
                        boxZoom: false,
                        keyboard: true,
                        worldCopyJump: true,
                        minZoom: 2,
                        maxZoom: 7,
                        zoomSnap: 0.5,
                        zoomDelta: 0.5
                    });

                    window.L.control.zoom({ position: 'topright' }).addTo(state.map);

                    window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 19,
                        attribution: 'Tiles &copy; Esri'
                    }).addTo(state.map);

                    state.marker = window.L.marker([latitude, longitude], {
                        icon: createTrackerMarkerIcon()
                    }).addTo(state.map);
                } else if (state.marker) {
                    state.marker.setLatLng([latitude, longitude]);
                }

                markerLabel = buildTrackerMarkerLabel(scope, payload);

                if (state.marker) {
                    if (state.marker.getTooltip()) {
                        state.marker.setTooltipContent(markerLabel);
                    } else {
                        state.marker.bindTooltip(markerLabel, {
                            direction: 'top',
                            offset: [0, -10],
                            opacity: 0.92
                        });
                    }
                }

                zoomLevel = getDefaultMapZoom();
                state.latitude = latitude;
                state.longitude = longitude;
                state.map.setView([latitude, longitude], zoomLevel, { animate: false });

                window.requestAnimationFrame(function () {
                    if (state.map) {
                        state.map.invalidateSize(false);
                    }
                });
            }

            function refreshVisuals() {
                var scope = mount.querySelector('[data-tracker-result="loaded"]');

                if (!scope) {
                    return;
                }

                window.requestAnimationFrame(function () {
                    renderTrackerMap(scope);
                });
            }

            function search(query) {
                if (!query) {
                    input.focus();
                    return;
                }

                hideSuggestions();
                clearTimeout(suggestionDebounceTimer);

                if (pendingSuggestionRequest && typeof pendingSuggestionRequest.abort === 'function') {
                    pendingSuggestionRequest.abort();
                }

                if (pendingRequest && typeof pendingRequest.abort === 'function') {
                    pendingRequest.abort();
                }

                setLoadingState(true, query);
                activeRequestId += 1;
                var requestId = activeRequestId;

                pendingRequest = $.ajax({
                    url: form.getAttribute('action'),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        query: query,
                        _token: csrfToken
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                pendingRequest.done(function (response) {
                    if (requestId !== activeRequestId) {
                        return;
                    }

                    teardownVisuals();
                    mount.innerHTML = response && response.html ? response.html : '';
                    refreshVisuals();
                });

                pendingRequest.fail(function (xhr, status) {
                    if (status === 'abort') {
                        return;
                    }

                    if (requestId !== activeRequestId) {
                        return;
                    }

                    teardownVisuals();
                    mount.innerHTML = '' +
                        '<div class="tracker-status-card alert-danger tracker-fade-in">' +
                            '<span class="tracker-status-kicker">Live lookup update</span>' +
                            '<div class="tracker-status-copy">The live result could not be loaded right now. Please try again.</div>' +
                        '</div>';
                });

                pendingRequest.always(function () {
                    if (requestId === activeRequestId) {
                        setLoadingState(false, query);
                        pendingRequest = null;
                    }
                });
            }

            $(form).on('submit', function (event) {
                event.preventDefault();
                search((input.value || '').trim());
            });

            $(input).on('input', function () {
                queueSuggestions();
            });

            $(input).on('focus', function () {
                if ((input.value || '').trim().length >= 2) {
                    queueSuggestions();
                }
            });

            $(input).on('keydown', function (event) {
                if (suggestionPanel.hidden) {
                    if (event.key === 'Escape') {
                        hideSuggestions();
                    }

                    return;
                }

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActiveSuggestion(activeSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    setActiveSuggestion(activeSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter' && activeSuggestionIndex > -1) {
                    event.preventDefault();
                    applySuggestion(activeSuggestionIndex);
                    return;
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    hideSuggestions();
                }
            });

            $(suggestionList).on('mouseenter', '[data-tracker-suggestion-index]', function () {
                setActiveSuggestion(Number(this.getAttribute('data-tracker-suggestion-index')));
            });

            $(suggestionList).on('click', '[data-tracker-suggestion-index]', function () {
                applySuggestion(Number(this.getAttribute('data-tracker-suggestion-index')));
            });

            $(mount).on('click', '[data-tracker-example]', function () {
                var query = (this.getAttribute('data-tracker-example') || '').trim();
                input.value = query;
                hideSuggestions();
                search(query);
            });

            document.addEventListener('click', function (event) {
                if (!form.contains(event.target)) {
                    hideSuggestions();
                }
            });

            window.addEventListener('resize', function () {
                clearTimeout(window.__trackerResizeTimer);
                window.__trackerResizeTimer = setTimeout(refreshVisuals, 120);
            });

            window.addEventListener('beforeunload', function () {
                teardownVisuals();
                clearTimeout(suggestionDebounceTimer);
            });

            refreshVisuals();
        });
    </script>
@endpush
