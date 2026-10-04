<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Master setup</title>
    @include('install.partials.styles')
</head>
<body>
<main class="wizard wizard-narrow">
    <header class="wizard-head">
        <h1>Master setup</h1>
        <p>Create a new company portal in its own folder.</p>
    </header>

    <form class="card" method="POST" action="{{ route('master.login.attempt') }}" novalidate>
        @csrf

        @unless ($passwordConfigured)
            <div class="alert alert-bad">
                No master password is set yet. On the server, run <strong>php artisan master:password</strong> in this folder, then reload.
            </div>
        @endunless

        <label for="master_password">Master password</label>
        <input type="password" id="master_password" name="master_password" maxlength="255" required autofocus
               autocomplete="current-password" class="@error('master_password') is-invalid @enderror" @disabled(! $passwordConfigured)>
        @error('master_password')<div class="field-error">{{ $message }}</div>@enderror

        <div class="actions">
            <button type="submit" class="btn btn-primary btn-block" @disabled(! $passwordConfigured)>Log in</button>
        </div>
    </form>
</main>
</body>
</html>
