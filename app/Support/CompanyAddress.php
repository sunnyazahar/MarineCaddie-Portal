<?php

namespace App\Support;

/**
 * Company address / contact block for PDFs and print views, read from
 * Admin > Company Settings via {@see Branding}.
 */
class CompanyAddress
{
    public static function name(): string
    {
        return Branding::legalName();
    }

    public static function phone(): string
    {
        return (string) Branding::phone();
    }

    public static function email(): string
    {
        return (string) Branding::email();
    }

    /**
     * @return array<int, string>
     */
    public static function addressLines(): array
    {
        return Branding::addressLines();
    }

    /**
     * @return array<int, string>
     */
    public static function footerLeftLines(): array
    {
        return [
            self::name(),
            ...self::addressLines(),
        ];
    }

    public static function footerContactLine(): string
    {
        $parts = [];

        if (self::phone() !== '') {
            $parts[] = 'Phone ' . self::phone();
        }
        if (self::email() !== '') {
            $parts[] = 'Email ' . self::email();
        }

        return implode(', ', $parts);
    }

    public static function htmlBlock(): string
    {
        return implode('<br>', array_map('e', array_values(array_filter([
            self::name(),
            implode(', ', self::addressLines()),
            self::footerContactLine(),
        ]))));
    }

    public static function htmlBlockAddress(): string
    {
        return implode('<br>', array_map('e', array_values(array_filter([
            self::name(),
            self::footerContactLine(),
        ]))));
    }
}
