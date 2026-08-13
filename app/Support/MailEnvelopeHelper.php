<?php

namespace App\Support;

class MailEnvelopeHelper
{
    /**
     * @return array{name: string, email: string}
     */
    public static function resolve(?string $senderName, ?string $senderEmail): array
    {
        $defaultAddress = (string) config('mail.from.address');
        $defaultName = (string) config('mail.from.name');

        $email = filled($senderEmail) ? trim((string) $senderEmail) : $defaultAddress;
        $name = filled($senderName) ? trim((string) $senderName) : $defaultName;

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $defaultAddress;
        }

        if ($name === '') {
            $name = $defaultName;
        }

        return [
            'name' => $name,
            'email' => $email,
        ];
    }

    /**
     * @return array{name: string, email: string}
     */
    public static function authenticatedSender(): array
    {
        $user = auth()->user();

        return self::resolve($user?->name, $user?->email);
    }

    public static function resolveShipmentSender(
        ?string $senderName,
        ?string $senderEmail,
        ?string $fallbackName,
        ?string $fallbackEmail
    ): array {
        if (filled($senderEmail)) {
            return self::resolve($senderName, $senderEmail);
        }

        $authSender = self::authenticatedSender();
        if (filled($authSender['email'])) {
            return $authSender;
        }

        return self::resolve($fallbackName, $fallbackEmail);
    }

    /**
     * Office 365 SMTP only allows sending as the authenticated mailbox.
     * Use that address as From and keep the intended sender as Reply-To.
     */
    public static function smtpMailboxAddress(): string
    {
        if ((string) config('mail.default') !== 'smtp') {
            return '';
        }

        $username = trim((string) config('mail.mailers.smtp.username'));
        if ($username !== '' && filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $username;
        }

        $from = trim((string) config('mail.from.address'));

        return filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : '';
    }

    public static function applySenderEnvelope(mixed $message, string $fromEmail, string $fromName): void
    {
        $mailbox = self::smtpMailboxAddress();
        $envelopeFrom = $mailbox !== '' ? $mailbox : $fromEmail;

        if ($envelopeFrom === '' || ! filter_var($envelopeFrom, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $message->from($envelopeFrom, $fromName);

        $replyTo = $fromEmail !== '' && filter_var($fromEmail, FILTER_VALIDATE_EMAIL)
            ? $fromEmail
            : $envelopeFrom;

        $message->replyTo($replyTo, $fromName);
    }
}
