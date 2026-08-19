<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLoginActivity;
use App\Repositories\Contracts\UserLoginActivityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginActivityService
{
    public function __construct(
        private UserLoginActivityRepositoryInterface $loginActivities,
    ) {}

    public function record(Request $request, User $user): UserLoginActivity
    {
        $this->loginActivities->closeOpenSessionsForUser($user->id);

        $clientContext = $request->session()->pull('login_client_context', []);
        $userAgent = (string) $request->userAgent();
        $device = $this->parseUserAgent($userAgent);
        $ipLocation = $this->resolveIpLocation($request->ip());

        return $this->loginActivities->create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent ?: null,
            'browser' => $device['browser'],
            'browser_version' => $device['browser_version'],
            'operating_system' => $device['operating_system'],
            'device_type' => $device['device_type'],
            'screen_resolution' => $this->stringValue($clientContext['screen_resolution'] ?? null, 255),
            'language' => $this->stringValue($clientContext['language'] ?? null, 255),
            'timezone' => $this->stringValue($clientContext['timezone'] ?? null, 255),
            'browser_latitude' => $this->coordinate($clientContext['latitude'] ?? null, -90, 90),
            'browser_longitude' => $this->coordinate($clientContext['longitude'] ?? null, -180, 180),
            'browser_location_accuracy' => $this->positiveNumber($clientContext['accuracy'] ?? null),
            'ip_latitude' => $this->coordinate($ipLocation['latitude'] ?? null, -90, 90),
            'ip_longitude' => $this->coordinate($ipLocation['longitude'] ?? null, -180, 180),
            'city' => $this->stringValue($ipLocation['city'] ?? null, 255),
            'region' => $this->stringValue($ipLocation['region'] ?? null, 255),
            'country' => $this->stringValue($ipLocation['country'] ?? null, 255),
            'country_code' => $this->stringValue($ipLocation['country_code'] ?? null, 2),
            'logged_in_at' => now(),
        ]);
    }

    private function parseUserAgent(string $userAgent): array
    {
        $browser = 'Unknown';
        $browserVersion = null;

        foreach ([
            'Edge' => '/Edg(?:A|iOS)?\/([\d.]+)/',
            'Opera' => '/OPR\/([\d.]+)/',
            'Chrome' => '/(?:Chrome|CriOS)\/([\d.]+)/',
            'Firefox' => '/(?:Firefox|FxiOS)\/([\d.]+)/',
            'Safari' => '/Version\/([\d.]+).*Safari\//',
        ] as $name => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                $browser = $name;
                $browserVersion = $matches[1];
                break;
            }
        }

        $operatingSystem = match (true) {
            preg_match('/Windows NT 10\.0/', $userAgent) === 1 => 'Windows 10/11',
            preg_match('/Windows NT 6\.3/', $userAgent) === 1 => 'Windows 8.1',
            preg_match('/Windows NT 6\.1/', $userAgent) === 1 => 'Windows 7',
            preg_match('/Android ([\d.]+)/', $userAgent, $matches) === 1 => 'Android ' . $matches[1],
            preg_match('/(?:iPhone|CPU) OS ([\d_]+)/', $userAgent, $matches) === 1 => 'iOS ' . str_replace('_', '.', $matches[1]),
            preg_match('/Mac OS X ([\d_]+)/', $userAgent, $matches) === 1 => 'macOS ' . str_replace('_', '.', $matches[1]),
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown',
        };

        $deviceType = match (true) {
            preg_match('/iPad|Tablet/i', $userAgent) === 1 => 'Tablet',
            preg_match('/Mobile|Android|iPhone/i', $userAgent) === 1 => 'Mobile',
            default => 'Desktop',
        };

        return [
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'operating_system' => $operatingSystem,
            'device_type' => $deviceType,
        ];
    }

    private function resolveIpLocation(?string $ipAddress): array
    {
        if (! $ipAddress || ! filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        )) {
            return [];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(2)
                ->get('https://ipwho.is/' . rawurlencode($ipAddress));

            if (! $response->successful() || $response->json('success') !== true) {
                return [];
            }

            return (array) $response->json();
        } catch (\Throwable) {
            return [];
        }
    }

    private function stringValue(mixed $value, int $maxLength): ?string
    {
        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        return mb_substr(trim((string) $value), 0, $maxLength);
    }

    private function coordinate(mixed $value, float $minimum, float $maximum): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $coordinate = (float) $value;

        return $coordinate >= $minimum && $coordinate <= $maximum ? $coordinate : null;
    }

    private function positiveNumber(mixed $value): ?float
    {
        return is_numeric($value) && (float) $value >= 0 ? (float) $value : null;
    }
}
