<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DenyOperationsAdministrationWrite
{
    /**
     * Administration modules that operations users may only view.
     *
     * @var list<string>
     */
    private array $routePrefixes = [
        'offices.',
        'hub.',
        'agents.',
        'customers.',
        'contacts.',
        'suppliers.',
        'other-companies.',
        'vessels.',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isOperations()) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();
        if (! $this->isProtectedRoute($routeName)) {
            return $next($request);
        }

        if ($this->isReadOnlyRequest($request, $routeName)) {
            return $next($request);
        }

        abort(403, 'This section is read-only for operations users.');
    }

    private function isProtectedRoute(string $routeName): bool
    {
        foreach ($this->routePrefixes as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function isReadOnlyRequest(Request $request, string $routeName): bool
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return false;
        }

        return ! str_contains($routeName, '.create');
    }
}
