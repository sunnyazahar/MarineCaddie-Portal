<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PrivateDisk
{
    public const NAME = 'private';
    private const PUBLIC_FALLBACK_DIRECTORIES = [
        'customer_logos/',
    ];

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

        if (self::allowsPublicFallback($relativePath)) {
            $public = Storage::disk('public');
            if ($public->exists($relativePath)) {
                return $public->path($relativePath);
            }
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

        if (self::allowsPublicFallback($relativePath)) {
            $public = Storage::disk('public');
            if ($public->exists($relativePath)) {
                $public->delete($relativePath);
            }
        }
    }

    public static function exists(string $relativePath): bool
    {
        if ($relativePath === '') {
            return false;
        }

        if (self::disk()->exists($relativePath)) {
            return true;
        }

        return self::allowsPublicFallback($relativePath)
            && Storage::disk('public')->exists($relativePath);
    }

    public static function sanitizeFilename(?string $filename, string $fallback = 'download'): string
    {
        $filename = trim((string) $filename);
        $filename = basename(str_replace(["\0", "\r", "\n", '\\'], '', $filename));
        $filename = preg_replace('/[^\w.\-]+/u', '_', $filename) ?? '';
        $filename = trim($filename, '._');

        return $filename !== '' ? $filename : $fallback;
    }

    public static function downloadResponse(string $relativePath, ?string $filename = null): BinaryFileResponse
    {
        $path = self::path($relativePath);

        if (! is_file($path) || ! is_readable($path)) {
            abort(404);
        }

        $safeFilename = self::sanitizeFilename($filename, basename($path));
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        $extension = strtolower(pathinfo($safeFilename, PATHINFO_EXTENSION) ?: pathinfo($path, PATHINFO_EXTENSION));

        // PDFs/images must be inline so iframe preview and browser tabs can open them.
        // Attachment + sandboxed CSP forces download and blanks the PDF viewer.
        if (self::isInlinePreviewable($mime, $extension)) {
            if ($extension === 'pdf') {
                $mime = 'application/pdf';
            }

            $fallback = preg_replace('/[^\x20-\x7E]/', '_', $safeFilename) ?: 'document';

            return response()->file($path, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ])->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $safeFilename, $fallback);
        }

        return response()->download($path, $safeFilename, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    private static function isInlinePreviewable(string $mime, string $extension): bool
    {
        if ($mime === 'application/pdf' || $extension === 'pdf') {
            return true;
        }

        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)
            || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    public static function imageResponse(string $relativePath, ?string $filename = null): BinaryFileResponse
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

    private static function allowsPublicFallback(string $relativePath): bool
    {
        foreach (self::PUBLIC_FALLBACK_DIRECTORIES as $directory) {
            if (str_starts_with($relativePath, $directory)) {
                return true;
            }
        }

        return false;
    }
}
