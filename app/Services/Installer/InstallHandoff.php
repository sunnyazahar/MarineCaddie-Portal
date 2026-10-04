<?php

namespace App\Services\Installer;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\UploadedFile;
use JsonException;

/**
 * One-time install data the master setup drops into a freshly copied project.
 * The file is AES-256-GCM encrypted with a key derived from a random token that
 * only travels in the browser POST to /install/handoff, so the file alone is
 * useless; it expires after TTL_MINUTES and is removed after a successful install.
 */
class InstallHandoff
{
    public const FILE = 'storage/app/install-handoff.json';

    public const TTL_MINUTES = 30;

    private const LOGO_BASENAME = 'storage/app/install-handoff-logo';

    private const LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp'];

    private const CIPHER = 'aes-256-gcm';

    private const INVALID = 'This setup link is invalid, expired or was already used. Fill in the setup form below instead.';

    /**
     * @param  array{company_name: string, app_url: string, db: array<string, string|int|null>, admin: array{name: string, email: string, password: string}}  $data
     * @return string token for the browser handoff
     *
     * @throws InstallerException
     */
    public function create(string $projectPath, array $data, ?UploadedFile $logo = null): string
    {
        $token = bin2hex(random_bytes(32));

        $data['logo'] = $logo !== null ? $this->stashLogo($projectPath, $logo) : null;
        $data['expires_at'] = now()->addMinutes(self::TTL_MINUTES)->getTimestamp();

        $payload = $this->encrypter($token)->encryptString(json_encode($data, JSON_THROW_ON_ERROR));
        $path = $projectPath . DIRECTORY_SEPARATOR . self::FILE;

        if (@file_put_contents($path, $payload, LOCK_EX) === false) {
            throw new InstallerException('Could not write the install handoff file in the new folder.');
        }
        @chmod($path, 0600);

        return $token;
    }

    /**
     * @return array{company_name: string, app_url: string, db: array<string, string|int|null>, admin: array{name: string, email: string, password: string}, logo: array{extension: string, name: string, mime: string}|null}
     *
     * @throws InstallerException
     */
    public function read(string $token, ?string $projectPath = null): array
    {
        $path = $projectPath !== null ? $projectPath . DIRECTORY_SEPARATOR . self::FILE : base_path(self::FILE);

        if (preg_match('/^[a-f0-9]{64}$/', $token) !== 1 || ! is_file($path)) {
            throw new InstallerException(self::INVALID);
        }

        try {
            $data = json_decode(
                $this->encrypter($token)->decryptString((string) file_get_contents($path)),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (DecryptException|JsonException) {
            throw new InstallerException(self::INVALID);
        }

        if (! is_array($data) || (int) ($data['expires_at'] ?? 0) < now()->getTimestamp()) {
            throw new InstallerException(self::INVALID);
        }

        return $data;
    }

    /** @param  array{logo?: array{extension: string, name: string, mime: string}|null}  $data */
    public function logoFile(array $data): ?UploadedFile
    {
        $logo = $data['logo'] ?? null;
        if (! is_array($logo) || ! in_array($logo['extension'] ?? '', self::LOGO_EXTENSIONS, true)) {
            return null;
        }

        $path = base_path(self::LOGO_BASENAME . '.' . $logo['extension']);

        return is_file($path)
            ? new UploadedFile($path, basename((string) $logo['name']), (string) $logo['mime'], null, true)
            : null;
    }

    /** Removes the handoff file and stashed logo of this project. */
    public function forget(): void
    {
        @unlink(base_path(self::FILE));

        foreach (self::LOGO_EXTENSIONS as $extension) {
            @unlink(base_path(self::LOGO_BASENAME . '.' . $extension));
        }
    }

    /**
     * @return array{extension: string, name: string, mime: string}
     *
     * @throws InstallerException
     */
    private function stashLogo(string $projectPath, UploadedFile $logo): array
    {
        $extension = strtolower((string) ($logo->guessExtension() ?: $logo->getClientOriginalExtension()));
        if (! in_array($extension, self::LOGO_EXTENSIONS, true)) {
            throw new InstallerException('Logo must be a PNG, JPG or WEBP image.');
        }

        $target = $projectPath . DIRECTORY_SEPARATOR . self::LOGO_BASENAME . '.' . $extension;
        if (! @copy($logo->getRealPath(), $target)) {
            throw new InstallerException('Could not copy the logo into the new folder.');
        }
        @chmod($target, 0600);

        return [
            'extension' => $extension,
            'name' => 'logo.' . $extension,
            'mime' => (string) $logo->getMimeType(),
        ];
    }

    private function encrypter(string $token): Encrypter
    {
        return new Encrypter(hash('sha256', $token, true), self::CIPHER);
    }
}
