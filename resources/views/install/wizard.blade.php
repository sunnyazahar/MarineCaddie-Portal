<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($master) ? 'Master setup' : 'Setup' }}</title>
    @include('install.partials.styles')
</head>
<body>
@php
    // Master setup (APP_MODE=master) reuses this wizard: it adds a folder name and
    // derives the web address instead of asking for it.
    $master = $master ?? null;
    $stepForField = [
        'company_name' => 2, 'company_logo' => 2, 'folder' => 2,
        'db_host' => 3, 'db_port' => 3, 'db_database' => 3, 'db_username' => 3, 'db_password' => 3,
        'admin_name' => 4, 'admin_email' => 4, 'admin_password' => 4,
        'app_url' => 5, 'install' => 5,
    ];
    $initialStep = 1;
    foreach ($stepForField as $field => $step) {
        if ($errors->has($field)) {
            $initialStep = $step;
            break;
        }
    }
    $formAction = $master ? route('master.provision') : route('install.run');
    $testUrl = $master ? route('master.test-database') : route('install.test-database');
@endphp
<main class="wizard">
    @if ($master)
        <div class="wizard-topbar">
            <form method="POST" action="{{ route('master.logout') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-small">Log out</button>
            </form>
        </div>
    @endif

    <header class="wizard-head">
        @if ($master)
            <h1>Set up a new company</h1>
            <p>Creates a new folder with its own copy of the portal, database and admin account.</p>
        @else
            <h1>Welcome — let's set up your portal</h1>
            <p>This takes about a minute. You will need your MySQL database details.</p>
        @endif
    </header>

    <ol class="steps" id="stepper">
        <li data-step="1">Requirements</li>
        <li data-step="2">Company</li>
        <li data-step="3">Database</li>
        <li data-step="4">Admin</li>
        <li data-step="5">Install</li>
    </ol>

    <form class="card" id="installForm" method="POST" action="{{ $formAction }}" enctype="multipart/form-data" novalidate
          data-initial-step="{{ $initialStep }}" data-test-url="{{ $testUrl }}"
          @if ($master) data-base-url="{{ $master['baseUrl'] }}" data-target-root="{{ $master['targetRoot'] }}" @endif>
        @csrf

        @if ($errors->has('handoff'))
            <div class="alert alert-bad">{{ $errors->first('handoff') }}</div>
        @endif
        @if ($errors->has('install'))
            <div class="alert alert-bad">{{ $errors->first('install') }}</div>
        @endif

        <section class="step" data-step="1">
            <h2>{{ $master ? 'Before you start' : 'Server requirements' }}</h2>
            <p class="lead">{{ $master ? 'The master setup needs these to create new company folders.' : 'The server must meet these requirements before installing.' }}</p>
            <ul class="checks">
                @foreach ($checks as $check)
                    <li>
                        <span><span class="badge {{ $check['ok'] ? 'ok' : 'bad' }}">{{ $check['ok'] ? '✓' : '✕' }}</span>{{ $check['label'] }}</span>
                        <span class="detail">{{ $check['detail'] }}</span>
                    </li>
                @endforeach
            </ul>
            @unless ($requirementsPass)
                <div class="alert alert-bad" style="margin-top:16px">Fix the items marked ✕, then reload this page.</div>
            @endunless
            <div class="actions">
                <span></span>
                <button type="button" class="btn btn-primary" data-next @disabled(! $requirementsPass)>Continue</button>
            </div>
        </section>

        <section class="step" data-step="2">
            <h2>{{ $master ? 'New company' : 'Your company' }}</h2>
            <p class="lead">Shown on the login page, menus, emails and PDF documents. You can change it later in Settings.</p>
            <div class="grid">
                <div class="full">
                    <label for="company_name">Company name</label>
                    <input type="text" id="company_name" name="company_name" maxlength="150" required autocomplete="organization"
                           value="{{ old('company_name') }}" class="@error('company_name') is-invalid @enderror">
                    @error('company_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                @if ($master)
                    <div class="full">
                        <label for="folder">Folder name</label>
                        <input type="text" id="folder" name="folder" minlength="2" maxlength="40" required autocomplete="off"
                               pattern="[a-z0-9][a-z0-9\-]{0,38}[a-z0-9]" value="{{ old('folder') }}"
                               class="@error('folder') is-invalid @enderror">
                        <div class="hint">Lower-case letters, numbers and dashes. Web address: <strong id="folderUrl">{{ $master['baseUrl'] }}/…</strong></div>
                        @error('folder')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                @endif
                <div class="full">
                    <label for="company_logo">Company logo <span class="opt">(optional)</span></label>
                    <div class="logo-drop">
                        <div class="logo-preview" id="logoPreview" aria-hidden="true">?</div>
                        <div>
                            <input type="file" id="company_logo" name="company_logo" accept="image/png,image/jpeg,image/webp">
                            <div class="hint">PNG, JPG or WebP, max 2 MB. A wide logo with a transparent background works best.</div>
                            @error('company_logo')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-light" data-prev>Back</button>
                <button type="button" class="btn btn-primary" data-next>Continue</button>
            </div>
        </section>

        <section class="step" data-step="3">
            <h2>Database connection</h2>
            <p class="lead">The installer creates the database if your MySQL user is allowed to. On shared hosting (e.g. Hostinger hPanel), create an empty database and user first, then enter them here.</p>
            <div class="grid">
                <div>
                    <label for="db_host">Host</label>
                    <input type="text" id="db_host" name="db_host" maxlength="255" required value="{{ old('db_host', '127.0.0.1') }}"
                           class="@error('db_host') is-invalid @enderror">
                    @error('db_host')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="db_port">Port</label>
                    <input type="number" id="db_port" name="db_port" min="1" max="65535" required value="{{ old('db_port', '3306') }}"
                           class="@error('db_port') is-invalid @enderror">
                    @error('db_port')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="full">
                    <label for="db_database">Database name</label>
                    <input type="text" id="db_database" name="db_database" maxlength="64" required pattern="[A-Za-z0-9_]+"
                           value="{{ old('db_database') }}" class="@error('db_database') is-invalid @enderror">
                    <div class="hint">Letters, numbers and underscores only. Suggested from your company name. The database must be empty.</div>
                    @error('db_database')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="db_username">Username</label>
                    <input type="text" id="db_username" name="db_username" maxlength="80" required autocomplete="off"
                           value="{{ old('db_username') }}" class="@error('db_username') is-invalid @enderror">
                    @error('db_username')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="db_password">Password</label>
                    <input type="password" id="db_password" name="db_password" maxlength="255" autocomplete="new-password"
                           class="@error('db_password') is-invalid @enderror">
                    @error('db_password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div id="dbResult" role="status" aria-live="polite" style="margin-top:16px"></div>
            <div class="actions">
                <button type="button" class="btn btn-light" data-prev>Back</button>
                <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end">
                    <button type="button" class="btn btn-outline" id="testDbBtn">Test connection</button>
                    <button type="button" class="btn btn-primary" id="dbNextBtn" data-next disabled>Continue</button>
                </div>
            </div>
        </section>

        <section class="step" data-step="4">
            <h2>Administrator account</h2>
            <p class="lead">{{ $master ? 'The company admin logs in with this email and password.' : 'You will log in with this email and password. Keep them safe.' }}</p>
            <div class="grid">
                <div class="full">
                    <label for="admin_name">Full name</label>
                    <input type="text" id="admin_name" name="admin_name" maxlength="255" required autocomplete="name"
                           value="{{ old('admin_name') }}" class="@error('admin_name') is-invalid @enderror">
                    @error('admin_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="full">
                    <label for="admin_email">Email</label>
                    <input type="email" id="admin_email" name="admin_email" maxlength="255" required autocomplete="email"
                           value="{{ old('admin_email') }}" class="@error('admin_email') is-invalid @enderror">
                    @error('admin_email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="admin_password">Password</label>
                    <input type="password" id="admin_password" name="admin_password" minlength="8" maxlength="255" required autocomplete="new-password"
                           class="@error('admin_password') is-invalid @enderror">
                    <div class="hint">At least 8 characters with upper case, lower case and a number.</div>
                    @error('admin_password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="admin_password_confirmation">Confirm password</label>
                    <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" minlength="8" maxlength="255" required autocomplete="new-password">
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-light" data-prev>Back</button>
                <button type="button" class="btn btn-primary" data-next>Continue</button>
            </div>
        </section>

        <section class="step" data-step="5">
            <h2>Ready to install</h2>
            <p class="lead">
                @if ($master)
                    This creates the folder, copies the application, creates all tables, loads countries and ports, and creates the admin account.
                @else
                    This creates all tables, loads countries and ports, and creates your admin account.
                @endif
            </p>
            @unless ($master)
                <div class="grid" style="margin-bottom:16px">
                    <div class="full">
                        <label for="app_url">Application URL</label>
                        <input type="url" id="app_url" name="app_url" maxlength="255" required
                               value="{{ old('app_url', $suggestedAppUrl) }}" class="@error('app_url') is-invalid @enderror">
                        <div class="hint">The address users open in the browser (used in emails and links).</div>
                        @error('app_url')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            @endunless
            <ul class="summary">
                <li><span>Company</span><span data-summary="company_name"></span></li>
                @if ($master)
                    <li><span>Folder</span><span data-summary="folder_path"></span></li>
                    <li><span>Web address</span><span data-summary="folder_url"></span></li>
                @endif
                <li><span>Database</span><span data-summary="db"></span></li>
                <li><span>Admin email</span><span data-summary="admin_email"></span></li>
            </ul>
            <div class="alert alert-info" style="margin-top:16px">
                After installing, add the address, bank details and invoice prefix in <strong>Settings → Company settings</strong>.
                Login OTP stays off until email (SMTP in the server .env file) is set up and a test email succeeds there.
            </div>
            <div class="actions">
                <button type="button" class="btn btn-light" data-prev>Back</button>
                <button type="submit" class="btn btn-primary" id="installBtn">{{ $master ? 'Create company' : 'Install now' }}</button>
            </div>
        </section>
    </form>

    @if ($master && $master['sites'] !== [])
        <section class="card">
            <h2>Companies created here</h2>
            <p class="lead">Newest first.</p>
            <ul class="summary">
                @foreach ($master['sites'] as $site)
                    <li>
                        <span>{{ $site['company'] ?? '' }} <small>· {{ $site['database'] ?? '' }}</small></span>
                        <span><a href="{{ $site['url'] ?? '#' }}" target="_blank" rel="noopener">{{ $site['url'] ?? '' }}</a></span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</main>

<div class="overlay" id="installOverlay" role="alertdialog" aria-modal="true" aria-labelledby="installOverlayTitle">
    <div class="overlay-box">
        <div class="spinner"></div>
        <strong id="installOverlayTitle">{{ $master ? 'Creating folder…' : 'Installing…' }}</strong>
        <p style="color:#64748b; font-size:14px; margin:8px 0 0">
            {{ $master ? 'Copying the application. This can take a minute or two — please keep this page open.' : 'Creating tables and loading data. Please keep this page open.' }}
        </p>
    </div>
</div>

<script>
(function () {
    'use strict';

    var form = document.getElementById('installForm');
    var steps = Array.prototype.slice.call(form.querySelectorAll('.step'));
    var stepper = Array.prototype.slice.call(document.querySelectorAll('#stepper li'));
    var dbNextBtn = document.getElementById('dbNextBtn');
    var testDbBtn = document.getElementById('testDbBtn');
    var dbResult = document.getElementById('dbResult');
    var dbFields = ['db_host', 'db_port', 'db_database', 'db_username', 'db_password'];
    var current = 1;
    var dbNameTouched = document.getElementById('db_database').value !== '';
    var folderInput = document.getElementById('folder');
    var folderTouched = !!(folderInput && folderInput.value !== '');
    var logoObjectUrl = null;

    function field(id) { return document.getElementById(id); }

    function showStep(step) {
        current = step;
        steps.forEach(function (section) {
            section.classList.toggle('is-active', Number(section.dataset.step) === step);
        });
        stepper.forEach(function (item) {
            var n = Number(item.dataset.step);
            item.classList.toggle('is-active', n === step);
            item.classList.toggle('is-done', n < step);
        });
        if (step === 5) { fillSummary(); }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function sectionValid(step) {
        var section = form.querySelector('.step[data-step="' + step + '"]');
        var inputs = section.querySelectorAll('input[required], input[pattern], input[type=email], input[type=url]');
        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].checkValidity()) {
                inputs[i].reportValidity();
                return false;
            }
        }
        if (step === 4 && field('admin_password').value !== field('admin_password_confirmation').value) {
            field('admin_password_confirmation').setCustomValidity('Passwords do not match.');
            field('admin_password_confirmation').reportValidity();
            field('admin_password_confirmation').setCustomValidity('');
            return false;
        }
        return true;
    }

    function slugify(value, separator, maxLength) {
        var slug = value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
            .replace(/[^a-z0-9]+/g, separator).slice(0, maxLength);
        var edges = new RegExp('^\\' + separator + '+|\\' + separator + '+$', 'g');
        return slug.replace(edges, '');
    }

    function initials(value) {
        var words = value.trim().split(/\s+/).filter(Boolean);
        return ((words[0] || '?').charAt(0) + (words[1] ? words[1].charAt(0) : '')).toUpperCase();
    }

    function folderUrl() {
        return form.dataset.baseUrl + '/' + (folderInput.value || '…');
    }

    function updateFolderUrl() {
        if (folderInput) { field('folderUrl').textContent = folderUrl(); }
    }

    var dbVerified = false;

    function setDbVerified(ok) {
        dbVerified = ok;
        dbNextBtn.disabled = !ok;
    }

    function renderDbResult(ok, message) {
        dbResult.innerHTML = '';
        var box = document.createElement('div');
        box.className = 'alert ' + (ok ? 'alert-ok' : 'alert-bad');
        box.textContent = message;
        dbResult.appendChild(box);
    }

    function fillSummary() {
        form.querySelector('[data-summary="company_name"]').textContent = field('company_name').value;
        form.querySelector('[data-summary="db"]').textContent =
            field('db_database').value + ' @ ' + field('db_host').value + ':' + field('db_port').value;
        form.querySelector('[data-summary="admin_email"]').textContent = field('admin_email').value;
        if (folderInput) {
            form.querySelector('[data-summary="folder_path"]').textContent = form.dataset.targetRoot + '/' + folderInput.value;
            form.querySelector('[data-summary="folder_url"]').textContent = folderUrl();
        }
    }

    form.addEventListener('click', function (event) {
        var target = event.target;
        if (target.matches('[data-next]')) {
            if (sectionValid(current)) { showStep(current + 1); }
        } else if (target.matches('[data-prev]')) {
            showStep(current - 1);
        }
    });

    form.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.tagName === 'INPUT' && current < 5) {
            event.preventDefault();
        }
    });

    field('company_name').addEventListener('input', function () {
        if (!dbNameTouched) { field('db_database').value = slugify(this.value, '_', 64) || 'portal'; }
        if (folderInput && !folderTouched) {
            folderInput.value = slugify(this.value, '-', 40);
            updateFolderUrl();
        }
        if (!logoObjectUrl) { field('logoPreview').textContent = initials(this.value); }
    });

    field('db_database').addEventListener('input', function () { dbNameTouched = true; });

    if (folderInput) {
        folderInput.addEventListener('input', function () {
            folderTouched = true;
            this.value = this.value.toLowerCase();
            updateFolderUrl();
        });
    }

    dbFields.forEach(function (id) {
        field(id).addEventListener('input', function () {
            setDbVerified(false);
            dbResult.innerHTML = '';
        });
    });

    field('company_logo').addEventListener('change', function () {
        var preview = field('logoPreview');
        var file = this.files && this.files[0];
        if (logoObjectUrl) { URL.revokeObjectURL(logoObjectUrl); logoObjectUrl = null; }
        preview.textContent = '';
        if (!file) {
            preview.textContent = initials(field('company_name').value);
            return;
        }
        if (['image/png', 'image/jpeg', 'image/webp'].indexOf(file.type) === -1 || file.size > 2 * 1024 * 1024) {
            this.value = '';
            preview.textContent = initials(field('company_name').value);
            alert('Please choose a PNG, JPG or WebP image up to 2 MB.');
            return;
        }
        logoObjectUrl = URL.createObjectURL(file);
        var img = document.createElement('img');
        img.alt = '';
        img.src = logoObjectUrl;
        preview.appendChild(img);
    });

    testDbBtn.addEventListener('click', function () {
        if (!sectionValid(3)) { return; }
        var body = new FormData();
        dbFields.forEach(function (id) { body.append(id, field(id).value); });

        testDbBtn.disabled = true;
        testDbBtn.textContent = 'Testing…';
        setDbVerified(false);

        fetch(form.dataset.testUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: body,
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (data) {
                return { status: response.status, data: data };
            });
        }).then(function (result) {
            var data = result.data || {};
            if (result.status === 200 && data.ok) {
                renderDbResult(true, data.message);
                setDbVerified(true);
                return;
            }
            var message = data.message || 'Connection test failed.';
            if (data.errors) {
                var first = Object.keys(data.errors)[0];
                message = data.errors[first][0];
            } else if (result.status === 419 || result.status === 401) {
                message = 'Your session expired. Reload the page and try again.';
            } else if (result.status === 429) {
                message = 'Too many attempts. Wait a minute and try again.';
            }
            renderDbResult(false, message);
        }).catch(function () {
            renderDbResult(false, 'Could not reach the server. Check your connection and try again.');
        }).finally(function () {
            testDbBtn.disabled = false;
            testDbBtn.textContent = 'Test connection';
        });
    });

    form.addEventListener('submit', function (event) {
        if (!dbVerified) {
            event.preventDefault();
            showStep(3);
            renderDbResult(false, 'Enter the database password again and click Test connection.');
            return;
        }
        for (var step = 2; step <= 5; step++) {
            if (!sectionValid(step)) {
                event.preventDefault();
                showStep(step);
                sectionValid(step);
                return;
            }
        }
        document.getElementById('installBtn').disabled = true;
        document.getElementById('installOverlay').classList.add('is-visible');
    });

    if (field('company_name').value) {
        field('logoPreview').textContent = initials(field('company_name').value);
    }
    updateFolderUrl();
    var initialStep = Number(form.dataset.initialStep) || 1;
    if (initialStep > 3) {
        // Passwords are never flashed back, so the step can be reached but install needs a fresh DB test.
        dbNextBtn.disabled = false;
    }
    showStep(initialStep);
})();
</script>
</body>
</html>
