<?php

namespace App\Support;

class LogoHelper
{
    /**
     * Returns an <img> tag with the company logo embedded as base64 (works in
     * DomPDF, browser HTML and mail clients that allow data URIs). Without an
     * uploaded logo the company name is rendered as text instead.
     */
    public static function imgTag(string $width = '180px', string $extraStyle = ''): string
    {
        $uri = self::base64DataUri();
        $style = 'width:' . $width . '; max-width:' . $width . '; height:auto;';
        if ($extraStyle !== '') {
            $style .= ' ' . trim($extraStyle);
        }

        if ($uri === '') {
            return self::textLogo($width);
        }

        return '<img src="' . $uri . '" alt="' . e(Branding::name()) . '" class="app-logo" style="' . e($style) . '">';
    }

    /**
     * Logo for the top navigation bar.
     */
    public static function headerImgTag(string $width = '160px', string $extraStyle = ''): string
    {
        return self::imgTag($width, $extraStyle);
    }

    /**
     * Alias for imgTag — used in PDF templates.
     */
    public static function pdfImgTag(string $width = '180px'): string
    {
        return self::imgTag($width);
    }

    /**
     * Returns the full base64 data URI ('' when no logo is uploaded).
     */
    public static function base64DataUri(): string
    {
        return Branding::logoDataUri();
    }

    private static function textLogo(string $width): string
    {
        return '<span class="app-logo app-logo-text" style="display:inline-block; max-width:' . e($width)
            . '; font-weight:700; font-size:20px; line-height:1.2; color:#1f3b57; word-break:break-word;">'
            . e(Branding::name()) . '</span>';
    }
}
