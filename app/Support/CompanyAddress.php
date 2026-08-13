<?php

namespace App\Support;

class CompanyAddress
{
    public const NAME = 'MarineCaddie Shipping LLC';

    public const LINE_1 = 'Unit No. 204 – 224, Al Safi Building, Tower 1';

    public const LINE_2 = 'Deira, Dubai, United Arab Emirates';

    public const PHONE = '+971 50 5643375';

    public const EMAIL = 'ops@marinecaddie.com';

    /**
     * @return array<int, string>
     */
    public static function footerLeftLines(): array
    {
        return [
            self::NAME,
            self::LINE_1,
            self::LINE_2,
        ];
    }

    public static function footerContactLine(): string
    {
        return 'Phone ' . self::PHONE . ', Email ' . self::EMAIL;
    }

    public static function htmlBlock(): string
    {
        return implode('<br>', [
            e(self::NAME),
            e(self::LINE_1 . ', ' . self::LINE_2),
            e(self::footerContactLine()),
        ]);
    }
}
