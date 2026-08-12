<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailDiagnoseCommand extends Command
{
    protected $signature = 'mail:diagnose {email? : Optional address to send a test message to}';

    protected $description = 'Show active mail configuration and optionally send a test email';

    public function handle(): int
    {
        $mailer = (string) config('mail.default');
        $from = (string) config('mail.from.address');
        $sendmailPath = (string) config('mail.mailers.sendmail.path');
        $smtp = config('mail.mailers.smtp', []);
        $username = (string) ($smtp['username'] ?? '');
        $password = (string) ($smtp['password'] ?? '');

        $this->info('Environment: '.app()->environment());
        $this->info('MAIL_MAILER: '.($mailer !== '' ? $mailer : '(empty — not set in .env)'));
        $this->info('MAIL_FROM_ADDRESS: '.$from);
        $this->info('Sendmail path: '.$sendmailPath);

        if ($mailer === '') {
            $this->error('MAIL_MAILER is missing or empty in .env. Set MAIL_MAILER=smtp or MAIL_MAILER=sendmail, then run php artisan config:clear');

            return self::FAILURE;
        }

        if ($mailer === 'smtp') {
            $this->info('SMTP host: '.($smtp['host'] ?? ''));
            $this->info('SMTP port: '.($smtp['port'] ?? ''));
            $this->info('SMTP scheme: '.(($smtp['scheme'] ?? '') ?: '(none)'));
            $this->info('SMTP username: '.$username);
            $this->info('SMTP password set: '.(filled($password) ? 'yes ('.strlen($password).' chars)' : 'NO — empty'));
            $this->info('FROM matches USERNAME: '.($from === $username ? 'yes' : 'no'));
        }

        if (in_array($mailer, ['log', 'array'], true)) {
            $this->error('This mailer does not deliver to real inboxes. Set MAIL_MAILER=sendmail (or smtp with valid mailbox) in .env and run php artisan config:clear');

            return self::FAILURE;
        }

        $binary = trim(explode(' ', $sendmailPath)[0] ?? '');
        if ($mailer === 'sendmail' && $binary !== '' && ! is_executable($binary)) {
            $this->error("Sendmail binary is missing or not executable: {$binary}");
            $this->line('Install/configure postfix/sendmail on the server, or switch to a working SMTP mailer.');

            return self::FAILURE;
        }

        if ($mailer === 'smtp' && (! filled($username) || ! filled($password))) {
            $this->error('SMTP username/password missing in .env');

            return self::FAILURE;
        }

        $email = $this->argument('email');
        if (! is_string($email) || $email === '') {
            $this->comment('Pass an email to also send a test message, e.g. php artisan mail:diagnose you@example.com');

            return self::SUCCESS;
        }

        try {
            Mail::raw('MarineCaddie mail diagnose test at '.now()->toDateTimeString(), function ($message) use ($email) {
                $message->to($email)->subject('MarineCaddie mail diagnose');
            });
            $this->info("Test message accepted by mailer for {$email}.");
        } catch (\Throwable $e) {
            $this->error('Test send failed: '.$e->getMessage());
            $this->line('535 auth failed usually means: mailbox password wrong, mailbox not created, or .env password truncated by # / special chars.');
            $this->line('Verify login in Hostinger Webmail first. Temporary fallback: MAIL_MAILER=sendmail');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
