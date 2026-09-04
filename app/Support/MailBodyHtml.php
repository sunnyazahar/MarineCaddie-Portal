<?php

namespace App\Support;

final class MailBodyHtml
{
    public static function fromComposeBody(string $body): string
    {
        $normalized = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? '';
        $blocks = [];
        $tokenized = preg_replace_callback(
            '/<(table|font|span|mark)\b[^>]*>.*?<\/\1>/is',
            function (array $match) use (&$blocks): string {
                $blocks[] = self::sanitize($match[0]);

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

        return '<div style="font-size:13px;line-height:1.5;font-family:inherit;">' . $html . '</div>';
    }

    private static function sanitize(string $html): string
    {
        return strip_tags(
            $html,
            '<table><thead><tbody><tfoot><tr><th><td><strong><b><em><i><u><br><span><font><mark>'
        );
    }
}
