<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
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
    }
}
