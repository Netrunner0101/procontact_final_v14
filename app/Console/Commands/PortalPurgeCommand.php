<?php

namespace App\Console\Commands;

use App\Services\PortalAuthService;
use Illuminate\Console\Command;

class PortalPurgeCommand extends Command
{
    protected $signature = 'portal:purge';

    protected $description = 'Purge expired portal OTPs, expired trusted devices, and old access log entries (GDPR retention).';

    public function handle(PortalAuthService $service): int
    {
        $stats = $service->purgeExpired();

        $this->info(sprintf(
            'Purged %d OTP(s), %d trusted device(s), %d access log entry(ies).',
            $stats['otps'],
            $stats['devices'],
            $stats['logs'],
        ));

        return self::SUCCESS;
    }
}
