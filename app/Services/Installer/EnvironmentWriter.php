<?php

namespace App\Services\Installer;

use Illuminate\Encryption\Encrypter;

/**
 * Creates / updates the .env file. Values are validated and quoted so input
 * cannot inject extra keys (newlines) or variable interpolation (${...}).
 */
class EnvironmentWriter
{
    public function __construct(private ?string $path = null)
    {
    }

    public function path(): string
    {
        return $this->path ?? base_path('.env');
    }

    /** Copies .env.example when there is no .env yet. */
    public function ensureExists(): void
    {
        if (is_file($this->path())) {
            return;
        }

        $example = dirname($this->path()) . DIRECTORY_SEPARATOR . '.env.example';
        $contents = is_file($example) ? (string) file_get_contents($example) : '';

        $this->writeAtomically($contents);
    }

    /** Generates APP_KEY when it is empty; returns the key in use. */
    public function ensureAppKey(): string
    {
        $this->ensureExists();

        $current = $this->get('APP_KEY');
        if ($current !== null && $current !== '') {
            return $current;
        }

        $key = 'base64:' . base64_encode(Encrypter::generateKey((string) config('app.cipher', 'AES-256-CBC')));
        $this->set(['APP_KEY' => $key]);

        return $key;
    }

    public function get(string $key): ?string
    {
        if (! is_file($this->path())) {
            return null;
        }

        $contents = (string) file_get_contents($this->path());
        if (! preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $contents, $match)) {
            return null;
        }

        $value = trim($match[1]);
        if (strlen($value) >= 2 && $value[0] === '"' && str_ends_with($value, '"')) {
            $value = stripcslashes(substr($value, 1, -1));
        }

        return $value;
    }

    /**
     * @param  array<string, string|int|bool|null>  $values
     *
     * @throws InstallerException
     */
    public function set(array $values): void
    {
        $this->ensureExists();
        $contents = (string) file_get_contents($this->path());

        foreach ($values as $key => $value) {
            if (! preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) {
                throw new InstallerException("Invalid environment key: {$key}");
            }

            $line = $key . '=' . $this->formatValue($value);
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            $commentedPattern = '/^#\s*' . preg_quote($key, '/') . '=.*$/m';

            if (preg_match($pattern, $contents)) {
                $contents = (string) preg_replace_callback($pattern, fn () => $line, $contents, 1);
            } elseif (preg_match($commentedPattern, $contents)) {
                $contents = (string) preg_replace_callback($commentedPattern, fn () => $line, $contents, 1);
            } else {
                $contents = ltrim(rtrim($contents, "\r\n") . "\n" . $line . "\n", "\n");
            }
        }

        $this->writeAtomically($contents);
    }

    /** @throws InstallerException */
    public function formatValue(string|int|bool|null $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        $value = (string) $value;
        if (preg_match('/[\r\n\x00]/', $value)) {
            throw new InstallerException('Configuration values cannot contain line breaks.');
        }

        if ($value === '' || preg_match('/^[A-Za-z0-9_.:\/@+\-]+$/', $value)) {
            return $value;
        }

        return '"' . str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value) . '"';
    }

    /**
     * Temp file + rename when the folder is writable. Otherwise (folder locked down,
     * only .env granted to the web user) fall back to an in-place locked write.
     *
     * @throws InstallerException
     */
    private function writeAtomically(string $contents): void
    {
        $target = $this->path();

        if (! is_writable(dirname($target))) {
            if (is_file($target) && is_writable($target) && @file_put_contents($target, $contents, LOCK_EX) !== false) {
                return;
            }

            throw new InstallerException('Could not write the .env file. Make the project folder (or the .env file) writable.');
        }

        // Keep the current mode: the web server may be a different user than the CLI
        // (e.g. XAMPP's daemon) and must still be able to read .env after an artisan write.
        $mode = is_file($target) ? (fileperms($target) & 0777) : 0640;
        $temp = $target . '.tmp-' . bin2hex(random_bytes(4));

        if (@file_put_contents($temp, $contents, LOCK_EX) === false) {
            throw new InstallerException('Could not write the .env file. Make the project folder writable.');
        }

        @chmod($temp, $mode);

        if (! @rename($temp, $target)) {
            @unlink($temp);
            throw new InstallerException('Could not replace the .env file. Make it writable.');
        }
    }
}
