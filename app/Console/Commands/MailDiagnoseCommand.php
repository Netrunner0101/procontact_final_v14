<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class MailDiagnoseCommand extends Command
{
    protected $signature = 'mail:diagnose {to? : Address to send a test message to (skip to only inspect config)}';

    protected $description = 'Show the active mail/queue configuration and optionally send a synchronous test email.';

    public function handle(): int
    {
        $this->info('=== Mail configuration ===');
        $this->table(['Key', 'Value'], [
            ['MAIL_MAILER (config)', config('mail.default')],
            ['MAIL_MAILER (env)', env('MAIL_MAILER', '<unset, falling back to config>')],
            ['MAIL_FROM_ADDRESS', config('mail.from.address')],
            ['MAIL_FROM_NAME', config('mail.from.name')],
            ['MAIL_HOST', config('mail.mailers.smtp.host')],
            ['MAIL_PORT', config('mail.mailers.smtp.port')],
            ['MAIL_USERNAME', config('mail.mailers.smtp.username') ? '<set>' : '<empty>'],
            ['MAIL_PASSWORD', config('mail.mailers.smtp.password') ? '<set>' : '<empty>'],
            ['RESEND_API_KEY', config('services.resend.key') ? '<set>' : '<empty>'],
            ['QUEUE_CONNECTION', config('queue.default')],
        ]);

        if (config('mail.default') === 'log') {
            $this->warn('Default mailer is "log" — messages are written to storage/logs and NOT delivered.');
        }
        if (config('mail.default') === 'array') {
            $this->warn('Default mailer is "array" — messages are kept in memory only (testing).');
        }
        if (config('queue.default') !== 'sync' && config('queue.default') !== 'database') {
            $this->line('Queue driver is "'.config('queue.default').'" — make sure a worker is running.');
        }
        if (config('queue.default') === 'database') {
            $this->warn('Queue driver is "database" — queued mailables require `php artisan queue:work`.');
        }

        $to = $this->argument('to');
        if (! $to) {
            $this->line('');
            $this->line('Pass an address to send a test message, e.g.: php artisan mail:diagnose you@example.com');

            return self::SUCCESS;
        }

        $this->info('=== Sending test message to '.$to.' ===');

        try {
            Mail::raw('ProContact mail diagnostic test sent at '.now()->toIso8601String(), function (Message $m) use ($to) {
                $m->to($to)->subject('ProContact mail diagnostic test');
            });
            $this->info('Message handed to the mailer without throwing. Check the destination inbox (and spam).');
            $this->line('If MAIL_MAILER=log, find the message in storage/logs/laravel.log.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Mailer threw: '.get_class($e).': '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
