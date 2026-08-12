<?php

namespace App\Support;

class LogoHelper
{
    private static ?string $cachedBase64 = null;

    /**
     * Returns an <img> tag with the MarineCaddie logo as a base64-encoded data URI,
     * suitable for use inside DomPDF-rendered PDF templates.
     */
    public static function pdfImgTag(string $width = '180px'): string
    {
        return '<img src="' . self::base64DataUri() . '" style="width:' . $width . '; max-width:' . $width . ';" alt="MarineCaddie">';
    }

    /**
     * Returns the full base64 data URI (data:image/png;base64,...).
     */
    public static function base64DataUri(): string
    {
        if (self::$cachedBase64 === null) {
            $path = public_path('files/assets/images/marinecaddie-logo.png');

            if (! file_exists($path)) {
                return '';
            }

            self::$cachedBase64 = 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
        }

        return self::$cachedBase64;
    }

    /**
     * Returns the public URL for use in browser-rendered print views.
     */
    public static function publicUrl(): string
    {
        return asset('files/assets/images/marinecaddie-logo.png');
    }
}
