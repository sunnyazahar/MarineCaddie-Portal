<?php

namespace App\Support;

/**
 * Hosts listed in APP_LIVE_HOSTS (config app.live_hosts). Supports exact names
 * and "*.example.com" wildcards (which also match example.com itself).
 */
class LiveHosts
{
    public static function matches(?string $host): bool
    {
        $host = strtolower(trim((string) $host));
        if ($host === '') {
            return false;
        }

        foreach ((array) config('app.live_hosts', []) as $pattern) {
            $pattern = strtolower(trim((string) $pattern));
            if ($pattern === '') {
                continue;
            }

            if (str_starts_with($pattern, '*.')) {
                $root = substr($pattern, 2);
                if ($host === $root || str_ends_with($host, '.' . $root)) {
                    return true;
                }

                continue;
            }

            if ($host === $pattern) {
                return true;
            }
        }

        return false;
    }
}
