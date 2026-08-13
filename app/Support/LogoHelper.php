<?php

namespace App\Support;

class LogoHelper
{
    private static ?string $cachedBase64 = null;

    /**
     * Returns an <img> tag with the MarineCaddie logo embedded as base64.
     * Works in DomPDF, browser HTML, and email clients that allow data URIs.
     */
    public static function imgTag(string $width = '180px', string $extraStyle = ''): string
    {
        $uri = self::base64DataUri();

        if ($uri === '') {
            return '';
        }

        $style = 'width:' . $width . '; max-width:' . $width . '; height:auto;';
        if ($extraStyle !== '') {
            $style .= ' ' . trim($extraStyle);
        }

        return '<img src="' . $uri . '" alt="MarineCaddie" style="' . $style . '">';
    }

    /**
     * Alias for imgTag — used in PDF templates.
     */
    public static function pdfImgTag(string $width = '180px'): string
    {
        return self::imgTag($width);
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
}
