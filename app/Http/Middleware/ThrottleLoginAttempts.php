<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLoginAttempts
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_MINUTES = 1;

    public function __construct(private ThrottleRequests $throttle) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $this->throttle->handle(
            $request,
            $next,
            self::MAX_ATTEMPTS,
            self::DECAY_MINUTES
        );
    }
}
