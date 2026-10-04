<?php

namespace App\Http\Middleware;

use App\Support\Installation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Installer routes disappear (404) once the install lock exists. */
class EnsureNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(Installation::isInstalled(), 404);

        return $next($request);
    }
}
