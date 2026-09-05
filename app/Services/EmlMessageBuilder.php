<?php

namespace App\Services;

use App\Support\MailBodyHtml;

class EmlMessageBuilder
{
    /**
     * @param  array<int, array{filename: string, content: string, mime?: string}>  $attachments
     */
    public function build(
        string $fromName,
        string $fromEmail,
        array $to,
        array $cc,
        string $subject,
        string $body,
        array $attachments = []
    ): string {
        $fromEmail = trim($fromEmail) ?: 'noreply@example.com';
        $fromName = trim($fromName) ?: $fromEmail;
        $htmlBody = MailBodyHtml::fromComposeBody($body);
        $htmlBody = str_replace(["\r\n", "\r"], "\n", $htmlBody);
        $htmlBody = str_replace("\n", "\r\n", $htmlBody);

        $mixedBoundary = '=_Mixed_' . bin2hex(random_bytes(8));
        $headers = [
            'From: ' . $this->formatNamedAddress($fromName, $fromEmail),
            'MIME-Version: 1.0',
            'X-Unsent: 1',
        ];

        $toLine = $this->formatAddressList($to);
        if ($toLine !== '') {
            $headers[] = 'To: ' . $toLine;
        }

        $ccLine = $this->formatAddressList($cc);
        if ($ccLine !== '') {
            $headers[] = 'Cc: ' . $ccLine;
        }

        $headers[] = 'Subject: ' . $this->encodeHeader($subject);

        if ($attachments !== []) {
            $headers[] = 'X-MS-Has-Attach: yes';
        }

        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $mixedBoundary . '"';

        $parts = [
            '--' . $mixedBoundary,
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: quoted-printable',
            '',
            quoted_printable_encode($htmlBody),
        ];

        foreach ($attachments as $attachment) {
            $filename = $this->sanitizeFilename($attachment['filename']);
            $mime = $attachment['mime'] ?? 'application/pdf';

            $parts[] = '--' . $mixedBoundary;
            $parts[] = 'Content-Type: ' . $mime . '; name="' . $filename . '"';
            $parts[] = 'Content-Transfer-Encoding: base64';
            $parts[] = 'Content-Disposition: attachment; filename="' . $filename . '"';
            $parts[] = '';
            $parts[] = chunk_split(base64_encode($attachment['content']));
        }

        $parts[] = '--' . $mixedBoundary . '--';

        return implode("\r\n", $headers) . "\r\n\n" . implode("\r\n", $parts);
    }

    /**
     * @param  array<int, array{name?: string, email: string}>  $addresses
     */
    private function formatAddressList(array $addresses): string
    {
        $parts = [];

        foreach ($addresses as $address) {
            $email = trim((string) ($address['email'] ?? ''));
            if ($email === '') {
                continue;
            }

            $name = trim((string) ($address['name'] ?? ''));
            if ($name !== '' && strcasecmp($name, $email) !== 0) {
                $parts[] = $this->formatNamedAddress($name, $email);
            } else {
                $parts[] = $email;
            }
        }

        return implode(', ', $parts);
    }

    private function formatNamedAddress(string $name, string $email): string
    {
        $escapedName = str_replace(['\\', '"'], ['\\\\', '\\"'], $name);

        return sprintf('"%s" <%s>', $escapedName, $email);
    }

    private function encodeHeader(string $value): string
    {
        if ($value === '' || preg_match('/^[\x20-\x7E]*$/', $value)) {
            return $value;
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = trim($filename);
        $filename = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);

        return $filename !== '' ? $filename : 'attachment.pdf';
    }
}
