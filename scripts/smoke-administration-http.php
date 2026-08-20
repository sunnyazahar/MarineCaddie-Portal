<?php

/**
 * Authenticated HTTP kernel smoke for Administration modules.
 * GET pages + AJAX lists + empty POST (validation only, no DB writes).
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

if (! $user) {
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

$checks = [];
$failedDetails = [];

function check(string $label, bool $ok, string $detail = ''): void
{
    global $checks, $failedDetails;
    $checks[] = ['ok' => $ok, 'label' => $label];
    $mark = $ok ? 'PASS' : 'FAIL';
    fwrite(STDOUT, "{$mark}: {$label}" . ($detail !== '' ? " ({$detail})" : '') . "\n");
    if (! $ok) {
        $failedDetails[] = $label . ($detail !== '' ? " — {$detail}" : '');
    }
}

function looksLikeException(string $html): ?string
{
    if (preg_match('/BadMethodCallException|ErrorException|Illuminate\\\\View\\\\ViewException|Undefined variable|does not exist\./i', $html, $m)) {
        if (preg_match('/class="exception-message[^"]*"[^>]*>\s*([^<]+)/i', $html, $msg)) {
            return trim(html_entity_decode($msg[1]));
        }
        return $m[0];
    }

    return null;
}

function requestPage(
    Illuminate\Contracts\Http\Kernel $kernel,
    string $method,
    string $path,
    string $sessionId,
    array $payload = [],
    bool $ajax = false
): array {
    $request = Illuminate\Http\Request::create($path, $method, $payload);
    $request->cookies->set(config('session.cookie'), $sessionId);
    if ($ajax) {
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');
    }

    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    return [
        'status' => $response->getStatusCode(),
        'location' => (string) $response->headers->get('Location'),
        'body' => $response->getContent(),
    ];
}

function assertOkPage(array $result, string $label, array $mustContain = []): void
{
    if ($result['status'] === 302 && str_contains($result['location'], 'login')) {
        check("{$label} auth", false, 'redirected to login');
        return;
    }

    $okStatus = in_array($result['status'], [200, 201], true);
    $exception = is_string($result['body']) ? looksLikeException($result['body']) : null;
    check("{$label} HTTP {$result['status']}", $okStatus && $exception === null, $exception ?: (string) $result['status']);

    if (! $okStatus || $exception) {
        return;
    }

    foreach ($mustContain as $needle) {
        check("{$label} has {$needle}", str_contains((string) $result['body'], $needle));
    }
}

$sessionId = $session->getId();
$csrf = $session->token();

$agentId = App\Models\Agent::query()->orderBy('id')->value('id');
$hubId = App\Models\Hub::query()->orderBy('id')->value('id');
$supplierId = App\Models\Supplier::query()->orderBy('id')->value('id');
$otherCompanyId = App\Models\OtherCompany::query()->orderBy('id')->value('id');
$officeId = App\Models\Office::query()->orderBy('id')->value('id');
$customerId = App\Models\Customer::query()->orderBy('id')->value('id');

$pages = [
    ['Agents list', '/Agents', ['agents-table', 'bindAjaxListFilters', 'filter-agent-name']],
    ['Agents create', '/Agents/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
    ['Customers list', '/customers', ['offices-table', 'filter-customer-search', 'customer-filter-multiselect']],
    ['Customers create', '/customers/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
    ['Hubs list', '/hubs', ['offices-table', 'filter-hub-name']],
    ['Hubs create', '/hubs/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
    ['Suppliers list', '/Suppliers', ['suppliers-table']],
    ['Suppliers create', '/Suppliers/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
    ['Other companies list', '/other-companies', ['other-companies-table', 'bindAjaxListFilters', 'company-filter-multiselect']],
    ['Other companies create', '/other-companies/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
    ['Offices list', '/offices', ['offices-table']],
    ['Offices create', '/offices/create', ['data-country-select="1"', 'MarineCaddieInitCountrySelect']],
];

if ($agentId) {
    $pages[] = ['Agents edit', "/Agents/edit/{$agentId}", ['data-country-select="1"']];
    $pages[] = ['Agents contact create', "/Agents/{$agentId}/contacts/create", ['contacts/store']];
}
if ($hubId) {
    $pages[] = ['Hubs show/edit', "/hubs/{$hubId}", ['data-country-select="1"']];
}
if ($supplierId) {
    $pages[] = ['Suppliers edit', "/Suppliers/edit/{$supplierId}", ['data-country-select="1"']];
}
if ($otherCompanyId) {
    $pages[] = ['Other companies edit', "/other-companies/{$otherCompanyId}/edit", ['data-country-select="1"']];
}
if ($officeId) {
    $pages[] = ['Offices edit', "/offices/edit/{$officeId}", ['data-country-select="1"']];
}
if ($customerId) {
    $pages[] = ['Customers edit', "/customers/edit/{$customerId}", ['data-country-select="1"']];
}

foreach ($pages as [$label, $path, $needles]) {
    assertOkPage(requestPage($kernel, 'GET', $path, $sessionId), $label, $needles);
}

$ajaxLists = [
    'Agents AJAX' => '/Agents',
    'Customers AJAX' => '/customers',
    'Hubs AJAX' => '/hubs',
    'Suppliers AJAX' => '/Suppliers',
    'Other companies AJAX' => '/other-companies',
];

foreach ($ajaxLists as $label => $path) {
    $result = requestPage($kernel, 'GET', $path, $sessionId, ['page' => 1], true);
    $body = (string) $result['body'];
    $json = json_decode($body, true);
    $okJson = is_array($json) && array_key_exists('html', $json) && array_key_exists('pagination', $json);
    $exception = looksLikeException($body);
    check("{$label} JSON", $result['status'] === 200 && $okJson && $exception === null, $exception ?: (string) $result['status']);
}

$validationPosts = [
    ['Agents store validation', 'POST', '/Agents/store'],
    ['Customers store validation', 'POST', '/customers'],
    ['Hubs store validation', 'POST', '/hubs'],
    ['Suppliers store validation', 'POST', '/Suppliers'],
    ['Other companies store validation', 'POST', '/other-companies'],
    ['Offices store validation', 'POST', '/offices/store'],
];

foreach ($validationPosts as [$label, $method, $path]) {
    $result = requestPage($kernel, $method, $path, $sessionId, ['_token' => $csrf]);
    $exception = is_string($result['body']) ? looksLikeException($result['body']) : null;
    $ok = in_array($result['status'], [302, 422], true) && $exception === null;
    check($label, $ok, $exception ?: "HTTP {$result['status']}");
}

$failed = array_filter($checks, fn ($c) => ! $c['ok']);
fwrite(STDOUT, "\n" . (count($checks) - count($failed)) . '/' . count($checks) . " passed\n");

if ($failedDetails) {
    fwrite(STDERR, "Failures:\n- " . implode("\n- ", $failedDetails) . "\n");
}

exit(count($failed) ? 1 : 0);
