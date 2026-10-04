<?php

namespace App\Http\Controllers;

use App\Support\Branding;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Public company logo / favicon (used by layouts and as the mail logo URL).
 */
class BrandingController extends Controller
{
    public function logo(): BinaryFileResponse|Response
    {
        $path = Branding::logoPath();
        $mime = Branding::logoMime();

        if ($path === null || $mime === null) {
            return $this->initialsIcon();
        }

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function initialsIcon(): Response
    {
        $initials = e(Branding::initials());
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">'
            . '<rect width="64" height="64" rx="12" fill="#1f3b57"/>'
            . '<text x="32" y="41" font-family="Arial, sans-serif" font-size="26" font-weight="700" fill="#ffffff" text-anchor="middle">'
            . $initials . '</text></svg>';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'",
        ]);
    }
}
