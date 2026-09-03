<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DenyAccountsWriteOutsideBilling
{
    /**
     * Routes Accounts may still POST (UX only — not module CRUD).
     *
     * @var list<string>
     */
    private array $allowedWriteRoutes = [
        'notifications.mark-all-read',
        'notifications.mark-read',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAccounts()) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();

        if ($this->isBillingRoute($routeName)) {
            return $next($request);
        }

        if ($this->isAllowedWriteRoute($routeName)) {
            return $next($request);
        }

        if ($this->isReadOnlyRequest($request, $routeName)) {
            return $next($request);
        }

        abort(403, 'This section is read-only for Accounts users. Only Billing can be modified.');
    }

    private function isBillingRoute(string $routeName): bool
    {
        return str_starts_with($routeName, 'billing.');
    }

    private function isAllowedWriteRoute(string $routeName): bool
    {
        return in_array($routeName, $this->allowedWriteRoutes, true);
    }

    private function isReadOnlyRequest(Request $request, string $routeName): bool
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return false;
        }

        return ! $this->isCreatePage($routeName);
    }

    private function isCreatePage(string $routeName): bool
    {
        if ($routeName === '') {
            return false;
        }

        if (str_contains($routeName, '.create')) {
            return true;
        }

        return str_starts_with($routeName, 'create-');
    }
}
