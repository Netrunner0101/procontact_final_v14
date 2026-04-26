<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Catch N+1 query issues in development
        Model::preventLazyLoading(!app()->isProduction());

        $replyToAddress = config('mail.reply_to.address');
        if (! empty($replyToAddress)) {
            Mail::alwaysReplyTo($replyToAddress, config('mail.reply_to.name'));
        }

        $this->configurePortalRateLimiters();
    }

    private function configurePortalRateLimiters(): void
    {
        RateLimiter::for('portal-otp-request', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(10)->by(strtolower((string) $request->input('email'))),
            ];
        });

        RateLimiter::for('portal-otp-verify', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('portal-token-visit', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
