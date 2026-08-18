<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PrivateDisk
{
    public const NAME = 'private';

    public static function disk(): Filesystem
    {
        return Storage::disk(self::NAME);
    }

    public static function path(string $relativePath): string
    {
        $relativePath = trim($relativePath);

        if ($relativePath === '' || $relativePath === '0') {
            return self::disk()->path('__missing__');
        }

        $private = self::disk();

        if ($private->exists($relativePath)) {
            return $private->path($relativePath);
        }

        // Temporary fallback for files not yet migrated off the public disk.
        $public = Storage::disk('public');
        if ($public->exists($relativePath)) {
            return $public->path($relativePath);
        }

        return $private->path($relativePath);
    }

    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $private = self::disk();
        if ($private->exists($relativePath)) {
            $private->delete($relativePath);
        }

        $public = Storage::disk('public');
        if ($public->exists($relativePath)) {
            $public->delete($relativePath);
        }
    }

    public static function exists(string $relativePath): bool
    {
        if ($relativePath === '') {
            return false;
        }

        return self::disk()->exists($relativePath)
            || Storage::disk('public')->exists($relativePath);
    }

    public static function sanitizeFilename(?string $filename, string $fallback = 'download'): string
    {
        $filename = trim((string) $filename);
        $filename = basename(str_replace(["\0", "\r", "\n", '\\'], '', $filename));
        $filename = preg_replace('/[^\w.\-]+/u', '_', $filename) ?? '';
        $filename = trim($filename, '._');

        return $filename !== '' ? $filename : $fallback;
    }

    public static function downloadResponse(string $relativePath, ?string $filename = null): Response
    {
        $path = self::path($relativePath);

        if (! is_file($path) || ! is_readable($path)) {
            abort(404);
        }

        $safeFilename = self::sanitizeFilename($filename, basename($path));
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return response()->download($path, $safeFilename, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    public static function imageResponse(string $relativePath, ?string $filename = null): Response
    {
        $path = self::path($relativePath);

        if (! is_file($path) || ! is_readable($path)) {
            abort(404);
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            abort(415, 'Unsupported image type.');
        }

        $safeFilename = self::sanitizeFilename($filename, basename($path));

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $safeFilename . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
