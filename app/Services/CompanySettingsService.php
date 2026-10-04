<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use App\Support\Branding;
use App\Support\PrivateDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CompanySettingsService
{
    /** SVG is excluded on purpose: it can carry script and is embedded inline in pages and PDFs. */
    public const LOGO_RULES = ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'mimetypes:image/png,image/jpeg,image/webp', 'max:2048'];

    private const NON_DELIVERING_MAILERS = ['log', 'array'];

    public function __construct(private CompanySettingsRepositoryInterface $settings)
    {
    }

    /**
     * @param  array<string, mixed>  $attributes  validated settings fields
     */
    public function update(array $attributes, ?UploadedFile $logo = null, ?int $userId = null): CompanySetting
    {
        if ($logo !== null) {
            $attributes['logo_path'] = $this->storeLogo($logo);
        }

        if (array_key_exists('invoice_notes', $attributes)) {
            $attributes['invoice_notes'] = $this->normaliseNotes($attributes['invoice_notes']);
        }

        $attributes['updated_by'] = $userId;

        return $this->settings->save($attributes);
    }

    /** Stores the logo on the private disk under branding/ and returns its relative path. */
    public function storeLogo(UploadedFile $logo): string
    {
        $extension = strtolower($logo->guessExtension() ?: $logo->getClientOriginalExtension());
        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            throw new RuntimeException('Logo must be a PNG, JPG or WEBP image.');
        }

        $path = $logo->storeAs(
            Branding::LOGO_DIRECTORY,
            'logo-' . Str::lower(Str::random(16)) . '.' . $extension,
            PrivateDisk::NAME
        );

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Logo could not be saved.');
        }

        return $path;
    }

    /**
     * Sends a plain test mail with the current mail configuration.
     *
     * @throws RuntimeException with a readable reason when the mail cannot be delivered
     */
    public function sendTestEmail(string $to): void
    {
        $mailer = (string) config('mail.default');

        if (in_array($mailer, self::NON_DELIVERING_MAILERS, true)) {
            throw new RuntimeException("MAIL_MAILER={$mailer} only writes to the log and does not send email. Configure SMTP in .env first.");
        }

        try {
            Mail::raw(
                'This is a test email from ' . Branding::name() . '. Email delivery works, so login verification codes (OTP) can be enabled.',
                fn ($message) => $message->to($to)->subject(Branding::name() . ' test email')
            );
        } catch (Throwable $e) {
            throw new RuntimeException('Mail send failed (' . $mailer . '): ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return list<string>
     */
    private function normaliseNotes(mixed $notes): array
    {
        $lines = is_array($notes) ? $notes : preg_split('/\R/u', (string) $notes);

        return array_values(array_filter(
            array_map(fn ($line) => Str::limit(trim((string) $line), 500, ''), $lines ?: []),
            fn (string $line) => $line !== ''
        ));
    }
}
