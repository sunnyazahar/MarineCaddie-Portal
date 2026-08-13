<?php

namespace App\Support;

class LogoHelper
{
    /** @var array<string, string> */
    private static array $cachedBase64 = [];

    /**
     * Returns an <img> tag with the MarineCaddie logo embedded as base64.
     * Works in DomPDF, browser HTML, and email clients that allow data URIs.
     */
    public static function imgTag(string $width = '180px', string $extraStyle = '', ?string $filename = null): string
    {
        $uri = self::base64DataUri($filename);

        if ($uri === '') {
            return '';
        }

        $style = 'width:' . $width . '; max-width:' . $width . '; height:auto;';
        if ($extraStyle !== '') {
            $style .= ' ' . trim($extraStyle);
        }

        return '<img src="' . $uri . '" alt="MarineCaddie" class="marinecaddie-logo" style="' . $style . '">';
    }

    /**
     * Compact header logo (no tagline).
     */
    public static function headerImgTag(string $width = '160px', string $extraStyle = ''): string
    {
        $tag = self::imgTag($width, $extraStyle, 'marinecaddie-header-logo.png');

        return $tag !== '' ? $tag : self::imgTag($width, $extraStyle);
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
    public static function base64DataUri(?string $filename = null): string
    {
        $filename = $filename ?: 'marinecaddie-logo.png';

        if (! isset(self::$cachedBase64[$filename])) {
            $path = public_path('files/assets/images/' . $filename);

            if (! file_exists($path)) {
                self::$cachedBase64[$filename] = '';
            } else {
                self::$cachedBase64[$filename] = 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
            }
        }

        return self::$cachedBase64[$filename];
    }
}
