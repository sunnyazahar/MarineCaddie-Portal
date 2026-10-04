<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Installing {{ $company }}</title>
    @include('install.partials.styles')
</head>
<body>
<main class="wizard wizard-narrow">
    <header class="wizard-head">
        <h1>Installing {{ $company }}</h1>
        <p>The folder is ready. Creating tables and the admin account now.</p>
    </header>

    {{-- No CSRF field: this posts to the new folder's installer; the one-time token is the credential. --}}
    <form class="card" id="handoffForm" method="POST" action="{{ $action }}" autocomplete="off">
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="spinner" aria-hidden="true"></div>
        <ul class="summary">
            <li><span>Folder</span><span>{{ $path }}</span></li>
            <li><span>Web address</span><span>{{ $appUrl }}</span></li>
        </ul>
        <p class="hint" style="text-align:center; margin-top:14px">Please keep this page open — the login page opens when installation finishes.</p>

        <div class="actions">
            <button type="submit" class="btn btn-primary btn-block" id="handoffBtn">Continue installation</button>
        </div>
    </form>
</main>
<script>
(function () {
    'use strict';
    var form = document.getElementById('handoffForm');
    var button = document.getElementById('handoffBtn');
    form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Installing…';
    });
    button.disabled = true;
    button.textContent = 'Installing…';
    form.submit();
})();
</script>
</body>
</html>
