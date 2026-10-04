<?php

namespace App\Services\Master;

/** Small JSON list of folders created by this master (no secrets stored). */
final class SiteRegistry
{
    public function __construct(private ?string $path = null)
    {
    }

    /**
     * @return list<array{company: string, folder: string, url: string, database: string, created_at: string}>
     */
    public function all(): array
    {
        $path = $this->path();
        if (! is_file($path)) {
            return [];
        }

        $sites = json_decode((string) file_get_contents($path), true);

        return is_array($sites) ? array_values(array_filter($sites, 'is_array')) : [];
    }

    /**
     * @param  array{company: string, folder: string, url: string, database: string}  $site
     */
    public function add(array $site): void
    {
        $path = $this->path();
        if (! is_dir(dirname($path))) {
            @mkdir(dirname($path), 0775, true);
        }

        $handle = @fopen($path, 'c+');
        if ($handle === false) {
            return;
        }

        try {
            flock($handle, LOCK_EX);
            $current = json_decode((string) stream_get_contents($handle), true);
            $sites = is_array($current) ? $current : [];
            array_unshift($sites, $site + ['created_at' => now()->toIso8601String()]);

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) json_encode($sites, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function path(): string
    {
        return $this->path ?? storage_path('app/master/sites.json');
    }
}
