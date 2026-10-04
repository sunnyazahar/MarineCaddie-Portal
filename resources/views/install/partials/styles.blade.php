<style>
    :root {
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
        --bg: #f1f5f9;
        --card: #ffffff;
        --brand: #2563eb;
        --brand-dark: #1d4ed8;
        --ok: #16a34a;
        --ok-bg: #f0fdf4;
        --bad: #dc2626;
        --bad-bg: #fef2f2;
        --radius: 14px;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--ink);
        background: radial-gradient(circle at top left, #dbeafe 0, transparent 45%), var(--bg);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 32px 16px;
    }
    .wizard { width: 100%; max-width: 720px; }
    .wizard-narrow { max-width: 440px; }
    .wizard-head { text-align: center; margin-bottom: 22px; }
    .wizard-head h1 { margin: 0 0 6px; font-size: 26px; letter-spacing: -0.02em; }
    .wizard-head p { margin: 0; color: var(--muted); font-size: 15px; }
    .wizard-topbar { display: flex; justify-content: flex-end; margin-bottom: 8px; }
    .steps { display: flex; gap: 6px; margin: 0 0 18px; padding: 0; list-style: none; }
    .steps li {
        flex: 1; text-align: center; font-size: 12px; font-weight: 600; color: var(--muted);
        padding-top: 10px; border-top: 4px solid var(--line); transition: color .2s, border-color .2s;
    }
    .steps li.is-active { color: var(--brand); border-color: var(--brand); }
    .steps li.is-done { color: var(--ok); border-color: var(--ok); }
    .card { background: var(--card); border-radius: var(--radius); box-shadow: 0 10px 30px rgba(15, 23, 42, .08); padding: 28px; }
    .card + .card { margin-top: 18px; }
    .card h2 { margin: 0 0 4px; font-size: 19px; }
    .step { display: none; }
    .step.is-active { display: block; }
    .step h2 { margin: 0 0 4px; font-size: 19px; }
    .step .lead, .card .lead { margin: 0 0 20px; color: var(--muted); font-size: 14px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 16px; }
    .grid .full { grid-column: 1 / -1; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    label .opt { font-weight: 400; color: var(--muted); }
    input[type=text], input[type=email], input[type=password], input[type=number], input[type=url] {
        width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 15px;
        background: #fff; color: var(--ink); transition: border-color .15s, box-shadow .15s;
    }
    input:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3px rgba(37, 99, 235, .15); }
    input.is-invalid { border-color: var(--bad); }
    .hint { font-size: 12px; color: var(--muted); margin-top: 5px; word-break: break-all; }
    .hint strong { color: var(--ink); }
    .field-error { font-size: 12px; color: var(--bad); margin-top: 5px; }
    .checks { list-style: none; margin: 0; padding: 0; border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
    .checks li { display: flex; justify-content: space-between; gap: 12px; padding: 10px 14px; font-size: 14px; border-top: 1px solid var(--line); }
    .checks li:first-child { border-top: 0; }
    .checks .detail { color: var(--muted); font-size: 13px; text-align: right; word-break: break-all; }
    .badge { display: inline-block; min-width: 22px; margin-right: 8px; font-weight: 700; }
    .badge.ok { color: var(--ok); }
    .badge.bad { color: var(--bad); }
    .logo-drop { display: flex; align-items: center; gap: 16px; padding: 14px; border: 1px dashed #cbd5e1; border-radius: 10px; }
    .logo-preview {
        width: 72px; height: 72px; flex: 0 0 72px; border-radius: 12px; background: #eef2ff; color: var(--brand);
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 22px; overflow: hidden;
    }
    .logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .alert { padding: 12px 14px; border-radius: 10px; font-size: 14px; margin-bottom: 16px; }
    .alert-bad { background: var(--bad-bg); color: #991b1b; border: 1px solid #fecaca; }
    .alert-ok { background: var(--ok-bg); color: #166534; border: 1px solid #bbf7d0; }
    .alert-info { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
    .actions { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 24px; }
    .btn {
        appearance: none; border: 0; border-radius: 9px; padding: 11px 20px; font-size: 15px; font-weight: 600; cursor: pointer;
        transition: background .15s, opacity .15s; text-decoration: none; display: inline-block; text-align: center;
    }
    .btn[disabled] { opacity: .55; cursor: not-allowed; }
    .btn-primary { background: var(--brand); color: #fff; }
    .btn-primary:hover:not([disabled]) { background: var(--brand-dark); }
    .btn-light { background: #e2e8f0; color: var(--ink); }
    .btn-outline { background: #fff; color: var(--brand); border: 1px solid var(--brand); }
    .btn-small { padding: 7px 14px; font-size: 13px; }
    .btn-block { width: 100%; }
    .summary { margin: 0; padding: 0; list-style: none; border: 1px solid var(--line); border-radius: 10px; }
    .summary li { display: flex; justify-content: space-between; gap: 12px; padding: 10px 14px; border-top: 1px solid var(--line); font-size: 14px; }
    .summary li:first-child { border-top: 0; }
    .summary span:first-child { color: var(--muted); }
    .summary span:last-child { font-weight: 600; text-align: right; word-break: break-all; }
    .summary a { color: var(--brand); }
    .overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, .55); display: none; align-items: center; justify-content: center; z-index: 10;
    }
    .overlay.is-visible { display: flex; }
    .overlay-box { background: #fff; border-radius: var(--radius); padding: 28px 32px; text-align: center; max-width: 360px; }
    .spinner {
        width: 38px; height: 38px; margin: 0 auto 14px; border-radius: 50%;
        border: 4px solid #dbeafe; border-top-color: var(--brand); animation: spin .9s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @media (max-width: 600px) {
        body { padding: 18px 10px; }
        .card { padding: 20px 16px; }
        .grid { grid-template-columns: 1fr; }
        .steps li { font-size: 0; padding-top: 6px; }
        .checks li, .summary li { flex-direction: column; gap: 2px; }
        .checks .detail, .summary span:last-child { text-align: left; }
        .actions { flex-direction: column-reverse; align-items: stretch; }
        .actions .btn { width: 100%; }
    }
</style>
