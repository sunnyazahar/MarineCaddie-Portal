<?php

namespace App\Support;

class Code39Barcode
{
    private const PATTERNS = [
        '0' => 'nnnwwnwnn',
        '1' => 'wnnwnnnnw',
        '2' => 'nnwwnnnnw',
        '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw',
        '5' => 'wnnwwnnnn',
        '6' => 'nnwwwnnnn',
        '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn',
        '9' => 'nnwwnnwnn',
        'A' => 'wnnnnwnnw',
        'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn',
        'D' => 'nnnnwwnnw',
        'E' => 'wnnnwwnnn',
        'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw',
        'H' => 'wnnnnwwnn',
        'I' => 'nnwnnwwnn',
        'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww',
        'L' => 'nnwnnnnww',
        'M' => 'wnwnnnnwn',
        'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn',
        'P' => 'nnwnwnnwn',
        'Q' => 'nnnnnnwww',
        'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn',
        'T' => 'nnnnwnwwn',
        'U' => 'wwnnnnnnw',
        'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn',
        'X' => 'nwnnwnnnw',
        'Y' => 'wwnnwnnnn',
        'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw',
        '.' => 'wwnnnnwnn',
        ' ' => 'nwwnnnwnn',
        '*' => 'nwnnwnwnn',
        '$' => 'nwnwnwnnn',
        '/' => 'nwnwnnnwn',
        '+' => 'nwnnnwnwn',
        '%' => 'nnnwnwnwn',
    ];

    /**
     * Returns an HTML barcode (table-based) that DomPDF can render.
     */
    public static function html(string $value, int $barHeight = 48, float $narrow = 1.2, float $wide = 3.0): string
    {
        $normalized = strtoupper(preg_replace('/[^A-Za-z0-9\-\. \$\/\+%]/', '', $value) ?? '');
        if ($normalized === '') {
            return '';
        }

        $encoded = '*' . $normalized . '*';
        $bars = '';

        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = $encoded[$i];
            $pattern = self::PATTERNS[$char] ?? null;
            if ($pattern === null) {
                continue;
            }

            for ($j = 0; $j < strlen($pattern); $j++) {
                $width = $pattern[$j] === 'w' ? $wide : $narrow;
                $isBar = $j % 2 === 0;
                $color = $isBar ? '#000' : '#fff';
                $bars .= '<td style="width:' . $width . 'px; background:' . $color . '; padding:0; border:0;"></td>';
            }

            // inter-character gap
            $bars .= '<td style="width:' . $narrow . 'px; background:#fff; padding:0; border:0;"></td>';
        }

        $spaced = implode(' ', str_split($normalized));

        return '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 0 4px auto;">'
            . '<tr style="height:' . $barHeight . 'px;">' . $bars . '</tr>'
            . '</table>'
            . '<div style="text-align:right; font-size:10px; letter-spacing:1px;">* ' . e($spaced) . ' *</div>';
    }
}
