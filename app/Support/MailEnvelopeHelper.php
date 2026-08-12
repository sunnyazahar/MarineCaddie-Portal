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

    public static function applySenderEnvelope(mixed $message, string $fromEmail, string $fromName): void
    {
        if ($fromEmail === '' || ! filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $message->from($fromEmail, $fromName);
        $message->replyTo($fromEmail, $fromName);
    }
}
