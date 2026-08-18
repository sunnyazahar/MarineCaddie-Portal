<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MailAttachmentStagingService
{
    private const DISK = 'local';
    private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp'];

    /**
     * @return array<int, array{path: string, filename: string, mime: string}>
     */
    public function stageFromRequest(Request $request): array
    {
        $staged = [];

        foreach ($request->file('files', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower((string) $file->getClientOriginalExtension());
            if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
                continue;
            }

            $filename = $file->getClientOriginalName();
            $path = 'mail-staging/'.Str::uuid().'/'.Str::uuid().'_'.preg_replace('/[^\w.\-]+/', '_', $filename);

            Storage::disk(self::DISK)->put($path, (string) file_get_contents($file->getRealPath()));

            $staged[] = [
                'path' => $path,
                'filename' => $filename,
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
            ];
        }

        return $staged;
    }

    /**
     * @param  array<int, array{path: string, filename: string, mime: string}>  $staged
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    public function loadAsExtraAttachments(array $staged): array
    {
        $attachments = [];

        foreach ($staged as $item) {
            if (! Storage::disk(self::DISK)->exists($item['path'])) {
                continue;
            }

            $attachments[] = [
                'filename' => $item['filename'],
                'content' => Storage::disk(self::DISK)->get($item['path']),
                'mime' => $item['mime'] ?? 'application/octet-stream',
            ];
        }

        return $attachments;
    }

    /**
     * @param  array<int, array{path: string, filename: string, mime: string}>  $staged
     */
    public function cleanup(array $staged): void
    {
        $directories = [];

        foreach ($staged as $item) {
            $path = $item['path'] ?? '';
            if ($path === '') {
                continue;
            }

            if (Storage::disk(self::DISK)->exists($path)) {
                Storage::disk(self::DISK)->delete($path);
            }

            $directory = dirname($path);
            if ($directory !== '.' && $directory !== '') {
                $directories[$directory] = true;
            }
        }

        foreach ($directories as $directory) {
            Storage::disk(self::DISK)->deleteDirectory($directory);
        }
    }
}
