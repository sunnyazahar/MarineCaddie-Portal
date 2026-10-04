<?php

namespace App\Support;

use App\Models\CompanySetting;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;

/**
 * Company branding for screens, mails and PDFs. Values come from the single
 * company_settings row (Admin > Company Settings); before install every
 * accessor returns a neutral default so guest pages still render.
 */
class Branding
{
    public const DEFAULT_NAME = 'Portal';

    public const LOGO_DIRECTORY = 'branding';

    /** @var array<string, string> keyed by path|mtime so a replaced logo is re-read */
    private static array $logoDataUris = [];

    public static function settings(): ?CompanySetting
    {
        return app(CompanySettingsRepositoryInterface::class)->current();
    }

    public static function name(): string
    {
        $name = trim((string) self::settings()?->company_name);

        if ($name !== '') {
            return $name;
        }

        $appName = trim((string) config('app.name'));

        return $appName !== '' && strcasecmp($appName, 'Laravel') !== 0 ? $appName : self::DEFAULT_NAME;
    }

    /** Registered company name for invoices / footers (falls back to the display name). */
    public static function legalName(): string
    {
        $legal = trim((string) self::settings()?->legal_name);

        return $legal !== '' ? $legal : self::name();
    }

    public static function website(): ?string
    {
        return self::nullable(self::settings()?->website);
    }

    public static function phone(): ?string
    {
        return self::nullable(self::settings()?->phone);
    }

    public static function email(): ?string
    {
        return self::nullable(self::settings()?->email);
    }

    /** Fallback sender address for outgoing mails when no user mailbox applies. */
    public static function mailFromAddress(): ?string
    {
        return self::nullable(config('mail.from.address')) ?? self::email();
    }

    /**
     * @return list<string>
     */
    public static function addressLines(): array
    {
        $settings = self::settings();

        return array_values(array_filter([
            self::nullable($settings?->address_line_1),
            self::nullable($settings?->address_line_2),
        ]));
    }

    public static function otpEnabled(): bool
    {
        $settings = self::settings();

        // No settings row (legacy install not yet marked) keeps the stricter OTP behaviour.
        return $settings === null ? true : (bool) $settings->otp_enabled;
    }

    /**
     * @return list<string>
     */
    public static function invoiceNotes(): array
    {
        $notes = self::settings()?->invoice_notes;

        if (! is_array($notes)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($note) => trim((string) $note),
            $notes
        ), fn (string $note) => $note !== ''));
    }

    /**
     * @return array{account_name: string, account_number: string, iban: string, swift_code: string, bank_name: string, city_country: string}
     */
    public static function bankDetails(): array
    {
        $settings = self::settings();

        return [
            'account_name' => (string) ($settings?->bank_account_name ?? ''),
            'account_number' => (string) ($settings?->bank_account_number ?? ''),
            'iban' => (string) ($settings?->bank_iban ?? ''),
            'swift_code' => (string) ($settings?->bank_swift ?? ''),
            'bank_name' => (string) ($settings?->bank_name ?? ''),
            'city_country' => (string) ($settings?->bank_city_country ?? ''),
        ];
    }

    /** Absolute path of the uploaded logo, or null when none / unreadable. */
    public static function logoPath(): ?string
    {
        $relative = trim((string) self::settings()?->logo_path);

        if ($relative === '' || ! str_starts_with($relative, self::LOGO_DIRECTORY . '/')) {
            return null;
        }

        $path = PrivateDisk::path($relative);

        return is_file($path) && is_readable($path) ? $path : null;
    }

    /** Public logo / favicon URL; the version changes whenever a new logo is uploaded. */
    public static function logoUrl(): string
    {
        $version = substr(md5((string) self::settings()?->logo_path), 0, 8);

        return route('branding.logo', ['v' => $version]);
    }

    public static function logoMime(): ?string
    {
        $path = self::logoPath();

        if ($path === null) {
            return null;
        }

        $mime = (string) (mime_content_type($path) ?: '');

        return in_array($mime, ['image/png', 'image/jpeg', 'image/webp'], true) ? $mime : null;
    }

    /** data:image/...;base64 URI for PDFs, mails and inline HTML ('' when no logo). */
    public static function logoDataUri(): string
    {
        $path = self::logoPath();
        $mime = self::logoMime();

        if ($path === null || $mime === null) {
            return '';
        }

        $key = $path . '|' . (int) filemtime($path);

        return self::$logoDataUris[$key] ??= 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    /** Proforma / invoice number prefix (Settings); defaults to the company initials. */
    public static function proformaPrefix(): string
    {
        return self::nullable(self::settings()?->proforma_prefix) ?? self::initials() . '-';
    }

    /** Short initials for compact UI badges ("Acme Shipping" → "AS", "BlueOcean" → "BO"). */
    public static function initials(): string
    {
        $words = preg_split('/\s+/u', self::name(), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($words) === 1) {
            $words = preg_split('/(?<=\p{Ll})(?=\p{Lu})/u', $words[0], -1, PREG_SPLIT_NO_EMPTY) ?: $words;
        }
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials !== '' ? $initials : 'P';
    }

    private static function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
