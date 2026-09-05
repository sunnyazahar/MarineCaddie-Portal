<?php

namespace App\Support;

final class MailBodyHtml
{
    private const BASE_FONT = 'Arial, Helvetica, sans-serif';

    private const BASE_SIZE = '13px';

    /** @var array<int, string> */
    private const FONT_SIZE_MAP = [
        1 => '10px',
        2 => '12px',
        3 => '13px',
        4 => '16px',
        5 => '18px',
        6 => '24px',
        7 => '32px',
    ];

    public static function fromComposeBody(string $body): string
    {
        $normalized = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? '';

        if (self::looksLikeEditorHtml($normalized)) {
            return self::wrap(self::sanitizeEditorHtml($normalized));
        }

        return self::wrap(self::fromHybridPlainText($normalized));
    }

    private static function wrap(string $html): string
    {
        return '<div style="font-size:' . self::BASE_SIZE . ';line-height:1.5;font-family:'
            . self::BASE_FONT . ';color:#111827;">'
            . $html
            . '</div>';
    }

    private static function looksLikeEditorHtml(string $body): bool
    {
        return (bool) preg_match('/<(div|p|br)\b/i', $body);
    }

    private static function fromHybridPlainText(string $normalized): string
    {
        $blocks = [];
        $tokenized = preg_replace_callback(
            '/<(table|font|span|mark)\b[^>]*>.*?<\/\1>/is',
            function (array $match) use (&$blocks): string {
                $blocks[] = self::sanitizeEditorHtml($match[0]);

                return "\x01RICH" . (count($blocks) - 1) . "\x02";
            },
            $normalized
        ) ?? $normalized;

        $html = '';
        $parts = preg_split("/(\x01RICH\d+\x02)/", $tokenized, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$tokenized];
        foreach ($parts as $part) {
            if (preg_match("/^\x01RICH(\d+)\x02$/", $part, $match)) {
                $html .= $blocks[(int) $match[1]];
                continue;
            }

            $escaped = nl2br(e($part), false);
            $html .= preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escaped) ?? $escaped;
        }

        return $html;
    }

    private static function sanitizeEditorHtml(string $html): string
    {
        $html = preg_replace_callback(
            '/<font\b([^>]*)>(.*?)<\/font>/is',
            static function (array $match): string {
                $attrs = $match[1];
                $inner = $match[2];
                $styles = [];

                if (preg_match('/\bstyle\s*=\s*"([^"]*)"/i', $attrs, $styleMatch)
                    || preg_match("/\bstyle\s*=\s*'([^']*)'/i", $attrs, $styleMatch)) {
                    $filtered = self::filterStyleDeclarations($styleMatch[1]);
                    if ($filtered !== '') {
                        $styles[] = $filtered;
                    }
                }

                if (preg_match('/\bcolor\s*=\s*"([^"]+)"/i', $attrs, $colorMatch)
                    || preg_match("/\bcolor\s*=\s*'([^']+)'/i", $attrs, $colorMatch)
                    || preg_match('/\bcolor\s*=\s*([^\s>]+)/i', $attrs, $colorMatch)) {
                    $color = self::normalizeCssColor(trim($colorMatch[1]));
                    if ($color !== null) {
                        $styles[] = 'color:' . $color;
                    }
                }

                if (preg_match('/\bsize\s*=\s*"([^"]+)"/i', $attrs, $sizeMatch)
                    || preg_match("/\bsize\s*=\s*'([^']+)'/i", $attrs, $sizeMatch)
                    || preg_match('/\bsize\s*=\s*([^\s>]+)/i', $attrs, $sizeMatch)) {
                    $size = self::normalizeFontSize(trim($sizeMatch[1]));
                    if ($size !== null) {
                        $styles[] = 'font-size:' . $size;
                    }
                }

                if (preg_match('/\bface\s*=\s*"([^"]+)"/i', $attrs, $faceMatch)
                    || preg_match("/\bface\s*=\s*'([^']+)'/i", $attrs, $faceMatch)
                    || preg_match('/\bface\s*=\s*([^\s>]+)/i', $attrs, $faceMatch)) {
                    $family = self::normalizeFontFamily(trim($faceMatch[1]));
                    if ($family !== null) {
                        $styles[] = 'font-family:' . $family;
                    }
                }

                $styles = array_values(array_filter($styles));
                if ($styles === []) {
                    return $inner;
                }

                return '<span style="' . e(implode(';', $styles)) . '">' . $inner . '</span>';
            },
            $html
        ) ?? $html;

        $clean = strip_tags(
            $html,
            '<div><p><br><span><font><mark><strong><b><em><i><u><table><thead><tbody><tfoot><tr><th><td>'
        );

        $clean = preg_replace_callback(
            '/<\s*([a-z0-9]+)(\s[^>]*)?>/i',
            static function (array $match): string {
                $tag = strtolower($match[1]);
                $attrs = $match[2] ?? '';

                if ($tag === 'br') {
                    return '<br>';
                }

                if (in_array($tag, ['span', 'mark'], true)) {
                    $safe = self::safeInlineAttributes($attrs, allowColorAttr: false);

                    return '<' . $tag . $safe . '>';
                }

                if ($tag === 'font') {
                    $safe = self::safeInlineAttributes($attrs, allowColorAttr: true);

                    return '<font' . $safe . '>';
                }

                if (in_array($tag, ['table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td'], true)) {
                    return '<' . $tag . self::safeTableAttributes($tag, $attrs) . '>';
                }

                return '<' . $tag . '>';
            },
            $clean
        ) ?? $clean;

        $clean = self::ensureEmailTableStyles($clean);

        // Contenteditable uses <div> lines; flatten for email clients (tables untouched).
        $clean = preg_replace_callback(
            '/<table\b[^>]*>.*?<\/table>/is',
            static function (array $match): string {
                return "\x01TABLE" . base64_encode($match[0]) . "\x02";
            },
            $clean
        ) ?? $clean;

        $clean = preg_replace('/<\/div>\s*<div\b[^>]*>/i', '<br>', $clean) ?? $clean;
        $clean = preg_replace('/<\/?div\b[^>]*>/i', '', $clean) ?? $clean;
        $clean = preg_replace('/<\/p>\s*<p\b[^>]*>/i', '<br>', $clean) ?? $clean;
        $clean = preg_replace('/<\/?p\b[^>]*>/i', '', $clean) ?? $clean;

        $clean = preg_replace_callback(
            "/\x01TABLE([A-Za-z0-9+\/=]+)\x02/",
            static function (array $match): string {
                $decoded = base64_decode($match[1], true);

                return $decoded === false ? '' : $decoded;
            },
            $clean
        ) ?? $clean;

        return $clean;
    }

    private static function ensureEmailTableStyles(string $html): string
    {
        return preg_replace_callback(
            '/<(table|th|td)(\s[^>]*)?>/i',
            static function (array $match): string {
                $tag = strtolower($match[1]);
                $attrs = $match[2] ?? '';
                $style = '';

                if (preg_match('/\bstyle\s*=\s*"([^"]*)"/i', $attrs, $styleMatch)
                    || preg_match("/\bstyle\s*=\s*'([^']*)'/i", $attrs, $styleMatch)) {
                    $style = $styleMatch[1];
                }

                $defaults = match ($tag) {
                    'table' => [
                        'width' => '100%',
                        'border-collapse' => 'collapse',
                        'font-size' => self::BASE_SIZE,
                        'font-family' => self::BASE_FONT,
                        'line-height' => '1.5',
                    ],
                    'th' => [
                        'border' => '0.5px solid #ccc',
                        'padding' => '5px 4px',
                        'text-align' => 'left',
                        'vertical-align' => 'top',
                        'background-color' => '#f3f4f6',
                        'font-weight' => 'bold',
                        'font-size' => self::BASE_SIZE,
                        'font-family' => self::BASE_FONT,
                        'line-height' => '1.5',
                    ],
                    default => [
                        'border' => '0.5px solid #ccc',
                        'padding' => '5px 4px',
                        'text-align' => 'left',
                        'vertical-align' => 'top',
                        'font-size' => self::BASE_SIZE,
                        'font-family' => self::BASE_FONT,
                        'line-height' => '1.5',
                    ],
                };

                $merged = self::mergeStyleDefaults($style, $defaults);
                $attrsWithoutStyle = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $attrs) ?? $attrs;

                return '<' . $tag . $attrsWithoutStyle . ' style="' . e($merged) . '">';
            },
            $html
        ) ?? $html;
    }

    /**
     * @param  array<string, string>  $defaults
     */
    private static function mergeStyleDefaults(string $existing, array $defaults): string
    {
        $map = [];
        foreach (explode(';', $existing) as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === '' || ! str_contains($declaration, ':')) {
                continue;
            }
            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            if ($property === '' || $value === '' || strtolower($value) === 'inherit') {
                continue;
            }
            $map[$property] = $value;
        }

        foreach ($defaults as $property => $value) {
            if (! array_key_exists($property, $map)) {
                $map[$property] = $value;
            }
        }

        // Force one consistent typeface in mail tables (avoid client inherit quirks).
        $map['font-family'] = self::BASE_FONT;
        if (($map['font-size'] ?? '') === '' || strtolower((string) $map['font-size']) === 'inherit') {
            $map['font-size'] = self::BASE_SIZE;
        }

        $parts = [];
        foreach ($map as $property => $value) {
            $parts[] = $property . ':' . $value;
        }

        return implode(';', $parts);
    }

    private static function safeTableAttributes(string $tag, string $attrs): string
    {
        $out = '';

        if (in_array($tag, ['td', 'th'], true)) {
            foreach (['colspan', 'rowspan'] as $name) {
                if (preg_match('/\b' . $name . '\s*=\s*"(\d+)"/i', $attrs, $match)
                    || preg_match('/\b' . $name . '\s*=\s*\'(\d+)\'/i', $attrs, $match)
                    || preg_match('/\b' . $name . '\s*=\s*(\d+)/i', $attrs, $match)) {
                    $out .= ' ' . $name . '="' . $match[1] . '"';
                }
            }
        }

        if (preg_match('/\bstyle\s*=\s*"([^"]*)"/i', $attrs, $match)
            || preg_match("/\bstyle\s*=\s*'([^']*)'/i", $attrs, $match)) {
            $style = self::filterStyleDeclarations($match[1], tableContext: true);
            if ($style !== '') {
                $out .= ' style="' . e($style) . '"';
            }
        }

        return $out;
    }

    private static function safeInlineAttributes(string $attrs, bool $allowColorAttr): string
    {
        $out = '';

        if ($allowColorAttr && (
            preg_match('/\bcolor\s*=\s*"([^"]+)"/i', $attrs, $match)
            || preg_match("/\bcolor\s*=\s*'([^']+)'/i", $attrs, $match)
            || preg_match('/\bcolor\s*=\s*([^\s>]+)/i', $attrs, $match)
        )) {
            $color = self::normalizeCssColor(trim($match[1]));
            if ($color !== null) {
                $out .= ' color="' . e($color) . '"';
            }
        }

        if (preg_match('/\bstyle\s*=\s*"([^"]*)"/i', $attrs, $match)
            || preg_match("/\bstyle\s*=\s*'([^']*)'/i", $attrs, $match)) {
            $style = self::filterStyleDeclarations($match[1]);
            if ($style !== '') {
                $out .= ' style="' . e($style) . '"';
            }
        }

        return $out;
    }

    private static function filterStyleDeclarations(string $style, bool $tableContext = false): string
    {
        $allowed = [
            'color',
            'background',
            'background-color',
            'font-size',
            'font-family',
            'font-weight',
            'line-height',
            'text-align',
            'vertical-align',
        ];

        if ($tableContext) {
            $allowed = array_merge($allowed, [
                'border',
                'border-collapse',
                'border-color',
                'border-width',
                'border-style',
                'padding',
                'width',
                'min-width',
                'max-width',
            ]);
        }

        $kept = [];

        foreach (explode(';', $style) as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === '' || ! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            if (! in_array($property, $allowed, true)) {
                continue;
            }

            if (in_array($property, ['color', 'background', 'background-color', 'border-color'], true)) {
                if ($property === 'border-color') {
                    $color = self::normalizeCssColor($value);
                    if ($color === null) {
                        continue;
                    }
                    $kept[] = 'border-color:' . $color;
                    continue;
                }

                $color = self::normalizeCssColor($value);
                if ($color === null) {
                    continue;
                }
                $kept[] = ($property === 'background' ? 'background-color' : $property) . ':' . $color;
                continue;
            }

            if ($property === 'font-size') {
                $size = self::normalizeFontSize($value);
                if ($size === null) {
                    continue;
                }
                $kept[] = 'font-size:' . $size;
                continue;
            }

            if ($property === 'font-family') {
                $family = self::normalizeFontFamily($value);
                if ($family === null) {
                    continue;
                }
                $kept[] = 'font-family:' . $family;
                continue;
            }

            if ($property === 'font-weight') {
                if (preg_match('/^(normal|bold|bolder|lighter|[1-9]00)$/i', $value)) {
                    $kept[] = 'font-weight:' . strtolower($value);
                }
                continue;
            }

            if ($property === 'line-height') {
                if (preg_match('/^(\d+(\.\d+)?(px|em|%)?)$/i', $value)) {
                    $kept[] = 'line-height:' . $value;
                }
                continue;
            }

            if (in_array($property, ['text-align', 'vertical-align'], true)) {
                if (preg_match('/^(left|right|center|justify|top|middle|bottom|baseline)$/i', $value)) {
                    $kept[] = $property . ':' . strtolower($value);
                }
                continue;
            }

            if ($property === 'border-collapse') {
                if (preg_match('/^(collapse|separate)$/i', $value)) {
                    $kept[] = 'border-collapse:' . strtolower($value);
                }
                continue;
            }

            if ($property === 'border' || str_starts_with($property, 'border-')) {
                if (preg_match('/^[0-9a-zA-Z#%\s\.\/\-]+$/', $value)) {
                    $kept[] = $property . ':' . $value;
                }
                continue;
            }

            if ($property === 'padding') {
                if (preg_match('/^[0-9a-zA-Z%\s\.]+$/', $value)) {
                    $kept[] = 'padding:' . $value;
                }
                continue;
            }

            if (in_array($property, ['width', 'min-width', 'max-width'], true)) {
                if (preg_match('/^(\d+(\.\d+)?(px|%)?|auto)$/i', $value)) {
                    $kept[] = $property . ':' . $value;
                }
            }
        }

        return implode(';', $kept);
    }

    private static function normalizeFontSize(string $value): ?string
    {
        $value = trim($value);
        $value = rtrim($value, ';');

        if (preg_match('/^[1-7]$/', $value)) {
            return self::FONT_SIZE_MAP[(int) $value];
        }

        if (preg_match('/^\d+(\.\d+)?(px|pt|em|rem|%)$/i', $value)) {
            return strtolower($value);
        }

        return null;
    }

    private static function normalizeFontFamily(string $value): ?string
    {
        $value = trim($value);
        $value = trim($value, '"\'');
        if ($value === '' || strtolower($value) === 'inherit') {
            return self::BASE_FONT;
        }

        if (! preg_match("/^[a-zA-Z0-9,\s\-_'\"]+$/", $value)) {
            return null;
        }

        return $value;
    }

    private static function normalizeCssColor(string $value): ?string
    {
        $value = trim($value);
        $value = rtrim($value, ';');

        if (preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?([0-9a-fA-F]{2})?$/', $value)) {
            return strtolower($value);
        }

        if (preg_match('/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', $value, $match)) {
            $r = min(255, max(0, (int) $match[1]));
            $g = min(255, max(0, (int) $match[2]));
            $b = min(255, max(0, (int) $match[3]));

            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }

        if (preg_match('/^[a-z]{3,20}$/i', $value)) {
            return strtolower($value);
        }

        return null;
    }
}
