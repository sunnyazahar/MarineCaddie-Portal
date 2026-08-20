<?php

/**
 * Local smoke via HTTP kernel (no cookie export). Checks markup + init script in HTML.
 */
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Models\User::query()
    ->where('is_active', 1)
    ->where('role', 'Admin')
    ->orderBy('id')
    ->first();

if (!$user) {
    fwrite(STDERR, "FAIL: no active Admin user\n");
    exit(1);
}

$session = app('session.store');
$session->start();
auth()->login($user);
$session->put('otp_verified', true);
$session->save();

Illuminate\Support\Facades\DB::table('sessions')
    ->where('id', $session->getId())
    ->update(['user_id' => $user->getAuthIdentifier()]);

function fetchAuthenticatedHtml(Illuminate\Contracts\Http\Kernel $kernel, string $path, string $sessionId): array
{
    $request = Illuminate\Http\Request::create($path, 'GET');
    $request->cookies->set(config('session.cookie'), $sessionId);

    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    return [
        'status' => $response->getStatusCode(),
        'location' => $response->headers->get('Location'),
        'html' => $response->getContent(),
    ];
}

$checks = [];

function check(string $label, bool $ok, string $detail = ''): void
{
    global $checks;
    $checks[] = ['ok' => $ok, 'label' => $label, 'detail' => $detail];
    $mark = $ok ? 'PASS' : 'FAIL';
    fwrite(STDOUT, "{$mark}: {$label}" . ($detail ? " ({$detail})" : '') . "\n");
}

$pages = [
    'Agents create' => '/Agents/create',
    'customers create' => '/customers/create',
];

foreach ($pages as $label => $path) {
    $result = fetchAuthenticatedHtml($kernel, $path, $session->getId());

    if ($result['status'] === 302 && str_contains((string) $result['location'], 'login')) {
        check("{$label} auth", false, 'redirected to login');
        continue;
    }

    check("{$label} HTTP 200", $result['status'] === 200, (string) $result['status']);

    $html = $result['html'];
    check("{$label} country-select markup", str_contains($html, 'data-country-select="1"'));
    check("{$label} init script", str_contains($html, 'MarineCaddieInitCountrySelect'));
    check("{$label} flag option data", str_contains($html, 'data-flag-url') || str_contains($html, 'data-iso'));
    check("{$label} no inline formatCountry", ! preg_match('/function formatCountry\b|function formatFlag\b/', $html));

    if ($label === 'customers create') {
        check('customers country required rule', str_contains($html, 'country: "required"'));
        check('customers no formatCountry on select2-field', ! preg_match('/\.select2-field\'\)\.select2\([\s\S]*formatCountry/s', $html));
    }
}

$failed = array_filter($checks, fn ($c) => ! $c['ok']);
exit(count($failed) ? 1 : 0);
